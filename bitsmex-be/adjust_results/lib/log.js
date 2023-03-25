/**
 * Module dependencies.
 */
var winston = require('winston')

/**
 * Log constructor
 */
function Log() {
    // Config template console print
    const customConsoleFormat = winston.format.printf(({ level, message, label, timestamp }) => {
        return `${timestamp} [${level}]: ${message}`
    })

    // Config template file print
    const customFileFormat = winston.format.printf(({ level, message, label, timestamp }) => {
        return `${timestamp} [${level}]: ${message}`
    })

    // Init winston log
    this.logger = new winston.createLogger({
        transports: [
            new winston.transports.Console({
                format: winston.format.combine(
                    winston.format.colorize(),
                    winston.format.simple(),
                    winston.format.label({ label: 'Error' }),
                    winston.format.timestamp({ format: 'YYYY-MM-DD HH:mm:ss' }),
                    customConsoleFormat
                ),
            }),
            new winston.transports.File({
                filename: './logs/logs.log',
                format: winston.format.combine(winston.format.timestamp({ format: 'YYYY-MM-DD HH:mm:ss' }), customFileFormat),
            }),
        ],
        exceptionHandlers: [
            new winston.transports.File({
                filename: './logs/exceptions.log',
                format: winston.format.combine(winston.format.timestamp({ format: 'YYYY-MM-DD HH:mm:ss' }), customFileFormat),
            }),
        ],
    })
}

/**
 * Error Function
 * @param String meg
 */
Log.prototype.error = function (meg) {
    this.logger.error(meg)
}

/**
 * Info Function
 * @param String meg
 */
Log.prototype.info = function (meg) {
    this.logger.info(meg)
}

// Export module
module.exports = Log
