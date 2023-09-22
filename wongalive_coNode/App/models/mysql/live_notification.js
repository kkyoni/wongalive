module.exports = function(Sequelize, Schema){
	var live_notification = Schema.define('live_notification', {	
	    user_id:{
            type: Sequelize.STRING, defaultValue:null
        },
        follow_user:{
            type: Sequelize.STRING, defaultValue:null
        },		
        u_id:{
            type: Sequelize.STRING
        },
		status:{
            type: Sequelize.ENUM('pending', 'online', 'offline'), defaultValue:'pending'
        },
        dual_status:{
            type: Sequelize.ENUM('requested', 'accepted', 'rejected'), defaultValue:null
        },
		chat_flag:{
            type: Sequelize.ENUM('true', 'false'), defaultValue:'false'
        },
        control_buttons:{
            type: Sequelize.ENUM('true', 'false'), defaultValue:'false'
        },
        filters:{
            type: Sequelize.ENUM('yes', 'no'), defaultValue:'no'
        },
        viewer:{
            type: Sequelize.ENUM('0', '1'), defaultValue:'0'
        },
        live_no :{
            type: Sequelize.ENUM('0', '1'), defaultValue:'1'
        },
        timer :{
            type: Sequelize.STRING, defaultValue:''
        },
        title :{
            type: Sequelize.STRING, defaultValue:''
        },
        pkmode:{
            type: Sequelize.ENUM('true', 'false',''), defaultValue:''
        },
	}, {underscored: true,freezeTableName: true});

	live_notification.sync({force: false});

	return live_notification;
}