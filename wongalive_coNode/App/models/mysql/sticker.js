module.exports = function(Sequelize, Schema){
	var sticker = Schema.define('sticker', {	
	    avatar:{
            type: Sequelize.STRING, defaultValue:null
        },
        name:{
            type: Sequelize.STRING, defaultValue:null
        },
        status:{
            type: Sequelize.ENUM('active', 'block'), defaultValue:'active'
        }
	}, {underscored: true,freezeTableName: true});

	sticker.sync({force: false});

	return sticker;
}