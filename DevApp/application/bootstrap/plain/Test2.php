<?php

define('ZF_APPLICATION_DIRECTORY', dirname(dirname(dirname(__FILE__))));
define('ZF_LIBRARY_DIRECTORY',     dirname(ZF_APPLICATION_DIRECTORY) . '/library');

set_include_path(ZF_LIBRARY_DIRECTORY
                 . PATH_SEPARATOR . ZF_APPLICATION_DIRECTORY . '/plugins');


require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload();


$logger = new Zend_Log($writer);

Zend_Registry::set('logger', $logger);



$controller = Zend_Controller_Front::getInstance();
$controller->setControllerDirectory(ZF_APPLICATION_DIRECTORY.'/controllers')
           ->setDefaultControllerName('TestLogging')
           ->setDefaultAction('index')
           ->setParam('useDefaultControllerAlways',true);

$loggingPlugin = new Controller_LoggingPlugin();

$controller->registerPlugin($loggingPlugin);
  
$controller->dispatch();

