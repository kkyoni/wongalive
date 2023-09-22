module.exports = function(Sequelize, Schema){
	var notifications = Schema.define('notifications', {	
	    user_id:{
            type: Sequelize.STRING, defaultValue:null
        },
        follow_user:{
            type: Sequelize.STRING, defaultValue:null
        },		
        title:{
            type: Sequelize.STRING
        },		
        description:{
            type: Sequelize.STRING
        },
        status:{
            type: Sequelize.ENUM('read', 'unread'), defaultValue:null
        },
        flag_status:{
            type: Sequelize.STRING, defaultValue:null
        },
		follow_status:{
            type: Sequelize.STRING, defaultValue:null
        },
        channel_id:{
            type: Sequelize.STRING, defaultValue:null
        }
	}, {underscored: true,freezeTableName: true});

	notifications.sync({force: false});

	return notifications;
}