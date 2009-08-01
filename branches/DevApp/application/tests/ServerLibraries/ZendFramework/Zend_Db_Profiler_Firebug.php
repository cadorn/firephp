<?php

require_once dirname(__FILE__).'/.Start.php';


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

