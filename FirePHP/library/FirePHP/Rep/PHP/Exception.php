<?php

class FirePHP_Rep_PHP_Exception extends FirePHP_Rep_PHP_Error
{


    public function setMessage($exception)
    {
        $message = array();

        $message['errno'] = get_class($exception);
        $message['errstr'] = $exception->getMessage();
        $message['errfile'] = $exception->getFile();
        $message['errline'] = $exception->getLine();
        $message['backtrace'] = $exception->getTrace();
        
//var_dump($exception->getTrace());        
        parent::setMessage($message);
    }


}
