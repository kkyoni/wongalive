module.exports = function(Sequelize, Schema){
	var BlockUserList = Schema.define('block_user_lists', {
	  user_id:{
	  	type: Sequelize.STRING
	  },
	  blocked_user_id:{
	  	  type: Sequelize.STRING
	  },
		deleted_at:{
			type: Sequelize.STRING, defaultValue:''
	  }
	}, {underscored: true});

	BlockUserList.sync({force: false});
	return BlockUserList;
}