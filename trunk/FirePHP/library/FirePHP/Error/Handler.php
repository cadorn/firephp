<?php

class FirePHP_Error_Handler
{
    
    protected $_bootstrap = null;
    
    
    public function __construct(FirePHP_Bootstrap $bootstrap)
    {
        $this->_bootstrap = $bootstrap;
    }
    
    
    public function activate()
    {
        set_error_handler(array($this,'errorHandler'));   
    }
    
    public function decativate()
    {
        restore_error_handler();   
    }
    
    
    
    public function errorHandler($errno, $errstr, $errfile, $errline, $errcontext)
    {
        
        $message = array();
        $message['errno'] = $errno;
        $message['errstr'] = $errstr;
        $message['errfile'] = $errfile;
        $message['errline'] = $errline;
        $message['errcontext'] = $errcontext;
        $message['backtrace'] = debug_backtrace();
        
        $this->_bootstrap->getLogger()->log('FirePHP_Rep_PHP_Error', $message);
    }
}
