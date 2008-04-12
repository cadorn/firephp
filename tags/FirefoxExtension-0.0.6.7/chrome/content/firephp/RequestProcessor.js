
/* 
 * Called once for each request as it comes in
 */
FirePHPProcessor.ProcessRequest = function() {

  this.data = json_parse(this.data);

  if (this.data['FirePHP.Firebug.Console']) {

    Firebug.Console.openGroup(this.url, null, "group", null, false);

    /* 
     * We wrap the logging code to ensure we can close the group
     * just in case something goes wrong.
     */
    try {
			
	    for (var index in this.data['FirePHP.Firebug.Console']) {
	
	      var item = this.data['FirePHP.Firebug.Console'][index];
        if (item && item.length==2) {
        
          var mode = item[0].toLowerCase();
          if (mode == 'log' || mode == 'info' || mode == 'warn') {
          
            this.logToFirebug(mode, item[1]);
            
          } else 
          if (mode == 'error') {
          
            Firebug.Errors.increaseCount(this.context);
            
            this.logToFirebug(mode, item[1]);
          }
        }
	    }
		} catch(e) {
      this.logToFirebug('error', ['There was a problem writing your data from X-FirePHP-Data[\'FirePHP.Firebug.Console\'] to the console.',e]);
		}

    Firebug.Console.closeGroup();
  } 	
}
