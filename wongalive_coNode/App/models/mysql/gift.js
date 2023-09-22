module.exports = function(Sequelize, Schema){
	var gift = Schema.define('gift', {	
	    avatar:{
            type: Sequelize.STRING, defaultValue:null
        },
        name:{
            type: Sequelize.STRING, defaultValue:null
        },
        price:{
            type: Sequelize.STRING, defaultValue:null
        },
        status:{
            type: Sequelize.ENUM('active', 'block'), defaultValue:'active'
        }
	}, {underscored: true,freezeTableName: true});

	gift.sync({force: false});

	return gift;
}