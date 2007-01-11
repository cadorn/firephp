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
 * Called to initialize the FirePHPServer component
 */


/* Initialize any default constants */

define('PINF-com.googlecode.firephp-BufferOutput',false);


/* Include the mail worker class */

require_once('FirePHP.class.php');


/* Instanciate the FirePHP Class that will do all the work */

$FirePHP =& new com__googlecode__firephp__FirePHP_class();

/* Register the object in the PINF singleton registry */
global $PINF_SINGLETON_OBJECTS;
$PINF_SINGLETON_OBJECTS['com.googlecode.firephp/FirePHP.class'] =& $FirePHP;  

/* First generate a RequestID for this request so that any data
 * can be referenced later and set the ID in the response headers
 */
 
$FirePHP->setRequestID(md5(uniqid(rand(), true)));


/* Register a shutdown function so we can track execution time
 * as well as number of loaded files
 */ 

register_shutdown_function('com__googlecode__firephp__Init_inc__Shutdown');


/* Record time of when execution started */
$FirePHP->stampTime('com.googlecode.firephp','Start');

/* Check if we have the output buffer constant set.
 * If we do we should start buffering the output now.
 * The advantage of output buffering is that we can
 * send more HTTP Response Headers when all the execution
 * is done.
 */

if(constant('PINF-com.googlecode.firephp-BufferOutput')===true) {
  ob_start();
}

/* Set a flag that the content generation has started */

$FirePHP->setContentStarted(true);




function com__googlecode__firephp__Init_inc__Shutdown() {
  global $PINF_SINGLETON_OBJECTS;  

  $FirePHP =& $PINF_SINGLETON_OBJECTS['com.googlecode.firephp/FirePHP.class'];

  $FirePHP->stampTime('com.googlecode.firephp','End');

  $FirePHP->setHeaderVariable('ExecutionTime',$FirePHP->getTimeSpan('com.googlecode.firephp','Start','End',4));

  $FirePHP->setHeaderVariable('IncludedFileCount',sizeof(get_included_files()));

  /* If we had enabled output buffering flush it now */
  if(constant('PINF-com.googlecode.firephp-BufferOutput')===true) {
    ob_end_flush();
  }
}

?>