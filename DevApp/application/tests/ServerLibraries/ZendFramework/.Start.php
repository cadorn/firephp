<?php

$writer = new Zend_Log_Writer_Firebug();
$logger = new Zend_Log($writer);

$request = new Zend_Controller_Request_Http();
$response = new Zend_Controller_Response_Http();
$channel = Zend_Wildfire_Channel_HttpHeaders::getInstance();
$channel->setRequest($request);
$channel->setResponse($response);

        