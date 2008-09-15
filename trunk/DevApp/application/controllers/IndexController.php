<?php

class IndexController extends Zend_Controller_Action
{
  
      
    public function indexAction()
    {
      

//        $test = new Test();
//        $test->testModelLogging();      

        $this->_helper->layout->setLayout('dojo');
    }
}

