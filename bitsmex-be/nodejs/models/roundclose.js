const db = require('./db');
var moment = require("moment-timezone");
const candleDb = require('../models/candle');

var user_win_total = 0;
var user_lose_total = 0;
var volume24h_yesterday = 0;
var profit_estimate = 0;
var profit_today = 0;

function getSetting(callback) {
    var sql = 'SELECT `setting_value` as value FROM `settings` WHERE `setting_name` = ?';
    db.simpleQuery(sql, ['system_win_percent'], (data) => {
        if (data) {
            callback(data[0]);
        } else {
            callback(false)
        }
    })
}

async function getVolumebyStatus(status, time_start, time_end, callback) {
    var sql = 'SELECT sum(`amount`) as total FROM `orders` WHERE `type` = "live" AND `status` = ? AND (created_at BETWEEN ? AND ?)';
    db.simpleQuery(sql, [status, time_start, time_end], (data) => {
        if (data) {
            callback(data[0]);
        } else {
            callback(false)
        }
    })
}

async function getVolume24hYesterday(time_start, time_end, callback) {
    var sql = 'SELECT sum(`amount`) as total FROM `orders` WHERE (created_at BETWEEN ? AND ?)';
    db.simpleQuery(sql, [time_start, time_end], (data) => {
        if (data) {
            callback(data[0]);
        } else {
            callback(false)
        }
    })
}

function stastics(callback) {
    getSetting(percent => {
        var system_win_percent = percent.value;
        var first_time = moment().format('YYYY-MM-DD');
        var last_time = moment().format('YYYY-MM-DD HH:mm:SS');
        var yesterday_start = moment().subtract(1, 'days').format('YYYY-MM-DD');
        var yesterday_end = moment().subtract(1, 'days').format('YYYY-MM-DD') + " 23:59:59";
        getVolumebyStatus(1, first_time, last_time, function(data) {
            if(data.total) {
                user_win_total = data.total;
            }
            getVolumebyStatus(2, first_time, last_time, function(data) {
                if(data.total) {
                    user_lose_total = data.total;
                }
                getVolume24hYesterday(yesterday_start, yesterday_end, function(data) {
                    if(data.total) {
                        volume24h_yesterday = data.total;
                    }
                    // đây là dữ liệu ảo volume của ngày hôm qua
                    volume24h_yesterday = volume24h_yesterday <= 20000 ? 20000 : volume24h_yesterday; // xóa dòng này đi để chạy dữ liệu thực
                    profit_estimate = volume24h_yesterday * system_win_percent / 100;
                    profit_today = user_lose_total - user_win_total;
                    callback(true);
                });
            });
        });
    })
}

function updateResult(result, marketname) {
    var sql = 'UPDATE `markets` set `result` = ? where `market_name` = ?';
    db.simpleQuery(sql, [result, marketname], (data) => {

    });
}

function SumByRound(expired_at, action, marketname, callback) {
    return new Promise((resolve, reject) => {
        var sql = 'SELECT sum(`amount`) as total FROM `orders` WHERE `action` = ? AND `market_name` = ? AND `type` = ? AND `status` = 0 AND `expired_at` = ?';
        db.simpleQuery(sql, [action, marketname, 'live', expired_at], (data) => {
            if (data && data.length > 0) {
                resolve(data[0].total)
            } else {
                resolve({
                    status: false,
                    data
                })
            }
        });
    });
}

function getLastCandle(marketname, timeround, callback) {
    var sql = 'SELECT * FROM `tb_candle` WHERE `marketname` = ? AND `time` = ? order by `id` desc limit 1';
    db.simpleQuery(sql, [marketname, timeround], (data) => {
        if (data && data.length > 0) {
            callback(data[0]);
        } else {
            callback(false);
        }
    });
}

function adjust(callback) {
    var round_time = moment().add(1, 'minutes').format('YYYY-MM-DD HH:mm');
    var sql = 'SELECT * FROM `orders` WHERE `status` = ? AND `type` = ? AND `expired_at` = ? GROUP BY `market_name`';
    db.simpleQuery(sql, [0, 'live', round_time], (data) => {
        if (data && data.length > 0) {
            for(let i = 0; i < data.length; i++) {
                let arrPromise = [];
                arrPromise.push(SumByRound(round_time, 'BUY', data[i].market_name));
                arrPromise.push(SumByRound(round_time, 'SELL', data[i].market_name));
                Promise.all(arrPromise).then(results => {
                    var total_buy = results[0] ? results[0] : 0;
                    var total_sell = results[1] ? results[1] : 0;
                    if(total_buy > total_sell) {
                        // Nếu số lượng người đánh BUY nhiều hơn SELL thì cho nến đỏ
                        updateResult('SELL', data[i].market_name);
                        callback({
                            marketname: data[i].market_name,
                            adjust: "SELL"
                        });
                    }

                    if(total_buy < total_sell) {
                        updateResult('BUY', data[i].market_name);
                        callback({
                            marketname: data[i].market_name,
                            adjust: "BUY"
                        });
                    }
                }).catch(error => {
                    console.log('error', error);
                })
            }
        }
    });
}

function handle(callback) {
    stastics(stastics => {
        console.log('Yesterday volume: '+parseFloat(profit_estimate), 'Today profit: '+parseFloat(profit_today));
        if(parseFloat(profit_today) < parseFloat(profit_estimate)) {
            adjust(data => {
                callback(data);
            });
        }
    });
}

module.exports = {
    handle
}