<?php

class Controller_LoggingPlugin extends Zend_Controller_Plugin_Abstract
{
  	public function routeShutdown(Zend_Controller_Request_Abstract $request)
  	{
  				
  	  	$writer = new Zend_Log_Writer_Firebug();
    		Zend_Registry::get('logger')->addWriter($writer);
        
  	}
}

