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



FirePHP.FirePHPRequestData = function FirePHPRequestData() {

  this.applicationID = null;
  this.requestID = null;
  this.windowName = null;
  this.url = null;
  this.serverVars = null;
  this.firephpMultipartData = null;
  this.anchor = null;
  this.variables = new Array();

  this.setApplicationID = function(ApplicationID) {
    this.applicationID = ApplicationID;
  }
  this.getApplicationID = function() {
    return this.applicationID;
  }
  this.getApplicationData = function() {
    return FirePHP.FirePHPApplicationHandler.getDataByID(this.applicationID);
  }
  this.setRequestID = function(RequestID) {
    this.requestID = RequestID;
  }
  this.setData = function(Data) {
    this.firephpMultipartData = Data;
  }
  this.getData = function() {
    return this.firephpMultipartData;
  }
  this.setAnchor = function(Anchor) {
    this.anchor = Anchor;
  }
  this.setVariable = function(Key, ID, Scope, Label, Options, Value ) {
    if(!this.variables[Key]) {
      this.variables[Key] = new Array();
    }
    var data = this.variables[Key][this.variables[Key].length] = new Array(Key, ID, Scope, Label, Options, Value);

    if(Scope=='SESSION') {
      FirePHP.FirePHPSessionHandler.setVariable(this.applicationID,this,data);
    }
  }
  this.getVariables = function(Key) {
    if(Key) {
      return this.variables[Key];
    } else {
      return this.variables;
    }
  }
  this.getAnchorLabel = function() {
    return this.anchor;
  }
  this.getFrameName = function() {
    return this.windowName;
  }
  this.getApplicationLabel = function() {
    try {
      return this.getApplicationData().getVar('Application','Label');
    } catch(err) {}
    return 'ID: '+this.applicationID;
  }
  this.getDisplayURL = function() {
    try {
      return this.url.substring(this.getApplicationData().getURL().length);
    } catch(err) {}
    return this.url;
  }

};



FirePHP.FirePHPRequestHandler = {

  data: new Array(),
  windowDataMap: new Array(),
  
  initialize: function() {
    
    /* Register a content lister for the text/firephp documents */

    Components.classes["@mozilla.org/uriloader;1"].getService(Components.interfaces.nsIURILoader)
        .registerContentListener(FirePHP.FirePHPRequestContentListener);
  },
  
  getData: function(RequestID) {
    if(!RequestID || !this.data[RequestID]) return null;
    return this.data[RequestID];
  },
  
  /* Get the latest request for the specified window */
  getDataForWindow: function(WindowName) {
    if(!WindowName || !this.windowDataMap[WindowName]) return null;
    return this.windowDataMap[WindowName];
  },
  

  
  anchorRequest: function(RequestID,AnchorName) {

    var requestData = this.getData(RequestID);
    if(!requestData) return;

    /* Only anchor the request once window name and anchor are defined */
    if(AnchorName==null) return;
    
    requestData.setAnchor(AnchorName);

    if(requestData.windowName==null) return;
    
    if(!this.windowDataMap[requestData.windowName]) {
      this.windowDataMap[requestData.windowName] = new Array();
    }

//dump(' -- SET ANCHOR: '+requestData.windowName+' - '+requestData.anchor+"\n");

    this.windowDataMap[requestData.windowName][requestData.anchor] = requestData;
  },

  
  setRequestData: function(RequestID,WindowName,URL,ServerVars) {

//dump('setRequestData: '+RequestID+"\n");

    var requestData = this.getData(RequestID);
    if(!requestData) {
      requestData = this.data[RequestID] = new FirePHP.FirePHPRequestData();
    }

    requestData.requestID = RequestID;
    requestData.windowName = WindowName;
    requestData.url = URL;
    requestData.serverVars = ServerVars;

    /* If the anchor has been set lets anchor the request.
     * If not the backend data fetch will anchor it once it has been determined
     */
    if(requestData.anchor!=null) {
      this.anchorRequest(RequestID,requestData.anchor);
    }

		/* If we found a X-PINF-org.firephp-CapabilitiesURL header lets try and load the definition */
		if(ServerVars['CapabilitiesURL']) {
			new FirePHPChannelEvent('Capabilities','LoadDefinition',{CapabilitiesURL:ServerVars['CapabilitiesURL']}).trigger();
		}

    /* If we found FirePHP multipart data lets trigger a capabilities detection */
    if(requestData.getData()) {
      FirePHP.FirePHPApplicationHandler.triggerDetect(requestData.url);
    }

    /* Notify our chrome handler to refresh its UI */
    FirePHPChrome.refreshUI(this);
  },
  
  
  

  uniqueWindowIndex: 0,
  
  
    /* A utility function that keeps a unique index used to assign
     * window names for windows that do not have names set
     */
  fetchNewUniqueWindowIndex: function() {
    this.uniqueWindowIndex = this.uniqueWindowIndex + 1;
    return this.uniqueWindowIndex;
  },
  
  
  
  /* React to state changes in the main sidebar frame */
  onStateChange: function(aProgress, aRequest, aFlag, aStatus) { 

    if( (aFlag & Components.interfaces.nsIWebProgressListener.STATE_STOP) &&
        (aFlag & Components.interfaces.nsIWebProgressListener.STATE_IS_REQUEST)) {

//dump('aProgress.DOMWindow.location.href: '+aProgress.DOMWindow.location.href+' aRequest.name: '+aRequest.name+"\n");
  
      /* Check if we have a window name set.
       * A window name is typically not set if we are in the top window or tab window
       */
      if(!aProgress.DOMWindow.name) {
  
        /* Lets give the window a name so we can reference it correctly in future
         * even when the tab list changes or new internal frames are loaded
         */
        aProgress.DOMWindow.name = 'FirePHP-Window-'+this.fetchNewUniqueWindowIndex();
      }
  
      
      /* Some utility code to help trace events fired
        var isRequest = (aFlag & Components.interfaces.nsIWebProgressListener.STATE_IS_REQUEST)?'Request':'';
        var isDocument = (aFlag & Components.interfaces.nsIWebProgressListener.STATE_IS_DOCUMENT)?'Document':'';
        var isNetwork = (aFlag & Components.interfaces.nsIWebProgressListener.STATE_IS_NETWORK)?'Network':'';
        var isWindow = (aFlag & Components.interfaces.nsIWebProgressListener.STATE_IS_WINDOW)?'Window':'';
        Firebug.FirePHP.printLine("aFlag: "+aFlag+' - '+isRequest+' - '+isDocument+' - '+isNetwork+' - '+isWindow+' - ');
       */
  
  
  
      /* Check through the response headers to find any PINF-org.firephp-*
       * headers sent by the FirePHPServer
       */
      var serverVars = new Array();
      
      try {
        var http = aRequest.QueryInterface(Components.interfaces.nsIHttpChannel);
        http.visitResponseHeaders({
          visitHeader: function(name, value) {
            if(name.substring(0,19)=='X-PINF-org.firephp-') {
              serverVars[name.substring(19)] = value;
            }
          }
        });
      } catch(err) {}
      
      
      /* Ensure that at least the RequestID header/variable is set
       */

  
      if(serverVars['RequestID']) {
//FirePHPLib.dump(serverVars,'serverVars - '+aProgress.DOMWindow.name+' - '+aRequest.QueryInterface(Components.interfaces.nsIChannel).URI.spec,false,true);


//FirePHPLib.dump(aRequest,'aRequest - '+aRequest.QueryInterface(Components.interfaces.nsIChannel).URI.spec,false,true);

        /* Now that we have determined the RequestID from the server
         * set it for the corect windowContext/name so we can make it available
         * in the inspector panel
         */
        
        this.setRequestData(serverVars['RequestID'],
                            aProgress.DOMWindow.name,
                            aRequest.QueryInterface(Components.interfaces.nsIChannel).URI.spec,
                            serverVars);
      }
    }

    return 0;
  },
  onLocationChange: function(aProgress, aRequest, aURI) { return; },
  onProgressChange: function() { return 0; },
  onStatusChange: function() { return 0; },
  onSecurityChange: function() { return 0; },
  onLinkIconAvailable: function() { return 0; },
  QueryInterface: function(aIID) {
   if (aIID.equals(Components.interfaces.nsIWebProgressListener) ||
       aIID.equals(Components.interfaces.nsISupportsWeakReference) ||
       aIID.equals(Components.interfaces.nsISupports))
     return this;
   throw Components.results.NS_NOINTERFACE;
  }  
}





FirePHP.FirePHPRequestContentListener = {

  loadCookie: null,
  parentContentListener: null,
  stream: null,
  data: null,

  QueryInterface: function(iid) {
    if (iid.equals(Components.interfaces.nsIURIContentListener) ||
        iid.equals(Components.interfaces.nsISupportsWeakReference) ||
        iid.equals(Components.interfaces.nsISupports) ||
        iid.equals(Components.interfaces.nsIStreamListener))
        return this;
      throw Components.results.NS_NOINTERFACE;
  },
  onStartRequest: function(Request, Context){
    this.stream = Components.classes["@mozilla.org/scriptableinputstream;1"].createInstance().QueryInterface(Components.interfaces.nsIScriptableInputStream);
  },
  onStopRequest: function(Request, Context, Status) {

/* TODO: At the moment the data for CSS requests is not properly set */

//dump('    DATA FOR URI - '+Request.QueryInterface(Components.interfaces.nsIChannel).URI.spec+"\n");                


    /* Now that we have all data collected lets set it for the corresponding request object */    
    try {
      
      if(this.data && this.data.substring(0,9)=='<firephp ') {
        var parser = new DOMParser();
        var doc = parser.parseFromString(this.data, "text/xml");
        if(doc) {
        
          var applicationID = null;
          var requestID = null;
          var anchor = null;
        
          var findPattern = "//firephp[attribute::version=\"0.2\"]/application";
          var nodes = document.evaluate( findPattern, doc, null, XPathResult.ORDERED_NODE_ITERATOR_TYPE, null ); 
          if(nodes) {
            while (res = nodes.iterateNext()) {
              applicationID = res.getAttribute('id');
            }
          }
          findPattern = "//firephp[attribute::version=\"0.2\"]/application[attribute::id=\""+applicationID+"\"]/request";
          nodes = document.evaluate( findPattern, doc, null, XPathResult.ORDERED_NODE_ITERATOR_TYPE, null ); 
          if(nodes) {
            while (res = nodes.iterateNext()) {
              requestID = res.getAttribute('id');
              anchor = res.getAttribute('anchor');
              if(!anchor) anchor = '';
            }
          }
          if(requestID) {

            var requestData = FirePHP.FirePHPRequestHandler.getData(requestID);
            /* Create the request data object if we dont already have one for this request */
            if(!requestData) {
              requestData = FirePHP.FirePHPRequestHandler.data[requestID] = new FirePHP.FirePHPRequestData();
              requestData.setApplicationID(applicationID);
              requestData.setRequestID(requestID);
            }
        
            var findPattern = "//firephp[attribute::version=\"0.2\"]/application[attribute::id=\""+applicationID+"\"]/request[attribute::id=\""+requestID+"\"]/data[attribute::type=\"html\"]";
            var node = document.evaluate( findPattern, doc, null, XPathResult.FIRST_ORDERED_NODE_TYPE , null ); 
            if(node) {
              if(node.singleNodeValue.textContent) {
                requestData.setData(node.singleNodeValue.textContent);
              }
            }
            
            findPattern = "//firephp[attribute::version=\"0.2\"]/application[attribute::id=\""+applicationID+"\"]/request[attribute::id=\""+requestID+"\"]/variable";
            nodes = document.evaluate( findPattern, doc, null, XPathResult.ORDERED_NODE_ITERATOR_TYPE , null ); 
            if(nodes) {
              while (res = nodes.iterateNext()) {
                requestData.setVariable(res.getAttribute('key'),
                                        res.getAttribute('id'),
                                        res.getAttribute('scope'),
                                        res.getAttribute('label'),
                                        res.getAttribute('options'),
                                        res.textContent);
              }
            }

            /* Target the request to the correct spot in the inspector */
            FirePHP.FirePHPRequestHandler.anchorRequest(requestID,anchor);
          }
        }
      }
    } catch(err) {}

    this.data = null;
    this.stream = null;
  },
  onDataAvailable: function (Request, Context, InputStream, Offset, Count) {
    this.stream.init(InputStream);
    this.data = '';
    try {
     while (true) {
       var chunk = this.stream.read(512);
       if (chunk.length == 0) break;
       this.data = this.data + chunk;
     }
   } catch (err) {}
  },

  onStartURIOpen: function(uri) { return false; },
  doContent: function(contentType, isContentPreferred, request, contentHandler) {
    contentHandler.value = this;
    return false;
  },
  isPreferred: function(contentType, desiredContentType) {
    /* Lets handle the text/firephp content */
    if(contentType=='text/firephp') return true;
    return false;
  },
  canHandleContent: function(contentType, isContentPreferred, desiredContentType) {
    /* Lets handle the text/firephp content */
    if(contentType=='text/firephp') return true;
    return false;
  },
  GetWeakReference: function() { return this; }
}
