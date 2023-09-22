module.exports = function (Sequelize, Schema) {
	var module = {};
	
	module.User = require('./user')(Sequelize, Schema);
	module.Chat = require('./chat')(Sequelize, Schema);
	module.ChatMessages = require('./chatmessages')(Sequelize, Schema);
	module.ReportCategory = require('./reportCategory')(Sequelize, Schema);
	module.BlockUserList = require('./blockUserList')(Sequelize, Schema);
	module.live_notification = require('./live_notification')(Sequelize, Schema);
	module.livecomment = require('./livecomment')(Sequelize, Schema);
	module.gift_diamonds = require('./gift_diamonds')(Sequelize, Schema);
	module.Gift = require('./gift')(Sequelize, Schema);
	module.follow_users = require('./follow_users')(Sequelize, Schema);
	module.notifications = require('./notifications')(Sequelize, Schema);
	module.Sticker = require('./sticker')(Sequelize, Schema);
	return module;
}
