
FBL.ns(function() { with (FBL) {

// ************************************************************************************************



function FirePHPProgressListener() {}

/* React to state changes in the main sidebar frame */
FirePHPProgressListener.prototype.onStateChange = function(aProgress, aRequest, aFlag, aStatus) { 

  if( (aFlag & Components.interfaces.nsIWebProgressListener.STATE_STOP) &&
      (aFlag & Components.interfaces.nsIWebProgressListener.STATE_IS_DOCUMENT)) {

    /* Check if we have a window name set.
     * A window name is typically not set if we are in the top window or tab window
     */
    if(!aProgress.DOMWindow.name) {

      /* Lets give the window a name so we can reference it correctly in future
       * even when the tab list changes or new internal frames are loaded
       */
      aProgress.DOMWindow.name = 'FirePHP-Window-'+Firebug.FirePHP.fetchNewUniqueWindowIndex();
    }

    
    /* Some utility code to help trace events fired
      var isRequest = (aFlag & Components.interfaces.nsIWebProgressListener.STATE_IS_REQUEST)?'Request':'';
      var isDocument = (aFlag & Components.interfaces.nsIWebProgressListener.STATE_IS_DOCUMENT)?'Document':'';
      var isNetwork = (aFlag & Components.interfaces.nsIWebProgressListener.STATE_IS_NETWORK)?'Network':'';
      var isWindow = (aFlag & Components.interfaces.nsIWebProgressListener.STATE_IS_WINDOW)?'Window':'';
      Firebug.FirePHP.printLine("aFlag: "+aFlag+' - '+isRequest+' - '+isDocument+' - '+isNetwork+' - '+isWindow+' - ');
     */



    /* Check through the response headers to find any PINF-com.googlecode.firephp-*
     * headers sent by the FirePHPServer
     */
    var serverVars = new Array();
     
    var http = FirebugLib.QI(aRequest, Components.interfaces.nsIHttpChannel);
    http.visitResponseHeaders({
      visitHeader: function(name, value) {
        if(name.substring(0,28)=='PINF-com.googlecode.firephp-') {
          serverVars[name.substring(28)] = value;
        }
      }
    });
    
    
    /* Ensure that at least the RequestID header/variable is set
     */

    if(serverVars['RequestID']) {
      
      /* Now that we have determined the RequestID from the server
       * set it for the corect windowContext/name so we can make it available
       * in the inspector panel
       */
  
      Firebug.FirePHP.updateWindowContext(aProgress.DOMWindow.name,
                                          serverVars['RequestID'],
                                          aProgress.DOMWindow.location.href,
                                          serverVars);
    }
  }

  return 0;
},
FirePHPProgressListener.prototype.onLocationChange = function(aProgress, aRequest, aURI) { return; },
FirePHPProgressListener.prototype.onProgressChange = function() { return 0; },
FirePHPProgressListener.prototype.onStatusChange = function() { return 0; },
FirePHPProgressListener.prototype.onSecurityChange = function() { return 0; },
FirePHPProgressListener.prototype.onLinkIconAvailable = function() { return 0; },
FirePHPProgressListener.prototype.QueryInterface = function(aIID) {
 if (aIID.equals(Components.interfaces.nsIWebProgressListener) ||
     aIID.equals(Components.interfaces.nsISupportsWeakReference) ||
     aIID.equals(Components.interfaces.nsISupports))
   return this;
 throw Components.results.NS_NOINTERFACE;
}


var FirePHPProgressListenerObject = new FirePHPProgressListener();




function FirePHPServerContext() {
}
FirePHPServerContext.prototype =
{
  domain: null,
  detectStatus: null    /* 0 => Detection in progress, 1 => Server Detected, -1 => Server not Detected */
};


function FirePHPWindowContext() {
}
FirePHPWindowContext.prototype =
{
  name: null,
  requestID: null,
  url: null,
  serverVars: null
};




Firebug.FirePHP = extend(Firebug.Module,
{

    serverContext: new Array(),
    windowContext: new Array(),
    
    uniqueWindowIndex: 0,
    
    


    // * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
    // extends Module


    /* Called when firefox (the chrome specifically) is first loaded.
     * This happens when firefox first starts up or the chrome is reloaded.
     */
    initialize: function()
    {
//      alert('initialize');
    },
    /* Called when firefox (the chrome specifically) is destroyed
     * This happens when firefox is closed or the chrome is reloaded.
     */
    shutdown: function()
    {
//      alert('shutdown');
    },
    /* Called when a page is loaded into the browser (or a new tab).
     * Will only be called once per URL loaded into a tab/window.
     * Will not fire for each page loaded into iframes or framesets
     * contained within the parent URL.
     */
    initContext: function(context)
    {
//      alert('initContext');
      /* Add a listener to the browser so we can monitor all window/frame/document loading states
       */
      context.browser.addProgressListener(FirePHPProgressListenerObject,Components.interfaces.nsIWebProgress.NOTIFY_DOCUMENT);
      context.browser.addProgressListener(FirePHPProgressListenerObject,Components.interfaces.nsIWebProgress.NOTIFY_STATE_WINDOW);
    },
    reattachContext: function(context)
    {
//      alert('reattachContext');
    },
    /* Opposite of initContext called when a URL is unloaded prior
     * to loading a new URL.
     * Also called when the chrome is reloaded or if firefox is closed.
     */
    destroyContext: function(context, persistedState)
    {
      /* Remove the listener we attached to do proper cleanup
       */
      context.browser.removeProgressListener(FirePHPProgressListenerObject);
//      alert('destroyContext');
    },
    watchWindow: function(context, win)
    {
//      alert('watchWindow');
//      alert('watchWindow: '+win.location.href);
//      this.attachToWindow(win);
    },
    unwatchWindow: function(context, win)
    {
//      alert('unwatchWindow: '+win.location.href);
//      this.unattachFromWindow(win);
    },
    showContext: function(browser, context)
    {
//      alert('showContext');
    },
    loadedContext: function(context)
    {
//      alert('loadedContext');
    },
    showPanel: function(browser, panel)
    {
//      alert('showPanel');

        var isFirePHP = panel && panel.name == "FirePHP";
        var FirePHPButtons = browser.chrome.$("fbFirePHPButtons");
        collapse(FirePHPButtons, !isFirePHP);
    },
    showSidePanel: function(browser, panel)
    {
//      alert('showSidePanel');
    },



    // * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
    // Internal



    attachToWindow: function(win) {
    },    

    unattachFromWindow: function(win) {
    },    



        
    triggerMenuToggle: function(button) {
      switch(button) {
        case 'Info':

          this.printLine('Info Button Clicked!');

          break;
        case 'Variables':

          this.printLine('Variables Button Clicked!');

          break;
      }
    },
    
    
    printLine: function(Message) {
      var panel = FirebugContext.getPanel("FirePHP");
      panel.printLine(Message);
    },



    updateWindowContext: function(name,requestID,url,serverVars) {

      /* Check if we already have a context defined for the given window name
       * NOTE: For the windowContext to work properly every window/frame
               must have a unique name
       */
      
      var windowContext = null;
              
      if(!this.windowContext[name]) {
        this.windowContext[name] = windowContext = new FirePHPWindowContext();
      } else {
        windowContext = this.windowContext[name];
      }

      windowContext.name = name;
      windowContext.requestID = requestID;
      windowContext.url = url;
      windowContext.serverVars = serverVars;
      

      FirebugContext.getPanel("FirePHP").refreshContext();
    },
    
    
    getWindowContext: function(name) {
      if(this.windowContext[name]) return this.windowContext[name];
      return null;
    },

    triggerFirePHPServerDetect: function(context) {
    
      var href = context.window.location.href;
      if(href!='about:blank') {
        var domain = FirebugLib.getDomain(href);
        var serverContext = null;
        
        /* If we dont have a server context for this domain yet, create it */
        if(!this.serverContext[domain]) {
          this.serverContext[domain] = serverContext = new FirePHPServerContext();
          serverContext.domain = domain;
        } else {
          serverContext = this.serverContext[domain];
        }
        
        /* Check the serverStatus of the serverContext to see if we should trigger
         * a detect or ignore the request */
        if(serverContext.detectStatus==null) {
          /* No detection has been done for this serverContext yet.
           * Lets start a detect
           */
           
          serverContext.detectStatus = 0;

          var callback = {   
            success: function(o) {

              try {

                var serverContext = o.argument;

                if(o.responseXML) {
                  var findPattern = "//pinf/package[attribute::name=\"com.googlecode.firephp\"]/vargroup[attribute::name=\"Application\"]/var[attribute::name=\"App.Base.URL\"]/value";
                  var labelNode = document.evaluate( findPattern, o.responseXML, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null ); 
                  if(labelNode) {
                    
                    serverContext.detectStatus = 1;
                    
//alert('Server Rsponse: '+labelNode.singleNodeValue.textContent);
                  }
                }            
              } catch(e) {
              }
              
              /* Trigger an update for the panel to ensure the display is consistent with
               * the internal data
               */
              FirebugContext.getPanel("FirePHP").refreshContext();
              
            },
            failure: function(o) { 
              switch(o.status) {
                case 403:   /* Forbidden */
                  /* We were not allowed to read the detection URL on the server.
                   * We assume we do not have access to the FirePHPServer and
                   * will not try the detection again.
                   */
                  serverContext.detectStatus = -1;
                  break;
                case 404:   /* Not Found */
                  /* The detection URL was not found on the server.
                   * We assume no FirePHPServer is setup and will not try
                   * the detection again.
                   */
                  serverContext.detectStatus = -1;
                  break;
                default:
                  alert('Got unsupported response status ['+o.status+'] while trying to detect FirePHPServer!');
                  break;
              }
              
              /* Trigger an update for the panel to ensure the display is consistent with
               * the internal data
               */
              FirebugContext.getPanel("FirePHP").refreshContext();
              
            },
            argument: serverContext
          }   
      
          var url = 'http://'+domain+'/PINF/com.googlecode.firephp/Detect.xml';
      
          try {
            YAHOO.util.Connect.asyncRequest('GET', url, callback, null);
          } catch(e) {
            /* The detection request failed. Lets try again as the request should not fail here */
            alert('Error trying to detect FirePHPServer at ['+url+']. We will try again!');
            serverContext.detectStatus = null;
          }
           
        } else
        if(serverContext.detectStatus==0) {
          /* The server detect is already in progress.
           * This may happen if the detect is triggered multiple times for the same
           * domain in very short intervals.
           * So lets just ignore this request as the original request
           * should complete soon
           */
        }
      }
      
      /* Trigger an update for the panel to ensure the display is consistent with
       * the internal data
       */
      FirebugContext.getPanel("FirePHP").refreshContext();
    },


    getServerContext: function(context) {

      var href = context.window.location.href;
      if(href!='about:blank') {
        var domain = FirebugLib.getDomain(href);
        if(this.serverContext[domain]) return this.serverContext[domain];
      }
      
      return null;
    },

    
    /* A utility function that keeps a unique index used to assign
     * window names for windows that do not have names set
     */
    fetchNewUniqueWindowIndex: function() {
      this.uniqueWindowIndex = this.uniqueWindowIndex + 1;
      return this.uniqueWindowIndex;
    }
    
});

// ************************************************************************************************

function FirePHPPanel() {}

FirePHPPanel.prototype = extend(Firebug.Panel,
{
    
    // * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *    
    // extends Panel
    
    name: "FirePHP",
    title: "FirePHP",
    searchable: false,
    editable: false,

    /* Called whenever the panel is selected from the menu or
     * whenever a new URL is loaded into a tab/browser window.
     * Is not loaded when a new URL is loaded into an iframe
     * or frameset contained within the parent URL.
     */
    show: function(state)
    {
      /* Whenever the panel is shown (assumes user wants to use FirePHP)
       * detect if there is a FirePHPServer for the loaded URL
       */
      Firebug.FirePHP.triggerFirePHPServerDetect(this.context);
    },


    // * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *    
    // Internal

    printLine: function(message) {
        var elt = this.document.createElement("p");
        elt.innerHTML = message;
        this.panelNode.appendChild(elt);
    },

    
    /* Check context and ensure UI is consistent with serverContext */
    refreshContext: function() {
    
      /* Only do this if the panel is visible.
       * We can limit based on the visibility because this method will
       * be called again if the panel was hidden and changed to visible
       */
      if(!this.visible) return;

      /* Try and get the serverContext from the FirePHP module
       * Once we have it we can update all default components of the UI
       * More specific components of the UI are updated upon implied request
       * of the user as different info is navigated.
       */
      var serverContext = Firebug.FirePHP.getServerContext(this.context);

      if(serverContext!=null && serverContext.detectStatus==1) {
        /* A FirePHPServer is available for the given context, thus enable all tools */

        var FirePHPPanelMenuIcon = FirebugContext.chrome.$('FirePHPPanelMenuIcon');
        FirePHPPanelMenuIcon.className = 'firephp-panel-menu-icon-enabled';
      
      } else {
        /* A FirePHPServer is not available for the given context, thus disable all tools */

        var FirePHPPanelMenuIcon = FirebugContext.chrome.$('FirePHPPanelMenuIcon');
        FirePHPPanelMenuIcon.className = 'firephp-panel-menu-icon-disabled';
      
      }
      
      this.renderRequestTable();
      
      /* Fetch all window contexts and insert them into the table */
      for( var i=0 ; i<this.context.windows.length ; i++ ) {
        var windowContext = Firebug.FirePHP.getWindowContext(this.context.windows[i].name);
        if(windowContext) {
          this.insertRequestInfoRequestTable(windowContext.requestID,
                                             windowContext.name,
                                             windowContext.url,
                                             windowContext.serverVars);
        }
      }
    },
    
    
    renderRequestTable: function() {
      this.panelNode.innerHTML =  ''+
'<style>'+
  '#FirePHP-RequestTable TR TD {'+
    'border: 1px solid #ececec;'+
  '}'+
'</style>'+
'<table id="FirePHP-RequestTable" border="0" cellpadding="5" cellspacing="0">'+
  '<tr>'+
    '<td><b>Frame</b></td>'+
    '<td><b>URL</b></td>'+
    '<td><b>ServerVars</b></td>'+
  '</tr>'+
'</table>';
    },

    insertRequestInfoRequestTable: function(RequestID,FrameName,URL,ServerVars) {
      var requestTable = this.document.getElementById('FirePHP-RequestTable');

      var newRow = requestTable.insertRow(requestTable.rows.length)
      newRow.id = RequestID;

      var newCell = null;

      newCell = newRow.insertCell(0);
      newCell.innerHTML = FrameName;

      newCell = newRow.insertCell(1);
      newCell.innerHTML = URL;

      newCell = newRow.insertCell(2);
      
      var html = '';
      if(ServerVars) {
        for(var name in ServerVars) {
          if(name!='RequestID') {
            html = html + '<b>'+name+':</b>&nbsp;'+ServerVars[name]+'&nbsp;&nbsp;&nbsp; ';
          }
        }
      }
      newCell.innerHTML = html;
    }

});


// ************************************************************************************************

Firebug.registerModule(Firebug.FirePHP);
Firebug.registerPanel(FirePHPPanel);

// ************************************************************************************************

}});

