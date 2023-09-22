module.exports = function(Sequelize, Schema){
	var ReportCategory = Schema.define('report_category', {
      category:{
	  	type: Sequelize.STRING
	  },
      status:{
	    type: Sequelize.ENUM('active', 'block'), defaultValue:'active'
      }
	}, {underscored: true});

	ReportCategory.sync({force: false});
	return ReportCategory;
}