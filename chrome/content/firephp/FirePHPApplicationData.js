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




FirePHP.FirePHPApplicationData = function FirePHPApplicationData() {

  this.url = null;
  this.detectStatus = null;    /* 0 => Detection in progress, 1 => Server Detected, -1 => Server not Detected */
  this.vars = new Array();
  this.tabs = new Array();
  this.selectedTab = null;
  this.changed = false;

  
  this.hasChanged = function() {
    return this.changed;
  }
  this.resetChanged = function() {
    this.changed = false;
  }
  
  this.setURL = function(URL) {
    this.url = URL;
    this.changed = true;
  }
  this.setDetectStatus = function(Status) {
    this.detectStatus = Status;
    this.changed = true;
  }
  
  this.setVar = function(Group,Name,Value) {
    if(!this.vars[Group]) this.vars[Group] = new Array();
    this.vars[Group][Name] = Value;
    this.changed = true;
  }
  
  this.getVar = function(Group,Name) {
    if(!this.vars[Group]) return false;
    return this.vars[Group][Name];
  }

  this.addTab = function(Name,Label,Source,Selected) {
    this.tabs[this.tabs.length] = {name: Name, label: Label, source: Source};
    if(Selected=='true') {
      this.setSelectedTab(Name);
    }
    this.changed = true;
  }

  this.getTabs = function() {
    return this.tabs;
  }
  
  this.setSelectedTab = function(Name) {
    var oo = (this.selectedTab!=Name)
    this.selectedTab = Name;
    /* Return true if the name has changed */
    if(oo) {
      this.changed = true;
    }
    return oo;
  }

  this.getSelectedTab = function() {
    return this.selectedTab;
  }

  this.resetTabs = function() {
    this.tabs = new Array();
    this.selectedTab = null;
    this.changed = true;
  }

};





FirePHP.FirePHPApplicationHandler = {
  

  data: new Array(),


  parseDomainPaths: function(Arg1) {
    var urls;
    if(Arg1 instanceof String || typeof Arg1 == 'string') {
      urls = new Array(Arg1);
    } else
    if(Arg1 instanceof Array || typeof Arg1 == 'array') {
      urls = Arg1;
/*
    } else {
      if(Arg1.window.location.href!='about:blank') {
        url = Arg1.window.location.href;
      }
*/      
    }
    
    if(!urls) return null;
    
    var keys = new Array();
    var url;
    for( var j=0 ; j<urls.length ; j++ ) {
      
      url = urls[j];
      if(url) {

        try {
  
          var m = url.split(new RegExp('\/','g'));
          
          /* Remove the last element which is a trailing slash or the page name */
          m = m.slice(0,m.length-1);
          
          /* If we are not dealing with the HTTP or HTTP protocol we return here */
          if(m[0]!='http:' && m[0]!='https:') return null;
  
          /* Now that we have the path broken up and validated lets generate the keys */
          
          for( var i=3 ; i<=m.length ; i++ ) {

            var key = m.slice(0,i).join('/')+'/';

            keys[key] = key;
          }
  
        } catch(err) {}            
      }
    }
    if(keys) {
      var return_keys = new Array();
      for( var key in keys ) {
        return_keys.push(key);
      }
      return return_keys;
    }
    return null;
  },

  
  getData: function(URL,ExactMatch,ParseURL) {
    if(ExactMatch) {
      var key;
      if(ParseURL) {
        var keys = this.parseDomainPaths(URL);
        if(!keys) return null;
        key = keys[keys.length-1];
      } else {
        key = URL;
      }
      if(!key || !this.data[key]) return null;
      return this.data[key];
    } else {
      var keys = this.parseDomainPaths(URL);
      if(!keys) return null;
      /* Search for all matching URL's */
      var result = new Array();
      for( var i = keys.length-1 ; i>=0 ; i-- ) {
        if(this.data[keys[i]]) {
          result.push(this.data[keys[i]]);
        }
      }
      if(!result) return null;
      return result;
    }
  },


  triggerDetect: function(URL,ForceDetect) {

//dump('URL: '+URL+"\n");    
    var keys = this.parseDomainPaths(URL);
    if(!keys) return false;
    
    /* For each key found lets do a server detection */

    var key;
    var serverData;
    for( var i = 0 ; i<keys.length ; i++ ) {
      
      key = keys[i];
      serverData = this.getData(key,true);
    
      /* If we dont have server data for this domain yet, create an object for it */
      
      if(!serverData) {
        serverData = this.data[key] = new FirePHP.FirePHPApplicationData();
        serverData.setURL(key);
      }

      /* Check the detectStatus of the server data to see if we should trigger
       * a detect or ignore the request. We can also force a detect.
       * 
       * If detectStatus = 0 the server detect is already in progress.
       * This may happen if the detect is triggered multiple times for the same
       * domain in very short intervals.
       * So lets just ignore this request as the original request
       * should complete soon
       */
      if(serverData.detectStatus!=null && ForceDetect!=true) {
        continue; 
      }
      
      /* No detection has been done for this serverContext yet.
       * Lets start a detect
       */
      
      serverData.setDetectStatus(0);
  
      var url = key+'PINF/org.firephp/Capabilities';
//dump('RUN DETECTION: '+url+"\n");      
      try {
  
        var callback =
        {
          success: FirePHP.FirePHPApplicationHandler.parseServerSuccessResponse,
          failure: FirePHP.FirePHPApplicationHandler.parseServerFailureResponse,
          argument: key,
          timeout: 5000,
          scope: FirePHP.FirePHPApplicationHandler
        }
  
        YAHOO.util.Connect.asyncRequest('GET', url, callback, null);
  
      } catch(err) {
        serverData.setDetectStatus(null);
  
        /* The detection request failed. Lets try again as the request should not fail here */
        dump('Error trying to detect FirePHPServer at ['+url+']. We will try again!');
      }
    }  
    return true;
  },
  
  
  parseServerSuccessResponse: function(Response) {
    
    try {

      var key = Response.argument;
      var serverData = this.getData(key,true);

      /* First reset the serverStatus so if the code below fails it can try again */              
      serverData.setDetectStatus(null);

      if(Response.responseXML) {
      
        var res;
        var findPattern = "//firephp/application";
        var nodes = document.evaluate( findPattern, Response.responseXML, null, XPathResult.ORDERED_NODE_ITERATOR_TYPE, null ); 
        if(nodes) {
          while (res = nodes.iterateNext()) {
            if(res.getAttribute('url')==key) {
              serverData.setDetectStatus(1);
              serverData.setVar('Application','Label',res.getAttribute('label'));
            }
          }
        }

        findPattern = "//firephp/application[attribute::url=\""+key+"\"]/toolbar[attribute::name=\"Application\"]/tab";
        nodes = document.evaluate( findPattern, Response.responseXML, null, XPathResult.ORDERED_NODE_ITERATOR_TYPE, null ); 
        if(nodes) {
          /* First reset tabs so we dont add duplicates if we are forcing another detect */
          serverData.resetTabs();
          while (res = nodes.iterateNext()) {
            serverData.addTab(res.getAttribute('name'),res.getAttribute('label'),res.getAttribute('source'),res.getAttribute('selected'));
          }
        }
      }            
    } catch(err) {}

    /* Trigger an update for the FirePHPChrome to ensure the display is consistent with
     * the internal data
     */

    FirePHPChrome.refreshUI();
  },
  
  parseServerFailureResponse: function(Response) {

    try {
    
      var key = Response.argument;
      var serverData = this.getData(key,true);

      /* First reset the serverStatus so if the code below fails it can try again */              
      serverData.setDetectStatus(null);
              
      switch(Response.status) {
        case 0:     /* Communication failure */
        case -1:     /* Transaction aborted */
          serverData.setDetectStatus(-1);
          break;
        case 403:   /* Forbidden */
          /* We were not allowed to read the detection URL on the server.
           * We assume we do not have access to the FirePHPServer and
           * will not try the detection again.
           */
          serverData.setDetectStatus(-1);
          break;
        case 404:   /* Not Found */
          /* The detection URL was not found on the server.
           * We assume no FirePHPServer is setup and will not try
           * the detection again.
           */
          serverData.setDetectStatus(-1);
          break;
        default:
          dump('Got unsupported response status ['+o.status+'] while trying to detect FirePHPServer!');
          break;
      }
    
    } catch(err) {}

    /* Trigger an update for the FirePHPChrome to ensure the display is consistent with
     * the internal data
     */

    FirePHPChrome.refreshUI();
  }
}
