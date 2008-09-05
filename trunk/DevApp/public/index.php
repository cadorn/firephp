<?php

require_once(dirname(dirname(__FILE__))
             . '/application/bootstrap/init.php');

require_once(dirname(dirname(__FILE__))
             . '/application/bootstrap/'
             . $_REQUEST['bootstrap']
             . '.php');
