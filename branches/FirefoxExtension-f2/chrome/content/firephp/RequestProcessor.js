
FirePHPProcessor.Init = function() {

  this.RegisterConsoleStyleSheet('chrome://firephp/content/RequestProcessor.css');
  
  this.RegisterConsoleTemplate('exception',
    domplate(Firebug.Rep,
    {
      className: 'firephp-exception',
      tag:
          DIV({class: "head", _repObject: "$object"},
              A({class: "title", onclick: "$onToggleBody"}, "$object|getCaption")
          ),
    
      infoTag: DIV({class: "info"},
             TABLE({cellpadding: 3, cellspacing: 0},
              TBODY(
                TR(
                    TD({class:'headerFile'},'File'),
                    TD({class:'headerLine'},'Line'),
                    TD({class:'headerInst'},'Instruction')
                ),
                FOR("call", "$object|getCallList",
                    TR({},
                        TD({class:'cellFile'},
                            DIV({}, "$call.file")
                        ),
                        TD({class:'cellLine'},
                            DIV({}, "$call.line")
                        ),
                        TD({class:'cellInst'},
                            DIV({},  "$call|getCallLabel(",
                              FOR("arg", "$call|argIterator",
                                  TAG("$arg.tag", {object: "$arg.value"}),
                                  SPAN({class: "arrayComma"}, "$arg.delim")
                              ),
                              ")")
                        )
                      )
                    )
                  )
                )
              ),
              
    
      getCaption: function(item)
      {
        return item.Class+': '+item.Message;
      },
    
      onToggleBody: function(event)
      {
        var target = event.currentTarget;
        var logRow = getAncestorByClass(target, "logRow-firephp-exception");
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
      
      getCallList: function(call) {
        var list = call.Trace;
        list.unshift({'file':call.File,'line':call.Line,'class':call.Class,'type':'throw','args':[call.Message]});
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
        } catch(e) {}
        return list;
      },
      
      getCallLabel: function(call) {
        if(call['class']) {
          if(call['type']=='throw') {
           return 'throw '+call['class'];
          } else {
            return call['class']+call['type']+call['function'];
          }
        }
        return call['function'];
      },
      
      argIterator: function(call) {
        var items = [];
        if (!call.args) return items;
        for (var i = 0; i < call.args.length; ++i)
        {
            var arg = call.args[i];
            var rep = Firebug.getRep(arg);
            var tag = rep.shortTag ? rep.shortTag : rep.tag;
            var delim = (i == call.args.length-1 ? "" : ", ");
            items.push({name: 'arg'+i, value: arg, tag: tag, delim: delim});
        }
        return items;
      }
    
    })
  );



  
}


/* 
 * Called once for each request as it comes in
 */
FirePHPProcessor.ProcessRequest = function(URL,Data) {

  var data = json_parse(Data);

  if (data['FirePHP.Firebug.Console']) {

    Firebug.Console.openGroup(URL, null, "group", null, false);

    /* 
     * We wrap the logging code to ensure we can close the group
     * just in case something goes wrong.
     */
    try {
			
	    for (var index in data['FirePHP.Firebug.Console']) {
	
	      var item = data['FirePHP.Firebug.Console'][index];
        if (item && (item.length==2 || item.length==3)) {
        
          var mode = item[0].toLowerCase();
          
          if (mode == 'log' || mode == 'info' || mode == 'warn' || mode == 'dump') {
          } else 
          if (mode == 'error' || mode == 'exception') {
            Firebug.Errors.increaseCount(this.context);
          } else {
            mode = false;
          }
          
          if(mode!=false) {
            if(item.length==3) {
              if(item[1]) {
                this.logToFirebug(mode, [item[1],item[2]]);
              } else {
                this.logToFirebug(mode, item[2]);
              }
            } else {
              this.logToFirebug(mode, item[1]);
            }
          }
        }
	    }
		} catch(e) {
      this.logToFirebug('error', ['There was a problem writing your data from X-FirePHP-Data[\'FirePHP.Firebug.Console\'] to the console.',e]);
		}

    Firebug.Console.closeGroup();
  } 	
}
