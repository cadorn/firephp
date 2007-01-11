<?php

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