var app = require('http').createServer(handler);
var io = require('socket.io')(app);
var Redis = require('ioredis');
var redis = new Redis({
    port: 6379, // 端口
    host: 'redis', // 地址
    family: 4, // ip类型
    db: 0 // 数据库
});

app.listen(9090, function () {
    console.log('Server is running!') ;
});

function handler(req, res) {
    res.writeHead(200);
    res.end('');
}

io.on('connection', function (socket) {
    socket.on('message', function (message) {
        console.log(message);
    });
    socket.on('disconnect', function () {
        console.log('user disconnect');
    });
});

redis.psubscribe('*', function (err, count) {
});

redis.on('pmessage', function (subscrbed, channel, message) {
    message = JSON.parse(message);
    io.emit(channel + ':' + message.event, message.data);
});
