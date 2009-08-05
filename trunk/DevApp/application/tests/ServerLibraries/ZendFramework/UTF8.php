<?php


require_once dirname(__FILE__).'/.Start.php';

Zend_Wildfire_Plugin_FirePhp::send(array("Отладочный"),'UTF-8 characters with json_encode()',Zend_Wildfire_Plugin_FirePhp::LOG);
Zend_Wildfire_Plugin_FirePhp::send("Отладочный",'UTF-8 characters with json_encode()',Zend_Wildfire_Plugin_FirePhp::LOG);

Zend_Wildfire_Plugin_FirePhp::send(array('Test work. Проверка работы.'),'UTF-8 characters with json_encode()',Zend_Wildfire_Plugin_FirePhp::LOG);
Zend_Wildfire_Plugin_FirePhp::send('Test work. Проверка работы.','UTF-8 characters with json_encode()',Zend_Wildfire_Plugin_FirePhp::LOG);


Zend_Json::$useBuiltinEncoderDecoder = true;

Zend_Wildfire_Plugin_FirePhp::send(array("Отладочный"),'UTF-8 characters with Zend_Json_Encoder::encode()',Zend_Wildfire_Plugin_FirePhp::LOG);
Zend_Wildfire_Plugin_FirePhp::send("Отладочный",'UTF-8 characters with Zend_Json_Encoder::encode()',Zend_Wildfire_Plugin_FirePhp::LOG);

Zend_Wildfire_Plugin_FirePhp::send(array('Test work. Проверка работы.'),'UTF-8 characters with Zend_Json_Encoder::encode()',Zend_Wildfire_Plugin_FirePhp::LOG);
Zend_Wildfire_Plugin_FirePhp::send('Test work. Проверка работы.','UTF-8 characters with Zend_Json_Encoder::encode()',Zend_Wildfire_Plugin_FirePhp::LOG);

require_once dirname(__FILE__).'/.End.php';

