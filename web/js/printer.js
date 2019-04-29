
var Printer = new function(){
    
    //private
    var applet_id;
    
    var self = this;
    
    this.registerApplet = function(route, id, containerId){
        
        if(!route || !id){
            alert('Error while registering printer applet.');
            return false;
        }
        
        applet_id = id;
        
        var app = document.createElement('object');
        app.id= applet_id;
        //app.archive= route;
        //app.code= 'applet.AppletPrinter';
        app.type= 'application/x-java-applet';
        app.width = '100';
        app.height = '100';
        
        var code = document.createElement('param');
        code.name = 'code';
        code.value='applet.AppletPrinter';
        app.appendChild(code);
        
        var archive = document.createElement('param');
        archive.name = 'archive';
        archive.value=route;
        app.appendChild(archive);
        
        if(containerId){
            document.getElementById(containerId).appendChild(app);
        }else{
            document.getElementsByTagName('body')[0].appendChild(app);
        }
    }
    
    function getNormalState(){
        console.log('Ejecutando metodo teststate de Javascript.');
        var applet = document.getElementById(applet_id); //Objeto del applet embebido en la pagina
        //applet.init();
        //applet.setData("Datos pasados por JAVASCRIPT.");
        //console.log("Muestro datos del applet por Javascript: " + applet.getData());
        var resp = applet.getNormalState();

        mostrardatos(resp); 

        applet.stop();
        console.log(resp);
    }

    function getPreferences(){
        console.log('Ejecutando metodo getPreferences() de Javascript.');
        var applet = document.getElementById(applet_id); //Objeto del applet embebido en la pagina
        var resp = applet.getPreferences();

        mostrardatos(resp); 

        applet.stop();
        console.log(resp);
    }

    function getDate(){
        console.log('Ejecutando metodo getDate() de Javascript.');
        var applet = document.getElementById(applet_id); //Objeto del applet embebido en la pagina
        var resp = applet.getDate();

        mostrardatos(resp); 

        applet.stop();
        console.log(resp);
    }

    function getPrinterState(){
        console.log('Ejecutando metodo obtener estado de impresora de Javascript.');
        var applet = document.getElementById(applet_id); //Objeto del applet embebido en la pagina
        var resp = applet.getPrinterState();

        mostrardatos(resp); 

        applet.stop();
        console.log(resp);
    }

    function getTaxPayerState(){
        console.log('Ejecutando metodo obtener estado de contribuyente de Javascript.');
        var applet = document.getElementById(applet_id); //Objeto del applet embebido en la pagina
        var resp = applet.getTaxPayerState();

        mostrardatos(resp); 

        applet.stop();
        console.log(resp);
    }

    function cancelBill(){
        console.log('Ejecutando cancelar facturar de Javascript.');
        var applet = document.getElementById(applet_id); //Objeto del applet embebido en la pagina
        applet.init();
        //applet.setData("Datos pasados por JAVASCRIPT.");
        //console.log("Muestro datos del applet por Javascript: " + applet.getData());
        var resp = applet.cancelBill();

        mostrardatos(resp); 

        applet.stop();
        console.log(resp);
    }
    
    function buildJson(bill){
        
        var json = {
            documentType:"T", // Se confecionara un Tiquet-factura
            documentLetter:bill.type, //Tique-Factura B
            costumerName:bill.customer.name + ' ' + bill.customer.lastname,
            costumerType: "F", //Consumidor final: TODO
            costumerDocumentType:bill.customer.document_type,
            costumerTaskID:bill.customer.document_number,
            costumerAdress:bill.customer.address,                
            paymentAmmount: bill.amount,
            items: []
        };
        
        bill.details.forEach(function(element, index){
            json.items.push(
                {
                    description: element.concept,
                    unitPrice : element.amount,
                    qty : element.qty,
                    taxes : element.taxes,
                }
            )
        });
        
        return json;
        
    }
    
    this.print = function(bill){
        
        var json = buildJson(bill);
        
        var applet = document.getElementById(applet_id); //Objeto del applet embebido en la pagina
        
        applet.init();
        var resp = applet.printBill(JSON.stringify(json));            
        applet.stop();            
        
        console.log(resp);

        return resp;

    }
    
}