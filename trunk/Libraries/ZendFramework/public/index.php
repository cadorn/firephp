<?php

/* If set to TRUE logger will not log to FirePHP. Debug messages are sent regardless. */

define('LIVE',false);


/* NOTE: You must have the Zend Framework and FirePHP.php core class in your include path! */

set_include_path(dirname(dirname(__FILE__)).'/library'.PATH_SEPARATOR.get_include_path());

require_once 'FirePhp/Core.php';
require_once 'Zend/Registry.php';
require_once 'Zend/Log.php';
require_once 'Zend/Log/Filter/Suppress.php';
require_once 'FirePhp/Log/Writer/FirePhp.php';
require_once 'Zend/Controller/Front.php';
require_once 'Zend/Controller/Request/Http.php';
require_once 'Zend/Controller/Response/Http.php';

/*
 * Initialize the HTTP Request and Response Objects
 */
 
$request = new Zend_Controller_Request_Http();
$response = new Zend_Controller_Response_Http();


/*
 * Initialize FirePHP
 */
 
FirePhp_Core::init($request, $response); 


/*
 * Optionally set custom processor and renderer
 */

//FirePhp_Core::getInstance()->setProcessorURL('http://'.$_SERVER['HTTP_HOST'].'/Libraries/ZendFramework/js/RequestProcessor.js');
//FirePhp_Core::getInstance()->setRendererURL('http://'.$_SERVER['HTTP_HOST'].'/Libraries/ZendFramework/js/ServerNetPanelRenderer.js');


/*
 * Add our FirePHP logger to the registry
 */

$writer = new FirePhp_Log_Writer_FirePhp();
$logger = new Zend_Log($writer);
        
$filter = new Zend_Log_Filter_Suppress();
$filter->suppress(LIVE);
$logger->addFilter($filter); 

Zend_Registry::set('logger',$logger);


/*
 * Run the front controller
 */
 
$controller = Zend_Controller_Front::getInstance();
$controller->setControllerDirectory('../application/controllers');
$controller->dispatch($request, $response);

