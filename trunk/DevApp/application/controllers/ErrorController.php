<?php

class ErrorController extends Zend_Controller_Action
{
    public function errorAction()
    {
        /*
         * Make sure we don't log exceptions thrown during the exception logging.
         * If we do we will create an infinite loop!
         */

        try {

            Zend_Registry::get('logger')->err($this->_getParam('error_handler')->exception);
          
        } catch(Exception $e) {
          
          /* TODO: You can log this exception somewhere or display it during development.
           *       DO NOT USE THE logger here as it will create an infinite loop!
           */
          
        }      
    }
}

