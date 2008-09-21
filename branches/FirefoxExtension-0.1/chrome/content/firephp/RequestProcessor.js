/* ***** BEGIN LICENSE BLOCK *****
 * 
 * This software is distributed under the New BSD License.
 * See LICENSE file for terms of use.
 * 
 * ***** END LICENSE BLOCK ***** */

FirePHPProcessor.Init = function() {

  this.RegisterConsoleStyleSheet('chrome://firephp/content/RequestProcessor.css');


  function getTraceTemplate() {
    return domplate(Firebug.Rep, {
      tag: DIV({
        class: "head",
        _repObject: "$object"
      }, A({
        class: "title",
        onclick: "$onToggleBody"
      }, "$object|getCaption")),
      
      infoTag: DIV({
        class: "info"
      }, TABLE({
        cellpadding: 3,
        cellspacing: 0
      }, TBODY(TR(TD({
        class: 'headerFile'
      }, 'File'), TD({
        class: 'headerLine'
      }, 'Line'), TD({
        class: 'headerInst'
      }, 'Instruction')), FOR("call", "$object|getCallList", TR({}, TD({
        class: 'cellFile'
      }, DIV({}, "$call.file")), TD({
        class: 'cellLine'
      }, DIV({}, "$call.line")), TD({
        class: 'cellInst'
      }, DIV({}, "$call|getCallLabel(", FOR("arg", "$call|argIterator", TAG("$arg.tag", {
        object: "$arg.value"
      }), SPAN({
        class: "arrayComma"
      }, "$arg.delim")), ")"))))))),
      
      
      getCaption: function(item){
        if (item.Class && item.Type == 'throw') {
          return item.Class + ': ' + item.Message;
        }
        else {
          return item.Message;
        }
      },
      
      onToggleBody: function(event){
        var target = event.currentTarget;
        var logRow = getAncestorByClass(target, 'logRow-'+this.className);
        if (isLeftClick(event)) {
          toggleClass(logRow, "opened");
          
          if (hasClass(logRow, "opened")) {
          
            /* Lets only render the stack trace once we request it */
            if (!getChildByClass(logRow, "head", "info")) {
              this.infoTag.append({
                'object': getChildByClass(logRow, "head").repObject
              }, getChildByClass(logRow, "head"));
            }
          }
        }
      },
      
      getCallList: function(call){
        var list = call.Trace;
        list.unshift({
          'file': call.File,
          'line': call.Line,
          'class': call.Class,
          'function': call.Function,
          'type': call.Type,
          'args': call.Args
        });
        /* Now that we have all call events, lets sew if we can shorten the filename.
       * This only works for unif filepaths for now.
       * TODO: Get this working for windows filepaths as well.
       */
        try {
          if (list[0].file.substr(0, 1) == '/') {
            var file_shortest = list[0].file.split('/');
            var file_original_length = file_shortest.length;
            for (var i = 1; i < list.length; i++) {
              var file = list[i].file.split('/');
              for (var j = 0; j < file_shortest.length; j++) {
                if (file_shortest[j] != file[j]) {
                  file_shortest.splice(j, file_shortest.length - j);
                  break;
                }
              }
            }
            if (file_shortest.length > 2) {
              if (file_shortest.length == file_original_length) {
                file_shortest.pop();
              }
              file_shortest = file_shortest.join('/');
              for (var i = 0; i < list.length; i++) {
                list[i].file = '...' + list[i].file.substr(file_shortest.length);
              }
            }
          }
        } 
        catch (e) {
        }
        return list;
      },
      
      getCallLabel: function(call){
        if (call['class']) {
          if (call['type'] == 'throw') {
            return 'throw ' + call['class'];
          }
          else {
            return call['class'] + call['type'] + call['function'];
          }
        }
        return call['function'];
      },
      
      argIterator: function(call){
        if (!call.args) 
          return [];
        var items = [];
        for (var i = 0; i < call.args.length; ++i) {
          var arg = call.args[i];
          
//          var rep = FirePHP.getRep(arg);
//          var tag = rep.shortTag ? rep.shortTag : rep.tag;
                var rep = FirebugReps.PHPVariable;
                var tag = rep.tag;
          
/*          
          if(!arg) {
            var rep = Firebug.getRep(arg);
            var tag = rep.shortTag ? rep.shortTag : rep.tag;
          } else
          if (arg.constructor.toString().indexOf("Array") != -1 ||
              arg.constructor.toString().indexOf("Object") != -1) {
            var rep = FirebugReps.PHPVariable;
            var tag = rep.tag;
          }
          else {
            var rep = Firebug.getRep(arg);
            var tag = rep.shortTag ? rep.shortTag : rep.tag;
          }
*/          
          var delim = (i == call.args.length - 1 ? "" : ", ");
          items.push({
            name: 'arg' + i,
            value: arg,
            tag: tag,
            delim: delim
          });
        }
        return items;
      }
      
    });
  }
    
  this.RegisterConsoleTemplate('exception',domplate(getTraceTemplate(),
    {
      className: 'firephp-exception',
    })
  );

  this.RegisterConsoleTemplate('trace',domplate(getTraceTemplate(),
    {
      className: 'firephp-trace',
    })
  );


  this.RegisterConsoleTemplate('table',
    domplate(Firebug.Rep,
    {
      className: 'firephp-table',
      tag:
          DIV({class: "head", _repObject: "$object"},
              A({class: "title", onclick: "$onToggleBody"}, "$object|getCaption")
          ),
    
      infoTag: DIV({class: "info"},
             TABLE({cellpadding: 3, cellspacing: 0},
              TBODY(
                TR(
                  FOR("column", "$object|getHeaderColumns",
                    TD({class:'header'},'$column')
                  )
                ),
                FOR("row", "$object|getRows",
                    TR({},
                      FOR("column", "$row|getColumns",
                        TD({class:'cell'},
                          TAG("$column.tag", {object: "$column.value"})
                        )
                      )
                    )
                  )
                )
              )
             ),
                  
           
                  
      getCaption: function(item)
      {
        return item[0];
      },
    
      onToggleBody: function(event)
      {
        var target = event.currentTarget;
        var logRow = getAncestorByClass(target, "logRow-firephp-table");
        if (isLeftClick(event))
        {
          toggleClass(logRow, "opened");
    
          if (hasClass(logRow, "opened"))
          {
    
            /* Lets only render the stack trace once we request it */        
            if (!getChildByClass(logRow, "head", "info"))
            {
                this.infoTag.append({'object':getChildByClass(logRow, "head").repObject},
                                    getChildByClass(logRow, "head"));
            }
          }
        }
      },
      
      getHeaderColumns: function(object) {
        
        try{
          return object[1][0];
        } catch(e) {}
        
        return [];
      },
      
      getRows: function(object) {
        
        try{
          var rows = object[1];
          rows.splice(0,1);
          return rows;
        } catch(e) {}
        
        return [];
      },
      
      getColumns: function(row) {

        if (!row) return [];
        
        var items = [];

        try {
        
          for (var i = 0; i < row.length; ++i)
          {
              var arg = row[i];
//          var rep = FirePHP.getRep(arg);
//          var tag = rep.shortTag ? rep.shortTag : rep.tag;
              
              var rep = FirebugReps.PHPVariable;
              
              if(typeof(arg)=='string') {
                rep = FirebugReps.FirePHPText;
              }
              
              var tag = rep.tag;
                
                
/*  
              if(!arg) {
                var rep = Firebug.getRep(arg);
                var tag = rep.shortTag ? rep.shortTag : rep.tag;
              } else
              if (arg.constructor.toString().indexOf("Array")!=-1 ||
                  arg.constructor.toString().indexOf("Object")!=-1) {
                var rep = FirebugReps.PHPVariable;
                var tag = rep.tag;
                
//                obj = new Object();
//                obj.Array = arg;
//                arg = ['Click for Data',obj];
              } else {
                var rep = FirebugReps.Text;
                var tag = rep.shortTag ? rep.shortTag : rep.tag;
              }
*/              
              items.push({name: 'arg'+i, value: arg, tag: tag});
          }
        } catch(e) {}
        
        return items;
      },
      
    })
  );



  
}


/* 
 * Called once for each request as it comes in
 */
FirePHPProcessor.ProcessRequest = function(Wildfire,URL,Data) {
  
  if (Data || Wildfire.hasMessages()) {

    Firebug.Console.openGroup([URL], null, "firephpRequestGroup", null, false);

    /* 
     * We wrap the logging code to ensure we can close the group
     * just in case something goes wrong.
     */
    try {
			
      if(Data) {
        var data = json_parse(Data);
      
        if (data['FirePHP.Firebug.Console']) {
        
    	    for (var index in data['FirePHP.Firebug.Console']) {
    	
    	      var item = data['FirePHP.Firebug.Console'][index];
            if (item && item.length==2) {
            
              this.processMessage(item[0], item[1]);
            }
    	    }
        }
      }      
		} catch(e) {
      this.logToFirebug('error', ['There was a problem writing your data from X-FirePHP-Data[\'FirePHP.Firebug.Console\'] to the console.',e]);
		}


    try {
			
      if(Wildfire.hasMessages()) {
           
        var messages = Wildfire.getMessages('http://meta.firephp.org/Wildfire/Structure/FirePHP/FirebugConsole/0.1');
           
        for( var index in messages ) {
          
          var item = json_parse(messages[index]);
          
          this.processMessage(item[0].Type, item[1]);
        }
      }
 
 		} catch(e) {
      this.logToFirebug('error', ['There was a problem writing your data from the Wildfire Plugin http://meta.firephp.org/Wildfire/Structure/FirePHP/FirebugConsole/0.1',e]);
		}

    Firebug.Console.closeGroup();
    
  }
}



FirePHPProcessor.processMessage = function(mode, data) {

  mode = mode.toLowerCase();

dump('DATA: '+mode+' - '+data+"\n");        


  /* Change mode from TRACE to EXCEPTION for backwards compatibility */
  if (mode == 'trace') {
    var change = true;
    for (var key in data) {
      if (key == 'Type') {
        change = false;
      }
    }
    if (change) {
      mode = 'exception';
      data.Type = 'throw';
    }
  }
            
  if (mode == 'log' || mode == 'info' || mode == 'warn' || mode == 'table' || mode == 'trace') {
  
    this.logToFirebug(mode, data);
    
  } else 
  if (mode == 'error' || mode == 'exception') {
  
    Firebug.Errors.increaseCount(this.context);
    
    this.logToFirebug(mode, data);
  }
}
