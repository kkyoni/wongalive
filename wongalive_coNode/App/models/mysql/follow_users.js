module.exports = function(Sequelize, Schema){
	var follow_users = Schema.define('follow_users', {	
	    user_id:{
            type: Sequelize.STRING, defaultValue:null
        },
        followed_user_id:{
            type: Sequelize.STRING, defaultValue:null
        },
        status:{
            type: Sequelize.STRING, defaultValue:null
        },
        deleted_at:{
            type: Sequelize.STRING, defaultValue:null
        },
	}, {underscored: true,freezeTableName: true});

	follow_users.sync({force: false});

	return follow_users;
}