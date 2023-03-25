const Log = require("./lib/log");
const MysqlKline = require('./model/MysqlKline');
const Sequelize = require('sequelize');
const ApiSocket = require('./services/ApiSocket').ApiSocket;
const mongoose = require("mongoose");
const moment = require('moment')
const Address6 = require('ip-address').Address6;
const config = require('config');
const chalk = require('chalk');
const figlet = require('figlet');
const app = require('express')();
const fetch = require('node-fetch');
const server = require('http').Server(app);
const io = require('socket.io')(server);
const pm2IO = require('@pm2/io')
const Redis = require('ioredis');
const Queue = require('bull');
const https = require('https');
var uri = require('lil-uri');

const PORT = process.env.npm_config_port || 3002;
// const Log = new Log();
const appConfig = config.get('config');

class Server {
    /**
     * constructor Function
     */
    constructor() {
        return new Promise(async (resolve, reject) => {
            try {
                const log = new Log();
                // Print name app
                console.log(
                    chalk.yellow(
                        figlet.textSync('Binance Stream', { horizontalLayout: 'half' })
                    )
                );
                log.info("Start Server...");
                process.setMaxListeners(0);
                // Get market data from Api
                let market = [];
                let fee = [];
                let format = appConfig.candle_round;
                // Init api websocket
                let arrAPi = [];
                this.arrIp = new Set();
                let self = this;
                var metric = pm2IO.metric({
                    name    : 'Realtime user',
                    value   : function() {
                      return self.arrIp.size
                    }
                  })
                this.arrUser = [];
                let mysqlDb = await this.initMysqlConnection(log);
                let mysqlKline = new MysqlKline(mysqlDb);
                let jobSave = await this.initJobSave(mysqlKline,log);

                const marketSql = 'SELECT * FROM markets';
                const marketResult = await mysqlDb.query(marketSql, {
                    raw: true,
                    type: Sequelize.SELECT
                });

                let resultMaketData = JSON.parse(JSON.stringify(marketResult[0], null, 2));
                if(resultMaketData.length > 0){
                    market = resultMaketData;
                }

                const feeSql = 'SELECT * FROM trade_fee';
                const feeResult = await mysqlDb.query(feeSql, {
                    raw: true,
                    type: Sequelize.SELECT
                });

                let resultFeeData = JSON.parse(JSON.stringify(feeResult[0], null, 2));
                if(resultFeeData.length > 0){
                    resultFeeData.forEach(element => {
                        fee[element.hour] = element.value;
                    });
                }

                let redis = await this.initRedisConnection(log);
                let listCoin = [];
                let listForex = [];
                market.forEach(element => {
                    if (element.type == 'CRYPTO') {
                        listCoin.push(element.market_name);
                    } else {
                        listForex.push(element.market_name);
                    }
                });

                let serverSocket = this.initServerSocketIo(listCoin, listForex,mysqlDb);
                this.initListenerApi(market, arrAPi, jobSave, serverSocket, format, fee,mysqlDb);

                // Add event handler to redis
                redis.psubscribe('*', function (err, count) { });
                redis.on('pmessage', function (partner, channel, message) {
                    if (message) {
                        let data = JSON.parse(message);
                        if (data) {
                            if (data.data != undefined && this.arrUser[data.data.userid] != undefined) {
                                serverSocket.to(this.arrUser[data.data.userid]).emit('results', data.data.message);
                            }
                        }
                    }
                }.bind(this));

                // Add listen port
                server.listen(PORT);
                log.info(`** Server is listening on localhost:${PORT}, open your browser on http://localhost:${PORT} **`);
                // Process listen event on exit
                process.on('exit', (code) => {
                    console.log('Process exit event with code: ', code);
                    log.info("Stop process...");
                });
            } catch (ex) {
                return reject(ex);
            }
            resolve(this);
        });
    }

    /**
     * Function Init Mysql Connection
     * @param log 
     */
    initMysqlConnection(log) {
        return new Promise(async function (resolve, reject) {
            // Init connect to mongodb
            try {
                var sequelize = new Sequelize(appConfig.mysql.database, appConfig.mysql.username, appConfig.mysql.password, {
                    host: appConfig.mysql.host,
                    dialect: 'mysql',
                    logging: false
                });
                await sequelize.authenticate();
                resolve(sequelize);
                log.info('Connection has been established successfully.');
            } catch (error) {
                log.error('Unable to connect to the database:', error);
                reject(err);
            }
        });
    }

    /**
     * Function Init Redis Connection
     * @param log 
     */
    initRedisConnection(log) {
        return new Promise(async function (resolve, reject) {
            try {
                let redis = new Redis({
                    port: appConfig.redis.port,
                    host: appConfig.redis.host,
                    connectTimeout: 20000
                 });
                log.info('Connection to Redis succeeded.');
                resolve(redis);
            } catch (err) {
                log.error('Redis connection error: ' + err);
                reject(err);
            }
        });
    }

    /**
     * Function Init Server SocketIo
     * @param listCoin 
     * @param arrAPi 
     * @param arrConnect 
     */
    initServerSocketIo(listCoin, listForex,sequelize) {
        const response = function (status, msg) {
            return {
                'status': status,
                'message': msg
            };
        }
        // SocketIo event handle
        io.on('connection', (socket) => {
            socket.on('join', async function (room) {
                if (room == null || room == '') {
                    socket.emit('join', response(422, 'Not found channel name for parameter.'));
                    return;
                }

                room = room.toUpperCase();
                if (listCoin.indexOf(room) != -1 || listForex.indexOf(room) != -1) {
                    socket.join(room);
                    const records = await sequelize.query('SELECT c.marketname as s, c.open as o, c.close as c, c.high as h, c.low as l, c.time as t, c.quote_vol as v FROM (SELECT *   '  + 
                    '   FROM tb_candle  '  + 
                    '   WHERE tb_candle.marketname = "'+room+'"  '  + 
                    '   	ORDER BY id DESC  '  + 
                    '   	LIMIT 10000  '  + 
                    '  ) AS c GROUP BY c.time ORDER BY c.id' , {
                        raw: true,
                        type: Sequelize.SELECT
                      });
                    
                    let kline = JSON.parse(JSON.stringify(records[0], null, 2));
                    let template = {
                        status: 200,
                        message: "success",
                        candles: []
                    };

                    // Split data
                    if (kline.length > 0) {
                        template.candles = kline;
                        socket.compress(true).emit('join', template);
                    } else {
                        socket.compress(true).emit('join', template);
                    }

                    let timeSql =
                            "    SELECT " +
                            "        res.marketname AS s," +
                            "        res.open AS o," +
                            "        res.close AS c," +
                            "        res.high AS h," +
                            "        res.low AS l," +
                            "        res.time AS t" +
                            "    FROM" +
                            "        (SELECT " +
                            "            *" +
                            "        FROM" +
                            "            tb_candle" +
                            "        INNER JOIN (SELECT " +
                            "            MAX(id) AS time_id," +
                            '                FROM_UNIXTIME(time, "%Y-%m-%d %H %i"),' +
                            '                FROM_UNIXTIME(time, "%Y-%m-%d %H %i %s"),' +
                            '                STR_TO_DATE(DATE_FORMAT(NOW(), "%Y-%M-%D %H %i %s"), "%Y-%M-%D %H %i %s"),' +
                            "                time AS time_val" +
                            "        FROM" +
                            "            tb_candle" +
                            "        WHERE" +
                            '            tb_candle.marketname = "' +
                            room +
                            '"' +
                            '                AND IF(CAST(DATE_FORMAT(NOW(), "%s") AS DECIMAL) BETWEEN 0 AND 30, STR_TO_DATE(FROM_UNIXTIME(time, "%Y-%M-%D %H %i %s"), "%Y-%M-%D %H %i %s") < STR_TO_DATE(CONCAT(DATE_FORMAT(NOW(), "%Y-%M-%D %H %i "), "00"), "%Y-%M-%D %H %i %s"), STR_TO_DATE(FROM_UNIXTIME(time, "%Y-%M-%D %H %i %s"), "%Y-%M-%D %H %i %s") < STR_TO_DATE(CONCAT(DATE_FORMAT(NOW(), "%Y-%M-%D %H %i "), "30"), "%Y-%M-%D %H %i %s"))' +
                            "        GROUP BY time) AS b ON (tb_candle.id = b.time_id)" +
                            "        ORDER BY tb_candle.id DESC" +
                            "        LIMIT 100) AS res" +
                            "    ORDER BY res.id";
                    const timerKlineResult = await sequelize.query(timeSql , {
                        raw: true,
                        type: Sequelize.SELECT
                      });

                    let timerKline = JSON.parse(JSON.stringify(timerKlineResult[0], null, 2));
                    let resultBlur = [];
                    if (timerKline.length != 0) {
                        resultBlur = timerKline.map(obj => { return obj.o > obj.c ? "CALL" : "PUT"; });
                        socket.compress(true).emit('blurs', resultBlur);
                    } else {
                        socket.compress(true).emit('blurs', resultBlur);
                    }

                    this.arrIp.add(new Address6(socket.client.conn.remoteAddress).inspectTeredo().client4);
                    io.to('admin').emit('admin', this.updateServerInfor());
                } else {
                    socket.emit('join', response(422, 'Not found channel name for parameter.'));
                }
            }.bind(this));

            socket.on('leave', function (room) {
                if (typeof room == 'string' && room != '') {
                    room = room.toUpperCase();
                    socket.leave(room);
                    this.arrIp.delete(new Address6(socket.client.conn.remoteAddress).inspectTeredo().client4);
                    io.to('admin').emit('admin', this.updateServerInfor());
                    socket.emit('leave', response(200, 'Leave room ' + room + ' success.'));
                } else {
                    socket.emit('leave', response(422, 'Leave room ' + room + ' success.'));
                }
            }.bind(this));

            socket.on('admin', function (action) {
                if (action == 'login') {
                    socket.join('admin');
                    socket.emit('admin', this.updateServerInfor());
                    setInterval(() => {
                        socket.emit('admin', this.updateServerInfor());
                    }, 1000);
                } else {
                    socket.leave('admin');
                }
            }.bind(this));

            socket.on('id', function (id) {
                this.arrUser[id] = socket.id;
                socket.emit('id', 'Ok');
            }.bind(this));

            socket.on('disconnect', function () {
                io.to('admin').emit('admin', this.updateServerInfor());
            }.bind(this));
        });

        return io;
    }

    /**
     * Function Init Listener Api
     * @param market 
     * @param arrAPi 
     * @param saveQueue 
     * @param io 
     * @param format 
     */
    initListenerApi(market, arrAPi, saveQueue, io, format, fee,mysql) {
        market.forEach(element => {
            let apiSocket = new ApiSocket(element.market_name, saveQueue, io, format, element.random, element.type, fee,mysql);
            arrAPi[element.market_name] = apiSocket;
        });
    }

    /**
     * Function Create Job Save
     */
    initJobSave(kline,log) {
        return new Promise(async function (resolve, reject) {
            try {
                const saveQueue = new Queue('saveQueue', {
                    redis: {
                      host: appConfig.redis.host,
                      port: appConfig.redis.port,
                    }
                });
        
                saveQueue.process('*', async job => {
                    return kline.create(job.data.trade);
                });
                log.info('Init job process successed');
                resolve(saveQueue);
            } catch (err) {
                log.error('Init job process: ' + err);
                reject(err);
            }
        });
    }

    updateServerInfor() {
        return {
            number_connect: Math.floor(Math.random() * Math.floor(1000)),
            list_ip: Array.from(this.arrIp),
            time: moment().format('YYYY/MM/DD HH:mm:ss')
        }
    }
}

// Export module
module.exports.Server = Server;
