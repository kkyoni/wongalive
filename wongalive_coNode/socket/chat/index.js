var flag = true;
module.exports = function(model, io, client){
	var config = require('../../config/constants.js');
	var chat = require('./chat')(model,io,client);
	var comment = require('./comment')(model,io,client);
	
	//START: Chat message insert into database
	client.on('updateSocket', function(data, callback){
		// console.log("updateSocket index",data);
		chat.updateSocket(data, function(response){

			if (typeof callback == 'function') {
				return callback(response);
			} else {
				client.emit('updateSocket',response);
			}
		});
	});
	//END: Chat message insert into database

	// START : Get All register user
	client.on('getAllUserList', function(data, callback){
		// console.log("getAllUserList index",data);
		chat.getAllUserList(data, function(response){

			if (typeof callback == 'function') {
				return callback(response);
			} else {
				client.emit('getAllUserList',response);
			}
		});
	});
	// client.on('getAllUserList', function(data, callback){
	// 	chat.getAllUserList(data, function(response){
	// 		if (typeof callback == 'function') {
	// 			return callback(response)
	// 		} else {
	// 			client.emit('getAllUserList',response);
	// 		}
	// 	});	
	// });
	//END : Get All register user

	client.on('updateListening', function(data, callback){
		// console.log("updateListening index",data);
		chat.updateListening(data, function(response){

			if (typeof callback == 'function') {
				return callback(response);
			} else {
				client.emit('getAllUserList',response);
			}
		});
	});

	//START: Chat message insert into database
	client.on('singleChatSend', function(data, callback){
		// console.log("singleChatSend index",data);
		chat.singleChatSend(data, function(response){
			if (typeof callback == 'function') {
				return callback(response);
			} else {
				client.emit('getAllUserList',response);
			}
		});
	});
	//END: Chat message insert into database

	//START: Chat message Get into database
	client.on('singleChatGet', function(data, callback){
		// console.log("singleChatGet index",data);
		chat.singleChatGet(data, function(response){
			
			if (typeof callback == 'function') {
				return callback(response);
			} else {
				client.emit('getAllUserList',response);
			}
		});
	});
	//END: Chat message Get into database

	// START : Get user block 
	client.on('blockUserGet', function(data, callback){
		chat.blockUserGet(data, function(response){
			if (typeof callback == 'function') {
				return callback(response)
			} else {
				client.emit('blockUserGet',response);
			}
		});	
	});
	//END :  Get user block 
	
	// START : Get user unblock 
	client.on('unBlockUserGet', function(data, callback){
		chat.unBlockUserGet(data, function(response){
			if (typeof callback == 'function') {
				return callback(response)
			} else {
				client.emit('unBlockUserGet',response);
			}
		});	
	});
	//END :  Get user unblock 
	
	// START : Get report user 
	client.on('reportUserGet', function(data, callback){
		chat.reportUserGet(data, function(response){
			if (typeof callback == 'function') {
				return callback(response)
			} else {
				client.emit('reportUserGet',response);
			}
		});	
	});
	//END :  Get report user 
	// START : Get rcategoryList
	client.on('getCategoryList', function(data, callback){
		chat.getCategoryList(data, function(response){
			if (typeof callback == 'function') {
				return callback(response)
			} else {
				client.emit('getCategoryList',response);
			}
		});	
	});
	//END :  Get categoryList

	// client.on('updateOnlineOffline', function(data, callback){
	// 	chat.updateOnlineOffline(data, function(response){
	// 		if (typeof callback == 'function') {
	// 			return callback(response);
	// 		} else {
	// 			client.emit('updateOnlineOffline',response);
	// 		}
	// 	});	
	// });

	//Start: For comment related API
	client.on("LiveCountHost",function(data, callback){
		comment.LiveCountHost(data,function(response){
			return callback(response)
		})
	});

	client.on("LiveCountUser",function(data, callback){
		comment.LiveCountUser(data,function(response){
			return callback(response)
		})
	});

	client.on("CreateCommentLive",function(data, callback){
		comment.CreateCommentLive(data,function(response){
			return callback(response)
		})
	});

	client.on("CreateLiveStreaming",function(data, callback){
		comment.CreateLiveStreaming(data,function(response){
			return callback(response)
		})
	});

	client.on("LiveStreamingStatus",function(data, callback){
		comment.LiveStreamingStatus(data,function(response){
			return callback(response)
		})
	});

	client.on("ChatHidenAndShow",function(data, callback){
		comment.ChatHidenAndShow(data,function(response){
			return callback(response)
		})
	});

	client.on("DualCreateLiveStreaming",function(data, callback){
		comment.DualCreateLiveStreaming(data,function(response){
			return callback(response)
		})
	});
	
	client.on("DualLiveStreamingStatus",function(data, callback){
		comment.DualLiveStreamingStatus(data,function(response){
			return callback(response)
		});
	});

	client.on("AddGiftDiamonds",function(data, callback){
		comment.AddGiftDiamonds(data,function(response){
			return callback(response)
		});
	});

	client.on("GiftDiamondsList",function(data, callback){
		comment.GiftDiamondsList(data,function(response){
			return callback(response)
		});
	});

	client.on("homelistsocket",function(data, callback){
		comment.homelistsocket(data,function(response){
			return callback(response)
		});
	});

	client.on("setpkmode",function(data, callback){
		comment.setpkmode(data,function(response){
			return callback(response)
		});
	});

	client.on("stickerList",function(data, callback){
		comment.stickerList(data,function(response){
			return callback(response)
		});
	});
	//End: For comment related API
}	