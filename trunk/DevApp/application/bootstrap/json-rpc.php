<?php

set_include_path(get_include_path()
                 . PATH_SEPARATOR . ZF_APPLICATION_DIRECTORY . '/rpc');

$request = new Zend_Controller_Request_Http();
$response = new Zend_Controller_Response_Http();
$channel = Zend_Wildfire_Channel_HttpHeaders::getInstance();
$channel->setRequest($request);
$channel->setResponse($response);

ob_start();

try {
  
    $server = new Zend_Json_Server();
    $server->setClass($request->getParam('endpoint'))
           ->setTarget('json-rpc/' . $request->getParam('endpoint'))
           ->setEnvelope('JSON-RPC-1.0')
           ->setDojoCompatible(true);
    
    // For GET requests, simply return the service map
    switch($_SERVER['REQUEST_METHOD']) {
       
        case 'GET':
            $smd = $server->getServiceMap();
            header('Content-Type: application/json');
            echo $smd;
            break;
       
        case 'POST':
            $server->handle();
            break;
    }

} catch(Exception $e) {
    $logger->err($e);
}

$channel->flush();
$response->sendHeaders();
