"use strict";
// Import module
const WS = require('ws');
const Log = require("../lib/log");
const ReconnectingWebSocket  = require('reconnecting-websocket');
const Sequelize = require('sequelize');
const fetch = require('node-fetch');
const config = require('config');
const truefx = require('truefx');
const appConfig = config.get('config');
var moment = require('moment');
var uri = require('lil-uri');

/**
 * Class ApiSocket
 */
class ApiSocket {

    /**
     * constructor Function
     * @param String param
     * @param Quere saveQueue
     * @param SocketIo server
     */
    constructor(param, saveQueue, server, format, randomNum, type, fee, mysql) {
        const log = new Log();
        this.lastClose = -1;
        this.lastOpen = -1;
        this.lastTime = 0;
        this.resFlag = false;
        this.senderFlag = null;
        this.streamFlag = false;
        this.buy_total = 0;
        this.sell_total = 0;
        this.fee = fee;
        this.mysql = mysql;
        this.price = [];
        const formatTimeRound = function (round) {
            let type = round.slice(-1);
            let roundNum = round.slice(0, -1);
            let result = null;
            let seconds = 1;
            switch (type) {
                case 's':
                    seconds = roundNum;
                    break;
                case 'm':
                    seconds = roundNum * 60;
                    break;
                case 'h':
                    seconds = roundNum * 3600;
                    break;
                case 'd':
                    seconds = roundNum * 3600 * 24;
                    break;
                default:
                    break;
            }
            result = parseInt(new Date().getTime() / 1000 / seconds, 10) * seconds;
            return result;
        }

        if (type == 'CRYPTO') {
            try {
                let apiUrl = uri().protocol(appConfig.stream.binance.protocol).host(appConfig.stream.binance.host).path(appConfig.stream.binance.path).query({ streams: param.toLowerCase() + '@markPrice@1s' }).build();
                const options = {
                    WebSocket: WS,
                    connectionTimeout: 1000,
                };
                this.api = new ReconnectingWebSocket(apiUrl,[],options);
                this.api.addEventListener('open', function(event) {
                    log.info(`Connect to stream ${param} succeed!`);
                })
                this.api.addEventListener('message', async function (event) {
                    let data = event.data
                    if (data) {
                        const trade = JSON.parse(data); // parse data to json
                        let price = trade.data.p;
                        if(parseFloat(this.price[0]) == parseFloat(trade.data.p)) {
                            let random = parseFloat(randomNum) * (1 + Math.floor(Math.random() * Math.floor(5)));
                            let random_price = parseFloat(random) * parseFloat(price);
                            let low = price - random_price;
                            let high = price + random_price
                            price = parseFloat(low) + (parseFloat(high) - parseFloat(low)) * Math.random();
                        }
                        this.price.push(parseFloat(price).toFixed(8));
                        let saveTrade = await this.processPrice();
                        if (saveTrade != null) {
                            // Emit data to client
                            this.senderCandle(saveTrade,Number(moment().format('ss')));
                        }
                        this.resFlag = true;
                    }
                }.bind(this));

                this.api.addEventListener('close', function (code, reason) {
                    // Emit error when api close connect
                    server.to(param).emit('close', {
                        status: code,
                        message: reason
                    });
                });
            } catch(e) {
                console.log(e);
            }
        } 
        if (type == 'FOREX') {
            setInterval(async function () {
                try {
                    truefx.get(param).then(async (response) => {
                        if (response && response.length > 0) {
                            if (!isNaN(response[0].bid)) {
                                let price = response[0].bid;
                                if(this.price[0] == response[0].bid) {
                                    let random = parseFloat(randomNum) * (1 + Math.floor(Math.random() * Math.floor(5)));
                                    let random_price = parseFloat(random) * parseFloat(price);
                                    let low = price - random_price;
                                    let high = price + random_price
                                    price = parseFloat(low) + (parseFloat(high) - parseFloat(low)) * Math.random();
                                }
                                this.price.push(parseFloat(price).toFixed(8));
                                let saveTrade = await this.processPrice();
                                if (saveTrade != null) {
                                    // Emit data to client
                                    this.senderCandle(saveTrade,Number(moment().format('ss')));
                                }
                                this.resFlag = true;
                            }
                        }
                    })
                } catch(e) {
                    console.log(e);
                }
            }.bind(this), 1000);
        }

        this.processPrice = function () {
            return new Promise(async (resolve, reject) => {
                if (this.price.length != 0) {
                    let now = moment();
                    // if(param.toUpperCase() == 'BTCUSDT'){
                    //     log.info(`flag-1: open: ${this.price[0]}, close: ${this.price[this.price.length - 1]}, trueTime:${moment().format('HH:mm:ss')}`);
                    // }
                    if (Number(now.format('ss')) >= 30 && Number(now.format('ss')) <= 59) {
                        while(parseFloat(this.price[0]) == parseFloat(this.price[this.price.length - 1])){
                            let price = this.price[this.price.length - 1]
                            let random = parseFloat(randomNum) * (1 + Math.floor(Math.random() * Math.floor(5)));
                            let random_price = parseFloat(random) * parseFloat(price);
                            let low = price - random_price;
                            let high = price + random_price
                            price = parseFloat(low) + (parseFloat(high) - parseFloat(low)) * Math.random();
                            this.price.push(parseFloat(price).toFixed(8));
                        }
                    }
                    // if(param.toUpperCase() == 'BTCUSDT'){
                    //     log.info(`flag-2: open: ${this.price[0]}, close: ${this.price[this.price.length - 1]}, trueTime:${moment().format('HH:mm:ss')}`);
                    // }
                    if (Number(now.format('ss')) > 30 && Number(now.format('ss')) <= 59) {
                        const marketSql = 'SELECT * FROM markets WHERE market_name = "'+param+'"';
                        const marketResult = await this.mysql.query(marketSql, {
                            raw: true,
                            type: Sequelize.SELECT
                        });

                        let result = JSON.parse(JSON.stringify(marketResult[0], null, 2));
                        if(result.length > 0){
                            let market = result[0];
                            let random = parseFloat(market.random) * (1 + Math.floor(Math.random() * Math.floor(5)));
                            if (market.result != null) {
                                let open = this.price[0];
                                let close = this.price[this.price.length - 1];
                                if (market.result.toUpperCase() == "SELL" && close >= open) {
                                    let price = parseFloat(this.price[0]) - random * parseFloat(this.price[0]);
                                    this.price.push(price);
                                }
                                if (market.result.toUpperCase() == "BUY" && close < open) {
                                    let price = parseFloat(this.price[0]) + random * parseFloat(this.price[0]);
                                    this.price.push(price);
                                }
                            }
                        }
                    }

                    // if(param.toUpperCase() == 'BTCUSDT'){
                    //     log.info(`flag-3: open: ${this.price[0]}, close: ${this.price[this.price.length - 1]}, trueTime:${moment().format('HH:mm:ss')}`);
                    // }

                    let trade = {
                        s: param.toUpperCase(),  // Symbol
                        o: this.price[0],  // Open price
                        c: this.price[this.price.length - 1],  // Close price
                        h: Math.max(...this.price),  // High price
                        l: Math.min(...this.price),  // Low price
                        t: formatTimeRound(format)
                    };

                    // if(param.toUpperCase() == 'BTCUSDT'){
                    //     log.info(`flag-4: open: ${trade.o}, close: ${trade.c}, trueTime:${moment().format('HH:mm:ss')}`);
                    // }

                    if (this.lastClose != -1 && this.lastTime != trade.t) {
                        let newestPrice = this.lastClose;
                        this.price = [newestPrice];
                        trade.o = this.lastClose;
                        trade.c = this.lastClose
                        trade.h = this.lastClose;
                        trade.l = this.lastClose;
                        this.lastOpen = trade.o;
                    } else {
                        this.lastClose = trade.c;
                        if (this.lastOpen != -1) {
                            trade.o = this.lastOpen;
                        }
                    }

                    // if(param.toUpperCase() == 'BTCUSDT'){
                    //     log.info(`flag-5: open: ${trade.o}, close: ${trade.c}, trueTime:${moment().format('HH:mm:ss')}`);
                    // }

                    this.lastTime = trade.t;
                    resolve(trade);
                }
                resolve(null);
            });
        };

        this.senderCandle = function (data,time) {
            let tempSave = {
                marketname: data.s,
                open: data.o,
                close: data.c,
                high: data.h,
                low: data.l,
                time: data.t
            }

            if(Number(moment().format('ss')) == 0 || Number(moment().format('ss')) == 31){
                this.volume = Math.floor(Math.random() * Math.floor(5));
                data.v = this.volume;
                tempSave.quote_vol = data.v;
            } else if (Number(moment().format('ss')) > 0 && Number(moment().format('ss')) <= 59) {
                this.volume += Math.floor(Math.random() * Math.floor(2));
                data.v = this.volume;
                tempSave.quote_vol = data.v;
            }

            if(this.senderFlag != time){
                saveQueue.add('saveQueue',{ trade: tempSave });
                server.to(param).emit('candles', data);
                this.senderFlag = time
            }
        }


        setInterval(async function () {
            if (!this.resFlag) {
                if (this.price.length != 0) {
                    let price = this.price[0];
                    if(this.price[0] == price) {
                        let random = parseFloat(randomNum) * (1 + Math.floor(Math.random() * Math.floor(5)));
                        let random_price = parseFloat(random) * parseFloat(price);
                        let low = price - random_price;
                        let high = price + random_price
                        price = parseFloat(low) + (parseFloat(high) - parseFloat(low)) * Math.random();
                    }
                    this.price.push(parseFloat(price).toFixed(8));
                    const saveTrade = await this.processPrice();
                    // Emit data to client
                    this.senderCandle(saveTrade,Number(moment().format('ss')));
                }
            } else {
                this.resFlag = false;
            }

            let arrayRandom = [0, 1, 0, 2, 0, 1, 0, -1, 0, 1, -1, 2,
                0, 0, 0, -1, 0, 1, 0, -1, -1, 0, 0, -1,
                1, 0, 1, 1, 0, 2, 0, 0, 1, 0, 1, 0, 2, 0];
            let userOnline = 700;
            userOnline = userOnline + arrayRandom[Math.floor(Math.random() * Math.floor(arrayRandom.length))];
            let now = moment();
            let status = Number(now.format('s')) < 30 ? 'open' : 'close';
            if (status == 'open') {
                this.buy_total += Math.floor(Math.random() * Math.floor(800))
                this.sell_total += Math.floor(Math.random() * Math.floor(800));
            }

            if (now.format('ss') == "00") {
                this.buy_total = 0;
                this.sell_total = 0;
                // Get Fee data from mysql
                const feeSql = 'SELECT * FROM trade_fee';
                const feeResult = await this.mysql.query(feeSql, {
                    raw: true,
                    type: Sequelize.SELECT
                });

                let resultFeeData = JSON.parse(JSON.stringify(feeResult[0], null, 2));
                if(resultFeeData.length > 0){
                    resultFeeData.forEach(element => {
                        this.fee[element.hour] = element.value;
                    });
                }
            }

            let timerData = {
                date: now.format('YYYY/MM/DD HH:mm:ss'),
                seconds: now.format('s'),
                status: status,
                online: userOnline,
                fee: this.fee[now.format('H')] != undefined ? this.fee[now.format('H')] : 80
            }

            let randomStatusData = {
                buy: this.buy_total,
                sell: this.sell_total,
            };

            server.to(param).emit('timer', timerData);
            server.to(param).emit('roomstatus', randomStatusData);

            if (now.format('ss') == "00" || now.format('ss') == "31") {
                //this.buy_total = 0;
                //this.sell_total = 0;
                let timeSql = '    SELECT '+
                                '        res.marketname AS s,'+
                                '        res.open AS o,'+
                                '        res.close AS c,'+
                                '        res.high AS h,'+
                                '        res.low AS l,'+
                                '        res.time AS t'+
                                '    FROM'+
                                '        (SELECT '+
                                '            *'+
                                '        FROM'+
                                '            tb_candle'+
                                '        INNER JOIN (SELECT '+
                                '            MAX(id) AS time_id,'+
                                '                FROM_UNIXTIME(time, "%Y-%m-%d %H %i"),'+
                                '                FROM_UNIXTIME(time, "%Y-%m-%d %H %i %s"),'+
                                '                STR_TO_DATE(DATE_FORMAT(NOW(), "%Y-%M-%D %H %i %s"), "%Y-%M-%D %H %i %s"),'+
                                '                time AS time_val'+
                                '        FROM'+
                                '            tb_candle'+
                                '        WHERE'+
                                '            tb_candle.marketname = "'+param+'"'+
                                '                AND IF(CAST(DATE_FORMAT(NOW(), "%s") AS DECIMAL) BETWEEN 0 AND 30, STR_TO_DATE(FROM_UNIXTIME(time, "%Y-%M-%D %H %i %s"), "%Y-%M-%D %H %i %s") < STR_TO_DATE(CONCAT(DATE_FORMAT(NOW(), "%Y-%M-%D %H %i "), "00"), "%Y-%M-%D %H %i %s"), STR_TO_DATE(FROM_UNIXTIME(time, "%Y-%M-%D %H %i %s"), "%Y-%M-%D %H %i %s") < STR_TO_DATE(CONCAT(DATE_FORMAT(NOW(), "%Y-%M-%D %H %i "), "30"), "%Y-%M-%D %H %i %s"))'+
                                '        GROUP BY time) AS b ON (tb_candle.id = b.time_id)'+
                                '        ORDER BY tb_candle.id DESC'+
                                '        LIMIT 100) AS res'+
                                '    ORDER BY res.id';
                const timerKlineResult = await this.mysql.query(timeSql, {
                    raw: true,
                    type: Sequelize.SELECT
                });

                let timerKline = JSON.parse(JSON.stringify(timerKlineResult[0], null, 2));
                let resultBlur = [];
                if (timerKline.length != 0) {
                    resultBlur = timerKline.map(obj => { return obj.o > obj.c ? "CALL" : "PUT"; });
                    server.to(param).compress(true).emit('blurs', resultBlur);
                } else {
                    server.to(param).compress(true).emit('blurs', resultBlur);
                }
            }
        }.bind(this), 1000);
    }
}

// Export module
module.exports.ApiSocket = ApiSocket;