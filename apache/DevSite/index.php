<?php

require_once(dirname(dirname(dirname(__FILE__)))
             . '/DevSite/application/bootstrap/init.php');

require_once(dirname(dirname(dirname(__FILE__)))
             . '/DevSite/application/bootstrap/'
             . $_REQUEST['bootstrap']
             . '.php');
