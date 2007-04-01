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


FBL.ns(function() { with (FBL) {

// ************************************************************************************************


var FirePHP, FirePHPLib;


Firebug.FirePHP = extend(Firebug.Module, {

    // * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
    // extends Module

    /* Called when firefox (the chrome specifically) is first loaded.
     * This happens when firefox first starts up or the chrome is reloaded.
     */
    initialize: function() {
      FirePHP = FirebugChrome.window.FirePHP;
      FirePHPLib = FirebugChrome.window.FirePHPLib;
    },
    /* Called when firefox (the chrome specifically) is destroyed
     * This happens when firefox is closed or the chrome is reloaded.
     */
    shutdown: function() {},
    /* Called when a page is loaded into the browser (or a new tab).
     * Will only be called once per URL loaded into a tab/window.
     * Will not fire for each page loaded into iframes or framesets
     * contained within the parent URL.
     */
    initContext: function(context) {
    	FirePHP.enable();

      /* Add a listener to the browser so we can monitor all window/frame/document loading states */
      try {
        context.browser.addProgressListener(FirePHP.FirePHPRequestHandler,Components.interfaces.nsIWebProgress.NOTIFY_DOCUMENT);
      } catch(err) {}
      try {
        context.browser.addProgressListener(FirePHP.FirePHPRequestHandler,Components.interfaces.nsIWebProgress.NOTIFY_STATE_WINDOW);
      } catch(err) {}
    },
    reattachContext: function(context) {},
    /* Opposite of initContext called when a URL is unloaded prior
     * to loading a new URL.
     * Also called when the chrome is reloaded or if firefox is closed.
     */
    destroyContext: function(context, persistedState) {
      /* Remove the listener we attached to do proper cleanup */
      try {
        context.browser.removeProgressListener(FirePHP.FirePHPRequestHandler);
      } catch(err) {}
    },
    /* Called for every window/frame loaded */
    watchWindow: function(context, win) {
    	/* Add the FirePHP Channel API to the window so the page
    	 * can access FirePHP services
    	 */
    	if(!win.FirePHPChannel) {
    		win.FirePHPChannel = FirePHP.FirePHPChannelAPI;
    	}
    },
    /* Called before for every window/frame is un-loaded */
    unwatchWindow: function(context, win) {
      delete win.FirePHPChannel;
    },
    /* Called when a browser tab is switched and a new window selected */
    showContext: function(browser, context) {
    dump('show context'+"\n");
      /* Notify our chrome to refresh its context now that the Firebug
       * context has been created/switched
       */
      FirePHPChrome.refreshContext();      
    },
    loadedContext: function(context) {},
    
    showPanel: function(browser, panel) {
      /* Hide/Show our Firebug panel buttons */
      var isFirePHP = panel && panel.name == top.FirePHP.name;
      var FirePHPButtons = browser.chrome.$("fbFirePHPButtons");
      collapse(FirePHPButtons, !isFirePHP);

      /* Notify our chrome to refresh its context based on the
       * selected Firebug tab
       */
      FirePHPChrome.refreshContext();
    },
    showSidePanel: function(browser, panel) {
      var isFirePHP = panel && panel.name == top.FirePHP.name;
      if(!isFirePHP) return;
    },

    enable: function() {
    	dump('enable'+"\n");
    },
    
    disable: function() {
    	dump('disable'+"\n");
    },

    // * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
    // Internal
 
    triggerMenuToggle: function(button) {
      switch(button) {
        case 'StatusIndicator':

          /* Trigger manual capabilities detection */
  
          var windows = FirePHPChrome.getPanel().context.windows;
          if(windows) {
            var urlList = new Array();
            for( var i=0 ; i<windows.length ; i++ ) {
              FirePHP.FirePHPApplicationHandler.triggerDetect(windows[i].location.href,true);
            }
          }
          break;
      }
    }
});

// ************************************************************************************************

function FirePHPPanel() {}

FirePHPPanel.prototype = extend(Firebug.Panel, {

    // * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *    
    // extends Panel

    name: top.FirePHP.name,
    title: top.FirePHP.title,
    searchable: false,
    editable: false,

    /* Called whenever the panel is selected from the menu or
     * whenever a new URL is loaded into a tab/browser window.
     * Is not loaded when a new URL is loaded into an iframe
     * or frameset contained within the parent URL.
     */
    show: function(state) {
      /* Notify our chrome handler to refresh its UI */
      FirePHPChrome.triggerRefreshUI(this);
    },


    // * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *    
    // Internal

    
    /* Check context and ensure UI is consistent with serverContext */
    refreshUI: function() {
    
      dump('FirePHPPanel.refreshUI()'+"\n");
    
      /* Only do this if the panel is visible.
       * We can limit based on the visibility because this method will
       * be called again if the panel was hidden and changed to visible
       */
      if(!this.visible) return;
      
      /* Set the style of the encompassing DIV element manually as
       * setting our own custom sytle definition via .panelNode-FirePHP does not work
       */
      this.panelNode.setAttribute('style','background-color: #ECECEC;');

      /* Try and get the serverContext from the FirePHP module
       * Once we have it we can update all default components of the UI
       * More specific components of the UI are updated upon implied request
       * of the user as different info is navigated.
       */
       
//      var serverContext = Firebug.FirePHP.getServerContext(this.context);
      var serverContext = null;

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
      
        var windowContexts = FirePHP.FirePHPRequestHandler.getDataForWindow(this.context.windows[i].name);
        if(windowContexts) {
          for( var anchor in windowContexts ) {
            this.insertRequestInfoRequestTable(windowContexts[anchor]);
          }
        }
      }
    },
    
    
    renderRequestTable: function() {
      this.panelNode.innerHTML =  ''+
      
'<link rel="stylesheet" href="chrome://firephp/skin/RequestsPanel.css"></link>'+
'<table id="FirePHP-RequestTable" border="0" cellpadding="0" cellspacing="0">'+
  '<tr>'+
    '<td class="header"><b>Application</b></td>'+
    '<td class="header"><b>Anchor</b></td>'+
    '<td class="header"><b>Frame</b></td>'+
    '<td class="header"><b>URL</b></td>'+
  '</tr>'+
'</table>';
    },

    insertRequestInfoRequestTable: function(windowContext) {
    
      if(!windowContext) return;
    
      var requestTable = this.document.getElementById('FirePHP-RequestTable');

      var newRow = requestTable.insertRow(requestTable.rows.length)
      newRow.id = windowContext.requestID;
      newRow.addEventListener('click',FirePHPChrome.RequestListListener,true);

      var newCell = null;

      newCell = newRow.insertCell(0);
      newCell.innerHTML = windowContext.getApplicationLabel();

      newCell = newRow.insertCell(1);
      newCell.innerHTML = windowContext.getAnchorLabel();

      newCell = newRow.insertCell(2);
      newCell.innerHTML = windowContext.getFrameName();

      newCell = newRow.insertCell(3);
      newCell.innerHTML = windowContext.getDisplayURL();
  
/*      
      var html = '';
      if(ServerVars) {
        for(var name in ServerVars) {
          if(name!='RequestID') {
            html = html + '<b>'+name+':</b>&nbsp;'+ServerVars[name]+'&nbsp;&nbsp;&nbsp; ';
          }
        }
      }
      newCell.innerHTML = html;
*/      
    }

});


// ************************************************************************************************

Firebug.registerModule(Firebug.FirePHP);
Firebug.registerPanel(FirePHPPanel);

// ************************************************************************************************

}});

