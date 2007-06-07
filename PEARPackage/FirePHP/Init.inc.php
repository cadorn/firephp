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


/*
 * Called to initialize the FirePHP API
 */


/* Include the main worker class */

require_once('Core.class.php');


global $FirePHP;
$FirePHP =& new org__firephp__Core_class();


class FirePHP {
  
  
  function Init($Options=null) {
    global $FirePHP;
    
    if($Options===null) {
      $Options = array('ApplicationID' => 'Default',
                       'RequestID' => md5(uniqid(rand(), true)),
                       'AccessKeyValue' => $_COOKIE['FirePHP-AccessKey'],
                       'InspectorTarget' => 'Default',
                       'ContentType' => 'text/html',
                       'ProtocolMode' => 'Header',
                       'RegisterShutdown' => true,
                       'StartContent' => true,
                       'BufferOutput' => true,
                       'SetCacheControlHeaders' => true,
                       'DefaultVariables' =>
                          array(array(true,array('REQUEST','$_GET'),$_GET),
                                array(true,array('REQUEST','$_POST'),$_POST),
                                array(true,array('REQUEST','$_COOKIE'),$_COOKIE),
                                array(true,array('REQUEST','$_SERVER'),$_SERVER)
                               )
                      );    
    }

    /* First check if FirePHP is supported by the client */
    if(!$FirePHP->doesClientAccept()) return false;

    /* Then check if the client is authorized */
    if(!$FirePHP->isClientAuthorized($Options['AccessKeyValue'])) return false;
    
    /* Generate a unique RequestID for this request so that any data
     * can be referenced later and set the ID in the response headers
     */     
    $FirePHP->setRequestID($Options['RequestID']);

    /* Set the primary content type for the response data.
     * This is only applicable if the multipart/mixed response
     * type is used
     */
    $FirePHP->setPrimaryContentType($Options['ContentType']);   
    
    /* Set the response protocol to the default header option */
    $FirePHP->setProtocolMode($Options['ProtocolMode']);
    
    /* Set the ID of the application */
    $FirePHP->setApplicationID($Options['ApplicationID']);
    
    /* Set the Inspector target which groups the requests */
    $FirePHP->setInspectorTarget($Options['InspectorTarget']);
    
    /* Set cache control header flag */
    $FirePHP->setCacheControlHeaders($Options['SetCacheControlHeaders']);
    
    /* Register a shutdown function to send FirePHP at the end of the request */
    if($Options['RegisterShutdown']) {
      register_shutdown_function('FirePHP_Shutdown');
    }
      
    /* Record some default variables */
    if($Options['DefaultVariables']) {
      foreach( $Options['DefaultVariables'] as $variable ) {
        FirePHP::SetVariable($variable[0],$variable[1],$variable[2]);
      }
    }

    /* Indicate to FirePHP that the content will now start */
    if($Options['StartContent']) {
      $FirePHP->startContent();
    }

    /* Start output buffering */
    if($Options['BufferOutput']) {
      ob_start();
    }
  }
  
  function Shutdown() {
    global $FirePHP;

    $FirePHP->endContent();
    $FirePHP->dumpFirePHPData();
  }

  
  function SetAccessKey($Key) {
    global $FirePHP;
    $FirePHP->setAccessKey($Key);
  }

  function SetVariable($Options, $ID, $Value) {
    global $FirePHP;
    $FirePHP->setVariable($Options, $ID, $Value);
  }

  function resolveVariables() {
    if(!$this->variables) return;
    foreach( $this->variables as $variable_id => $variable_info ) {
      for( $i=0 ; $i<sizeof($variable_info) ; $i++ ) {
        $options = $variable_info[$i][0];
        $id = $variable_info[$i][1];
 
        $this->variables[$variable_id][$i][3] = $id[0];   /* Scope */
        $this->variables[$variable_id][$i][4] = $id[2].($id[3])?'('.$id[3].')':'';  /* Label & Sub-Label */
      }
    }
  }
}

function FirePHP_Shutdown() {
  FirePHP::Shutdown();
}

?>