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
	  
	  
	removeKey: function(list, key) {
    /* TODO: Try and figure out a way to do this by reference */
		var new_list = Array();
		for( var item in list ) {
	  	if(item == key) {
	    	/* Don't add element to new array */
	    } else {
	    	new_list[item] = list[item];
	    }
	  }
	  return new_list;
	},
  
  getXMLTreeNodeAttributes: function(Node) {
  	var attributes = new Array();
  	for( var name in Node ) {
  		if(name.substring(0,1)=='-') {
  			attributes[name.substring(1)] = Node[name];
  		}
  	}
  	return attributes;
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
  },
  
  
getProtocol: function(url)
{
    var m = /([^:]+):\/{1,3}/.exec(url);
    return m ? m[1] : "";
},
	
	
  
/**
*
*  URL encode / decode
*  http://www.webtoolkit.info/
*
**/  
// public method for url encoding
	urlEncode : function (string) {
		return escape(this._utf8_encode(string));
	},

	// public method for url decoding
	urlDecode : function (string) {
		return this._utf8_decode(unescape(string));
	},

	// private method for UTF-8 encoding
	_utf8_encode : function (string) {
		string = string.replace(/\r\n/g,"\n");
		var utftext = "";

		for (var n = 0; n < string.length; n++) {

			var c = string.charCodeAt(n);

			if (c < 128) {
				utftext += String.fromCharCode(c);
			}
			else if((c > 127) && (c < 2048)) {
				utftext += String.fromCharCode((c >> 6) | 192);
				utftext += String.fromCharCode((c & 63) | 128);
			}
			else {
				utftext += String.fromCharCode((c >> 12) | 224);
				utftext += String.fromCharCode(((c >> 6) & 63) | 128);
				utftext += String.fromCharCode((c & 63) | 128);
			}

		}

		return utftext;
	},

	// private method for UTF-8 decoding
	_utf8_decode : function (utftext) {
		var string = "";
		var i = 0;
		var c = c1 = c2 = 0;

		while ( i < utftext.length ) {

			c = utftext.charCodeAt(i);

			if (c==43) {
				string += ' ';
				i++;
			} else
			if (c < 128) {
				string += String.fromCharCode(c);
				i++;
			}
			else if((c > 191) && (c < 224)) {
				c2 = utftext.charCodeAt(i+1);
				string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
				i += 2;
			}
			else {
				c2 = utftext.charCodeAt(i+1);
				c3 = utftext.charCodeAt(i+2);
				string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
				i += 3;
			}

		}

		return string;
	} ,
	
	
	renderJSONString: function(arr,level) {
			
		/**
		* Function : dump()
		* Arguments: The data - array,hash(associative array),object
		*    The level - OPTIONAL
		* Returns  : The textual representation of the array.
		* This function was inspired by the print_r function of PHP.
		* This will accept some data as the argument and return a
		* text that will be a more readable version of the
		* array/hash/object that is given.
		*/
		var dumped_text = "";
		if(!level) level = 0;
		
		//The padding given at the beginning of the line.
		var level_padding = "";
		for(var j=0;j<level+1;j++) level_padding += "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		
		if(typeof(arr) == 'object') { //Array/Hashes/Objects
		 for(var item in arr) {
		  var value = arr[item];
		 
		  if(typeof(value) == 'object') { //If it is an array,
		   dumped_text += level_padding + "'" + item + "' ...<br>";
		   dumped_text += FirePHPLib.renderJSONString(value,level+1);
		  } else {
		   dumped_text += level_padding + "'" + item + "' => \"" + value + "\"<br>";
		  }
		 }
		} else { //Stings/Chars/Numbers etc.
		 dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
		}
		return dumped_text;
		} 
  
  
}
