const { QueryTypes } = require('sequelize')
const Log = require('../lib/log')
const log = new Log()
let db = null

class Market {
    constructor(mysql) {
        db = mysql
    }

    async SumByRound(expired_at, action, market_name) {
        return new Promise(async function (resolve, reject) {
            let result = null;
            let sql =
                'SELECT sum(`amount`) as total FROM `orders` WHERE `action` = :action AND `market_name` = :market_name AND `type` = :type AND `status` = 0 AND `admin_setup` = 0 AND `expired_at` = :expired_at';

            try {
                let data = await db.query(sql, {
                    replacements: { expired_at, action, type:'live' , market_name },
                    type: QueryTypes.SELECT,
                });
                if (data.length > 0) {
                    result = JSON.parse(JSON.stringify(data[0], null, 2));
                }
                resolve(result);
            } catch (error) {
                log.error(error);
                reject(error);
            }
        });
    }

    async UpdateMarket(result, market_name) {
        return new Promise(async function (resolve, reject) {
            var sql = 'UPDATE `markets` set `result` = :result where `market_name` = :market_name';
            try {
                await db.query(sql, {
                    replacements: { result, market_name },
                    type: QueryTypes.UPDATE,
                });
                resolve(true);
            } catch (error) {
                log.error(error);
                reject(error);
            }
        })
    }

    async checkHasOrder(status, type, market_name, expired_at) {
        return new Promise(async function (resolve, reject) {
            var sql =
                'SELECT * FROM `orders` WHERE `status` = :status AND `type` = :type AND `expired_at` = :expired_at AND `market_name` = :market_name  limit 1';
            try {
                let data = await db.query(sql, {
                    replacements: { status, type, market_name, expired_at },
                    type: QueryTypes.SELECT,
                });

                if (data.length > 0) {
                    resolve(true);
                } else {
                    resolve(false);
                }
            } catch (error) {
                log.error(error);
                reject(error);
            }
        })
    }
}

// Export module
module.exports.Market = Market
