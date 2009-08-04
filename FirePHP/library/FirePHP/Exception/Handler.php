<?php

class FirePHP_Exception_Handler
{
    
    protected $_bootstrap = null;
    
    protected static $_inException = 0;
    
    
    public function __construct(FirePHP_Bootstrap $bootstrap)
    {
        $this->_bootstrap = $bootstrap;
    }
    
    
    public function activate()
    {
        set_exception_handler(array($this,'exceptionHandler'));   
    }
    
    public function decativate()
    {
        restore_error_handler();   
    }
    
    
    
    public function exceptionHandler(Exception $exception)
    {
        self::$_inException++;
        
        try {
            $this->_bootstrap->getLogger()->log('FirePHP_Rep_PHP_Exception', $exception);
        } catch(Exception $e) {
            
            $message = array();
            $message['errno'] = get_class($exception);
            $message['errstr'] = $e->getMessage();
            $message['errfile'] = $e->getFile();
            $message['errline'] = $e->getLine();
            $message['backtrace'] = $e->getTrace();
            
            $this->_bootstrap->getLogger()->log('FirePHP_Rep_PHP_Error', $message);
        }
        self::$_inException--;
    }
}
