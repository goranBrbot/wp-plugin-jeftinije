var smdObject = {        
    Key: cenejeVars.shopId,                
    Type: "order",        
    OrderId: "",        
    Products: []      
};     
var smdWrapper = document.createElement("script");
smdWrapper.id = "_cpxTag";      
smdWrapper.type = "text/javascript";      
smdWrapper.src = "https://cpx.smind.si/Log/LogData?data=" + encodeURIComponent(JSON.stringify(smdObject));      
var smdScript = document.getElementsByTagName("script")[0];      
smdScript.parentNode.insertBefore(smdWrapper, smdScript); 