var moment = require('moment')
const Market = require('../models/market').Market

class Worker {
    /**
     * constructor Function
     * @param String param
     */
    constructor(param, mysql, log, profit) {
        log.info('Init worker for market: ' + param)
        let market = new Market(mysql);
        this.countPreLose = 0;

        this.processOrder = function () {
            return new Promise(async (resolve, reject) => {
                try {
                    let round_time = moment().utc().add(1, 'minutes').format('YYYY-MM-DD HH:mm');
                    let checkOrder = await market.checkHasOrder(0, 'live', param, round_time);

                    if (checkOrder && parseFloat(profit.profit_today) < parseFloat(profit.profit_estimate)) {
                        let result_buy = await market.SumByRound(round_time, 'BUY', param);
                        let result_sell = await market.SumByRound(round_time, 'SELL', param);
                        let total_buy = result_buy && result_buy.total ? Number(result_buy.total) : 0;
                        let total_sell = result_sell && result_sell.total ? Number(result_sell.total) : 0;

                        // Trường hợp chỉ toàn SELL hoặc toàn BUY
                        if (total_buy == 0 || total_sell == 0) {
                            
                            // Trường hợp chỉ toàn BUY
                            if (total_buy == 0) {

                                // Trường hợp thua Math.floor(Math.random() * 4) lần liên tiếp và giá trị thắng nhỏ hơn lợi nhuận ngày
                                if (this.countPreLose > Math.floor(Math.random() * 4) && total_sell < profit.profit_today) {
                                    await market.UpdateMarket('SELL', param);
                                    this.countPreLose = 0;
                                } else {
                                    await market.UpdateMarket('BUY', param);
                                    this.countPreLose++;
                                }
                            }

                            // Trường hợp chỉ toàn SELL 
                            if (total_sell == 0) {

                                // Trường hợp thua Math.floor(Math.random() * 4) lần liên tiếp và giá trị thắng nhỏ hơn lợi nhuận ngày
                                if (this.countPreLose > Math.floor(Math.random() * 4) && total_buy < profit.profit_today) {
                                    await market.UpdateMarket('BUY', param);
                                    this.countPreLose = 0;
                                } else {
                                    await market.UpdateMarket('SELL', param);
                                    this.countPreLose++;
                                }
                            }
                        } else {
                            // Trường hợp BUY lớn hơn SELL
                            if (total_buy > total_sell) {
                                await market.UpdateMarket('SELL', param);
                            }

                            // Trường hợp SELL lớn hơn BUY
                            if (total_buy < total_sell) {
                                await market.UpdateMarket('BUY', param);
                            }
                        }
                    }
                    resolve(true)
                } catch (error) {
                    reject(error)
                }
            })
        }

        setInterval(
            async function () {
                if (Number(moment().utc().format('ss')) > 30 && Number(moment().utc().format('ss')) <= 35) {
                    await this.processOrder();
                }
            }.bind(this),
            1000
        )
    }
}

// Export module
module.exports.Worker = Worker
