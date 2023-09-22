const Op = Sequelize.Op;
var dateformat = require('dateformat');
var striptags = require('striptags');
var moment = require("moment");
var tz = require('moment-timezone');
var timeZone = 'Asia/Kolkata';
moment.tz.setDefault(timeZone);

module.exports = function (model, io, client) {
	var module = {};

	// ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: A function to get Unique stirngs in Array
	Array.prototype.unique = function() {
		return this.filter(function (value, index, self) {
			return self.indexOf(value) === index;
		});
	};

	module.updateSocket = async function (data, callback) {

		console.log("updateSocket data",data);
		await model.User.update({ socket_id: client.id, available_flag: 'online',socket_time: null}, { where: { id: data.user_id } });

		client.myData = {
			userId : data.user_id
		}

		callback({ status: "success", message: "Socket updated successfully" });
	};

	module.getAllUserList = async function (data, callback) {
		try {
			
			console.log("chat getAllUserList data log",data);
			let input = data;
			var dataArr = [];
			if (input.userId) {

				//let query = await sequelize1.query("SELECT t1. sender_id,t1. receiver_id,t1. message,t1. updated_at,t1. isRead,t1. created_at,t1. isDelivered,t3. username,t3. id, t3. avatar,t3. status,t3. device_token,t3. device_type,t3. sign_up_as,t3. available_flag,t3. socket_id,t3. notification,t3. isReport,t3. block_status FROM `chat_messages` AS t1 JOIN `users` AS t3 ON t3.`id` = t1.`receiver_id` INNER JOIN (SELECT LEAST(sender_id, receiver_id) AS sender_id, GREATEST(sender_id, receiver_id) AS receiver_id, MAX(id) AS max_id FROM chat_messages GROUP BY LEAST(sender_id, receiver_id), GREATEST(sender_id, receiver_id)) AS t2 ON LEAST(t1.sender_id, t1.receiver_id) = t2.sender_id AND GREATEST(t1.sender_id, t1.receiver_id) = t2.receiver_id AND t1.id = t2.max_id WHERE t1.sender_id = ? OR t1.receiver_id = ? GROUP BY t1.id ORDER BY t1.id DESC", { replacements: [input.userId, input.userId], type: sequelize1.QueryTypes.SELECT })
				let query = await sequelize1.query("SELECT * FROM chat_messages WHERE id in (SELECT MAX(cm1.id) FROM `chat_messages` cm1 WHERE cm1.sender_id=? OR cm1.receiver_id=? GROUP BY cm1.chat_id)", { replacements: [input.userId, input.userId], type: sequelize1.QueryTypes.SELECT })
				if (query.length) {
					for (let q = 0; q < query.length; q++) {
						let uuSerId = (query[q].sender_id==input.userId)?query[q].receiver_id:query[q].sender_id;
						let uuData = await model.User.findOne({ where: { id: uuSerId},attributes: ['username','status','avatar','available_flag'],raw:true });
						query[q].username = uuData.username;
						query[q].avatar = uuData.avatar;
						query[q].available_flag = uuData.available_flag;
						query[q].status = uuData.status;

						let unReadMessage = await model.ChatMessages.count({ where: { "sender_id": query[q].sender_id, "receiver_id": query[q].receiver_id, "isRead": "0" } });
						
						let unReadMessage2 = await model.ChatMessages.count({ where: { "sender_id": query[q].receiver_id, "receiver_id": query[q].sender_id, "isRead": "0" } });


						let dt = new Date(query[q].updated_at);
						let s = new Date(dt).toLocaleString(undefined, { timeZone: 'Asia/Kolkata' });

						query[q].updated_at = s;
						let newDt = s;
						query[q].updated_at = newDt;
						query[q].unreadMessageCount = 0;
						query[q].unreadMessageCount2 = unReadMessage2;

						if (query[q].receiver_id == input.userId) {

							let users = await model.User.findOne({ where: { id: query[q].sender_id }, "user_type": "user" });
							if (users) {
								query[q].username = users.username;
								query[q].avatar = users.avatar;
								query[q].block_status = users.block_status;
							}
							query[q].receiver_id = query[q].sender_id;
							
							query[q].unreadMessageCount = unReadMessage;
							query[q].unreadMessageCount2 = unReadMessage2;

						}

					}

					// for blocked User ------------------------------------------------------------- Start
					let blockeduser = await model.BlockUserList.findAll({ where: {
						[Op.and]: [
							{ [Op.or]: [ {"user_id":input.userId},{"blocked_user_id":input.userId} ] },
							{ "deleted_at":null}
						] 
					},raw:true });
		
					let blockedIds = [];
					for(let i = 0; i < blockeduser.length; i++){
						blockedIds.push(blockeduser[i].user_id.toString(),blockeduser[i].blocked_user_id.toString());
					}
					blockedIds = blockedIds.unique();
					let removeId = blockedIds.indexOf(input.userId);
					if (removeId > -1) {
						blockedIds.splice(removeId, 1);
					}
		
					let blockedUsername = [];
					if(blockedIds.length > 0){
						for(let i = 0; i < blockedIds.length; i++){
							let name = await model.User.findOne({ where: { id: blockedIds[i], user_type: "user" },attributes: ['username'],raw:true });
							blockedUsername.push(name.username);
						}
		
						for(let i = 0; i < query.length; i++){
							for(let j = 0; j < blockedUsername.length; j++){
								if(blockedUsername[j] != query[i].username){
									dataArr.push(query[i]);
								}
							}
						}
					} else {
						for(let i = 0; i < query.length; i++){
							dataArr.push(query[i]);
						}
					}
					// for blocked User ------------------------------------------------------------- End

				}

				

				// for (let u = 0; u < query.length; u++) {

				// 	let blockUserListData = await model.BlockUserList.findOne({ where: { "user_id": query[u].sender_id, "blocked_user_id": query[u].receiver_id, "deleted_at": null }, raw:true });
					
				// 	if (!blockUserListData) {
				// 		dataArr.push(query[u]);
				// 	}

				// }
				console.log("getAllUserList response ",dataArr)
				callback({ "status": "success", "message": "Users found successfully", "data": dataArr });

			} else {
				callback({ "status": "fail", "message": "Invalid parameters", 'data': {} });
			}
		} catch (error) {
			console.log(error)
			callback({ "status": "fail", "message": "Somwthing went wrong.", 'data': {} });
		}
	};

	module.singleChatSend = async function (data, callback) {
		try {
			console.log("singleChatSend data",data);
			let input = data;
			let senderId = input.senderId;
			let receiverId = input.receiverId;
			let message = input.message;

			if (senderId && receiverId && message) {
				let receiver_user = await model.User.findOne({ where: { id: receiverId, notification: '1' } });
				let sender_user = await model.User.findOne({ where: { id: senderId } });
				if (sender_user && receiver_user) {
					let chatData = await model.Chat.findOne({ where: { sender_id: senderId, receiver_id: receiverId } });
					if (!chatData) {
						chatData = await model.Chat.findOne({ where: { sender_id: receiverId, receiver_id: senderId } });
					}
					var chat_data = chatData;

					var chatMessageDeail = '';
					if (!chat_data) {
						let chatId = randomCapitalNumeric(13);
						var inputChatMaster = {
							chat_id: chatId,
							sender_id: senderId,
							receiver_id: receiverId
						}
						var chatDatas = await model.Chat.create(inputChatMaster)
						var inputChatMessage = {
							chat_id: chatId,
							sender_id: senderId,
							receiver_id: receiverId,
							message: message,
							isRead: "0",
							isDelivered: "0",
						}
						chatMessageDeail = await model.ChatMessages.create(inputChatMessage)
					}

					if (chat_data) {
						if (chat_data.chat_id != '') {
							var inputChatMessage = {
								chat_id: chat_data.chat_id,
								sender_id: senderId,
								receiver_id: receiverId,
								message: message,
								isRead: "0",
								isDelivered: "0",
							}
							chatMessageDeail = await model.ChatMessages.create(inputChatMessage)
						}
					}

					let chatDetail = await model.ChatMessages.findOne({ where: { chat_id: chatMessageDeail.chat_id }, order: [["id", "DESC"]] });
					let receiverUserDetail = await model.User.findOne({ where: { id: chatDetail.receiver_id } });
					let senderUserDetail = await model.User.findOne({ where: { id: chatDetail.sender_id } });
					// chatDetail.dataValues.receiver_name = receiverUserDetail.name
					chatDetail.dataValues.receiver_name = receiverUserDetail.first_name ? receiverUserDetail.first_name : receiverUserDetail.username
					chatDetail.dataValues.receiver_avatar = receiverUserDetail.avatar
					// chatDetail.dataValues.sender_name = senderUserDetail.name
					chatDetail.dataValues.sender_name = senderUserDetail.first_name ? senderUserDetail.first_name : senderUserDetail.username
					chatDetail.dataValues.sender_avatar = senderUserDetail.avatar
					chatDetail.dataValues.sender_username = senderUserDetail.username
					// chatDetail.dataValues.created_at = dateformat(chatDetail.dataValues.created_at, "dd-mmm-yyyy HH:MM TT", true);
					chatDetail.dataValues.created_at = moment(chatDetail.dataValues.created_at).format('DD-MM-YYYY, h:mm:ss A')
					chatDetail.dataValues.available_flag = senderUserDetail.available_flag

					if (receiverUserDetail.socket_id) {

						var check = client.to([receiverUserDetail.socket_id]).emit("singleChatReceive", { "status": "success", 'result': chatDetail });
					}

					// console.log("receiverUserDetail---", receiverUserDetail);

					if (receiverUserDetail.device_type == "android" && receiverUserDetail.device_token && receiverUserDetail.device_token != "" && receiverUserDetail.notification == 1) {
						console.log("android");
						// androidPushNotification({ "status": "success", 'data': chatDetail }, [receiverUserDetail.device_token], message);

						let myMessage = ''+senderUserDetail.username+ ' :'+message+'';
						androidPushNotification({ "status": "success", 'data': chatDetail }, [receiverUserDetail.device_token], myMessage);
					} else if (receiverUserDetail.device_type == "ios" && receiverUserDetail.device_token && receiverUserDetail.device_token != "" && receiverUserDetail.notification == 1) {
						console.log("ios");
						// iosPushNotification({ "status": "success", 'data': chatDetail }, [receiverUserDetail.device_token], message);

						let myMessage = ''+senderUserDetail.username+ ' :'+message+'';
						iosPushNotification({ "status": "success", 'data': chatDetail }, [receiverUserDetail.device_token], myMessage);
					}

					callback({ 'status': 'success', 'message': 'Message successfully sent', 'data': chatDetail });
				} else {
					callback({ 'status': 'fail', 'message': 'Invalid SenderId and ReceiverId', 'data': {} });
				}
			} else {
				callback({ 'status': 'fail', 'message': 'Invalid Parameters', 'data': {} });
			}
		} catch (error) {
			console.log(error)
			callback({ 'status': 'fail', 'message': 'Chat not available', 'data': {} });
		}
	};

	module.singleChatGet = async function (data, callback) {
		try {

			console.log("single chat call yes", data);
			let input = data;
			let senderId = input.sender_id;
			let receiverId = input.receiver_id;
			let message_id = input.message_id;

			if (senderId && receiverId) {
				let chatData = await model.Chat.findOne({ where: { sender_id: senderId, receiver_id: receiverId } });

				if (!chatData) {
					chatData = await model.Chat.findOne({ where: { sender_id: receiverId, receiver_id: senderId } });
				}
				var chat_data = chatData;

				if (chat_data) {
					var chat_ids = chat_data.chat_id;
					if (chat_ids) {

						let chatDetailmsg = await model.ChatMessages.findAll({ where: { chat_id: chat_ids, sender_id: receiverId, receiver_id: senderId }, order: [["id", "ASC"]] });
						if (chatDetailmsg) {
							for (var i = 0; i < chatDetailmsg.length; i++) {
								var check = await model.ChatMessages.update({ "isRead": "1" }, { where: { id: chatDetailmsg[i].id } });
							}
						}
						let chatDetail = await model.ChatMessages.findAll({ where: { chat_id: chat_ids }, order: [["id", "ASC"]] });

						var newArrayCreate = [];
						for (var i = 0; i < chatDetail.length; i++) {

							// await model.ChatMessages.update({ "isRead": "1" }, { where: { id: chatDetail[i].id }});
							let receiverUserDetail = await model.User.findOne({ where: { id: chatDetail[i].receiver_id } });
							let senderUserDetail = await model.User.findOne({ where: { id: chatDetail[i].sender_id } });
							// var createDate = dateformat(chatDetail[i].created_at, "dd-mmm-yyyy HH:MM TT", true);
							var createDate = moment(chatDetail[i].created_at).format('DD-MM-YYYY, h:mm:ss A')
							var updateDate = dateformat(chatDetail[i].updated_at, "dd-mmm-yyyy HH:MM TT", true);

							newArrayCreate.push({ 'id': chatDetail[i].id, 'chat_id': chatDetail[i].chat_id, 'sender_id': chatDetail[i].sender_id, 'receiver_id': chatDetail[i].receiver_id, 'message': chatDetail[i].message, 'deleted_at': chatDetail[i].deleted_at, 'created_at': createDate, 'updated_at': updateDate, 'receiver_name': receiverUserDetail.dataValues.name, 'receiver_avatar': receiverUserDetail.dataValues.avatar, 'sender_name': senderUserDetail.dataValues.name, 'sender_avatar': senderUserDetail.dataValues.avatar })
						}

						chat_data = newArrayCreate;

						callback({ 'status': 'success', 'message': 'Message get successfully', 'data': chat_data });
					} else {
						callback({ 'status': 'fail', 'message': 'Chat id not found' });
					}
				} else {
					callback({ 'status': 'fail', 'message': 'Chat id not found' });
				}
			} else {
				callback({ 'status': 'fail', 'message': 'Invalid Parameters' });
			}
		} catch (error) {
			console.log(error)
			callback({ 'status': 'fail', 'message': 'Chat not available' });
		}
	};

	module.updateListening = async function (data, callback) {
		try {

			console.log("updateListening data",data);
			let input = data;
			let message_id = input.messageId;

			if(message_id){

				await model.ChatMessages.update({ "isRead": "1" }, { where: { id: message_id }});

				callback({ "status": "success", "message": "Updated Success"});

			} else {
				callback({ "status": "fail", "message": "Invalid parameters", 'data': {} });
			}

		} catch (error) {
			console.log(error)
			callback({ 'status': 'fail', 'message': 'Chat not available' });
		}
	};

	module.blockUserGet = async function (data, callback) {
		try {

			let input = data;
			if (input.userId) {
				let user = await model.User.findOne({ where: { id: input.userId }, "block_status": "active", "user_type": "user" });

				if (user) {

					let blockUserdata = await model.User.update({ "block_status": "block" }, { where: { id: input.userId } });
					let user = await model.User.findOne({ where: { id: input.userId }, "block_status": "block" });

					callback({ "status": "success", "message": "User Block successfully", "data": user });
				} else {
					callback({ "status": "fail", "message": "Users not found", "data": {} });
				}
			} else {
				callback({ "status": "fail", "message": "Invalid parameters", 'data': {} });
			}
		} catch (error) {
			console.log(error)
			callback({ "status": "fail", "message": "Somwthing went wrong.", 'data': {} });
		}
	};

	module.unBlockUserGet = async function (data, callback) {
		try {

			let input = data;
			if (input.userId) {
				let user = await model.User.findOne({ where: { id: input.userId }, "block_status": "block", "user_type": "user" });

				if (user) {

					let blockUserdata = await model.User.update({ "block_status": "active" }, { where: { id: input.userId } });
					let user = await model.User.findOne({ where: { id: input.userId }, "block_status": "active" });

					callback({ "status": "success", "message": "User Unblock successfully", "data": user });
				} else {
					callback({ "status": "fail", "message": "Users not found", "data": {} });
				}
			} else {
				callback({ "status": "fail", "message": "Invalid parameters", 'data': {} });
			}
		} catch (error) {
			console.log(error)
			callback({ "status": "fail", "message": "Somwthing went wrong.", 'data': {} });
		}
	};

	module.reportUserGet = async function (data, callback) {
		try {

			let input = data;
			let catgId = input.categoryId;
			if (input.userId) {
				let user = await model.User.findOne({ where: { id: input.userId }, "user_type": "user" });

				if (user) {

					let blockUserUpdatedata = await model.User.update({ "isReport": "1" }, { where: { id: input.userId } });
					let blockUserdata = await model.User.findOne({ where: { id: input.userId }, "isReport": "1" });

					let catgData = await model.ReportCategory.findOne({ where: { id: catgId }, "status": "active" });

					if (catgData) {
						var catData = {
							"userId": blockUserdata.id,
							"userisReport": blockUserdata.isReport,
							"catgId": catgData.id,
							"catgName": catgData.category,
						}

						callback({ "status": "success", "message": "User Report successfully", "data": catData });

					} else {
						callback({ "status": "fail", "message": "Category not found", "data": {} });
					}
				} else {
					callback({ "status": "fail", "message": "Users not found", "data": {} });
				}
			} else {
				callback({ "status": "fail", "message": "Invalid parameters", 'data': {} });
			}
		} catch (error) {
			console.log(error)
			callback({ "status": "fail", "message": "Somwthing went wrong.", 'data': {} });
		}
	};

	module.getCategoryList = async function (data, callback) {
		try {

			let catgData = await model.ReportCategory.findAll({ where: { "status": "active" } });

			if (catgData) {
				callback({ "status": "success", "message": "Category list successfully", "data": catgData });
			} else {
				callback({ "status": "fail", "message": "Category list not found", "data": {} });
			}
		} catch (error) {
			console.log(error)
			callback({ "status": "fail", "message": "Somwthing went wrong.", 'data': {} });
		}
	};

	return module;
};


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



function randomCapitalNumeric(length) {
	var chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	var result = '';
	for (var i = length; i > 0; --i) result += chars[Math.floor(Math.random() * chars.length)];
	return result;
}