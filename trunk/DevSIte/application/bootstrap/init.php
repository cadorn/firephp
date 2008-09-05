<?php

define('ZF_APPLICATION_DIRECTORY', dirname(dirname(__FILE__)));
define('ZF_LIBRARY_DIRECTORY', dirname(dirname(dirname(__FILE__))) . '/library');

set_include_path(ZF_LIBRARY_DIRECTORY);

require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload();


$writer = new Zend_Log_Writer_Firebug();
$logger = new Zend_Log($writer);

Zend_Registry::set('logger', $logger);


function fb($message, $label=null)
{
    if ($label!=null) {
        $message = array($label,$message);
    }
    Zend_Registry::get('logger')->debug($message);
}