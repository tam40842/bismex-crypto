var request = require('request')
const generator = require('generate-password');

function arrToDic(arr, key) {
  if (!arr || !Array.isArray(arr)) {
    return {}
  }

  let dic = {}
  arr.forEach(item => {
    dic[item[key]] = item
  });
  return dic
}

function getCurrentRound() {
  var minute = 60 * 1000;
  var round = parseInt(moment().valueOf() / minute) * (minute);
  return round;
}

function getPreviousRound() {
  var minute = 60 * 1000;
  var round = parseInt(moment().valueOf() / minute) * (minute) - minute;
  return round;
}

function jsonStringToArr(jsonString) {
  if (!jsonString) {
    return []
  }

  let arr = []
  try {
    const json = JSON.parse(jsonString)
    for (var key in json) {
      const item = json[key]
      arr.push(item)
    }
    return arr
  } catch (error) {
    return []
  }
}

function makeRequest(objRequest) {
  request({
    uri: objRequest.url,
    method: objRequest.method,
    form: objRequest.form,
    headers: objRequest.headers
  },
    function (error, response, body) {
      objRequest.callback(body);
    }
  );
}

function generateToken(length) {
  var token = generator.generate({
    length,
    numbers: true,
  });
  token = md5(token)
  return token
}

function getRandomInt(max) {
  return Math.floor(Math.random() * Math.floor(max));
}

module.exports = {
  arrToDic,
  getCurrentRound,
  getPreviousRound,
  jsonStringToArr,
  makeRequest,
  generateToken,
  getRandomInt
}