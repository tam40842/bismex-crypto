const db = require('./db');

function getAllMarket(callback) {
    var sql = 'SELECT * FROM markets';
    db.simpleQuery(sql, [], (data) => {
        if (data && data.length > 0) {
            callback(data);
        } else {
            callback(false)
        }
    })
}

function getMarketByMarketname(marketname, callback) {
    var sql = 'SELECT * FROM `markets` WHERE market_name=?';
    db.simpleQuery(sql, [marketname], (data) => {
        if (data && data.length > 0) {
            callback(data[0]);
        } else {
            callback(false)
        }
    })
}

function getFeeByHour(hour, callback) {
    var sql = "SELECT * FROM `trade_fee` WHERE hour = ?";
    db.simpleQuery(sql, [hour], (data) => {
        if (data && data.length > 0) {
            callback(data[0])
        } else {
            callback(false);
        }
    })
}

function getListByType(type, callback) {
    var sql = "SELECT market_name, random FROM markets where type=?";
    db.simpleQuery(sql, [type], (data) => {
        if (data) {
            callback(data)
        } else {
            callback(false)   
        }
    })
}

module.exports =  {
    getAllMarket, getListByType, getFeeByHour, getMarketByMarketname
}