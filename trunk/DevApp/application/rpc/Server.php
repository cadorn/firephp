<?php

class Server
{

    public function bar()
    {      
        
        
        
$db = Zend_Db::factory('PDO_SQLITE',
                       array('dbname' => ':memory:'));

$profiler = new Zend_Db_Profiler_Firebug('Queries');
$profiler->setEnabled(true);
$db->setProfiler($profiler);


$db->getConnection()->exec('CREATE TABLE foo (
                              id      INTEGNER NOT NULL,
                              col1    TEXT NOT NULL,
                              col2    TEXT NOT NULL
                            )');

$data = file_get_contents(ZF_APPLICATION_DIRECTORY . '/resources/Test.txt.zip');


$db->insert('foo', array('id'=>1,'col1'=>'test text','col2'=>$data));        
        
        
        fb('Hello World');

        return 'Hello World';
    }

}