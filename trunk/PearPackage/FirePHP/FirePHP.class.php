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

  var $version = '0.0.1';

  var $request_id = null;
  var $primary_content_type = 'text/html';
  var $time_markers = array();

  var $multipart_requested = false;
  var $multipart_enabled = false;
  var $collection_enabled = false;
  
  var $content_started = false;
  

  function setRequestID($RequestID) {
    if($this->content_started) {
      trigger_error('Content has already started. You set the request ID before calling startContent()!');
      return false;
    }
    
    $this->request_id = $RequestID;
    $this->setHeaderVariable('RequestID',$this->request_id);
    
    return true;
  }
  function getRequestID() {
    return $this->request_id;
  }
  
  
  /* Check if the browser accepts multipart/firephp server responses */
  function doesBrowserAccept() {
    if(preg_match_all("/(^|,|\s)(text\/firephp)($|,|\s)/si",$_SERVER['HTTP_ACCEPT'],$m)) {
      return true;
    }
    return false;
  }
  
  /* Enable the multipart/firephp server response by setting the header
   * and keeping a flag that we can collect the data.
   * There is no need to collect the data and slow down PHP if we cannot
   * even send it.
   */ 
  function enableMultipartData() {
    if($this->content_started) {
      trigger_error('Content has already started. You must enable multipart data before calling startContent()!');
      return false;
    }

    if(headers_sent($file,$line)) {
      trigger_error('Headers already sent in file['.$file.'] line['.$line.']!');
      return false;
    } else { 
      
      if(!$this->request_id) {
        trigger_error('The RequestID must be set first before enabeling the multipart response!');
        return false;
      }
      
      $this->multipart_requested = true;
      return true;
    }
  }

  function setHeaderVariable($Name,$Value) {
    if($this->content_started) {
      trigger_error('Content has already started. You must set header variables before calling startContent()!');
      return false;
    }
    if(headers_sent($file,$line)) {
      trigger_error('Headers already sent in file['.$file.'] line['.$line.']!');
      return false;
    } else { 
      header('X-PINF-org.firephp-'.$Name.': '.$Value);
      return true;
    }
  }  
  
  /* Tells FirePHP where to anchor the data in the inspector */
  function setInspectorTarget($InspectorTarget) {
    $this->inspector_target = $InspectorTarget;
  }

  function setPrimaryContentType($Type) {
    if($this->content_started) {
      trigger_error('Content has already started. You must set the content type before calling startContent()!');
      return false;
    }
    $this->primary_content_type = $Type;
    return true;
  }
  
  
  function startContent($PrimaryContentType=false,$InspectorTarget=false) {
    
    if($PrimaryContentType) {
      if(!$this->setPrimaryContentType($PrimaryContentType)) return false;
    }
    if($InspectorTarget) {
      $this->setInspectorTarget($InspectorTarget);
    }

    if(headers_sent($file,$line)) {
      trigger_error('Headers already sent in file['.$file.'] line['.$line.']!');
      return false;
    } else { 

      if($this->multipart_requested) {
        /* Set the multipart mixed header */
        header('Content-type: multipart/mixed; boundary="'.$this->request_id.'"');
        /* Ensure that the request is never cached by the browser */
        header('Last-Modified: '.gmdate('r', time()));
        header('Expires: '.gmdate('r', time()-86400));
        header('Pragma: no-cache');
        header('Cache-Control: no-cache, no-store, must-revalidate, max_age=0');
        header('Cache-Control: post-check=0, pre-check=0'); 
        
        $this->multipart_enabled = true;
        $this->collection_enabled = true;
      }
    }
  
    $this->content_started = true;

    if($this->multipart_enabled) {
      print '--'.$this->request_id."\n";
      print 'Content-type: '.$this->primary_content_type."\n";
      print "\n";
    }

    /* Record time of when content started */
    $this->stampTime('com.googlecode.firephp','StartPrimaryContent');

    return true;
  }

  function endContent() {

    /* Record time of when content ended */
    $this->stampTime('com.googlecode.firephp','EndPrimaryContent');

    if($this->multipart_enabled) {
      print "\n".'--'.$this->request_id."\n";
    }
  }


  function dumpFirePHPData($Data=false) {
    
    /* If there is no data defined that should be dumped lets just dump the execution time */
    
    if(!$Data) {
      $Data = 'Execution Time: '.$this->getTimeSpan('com.googlecode.firephp','StartPrimaryContent','EndPrimaryContent',4);
    }
    
    if($this->multipart_enabled) {
      print 'Content-type: text/firephp'."\n";
      print "\n";
      /* Lets construct the default XML envelope */
      print '<firephp>'."\n";        
      print '<request id="'.$this->request_id.'" anchor="'.$this->inspector_target.'">'."\n";        
      print '<data type="html"><![CDATA['.trim($Data).']]></data>'."\n";        
      print '</request>'."\n";        
      print '</firephp>'."\n";        
      print '--'.$this->request_id.'--'."\n";
    }
  }
  
  
  
  function stampTime($Group,$Marker) {
    if(!$this->collection_enabled) return false;
    $this->time_markers[$Group][$Marker] = microtime();
    return true;
  }
  function getTimeSpan($Group,$StartMarker,$EndMarker,$Precision=4) {
    if(!$this->collection_enabled) return false;
    
    $start_time = explode(' ',$this->time_markers[$Group][$StartMarker]);
    $start_time = $start_time[1].substr($start_time[0], 1);

    $end_time = explode(' ',$this->time_markers[$Group][$EndMarker]);
    $end_time = $end_time[1].substr($end_time[0], 1);

    return round($end_time-$start_time,$Precision);    
  }
  
  
}

?>