// import the necessary modules
'use strict';
const Sequelize = require('sequelize');
const Model = Sequelize.Model;
class Kline extends Model { }
class MysqlKline {
    constructor(sequelize) {
        Kline.init({
            marketname: {
                type: Sequelize.STRING,
            },
            open: {
                type: Sequelize.STRING
            },
            close: {
                type: Sequelize.STRING
            },
            high: {
                type: Sequelize.STRING
            },
            low: {
                type: Sequelize.STRING
            },
            time: {
                type: Sequelize.STRING
            },
            quote_vol:{
                type: Sequelize.STRING
            },
        }, {
            sequelize,
            modelName: 'tb_candle',
            freezeTableName: true,
            timestamps: false
        });

        return Kline;
    }
}

module.exports = MysqlKline
