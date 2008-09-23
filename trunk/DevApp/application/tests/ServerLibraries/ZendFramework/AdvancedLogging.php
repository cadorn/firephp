<?php

require_once dirname(__FILE__).'/.Start.php';


Zend_Wildfire_Plugin_FirePhp::send('Hello World'); /* Defaults to FirePHP::LOG */

Zend_Wildfire_Plugin_FirePhp::send('Log message', ''  ,Zend_Wildfire_Plugin_FirePhp::LOG);
Zend_Wildfire_Plugin_FirePhp::send('Info message', '' ,Zend_Wildfire_Plugin_FirePhp::INFO);
Zend_Wildfire_Plugin_FirePhp::send('Warn message', '' ,Zend_Wildfire_Plugin_FirePhp::WARN);
Zend_Wildfire_Plugin_FirePhp::send('Error message', '',Zend_Wildfire_Plugin_FirePhp::ERROR);

Zend_Wildfire_Plugin_FirePhp::send('Message with label','Label',Zend_Wildfire_Plugin_FirePhp::LOG);
Zend_Wildfire_Plugin_FirePhp::send(true,'Label',Zend_Wildfire_Plugin_FirePhp::LOG);
Zend_Wildfire_Plugin_FirePhp::send(false,'Label',Zend_Wildfire_Plugin_FirePhp::LOG);
Zend_Wildfire_Plugin_FirePhp::send(null,'Label',Zend_Wildfire_Plugin_FirePhp::INFO);
Zend_Wildfire_Plugin_FirePhp::send(1,'Label',Zend_Wildfire_Plugin_FirePhp::WARN);
Zend_Wildfire_Plugin_FirePhp::send(1.1,'Label',Zend_Wildfire_Plugin_FirePhp::LOG);



Zend_Wildfire_Plugin_FirePhp::send(array('key1'=>'val1',
         'key2'=>array(array('v1','v2'),'v3')),
   'TestArray', Zend_Wildfire_Plugin_FirePhp::LOG);

function test($Arg1) {
  throw new Exception('Test Exception');
}
try {
  test(array('Hello'=>'World'));
} catch(Exception $e) {
  /* Log exception including stack trace & variables */
  Zend_Wildfire_Plugin_FirePhp::send($e);
}

Zend_Wildfire_Plugin_FirePhp::send('Backtrace to here', '',Zend_Wildfire_Plugin_FirePhp::TRACE);

Zend_Wildfire_Plugin_FirePhp::send(array('2 SQL queries took 0.06 seconds',array(
   array('SQL Statement','Time','Result'),
   array('SELECT * FROM Foo','0.02',array('row1','row2')),
   array('SELECT * FROM Bar','0.04',array('row1','row2'))
  )), '',Zend_Wildfire_Plugin_FirePhp::TABLE);

/* Will show only in "Server" tab for the request */
Zend_Wildfire_Plugin_FirePhp::send(apache_request_headers(),'RequestHeaders',Zend_Wildfire_Plugin_FirePhp::DUMP);




require_once dirname(__FILE__).'/.End.php';

