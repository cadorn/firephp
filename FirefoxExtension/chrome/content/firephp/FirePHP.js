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

var FirePHP = top.FirePHP = {

  version: '0.0.5',
  name: 'FirePHP',
  title: 'FirePHP',
  
  selectedApplication: null,
  selectedRequest: null,
  
  preferencesService: null,


  initialize: function() {

    /* Get preferences service */
    try {
      this.preferencesService = Components.classes['@mozilla.org/preferences-service;1'].getService(Components.interfaces.nsIPrefBranch2);
    } catch (err) {}

    FirePHP.Handlers.initialize();

    FirePHP.FirePHPCapabilitiesHandler.initialize();
    FirePHP.FirePHPRequestHandler.initialize();
    
    /* Lets listen to all UI and Internal events */
    FirePHP.FirePHPChannel.addHandler("UI", this);
    FirePHP.FirePHPChannel.addHandler("Internal", this);
  },
  	
	
	handleFirePHPEvent: function(Event,Flags) {
  	switch(Event.getGroup()+'->'+Event.getName()) {

  		case 'UI->SelectRequest':
  	    this.selectedRequest = Event.getValue('RequestID');
 				new FirePHPChannelEvent('State','SelectedRequestChanged',{RequestID:this.selectedRequest}).trigger();
  			break;

  	  case 'UI->FirefoxMenuCommand':
		  	switch(Event.getValue('Name')) {

		  		case 'OpenAllowedCapabilityHostsWindow':
		  			new FirePHPChannelEvent('Internal','OpenAllowedCapabilityHostsWindow').trigger()
		  			break;

		  		case 'GoReferenceWebsite':
		  			FirePHPChrome.browser$("content").selectedTab = FirePHPChrome.browser$("content").addTab('http://www.firephp.org/Reference/');
		  			break;
		  	}
  	  	break;
  	  	
  	  case 'Internal->OpenAllowedCapabilityHostsWindow':
        var params = {
            permissionType: "firephp.capabilities",
            windowTitle: "FirePHP Allowed Capability Hosts",
            introText: "The list below specifies all Capability Definition Hosts allowed by you. A capability definition adds functionality to your FirePHP tool. It is important that you load definitions only from trusted hosts. Loading an un-trusted definition may create a security hole in your browser. Please see http://www.firephp.org/Security for more information.",
            blockVisible: true, sessionVisible: false, allowVisible: true, prefilledHost: Event.getValue('URL')
        };
        FirebugLib.openWindow("Browser:Permissions", "chrome://browser/content/preferences/permissions.xul","", params);
  			break;
    }
	},

  
  /* Enable and disable FirePHP
   * At the moment this enables and disables the FirePHP accept header
   */ 
  enable: function() {
    /* Enable the FirePHP Service Component to set the multipart/firephp accept header */  
    try {
      Components.classes['@firephp.org/service;1'].getService(Components.interfaces.nsIFirePHP).setRequestHeaderEnabled(true);
    } catch (err) {}
  },
  disable: function() {
    /* Enable the FirePHP Service Component to set the multipart/firephp accept header */  
    try {
      Components.classes['@firephp.org/service;1'].getService(Components.interfaces.nsIFirePHP).setRequestHeaderEnabled(false);
    } catch (err) {}
  },

  setSelectedApplication: function(Name) {
    this.selectedApplication = Name;
  },

  getSelectedApplication: function() {
    return this.selectedApplication;
  },
  
  getSelectedRequestID: function() {
    return this.selectedRequest;
  },



  /* Synchronizes the UI based on all context, preference and
   * status information. It takes the current browser mode
   * into account and targets the appropriate FirePHPChrome object.
   */
  syncUI: function() {
    setTimeout(FBL.bindFixed(function() { FirePHPChrome.syncUI(); }, this));
  },

  /* Sets a user preference and refreshes the UI afterwards */
  setUIPreference: function(Name,Value) {
    this.setPreference(Name,Value);
    this.syncUI();
  },

  setPreference: function(Name,Value) {
    Name = "extensions.firephp."+Name;
    switch(this.preferencesService.getPrefType(Name)) {
      case Components.interfaces.nsIPrefBranch.PREF_STRING:
        this.preferencesService.setCharPref(Name, Value);
        break;
      case Components.interfaces.nsIPrefBranch.PREF_INT:
        this.preferencesService.setIntPref(Name, Value);
        break;
      case Components.interfaces.nsIPrefBranch.PREF_BOOL:
        this.preferencesService.setBoolPref(Name, Value);
        break;
    }
  },
  getPreference: function(Name) {
    Name = "extensions.firephp."+Name;
    switch(this.preferencesService.getPrefType(Name)) {
      case Components.interfaces.nsIPrefBranch.PREF_STRING:
        return this.preferencesService.getCharPref(Name);
      case Components.interfaces.nsIPrefBranch.PREF_INT:
        return this.preferencesService.getIntPref(Name);
      case Components.interfaces.nsIPrefBranch.PREF_BOOL:
        return this.preferencesService.getBoolPref(Name);
    }
  }
}




FirePHP.FirePHPClientEventHandler = {

 
  QueryInterface: function(iid) {
    if (iid.equals(Components.interfaces.nsIDOMEventListener ) ||
        iid.equals(Components.interfaces.nsISupports))
        return this;
      throw Components.results.NS_NOINTERFACE;
  },  
  
  /* Interface: Components.interfaces.nsIDOMEventListener */  
  handleEvent: function ( event ) {

Firebug.Console.log(event);

  }
}