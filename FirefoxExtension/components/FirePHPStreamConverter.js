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


/* This code is inspired and adapted from http://xhtmlmp.mozdev.org/ */


const FIREPHP_CONVERTER_CONVERSION = "?from=multipart/firephp&to=*/*";
const FIREPHP_CONVERTER_CLASS_ID = Components.ID("{0adc2f40-afc2-11db-abbd-0800200c9a66}");
const FIREPHP_CONVERTER_CONTRACT_ID = "@mozilla.org/streamconv;1"+FIREPHP_CONVERTER_CONVERSION;


/*
 * FirePHP Stream Converter
 */
function FirePHPStreamConverter() {
}

FirePHPStreamConverter.prototype.QueryInterface = function (iid) {
    if (iid.equals(Components.interfaces.nsISupports) ||
        iid.equals(Components.interfaces.nsIStreamConverter) ||
        iid.equals(Components.interfaces.nsIStreamListener) ||
        iid.equals(Components.interfaces.nsIRequestObserver))
        return this;
    throw Components.results.NS_ERROR_NO_INTERFACE;
}


// nsIRequest observer methods
FirePHPStreamConverter.prototype.onStartRequest = function (aRequest, aContext) {

    this.data = '';

    this.uri = aRequest.QueryInterface(Components.interfaces.nsIChannel).URI.spec;

    this.channel = aRequest;

    
    try {

      this.channel.contentType = aRequest.QueryInterface(Components.interfaces.nsIHttpChannel).getResponseHeader('PINF-org.firephp-PrimaryContentType');
      this.requestID = aRequest.QueryInterface(Components.interfaces.nsIHttpChannel).getResponseHeader('PINF-org.firephp-RequestID');

      this.multipartBoundary = aRequest.QueryInterface(Components.interfaces.nsIHttpChannel).
                                    getResponseHeader('Content-type').match(new RegExp('boundary="(.*)"'))[1];
  
    } catch(err) {

      this.channel.contentType = 'text/plain';

    }

    this.listener.onStartRequest(this.channel, aContext);
};

FirePHPStreamConverter.prototype.onStopRequest = function (aRequest, aContext, aStatusCode) {
  
  try {
  
    var contentParts = this.data.split(new RegExp('(--)?('+this.multipartBoundary+')(--)?','g'));
  
    if(contentParts.length==13 &&
       contentParts[1]=='--' &&
       contentParts[5]=='--' &&
       contentParts[9]=='--' &&
       contentParts[11]=='--' &&
       contentParts[2]==this.multipartBoundary &&
       contentParts[6]==this.multipartBoundary &&
       contentParts[10]==this.multipartBoundary) {
  
      this.data = TrimString(contentParts[4]);
  
      if(TrimString(contentParts[8]).substring(0,26)=='Content-type: text/firephp') {
        var firephp_data_section = TrimString(TrimString(contentParts[8]).substring(26));
        if(firephp_data_section) {
        
          Components.classes['@firephp.org/service;1'].getService(Components.interfaces.nsIFirePHP).
                setResponseData(this.requestID,firephp_data_section);
          
        }
      }
      
    } else {
      this.data = 'Unable to split "multipart/firephp" message!';
    }
  
  } catch(err) {
    this.data = 'Unable to split "multipart/firephp" message!';
  }
  

  var targetDocument = this.data;

  try {
    
    var sis = Components.classes["@mozilla.org/io/string-input-stream;1"].createInstance(Components.interfaces.nsIStringInputStream);
    sis.setData (targetDocument, targetDocument.length);

    // Pass the data to the main content listener
    this.listener.onDataAvailable (this.channel, aContext, sis, 0, targetDocument.length);

  } catch(err) {}

  this.listener.onStopRequest (this.channel, aContext, aStatusCode);
};

// nsIStreamListener methods
FirePHPStreamConverter.prototype.onDataAvailable = function (aRequest, aContext, aInputStream, aOffset, aCount) {

    var si = Components.classes["@mozilla.org/scriptableinputstream;1"].createInstance();
    si = si.QueryInterface(Components.interfaces.nsIScriptableInputStream);
    si.init(aInputStream);
    this.data += si.read(aCount);
}

// nsIStreamConverter methods
// old name (before bug 242184)...
FirePHPStreamConverter.prototype.AsyncConvertData = function (aFromType, aToType, aListener, aCtxt) {
    this.asyncConvertData (aFromType, aToType, aListener, aCtxt);
}

// renamed to...
FirePHPStreamConverter.prototype.asyncConvertData = function (aFromType, aToType, aListener, aCtxt) {
    // Store the listener passed to us
    this.listener = aListener;
}

// Old name (before bug 242184):
FirePHPStreamConverter.prototype.Convert = function (aFromStream, aFromType, aToType, aCtxt) {
    return this.convert (aFromStream, aFromType, aToType, aCtxt);
}

// renamed to...
FirePHPStreamConverter.prototype.convert = function (aFromStream, aFromType, aToType, aCtxt) {
    return aFromStream;
}






/*
 * Factory objects
 */
var FirePHPStreamConverterFactory = new Object();
FirePHPStreamConverterFactory.createInstance = function (outer, iid) {
    if (outer != null)
        throw Components.results.NS_ERROR_NO_AGGREGATION;

    if (iid.equals(Components.interfaces.nsISupports) ||
        iid.equals(Components.interfaces.nsIStreamConverter) ||
        iid.equals(Components.interfaces.nsIStreamListener) ||
        iid.equals(Components.interfaces.nsIRequestObserver)) {
        return new FirePHPStreamConverter();
    }
    throw Components.results.NS_ERROR_NO_INTERFACE;
}




/* FirePHPModule is responsible for the registration of the component */
var FirePHPModule = new Object();


// Register the component with the browser
FirePHPModule.registerSelf = function (compMgr, fileSpec, location, type) {

    var compMgr = compMgr.QueryInterface(Components.interfaces.nsIComponentRegistrar);

    // Register the stream converter factory object
    compMgr.registerFactoryLocation(FIREPHP_CONVERTER_CLASS_ID,
                                    "firephp Stream Converter",
                                    FIREPHP_CONVERTER_CONTRACT_ID, 
                                    fileSpec, location, type);

    var catman = Components.classes["@mozilla.org/categorymanager;1"].getService(Components.interfaces.nsICategoryManager);
                            
    catman.addCategoryEntry("@mozilla.org/streamconv;1",
                            FIREPHP_CONVERTER_CONVERSION,
                            "firephp Stream Converter",
                            true, true);
                            
}

// Removes the component from the app-startup category
FirePHPModule.unregisterSelf = function(compMgr, fileSpec, location) {
    var catman = Components.classes["@mozilla.org/categorymanager;1"] .getService(Components.interfaces.nsICategoryManager);
    catMan.deleteCategoryEntry("@mozilla.org/streamconv;1", FIREPHP_CONVERTER_CONTRACT_ID, true);
}

// Return the Factory object
FirePHPModule.getClassObject = function (compMgr, cid, iid) {
    
    if (!iid.equals(Components.interfaces.nsIFactory))
        throw Components.results.NS_ERROR_NOT_IMPLEMENTED;
    
    // Check that the component ID is the FirePHP Proxy
    if(cid.equals(FIREPHP_CONVERTER_CLASS_ID)) {
      return FirePHPStreamConverterFactory;
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



function TrimString(sInString) {
  if(!sInString || !sInString.replace) return sInString;
  sInString = sInString.replace( /^\s+/g, "" );// strip leading
  return sInString.replace( /\s+$/g, "" );// strip trailing
}



// A logger
var gConsoleService = Components.classes['@mozilla.org/consoleservice;1'].getService(Components.interfaces.nsIConsoleService);

function FirePHP_logMessage(aMessage,Expand) {

  return;

  dump('FirePHP STREAM: [' + aMessage+"]\n");
  
  if(Expand) {
    for( var nam in aMessage ) {
      dump(' '+nam+' : ['+aMessage[nam]+"]\n");
    }
  }
  
}

