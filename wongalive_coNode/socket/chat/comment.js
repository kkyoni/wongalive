const Op = Sequelize.Op;
var dateformat = require('dateformat');
var striptags = require('striptags');
var moment = require("moment");
var tz = require('moment-timezone');
var timeZone = 'Asia/Kolkata';
moment.tz.setDefault(timeZone);

module.exports = function (model, io, client) {
	var module = {};
	

	module.LiveCountHost = async function (data, callback) {
		try {
			console.log("LiveCountHost Data",data);
			if(data.u_id){
				client.join(data.u_id);	//join in socket room
				let commentCondition={u_id:data.u_id};
				let giftCondition = {unique_id:data.u_id};
				console.time("LiveCountHost");
				let [commentData,usercount,requestedcount,notiData,giftData] = await Promise.all([
					model.livecomment.findAll({where:commentCondition}),
					model.live_notification.count({where:{u_id:data.u_id,status:"online"}}),
					model.live_notification.count({where:{u_id:data.u_id,dual_status:"requested"}}),
					model.live_notification.find({where:{u_id:data.u_id}}),
					model.gift_diamonds.findAll({where:giftCondition})
				]);
				//Start: Get unique user
				let userIds = [];
				for(let i=0;i<commentData.length;i++){
					userIds.push(commentData[i].follow_user);
				}
				var unique = userIds.filter(onlyUnique);
				let userData = await model.User.findAll({
					attributes: ['username', 'avatar','id'],
					where:{id:{[Op.in]:unique}}
				});
				let newCommentData = [];
				for(let i=0;i<commentData.length;i++){
					newCommentData[i] = commentData[i].dataValues;
					for(let j=0;j<userData.length;j++){
						if(commentData[i].follow_user == userData[j].id){
							newCommentData[i].username = userData[j].username;
							newCommentData[i].avatar = userData[j].avatar;
						}
					}
				}
				//End: Get unique user

				//Start: Get unique gift
				let giftIds = [];
				for(let i=0;i<giftData.length;i++){
					giftIds.push(giftData[i].gift_id);
				}
				var unique = giftIds.filter(onlyUnique);
				let giftInfo = await model.Gift.findAll({
					attributes: ['avatar','id'],
					where:{id:{[Op.in]:unique}}
				});
				let newGiftData = [];
				for(let i=0;i<giftData.length;i++){
					newGiftData[i] = giftData[i].dataValues;
					for(let j=0;j<giftInfo.length;j++){
						newGiftData[i].avatar = "";
						if(giftData[i].gift_id == giftInfo[j].id){
							newGiftData[i].avatar = giftInfo[j].avatar;
						}
					}
				}
				//End: Get unique gift
				console.timeEnd("LiveCountHost");
				
				return callback({
					usercount:usercount,
					requestedcount:requestedcount,
					chat_flag:notiData.chat_flag,
					control_buttons_flag:notiData.control_buttons,
					"requested": newCommentData,
					gift_diamond:newGiftData.length,
					gifts:newGiftData,
					"status": "success", 
					"message": "User in Live and Comments Broadacasting..", 
				 });
			}else{
				return callback({ "status": "fail", "message": "Invalid parameters", 'data': {} });
			}
		} catch (error) {
			console.log("catch error",error)
			return callback({ "status": "fail", "message": "Something went wrong.", 'data': {} });
		}
	};

	module.LiveCountUser = async function (data, callback) {
		try {
			console.log("LiveCountUser Data",data);
			if(data.u_id && data.userId){
				client.join(data.u_id);	//join in socket room
				let commentCondition={u_id:data.u_id};
				let giftCondition = {unique_id:data.u_id};
				console.time("LiveCountUser");
				let [commentData,usercount,notiData,giftData,notiUserData] = await Promise.all([
					model.livecomment.findAll({where:commentCondition}),
					model.live_notification.count({where:{u_id:data.u_id,status:"online"}}),					
					model.live_notification.find({where:{u_id:data.u_id}}),
					model.gift_diamonds.findAll({where:giftCondition}),
					model.live_notification.find({where:{u_id:data.u_id,follow_user:data.userId}}),
				]);
				let userIds = [];
				for(let i=0;i<commentData.length;i++){
					userIds.push(commentData[i].follow_user);
				}
				var unique = userIds.filter(onlyUnique);
				let userData = await model.User.findAll({
					attributes: ['username', 'avatar','id'],
					where:{id:{[Op.in]:unique}}
				});
				let newCommentData = [];
				for(let i=0;i<commentData.length;i++){
					newCommentData[i] = commentData[i].dataValues;
					for(let j=0;j<userData.length;j++){
						if(commentData[i].follow_user == userData[j].id){
							newCommentData[i].username = userData[j].username;
							newCommentData[i].avatar = userData[j].avatar;
						}
					}
				}

				//Start: Get unique gift
				let giftIds = [];
				for(let i=0;i<giftData.length;i++){
					giftIds.push(giftData[i].gift_id);
				}
				var unique = giftIds.filter(onlyUnique);
				let giftInfo = await model.Gift.findAll({
					attributes: ['avatar','id'],
					where:{id:{[Op.in]:unique}}
				});
				let newGiftData = [];
				for(let i=0;i<giftData.length;i++){
					newGiftData[i] = giftData[i].dataValues;
					for(let j=0;j<giftInfo.length;j++){
						newGiftData[i].avatar = "";
						if(giftData[i].gift_id == giftInfo[j].id){
							newGiftData[i].avatar = giftInfo[j].avatar;
						}
					}
				}
				//End: Get unique gift
				console.timeEnd("LiveCountUser");

				return callback({ 
					usercount:usercount,
					dual_flag:(notiUserData.dual_status)?notiUserData.dual_status:"",
					chat_flag:notiData.chat_flag,
					control_buttons_flag:notiData.control_buttons,
					"comments": newCommentData,
					gifts:newGiftData,
					"status": "success", 
					"message": "User in Live and Comments Broadacasting..", 
				 });
			}else{
				return callback({ "status": "fail", "message": "Invalid parameters", 'data': {} });
			}
		} catch (error) {
			console.log("catch error",error)
			return callback({ "status": "fail", "message": "Something went wrong.", 'data': {} });
		}
	};

	module.CreateCommentLive = async function (data, callback) {
		try {
			console.log("CreateCommentLive Data",data);
			if(data.u_id && data.user_id && data.comment && data.follow_id){
				client.join(data.u_id);	//join in socket room
				console.time("CreateCommentLive");
				let d = await model.livecomment.create({
					user_id : data.user_id,
					follow_user : data.follow_id,
					u_id : data.u_id,
					comment : data.comment,
					status : "comment"
				});
				let commentData = d.dataValues;
				let userData = await model.User.find({
					attributes: ['username', 'avatar','id'],
					where:{id:data.follow_id}
				});
				commentData.username=userData.username;
				commentData.avatar=userData.avatar;

				let response = {
					"requested" : [commentData],
					"status": "success",
					"message": "User in Live and Comments Broadacasting..!",
				}

				console.log("commentData",response);
				//io.emit("GetCommentLive",response);
				io.sockets.in(data.u_id).emit('GetCommentLive', response);	//broadcast message to room user only
				console.timeEnd("CreateCommentLive");				
				return callback({
					"status": "success", 
					"message": "Comment added successfully", 
				 });
			}else{
				return callback({ "status": "fail", "message": "Invalid parameters", 'data': {} });
			}
		} catch (error) {
			console.log("catch error",error)
			return callback({ "status": "fail", "message": "Something went wrong.", 'data': {} });
		}
	};

	module.CreateLiveStreaming = async function (data, callback) {
		try {
			console.log("CreateLiveStreaming Data",data);
			
			//Start: code for offline			
			if(data.status && data.status=='offline' && data.u_id){	
				client.join(data.u_id);	//join in socket room				
				console.time("CreateLiveStreamingOffline");
				let userData = await model.User.find({
					where:{id:data.user_id},
					attributes: ['id', 'username','avatar'],
				})
				let notiData = await model.live_notification.findAll({where:{u_id:data.u_id}});
				let notificationData = await model.notifications.findAll({where:{channel_id:data.u_id}});
				for(let i=0;i<notiData.length;i++){
					notiData[i].set({status:"offline",pkmode:"false"});
					notiData[i].save();
				}
				for(let i=0;i<notificationData.length;i++){
					notificationData[i].set({flag_status:"offline"});
					notificationData[i].save();
				}
				let socketUserRes = {
					"status": "success",
					"message": `User ${data.status}`,
					data:[{
						id : data.user_id,
						avatar : userData.avatar,
						u_id : data.u_id,
						username : userData.username,
						status : data.status,
						pkmode: "false"
					}]					
				}
				io.emit('GetHomeSocketUser', socketUserRes);	//broadcast message to room user only
				
				console.timeEnd("CreateLiveStreamingOffline");
				return callback({
					"status": "success", 
					"message": "Live Broadacasting offline successfully", 
				 });
			}
			//End: code for offline

			if(data.u_id && data.filters && data.user_id){
				console.time("CreateLiveStreaming");
				let userData = await model.User.find({
					where:{id:data.user_id},
					attributes: ['id', 'username','avatar'],
				})
				if(!userData){
					return callback({ "status": "fail", "message": "User not found", 'data': {} });
				}
				//Start: Reset all previous one event for this user
				/* model.live_notification.update({
					status:"offline",
					chat_flag:"false",
					control_buttons:"false",
					filters:"no",
					pkmode:"false"
				},{where:{user_id:data.user_id}}); */
				//End: Reset all previous one event for this user

				let d = await model.live_notification.create({
					user_id : data.user_id,
					u_id : data.u_id,
					filters : data.filters,
					live_no:"1",
					status : "pending",
					title:data.title,
					pkmode:data.pkmode
				});
				let notiData = d.dataValues;
				//Start: Get all follower of data.user_id
				let followerData = await model.follow_users.findAll({where:{user_id:data.user_id,deleted_at:null}});
				console.log("folowerdata",followerData.length);
				let followers=[];
				let notifications=[];
				let followerIds=[];
				if(followerData.length){
					for(let i=0;i<followerData.length;i++){
						followerIds.push(followerData[i].followed_user_id);
							followers.push({
								follow_user : followerData[i].followed_user_id,
								user_id : data.user_id,
								u_id : data.u_id,
								filters : data.filters,
								live_no:"1",
								status : "pending"
							});
						notifications.push({
							follow_user : followerData[i].followed_user_id,
							user_id : data.user_id,
							channel_id : data.u_id,
							title : "Live Streaming",
							description : userData.username+" Started Live Broadacasting..!",
							status:"unread",
							flag_status:"live",
							follow_status:null
						});
					}
				}
				console.log("push followers length",followers.length);
				if(followers.length){
					await model.live_notification.bulkCreate(followers);
					await model.notifications.bulkCreate(notifications);
					followerData = await model.User.findAll({
						attributes: ['device_token', 'device_type','id','notification'],
						where:{id:{[Op.in]:followerIds}}
					});
					let pushData = {
						type  : "Live Broadcasting Started",
						u_id  : data.u_id,
						body  : userData.username+" has been started broadcasting",
						notification_type:"live_broadcasting",
						title:"Live Broad Casting",
						sender_id:data.user_id
					}
					console.log("push followerData length",followerData.length);

					
					for(let i=0;i<followerData.length;i++){
						if (followerData[i].device_type == "android" && followerData[i].device_token && followerData[i].device_token != "" && followerData[i].notification == 1) {
							console.log("android");
							let myMessage = userData.username+" Live Broadcasting Started";
							androidPushNotification({ "status": "success", 'data': pushData }, [followerData[i].device_token], myMessage);
						} else if (followerData[i].device_type == "ios" && followerData[i].device_token && followerData[i].device_token != "" && followerData[i].notification == 1) {
							console.log("ios");
							let myMessage = userData.username+" Live Broadcasting Started";
							iosPushNotification({ "status": "success", 'data': pushData }, [followerData[i].device_token], myMessage);
						}
					}
				}
				let socketUserRes = {
					"status": "success",
					"message": `User ${data.status}`,
					data:[{
						id : data.user_id,
						avatar : userData.avatar,
						u_id : data.u_id,
						username : userData.username,
						status : "online",
						pkmode:(data.pkmode)?data.pkmode:""
					}]					
				}
				io.emit('GetHomeSocketUser', socketUserRes);	//broadcast message to room user only
				console.timeEnd("CreateLiveStreaming");				
					return callback({
						"status": "success", 
						"message": "Live Broadacasting Started successfully", 
					});
				//End: Get all follower of data.user_id

				
			}else{
				return callback({ "status": "fail", "message": "Invalid parameters", 'data': {} });
			}
		} catch (error) {
			console.log("catch error",error)
			return callback({ "status": "fail", "message": "Something went wrong.", 'data': {} });
		}
	};

	module.LiveStreamingStatus = async function (data, callback) {
		try {
			console.log("LiveStreamingStatus Data",data);
			if(data.u_id && data.status && data.user_id && data.host_id){
				client.join(data.u_id);	//join in socket room
				console.time("LiveStreamingStatus");
				let updateObj = {
					status : data.status
				}
				// let userInfo = await model.User.find({where:{id:data.user_id}});
				// let userData = await model.live_notification.find({where:{u_id:data.u_id,follow_user:data.user_id}});
				// let userComment = await model.livecomment.find({where:{u_id:data.u_id,follow_user:data.user_id,status:{[Op.or]:["requested","join"]}}});
				let [userInfo,hostInfo,userData, userComment] = await Promise.all([
					model.User.find({where:{id:data.user_id}}),
					model.live_notification.find({where:{user_id:data.host_id,follow_user:null,u_id:data.u_id}}),
					model.live_notification.find({where:{u_id:data.u_id,follow_user:data.user_id}}),
					model.livecomment.find({where:{u_id:data.u_id,follow_user:data.user_id,status:{[Op.or]:["requested","join"]}}})
				]);
				if(!userComment){
					userComment = await model.livecomment.create({
						user_id : data.host_id,
						follow_user : data.user_id,
						u_id : data.u_id,
						status : "join",
						comment: userInfo.username+" has joined"
					});
				}
				if(userData){
					updateObj.viewer = (data.status=="online")?"1":userData.viewer;	
					if(data.status == "offline"){
						updateObj.dual_status = null;
					}			
					await userData.set(updateObj);
					await userData.save();
				}else{
					if(data.status=="online"){
						userData = await model.live_notification.create({						
							follow_user : data.user_id,
							user_id : data.host_id,
							u_id : data.u_id,
							filters : hostInfo.filters,
							live_no:"1",
							viewer:"1",
							status : "online"
						});
					}
				}
				let uComment = userComment.dataValues;
				uComment.avatar = userInfo.avatar;
				uComment.username = userInfo.username;

				let usercount = await model.live_notification.count({where:{u_id:data.u_id,status:"online"}});
				let response = {
					"status": "success",
					"message": "User in Live and Comments Broadacasting..!",
					"usercount": usercount,
					"requested" : [uComment],
					title: hostInfo.title,
					pkmode:hostInfo.pkmode
				}
				
				console.log("GetStreamingStatus response",response);
				io.sockets.in(data.u_id).emit('GetStreamingStatus', response);	//broadcast message to room user only
				
				//io.emit("GetStreamingStatus",response);
				console.timeEnd("LiveStreamingStatus");
				let msg = (data.status=="online")?"joined":"left";	
				
				//Start: code for slider value when pkmode ON
				let pkNotiData = await model.live_notification.find({where:{u_id:data.u_id,pkmode:'true'}});
				let hostDiamondCount = 50;
				let coHostDiamondCount = 50;
				let hostDiamondPer = 50;
				let coHostDiamondPer = 50;
				if(pkNotiData){
					let hostD =  await model.gift_diamonds.sum("gift_diamond",{where:{unique_id:pkNotiData.u_id,receive_id:pkNotiData.user_id}});
					let coHostD = await model.gift_diamonds.sum("gift_diamond",{where:{unique_id:pkNotiData.u_id,receive_id:pkNotiData.follow_user}});
					console.log("hostD",hostD);
					console.log("coHostD",coHostD);
					hostDiamondCount += hostD;
					coHostDiamondCount += coHostD;
					console.log("hostDiamondCount",hostDiamondCount);
					console.log("coHostDiamondCount",coHostDiamondCount);
					hostDiamondPer = ((hostDiamondCount*100)/(hostDiamondCount+coHostDiamondCount)).toFixed(2);
					coHostDiamondPer = ((coHostDiamondCount*100)/(coHostDiamondCount+hostDiamondCount)).toFixed(2);					
					console.log("hostDiamondCount Per",hostDiamondPer);
					console.log("coHostDiamondCount Per",coHostDiamondPer);
				}
				//End: code for slider value when pkmode ON

				//Start: check for PKMODE TRUE or FALSE
				let pkData = await model.live_notification.find({where:{u_id:data.u_id,pkmode:"true"}});
				
				//End: check for PKMODE TRUE or FALSE
				return callback({
					pkmode : (pkData)?"true":"false",
					coHostId : (pkData)?pkData.follow_user:"",
					hostDiamondPer:hostDiamondPer,
					coHostDiamondPer:coHostDiamondPer,
					"Filter_Flag":userData.filters,
					"status": "success", 
					"message": `Live Broadacasting ${msg} successfully`, 
				 });
			}else{
				return callback({ "status": "fail", "message": "Invalid parameters", 'data': {} });
			}
		} catch (error) {
			console.log("catch error",error)
			return callback({ "status": "fail", "message": "Something went wrong.", 'data': {} });
		}
	};	

	module.ChatHidenAndShow = async function (data, callback) {
		try {
			console.log("ChatHidenAndShow Data",data);
			if(data.u_id && data.control_buttons && data.chat_flag && data.user_id){
				client.join(data.u_id);	//join in socket room
				console.time("ChatHidenAndShow");
				let updateObj = {
					status : data.status
				}
				let notiData = await model.live_notification.findAll({where:{u_id:data.u_id,user_id:data.user_id}});
				for(let i=0;i<notiData.length;i++){
					notiData[i].set({
						control_buttons:data.control_buttons,
						chat_flag:data.chat_flag
					});
					notiData[i].save();
				}
				let response = {
					status : "success",
					chat_flag: data.chat_flag,
					control_buttons: data.control_buttons,
					message:"Updated successfully"
				}
				io.sockets.in(data.u_id).emit('GetChatHidenAndShow', response);	//broadcast message to room user only
				//io.emit("GetChatHidenAndShow",response);
				console.timeEnd("ChatHidenAndShow");
				return callback({
					"status": "success", 
					"message": "Updated successfully", 
				 });
			}else{
				return callback({ "status": "fail", "message": "Invalid parameters", 'data': {} });
			}
		} catch (error) {
			console.log("catch error",error)
			return callback({ "status": "fail", "message": "Something went wrong.", 'data': {} });
		}
	};

	module.DualCreateLiveStreaming = async function (data, callback) {
		try {
			console.log("DualCreateLiveStreaming Data",data);
			if(data.u_id && data.follow_user && data.host_id){
				client.join(data.u_id);	//join in socket room
				console.time("DualCreateLiveStreaming");
				let updateObj = {
					dual_status : "requested"
				}
				let userComment = await model.livecomment.find({where:{u_id:data.u_id,follow_user:data.user_id,status:{[Op.or]:["requested","join"]}}});
				if(!userComment){
					userComment = await model.livecomment.create({
						user_id : data.host_id,
						follow_user : data.follow_user,
						u_id : data.u_id,
						status : "requested"
					});
				}

				let userData = await model.User.find({
					attributes: ['username', 'avatar','id'],
					where:{id:data.follow_user}
				});
				let uComment = userComment.dataValues;
				uComment.avatar = userData.avatar;
				uComment.username = userData.username;
				uComment.dual_status = "requested";
				let notiData = await model.live_notification.find({where:{u_id:data.u_id,follow_user:data.follow_user}});
				notiData.set(updateObj);
				await notiData.save();
				let requestedcount = await model.live_notification.count({where:{u_id:data.u_id,dual_status:"requested"}});
				console.log("uComment ",uComment);
				let response = {
					requested : [uComment],
					status : "success",
					requestedcount: requestedcount,
					message:"Dual Live Broadacasting Request Been Sent"
				}
				io.sockets.in(data.u_id).emit('GetDualCreateLiveStreaming', response);	//broadcast message to room user only
				//io.emit("GetDualCreateLiveStreaming",response);
				console.timeEnd("DualCreateLiveStreaming");
				return callback({
					data:notiData,
					"status": "success", 
					"message": "Dual Live Broadacasting Request Been Sent", 
				 });
			}else{
				return callback({ "status": "fail", "message": "Invalid parameters", 'data': {} });
			}
		} catch (error) {
			console.log("catch error",error)
			return callback({ "status": "fail", "message": "Something went wrong.", 'data': {} });
		}
	};


	module.DualLiveStreamingStatus = async function (data, callback) {
		try {
			console.log("DualLiveStreamingStatus Data",data);
			if(data.u_id && data.follow_user && data.dual_status){
				client.join(data.u_id);	//join in socket room
				console.time("DualLiveStreamingStatus");
				let updateObj = {
					dual_status : data.dual_status
				}				
				let notiData = await model.live_notification.find({where:{u_id:data.u_id,follow_user:data.follow_user}});
				notiData.set(updateObj);
				await notiData.save();
				console.timeEnd("DualLiveStreamingStatus");
				//Start: Get cohost user id
				let pkData = await model.live_notification.find({where:{u_id:data.u_id,pkmode:"true"}});
				//End: Get cohost user id

				let response = {
					coHostId : (pkData)?pkData.follow_user:"",
					"user_id":data.follow_user,
					"status": "success", 
					"message": "Updated successfully", 
					"dual_flag" : data.dual_status,
					pkmode: (pkData && pkData.pkmode)?pkData.pkmode:""
				}
				io.emit("GetDualLiveStreamingStatus",response);
				io.sockets.in(data.u_id).emit('GetDualLiveStreamingStatus', response);	//broadcast message to room user only
				let coHostData = await model.User.find({
					where:{id:data.follow_user},
					attributes : ["id","diamond","username","avatar"]
				});
				return callback({
					"data" : coHostData,
					"status": "success", 
					"message": "Updated successfully", 
				 });
			}else{
				return callback({ "status": "fail", "message": "Invalid parameters", 'data': {} });
			}
		} catch (error) {
			console.log("catch error",error)
			return callback({ "status": "fail", "message": "Something went wrong.", 'data': {} });
		}
	};


	module.AddGiftDiamonds = async function (data, callback) {
		try {
			console.log("AddGiftDiamonds Data",data);
			if(data.u_id && data.sender_id && data.receive_id){
				client.join(data.u_id);	//join in socket room
				console.time("AddGiftDiamonds");
				let giftData;
				let sliderVal = 0;
				if(data.gift_id){
					giftData = await model.Gift.find({
						where:{id:data.gift_id},
						attributes : ["id","price","avatar"]
					});
					if(giftData){
						data.gift_diamond = giftData.price;
					}
				}
				let senderData = await model.User.find({
					where:{id:data.sender_id},
					attributes : ["id","diamond","username","avatar"]
				});
				
				if(parseInt(senderData.diamond)<parseInt(data.gift_diamond)){
					console.timeEnd("AddGiftDiamonds");
					return callback({ "status": "fail", "message": "Insufficient diamond", 'data': {} });	
				}
				await model.User.increment('diamond',{by:data.gift_diamond,where:{id:data.receive_id}});
				await model.User.decrement('diamond',{by:data.gift_diamond,where:{id:data.sender_id}});
				
				let pkNotiData = await model.live_notification.find({where:{u_id:data.u_id,pkmode:'true'}});
				let giftDetail = {
					sender_id : data.sender_id,
					receive_id : data.receive_id,
					unique_id : data.u_id,
					gift_diamond:data.gift_diamond,
					gift_id : (data.gift_id)?data.gift_id:null
				};
				if(pkNotiData){
					giftDetail.pkmode = true;
				}
				let gdData = await model.gift_diamonds.create(giftDetail);
				let gdDataNew = gdData.dataValues;
				gdDataNew.avatar = "";
				if(giftData){
					gdDataNew.avatar = giftData.avatar;
				}
				
				//Start: code for slider value when pkmode ON
				
				let hostDiamondCount = 50;
				let coHostDiamondCount = 50;
				let hostDiamondPer = 50;
				let coHostDiamondPer = 50;
				if(pkNotiData){
					let hostD =  await model.gift_diamonds.sum("gift_diamond",{where:{pkmode:true,unique_id:pkNotiData.u_id,receive_id:pkNotiData.user_id}});
					let coHostD = await model.gift_diamonds.sum("gift_diamond",{where:{pkmode:true,unique_id:pkNotiData.u_id,receive_id:pkNotiData.follow_user}});
					console.log("hostD",hostD);
					console.log("coHostD",coHostD);
					hostDiamondCount += hostD;
					coHostDiamondCount += coHostD;
					console.log("hostDiamondCount",hostDiamondCount);
					console.log("coHostDiamondCount",coHostDiamondCount);
					hostDiamondPer = ((hostDiamondCount*100)/(hostDiamondCount+coHostDiamondCount)).toFixed(2);
					coHostDiamondPer = ((coHostDiamondCount*100)/(coHostDiamondCount+hostDiamondCount)).toFixed(2);					
					console.log("hostDiamondCount Per",hostDiamondPer);
					console.log("coHostDiamondCount Per",coHostDiamondPer);
				}
				//End: code for slider value when pkmode ON

				let response = {
					gift_diamond : data.gift_diamond,
					gifts : [gdDataNew],
					hostDiamondPer:hostDiamondPer,
					coHostDiamondPer:coHostDiamondPer,
					status:"success",
					message:`Sent ${data.gift_diamond} diamonds`
				}
				console.log("GetAddGiftDiamonds response",response);
				//io.emit("GetAddGiftDiamonds",response);
				
				io.sockets.in(data.u_id).emit('GetAddGiftDiamonds', response);	//broadcast message to room user only

				console.timeEnd("AddGiftDiamonds");
				return callback({
					"status": "success",
					"message": `You have successfully sent ${data.gift_diamond} diamonds` 
				 });
			}else{
				return callback({ "status": "fail", "message": "Invalid parameters", 'data': {} });
			}
		} catch (error) {
			console.log("catch error",error)
			return callback({ "status": "fail", "message": "Something went wrong.", 'data': {} });
		}
	};

	module.GiftDiamondsList = async function (data, callback) {
		try {
			console.log("GiftDiamondsList Data",data);
			if(data.u_id && data.receive_id){
				client.join(data.u_id);	//join in socket room
				console.time("GiftDiamondsList");
				let giftData = await sequelize1.query("SELECT u.id,u.username,u.avatar,SUM(g.gift_diamond) as gift_diamond,g.unique_id FROM gift_diamonds g JOIN users u ON g.sender_id=u.id WHERE unique_id=? AND receive_id=? GROUP BY g.sender_id",{
					replacements: [data.u_id,data.receive_id],
					type:"SELECT"
				});
				console.log("giftData",giftData);
				
				//End: Get unique user
				console.timeEnd("GiftDiamondsList");
				return callback({
					data:giftData,
					"status": "success",
					"message": `List found successfully` 
				 });
			}else{
				return callback({ "status": "fail", "message": "Invalid parameters", 'data': {} });
			}
		} catch (error) {
			console.log("catch error",error)
			return callback({ "status": "fail", "message": "Something went wrong.", 'data': {} });
		}
	};

	module.homelistsocket = async function (data, callback) {
		try {
			console.log("homelistsocket Data",data);
			
			client.join(data.u_id);	//join in socket room
			console.time("homelistsocket");
			let userData = await sequelize1.query("SELECT u.id,u.username,u.avatar,ln.u_id,ln.status FROM live_notification ln JOIN users u ON ln.user_id=u.id WHERE ln.status=? OR ln.status=? GROUP BY ln.user_id,ln.u_id",{
				replacements: ["pending","online"],
				type:"SELECT"
			});
			console.log("userData",userData);
			
			//End: Get unique user
			console.timeEnd("homelistsocket");
			return callback({
				data:userData,
				"status": "success",
				"message": `List found successfully` 
				});
			
		} catch (error) {
			console.log("catch error",error)
			return callback({ "status": "fail", "message": "Something went wrong.", 'data': {} });
		}
	};

	module.setpkmode = async function (data, callback) {
		try {
			console.log("setpkmode Data",data);
			if(data.u_id && data.follow_user && data.user_id && data.pkmode){
				console.time("setpkmode");
				let notificationData = await sequelize1.query("SELECT * FROM live_notification ln  WHERE ln.status=? AND ln.dual_status=? AND u_id=? AND user_id=? AND follow_user=?",{
					replacements: ["online","accepted",data.u_id,data.user_id,data.follow_user],
					type:"SELECT"
				});				
				if(notificationData.length==1){
					
					//Start: server side timer
					let minute = parseFloat(data.timer);
					let seconds = minute*60;

					var t = setInterval(async function() {
						let liveNotiData = await model.live_notification.find({where:{u_id:data.u_id,status:"offline",pkmode:"true"}});
						if(liveNotiData){
							clearInterval(t);	//reset timer once host kill app
						}

					 let mins = parseInt(seconds/60,10);
					 let secs = parseInt(seconds%60,10);
					 mins = (mins<10)?"0"+mins:mins;
					 secs = (secs<10)?"0"+secs:secs;
					 console.log(mins+":"+secs);
					 io.emit('pkmodetimer', {
						u_id:data.u_id,
						timer : (mins+":"+secs)
					});
					 seconds--;
					 if(!seconds){
					    clearInterval(t);	//reset timer
					    let winnerData=[];
					    let pkmodeData = await sequelize1.query("SELECT id,user_id,follow_user,u_id,status,pkmode FROM `live_notification` WHERE `u_id` LIKE ? AND status='online' AND pkmode=true",{
							replacements: [data.u_id],
							type:"SELECT"
						});
						if(pkmodeData.length){
							let hostData = await sequelize1.query("SELECT users.id as user_id,users.username,SUM(gift_diamond) as tot_diamond FROM `users` LEFT JOIN gift_diamonds gd ON gd.receive_id=users.id  WHERE users.id=?  AND gd.unique_id=?",{
								replacements: [pkmodeData[0].user_id,data.u_id],
								type:"SELECT"
							});
							let coHostData = await sequelize1.query("SELECT users.id as user_id,users.username,SUM(gift_diamond) as tot_diamond FROM `users` LEFT JOIN gift_diamonds gd ON gd.receive_id=users.id  WHERE users.id=?  AND gd.unique_id=?",{
								replacements: [pkmodeData[0].follow_user,data.u_id],
								type:"SELECT"
							});
							if(hostData[0].tot_diamond && coHostData[0].tot_diamond){
								if(hostData[0].tot_diamond>coHostData[0].tot_diamond){
									hostData[0].status = "Win";
									coHostData[0].status = "Lose";									
								}else if(coHostData[0].tot_diamond>hostData[0].tot_diamond){
									hostData[0].status = "Lose";
									coHostData[0].status = "Win";
								}else{
									hostData[0].status = "Draw";
									coHostData[0].status = "Draw";
								}
							}else{
								if(hostData[0].tot_diamond){
									hostData[0].status = "Win";
									coHostData[0].status = "Lose";
								}else if(coHostData[0].tot_diamond){
									hostData[0].status = "Lose";
									coHostData[0].status = "Win";
								}else{
									hostData[0].status = "Draw";
									coHostData[0].status = "Draw";
								}
							}
							winnerData.push(hostData[0]);
							winnerData.push(coHostData[0]);
							io.emit('winnerData', {
								u_id:data.u_id,
								winnerData: winnerData
							});
						}
					 }
					},1000);
					//End: server side timer


					await model.live_notification.update({ pkmode:data.pkmode, timer:data.timer}, { where: { u_id:data.u_id,follow_user:data.follow_user,user_id:data.user_id} });
					io.emit('getpkmode', {
						user_id:data.user_id,
						pkmode:data.pkmode,
						timer:data.timer,
						u_id:data.u_id,
						status:"success",
						message: `PK mode updated successfully` 
					});	//broadcast message to room user only
					return callback({
						"status": "success",
						"message": `PK mode updated successfully` 
					});	
				}else{
					return callback({
						"status": "fail",
						"message": `Fail to update pk mode.` 
					});	
				}
				console.timeEnd("setpkmode");
				return callback({
					data:userData,
					"status": "success",
					"message": `List found successfully` 
					});
			}else{
				return callback({
					status : "fail",
					message:"Invalid parameters"
				})
			}
		} catch (error) {
			console.log("catch error",error)
			return callback({ "status": "fail", "message": "Something went wrong.", 'data': {} });
		}
	};

	module.stickerList = async function (data, callback) {
		try {
			console.log("stickerList Data",data);
			if(data.userId){
				let stickerData = await model.Sticker.find({where:{status:"active"}});
				return callback({
					data:stickerData,
					"status": "success",
					"message": `Stickers found successfully` 
				 });
			}else{
				return callback({ "status": "fail", "message": "Invalid parameters", 'data': {} });
			}
		} catch (error) {
			console.log("catch error",error)
			return callback({ "status": "fail", "message": "Something went wrong.", 'data': {} });
		}
	};

	return module;
};


function onlyUnique(value, index, self) {
	return self.indexOf(value) === index;
}

function androidPushNotification(data, deviceTokens, textMessage) {
	let gcm = require('node-gcm');
	//let sender = new gcm.Sender('AIzaSyA7CFPx4iksIS6i3KQmIoPyJ5kTJuEtd1I');   
	// let sender = new gcm.Sender('AAAAShvo7YE:APA91bGPG6qG_pihCsMgPhewjajKgSJBh-0rIFyosCHbE5b1VSETDSJmXRydu1RLEJW-gePyPh0XyRXpIKldmkdAp_iyekY4RTGnrR_HK2LJqFvqytpi4MpGTFiZGj1wN-IZKgMm_Imc'); // changing on 5 Jan 2021
	let sender = new gcm.Sender('AAAAU9VW3iA:APA91bEAs1WIOkxiDDWTH9fqP40os8Z5UmROCx7Z6v4sBa-btKUic9_cFGMCBZGTRLLdJg2QIs37vFHXTXZDaiBgng7_UzREJmXdg2YeMzu20dHGsklkTakWUNAsszJEdheMo-p6VHyu');
	let message = new gcm.Message({
		data: {
			data: data,
			'title': 'com.ais.wongalive',
			'body': textMessage,
			'icon': 'wonga_bigo_icon'
		},
	});

	message.addNotification('title', 'com.ais.wongalive');
	message.addNotification('body', textMessage);
	message.addNotification('icon', 'wonga_bigo_icon');

	// delete message.params.notification;
	let regTokens = deviceTokens;
	console.log("regTokens", regTokens);
	console.log("message", message);

	sender.send(message, { registrationTokens: regTokens }, function (err, response) {
		if (err) {
			console.log(err);
		} else {
			console.log("Pushnotification response", response);

		}
		return true;
	});
}

function iosPushNotification(data, deviceTokens, textMessage) {
	const apn = require('apn');
	const AppConstantKeyId = "F643PRBDCP";
	const AppConstantTeamId = "T4YKTL658N";
	let options = {
		token: {
			key: "./config/AuthKey_F643PRBDCP.p8",
			keyId: AppConstantKeyId,
			teamId: AppConstantTeamId
		},
		production: false
	};

	let apnProvider = new apn.Provider(options);

	let deviceToken = deviceTokens;

	let notification = new apn.Notification();
	notification.expiry = Math.floor(Date.now() / 1000) + 24 * 3600;
	notification.badge = 0;
	notification.alert = textMessage;
	notification.payload = data;

	notification.topic = "com.ais.wongalive";
	apnProvider.send(notification, deviceToken).then((result, err) => {
		console.log("Response is :", result);
		console.log("Iphone Failed Response is : ", result.failed);
	});
	apnProvider.shutdown()
}