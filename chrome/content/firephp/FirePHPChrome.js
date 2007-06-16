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



/* Check if Firebug is running in the external window or
 * if it is running within the bottom of the browser
 */
var FirebugExternalMode = (window.location == "chrome://firebug/content/firebug.xul");


/* Find the reference to FirePHP & FirePHPLib which is at the top of the main window
 * If FirePHP is running in the main window its easy
 * If we are running in the external window reference it via
 * the arguments Firebug passes to the new window
 */

var FirePHP = (FirebugExternalMode)?top.arguments[0].context.originalChrome.window.FirePHP:top.FirePHP;
var FirePHPLib = (FirebugExternalMode)?top.arguments[0].context.originalChrome.window.FirePHPLib:top.FirePHPLib;



var FirePHPChrome = top.FirePHPChrome = {
  
  CapabilitiesURL: null,
  
  initialized: false,
  
  
	initialize: function() {
		
		if(this.initialized) return;	
		this.initialized = true;
		
dump('FirePHPChrome:initialize()'+"\n");    


		FirePHPChrome.BottomToolbar.BrowserWatcher.initialize(FirePHPChrome.BottomToolbar);	

    /* Lets listen to all Capabilities events */
    FirePHP.FirePHPChannel.addListener("Capabilities", this);
    

    var ContentWindowTopToolbarContainer = FirePHPChrome.browser$("idContentWindowTopToolbarContainer");
		/* Check if we have the window top toolbar already added.
		 * If not lets insert it and then the code below can
		 * show and hide it
		 * Once the toolbar is added the binding will initialize and
		 * fill it with content
		 */
		if(!ContentWindowTopToolbarContainer) {
			var TabBrowserParent = FirePHPChrome.browser$("content").selectedBrowser.parentNode.parentNode.parentNode;
			var ContentWindowTopToolbarContainer = FirePHPChrome.getDocument().createElement("tagFirePHPUIToolbar");
			ContentWindowTopToolbarContainer.setAttribute("id","idContentWindowTopToolbarContainer");
			TabBrowserParent.insertBefore(ContentWindowTopToolbarContainer,TabBrowserParent.childNodes[2]);
		}    
    
    this.buildUI(this.CapabilitiesURL);
    
	},  
  
  	
	notifyFirePHPEvent: function(Event,Flags) {
  	switch(Event.getGroup()+'->'+Event.getName()+(((Flags & FirePHP.FirePHPChannel.STATE_AFTER)==FirePHP.FirePHPChannel.STATE_AFTER)?'->STATE_AFTER':'')) {
  		case 'Capabilities->DefinitionChanged->STATE_AFTER':
				this.buildUI(Event.getValue('CapabilitiesURL'));
  			break;
    }
	},	
  
  
  /* Get a reference to an object by ID
   * This takes into account if Firebug is running within the
   * main window or the external window
   */
  $: function(Name) {
    return FirebugChrome.getCurrentBrowser().chrome.$(Name);
  },

	/* Get a reference to an object ID in the main browser */
  browser$: function(Name) {
		var browser = Firebug.tabBrowser.selectedBrowser;    
 		if (browser.detached) {
 			return browser.originalChrome.$(Name);	
		} else {
    	return browser.chrome.$(Name);
    }
  },
  
  /* Get a reference to the active Firebug document
   * This takes into account if Firebug is running within the
   * main window or the external window
   */
  getDocument: function() {
    return FirebugChrome.getCurrentBrowser().chrome.window.document;
  },
  
  
  
  
  /* Called whenever the capabilities definition has changed and the UI needs to be re-built */
  buildUI: function(CapabilitiesURL) {
  	this.CapabilitiesURL = CapabilitiesURL;

		if(!this.initialized) return;	
  		
    dump('FirePHPChrome.buildUI()'+"\n");
  	
  	/* Lets first build the toolbars */
  	
  	var definition = FirePHP.FirePHPCapabilitiesHandler.getDefinition(CapabilitiesURL);

		FirePHPChrome.UI.ContentWindowTopToolbarContainer.buildUI(definition.getToolbarDefinition('ContentWindowTopToolbarContainer'));
  	FirePHPChrome.UI.ContentWindowTopSlideoutContainer.buildUI(definition.getSlideoutDefinition('ContentWindowTopSlideoutContainer'));
  	FirePHPChrome.UI.FirePHPWindowResourceToolbarContainer.buildUI(definition.getToolbarDefinition('FirePHPWindowResourceToolbarContainer'));
		FirePHPChrome.UI.FirePHPWindowResourceWidgetContainer.buildUI(definition.getWidgetDefinition('FirePHPWindowResourceWidgetContainer'));
  },  
  
  
  /* This method is called whenever Firebug tabs change
   */
  refreshContext: function() {

		this.initialize();

//    dump('FirePHPChrome.refreshContext()'+"\n");
    
    /* Check to see if our panel is showing and
     * show/hide our additional toolbars and panels accordingly
     */

    var FirePHPBottomToolbar = FirePHPChrome.$("idFirePHPBottomToolbar");
    var idFirePHPSplitter = FirePHPChrome.$("idFirePHPSplitter");
    var idFirePHPPanel = FirePHPChrome.$("idFirePHPPanel");
    var ContentWindowTopToolbarContainer = FirePHPChrome.browser$("idContentWindowTopToolbarContainer");
		
		
		/* Check if we have the window top toolbar already added.
		 * If not lets insert it and then the code below can
		 * show and hide it
		 * Once the toolbar is added the binding will initialize and
		 * fill it with content
		 */
/*		 
		if(!ContentWindowTopToolbarContainer) {
			var TabBrowserParent = FirePHPChrome.browser$("content").selectedBrowser.parentNode.parentNode.parentNode;
			var ContentWindowTopToolbarContainer = FirePHPChrome.getDocument().createElement("tagFirePHPUIToolbar");
			ContentWindowTopToolbarContainer.setAttribute("id","idContentWindowTopToolbarContainer");
			TabBrowserParent.insertBefore(FirePHPWindowTopToolbar,TabBrowserParent.childNodes[2]);
		}    
*/		
		/* TODO: Provide a preference to keep the window top toolbar showing when
		 * other Firebug panels are selected
		 */

    if(this.getPanel() && this.getPanel().visible==true) {
      
      /* Our panel is showing */
      
      idFirePHPSplitter.hidden = false;
      idFirePHPPanel.hidden = false;
			ContentWindowTopToolbarContainer.hidden = false;
    } else {
      
      /* Our panel is not showing */

      idFirePHPSplitter.hidden = true;
      idFirePHPPanel.hidden = true;
			ContentWindowTopToolbarContainer.hidden = true;
    }
    
    
		FirePHPChrome.UI.ContentWindowTopSlideoutContainer.layoutUI();
    
  
    /* Add listener to monitor resizing of the variables panel
     * so we can adjust the flow box accordingly
     */    
    FirePHPChrome.$("idFirePHPVariableWatchBox").addEventListener('DOMAttrModified',FirePHPChrome,true);
	      
    setTimeout(FBL.bindFixed(function() { FirePHPChrome.layoutUI(); }, this));
  },
  
  QueryInterface: function(iid) {
    if (iid.equals(Components.interfaces.nsIDOMEventListener ) ||
        iid.equals(Components.interfaces.nsISupports))
        return this;
      throw Components.results.NS_NOINTERFACE;
  },  
  
  /* Interface: Components.interfaces.nsIDOMEventListener */  
  handleEvent: function ( event ) {

    var idFirePHPVariableWatchBox = FirePHPChrome.$("idFirePHPVariableWatchBox");
    
    if(event.originalTarget==idFirePHPVariableWatchBox && 
       event.type=='DOMAttrModified' &&
       event.attrName=='width') {

      FirePHPChrome.layoutUI();
    }
  },
  
  
  
  /* This method is called whenever internal data may have changed
   * and the UI should be updated to bring it into sync with the data
   * NOTE: The timeout and FBL.bindFixed is necessary here to ensure
   *       the UI will properly refresh but not 100% sure why.
   *       Maybe because it triggers the call in a new thread which
   *       gives the show() thread a chance to finish and init the Firebug UI properly
   */
  triggerRefreshUI: function(Context) {

//    dump('FirePHPChrome.triggerRefreshUI()'+"\n");

    setTimeout(FBL.bindFixed(function() { FirePHPChrome.refreshUI(); }, Context));
  },
   
  refreshUI: function() {

//    dump('FirePHPChrome.refreshUI()'+"\n");

    /* Check to see if our panel is showing
     * We only want to continue if it is showing
     */
    if(!this.getPanel().visible) return;

    /* First synchronize the UI with all context and status data */
    FirePHP.syncUI();
  },



  /* Synchronizes the UI based on all context, preference and
   * status information. It takes the current browser mode
   * into account and targets the appropriate FirePHPChrome object.
   */
  syncUI: function() {

    dump('FirePHPChrome.syncUI()'+"\n");


/* TODO: Allow toggle of "Variables", "Console", "Viewer", "Resources" only for now - this needs to be updated in future to allow removing and adding of widgets etc...
   Maybe allow use to drag a widget "pin" or "tab" to the toolbar which will add a toggle for the widget.
   If all wigets in a container are hidden the container should be removed.
   Need to work with Joe to allow hiding of requests panel.
 */  

    /* Sync menu toggles */
    var requestFlag, variableFlag, consoleFlag, applicationFlag;
    this.$('idFirePHPRequestsToggleBroadcaster').setAttribute('checked',(requestFlag = FirePHP.getPreference('showRequestInspector')));
//    this.$('fbPanelBar1').deck.hidden = !requestFlag;
//    this.$('idFirePHPSplitter').hidden = !requestFlag;
    this.$('idFirePHPSplitterRequestFlowBox').style.display = (variableFlag)?'':'none';
    this.$('idFirePHPVariableSessionRequestSplitter').hidden = !requestFlag;
    this.$('idFirePHPVariableRequestBox').hidden = !requestFlag;

    this.$('idFirePHPVariablesToggleBroadcaster').setAttribute('checked',(variableFlag = FirePHP.getPreference('showVariableInspector')));
    this.$('idFirePHPVariableBox').hidden = !variableFlag;
    this.$('idFirePHPConsoleSplitter').hidden = !variableFlag;
    this.$('idFirePHPSplitterRequestFlowBox').style.display = (variableFlag && requestFlag)?'':'none';
    this.$('idFirePHPVariableViewerSplitter').hidden = !variableFlag;
    this.$('idFirePHPVariableViewerFrame').hidden = !variableFlag;

    this.$('idFirePHPConsoleToggleBroadcaster').setAttribute('checked',(consoleFlag = FirePHP.getPreference('showConsole')));
    this.$('idFirePHPConsoleSplitter').hidden = (!consoleFlag || !variableFlag);
    this.$('idFirePHPConsoleFrame').hidden = !consoleFlag;
    
    this.$('idFirePHPVariableConsoleBox').hidden = (!variableFlag && !consoleFlag);
//2    this.$('idFirePHPApplicationSplitter').hidden = (!variableFlag && !consoleFlag);
    
    this.$('idFirePHPApplicationToolbarBroadcaster').setAttribute('checked',(applicationFlag = FirePHP.getPreference('showApplicationToolbar')));

//2    this.$('idFirePHPApplicationSplitter').hidden = ((!variableFlag && !consoleFlag) || !applicationFlag);

//    this.$('idFirePHPApplicationDeck').hidden = !applicationFlag;
//    this.$('idFirePHPBottomToolbar').hidden = !applicationFlag;

    /* Trigger a refresh of the WindotTopToolbar */
    FirePHPChrome.WindowTopToolbar.refreshUI();

    /* Trigger a refresh of the BottomToolbar */
    FirePHPChrome.BottomToolbar.refreshUI();

    FirePHPChrome.getPanel().refreshUI();

    /* Now that all UI data is refreshed lets layout the UI
     * based on the data content
     */
    FirePHPChrome.layoutUI();
  },


  /* Called whenever anything resizes to ensure everything is layed out properly */
  layoutUI: function() {

    /* Adjust the flow div according to the request box */
    var FirePHPSplitterRequestFlowBox = FirePHPChrome.$("idFirePHPSplitterRequestFlowBox");
    var FirePHPVariableRequestBox = FirePHPChrome.$("idFirePHPVariableRequestBox");
    FirePHPSplitterRequestFlowBox.style.left = (FirePHPVariableRequestBox.boxObject.x-1)+'px';
    FirePHPSplitterRequestFlowBox.style.width = FirePHPVariableRequestBox.boxObject.width+'px';

  },


  /* Returns the FirePHP Panel */
  getPanel: function() {
    if(!FirebugContext) return false;
    return FirebugContext.getPanel(FirePHP.name);
  },


  togglePanel: function(PanelName) {
    switch(PanelName) {
      case 'RequestInspector':
      case 'VariableInspector':
      case 'Console':
      case 'ApplicationToolbar':
        FirePHP.setUIPreference('show'+PanelName,!FirePHP.getPreference('show'+PanelName));
        break;
    }
  }
}


FirePHPChrome.UI = {}


FirePHPChrome.RequestListListener = {
  
  QueryInterface: function(iid) {
    if (iid.equals(Components.interfaces.nsIDOMEventListener ) ||
        iid.equals(Components.interfaces.nsISupports))
        return this;
      throw Components.results.NS_NOINTERFACE;
  },  
  
  /* Interface: Components.interfaces.nsIDOMEventListener */  
  handleEvent: function ( event ) {
  
    if(event.type=='click') {

			new FirePHPChannelEvent('UI','SelectRequest',{RequestID:event.currentTarget.id}).trigger();

      /* TODO: Instead of setting the class here we should have FirePHPChrome
       * listen for events on FirePHP and update the class based on the
       * setSelectedRequestID so that it can be selected from elsewhere as well.
       */
      for( var i=0 ; i<event.currentTarget.parentNode.parentNode.rows.length ; i++ ) {
        event.currentTarget.parentNode.parentNode.rows[i].className = '';
      }
      event.currentTarget.className = 'SelectedRow';


      var FirePHPVariableSessionFrame = FirePHPChrome.$("idFirePHPVariableSessionFrame");
      var sessionTable = FirePHPVariableSessionFrame.contentDocument.getElementById('table');
      while( sessionTable.rows.length > 0 ) {
        sessionTable.deleteRow(0);
      }

      
      var FirePHPVariableRequestFrame = FirePHPChrome.$("idFirePHPVariableRequestFrame");
      var requestTable = FirePHPVariableRequestFrame.contentDocument.getElementById('table');
      while( requestTable.rows.length > 0 ) {
        requestTable.deleteRow(0);
      }

      /* Get Request Variables */
      var requestData = FirePHP.FirePHPRequestHandler.getData(event.currentTarget.id);
      if(requestData) {
      
        /* Update session variable list */
        var sessionVariables = FirePHP.FirePHPSessionHandler.getVariables(requestData.getApplicationID());
        if(sessionVariables) {
          for( var key in sessionVariables ) {
        
                var newRow = sessionTable.insertRow(sessionTable.rows.length)
                newRow.id = key;
                newRow.addEventListener('click',FirePHPChrome.VariableListListener,true);

                var newCell = newRow.insertCell(0);
                newCell.innerHTML = sessionVariables[key][1][2];
          }
        }
        
              
        /* Update request variable list */
        var data = requestData.getData();
        if(false && data) {
          var newRow = requestTable.insertRow(requestTable.rows.length)
          newRow.id = 'DATA';
          newRow.addEventListener('click',FirePHPChrome.VariableListListener,true);
          var newCell = newRow.insertCell(0);
          newCell.innerHTML = 'DATA';
        }
        var variables = requestData.getVariables();
        
        if(variables) {
          for( var key in variables ) {
        
              if(variables[key][1]=='REQUEST') {
                var newRow = requestTable.insertRow(requestTable.rows.length)
                newRow.id = key;
                newRow.addEventListener('click',FirePHPChrome.VariableListListener,true);
  
                var newCell = newRow.insertCell(0);
                newCell.innerHTML = variables[key][2];
              }
          }
        }
      }
    }
  }
}

FirePHPChrome.VariableListListener = {
  
  QueryInterface: function(iid) {
    if (iid.equals(Components.interfaces.nsIDOMEventListener ) ||
        iid.equals(Components.interfaces.nsISupports))
        return this;
      throw Components.results.NS_NOINTERFACE;
  },  
  
  /* Interface: Components.interfaces.nsIDOMEventListener */  
  handleEvent: function ( event ) {
  
    if(event.type=='click') {
      
      /* Get Request Variables */
      var requestData = FirePHP.FirePHPRequestHandler.getData(FirePHP.getSelectedRequestID());
      if(requestData) {

        /* TODO: Keep the selected variable elsewhere so we can re-select the variable
         * when the variable list is reloaded.
         * Need different selection modes (keep selected context per request or
         * keep selected context based on variable key so it is auto-selected
         * even when request ius changed) - maybe offer one and 2 click selection
         * where one click selects it for the request and a second click sets the background
         * darker and keeps the context for the variable accorss all requests.
         */
        for( var i=0 ; i<event.currentTarget.parentNode.parentNode.rows.length ; i++ ) {
          event.currentTarget.parentNode.parentNode.rows[i].className = '';
        }
        event.currentTarget.className = 'SelectedRow';


        var FirePHPVariableViewerFrame = FirePHPChrome.$("idFirePHPVariableViewerFrame");
        var div = FirePHPVariableViewerFrame.contentDocument.getElementById('variable');
        
        if(event.currentTarget.id=='DATA') {
          div.innerHTML = requestData.getData();
        } else {
          var variables = requestData.getVariables(event.currentTarget.id);
                    
          if(variables) {
          
          	
          	var value = eval('(' + variables[4] + ')');
            div.innerHTML = FirePHPLib.renderJSONString(value);
          }
        }
      }
    }
  }
}
