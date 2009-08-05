<?php

class IndexController extends Zend_Controller_Action
{
  
      
    public function indexAction()
    {

      if($header = $this->getRequest()->getHeader('User-Agent')) {
        preg_match_all('/\s?FirePHP\/([\.|\d|\w]*)\s?/si',$header,$m);
        if(isset($m[1][0])) {
            $this->view->FirePHPUserAgentVersion = $m[1][0];
        }
      } else {
        $this->view->FirePHPUserAgentVersion = false;        
      }
      


//        $test = new Test();
//        $test->testModelLogging();      

        $this->_helper->layout->setLayout('dojo');
    }
}

