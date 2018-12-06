var webSocket = null;
var global_callback = null;
var serverPort = '5000';	//webSocket连接端口

function getWebIP(){
    // let serverIp = window.location.hostname;
    return window.location.hostname;
}

function initWebSocket(){ //初始化weosocket
    //ws地址
    let wsUri = "ws://" +getWebIP()+ ":" + serverPort;
    webSocket = new WebSocket(wsUri);
    webSocket.onmessage = function(e){
        webSocketOnMessage(e);
    };
    webSocket.onclose = function(e){
        webSocketClose(e);
    };
    webSocket.onopen = function () {
        webSocketOpen();
    };
    //连接发生错误的回调方法
    webSocket.onerror = function () {
        console.log("WebSocket连接发生错误");
    };
}

// 实际调用的方法
function sendSock(agentData,callback){
    global_callback = callback;
    if (webSocket.readyState === webSocket.OPEN) {
        //若是ws开启状态
        webSocketSend(agentData);
    }else if (webSocket.readyState === webSocket.CONNECTING) {
        // 若是 正在开启状态，则等待1s后重新调用
        setTimeout(function () {
            sendSock(agentData,callback);
        }, 1000);
    }else {
        // 若未开启 ，则等待1s后重新调用
        setTimeout(function () {
            sendSock(agentData,callback);
        }, 1000);
    }
}

//数据接收
function webSocketOnMessage(e){
    global_callback(JSON.parse(e.data));
}

//数据发送
function webSocketSend(agentData){
    webSocket.send(JSON.stringify(agentData));
}

//关闭
function webSocketClose(e){
    console.log("connection closed (" + e.code + ")");
}

function webSocketOpen(e){
    console.log("连接成功");
}

initWebSocket();

export{sendSock};
