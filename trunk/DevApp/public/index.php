<?php

require_once(dirname(dirname(__FILE__))
             . '/application/bootstrap/init.php');


if (substr($_SERVER['REQUEST_URI'],0,9)=='/json-rpc') {
  
  run_bootstrap('json-rpc');

} else {
  
  run_bootstrap('mvc');
  
}
