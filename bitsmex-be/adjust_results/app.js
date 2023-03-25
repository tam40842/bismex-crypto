const Log = require('./lib/log')
const Sequelize = require('sequelize')
const moment = require('moment')
const chalk = require('chalk')
const figlet = require('figlet')
const pm2IO = require('@pm2/io')
const Worker = require('./services/worker').Worker
const RoundClose = require('./models/roundClose').RoundClose
require('dotenv').config()

const log = new Log()

class App {
    /**
     * constructor Function
     */
    constructor() {
        return new Promise(async (resolve, reject) => {
            try {
                // Print name app
                console.log(chalk.yellow(figlet.textSync('Candle Adjust', { horizontalLayout: 'half' })))
                log.info('Start Server.....')
                process.setMaxListeners(0)
                //Init variable
                let market = []
                let arrAPi = []
                let profit = {}
                let currentDay = moment().utc().format('DD');

                // Init DB connect
                let mysqlDb = await this.initMysqlConnection(log)
                const marketSql = 'SELECT * FROM markets'
                const marketResult = await mysqlDb.query(marketSql, {
                    raw: true,
                    type: Sequelize.SELECT,
                })
                let resultMaketData = JSON.parse(JSON.stringify(marketResult[0], null, 2))
                if (resultMaketData.length > 0) {
                    market = resultMaketData
                }

                // Get last round
                let roundClose = new RoundClose(mysqlDb)
                profit = await this.initProfitData(roundClose, log)
                // Init worker
                this.initWorker(market, mysqlDb, log, arrAPi, profit)
                setInterval(async function() {
                    if(Number(moment().utc().format('ss')) == 0){
                        log.info('Recall initProfitData in new round.')
                        let newProfit = await this.initProfitData(roundClose, log);
                        profit = Object.assign(profit,newProfit);
                        // profit = Object.assign(profit,newProfit,{demo:1});
                        // currentDay = moment().utc().format('DD');
                    }

                    // if (Number(moment().utc().format('ss')) == 0) {
                    //     log.info('Recall profit_today.')
                    //     let first_time = moment().utc().format('YYYY-MM-DD');
                    //     let last_time = moment().utc().format('YYYY-MM-DD HH:mm:ss');
                    //     let user_win_total = await roundClose.getVolumebyStatus(1, first_time, last_time);
                    //     let user_lose_total = await roundClose.getVolumebyStatus(2, first_time, last_time);
                    //     let lose_total = user_lose_total && user_lose_total.total ? Number(user_lose_total.total) : 0;
                    //     let win_total = user_win_total && user_win_total.total ? Number(user_win_total.total) : 0;
                    //     let profit_today = lose_total - win_total;
                    //     profit = Object.assign(profit,{profit_today: profit_today});
                    // }
                }.bind(this), 1000);
                // Process listen event on exit
                process.on('exit', (code) => {
                    log.info('Process exit event with code: ', code)
                    log.info('Stop process...')
                })
            } catch (ex) {
                return reject(ex)
            }
            resolve(this)
        })
    }

    /**
     * Function Init Listener Api
     * @param market
     * @param arrAPi
     * @param saveQueue
     * @param io
     * @param format
     */
    initWorker(market, mysql, log, arrAPi, profit) {
        market.forEach((element) => {
            let worker = new Worker(element.market_name, mysql, log, profit)
            arrAPi[element.market_name] = worker
        })
    }

    /**
     * Function Init Mysql Connection
     * @param log
     */
    initProfitData(roundClose, log) {
        return new Promise(async function (resolve, reject) {
            // Init connect to mongodb
            try {
                let first_time = moment().utc().format('YYYY-MM-DD');
                let last_time = moment().utc().format('YYYY-MM-DD HH:mm:ss');
                let yesterday_start = moment().utc().subtract(1, 'days').format('YYYY-MM-DD');
                let yesterday_end = moment().utc().subtract(1, 'days').format('YYYY-MM-DD') + ' 23:59:59';
                let settings = await roundClose.getSetting();
                let user_win_total = await roundClose.getVolumebyStatus(1, first_time, last_time);
                let user_lose_total = await roundClose.getVolumebyStatus(2, first_time, last_time);
                let volume24h_yesterday = await roundClose.getVolume24hYesterday(yesterday_start, yesterday_end);
                let profit = {
                    user_lose_total: user_lose_total && user_lose_total.total ? Number(user_lose_total.total) : 0,
                    user_win_total: user_win_total && user_win_total.total ? Number(user_win_total.total) : 0,
                    percent_win: settings && settings.value ? Number(settings.value) : 0,
                    volume24h_yesterday: volume24h_yesterday && volume24h_yesterday.total ? Number(volume24h_yesterday.total) : 0,
                    // volume24h_yesterday: 20000,
                }
                profit.profit_estimate = profit.volume24h_yesterday*profit.percent_win/100;
                profit.profit_today = profit.user_lose_total - profit.user_win_total;
                resolve(profit)
            } catch (error) {
                log.error('Unable to get data: ', error)
                reject(error)
            }
        })
    }

    /**
     * Function Init Mysql Connection
     * @param log
     */
    initMysqlConnection(log) {
        return new Promise(async function (resolve, reject) {
            // Init connect to mongodb
            try {
                var sequelize = new Sequelize(process.env.DB_DATABASE, process.env.DB_USERNAME, process.env.DB_PASSWORD, {
                    host: process.env.DB_HOST,
                    dialect: 'mysql',
                    logging: false //console.log
                })
                await sequelize.authenticate()
                resolve(sequelize)
                log.info('Connection has been established successfully.')
            } catch (error) {
                log.error('Unable to connect to the database:', error)
                reject(error)
            }
        })
    }
}

// Export module
module.exports.App = App
