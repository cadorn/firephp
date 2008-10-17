        
Wildfire.Channel.HttpHeaders = function() {
  
  this.headerPrefix = 'x-wf-';
  
  this.protocols = new Array();

  this.protocol_ids = new Array();
  
  this.messages = new Array();
  
  this.messageReceived = function(Key, Value)
  {
    Key = Key.toLowerCase();

		if(Key.substr(0,this.headerPrefix.length)==this.headerPrefix) {

      if(Key.substr(this.headerPrefix.length,9)=='protocol-') {
        var id = parseInt(Key.substr(this.headerPrefix.length+9));
        
        this.protocol_ids[id] = Value;
  
        /* Flush the messages to the protocol */
       
        if(this.messages[id]) {

          var protocol = this.getProtocol(Value);
                    
          for( var index in this.messages[id] ) {

            protocol.receiveMessage(this.messages[id][index][0][1], this.messages[id][index][1]);
  
          }

          this.messages[id] = new Array();
        }
              

      } else {
        
        var parsed_key = this.parseKey(Key);
        
        var protocol = this.getProtocol(this.protocol_ids[parsed_key[0]]);
        
        if(protocol) {
          protocol.receiveMessage(parsed_key[1], Value);
          
        } else {
          
          if(!this.messages[parsed_key[0]]) {
            this.messages[parsed_key[0]] = new Array();
          }
          this.messages[parsed_key[0]].push([parsed_key,Value]);
        }
      }
    }
  };
  
  this.allMessagesReceived = function() {
    for( var uri in this.protocols ) {
      this.protocols[uri].allMessagesReceived();
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
        return new Wildfire.Protocol.JsonStream_0_1;
      case 'http://meta.wildfirehq.org/Protocol/JsonStream/0.2':
        return new Wildfire.Protocol.JsonStream_0_2;
    }
    return false;
  };
   
  this.parseKey = function(Key) {
    
    var index = Key.indexOf('-',this.headerPrefix.length);
    var id = parseInt(Key.substr(this.headerPrefix.length,index-this.headerPrefix.length));

    return [id,Key.substr(index+1)];
  }; 
  
}
