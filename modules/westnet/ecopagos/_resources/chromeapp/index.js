var Index = new function(){
    this.getConfig = function(){
        var config = {};
        chrome.storage.local.get('path', function(result){
            config.path = result.path;
        });
        chrome.storage.local.get('baud', function(result){
            config.baud = result.baud;
        });
        return config;
    }

    this.loadPorts = function(){
        chrome.serial.getDevices(function (ports) {
            if(!ports) {
                console.log("Error listing ports", err);
                portsPath.options[0] = new Option(err, "ERROR");
                portsPath.options[0].selected = true;
                return;
            } else {
                var portsPath = document.getElementById("portPath");
                var config = Index.getConfig();

                for (var i = 0; i < ports.length; i++) {
                    portsPath.options[i] = new Option(ports[i].displayName, ports[i].path);

                    if(config) {
                        if (config.path = ports[i].path) {
                            portsPath.options[i].selected = true;
                        }
                    }
                }
                //var baud = document.getElementById("baudrate");

                var btnSave = document.getElementById("save");
                btnSave.onclick = function() {
                    var port = portsPath.options[portsPath.selectedIndex].value;
                    //var baudrateElement = document.getElementById("baudrate");
                    //var baudrate = baudrateElement.options[baudrateElement.selectedIndex].value;

                    // Guardo en el local el path del puerto
                    chrome.storage.local.set({'path': port});

                    var message = document.getElementById("message");
                    message.innerHTML = "Configuración guardada con exito";
                    message.style.display = 'block';

                    setTimeout(function(){
                        document.getElementById("message").innerHTML = "";
                        message.style.display = 'none';
                    }, 3000);
                };

            }
        });
    }
}
Index.loadPorts();