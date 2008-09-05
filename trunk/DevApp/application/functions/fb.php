<?php

function fb($message, $label=null)
{
    if ($label!=null) {
        $message = array($label,$message);
    }
    Zend_Registry::get('logger')->debug($message);
}
