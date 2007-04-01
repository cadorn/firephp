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


/*
	 WARNING: All function and properties in this object are exposed
   to browser content. It is essential that access to internals is
   restricted and only safe data is provided.
*/


FirePHP.FirePHPChannelAPI = {
	
  getFirePHPVersion: function() {
  	return FirePHP.FirePHPChannel.getFirePHPVersion();
  },

  getExtensionVersion: function() {
  	return FirePHP.FirePHPChannel.getExtensionVersion();
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
