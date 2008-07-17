        
Wildfire.Channel.HttpHeaders = function() {
  
  this.headerPrefix = 'x-wf-';
  
  this.protocols = new Array();

  this.protocol_ids = new Array();
  
  this.messageReceived = function(Key, Value)
  {
    Key = Key.toLowerCase();

		if(Key.substr(0,this.headerPrefix.length)==this.headerPrefix) {

      if(Key.substr(this.headerPrefix.length,9)=='protocol-') {
        var id = parseInt(Key.substr(this.headerPrefix.length+9));
        
        this.protocol_ids[id] = Value;

      } else {
        
        var parsed_key = this.parseKey(Key);
        
        var protocol = this.getProtocol(this.protocol_ids[parsed_key[0]]);

        protocol.receiveMessage(parsed_key[1], Value);
      }
    }
  };
  
  this.getProtocol = function(URI) {
    if(!this.protocols[URI]) {
      this.protocols[URI] = this.initProtocol(URI);
    }
    return this.protocols[URI];
  };

  this.initProtocol = function(URI) {
    switch(URI) {
      case 'http://meta.wildfirehq.org/Protocol/JsonStream/0.1':
        return new Wildfire.Protocol.JsonStream;
    }
    return false;
  };
   
  this.parseKey = function(Key) {
    
    var index = Key.indexOf('-',this.headerPrefix.length);
    var id = parseInt(Key.substr(this.headerPrefix.length,index-this.headerPrefix.length));

    return [id,Key.substr(index+1)];
  }; 
  
}
