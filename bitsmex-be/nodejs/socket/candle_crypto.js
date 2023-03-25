const binance = require('node-binance-api')().options({
    APIKEY: '<key>',
    APISECRET: '<secret>',
    useServerTime: true,
});
const db = require('../models/db.js');
var moment = require("moment-timezone");
const market = require('../models/market');

const PAIR_TYPE = "CRYPTO";

function getPrice(callback) {
    setInterval(function() {
        market.getListByType(PAIR_TYPE, (data) => {
            var arrMkn = [];
            if (data) {
                for (let i = 0; i < data.length; i++) {
                    arrMkn.push(data[i].market_name);
                    binance.prices(data[i].market_name, (error, trades) => {
                        if(trades) {
                            callback({
                                marketname: data[i].market_name,
                                random: data[i].random,
                                price: trades[data[i].market_name]
                            })
                        } else {
                            callback(false);
                        }
                    });
                }
            }
        })
    }, 1000);
}

module.exports = {
    getPrice,
}