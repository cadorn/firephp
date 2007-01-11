<?php
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


class com__googlecode__firephp__FirePHP_class {
  
  
  var $request_id = null;
  var $time_markers = array();
  var $content_started = false;
    
  
  function setRequestID($RequestID) {
    $this->request_id = $RequestID;
    
    $this->setHeaderVariable('RequestID',$this->request_id);
  }
  function getRequestID() {
    return $this->request_id;
  }
  
  
  function setContentStarted($ContentStarted) {
    $this->content_started = $ContentStarted;
  }
  
  
  
  function setHeaderVariable($Name,$Value) {

    /* Only set headers if the content has not already started
     * or if we are buffering the output
     */

    if(constant('PINF-com.googlecode.firephp-BufferOutput')===true ||
       $this->content_started===false) {

      header('PINF-com.googlecode.firephp-'.$Name.': '.$Value);
    }
  }  
  
  
  
  function stampTime($Group,$Marker) {
    $this->time_markers[$Group][$Marker] = microtime();
  }
  function getTimeSpan($Group,$StartMarker,$EndMarker,$Precision=4) {
    
    $start_time = explode(' ',$this->time_markers[$Group][$StartMarker]);
    $start_time = $start_time[1].substr($start_time[0], 1);

    $end_time = explode(' ',$this->time_markers[$Group][$EndMarker]);
    $end_time = $end_time[1].substr($end_time[0], 1);

    return round($end_time-$start_time,$Precision);    
  }
  
  
}

?>