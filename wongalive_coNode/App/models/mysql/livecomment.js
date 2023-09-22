module.exports = function(Sequelize, Schema){
	var livecomment = Schema.define('livecomment', {	
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
            type: Sequelize.ENUM('requested', 'comment', 'join'), defaultValue:''
        },
        comment:{
            type: Sequelize.STRING
        },
	}, {underscored: true,freezeTableName: true});

	livecomment.sync({force: false});

	return livecomment;
}