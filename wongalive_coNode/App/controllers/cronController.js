var CronJob = require('cron').CronJob;
console.log("cron loaded")
module.exports = function (model) {
	var module = {};	

	//This cron used for when Main HOST kill App then socket disconnected and HOST do not connect socket again within 3 minutes then update status offline for all participant as well as host and call socket listner(GetHomeSocketUser) to update all participant
	new CronJob('* * * * * *', async function() {
		//Start: Update availbale flag of user offline for all user who have socket disconnected and App kill
		let userList = await sequelize1.query("SELECT u.id,u.username,u.socket_time FROM users u WHERE u.available_flag='online'",{
			type:"SELECT"
		});
		for(let i=0;i<userList.length;i++){
			if(userList[i].socket_time){
				console.log("valid socket");
				let currDate = new Date();
				let today = new Date(userList[i].socket_time);				
				let minutes = parseInt(Math.abs(currDate.getTime() - today.getTime()) / (1000 * 60) % 60);
				console.log("minutes ",minutes);
				if(minutes>=3){
					model.User.update({ "available_flag": "offline" }, { where: { id: userList[i].id } });
				}
			}
		}
		//End: Update availbale flag of user offline for all user who have socket disconnected and App kill
		
		let userData = await sequelize1.query("SELECT u.id,u.username,u.avatar,u.socket_time,ln.u_id FROM live_notification ln JOIN users u ON ln.user_id=u.id WHERE ln.follow_user IS NULL AND (ln.status='pending' OR ln.status='online')",{
			type:"SELECT"
		});
		try{

			//console.log("cron userData length",userData.length);
			for(let i=0;i<userData.length;i++){
				if(userData[i].socket_time){
					console.log("valid");
					let currDate = new Date();
					let today = new Date(userData[i].socket_time);
					
					let minutes = parseInt(Math.abs(currDate.getTime() - today.getTime()) / (1000 * 60) % 60);
					console.log("minutes ",minutes);
					if(minutes>=3){
						let notiData = await model.live_notification.findAll({where:{u_id:userData[i].u_id}});
						let notificationData = await model.notifications.findAll({where:{channel_id:userData[i].u_id}});
						for(let i=0;i<notiData.length;i++){
							notiData[i].set({status:"offline"});
							notiData[i].save();
						}
						for(let i=0;i<notificationData.length;i++){
							notificationData[i].set({flag_status:"offline"});
							notificationData[i].save();
						}
						let socketUserRes = {
							"status": "success",
							"message": `User offline`,
							data:[{
								id : userData[i].id,
								avatar : userData[i].avatar,
								u_id : userData[i].u_id,
								username : userData[i].username,
								status : "offline"
							}]					
						}
						io.emit('GetHomeSocketUser', socketUserRes);
					}
					console.log("today ",today);
					console.log("currDate ",currDate);
				}
			}
		}catch(e){
			console.log("error sad",e);
		}
	  }, null, true, 'America/Los_Angeles');

	return module;
}