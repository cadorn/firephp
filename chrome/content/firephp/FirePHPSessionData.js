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



FirePHP.FirePHPSessionData = function FirePHPSessionData() {

  this.applicationID = null;
  this.variables = new Array();
  
  this.setApplicationID = function(ApplicationID) {
    this.applicationID = ApplicationID;
  }
  this.setVariable = function(RequestData,VariableData) {
    if(!this.variables[VariableData[0]]) {
      this.variables[VariableData[0]] = new Array();
    }
    this.variables[VariableData[0]] = new Array(RequestData,VariableData);
  }
  this.getVariables = function(Key) {
    if(Key) {
      return this.variables[Key];
    } else {
      return this.variables;
    }
  }
};



FirePHP.FirePHPSessionHandler = {

  data: new Array(),
  
  getVariables: function(ApplicationID) {
    if(!ApplicationID || !this.data[ApplicationID]) return null;
    return this.data[ApplicationID].variables;
  },

  getVariable: function(ApplicationID,Key) {
    if(!ApplicationID || !this.data[ApplicationID]) return null;
    return this.data[ApplicationID].getVariables(Key);
  },

  setVariable: function(ApplicationID,RequestData,VariableData) {
    if(!ApplicationID) return null;
    var data = this.data[ApplicationID];
    if(!data) {
      data = this.data[ApplicationID] = new FirePHP.FirePHPSessionData();
      data.setApplicationID(ApplicationID);
    }
    data.setVariable(RequestData,VariableData);
  }
}
