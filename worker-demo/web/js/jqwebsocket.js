/**
 * jqwebsocket.js
 * $.websocket({url:'',onMessage:null});
 */
(function($) {
    $.websocket = function(options) {
        /*
        var defaults = {
            domain: top.location.hostname,
            port:3398,
            protocol:"",
            url: '',
            onInit: function(){},
            onOpen: function(event){},
            onSend: function(msg){},
            onMessage: function(msg){},
            onError: function(event){},
        };
        */
        var defaults = {
            url: ''
        };
        var opts = $.extend(defaults,options);
        //var szServer = "ws://" + opts.domain + ":" + opts.port + "/" + opts.protocol;
        var szServer = opts.url;
        var socket = null;
        var bOpen = false;
        var t1 = 0; 
        var t2 = 0; 
        var messageevent = {
            onInit:function(){
                if(!("WebSocket" in window) && !("MozWebSocket" in window)){  
                    return false;
                }
                if(("MozWebSocket" in window)){
                    socket = new MozWebSocket(szServer);  
                }else{
                    socket = new WebSocket(szServer);
                }
                if(opts.onInit){
                    opts.onInit();
                }
            },
            onOpen:function(event){
                bOpen = true;
                if(opts.onOpen){
                    opts.onOpen(event);
                }
            },
            onSend:function(msg){
                //t1 = new Date().getTime(); 
                if(opts.onSend){
                    opts.onSend(msg);
                }
                socket.send(msg);
            },
            onMessage:function(msg){
                //t1 = t2;
                //t2 = new Date().getTime(); 
                if(opts.onMessage){
                    opts.onMessage(msg.data);//,t2 - t1);
                }
            },
            onError:function(event){
                if(opts.onError){
                    opts.onError(event);
                }
            },
            onClose:function(event){
                if(opts.onclose){
                    opts.onclose(event);
                }
                if(socket.close() != null){
                    socket = null;
                }
                //websocket关闭的时候，判断是否浏览器的情况
                if(document.visibilityState == 'hidden') {
                    //页面被隐藏或调到后台的情况，我们这就不做处理
                    console.log("Close-hidden")
                }else{
                    //页面还在时异常被关闭才进行重连
                    console.log("Close-Exception，自动重连")
                    //reconnect();
                }
            }
        }

        messageevent.onInit();
        socket.onopen = messageevent.onOpen;
        socket.onmessage = messageevent.onMessage;
        socket.onerror = messageevent.onError;
        socket.onclose = messageevent.onClose;
        
        this.send = function(pData){
            if(bOpen == false){
                return false;
            }
            messageevent.onSend(pData);
            return true;
        }
        this.close = function(){
            messageevent.onClose();
        }
        return this;
    };
})(jQuery);
