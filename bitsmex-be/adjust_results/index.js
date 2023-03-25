const App = require('./app.js').App

async function init(){
    let app = await new App();
    return app;
}

var app = init();