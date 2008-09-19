<?php

$file = dirname(dirname(__FILE__)) . '/application/bootstrap/plain/'
                                   . basename($_SERVER['REDIRECT_URL']);

if(!file_exists($file)) {
  die('File not found!');
}

require_once($file);
