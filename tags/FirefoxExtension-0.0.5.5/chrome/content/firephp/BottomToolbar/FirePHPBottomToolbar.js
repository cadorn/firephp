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



FirePHPChrome.BottomToolbar = {

  panels: null,

  /* Set true initially so its refreshed at least once for every new instance */
  forceRefresh: true,


  refreshUI: function(ForceUIRefresh) {

    if(this.forceRefresh) {
      ForceUIRefresh = true;
      this.forceRefresh = false;
    }

//    dump('FirePHPChrome.BottomToolbar.refreshUI()'+"\n");

    var FirePHPBottomToolbar = FirePHPChrome.$("idFirePHPBottomToolbar");
    var FirePHPApplicationDeck = FirePHPChrome.$("idFirePHPApplicationDeck");


    var selectedApplication = FirePHP.getSelectedApplication();
    var serverData;
    
    if(selectedApplication==null) {
      var window = FirePHPChrome.getPanel().context.window;
      if(window) {
        if(window.location.href) {
          serverData = FirePHP.FirePHPApplicationHandler.getData(window.location.href,true,true);
        }
      }
    } else {
      serverData = FirePHP.FirePHPApplicationHandler.getData(selectedApplication,true);
    }
    
    
    /* If we have found server data for the selected application
     * lets skip this toolbar refresh if nothing has changed
     */
    if(serverData && !ForceUIRefresh) {
      if(!serverData.hasChanged()) {
        
        /* Update the correct deck index based on the selected tab */
        var selectedTab = serverData.getSelectedTab();
        if(selectedTab!=null && this.panels) {
          for (var i = 0; i < this.panels.length; ++i) {
            if(this.panels[i].name==selectedTab) {
              FirePHPApplicationDeck.selectedIndex = parseInt(this.panels[i].tabIndex)+1;
            }
          }
        }
        return;
      } else {
        serverData.resetChanged();
      }
    }
    
    


    /* Remove any old items already present in the bar */
    try {
      if(this.panels && FirePHPBottomToolbar.tabMap) {
        for (var i = 0; i < this.panels.length; ++i) {
          FirePHPBottomToolbar.removeChild(FirePHPBottomToolbar.tabMap[this.panels[i].name]);
        }
      }    
    } catch(err) {}


    this.panels = new Array();


    var panel = null;

    panel = FirePHPLib.extend(FirePHPChrome.BottomToolbar.Panel,{
      name: "FirePHPRequests",
      title: "Requests",
      toolbarItemType: "tab"
    });
    this.panels.push(panel);
    
    panel = FirePHPLib.extend(FirePHPChrome.BottomToolbar.Panel,{
      name: "FirePHPApplications",
      title: ((serverData)?serverData.getVar('Application','Label'):"Applications"),
      toolbarItemType: "dropdown",
      optionsPopup: null,

      initialize: function(Item) {
          
        var windows = FirePHPChrome.getPanel().context.windows;
        
        if(Item.optionsPopup && windows) {
          var urlList = new Array();
          for( var i=0 ; i<windows.length ; i++ ) {
            urlList.push(windows[i].location.href);
          }
          var applications = FirePHP.FirePHPApplicationHandler.getData(urlList);
          if(applications) {
            var label;
            var key;
            for( var i=0 ; i<applications.length ; i++ ) {
              label = applications[i].getVar('Application','Label');
              key = applications[i].url;
              if(label) {
                FirePHPLib.createMenuItem(Item.optionsPopup, {label: label, type: "radio", value: key, command: function() { FirePHPChrome.BottomToolbar.selectApplication(this.getAttribute('value')); }});
              }
            }
          }
        }
      }
    });
    this.panels.push(panel);

    /* Check if we have a selected application, if we do lets add the panels */

    if(serverData && false) {

      var tabs = serverData.getTabs();
      if(tabs) {
        
        /* Remove any previous browsers we have in the deck
         * TODO: Instead of removing browsers whenever the application is switched
         *       we should instead keep a central repository of browsers
         *       scoped by serverData and tab and then add and remove them
         *       as needed. This way we never loose the browser context.
         */
        if(FirePHPApplicationDeck.childNodes.length>1) {
          while( FirePHPApplicationDeck.childNodes.length > 1) {
            FirePHPApplicationDeck.removeChild(FirePHPApplicationDeck.childNodes.item(FirePHPApplicationDeck.childNodes.length-1));
          }
        }
    
        for( var i in tabs ) {
    
          /* Create a browser for the tab and add it to the deck */
          var browser = FirePHPChrome.getDocument().createElement("browser");
          browser.setAttribute("type","content");
          FirePHPApplicationDeck.appendChild(browser);
    
    			FirePHPChrome.BottomToolbar.BrowserWatcher.watchBrowser(browser);
    
          /* Create a tab */
          var panel = FirePHPLib.extend(FirePHPChrome.BottomToolbar.Panel,{
            tabIndex: parseInt(i),
            name: tabs[i]['name'],
            title: tabs[i]['label'],
            source: tabs[i]['source'],
            serverData: serverData,
            toolbarItemType: "tab"
          });
          this.panels.push(panel);
        }
      }
    }

return;

    /* Add the created panels to the UI */
    for (var i = 0; i < this.panels.length; ++i) {
      FirePHPBottomToolbar.addItem(this.panels[i]);
    }

    /* Now that all panels/tabs are added lets select the
     * selected panel if we have one
     */
    if(serverData) {
      var selectedTab = serverData.getSelectedTab();
      if(selectedTab!=null) {
        FirePHPBottomToolbar.selectPanel(selectedTab,false);
      }
    } else {
      FirePHPBottomToolbar.selectPanel('FirePHPRequests',false);
    }

  },
  
  
  selectApplication: function(Name) {

    /* Set the selected application for global FirePHP
     * TODO: The code below should really listen for application
     *       selection events on FirePHP and act accordingly
     *       instead of being called inline below. This will
     *       then allow selection of application from other
     *       areas of the app as well and not just the tabs
     */

    FirePHP.setSelectedApplication(Name);

    this.refreshUI(true);
  },
  
  
  

  
  // * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
  // BrowserWatcher Owner
  
  isURIAllowed: function(uri) {
  	return true;
  },
  isURIDenied: function(uri) {
  	return false;
  },
  createTabContext: function(win, browser, chrome, state) {
    return new FirePHPChrome.BottomToolbar.TabContext(win, browser, chrome, state);
  },
  destroyTabContext: function(browser, context) {
  },

  // * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
  // BrowserWatcher Listener

  initContext: function(context) {
  },
  showContext: function(browser, context) {
  },
  watchWindow: function(context, win) {
  	/* Add the FirePHP Channel API to the window so the page
  	 * can access FirePHP services
  	 */
  	if(!win.FirePHPChannel) {
  		win.FirePHPChannel = FirePHP.FirePHPChannel;
  	}
  },
  unwatchWindow: function(context, win) {
    delete win.FirePHPChannel;
  },
  loadedContext: function(context) {
  }

};


FirePHPChrome.BottomToolbar.Panel = {

  tabIndex: null,
  name: null,
  title: null,
  source: null,
  toolbarItemType: null,
  serverData: null,
  

  initialize: function(Item) {
  },
  
  select: function(Document,DoubleClick) {

    /* Store the fact that this panel/tab has been selected */
    if(this.serverData) {
      this.serverData.setSelectedTab(this.name);
    } else {
      FirePHP.setSelectedApplication(false);
    }

    /* Check if we clicked on the Requests tab or another tab
     * The requests are shown in the default firebug panel
     * while any other tabs are shown in our custom browser
     * If we had a double-click on the tab, re-load the original source
     */

    var FirePHPApplicationDeck = FirePHPChrome.$("idFirePHPApplicationDeck");
    
    if(this.name=='FirePHPRequests') {

      /* Select the first element in the deck with is the default Firebug panel */
      FirePHPApplicationDeck.selectedIndex = 0;

    } else {

      /* Select the correct browser element for the tab */
      FirePHPApplicationDeck.selectedIndex = parseInt(this.tabIndex)+1;

      var browser = FirePHPApplicationDeck.childNodes.item(parseInt(this.tabIndex)+1);

      /* If we had a double-click we set the source for the browser again
       * and reload the URL
       */
      if(DoubleClick) {
        browser.loadURIWithFlags(this.source, Components.interfaces.nsIWebNavigation.LOAD_FLAGS_BYPASS_CACHE, null, null, null);
      } else {
      
        /* Check if the URI of the browser has ever been set */
        if(browser.contentDocument.location.href=='about:blank') {

          browser.loadURIWithFlags(this.source, Components.interfaces.nsIWebNavigation.LOAD_FLAGS_BYPASS_CACHE, null, null, null);
        }
      }
    }
  }
};

FirePHPChrome.BottomToolbar.TabContext = function(win, browser, chrome, persistedState)
{
  this.window = win;
  this.browser = browser;
  this.persistedState = persistedState;    
  this.chrome = chrome;
  this.windows = [];    
  this.panelMap = {};
  this.sidePanelNames = {};
  
  this.destroy = function(state) {
  };
};
