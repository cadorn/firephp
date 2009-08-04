<?php

class FirePHP_Rep_PHP_Exception extends FirePHP_Rep_PHP_Error
{
    public function setData($exception)
    {
        $data = array();
        $data['errno'] = get_class($exception);
        $data['errstr'] = $exception->getMessage();
        $data['errfile'] = $exception->getFile();
        $data['errline'] = $exception->getLine();
        $data['backtrace'] = $exception->getTrace();
        
        parent::setData($data);
    }
}
