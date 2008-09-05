<?php

define('ZF_APPLICATION_DIRECTORY', dirname(dirname(__FILE__)));
define('ZF_LIBRARY_DIRECTORY', dirname(ZF_APPLICATION_DIRECTORY) . '/library'
                               . PATH_SEPARATOR . ZF_APPLICATION_DIRECTORY . '/functions');

set_include_path(ZF_LIBRARY_DIRECTORY);

require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload();


$writer = new Zend_Log_Writer_Firebug();
$logger = new Zend_Log($writer);

Zend_Registry::set('logger', $logger);


require_once('fb.php');
