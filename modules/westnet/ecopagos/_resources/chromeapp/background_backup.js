chrome.app.runtime.onLaunched.addListener(function () {
    chrome.app.window.create('window.html', {
        'outerBounds': {
            'width': 400,
            'height': 500
        }
    });
});

//Messages from content scripts
chrome.runtime.onMessageExternal.addListener(function (request, sender, sendResponse) {

    sendResponse({farewell: "goodbye"});
    return true;

});

//onConnect callback
var onConnect = function (connectionInfo) {

    console.log("Conectado con éxito!");
    console.log(connectionInfo);

    connection = connectionInfo;

    //Getting all signals from the serial port
    chrome.serial.getControlSignals(connection.connectionId, function (signals) {
        console.log("Señales: ");
        console.log(signals);
    })

    //Set event for receiving data from the printer
    chrome.serial.onReceive.addListener(function (info) {
        console.log(info);
    });
    chrome.serial.onReceiveError.addListener(function (info) {
        console.log(info);
    });

    //Flush every buffer
    chrome.serial.flush(connection.connectionId, function (result) {
        console.log(result);
    });

    //Sending data to the port
    send(connection.connectionId, "Que onda?");

};

//send function
var send = function (connectionId, string) {
    chrome.serial.send(connectionId, stringToArrayBuffer(string), onSend);
}

//onSend event
var onSend = function (sendInfo) {
    console.log(sendInfo);
    console.log(chrome.runtime.lastError);
}

//Checks for port errors
var checkErrors = function () {
    if (chrome.runtime.lastError !== undefined) {
        console.log('Error attempting to connect: ', chrome.runtime.lastError);
        return;
    }
}

// Convert string to ArrayBuffer
var stringToArrayBuffer = function (str) {
    var buf = new ArrayBuffer(str.length * 2); // 2 bytes for each char
    var bufView = new Uint16Array(buf);
    for (var i = 0, strLen = str.length; i < strLen; i++) {
        bufView[i] = str.charCodeAt(i);
    }
    return buf;
}

// Convert buffer to ArrayBuffer
function bufferToArrayBuffer(buffer) {
    var buf = new ArrayBuffer(buffer.length);
    var bufView = new Uint8Array(buf);
    for (var i = 0; i < buffer.length; i++) {
        bufView[i] = buffer[i];
    }
    return buf;
}

//PrinterPort
var printerPort = null;

//Connection
var connection = null;

//Printer options
var deviceId = {
    vendorId: 1659,
    productId: 8963
};

//Find serial devices
chrome.serial.getDevices(function (ports) {

    //Find between all serial ports, the printer port
    for (var i = 0; i < ports.length; i++) {

        var currentPort = ports[i];

        if (currentPort.vendorId == deviceId.vendorId && currentPort.productId == deviceId.productId) {
            printerPort = currentPort;
            console.log(printerPort);
            break;
        }
    }

    //If the printerPort is available
    if (printerPort !== null) {

        //Try a connection to the printerPort
        chrome.serial.connect(printerPort.path, {
            name: printerPort.path,
            bitrate: parseInt(9600, 10),
            dataBits: "eight",
            parityBit: "no",
            stopBits: "one",
            sendTimeout: 0,
            receiveTimeout: 0,
            ctsFlowControl: false,
            bufferSize: 1,
            persistent: false
        }, onConnect);

    }

});