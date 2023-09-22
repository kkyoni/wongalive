module.exports = function(dataBaseType){
	
	//Start: sequelize database connection
	// var sequelize = new dataBaseType('sprtatht_demo', 'sprtatht_demo', 'MishubEH', {
	
	//Local DB Connection	
	// var sequelize = new dataBaseType('ais_wlive_stg', 'root', '', {
	// 		host: 'localhost',
	// 		dialect: 'mysql',
	// 		operatorsAliases: false,
	// 		logging: true,
	// 		//port : 3702,
	// 		pool: {
	// 			max: 50000,
	// 			min: 0,
	// 			acquire: 30000,
	// 			idle: 10000
	// 		}
	// 	});	
	
	
		//Staging DB Connection
	var sequelize = new dataBaseType('ais_wlive_stg', 'ais_wlive_stg', 'nkDrUHsw', {
	  host: '192.168.1.4',
	  dialect: 'mysql',
	  operatorsAliases: false,
	  logging: false,
	  port : 3702,
	  pool: {
	    max: 50000,
	    min: 0,
	    acquire: 30000,
	    idle: 10000
	  }
	});

	sequelize.authenticate().then(() => {
		console.log('Connection has been established successfully.');
	}).catch(err => {
		console.error('Unable to connect to the database:', err);
	});
	return sequelize;
	
}
