<?php

require_once dirname(__FILE__).'/.Start.php';


Zend_Wildfire_Plugin_FirePhp::group('Group 1');

Zend_Wildfire_Plugin_FirePhp::send('Log message 1', ''  ,Zend_Wildfire_Plugin_FirePhp::LOG);

Zend_Wildfire_Plugin_FirePhp::group('Group 2');

Zend_Wildfire_Plugin_FirePhp::send('Log message 2', ''  ,Zend_Wildfire_Plugin_FirePhp::INFO);

Zend_Wildfire_Plugin_FirePhp::send('Backtrace to here', '',Zend_Wildfire_Plugin_FirePhp::TRACE);

Zend_Wildfire_Plugin_FirePhp::groupEnd();

Zend_Wildfire_Plugin_FirePhp::send('Log message 3', 'Label'  ,Zend_Wildfire_Plugin_FirePhp::WARN);

Zend_Wildfire_Plugin_FirePhp::groupEnd();



require_once dirname(__FILE__).'/.End.php';

