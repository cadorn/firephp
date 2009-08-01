<?php

require_once dirname(__FILE__).'/.Start.php';

$firephp = Zend_Wildfire_Plugin_FirePhp::getInstance();


Zend_Wildfire_Plugin_FirePhp::send('Hello World', 'Label', Zend_Wildfire_Plugin_FirePhp::LOG);

Zend_Wildfire_Plugin_FirePhp::send('', 'Trace to here', Zend_Wildfire_Plugin_FirePhp::TRACE);

try {
  test();
} catch(Exception $e) {
  Zend_Wildfire_Plugin_FirePhp::send($e);
}

function test() {
  throw new Exception('Test Exception');
}


$logger = Zend_Registry::get('logger');
$logger->info('Test info message');

$logger->addPriority('TRACE', 8);
$writer->setPriorityStyle(8, 'TRACE');
$logger->trace('Trace to here');


$profiler = new Zend_Db_Profiler_Firebug('All DB Queries');
$profiler->setEnabled(true);
$db = Zend_Db::factory('PDO_SQLITE', array('dbname' => ':memory:'));
$db->setProfiler($profiler);


$db->getConnection()->exec('CREATE TABLE foo (
                              id      INTEGNER NOT NULL,
                              col1    VARCHAR(10) NOT NULL  
                            )');

$db->insert('foo', array('id'=>1,'col1'=>'original'));


require_once dirname(__FILE__).'/.End.php';

