<?php

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