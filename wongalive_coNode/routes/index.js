module.exports = function (app, model, controllers) {
	app.get("/testIosPush",function(req, res){
		let device_token = req.query.deviceToken;
		console.log("device_token",device_token);
		const apn = require('apn');
		const AppConstantKeyId = "GYAY3N2TCK";
		const AppConstantTeamId = "2FJUTJSD6Z";
		let options = {
			token: {
				key: "./config/test.p8",
				keyId: AppConstantKeyId,
				teamId: AppConstantTeamId
			},
			production: true
		};

		let apnProvider = new apn.Provider(options);

		let deviceToken = [device_token];

		let notification = new apn.Notification();
		notification.expiry = Math.floor(Date.now() / 1000) + 24 * 3600;
		notification.badge = 0;
		notification.alert = "Hello! I am testing";
		notification.payload = {status:"success",message:"Testing success"};

		notification.topic = "com.app.allinSocio";
		apnProvider.send(notification, deviceToken).then((result, err) => {
			console.log("Response is :", result);
			console.log("Iphone Failed Response is : ", result.failed);
		});
		apnProvider.shutdown();
		return res.send({status:"success",message:"Push send successfully"});
	})
}	