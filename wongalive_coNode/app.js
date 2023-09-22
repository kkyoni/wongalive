var express = require('express');
var app = express();
var multer = require('multer')
// var port = process.env.PORT || 711;
var port = 711;

var flash = require('connect-flash');
var path = require('path');
var morgan = require('morgan');
var cookieParser = require('cookie-parser');
global.moment = require('moment');
var session = require('express-session');
var cookieSession = require('cookie-session');
var bodyParser = require('body-parser');
var dateFormat = require('dateformat');
var fileUpload = require('express-fileupload');
const expressValidator = require('express-validator');

var nunjucks  = require('nunjucks');
global.now = new Date();
global.dateFormat = dateFormat;

var server = require('http').createServer(app);

io = require('socket.io')(server);
let config = require('./config/constants.js');
var Sequelize = require('sequelize');
global.Sequelize = Sequelize;
var sequelizeDB = require('./config/database.js')(Sequelize);
global.sequelize1 = sequelizeDB;
require('./config/logconfig.js');
var passport = require('passport');
app.use(passport.initialize());
app.use(passport.session());

app.use(expressValidator());
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({extended: true}));
app.use(cookieParser());

//set in headers in every request
app.use(function(req, res, next) {
  res.header("Access-Control-Allow-Origin", "*");
  res.header("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");
  next();
});
app.use(cookieSession({
  name: 'session',
  keys: ["coinflipcookie"],
  maxAge: 24 * 60 * 60 * 1000 // 24 hours
}));

app.use(flash());
app.use(fileUpload());

//Start: Server connection
app.set('port', port);
server.listen(port, function(){
  console.log("(----------------------------------------)");  
  console.log("|          Server Started at...          |");
  console.log("|       " + config.baseUrl + "       |");
  console.log("(----------------------------------------)");
});
//End: Server connection

var model = require('./App/models/mysql/index')(Sequelize, sequelizeDB);
var controllers = require('./App/controllers/index')(model);
require('./routes/index.js')(app, model, controllers);
var socket_count=0;
//Start: Socket connection code
io.on('connection', function (client) {
	
  socket_count++;
  require('./socket/index')(model, io, client);
  client.on('disconnect', function () { 
    socket_count--;
    if(client.myData){

      console.log("user disconnected -->",client.myData);
      let today = new Date();
      today = dateFormat(today, "yyyy-mm-dd HH:MM:ss", true);
      model.User.update({ socket_time: today}, { where: { id: client.myData.userId } });
      // model.User.update({ available_flag: 'offline'}, { where: { id: client.myData.userId } });
      // model.live_notification.update({ status:'offline' }, { where: { user_id: client.myData.userId } });
    }
    console.log("Socket disconnected",socket_count); 
  });
});
//End: Socket connection code

//catch 404 and forward to error handler
require('./config/error.js')(app);
module.exports = {app:app, server:server}
