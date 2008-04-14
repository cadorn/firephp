/* ***** BEGIN LICENSE BLOCK *****
 * Version: MPL 1.1/GPL 2.0/LGPL 2.1
 *
 * The contents of this file are subject to the Mozilla Public License Version
 * 1.1 (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Software distributed under the License is distributed on an "AS IS" basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
 * for the specific language governing rights and limitations under the
 * License.
 *
 * The Initial Developer of the Original Code is Christoph Dorn.
 *
 * Portions created by the Initial Developer are Copyright (C) 2006
 * the Initial Developer. All Rights Reserved.
 *
 * Contributor(s):
 *     Christoph Dorn <christoph@christophdorn.com>
 *
 * Alternatively, the contents of this file may be used under the terms of
 * either the GNU General Public License Version 2 or later (the "GPL"), or
 * the GNU Lesser General Public License Version 2.1 or later (the "LGPL"),
 * in which case the provisions of the GPL or the LGPL are applicable instead
 * of those above. If you wish to allow use of your version of this file only
 * under the terms of either the GPL or the LGPL, and not to allow others to
 * use your version of this file under the terms of the MPL, indicate your
 * decision by deleting the provisions above and replace them with the notice
 * and other provisions required by the GPL or the LGPL. If you do not delete
 * the provisions above, a recipient may use your version of this file under
 * the terms of any one of the MPL, the GPL or the LGPL.
 *
 * ***** END LICENSE BLOCK ***** */


var externalMode = (window.location == "chrome://firebug/content/firebug.xul");


if(externalMode) {
	var detachArgs = window.arguments[0];
	var FBL = detachArgs.FBL;            
	var Firebug = detachArgs.Firebug;
}


FBL.ns(function() { with (FBL) {


const nsIPrefBranch2 = FirebugLib.CI("nsIPrefBranch2");
const nsIPermissionManager = FirebugLib.CI("nsIPermissionManager");

const PrefService = FirebugLib.CC("@mozilla.org/preferences-service;1");
const PermManager = FirebugLib.CC("@mozilla.org/permissionmanager;1");

const observerService = CCSV("@mozilla.org/observer-service;1", "nsIObserverService");



const nsIHttpChannel = CI("nsIHttpChannel")
const nsIWebProgress = CI("nsIWebProgress")
const nsIWebProgressListener = CI("nsIWebProgressListener")
const nsISupportsWeakReference = CI("nsISupportsWeakReference")
const nsISupports = CI("nsISupports")
const STATE_TRANSFERRING = nsIWebProgressListener.STATE_TRANSFERRING;
const STATE_IS_DOCUMENT = nsIWebProgressListener.STATE_IS_DOCUMENT;
const STATE_STOP = nsIWebProgressListener.STATE_STOP;
const STATE_IS_REQUEST = nsIWebProgressListener.STATE_IS_REQUEST;
const NOTIFY_ALL = nsIWebProgress.NOTIFY_ALL;



// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 

const prefs = PrefService.getService(nsIPrefBranch2);
const pm = PermManager.getService(nsIPermissionManager);

var FirePHP = top.FirePHP = {

  version: '0.0.6',

  initialize: function() {

    /* Set the FirePHP Extension version for the FirePHP Service Component */  
    try {
      Components.classes['@firephp.org/service;1'].getService(Components.interfaces.nsIFirePHP).setExtensionVersion(this.version);
    } catch (err) {}


		this.enable();

  },
  	  
  /* Enable and disable FirePHP
   * At the moment this enables and disables the FirePHP accept header
   */ 
  enable: function() {
dump('FirePHP.enable()'+"\n");    
    /* Enable the FirePHP Service Component to set the multipart/firephp accept header */  
    try {
      Components.classes['@firephp.org/service;1'].getService(Components.interfaces.nsIFirePHP).setRequestHeaderEnabled(true);
    } catch (err) {}
  },
  disable: function() {
dump('FirePHP.disable()'+"\n");    
    /* Enable the FirePHP Service Component to set the multipart/firephp accept header */  
    try {
      Components.classes['@firephp.org/service;1'].getService(Components.interfaces.nsIFirePHP).setRequestHeaderEnabled(false);
    } catch (err) {}
  },
	
  openPermissions: function()
  {
      var params = {
          permissionType: "firephp",
          windowTitle: "FirePHP Allowed Sites",
          introText: "Choose which web sites are allowed to be used with FirePHP.",
          blockVisible: true, sessionVisible: false, allowVisible: true, prefilledHost: ""
      };

      FirebugLib.openWindow("Browser:Permissions", "chrome://browser/content/preferences/permissions.xul",
          "", params);
  },
	
  isURIAllowed: function(host)
  {
    if(!host) return false;
    var ioService = FirebugLib.CCSV("@mozilla.org/network/io-service;1", "nsIIOService");
    var uri = ioService.newURI('http://'+host, null, null);
    return uri && 
        (pm.testPermission(uri, "firephp") == nsIPermissionManager.ALLOW_ACTION);
  },

  enableSite: function(host)
  {
    var ioService = FirebugLib.CCSV("@mozilla.org/network/io-service;1", "nsIIOService");

    var uri = ioService.newURI('http://'+host, null, null);
    pm.add(uri, "firephp", nsIPermissionManager.ALLOW_ACTION);
  },
  
  
  parseHeaders: function(url,headers_in,parser) {
    
 		var info = [];
   
		var header_indexes = [];
		var header_values = [];
		var data = '';
    
    function parseHeader(name,value) {
				name = name.toLowerCase();

				if(name.substr(0,15)=='x-firephp-data-' && name.length==27) {

					header_indexes[header_indexes.length] = name.substr(15);
					header_values[header_values.length] = value;

				} else							
				if(name=='x-firephp-data' ||
					 name=='firephp-data' || 
					 name.substr(0,15)=='x-firephp-data-' ||
					 name.substr(0,13)=='firephp-data-') {

					data += value;
				
				} else
				if(name=='x-firephp-processorurl') {
					info['processorurl'] = value;
				} else
				if(name=='x-firephp-rendererurl' ||
           name=='firephp-rendererurl' ||
           name=='firephp-mask') {
					info['rendererurl'] = value;
				}      
    }
    
    
    if(parser=='visit') {

      headers_in.visitResponseHeaders({
        visitHeader: function(name, value)
        {
          parseHeader(name,value);
        }
      });						
      
    } else
    if(parser=='array') {
      
      for( var index in headers_in ) {
        parseHeader(headers_in[index].name,headers_in[index].value);
      }
    }
    
		/* Sort the header and create final data object */
		
		if(header_indexes.length>0) {
			
			var headers = FirePHPLib.sortSecondByFirstNumeric(header_indexes,header_values);
			
			for( var index in headers ) {
				data += headers[index];
			}
      
      info['data'] = data;

		} else {
      
      info['data'] = data;
    }
    
    return info;
  }	
	
}


function safeGetURI(browser)
{
    try
    {
        return browser.currentURI;
    }
    catch (exc)
    {
        return null;
    }
}


function isEnabled(uri) {
    if (Firebug.disabledAlways)
    {
        // Check if the whitelist makes an exception
        if (!Firebug.isURIAllowed(uri))
            return false;
    }
    else
    {
        // Check if the blacklist says no
        if (Firebug.isURIDenied(uri))
            return false;
    }
    return true;  
}




Firebug.FirePHP = extend(Firebug.Module,
{
	
  activeContext: null,
  activeBrowser: null,
  
  
  enable: function()
  {
dump('Firebug.FirePHP.enable()'+"\n");    
		FirePHP.enable();
  },
  
  disable: function()
  {
dump('Firebug.FirePHP.disable()'+"\n");    
		FirePHP.disable();
  },		
	

  initContext: function(context)
  {
dump('Firebug.FirePHP.initContext()'+"\n");    
    monitorContext(context);
  },
  destroyContext: function(context)
  {
dump('Firebug.FirePHP.destroyContext()'+"\n");    
    unmonitorContext(context);
  },
  
  reattachContext: function(browser, context)
  {
dump('Firebug.FirePHP.reattachContext()'+"\n");    
  },
  
  watchWindow: function(context, win)
  {
dump('Firebug.FirePHP.watchWindow()'+"\n");    
  },
  unwatchWindow: function(context, win)
  {
dump('Firebug.FirePHP.unwatchWindow()'+"\n");    
  },
  showPanel: function(browser, panel)
  {
dump('Firebug.FirePHP.showPanel()'+"\n");    
  },
  
  
  
  showContext: function(browser, context)
  {
dump('Firebug.FirePHP.showContext()'+"\n");    
    
    if(isEnabled(safeGetURI(browser))) {
  		FirePHP.enable();
    } else {
  		FirePHP.disable();
    }

    this.activeBrowser = browser;
    this.activeContext = context;
  },
	 
   
   
   
  
	processRequest: function(Request) {
	
		var name = '';
		var url = Request.name;


		var http = QI(Request, nsIHttpChannel);

    var info = FirePHP.parseHeaders(url,http,'visit');
    var mask = info['processorurl'];
    var data = info['data'];

		
		var domain = FirebugLib.getDomain(mask);
    
		if(data) {
			if(!mask) {
				mask = 'chrome://firephp/content/RequestProcessor.js';
			} else {
  			if(!FirePHP.isURIAllowed(domain)) {
          this.logFormatted(['By default FirePHP is not allowed to load your custom processor from "'+mask+'". You can allow this by going to the "Net" panel and clicking on the "Server" tab for this request.'], "warn");
  				mask = 'chrome://firephp/content/RequestProcessor.js';
        }
      }
			
			if(!this.FirePHPProcessor) {
				this.FirePHPProcessor = function() {
					var initialized = false;
					return {
						_Init: function() {
							if(this.initialized) return;
							this.Init();
							this.initialized = true;
						},
						Init : function() {
						},
						ProcessRequest: function() {
						},
						logToFirebug: function(Type, Data) {
							Firebug.FirePHP.logFormatted([Data], Type);
						}
					}
				}();
			}
			
			var proecessor_context = {FirePHPProcessor: this.FirePHPProcessor,
										 Firebug: Firebug,
										 data: data,
										 context: this.activeContext,
										 url: url};

			jQuery.ajax({
				type: "GET",
				url: mask,
				success: function(ReturnData){

					with (proecessor_context) {							
						FirePHPProcessor.url = url;
						FirePHPProcessor.data = data;
						FirePHPProcessor.context = proecessor_context.context;

            try {
  						eval(ReturnData);
              
  						FirePHPProcessor._Init();
  						FirePHPProcessor.ProcessRequest();
            } catch(e) {
              Firebug.FirePHP.logFormatted(['Error executing custom FirePHP processor!',e],'warn');  
            }
					}	
		
				},
				error: function(XMLHttpRequest, textStatus, errorThrown){
					if(mask.substr(0,9)=='chrome://') {

						with (proecessor_context) {
							FirePHPProcessor.url = url;
							FirePHPProcessor.data = data;
							FirePHPProcessor.context = proecessor_context.context;

							eval(XMLHttpRequest.responseText);
						
							FirePHPProcessor._Init();
							FirePHPProcessor.ProcessRequest();
						}	

					} else {
						
						this.logFormatted(['Error loading processor from: '+mask], "warn");
						
					}
				}
			});		
		}
		
	},

  logFormatted: function(args, className)
  {
    if (className == 'trace') {
      className = 'spy';
      return Firebug.Console.logRow(this.appendObject, args[0], this.activeContext, className, FirePHP.Reps.PHPException, null, true);
    } else {
  	  return Firebug.Console.logFormatted(args, this.activeContext, className, false, null);
    }
  },
  
  appendObject: function(object, row, rep)
  {
      row.style.padding = '0px';
      return rep.tag.append({object: object}, row);
  }
    		   
});




/*
 * The section below implements the custom PHP Exception (with trace) log item representation.
 * 
 * The code works but could use some refactoring and cleaning up.
 * 
 * TODO: The CSS styles are defined inline. They should be defined in an external css file instead.
 *       This can only be done once Firebug supports custom CSS files for the panel.html file.
 */


FirePHP.Reps = {
}

FirePHP.Reps.PHPException = domplate(Firebug.Rep,
{
  tag:
      DIV({class: "spyHead", _repObject: "$object", style: 'padding-bottom: 1px;padding-top: 2px; padding-right: 10px; background-color: LightYellow;'},
          A({class: "spyTitle", onclick: "$onToggleBody", style: 'padding-bottom: 0px; padding-top: 2px; font-family: Monaco, monospace; background-image: url(chrome://firebug/skin/errorIcon.png); background-repeat: no-repeat; background-position: 4px 2px;'},
              SPAN({style:'padding-left: 4px; color: red; font-weight: normal; text-decoration: underline;'}, "$object|getCaption")
          )
      ),

  getCaption: function(spy)
  {
    return spy.Class+': '+spy.Message;
  },

  onToggleBody: function(event)
  {
    var target = event.currentTarget;
    var logRow = getAncestorByClass(target, "logRow-spy");
    if (isLeftClick(event))
    {
      toggleClass(logRow, "opened");

      if (hasClass(logRow, "opened"))
      {
        logRow.style.borderBottom = '1px solid #D7D7D7';

        var spy = getChildByClass(logRow, "spyHead").repObject;
        updatePHPException(logRow,spy);
      }
    }
  }
});


FirePHP.PHPExceptionBody = domplate(Firebug.Rep,
{
  tag: DIV({class: "netInfoBody"},
         TABLE({class: "netTable", cellpadding: 3, cellspacing: 0},
          TBODY(
            TR(
                TD({style:'white-space:nowrap; font-weight: bold;'},'File'),
                TD({style:'white-space:nowrap; font-weight: bold; text-align: right;'},'Line'),
                TD({style:'white-space:nowrap; font-weight: bold; padding-left: 10px; width: 100%;'},'Instruction')
            ),
            FOR("call", "$object|getCallList",
                TR({},
                    TD({style:'border:1px solid #D7D7D7; padding-right: 10px;'},
                        DIV({}, "$call.file")
                    ),
                    TD({style:'border:1px solid #D7D7D7; text-align: right;'},
                        DIV({}, "$call.line")
                    ),
                    TD({style:'border:1px solid #D7D7D7; padding-left: 10px;'},
                        DIV({style:'font-weight: bold;'},  "$call|getCallLabel(",
                          FOR("arg", "$call|argIterator",
                              TAG("$arg.tag", {object: "$arg.value"}),
                              SPAN({class: "arrayComma", style:'font-weight: bold;'}, "$arg.delim")
                          ),
                          ")")
                    )
                  )
                )
              )
            )
          ),
    
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

});


function updatePHPException(logRow,spy)
{
  if (!logRow || !hasClass(logRow, "opened")) {
    return;
  }
  var template = FirePHP.PHPExceptionBody;
  var netInfoBox = getChildByClass(logRow, "spyHead", "netInfoBody");
  if (!netInfoBox)
  {
      var head = getChildByClass(logRow, "spyHead");
      netInfoBox = template.tag.append({'object':spy}, head);
      netInfoBox.style.marginTop = '2px';
      netInfoBox.style.paddingBottom = '6px';
  }
} 




/*
 * Monitor all requests so we can parse the response header data.
 */


function FirePHPProgress(context)
{
    this.context = context;
}



FirePHPProgress.prototype =
{
  
    // * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
    // nsISupports

    QueryInterface: function(iid)
    {
        if (iid.equals(nsIWebProgressListener)
            || iid.equals(nsISupportsWeakReference)
            || iid.equals(nsISupports))
        {
            return this;
        }

        throw Components.results.NS_NOINTERFACE;
    },

    // * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
    // nsIObserver


    observe: function(request, topic, data)
    {
        request = QI(request, nsIHttpChannel);

        if(this.context==Firebug.FirePHP.activeContext &&
           FirebugChrome.isFocused()) {
        
          Firebug.FirePHP.processRequest(request);
        }
    },

    // * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
    // nsIWebProgressListener

    onStateChange: function(progress, request, flag, status)
    {
        if (flag & STATE_TRANSFERRING && flag & STATE_IS_DOCUMENT)
        {
//						dump('FILE 3: '+request+"\n");
        }
        else if (flag & STATE_STOP && flag & STATE_IS_REQUEST)
        {
//						dump('FILE 4: '+request+"\n");
        }
    },

    onProgressChange : function(progress, request, current, max, total, maxTotal)
    {
//			dump('FILE 5: '+request+"\n");
    },

    stateIsRequest: false,
    onLocationChange: function() {},
    onStatusChange : function() {},
    onSecurityChange : function() {},
    onLinkIconAvailable : function() {}
};



function monitorContext(context)
{
    if (!context.firephpProgress)
    {

//FirePHPLib.dump(context,'context',null,true);


        var listener = context.firephpProgress = new FirePHPProgress(context);

//        context.browser.addProgressListener(listener, NOTIFY_ALL);
//        observerService.addObserver(listener, "http-on-modify-request", false);

        observerService.addObserver(listener, "http-on-examine-response", false);
    }
}

function unmonitorContext(context)
{
    if (context.firephpProgress)
    {

//        if (context.browser.docShell)
//            context.browser.removeProgressListener(context.firephpProgress, NOTIFY_ALL);

//        observerService.removeObserver(context.firephpProgress, "http-on-modify-request", false);
        observerService.removeObserver(context.firephpProgress, "http-on-examine-response", false);

        delete context.firephpProgress;
    }
}

Firebug.registerModule(Firebug.FirePHP);

}});
    
