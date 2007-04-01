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




FirePHP.FirePHPCapabilitiesDefinition = function FirePHPCapabilitiesDefinition() {

	this.xmlTree = null;
	this.xmlString = null;
  this.changed = false;

  this.capabilitiesURL = null;
  this.loadStatus = null;    /* 0 => Detection in progress, 1 => Server Detected, -1 => Server not Detected */


  this.setCapabilitiesURL = function(CapabilitiesURL) {
    this.capabilitiesURL = CapabilitiesURL;
  }
  this.setLoadStatus = function(Status) {
    this.loadStatus = Status;
  }

  this.setXMLDefinition = function(XMLTree,XMLString) {
  	this.xmlTree = XMLTree;
  	this.xmlString = XMLString;
  	this.changed = true;
  }
  
  this.triggerNotifyIfChanged = function() {
  	if(!this.changed) return;
  	this.changed = false;
  	new FirePHPChannelEvent('Capabilities','DefinitionChanged',{CapabilitiesURL:this.capabilitiesURL}).trigger();
  }
  
  this.getToolbarDefinition = function(ToolbarContainerName) {
  	try {
  		var xotree = new XML.ObjTree();
    	var tree = xotree.parseXML(this.xmlString);  
  		if(tree.firephp.application.toolbar) {
  			if(!tree.firephp.application.toolbar[0]) {
  				tree.firephp.application.toolbar = new Array(tree.firephp.application.toolbar);
  			}
  			for( var index in tree.firephp.application.toolbar ) {
	  			if(tree.firephp.application.toolbar[index]['-container'] == ToolbarContainerName) {
	  				return tree.firephp.application.toolbar[index];
	  			}
	  		}
  		}
  	} catch(err) {}
  	return null;
  }
  
  this.getWidgetDefinition = function(WidgetContainerName) {
  	try {
  		var xotree = new XML.ObjTree();
    	var tree = xotree.parseXML(this.xmlString);  
  		if(tree.firephp.application.widget) {
  			if(!tree.firephp.application.widget[0]) {
  				tree.firephp.application.widget = new Array(tree.firephp.application.widget);
  			}
  			for( var index in tree.firephp.application.widget ) {
	  			if(tree.firephp.application.widget[index]['-container'] == WidgetContainerName) {
	  				return tree.firephp.application.widget[index];
	  			}
	  		}
  		}
  	} catch(err) {}
  	return null;
  }
  
  this.getSlideoutDefinition = function(SlideoutContainerName) {
  	try {
  		var xotree = new XML.ObjTree();
    	var tree = xotree.parseXML(this.xmlString);  
  		if(tree.firephp.application.slideouts) {
  			if(!tree.firephp.application.slideouts[0]) {
  			 tree.firephp.application.slideouts = new Array(tree.firephp.application.slideouts);
  			}
	  		for( var index in tree.firephp.application.slideouts ) {
	  			if(tree.firephp.application.slideouts[index]['-container'] == SlideoutContainerName) {
	  				return tree.firephp.application.slideouts[index];
	  			}
  			}
  		}
  	} catch(err) {}
  	return null;
  }
  
  this.getHandlerDefinitions = function() {
  	try {
  		var xotree = new XML.ObjTree();
    	var tree = xotree.parseXML(this.xmlString);  
  		if(tree.firephp.application.handlers) {
  			return tree.firephp.application.handlers;
  		}
  	} catch(err) {}
  	return null;
  }
  
};





FirePHP.FirePHPCapabilitiesHandler = {

  definitions: new Array(),


	initialize: function() {
    /* Lets listen to all Capabilities events */
    FirePHP.FirePHPChannel.addHandler("Capabilities", this);
	},
	
	
	getDefinition: function(CapabilitiesURL) {
		return this.definitions[CapabilitiesURL];
	},
	
	
	handleFirePHPEvent: function(Event,Flags) {
	
  	switch(Event.getGroup()+'->'+Event.getName()) {
  		case 'Capabilities->LoadDefinition':
  			this.load(Event.getValue('CapabilitiesURL'),false);
  			break;
  		case 'Capabilities->ReloadDefinition':
  			this.load(Event.getValue('CapabilitiesURL'),true);
  			break;
    }
	},	



	load: function(CapabilitiesURL,FroceLoad) {
	
		var ioService = Components.classes["@mozilla.org/network/io-service;1"]
                                .getService(Components.interfaces.nsIIOService);
    var uri = ioService.newURI(CapabilitiesURL, null, null);
		var pm = Components.classes["@mozilla.org/permissionmanager;1"].getService(Components.interfaces.nsIPermissionManager);

		/* Check if allowed */
		if(!(uri && (pm.testPermission(uri, "firephp.capabilities") == Components.interfaces.nsIPermissionManager.ALLOW_ACTION || uri.scheme == "file"))) {
			/* Not allowd, se if it is denied */
			if((uri && (pm.testPermission(uri, "firephp.capabilities") == Components.interfaces.nsIPermissionManager.DENY_ACTION && uri.scheme != "file"))) {
				/* Denied so lets return */
				return false;
			} else {
				/* URI not allowed nor denied so lets prompt to ask user */
   			new FirePHPChannelEvent('Internal','OpenAllowedCapabilityHostsWindow',{URL:CapabilitiesURL}).trigger()
				return false;							
			}
		}

 
    var definition = this.definitions[CapabilitiesURL];

    /* If we dont have a data object for this definition, lets create one */
    if(!definition) {
      definition = this.definitions[CapabilitiesURL] = new FirePHP.FirePHPCapabilitiesDefinition();
      definition.setCapabilitiesURL(CapabilitiesURL);
    }

    /* Check the loadStatus of the server data to see if we should trigger
     * a detect or ignore the request. We can also force a detect.
     * 
     * If loadStatus = 0 the server detect is already in progress.
     * This may happen if the detect is triggered multiple times for the same
     * domain in very short intervals.
     * So lets just ignore this request as the original request
     * should complete soon
     */
    if(definition.loadStatus!=null && FroceLoad!=true) {
      return; 
    }
      
    /* No detection has been done for this definition yet.
     * Lets start a detect
     */

    definition.setLoadStatus(0);
  
    try {
  
      var callback =
      {
        success: FirePHP.FirePHPCapabilitiesHandler.parseServerSuccessResponse,
        failure: FirePHP.FirePHPCapabilitiesHandler.parseServerFailureResponse,
        argument: CapabilitiesURL,
        timeout: 5000,
        scope: FirePHP.FirePHPCapabilitiesHandler
      }
  
      YAHOO.util.Connect.asyncRequest('GET', CapabilitiesURL, callback, null);
  
    } catch(err) {
      definition.setLoadStatus(null);
  
      /* The detection request failed. Lets try again as the request should not fail here */
      dump('Error trying to detect FirePHPServer at ['+url+']. We will try again!');
    }
  },
  
  
  parseServerSuccessResponse: function(Response) {

    var definition;
    
    try {

      definition = this.definitions[Response.argument];

      /* First reset the serverStatus so if the code below fails it can try again */              
      definition.setLoadStatus(null);

      if(Response.responseXML) {
      	
      	definition.setXMLDefinition(Response.responseXML,Response.responseText);
        definition.setLoadStatus(1);

      }            
    } catch(err) {}

		/* Check with the capabilities definition if anything has changed */
		if(definition) {
			definition.triggerNotifyIfChanged();
		}
  },
  
  parseServerFailureResponse: function(Response) {

    var definition;;

    try {
    
      definition = this.definitions[Response.argument];

      /* First reset the serverStatus so if the code below fails it can try again */              
      definition.setLoadStatus(null);
              
      switch(Response.status) {
        case 0:     /* Communication failure */
        case -1:     /* Transaction aborted */
          definition.setLoadStatus(-1);
          break;
        case 403:   /* Forbidden */
          /* We were not allowed to read the detection URL on the server.
           * We assume we do not have access to the FirePHPServer and
           * will not try the detection again.
           */
          definition.setLoadStatus(-1);
          break;
        case 404:   /* Not Found */
          /* The detection URL was not found on the server.
           * We assume no FirePHPServer is setup and will not try
           * the detection again.
           */
          definition.setLoadStatus(-1);
          break;
        default:
          dump('Got unsupported response status ['+o.status+'] while trying to load capabilities definition!');
          break;
      }
    
    } catch(err) {}
		
		/* Check with the capabilities definition if anything has changed */
		if(definition) {
			definition.triggerNotifyIfChanged();
		}
  }
}
