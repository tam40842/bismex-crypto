const truefx = require('truefx')
const market = require('../models/market');

const PAIR_TYPE = "FOREX";


function getPrice(callback) {
    setInterval(function () {
        market.getListByType(PAIR_TYPE, (data) => {
            var arrMkn = [];
            if (data) {
                for (let i = 0; i < data.length; i++) {
                    // arrMkn.push(data[i].market_name);
                    // truefx.get(arrMkn.toString()).then(response => {
                    truefx.get(data[i].market_name).then(response => {
                        response.forEach(function(value) {
                            var symbol = value.symbol.replace('/', '');
                            callback({
                                marketname: symbol,
                                random: data[i].random,
                                price: value.bid,
                            })
                        })
                    });
                }
            }
        })
    }, 1000);
}
module.exports = {
    getPrice,
}