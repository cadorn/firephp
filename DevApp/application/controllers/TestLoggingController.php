<?php

class TestLoggingController extends Zend_Controller_Action
{
  
      
    public function test2PhpAction()
    {

        Zend_Registry::get('logger')->debug('Test Logging Message');      
        $this->view->json('Sample Json Data');


$this->getHelper('Json')->suppressExit = true;
$this->getHelper('Json')->sendJson('Test JSON data');
Zend_Wildfire_Channel_HttpHeaders::getInstance()->flush();
$this->getHelper('Json')->getResponse()->sendResponse();
exit;

/*
        Zend_Registry::get('logger')->debug('Test Logging Message');      
        $this->view->json('Sample Json Data');

        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $viewRenderer->setNoRender(true);
*/        
    }
}

