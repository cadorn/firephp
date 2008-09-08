<?php

define('ZF_APPLICATION_DIRECTORY', dirname(dirname(dirname(__FILE__))));
define('ZF_LIBRARY_DIRECTORY',     dirname(ZF_APPLICATION_DIRECTORY) . '/library');

set_include_path(ZF_LIBRARY_DIRECTORY);


require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload();


$request = new Zend_Controller_Request_Http();
$response = new Zend_Controller_Response_Http();
$channel = Zend_Wildfire_Channel_HttpHeaders::getInstance();
$channel->setRequest($request);
$channel->setResponse($response);

$writer = new Zend_Log_Writer_Firebug();
$writer->setPriorityStyle(8, 'TABLE');       
    
$logger = new Zend_Log($writer);
$logger->addPriority('TABLE', 8);

$db = Zend_Db::factory('PDO_SQLITE',
                       array('dbname' => ':memory:'));

$profiler = new Zend_Db_Profiler_Firebug('Queries');
$profiler->setEnabled(true);
$db->setProfiler($profiler);


$db->getConnection()->exec('CREATE TABLE foo (
                              id      INTEGNER NOT NULL,
                              col1    VARCHAR(10) NOT NULL
                            )');


$table = array('sdfsdf',array(array('sdfsdf','sdfsdf'),array('sdwt32g2','23g23g')));
$logger->table($table);

$channel->flush();
$channel->getResponse()->sendHeaders();
