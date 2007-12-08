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


var externalMode = (window.location == "chrome://firebug/content/firebug.xul");


if(externalMode) {
	var detachArgs = window.arguments[0];
	var FBL = detachArgs.FBL;            
	var Firebug = detachArgs.Firebug;
}


FBL.ns(function() { with (FBL) {


const nsIPrefBranch2 = FirebugLib.CI("nsIPrefBranch2");
const nsIPermissionManager = FirebugLib.CI("nsIPermissionManager");

const PrefService = FirebugLib.CC("@mozilla.org/preferences-service;1");
const PermManager = FirebugLib.CC("@mozilla.org/permissionmanager;1");

// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 

const prefs = PrefService.getService(nsIPrefBranch2);
const pm = PermManager.getService(nsIPermissionManager);

var FirePHP = top.FirePHP = {

  version: '0.0.5',

  initialize: function() {

    /* Set the FirePHP Extension version for the FirePHP Service Component */  
    try {
      Components.classes['@firephp.org/service;1'].getService(Components.interfaces.nsIFirePHP).setExtensionVersion(this.version);
    } catch (err) {}


		this.enable();

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
	
  openPermissions: function()
  {
      var params = {
          permissionType: "firephp",
          windowTitle: "FirePHP Allowed Sites",
          introText: "Choose which web sites are allowed to be used with FirePHP.",
          blockVisible: true, sessionVisible: false, allowVisible: true, prefilledHost: ""
      };

      FirebugLib.openWindow("Browser:Permissions", "chrome://browser/content/preferences/permissions.xul",
          "", params);
  },
	
  isURIAllowed: function(host)
  {
    var ioService = FirebugLib.CCSV("@mozilla.org/network/io-service;1", "nsIIOService");
    var uri = ioService.newURI('http://'+host, null, null);
    return uri && 
        (pm.testPermission(uri, "firephp") == nsIPermissionManager.ALLOW_ACTION);
  },

  enableSite: function(host)
  {
    var ioService = FirebugLib.CCSV("@mozilla.org/network/io-service;1", "nsIIOService");

    var uri = ioService.newURI('http://'+host, null, null);
    pm.add(uri, "firephp", nsIPermissionManager.ALLOW_ACTION);
  }	
	
}




Firebug.FirePHP = extend(Firebug.Module,
{
  enable: function()
  {
		FirePHP.enable();
  },
  
  disable: function()
  {
		FirePHP.disable();
  }		
		   
});

Firebug.registerModule(Firebug.FirePHP);

}});
    
