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


require_once('JSON.php');

class org__firephp__Core_class {

  var $version = '0.0.1';

  var $access_key = null;
  var $protocol_mode = 'Header';
  var $temporary_dirpath = './.tmp/';
  var $application_id = 'default';
  var $capabilities_url = null;
  var $request_id = null;
  var $primary_content_type = 'text/html';
  var $time_markers = array();

  var $multipart_requested = false;
  var $multipart_enabled = false;
  var $collection_enabled = false;
  
  var $content_started = false;
  
  /* Default options */
  var $options = 0;
  
  var $variables = array();
  
  
  function setAccessKey($Key) {
    $this->access_key = $Key;
  }
  

  function setApplicationID($ApplicationID) {
    if($this->content_started) {
      trigger_error('Content has already started. You must set the application ID before calling startContent()!');
      return false;
    }

    $this->application_id = $ApplicationID;
  }
  
  function setProtocolMode($Mode) {
    $this->protocol_mode = $Mode;
  }
  
  function setTemporaryDirpath($Dirpath) {
    if($this->content_started) {
      trigger_error('Content has already started. You must set the temporary directory before calling startContent()!');
      return false;
    }

    $this->temporary_dirpath = $Dirpath;
  }
  
  function setCapabilitiesURL($CapabilitiesURL) {
    if($this->content_started) {
      trigger_error('Content has already started. You must set the capabilities URL before calling startContent()!');
      return false;
    }

    $this->capabilities_url = $CapabilitiesURL;
    $this->setHeaderVariable('CapabilitiesURL',$this->capabilities_url);
  }

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
  
  
  /* Check if the browser accepts the FirePHP protocol */
  function doesClientAccept() {
    
    if(preg_match_all("/(^|,|\s)(text\/firephp)($|,|\s)/si",$_SERVER['HTTP_ACCEPT'],$m) &&
       preg_match_all("/(^|\s)(FirePHP\/(\d*\.\d*(\.\d*)?(\.\d*)?))($|\s)/si",$_SERVER['HTTP_USER_AGENT'],$m)) {

      /* @TODO  Check the client extension version and use the appropriate server protocol version */
      return true;
    }
    return false;
  }
  
  function isClientAuthorized($ClientKey) {
    if($ClientKey==$this->access_key) return true;
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
      
      /* Ensure that the request is never cached by the browser */
      header('Last-Modified: '.gmdate('r', time()));
      header('Expires: '.gmdate('r', time()-86400));
      header('Pragma: no-cache');
      header('Cache-Control: no-cache, no-store, must-revalidate, max_age=0');
      header('Cache-Control: post-check=0, pre-check=0'); 
      
      switch($this->protocol_mode) {
        case 'Multipart':
          /* Set the multipart mixed header */
//      if($this->multipart_requested) {
//          header('Content-type: multipart/mixed; boundary="'.$this->request_id.'"');
//      }
//        $this->multipart_enabled = true;
          break;
        case 'Header':
          break;
        case 'Secondary':
          break;
      }
      
        
      $this->collection_enabled = true;
    }
  
    $this->content_started = true;

    if($this->multipart_enabled) {
      print '--'.$this->request_id."\n";
      print 'Content-type: '.$this->primary_content_type."\n";
      print "\n";
    }

    /* Record time of when content started */
    $this->stampTime('org.cadorn.projects.FirePHP','StartPrimaryContent');

    return true;
  }

  function endContent() {

    /* Record time of when content ended */
    $this->stampTime('org.cadorn.projects.FirePHP','EndPrimaryContent');

    if($this->multipart_enabled) {
      print "\n".'--'.$this->request_id."\n";
    }
  }


  function setVariable($Options, $ID, $Value) {
    
    if($Options===true) {
      /* Use the default options */
      $options = $instance->options;
    } else
    if($Options===false) {
      /* Do not use any options */
      $options = 0;
    } else {
      /* Use the options we supplied */

      /* TODO: Check for an option merge option to determine if
       * we should merge these specific options with our default
       * options or replace them.
       * The default is to merge them.
       */

      $options = $this->options | $Options;
    }

    /* TODO: Look at options to gather additional data */
    
    $this->variables[md5(serialize($ID))][] = array($options,$ID,$Value); 
  }


  /* Internal method used to resolve the variable ID's
   * to standard keys, scope and labels used within the
   * FirePHP Extension.
   */
  function resolveVariables() {
    /* Must be subclassed */
  }


  function dumpFirePHPData($Data=false) {
    
    /* If there is no data defined that should be dumped lets just dump the execution time */
    
    if(!$Data) {
      $Data = 'Execution Time: '.$this->getTimeSpan('org.cadorn.projects.FirePHP','StartPrimaryContent','EndPrimaryContent',4);
    }

    /* Resolve any variables that have been set */
    $this->resolveVariables();

    /* Generate the XML payload */
    $payload = array();
    $payload[] = '<firephp version="0.2">';        
    $payload[] = '<application id="'.$this->application_id.'">';        
    $payload[] = '<request id="'.$this->request_id.'" anchor="'.$this->inspector_target.'">';        
    $payload[] = '<data type="html"><![CDATA['.trim($Data).']]></data>';        
    if($this->variables) {
      $json_service = new Services_JSON();
      foreach( $this->variables as $variable_id => $variable_info ) {
        for( $i=0 ; $i<sizeof($variable_info) ; $i++ ) {
          $payload[] = '<variable id="'.$variable_id.
                            //   '" key="'.$variable_info[$i][2].
                               '" scope="'.$variable_info[$i][3].
                               '" label="'.$variable_info[$i][4].
                               '" options="'.$variable_info[$i][0].
                        '"><![CDATA['.$json_service->encode($variable_info[$i][2]).']]></variable>';
        }
      }
    }
    $payload[] = '</request>';        
    $payload[] = '</application>';        
    $payload[] = '</firephp>';        

    switch($this->protocol_mode) {
      case 'Multipart':
        if($this->multipart_enabled) {
          print 'Content-type: text/firephp'."\n";
          print "\n";
          print implode("\n",$payload);
          print "\n";
          print '--'.$this->request_id.'--'."\n";
        } 
        break;
      case 'Header':
        
        if(headers_sent($file,$line)) {
          trigger_error('Headers already sent in file['.$file.'] line['.$line.']!');
          return false;
        } else { 
          header('X-PINF-org.firephp-Data: '.urlencode(implode('',$payload)));
        }        
        break;
      case 'Secondary':
        break;
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