
/* ***** BEGIN LICENSE BLOCK *****
 *  
 * This file is part of FirePHP (http://www.firephp.org/).
 * 
 * Copyright (C) 2007 Christoph Dorn
 * 
 * FirePHP is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * FirePHP is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public License
 * along with FirePHP.  If not, see <http://www.gnu.org/licenses/lgpl.html>.
 * 
 * ***** END LICENSE BLOCK ***** */


/*
 * @copyright  Copyright (C) 2007 Christoph Dorn
 * @license    http://www.gnu.org/licenses/lgpl.html
 * @author     Christoph Dorn <christoph@christophdorn.com>
 */


FirePHPProcessor.Init = function() {

  this.RegisterConsoleStyleSheet('chrome://firephp/content/RequestProcessor.css');
  
  this.RegisterConsoleTemplate('trace',
    domplate(Firebug.Rep,
    {
      className: 'firephp-trace',
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
        var logRow = getAncestorByClass(target, "logRow-firephp-trace");
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
        list.unshift({'file':call.File,'line':call.Line,'class':call.Class,'type':'throw'});
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
        if (!call.args) return [];
        var items = [];
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
          if (mode == 'error' || mode == 'trace') {
          
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
