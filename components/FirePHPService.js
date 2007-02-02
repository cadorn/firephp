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


/* This code is inspired and adapted from http://modifyheaders.mozdev.org/ */


const nsIFirePHP = Components.interfaces.nsIFirePHP;
const nsISupports = Components.interfaces.nsISupports;

const FIREPHP_PROXY_CLASS_NAME = "FirePHP Proxy";
const FIREPHP_PROXY_CLASS_ID = Components.ID("{86526120-af28-11db-abbd-0800200c9a66}");
const FIREPHP_PROXY_CONTRACT_ID = "@firephp.org/proxy;1";

const FIREPHP_SERVICE_CLASS_NAME = "FirePHP Service";
const FIREPHP_SERVICE_CLASS_ID = Components.ID("{86526121-af28-11db-abbd-0800200c9a66}");
const FIREPHP_SERVICE_CONTRACT_ID = "@firephp.org/service;1";


/*
 * FirePHP Service
 */
function FirePHPService() {
  
  this.requestHeaderEnabled = false;
  
  this.responseData = new Array();

  // Observer service is used to notify observing FirePHPProxy objects that the headers have been updated
  this.observerService = Components.classes["@mozilla.org/observer-service;1"].getService(Components.interfaces.nsIObserverService);
}

FirePHPService.prototype = {

    setRequestHeaderEnabled: function(oo) {
      this.requestHeaderEnabled = oo;
      FirePHP_logMessage("FirePHPService.setHeaderEnabled: "+oo);
    },

    getRequestHeaderEnabled: function() {
      return this.requestHeaderEnabled;
    },

    setResponseData: function(RequestID,Data) {
      this.responseData[RequestID] = Data;
    },
    getRequestIDs: function() {
      if(!this.responseData) return;
      var ids = '';
      for( var key in this.responseData ) {
        if(ids) ids = ids + "|";
        ids = ids + key;
      }
      return ids;
    },
    popResponseData: function(RequestID) {

      FirePHP_logMessage(this.responseData,true);
    
      var data = null;
      var responseData = new Array();

      for( var key in this.responseData ) {
        if(key==RequestID) {
          data = this.responseData[key];
        } else {
          responseData[key] = this.responseData[key];
        }
      }
      
      this.responseData = responseData;

      FirePHP_logMessage(this.responseData,true);

      return data;
    }
}


/*
 * FirePHP Proxy
 */
function FirePHPProxy() {
    this.FirePHPService = Components.classes[FIREPHP_SERVICE_CONTRACT_ID].getService(Components.interfaces.nsIFirePHP);
}


// nsIObserver interface method
FirePHPProxy.prototype.observe = function(subject, topic, data) {
    FirePHP_logMessage("Entered FirePHPProxy.prototype.observe");

    if (topic == 'http-on-modify-request') {
        FirePHP_logMessage("topic is http-on-modify-request");

        subject.QueryInterface(Components.interfaces.nsIHttpChannel);
        
        if(this.FirePHPService.getRequestHeaderEnabled()) {
          subject.setRequestHeader("Accept", "text/firephp", true);
        }
    } else if (topic == 'app-startup') {
      FirePHP_logMessage("topic is app-startup");
        
      if ("nsINetModuleMgr" in Components.interfaces) {
          // Should be an old version of Mozilla (before september 15, 2003
          // Do Nothing as these old versions of firefox (firebird, phoenix etc) are not supported
      } else {
        // Should be a new version of  Mozilla (after september 15, 2003)
        var observerService = Components.classes["@mozilla.org/observer-service;1"].getService(Components.interfaces.nsIObserverService);
        observerService.addObserver(this, "http-on-modify-request", false);
      }
    } else {
       FirePHP_logMessage("No observable topic defined");
    }
    
    FirePHP_logMessage("Exiting FirePHPProxy.prototype.observe");
}

// nsISupports interface method
FirePHPProxy.prototype.QueryInterface = function(iid) {
    if (!iid.equals(nsIFirePHP) && !iid.equals(Components.interfaces.nsISupports)) {
        throw Components.results.NS_ERROR_NO_INTERFACE;
    }
    return this;
}





/*
 * Factory objects
 */
var FirePHPServiceFactory = new Object();
FirePHPServiceFactory.createInstance = function (outer, iid) {
    if (outer != null)
        throw Components.results.NS_ERROR_NO_AGGREGATION;
    if (iid.equals(Components.interfaces.nsIFirePHP)) {
        return new FirePHPService();
    }
    throw Components.results.NS_ERROR_NO_INTERFACE;
}
var FirePHPProxyFactory = new Object();
FirePHPProxyFactory.createInstance = function (outer, iid) {
    if (outer != null)
        throw Components.results.NS_ERROR_NO_AGGREGATION;
    if (iid.equals(Components.interfaces.nsIObserver)) {
        return new FirePHPProxy();
    }
    throw Components.results.NS_ERROR_NO_INTERFACE;
}



/* FirePHPModule is responsible for the registration of the component */
var FirePHPModule = new Object();

FirePHPModule.firstTime = true;

// Register the component with the browser
FirePHPModule.registerSelf = function (compMgr, fileSpec, location, type) {
    if (this.firstTime) {
        this.firstTime = false;
        throw Components.results.NS_ERROR_FACTORY_REGISTER_AGAIN;
    }

    var compMgr = compMgr.QueryInterface(Components.interfaces.nsIComponentRegistrar);

    // Register the service factory object
    compMgr.registerFactoryLocation(FIREPHP_SERVICE_CLASS_ID,
                                    FIREPHP_SERVICE_CLASS_NAME,
                                    FIREPHP_SERVICE_CONTRACT_ID, 
                                    fileSpec, location, type);

    // Register the proxy factory object
    compMgr.registerFactoryLocation(FIREPHP_PROXY_CLASS_ID,
                                    FIREPHP_PROXY_CLASS_NAME,
                                    FIREPHP_PROXY_CONTRACT_ID, 
                                    fileSpec, location, type);

    var catman = Components.classes["@mozilla.org/categorymanager;1"].getService(Components.interfaces.nsICategoryManager);
    catman.addCategoryEntry("app-startup",
                            FIREPHP_PROXY_CLASS_NAME,
                            FIREPHP_PROXY_CONTRACT_ID,
                            true, true);
                            
}

// Removes the component from the app-startup category
FirePHPModule.unregisterSelf = function(compMgr, fileSpec, location) {
    var catman = Components.classes["@mozilla.org/categorymanager;1"] .getService(Components.interfaces.nsICategoryManager);
    catMan.deleteCategoryEntry("app-startup", FIREPHP_PROXY_CONTRACT_ID, true);
}

// Return the Factory object
FirePHPModule.getClassObject = function (compMgr, cid, iid) {
    
    if (!iid.equals(Components.interfaces.nsIFactory))
        throw Components.results.NS_ERROR_NOT_IMPLEMENTED;

    
    // Check that the component ID is the FirePHP Proxy
    if (cid.equals(FIREPHP_PROXY_CLASS_ID)) {
      return FirePHPProxyFactory;
    } else if(cid.equals(FIREPHP_SERVICE_CLASS_ID)) {
      return FirePHPServiceFactory;
    }
    
    throw Components.results.NS_ERROR_NO_INTERFACE;
}

FirePHPModule.canUnload = function(compMgr) {
    return true;
}


/* Entrypoint - registers the component with the browser */
function NSGetModule(compMgr, fileSpec) {
    return FirePHPModule;
}


// A logger
var gConsoleService = Components.classes['@mozilla.org/consoleservice;1'].getService(Components.interfaces.nsIConsoleService);


function FirePHP_logMessage(aMessage,Expand) {

  return;

  dump('FirePHP: [' + aMessage+"]\n");
  
  if(Expand) {
    for( var nam in aMessage ) {
      dump(' '+nam+' : ['+aMessage[nam]+"]\n");
    }
  }
  
}

