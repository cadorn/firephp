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

  this.requestID = null;
  this.windowName = null;
  this.url = null;
  this.serverVars = null;
  this.firephpMultipartData = null;
  
  this.getData = function() {
    return this.firephpMultipartData;
  }

};



FirePHP.FirePHPRequestHandler = {

  data: new Array(),
  windowDataMap: new Array(),
  
  
  getData: function(RequestID) {
    if(!RequestID || !this.data[RequestID]) return null;
    return this.data[RequestID];
  },
  
  /* Get the latest request for the specified window */
  getDataForWindow: function(WindowName) {
    if(!WindowName || !this.windowDataMap[WindowName]) return null;
    return this.windowDataMap[WindowName];
  },
  

  
  setRequestData: function(RequestID,WindowName,URL,ServerVars) {
    
    var requestData = this.getData(RequestID);
    if(!requestData) {
      requestData = this.data[RequestID] = new FirePHP.FirePHPRequestData();
    }

    this.windowDataMap[WindowName] = requestData;

    requestData.requestID = RequestID;
    requestData.windowName = WindowName;
    requestData.url = URL;
    requestData.serverVars = ServerVars;

    /* Trigger a data fetch for any additional info for this rtequest */
    this.triggerDataFetch();

    /* If we found FirePHP multipart data lets trigger a capabilities detection */
    if(requestData.firephpMultipartData) {
      FirePHP.FirePHPApplicationHandler.triggerDetect(requestData.url);
    }

    /* Notify our chrome handler to refresh its UI */
    FirePHPChrome.refreshUI(this);
  },
  
  
  /* Connects to the FirePHP Component Service and tries to get any data that is available */  
  triggerDataFetch: function() {
    
    try {

      var component = Components.classes['@firephp.org/service;1'].getService(Components.interfaces.nsIFirePHP);

      var ids = component.getRequestIDs();
      if(ids) {
        ids = ids.split("|");
        if(ids.length>0) {
          var data, requestData;
          for( var i = 0 ; i < ids.length ; i++ ) {
            data = component.popResponseData(ids[i]);
            if(data) {
            
              requestData = this.getData(ids[i]);
              
              /* Create the request data object if we dont already have one for this request */
              if(!requestData) {
                requestData = this.data[ids[i]] = new FirePHP.FirePHPRequestData();
                requestData.requestID = ids[i];
              }

              requestData.firephpMultipartData = data;
            }
          }
        }
      }
    } catch (err) {}    
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
        (aFlag & Components.interfaces.nsIWebProgressListener.STATE_IS_DOCUMENT)) {
  
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
       
      var http = FirebugLib.QI(aRequest, Components.interfaces.nsIHttpChannel);
      http.visitResponseHeaders({
        visitHeader: function(name, value) {
          if(name.substring(0,17)=='PINF-org.firephp-') {
            serverVars[name.substring(17)] = value;
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

        this.setRequestData(serverVars['RequestID'],
                            aProgress.DOMWindow.name,
                            aProgress.DOMWindow.location.href,
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