<?php

Zend_Layout::startMvc(array(
    'layoutPath' => ZF_APPLICATION_DIRECTORY . '/layouts',
    'layout' => 'main'
    ));

$view = new Zend_View();
$view->addHelperPath('Zend/Dojo/View/Helper/', 'Zend_Dojo_View_Helper');

$layout = Zend_Layout::getMvcInstance();
$layout->setView($view);

$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
$viewRenderer->setView($view);

$controller = Zend_Controller_Front::getInstance();
$controller->setControllerDirectory(ZF_APPLICATION_DIRECTORY.'/controllers');
  
$controller->dispatch();
