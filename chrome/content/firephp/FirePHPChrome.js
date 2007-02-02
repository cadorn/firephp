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
  
  
  /* Get a reference to an object by ID
   * This takes into account if Firebug is running within the
   * main window or the external window
   */
  $: function(Name) {
    return FirebugChrome.getCurrentBrowser().chrome.$(Name);
  },
  /* Get a reference to the active Firebug document
   * This takes into account if Firebug is running within the
   * main window or the external window
   */
  getDocument: function() {
    return FirebugChrome.getCurrentBrowser().chrome.window.document;
  },
  
  
  
  /* This method is called whenever Firebug tabs change
   */
  refreshContext: function() {

    dump('FirePHPChrome.refreshContext()'+"\n");
    
    /* Check to see if our panel is showing and
     * show/hide our additional toolbars and panels accordingly
     */

    var FirePHPBottomToolbar = FirePHPChrome.$("idFirePHPBottomToolbar");

    if(this.getPanel().visible==true) {
      /* Our panel is showing */
      
      FirePHPBottomToolbar.hidden = false;

    } else {
      /* Our panel is not showing */

      FirePHPBottomToolbar.hidden = true;
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

    dump('FirePHPChrome.triggerRefreshUI()'+"\n");

    setTimeout(FBL.bindFixed(function() { FirePHPChrome.refreshUI(); }, Context));
  },
   
  refreshUI: function() {

    dump('FirePHPChrome.refreshUI()'+"\n");
    
    /* Check to see if our panel is showing
     * We only want to continue if it is showing
     */

    if(!this.getPanel().visible) return;

    /* Trigger a refresh of the BottomToolbar */
    FirePHPChrome.BottomToolbar.refreshUI();
    
    /* Trigger a refresh of the FirePHP Panel */
    this.getPanel().refreshUI();
  },

  
  /* Returns the FirePHP Panel */
  getPanel: function() {
    if(!FirebugContext) return false;
    return FirebugContext.getPanel("FirePHP");
  }
  
}

