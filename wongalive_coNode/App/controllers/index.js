module.exports = function (model) {
	var module = {};
	module.cronController = require('./cronController.js')(model);
	return module;
}	