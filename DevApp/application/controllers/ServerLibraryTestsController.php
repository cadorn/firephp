<?php

class ServerLibraryTestsController extends Zend_Controller_Action
{
  
      
    public function firephpcoreAction()
    {
        $this->_helper->layout->setLayout('dojo');
    }

    public function zendframeworkAction()
    {
        $this->_helper->layout->setLayout('dojo');
    }

}

