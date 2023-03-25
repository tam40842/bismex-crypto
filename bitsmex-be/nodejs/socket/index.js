const market = require('../models/market');
var moment = require("moment-timezone");
const candleDb = require('../models/candle');
const roundclose = require('../models/roundclose');

let ADJUSTRESULT = '';
let ADJUSTMARKET = '';

function systemAdjusted() {
    let now = moment()
    let sec = now.seconds()

    if(sec == 30) {
        try {
            roundclose.handle(data => {
                ADJUSTMARKET = data.marketname;
                ADJUSTRESULT = data.adjust
                console.log("Thuat toan: DIEU CHINH CHART", data.marketname, "THANH " + data.adjust)
            });
        } catch(e) {
            console.log(e);
        }
    }
    if(sec < 45) {
        ADJUSTMARKET = '';
        ADJUSTRESULT = '';
    }
}

setInterval(() => {
    let now = moment()
    let sec = now.seconds()
    systemAdjusted();
}, 1000);