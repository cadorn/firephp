<?php

abstract class FirePHP_Rep
{
     
    protected $_data = null;
     
     
    public function setData($data)
    {
    	$this->_data = $data;
    }
    
    public function getData()
    {
        return $this->_data;
    }

    
    
    public static function factory($repString)
    {
        // Check if we are referring to a class
        Zend_Loader_Autoloader::autoload($repString);
        if(!class_exists($repString)) {
         
            throw new Exception('Only class-based representations are supported at this time!');
            
        }
        
        $rep = new $repString();
       
        return $rep;
    }
    

    public function shouldDisplay()
    {
        return true;
    }
     
    abstract public function toString();
    
     
}
