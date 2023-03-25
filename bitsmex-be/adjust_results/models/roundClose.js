const { QueryTypes } = require('sequelize')
const Log = require('../lib/log')
const log = new Log()
let db = null
class RoundClose {
    constructor(mysql) {
        db = mysql
    }

    async getSetting() {
        return new Promise(async function (resolve, reject) {
            let result = null;
            let sql = 'SELECT `setting_value` as value FROM `settings` WHERE `setting_name` = :setting_name';
            try {
                let data = await db.query(sql, {
                    replacements: { setting_name: 'system_win_percent' },
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
        })
    }

    async getVolumebyStatus(status, time_start, time_end) {
        return new Promise(async function (resolve, reject) {
            let result = null;
            let sql =
                'SELECT sum(`amount`) as total FROM `orders` WHERE `type` = "live" AND `status` = :status AND `admin_setup` = 0 AND (created_at BETWEEN :time_start AND :time_end)';
            try {
                let data = await db.query(sql, {
                    replacements: { status, time_start, time_end },
                    type: QueryTypes.SELECT,
                });
                if (data.length > 0) {
                    result = JSON.parse(JSON.stringify(data[0], null, 2));
                }
                resolve(result);
            } catch (error) {
                log.error(error)
                reject(error);
            }
        })
    }

    async getVolume24hYesterday(time_start, time_end) {
        return new Promise(async function (resolve, reject) {
            let result = null;
            let sql = 'SELECT sum(`amount`) as total FROM `orders` WHERE (created_at BETWEEN :time_start AND :time_end) AND `admin_setup` = 0';
            try {
                let data = await db.query(sql, {
                    replacements: { time_start, time_end },
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
        })
    }
}

// Export module
module.exports.RoundClose = RoundClose
