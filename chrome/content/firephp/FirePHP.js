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

const FB_NEW = (Firebug.version == '1.2')?true:false;

const Cc = Components.classes;
const Ci = Components.interfaces;

const nsIPrefBranch2 = (FB_NEW)?Ci.nsIPrefBranch2:FirebugLib.CI("nsIPrefBranch2");
const nsIPermissionManager = (FB_NEW)?Ci.nsIPermissionManager:FirebugLib.CI("nsIPermissionManager");

const PrefService = (FB_NEW)?Cc["@mozilla.org/preferences-service;1"]:FirebugLib.CC("@mozilla.org/preferences-service;1");
const PermManager = (FB_NEW)?Cc["@mozilla.org/permissionmanager;1"]:FirebugLib.CC("@mozilla.org/permissionmanager;1");

const nsIHttpChannel = (FB_NEW)?Ci.nsIHttpChannel:CI("nsIHttpChannel");
const nsIWebProgress = (FB_NEW)?Ci.nsIWebProgress:CI("nsIWebProgress");
const nsIWebProgressListener = (FB_NEW)?Ci.nsIWebProgressListener:CI("nsIWebProgressListener");
const nsISupportsWeakReference = (FB_NEW)?Ci.nsISupportsWeakReference:CI("nsISupportsWeakReference");
const nsISupports = (FB_NEW)?Ci.nsISupport:CI("nsISupports");
  
const ioService = (FB_NEW)?Ci.nsIIOService:FirebugLib.CCSV("@mozilla.org/network/io-service;1", "nsIIOService");

const observerService = CCSV("@mozilla.org/observer-service;1", "nsIObserverService");


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
//      Components.classes['@firephp.org/service;1'].getService(Components.interfaces.nsIFirePHP).setExtensionVersion(this.version);
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
      this.observerService.addObserver(this, "http-on-modify-request", false);
//      Components.classes['@firephp.org/service;1'].getService(Components.interfaces.nsIFirePHP).setRequestHeaderEnabled(true);
    } catch (err) {}
  },
  disable: function() {
dump('FirePHP.disable()'+"\n");    
    /* Enable the FirePHP Service Component to set the multipart/firephp accept header */  
    try {
      this.observerService.removeObserver(this, "http-on-modify-request");
//      Components.classes['@firephp.org/service;1'].getService(Components.interfaces.nsIFirePHP).setRequestHeaderEnabled(false);
    } catch (err) {}
  },
	
  get observerService() {
    return Components.classes["@mozilla.org/observer-service;1"]
                     .getService(Components.interfaces.nsIObserverService);
  },
  
  observe: function(subject, topic, data)
  {
    if (topic == "http-on-modify-request") {
      var httpChannel = subject.QueryInterface(Components.interfaces.nsIHttpChannel);

      /* Add FirePHP/X.X.X to User-Agent header if not already there */

      if(httpChannel.getRequestHeader("User-Agent").match(/\sFirePHP\/([\.|\d]*)\s?/)==null) {
        httpChannel.setRequestHeader("User-Agent", 
          httpChannel.getRequestHeader("User-Agent") + ' ' +
          "FirePHP/" + this.version, false);
      }
    }
  },  
	
  openPermissions: function()
  {
      var params = {
          permissionType: "firephp",
          windowTitle: "FirePHP Allowed Sites",
          introText: "Choose which web sites are allowed to be used with FirePHP.",
          blockVisible: true, sessionVisible: false, allowVisible: true, prefilledHost: ""
      };

    openWindow("Browser:Permissions", "chrome://browser/content/preferences/permissions.xul",'', params);
  },
	
  isURIAllowed: function(host)
  {
    if(!host) return false;
    var uri = ioService.newURI('http://'+host, null, null);
    return uri && 
        (pm.testPermission(uri, "firephp") == nsIPermissionManager.ALLOW_ACTION);
  },

  enableSite: function(host)
  {
    var uri = ioService.newURI('http://'+host, null, null);
    pm.add(uri, "firephp", nsIPermissionManager.ALLOW_ACTION);
  },
  
  
  parseHeaders: function(headers_in,parser) {
    
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
  
  requestBuffer: [],
  
  enable: function()
  {
dump('Firebug.FirePHP.enable()'+"\n");    
    this.requestBuffer = [];
		FirePHP.enable();
  },
  
  disable: function()
  {
dump('Firebug.FirePHP.disable()'+"\n");    
		FirePHP.disable();
    this.requestBuffer = [];
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
dump('Firebug.FirePHP.reattachContext'+"\n");

    this.addStylesheets(true);
  },
  
  watchWindow: function(context, win)
  {
dump('Firebug.FirePHP.watchWindow()'+"\n");    

    this.processRequestQue();
  },
  unwatchWindow: function(context, win)
  {
dump('Firebug.FirePHP.unwatchWindow()'+"\n");    
  },
  showPanel: function(browser, panel)
  {
dump('Firebug.FirePHP.showPanel()'+"\n");    

    this.addStylesheets();
  },
  
  addStylesheets: function(Force) {
    
    if(!Force) Force = false;
 
    /* Add any stylesheets if not added yet */
    try {
      if(this.activeContext && this.FirePHPProcessor) {
    
      var panel = this.activeContext.getPanel('console');
      if(panel) {

          if(!panel.customStylesheets) {
            panel.customStylesheets = [];
          }
          
        for( var url in this.FirePHPProcessor.consoleStylesheets ) {
            if(!panel.customStylesheets[url] || Force==true) {
            var doc = panel.document;
            addStyleSheet(doc, createStyleSheet(doc, url));
              panel.customStylesheets[url] = true;
          }
        }
      }
    } 
    } catch(e) {}    

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
	 
   
	queRequest: function(Request) {
		var http = QI(Request, nsIHttpChannel);
    var info = FirePHP.parseHeaders(http,'visit');
    this.requestBuffer.push([Request.name,info]);
  },

	processRequest: function(Request) {

		var url = Request.name;
		var http = QI(Request, nsIHttpChannel);
    var info = FirePHP.parseHeaders(http,'visit');
    
    this._processRequest(url,info);
  },
   
   
	processRequestQue: function(Request) {
    if(!this.requestBuffer) return;
  
    for( var index in this.requestBuffer ) {
      this._processRequest(this.requestBuffer[index][0],this.requestBuffer[index][1]);
    }
    this.requestBuffer = [];
  }, 
	


	_processRequest: function(url,info) {

    var mask = info['processorurl'];
    var data = info['data'];

		var domain = getDomain(mask);
    
		if(data) {
			if(!mask) {
				mask = 'chrome://firephp/content/RequestProcessor.js';
			} else {
  			if(!FirePHP.isURIAllowed(domain)) {
          this.logWarning('By default FirePHP is not allowed to load your custom processor from "'+mask+'". You can allow this by going to the "Net" panel and clicking on the "Server" tab for this request.');
  				mask = 'chrome://firephp/content/RequestProcessor.js';
        }
      }
			
			if(!this.FirePHPProcessor) {
				this.FirePHPProcessor = function() {
					return {
					  initialized: false,
            consoleStylesheets: [],
            consoleTemplates: [],
            sourceURL: null,
						_Init: function() {
							if(this.initialized) return;
              try {
  							this.Init();
  							this.initialized = true;
              } catch(e) {
              }           
						},
						Init : function() {
						},
						ProcessRequest: function() {
						},
            RegisterConsoleStyleSheet: function(URL) {
              this.consoleStylesheets[URL] = true;
            },
            RegisterConsoleTemplate: function(Name,Template) {
              this.consoleTemplates[Name] = Template;
            },
						logToFirebug: function(TemplateName, Data) {
              if (this.consoleTemplates[TemplateName]) {
                return Firebug.Console.logRow(function(object, row, rep)
                  {
                    return rep.tag.append({object: object}, row);
                  }, Data, this.activeContext, this.consoleTemplates[TemplateName].className, this.consoleTemplates[TemplateName], null, true);
              } else {
            	  return Firebug.Console.logFormatted([Data], this.activeContext, TemplateName, false, null);
              }
						}
					}
				}();
			}
			
			var proecessor_context = {FirePHPProcessor: this.FirePHPProcessor,
										 Firebug: Firebug,
										 data: data,
										 context: this.activeContext,
										 url: url};

      /* Check if the processor to be loaded is the current processor.
       * If it is we do not re-load the processor
       */
      
      if (this.FirePHPProcessor.sourceURL == mask &&
          this.FirePHPProcessor.initialized) {

        with (proecessor_context) {
          FirePHPProcessor.data = data;
          FirePHPProcessor.context = proecessor_context.context;

          try {
            eval(FirePHPProcessor.code);
            
            FirePHPProcessor.ProcessRequest(url,data);
          } 
          catch (e) {
            Firebug.FirePHP.logWarning(['Error executing custom FirePHP processor!', e]);
          }
        }
        
      } else {

			jQuery.ajax({
          type: 'GET',
          //				url: mask+'?t='+(new Date().getTime()),
          url: mask,
				success: function(ReturnData){
					with (proecessor_context) {							
              FirePHPProcessor.sourceURL = mask;
						FirePHPProcessor.data = data;
						FirePHPProcessor.context = proecessor_context.context;
              FirePHPProcessor.code = ReturnData;

            try {
  						eval(ReturnData);
              
  						FirePHPProcessor._Init();
                FirePHPProcessor.ProcessRequest(url,data);
              } 
              catch (e) {
              Firebug.FirePHP.logWarning(['Error executing custom FirePHP processor!',e]);  
            }
					}	
		
				},
				error: function(XMLHttpRequest, textStatus, errorThrown){
					if(mask.substr(0,9)=='chrome://') {

						with (proecessor_context) {
                FirePHPProcessor.sourceURL = mask;
							FirePHPProcessor.data = data;
							FirePHPProcessor.context = proecessor_context.context;
                FirePHPProcessor.code = XMLHttpRequest.responseText;

                try {
							eval(XMLHttpRequest.responseText);
						
							FirePHPProcessor._Init();
                  FirePHPProcessor.ProcessRequest(url,data);
                } 
                catch (e) {
                  Firebug.FirePHP.logWarning(['Error executing default FirePHP processor!', e]);
                }
						}	

            }
            else {
						
						this.logWarning('Error loading processor from: '+mask);
						
					}
				}
			});		
		}
		}
		
	},
  
  logWarning: function(args)
  {
	  return Firebug.Console.logFormatted(args, this.activeContext, 'warn', false, null);
  }
    		   
});





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
          var win = progress.DOMWindow;
          if (win == win.parent) {
            if(this.context==Firebug.FirePHP.activeContext &&
                FirebugChrome.isFocused()) {

              Firebug.FirePHP.queRequest(request);
            }
          }
        }
        else if (flag & STATE_STOP && flag & STATE_IS_REQUEST)
        {
//						dump('FILE 4: '+request+' - '+progress.isLoadingDocument+"\n");
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

        context.browser.addProgressListener(listener, NOTIFY_ALL);
//        observerService.addObserver(listener, "http-on-modify-request", false);

        observerService.addObserver(listener, "http-on-examine-response", false);
    }
}

function unmonitorContext(context)
{
    if (context.firephpProgress)
    {

        if (context.browser.docShell)
            context.browser.removeProgressListener(context.firephpProgress, NOTIFY_ALL);

//        observerService.removeObserver(context.firephpProgress, "http-on-modify-request", false);
        observerService.removeObserver(context.firephpProgress, "http-on-examine-response", false);

        delete context.firephpProgress;
    }
}

Firebug.registerModule(Firebug.FirePHP);

}});
    
