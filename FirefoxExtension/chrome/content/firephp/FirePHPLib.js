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


var FirePHPLib = top.FirePHPLib = {


  createMenuItem: function(popup, item) {
    
    var menuitem = popup.ownerDocument.createElement("menuitem");

    var label = item.label;
    
    menuitem.setAttribute("label", label);
    menuitem.setAttribute("type", item.type);
    menuitem.setAttribute("value", item.value);
    if (item.checked)
        menuitem.setAttribute("checked", "true");
    if (item.disabled)
        menuitem.setAttribute("disabled", "true");
    
    if (item.command)
        menuitem.addEventListener("command", item.command, false);

    popup.appendChild(menuitem);
    return menuitem;
  },

  
  extend: function(l, r) {
    var newOb = {};
    for (var n in l)
        newOb[n] = l[n];
    for (var n in r)
        newOb[n] = r[n];
    return newOb;
  },
  
  bindFixed: function() {
      var args = cloneArray(arguments), fn = args.shift(), object = args.shift();
      return function() { return fn.apply(object, args); }
  },
  
  /* Prints the given object to the console */
  dump: function(Object,Name,Filter,IncludeValues) {
    dump('Var: '+Name+' ['+Object+']'+"\n");
    if(!Object) return;
    
    var list = new Array();
    for( var name in Object ) {
    
      if(Filter) {
        if(name.substring(0,Filter.length)==Filter) {
          list[list.length] = name;
        }
      } else {
        list[list.length] = name;
      }
    }
    
    if(!list) return;    
    
    list.sort();

    dump(' {'+"\n");
    
    for( var name in list ) {
      if(IncludeValues) {
        dump('  '+list[name]+' = '+Object[list[name]]+"\n");
      } else {
        dump('  '+list[name]+"\n");
      }
    }
    dump(' }'+"\n");
  }
}
