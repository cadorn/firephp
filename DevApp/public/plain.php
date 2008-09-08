<?php

$file = dirname(dirname(__FILE__)) . '/application/bootstrap/plain/'
                                   . basename($_SERVER['REQUEST_URI']);

if(!file_exists($file)) {
  die('File not found!');
}

require_once($file);
