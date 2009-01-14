<?php

require_once dirname(__FILE__).'/.Start.php';

$logger = Zend_Registry::get('logger');


$logger->setEventItem('firebugLabel','Test Label');

$logger->debug('Test debug message');

$logger->info('Test info message');

$logger->err('Test error message');


require_once dirname(__FILE__).'/.End.php';

