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

FirePHPChrome.UI.Slideout = {

	/* Global variables across all slideout containers */
	
	global_active_slideout: null,
	
	/* Slideout container specific variables */

	name: null,
	definition: null,
	container_element: null,
	
	slideouts: null,
	
	parent_window_width: null,
	

	_buildUI: function(Name,Definition,ContainerElement) {
		this.name = Name;
		this.definition = Definition;
		this.container_element = ContainerElement;

		this.slideouts = new Array();

		for( var index in this.definition.slideout ) {
			var info = FirePHPLib.getXMLTreeNodeAttributes(this.definition.slideout[index]);
		  this.slideouts[info['name']] = info;
		}
	},
  	
	open: function(Name) {
		
		var slideout = this.slideouts[Name];
		if(!slideout) return false;
		
		this.global_active_slideout = slideout;
		
		
		/* Set width */
	  this.container_element.style.width = this.global_active_slideout['width']+'px';
		
		/* Set X offset */
		if(this.parent_window_width) {
		  this.container_element.style.left = ((this.parent_window_width-this.global_active_slideout['width'])/2)+'px';

dump('this.container_element.style.left: '+this.container_element.style.left+"\n");		


		}
		
		
		if(this.container_element.boxObject.height>0) {
			this.animateWindowOverlay('minimize',this.container_element.boxObject.height);
		} else {
			this.animateWindowOverlay('expand',0);
		}
  
    this.container_element.childNodes[0].loadURIWithFlags(this.global_active_slideout['source'], Components.interfaces.nsIWebNavigation.LOAD_FLAGS_BYPASS_CACHE, null, null, null);

	},
  
  close: function() {
  },
 
	
	animateWindowOverlay: function(mode,index) {
		
		if(mode=='expand') {
			index = index + 15;
		} else {
			index = index - 15;
		}

		this.container_element.style.height = index+'px';

		if(mode=='expand') {
			this.container_element.hidden = false;
			index = index + 5;
			if(index>this.global_active_slideout['height']) {
				return;
			}
		} else {
			index = index - 5;
			if(index<0) {
				this.container_element.hidden = true;
				return;
			}
		}
		
    setTimeout('FirePHPChrome.UI.'+this.name+'.animateWindowOverlay("'+mode+'",'+index+')', 0);
	}

};
