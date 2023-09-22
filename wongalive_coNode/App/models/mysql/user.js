module.exports = function(Sequelize, Schema){
	var User = Schema.define('users', {	
		username:{
	    type: Sequelize.STRING
	  },
	  first_name:{
	    type: Sequelize.STRING
	  },		
	  last_name:{
	    type: Sequelize.STRING
		},
		contact_number:{
	    type: Sequelize.STRING
	  },
	  email:{
	    type: Sequelize.STRING
		},
		password:{
	    type: Sequelize.STRING
	  },
	  user_type:{
	    type: Sequelize.ENUM('superadmin', 'trainer','front_user')
	  },
	  status:{
	    type: Sequelize.ENUM('active', 'block'), defaultValue:'active'
	  },
	  avatar:{
	    type: Sequelize.STRING, defaultValue: 'default.png'
		},
		device_token :{
	    type: Sequelize.STRING, defaultValue: ''
	  },
	  device_type:{
	    type: Sequelize.ENUM('IOS', 'ANDROID')
		},
	  social_id:{
	    type: Sequelize.STRING
	  },
	  social_media:{
	    type: Sequelize.STRING
	  },
	  sign_up_as:{
			type: Sequelize.STRING
	  },
	  link_code:{
			type: Sequelize.STRING
		},
		remember_token:{
	    type: Sequelize.STRING
	  },
	  otp_varifiy:{
	    type: Sequelize.STRING
		},  
		follow_flge:{
	    type: Sequelize.INTEGER
		},
		// email_verified_at:{
	  //   type: Sequelize.STRING
		// },
		socket_id:{
	    type: Sequelize.STRING
		},
		notification:{
	    type: Sequelize.ENUM('1', '0'), defaultValue:'1'
		},
		isReport:{
	    type: Sequelize.ENUM('1', '0'), defaultValue:'0'
		},
		available_flag:{
	    type: Sequelize.ENUM('online', 'offline'), defaultValue:'offline'
		},
		block_status:{
	    type: Sequelize.ENUM('active', 'block'), defaultValue:'active'
		},
		deleted_at:{
	    type: Sequelize.STRING
		},
		diamond:{
	    type: Sequelize.STRING
		},
		socket_time:{
	    	type: Sequelize.STRING, defaultValue:null  //socket connect time update
		},
	}, {underscored: true});

	User.sync({force: false});

	return User;
}