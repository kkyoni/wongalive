module.exports = function(Sequelize, Schema){
	var ChatMessages = Schema.define('chat_messages', {
	  chat_id:{
	  	type: Sequelize.STRING
	  },
	  sender_id:{
	  	  type: Sequelize.STRING
	  },
	  receiver_id:{
	    	type: Sequelize.STRING
	  },
	  message:{
	    	type: Sequelize.STRING
	  },
	  deleted_at:{
	    	type: Sequelize.STRING, defaultValue:''
	   },
	  isRead:{
	    type: Sequelize.ENUM('1', '0'), defaultValue:'0'
	  },
	  isDelivered : {
	  	type: Sequelize.ENUM('1', '0'), defaultValue:'0'
	  }
	}, {underscored: true});

	ChatMessages.sync({force: false});
	return ChatMessages;
}