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



/* NOTE: This chrome element resides in the browser context only.
 * There is not a separate context if Firebug is open in a window.
 * It is thus important to reference any UI elements for this
 * component via the FirePHPChrome.browser$ method and not
 * the FirePHPChrome.$ method.
 */

FirePHPChrome.WindowTopToolbar = {

	initialized: false,

	initialize: function() {
		this.initialized = true;
		
		var FirePHPWindowTopToolbar = FirePHPChrome.browser$("idFirePHPWindowTopToolbar");
		FirePHPWindowTopToolbar.addEventListener('click',this,true);
		
		this.refreshUI();	
	},

  refreshUI: function() {
		if(!this.initialized) return;  	
  	
		var FirePHPWindowTopToolbar = FirePHPChrome.browser$("idFirePHPWindowTopToolbar");

    /* Remove any old items already present in the bar */
    try {
      if(FirePHPWindowTopToolbar.tabMap) {
        for( var name in FirePHPWindowTopToolbar.tabMap ) {
          FirePHPWindowTopToolbar.removeChild(FirePHPWindowTopToolbar.tabMap[name]);
        	/* TODO: Remove item from tabMap */
        }
      }    
    } catch(err) {}

    var panel = {
      name: 'test',
      title: 'Test Button',
      toolbarItemType: 'button'
    };

		FirePHPWindowTopToolbar.addItem(panel);
  },
   
  
  /* Interface: Components.interfaces.nsIDOMEventListener */  
  handleEvent: function ( event ) {

		var FirePHPOverlayWindow = FirePHPChrome.browser$("idFirePHPOverlayWindow");
		var FirePHPWindowTopToolbar = FirePHPChrome.browser$("idFirePHPWindowTopToolbar");

		FirePHPOverlayWindow.style.top = (FirePHPWindowTopToolbar.boxObject.y+FirePHPWindowTopToolbar.boxObject.height)+'px';

		if(FirePHPOverlayWindow.boxObject.height>0) {
			this.animateWindowOverlay('minimize',FirePHPOverlayWindow.boxObject.height);
		} else {
			this.animateWindowOverlay('expand',0);
		}
		
		
		var console = new ConsolePanel();
    console.document = FirePHPChrome.$('idFirePHPConsoleFrame').contentDocument;
		console.panelNode = console.document.getElementById('panelNode');    
    console.log(this);
    console.warn('Yup1');
    console.info('Yup2');
  },
  
	
	animateWindowOverlay: function(mode,index) {

		var FirePHPOverlayWindow = FirePHPChrome.browser$("idFirePHPOverlayWindow");
		
		if(mode=='expand') {
			index = index + 15;
		} else {
			index = index - 15;
		}

		FirePHPOverlayWindow.style.height = index+'px';

		if(mode=='expand') {
			FirePHPOverlayWindow.hidden = false;
			index = index + 5;
			if(index>400) {
				return;
			}
		} else {
			index = index - 5;
			if(index<0) {
				FirePHPOverlayWindow.hidden = true;
				return;
			}
		}
		
    setTimeout('FirePHPChrome.WindowTopToolbar.animateWindowOverlay("'+mode+'",'+index+')', 0);
  }

};
