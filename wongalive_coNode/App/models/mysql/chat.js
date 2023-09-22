module.exports = function(Sequelize, Schema){
	var Chat = Schema.define('chat_masters', {
	  chat_id:{
	  	type: Sequelize.STRING
	  },
	  sender_id:{
	  	  type: Sequelize.STRING
	  },
	  receiver_id:{
	    	type: Sequelize.STRING
	  },
	  deleted_at:{
	    	type: Sequelize.STRING, defaultValue:''
	   }
	}, {underscored: true});

	Chat.sync({force: false});
	return Chat;
}