
/* 
 * Called once for each request as it comes in
 */
FirePHPProcessor.ProcessRequest = function() {

  if (this.data['FirePHP.Firebug.Console']) {

    Firebug.Console.openGroup(this.url, null, "group", null, false);

    for (var index in this.data['FirePHP.Firebug.Console']) {

      var item = this.data['FirePHP.Firebug.Console'][index];

      if (item[0] == 'log' || item[0] == 'info' || item[0] == 'warn') {

        this.logToFirebug(item[0], item[1]);

      } else 
      if (item[0] == 'error') {

        Firebug.Errors.increaseCount(this.context);

        this.logToFirebug(item[0], item[1]);
      }
    }	

    Firebug.Console.closeGroup();
  } 	
}
