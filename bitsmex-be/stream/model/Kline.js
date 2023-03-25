// import the necessary modules
'use strict';

const mongoose = require('mongoose');

// create an export function to encapsulate the model creation
const KlineSchema = new mongoose.Schema({
    s: String,  // Symbol
    o: String,  // Open price
    c: String,  // Close price
    h: String,  // High price
    l: String,  // Low price
    t: String // Format time
});

module.exports = mongoose.model('Kline', KlineSchema);
