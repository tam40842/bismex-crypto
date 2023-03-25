var db = require('./db')

function insertCandle(candle, callback) {
  var sql = `INSERT INTO tb_candle(marketname,close,high,time,low,open,quote_vol) VALUES(?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE close=?,high=?,low=?`;
  const {
    s,
    c,
    h,
    t,
    l,
    o,
    q
  } = candle;
  db.simpleQuery(sql, [s, c, h, t, l, o, q, c, h, l], (data) => {
    if (data && data.insertId)
      callback(data)
    else
      callback(false)
  })
}

function insertBlur(marketname, result, round, callback) {
  var sql = `INSERT INTO blur(marketname,result,round) VALUES(?,?,?)`;
  db.simpleQuery(sql, [marketname, result, round], (data) => {
    if (data && data.insertId)
      callback(data)
    else
      callback(false)
  })
}

function getCandleByMarket(marketname, callback) {
  var sql = "SELECT `id` as seq, `marketname` as s, `base_vol`, `close` as c, `high` as h, `time` as t, `low` as l, `open` as o, `quote_vol` as q FROM tb_candle WHERE marketname=? ORDER BY id desc LIMIT 10000";
  db.simpleQuery(sql, [marketname], (data) => {
    if (data)
      callback(data)
    else
      callback(false)
  })
}

function getCandleNext(seq, marketname, callback) {
  var sql = "SELECT `id` as seq, `marketname`, `base_vol`, `close`, `high`, `time` as id, `low`, `open`, `quote_vol` FROM tb_candle WHERE marketname=? and id>? ORDER BY id desc LIMIT 1";
  db.simpleQuery(sql, [marketname, seq], (data) => {
    if (data) {
      callback(data);
    } else {
      callback(false);
    }
  });
}
//LOGIC ROBOT 
function getRandomCandle(round, callback) {
  var sql = "SELECT * FROM `tb_candle`,tb_market WHERE tb_candle.marketname = tb_market.marketname AND tb_market.active=1 AND tb_candle.time = ? order by rand() DESC limit 1"
  db.simpleQuery(sql, [round], (data) => {
    if (data && data.length > 0) {
      callback(data[0])
    } else {
      callback(false)
    }
  });
}

function getCandleInTime(marketname, time, callback) {
    let sql = "SELECT * FROM `tb_candle` WHERE tb_candle.marketname = ? AND tb_candle.time = ?"
    db.simpleQuery(sql, [marketname, time], (data) => {
        if (data && data.length > 0) {
        callback(data[data.length - 1])
        }
        else {
        callback(false)
        }
    })
}

function getClosePrice(marketname, round, callback) {
    let sql = "SELECT `close` as price FROM `tb_candle` WHERE tb_candle.marketname = ? AND tb_candle.time = ? order by `id` desc limit 1"
    db.simpleQuery(sql, [marketname, round], (data) => {
        if (data) {
            callback(data.price)
        }
        else {
            callback(false)
        }
    })
}




module.exports = {
  insertCandle,
  getCandleByMarket,
  getCandleNext,
  getRandomCandle,
  getCandleInTime,
  getClosePrice,
  insertBlur
}
