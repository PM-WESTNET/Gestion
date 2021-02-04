chrome.app.runtime.onLaunched.addListener(function() {
    chrome.app.window.create('index.html', {
        bounds: {
            width: 400,
            height: 400,
            left: 100,
            top: 100
        },
        minWidth: 400,
        minHeight: 400
    });
});


function SerialPort(path, options, openImmediately) {
    console.log("SerialPort constructed.");

    this.comName = path;

    if (options) {
        for (var key in this.options) {
            //console.log("Looking for " + key + " option.");
            if (options[key] != undefined) {
                //console.log("Replacing " + key + " with " + options[key]);
                this.options[key] = options[key];
            }
        }
    }

    if (typeof chrome != "undefined" && chrome.serial) {
        var self = this;

        if (openImmediately != false) {
            this.open();
        }

    } else {
        throw "No access to serial ports. Try loading as a Chrome Application.";
    }
}

SerialPort.prototype.options = {
    baudrate: 9600,
    buffersize: 1
};

SerialPort.prototype.connectionId = -1;

SerialPort.prototype.comName = "";

SerialPort.prototype.eventListeners = {};

SerialPort.prototype.open = function (callback) {
    console.log("Opening ", this.comName);
    chrome.serial.connect(this.comName, {bitrate: parseInt(this.options.baudrate)}, this.proxy('onOpen', callback));
};

SerialPort.prototype.onOpen = function (callback, openInfo) {
    console.log("onOpen", callback, openInfo);
    this.connectionId = openInfo.connectionId;
    if (this.connectionId == -1) {
        this.publishEvent("error", "Could not open port.");
        return;
    }

    this.publishEvent("open", openInfo);


    console.log('Connected to port.', this.connectionId);

    typeof callback == "function" && callback(openInfo);

    chrome.serial.onReceive.addListener(this.proxy('onRead'));

};

SerialPort.prototype.onRead = function (readInfo) {
    if (readInfo && this.connectionId == readInfo.connectionId) {

        var uint8View = new Uint8Array(readInfo.data);
        var string = "";
        for (var i = 0; i < readInfo.data.byteLength; i++) {
            string += String.fromCharCode(uint8View[i]);
        }

        //console.log("Got data", string, readInfo.data);

        //Maybe this should be a Buffer()
        this.publishEvent("data", uint8View);
        this.publishEvent("dataString", string);
    }
}

SerialPort.prototype.write = function (buffer, callback) {
    if (typeof callback != "function") { callback = function() {}; }

    //Make sure its not a browserify faux Buffer.
    if (buffer instanceof ArrayBuffer == false) {
        buffer = buffer2ArrayBuffer(buffer);
    }

    chrome.serial.send(this.connectionId, buffer, callback);
};

SerialPort.prototype.writeString = function (string, callback) {
    this.write(str2ab(string), callback);
};

SerialPort.prototype.raw = function (string, callback) {
    this.write(new Uint8Array(string), callback);
};

SerialPort.prototype.close = function (callback) {
    chrome.serial.close(this.connectionId, this.proxy('onClose', callback));
};

SerialPort.prototype.disconnect = function (callback) {
    chrome.serial.disconnect (this.connectionId, this.proxy('onDisconnect', callback));
};

SerialPort.prototype.onClose = function (callback) {
    this.connectionId = -1;
    console.log("Closed port", arguments);
    this.publishEvent("close");
    typeof callback == "function" && callback(openInfo);
};

SerialPort.prototype.onDisconnect = function (callback) {
    this.connectionId = -1;
    console.log("Disconect port", arguments);
    this.publishEvent("disconnect");
    typeof callback == "function" && callback(openInfo);
};

SerialPort.prototype.flush = function (callback) {
    if(this.connectionId) {
        chrome.serial.flush(this.connectionId, function(data){

        });
    }
};

//Expecting: data, error
SerialPort.prototype.on = function (eventName, callback) {
    if (this.eventListeners[eventName] == undefined) {
        this.eventListeners[eventName] = [];
    }
    if (typeof callback == "function") {
        this.eventListeners[eventName].push(callback);
    } else {
        throw "can not subscribe with a non function callback";
    }
}

SerialPort.prototype.publishEvent = function (eventName, data) {
    if (this.eventListeners[eventName] != undefined) {
        for (var i = 0; i < this.eventListeners[eventName].length; i++) {
            this.eventListeners[eventName][i](data);
        }
    }
}

SerialPort.prototype.proxy = function () {
    var self = this;
    var proxyArgs = [];

    //arguments isnt actually an array.
    for (var i = 0; i < arguments.length; i++) {
        proxyArgs[i] = arguments[i];
    }

    var functionName = proxyArgs.splice(0, 1)[0];

    var func = function() {
        var funcArgs = [];
        for (var i = 0; i < arguments.length; i++) {
            funcArgs[i] = arguments[i];
        }
        var allArgs = proxyArgs.concat(funcArgs);

        self[functionName].apply(self, allArgs);
    }

    return func;
}


function SerialPortList(callback) {
    if (typeof chrome != "undefined" && chrome.serial) {
        chrome.serial.getDevices(function(ports) {
            var portObjects = Array(ports.length);
            for (var i = 0; i < ports.length; i++) {
                portObjects[i] = new SerialPort(ports[i], null, false);
            }
            callback(null, portObjects);
        });
    } else {
        callback("No access to serial ports. Try loading as a Chrome Application.", null);
    }
};

// Convert string to ArrayBuffer
function str2ab(str) {
    var buf = new ArrayBuffer(str.length);
    var bufView = new Uint8Array(buf);
    for (var i = 0; i < str.length; i++) {
        bufView[i] = str.charCodeAt(i);
    }
    return buf;
}

// Convert buffer to ArrayBuffer
function buffer2ArrayBuffer(buffer) {
    var buf = new ArrayBuffer(buffer.length);
    var bufView = new Uint8Array(buf);
    for (var i = 0; i < buffer.length; i++) {
        bufView[i] = buffer[i];
    }
    return buf;
}

function sleep(milliseconds) {
    var start = new Date().getTime();
    for (var i = 0; i < 1e7; i++) {
        if ((new Date().getTime() - start) > milliseconds){
            break;
        }
    }
}


chrome.runtime.onMessageExternal.addListener(function (request, sender, sendResponse) {
    var path = "";
    chrome.storage.local.get('path', function(result){
        path = result.path;
    });

    var sp;
    setTimeout(function(){
        sp = new SerialPort(path, {
            bufferSize: 1024
        }, true);
    }, 500);

    setTimeout(function(){
        for(i=1;i<=request.ticket.length; i++) {
            sp.writeString(request.ticket[i-1]);
            sp.flush();
            sleep(200);
            if(request.ticket.length == i) {
                sp.disconnect();
            }
        }
    }, 1000);
});