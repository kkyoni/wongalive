module.exports = function(Sequelize, Schema){
	var gift_diamonds = Schema.define('gift_diamonds', {	
	    sender_id:{
            type: Sequelize.STRING, defaultValue:null
        },
        receive_id:{
            type: Sequelize.STRING, defaultValue:null
        },
        gift_id:{
            type: Sequelize.STRING, defaultValue:null
        },
        gift_diamond:{
            type: Sequelize.STRING, defaultValue:null
        },
        unique_id:{
            type: Sequelize.STRING
        },
        pkmode:{
            type: Sequelize.ENUM('true', 'false',''), defaultValue:''
        },
	}, {underscored: true,freezeTableName: true});

	gift_diamonds.sync({force: false});

	return gift_diamonds;
}