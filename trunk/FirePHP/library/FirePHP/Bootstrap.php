<?php

class FirePHP_Bootstrap
{
    
    protected $_errorHandler = null;
    protected $_exceptionHandler = null;
    protected $_logger = null;
    protected $_clients = array();
    
    protected static $_instances = array();
    

    public function __construct()
    {
        $this->_errorHandler = new FirePHP_Error_handler($this);
        $this->_exceptionHandler = new FirePHP_Exception_handler($this);
        $this->_logger = new FirePHP_Logger($this);
        
        
        // Automatically determine which clients are available
          
        if(php_sapi_name()=='cli') {
            $this->_clients[] = new FirePHP_Client_CLI();
        }
    }
    
    public static function getInstance()
    {
        return self::$_instances[sizeof(self::$_instances)-1];
    }
    
    public function activate()
    {
        array_push(self::$_instances, $this);
        $this->_errorHandler->activate();
        $this->_exceptionHandler->activate();
    }
    
    public function deactivate()
    {
        array_pop(self::$_instances);
        $this->_errorHandler->deactivate();
        $this->_exceptionHandler->deactivate();
    }
    
    
    public function getLogger()
    {
        return $this->_logger;
    }    
    
    public function getCLients()
    {
        return $this->_clients;
    }
    
    

}
