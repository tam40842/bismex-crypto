const finnhub = require('finnhub');
const api_key = finnhub.ApiClient.instance.authentications['api_key'];
api_key.apiKey = "bt419o748v6tg7b8hnfg" // Replace this
const finnhubClient = new finnhub.DefaultApi()
 
// Stock candles
finnhubClient.quote("APPL", (error, data, response) => {
    console.log(response)
});
