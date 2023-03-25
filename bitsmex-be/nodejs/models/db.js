const mysql = require('mysql');
require('dotenv').config({ path: '../.env' })

const dbConfig = {
    host: process.env.DB_HOST,
    user: process.env.DB_USERNAME,
    password: process.env.DB_PASSWORD,
    database: process.env.DB_DATABASE,
    connectionLimit: 15,
    multipleStatements: true
};


var connection;
const TIME_OUT_QUERY_SQL = 20000
connectMySql();

function connectMySql() {
    connection = mysql.createConnection(dbConfig);

    connection.on('error', function (err) {
        connection = mysql.createConnection(dbConfig);
    });

    connection.on('close', function (err) {
        if (err) {
            console.error("SQL Connection Closed");
            connection = mysql.createConnection(dbConfig);
        } else {
            console.error('Manually called .end()');
        }
    });
}

function simpleQuery(sql, values, callback, verbose = true) {
    try {
        connection.query({
            sql: sql,
            values: values,
            timeout: TIME_OUT_QUERY_SQL
        }, function (error, results, fields) {
    
            if (error != null) {
                console.error(error);
                log.info(error)
                if (connection.state === 'disconnected') {
                    connectMySql();
                } else {
                    if (verbose) {
                        // console.error(error);
                    }
                }
            }
            if (typeof callback == 'function') {
                callback(results, error)
            }
        });
    } catch(e) {
        
    }
}

module.exports = {
    simpleQuery
}
