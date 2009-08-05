<?php


class Test
{
  
    
    function testModelLogging() {
        
        
Zend_Registry::get('logger')->debug('This is a model debug message');


    }
  
  
}
