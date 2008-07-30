
/* 
 * Called once for each request as it comes in
 */
FirePHPProcessor.ProcessRequest = function(Wildfire,URL,Data) {

  var data = json_parse(Data);

	if (data['FirePHP.Firebug.Console']) {
  
  	Firebug.Console.openGroup(URL, null, "group", null, false);
	
		for (var index in data['FirePHP.Firebug.Console']) {
			
			var item = data['FirePHP.Firebug.Console'][index];
			
      if (item) {
	  
		  	if (item[0] == 'LOG' || item[0] == 'info' || item[0] == 'warn') {
		  	
		  		this.logToFirebug(item[0], 'TEST: ' + item[1]);
		  		
		  	} else 
	  		if (item[0] == 'error') {
	  		
	  			Firebug.Errors.increaseCount(this.context);
	  			this.logToFirebug(item[0], 'TEST: ' + item[1]);
        }
      }
		}	
	
		Firebug.Console.closeGroup(this.context);
  }	
	  	
}
