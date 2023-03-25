# Binance-Stream
Streaming data from binance page

## Install node module and run:
```sh
$ npm install
$ npm start // Run with default port 8080
$ npm start --port="8000" // Run with  port 8000(run in localhost)
$ npm install -g pm2 // pm2 package manage nodejs process
$ pm2 start index.js // Run on server
```

## Config connect db:
```json
{
    "config":{
        "database": {
            "host": "mongodb://localhost:27017/binance" 
        }
    }
}
```
