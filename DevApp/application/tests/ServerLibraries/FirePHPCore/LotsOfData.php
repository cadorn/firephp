<?php

$firephp = FirePHP::getInstance(true);

$array = array();

for ( $i=0 ; $i<500 ; $i++ ) {
  $array[$i] = 'Element '.$i;
}

$firephp->fb($array);
