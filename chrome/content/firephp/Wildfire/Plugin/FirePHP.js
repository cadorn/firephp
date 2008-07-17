        
Wildfire.Plugin.FirePHP = function() {
  

  this.PLUGINL_URI = 'http://meta.firephp.org/Wildfire/Plugin/ZendFramework/FirePHP/1.6';
  this.STRUCTURE_URI_DUMP = 'http://meta.firephp.org/Wildfire/Structure/FirePHP/Dump/0.1';
  this.STRUCTURE_URI_FIREBUGCONSOLE = 'http://meta.firephp.org/Wildfire/Structure/FirePHP/FirebugConsole/0.1';

  
  this.channel = new Wildfire.Channel.HttpHeaders;

  this.messages = new Array();


  this.init = function() {
  
    this.channel.getProtocol('http://meta.wildfirehq.org/Protocol/JsonStream/0.1').registerPlugin(this);
  };

  this.getURI = function()
  {
    return this.PLUGINL_URI;
  };
  
  this.receivedMessage = function(Index, Structure, Message) {
    
    this.messages.push([Index,Structure,Message]);
  };
  
  this.hasMessages = function() {
    return (this.messages.length>0)?true:false;
  };
  
  this.getMessages = function(StructureURI) {
    
    var messages = new Array();
    
    for( var index in this.messages ) {
      if(this.messages[index][1]==StructureURI) {
        messages.push(this.messages[index][2]);
      }
    }

    return messages;
  };
  
}
