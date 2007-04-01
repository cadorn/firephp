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



FirePHP.Handlers = {

  definition: null,
  handlers: null,


	initialize: function() {
    /* Lets listen to all Capabilities events */
    FirePHP.FirePHPChannel.addListener("Capabilities", this);
  	FirePHP.FirePHPChannel.addHandler("UI", this);
	},
	

	notifyFirePHPEvent: function(Event,Flags) {
  	switch(Event.getGroup()+'->'+Event.getName()+(((Flags & FirePHP.FirePHPChannel.STATE_AFTER)==FirePHP.FirePHPChannel.STATE_AFTER)?'->STATE_AFTER':'')) {
  		case 'Capabilities->DefinitionChanged->STATE_AFTER':
  	  	var definition = FirePHP.FirePHPCapabilitiesHandler.getDefinition(Event.getValue('CapabilitiesURL'));
				this.setDefinition(definition.getHandlerDefinitions());
  			break;
    }
	},	
	
		
	setDefinition: function(Definition) {
		this.definition = Definition;
		
		/* Based on the definition received lets organize all our handlers */
		
		this.handlers = new Array();
		if(!this.definition.handler[0]) {
		  this.definition.handler = new Array(this.definition.handler);
		}
		for( var index in this.definition.handler ) {
			var handler = this.definition.handler[index];

			this.handlers[handler.observes['-group']]
			if(!this.handlers[handler.observes['-group']]) {
				this.handlers[handler.observes['-group']] = new Array();
			}
			if(!this.handlers[handler.observes['-group']][handler.observes['-name']]) {
				this.handlers[handler.observes['-group']][handler.observes['-name']] = new Array();
			}
			
			this.handlers[handler.observes['-group']][handler.observes['-name']][handler.event['-name']] = handler.event.javascript;
		}		
	},
	
	
	handleFirePHPEvent: function(Event,Flags) {
	
  	switch(Event.getGroup()) {
  		case 'UI':
				try {
  				var handler = this.handlers[Event.getValue('Item').info['group']][Event.getValue('Item').info['name']][Event.getName()];
  				if(handler) {
  					eval(handler);
  				}
  			} catch(err) {}
  			break;
    }
	}
	
}
