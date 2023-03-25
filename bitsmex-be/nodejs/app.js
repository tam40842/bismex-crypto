const path = require('path')
const_DIRECTION = {
  ADJUSTMENT: true,
}
const fs = require('fs')
const https = require('https')
const bodyParser = require('body-parser');
const cors = require('cors')
express = require('express');
app = express();
var http = require('http')
require('dotenv').config({ path: '../.env' })
// timezone = process.env.TIMEZONE;
let arrUser = [];

require('./models/db.js')
require('./socket')