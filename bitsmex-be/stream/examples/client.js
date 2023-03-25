var io = require('socket.io-client');
var socket = io.connect('http://144.202.110.72:8080', { reconnect: true });

// Add a connect listener
socket.on('connect', function () {
    socket.emit('join', 'btcusdt');
    console.log('Connected!');
});

socket.on('message', function (data) {
    console.log('Incoming message:', data);
});
