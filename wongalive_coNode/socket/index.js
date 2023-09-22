module.exports = function (model, io, client) {
	require('./chat/index.js')(model, io, client);
}
