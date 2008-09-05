<?php

require_once(dirname(dirname(__FILE__))
             . '/application/bootstrap/init.php');

$bootstrap = 'mvc';

if(substr($_SERVER['REQUEST_URI'],0,9)=='/json-rpc') {
    $bootstrap = 'json-rpc';
}

require_once(dirname(dirname(__FILE__))
             . '/application/bootstrap/'
             . $bootstrap
             . '.php');
