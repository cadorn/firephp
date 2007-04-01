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


FirePHP.FirePHPChannel = {

	STATE_BEFORE: 1,
	STATE_AFTER: 3,
	
	
  getFirePHPVersion: function() {
  	return Firebug.version;
  },

  getExtensionVersion: function() {
  	return FirePHP.version;
  },

  
  /* All FirePHP events are run through this channel
   * and may be listened to, modified and handled
   * by registering appropriate listeners and handlers.
   * This allows for complete customization of every
   * aspect of FirePHP by simply hooking in custom
   * javascript code from other extensions or
   * even content pages loaded in the browser.
   * NOTE: Only content pages that have a base URL
   * as specified in a Capabilities Definition may
   * make changes to this channel. Even with this
   * restriction however it may still pose a security
   * issue if users are able to post arbitrary
   * javascript code into your content pages.
   */

  listeners: new Array(),
  handlers: new Array(),

  /* Passes the Event object to all listeners and
   * finally to all event handlers if the evnt makes
   * it that far
   */
  triggerEvent: function(Event) {
  	
  	/* First notify all listeners that an event is
  	 * about to be handled
  	 */
  	if(this.listeners[Event.getGroup()]) {
			for( var i=0 ; i<this.listeners[Event.getGroup()].length ; i++ ) {
				this.listeners[Event.getGroup()][i].notifyFirePHPEvent(Event,this.STATE_BEFORE);
			}
		}
		/* Then trigger all event handlers */
		if(this.handlers[Event.getGroup()]) {
			for( var i=0 ; i<this.handlers[Event.getGroup()].length ; i++ ) {
				this.handlers[Event.getGroup()][i].handleFirePHPEvent(Event,0);
			}
		}
  	/* Last notify all listeners that an event has been handled */
		if(this.listeners[Event.getGroup()]) {
			for( var i=0 ; i<this.listeners[Event.getGroup()].length ; i++ ) {
				this.listeners[Event.getGroup()][i].notifyFirePHPEvent(Event,this.STATE_AFTER);
			}
		}  	
  },
 	
 	/* Add a new event listener. Duplicate listeners
 	 * get discarded.
 	 */
	addListener: function(EventGroup,Listener) {
		if(!this.listeners[EventGroup]) {
			this.listeners[EventGroup] = new Array();
		}
		/* Ensure listener does not already exist */
		for( var i=0 ; i<this.listeners[EventGroup].length ; i++ ) {
			if(this.listeners[EventGroup][i]==Listener) return;
		}
		this.listeners[EventGroup][this.listeners[EventGroup].length] = Listener;
	},

	/* Remove and event listener */
	removeListener: function(EventGroup,Listener) {
		if(!this.listeners | !this.listeners[EventGroup]) return;
		for( var i=0 ; i<this.listeners[EventGroup].length ; i++ ) {
			if(this.listeners[EventGroup][i]==Handler) {
				array_slice(this.listeners[EventGroup],i,1);
			}
		}
	},
	
	/* Add a new event handler. Duplicate handlers
	 * get discarded.
	 */
	addHandler: function(EventGroup,Handler) {
		if(!this.handlers[EventGroup]) {
			this.handlers[EventGroup] = new Array();
		}
		/* Ensure handler does not already exist */
		for( var i=0 ; i<this.handlers[EventGroup].length ; i++ ) {
			if(this.handlers[EventGroup][i]==Handler) return;
		}
		this.handlers[EventGroup][this.handlers[EventGroup].length] = Handler;
	},
  
	/* Remove and event handlers */
	removeHandler: function(EventGroup,Handler) {
		if(!this.handlers | !this.handlers[EventGroup]) return;
		for( var i=0 ; i<this.handlers[EventGroup].length ; i++ ) {
			if(this.handlers[EventGroup][i]==Handler) {
				array_slice(this.handlers[EventGroup],i,1);
			}
		}
	},
  
  
  
	/* Parse a server response and return the content
	 * if it is not a FirePHP response its easy
	 * if it is a FirePHP response return the content
	 * part only.
	 */
	parseContentResponse: function(Data) {
		/* The data only contains the multipart sections
		 * if it is a multipart message and does not contain
		 * the header indicating that it is a multipart message/
		 * This we search for a specific FirePHP string that
		 * is always available if it is a FirePHP multipart
		 * response.
		 */
		if(!Data) return Data;
		try {
			var re = new RegExp('\n--([^\n\s]*)\nContent-type: text\/firephp\n','gi');	
			var part = Data.split(new RegExp('--'+re.exec(Data)[1],'g'))[1];
			return part.substring(part.indexOf('\n\n')+2,part.length-1);
		} catch(err) {}
		return Data;
	}
}


function FirePHPChannelEvent(EventGroup,EventName,Values) {
	
	this.group = EventGroup;
	this.name = EventName;	
	this.values = Values;
	
	this.getName = function() {
		return this.name;
	};
	this.getGroup = function() {
		return this.group;
	};
	this.getValue = function(Name) {
		try {
			return this.values[Name];
		} catch(err) {}
		return null;
	};
	
	this.trigger = function() {
		FirePHP.FirePHPChannel.triggerEvent(this);
	};
}
