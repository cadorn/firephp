<?php

/* Add the FirePHP PEAR package to the include path */
set_include_path(realpath(dirname(__FILE__).'/../PEARPackage').PATH_SEPARATOR.get_include_path());


/* Set a FirePHP-AccessKey cookie if not already set */
if(!$_COOKIE['FirePHP-AccessKey']) {
  $_COOKIE['FirePHP-AccessKey'] = md5(uniqid(rand(), true));
  setcookie('FirePHP-AccessKey', $_COOKIE['FirePHP-AccessKey']);
}


/* Load properties for this companion app */
$PROPERTIES = array();

$properties = @parse_ini_file('PINFApp.properties',true);
if($properties) {
  foreach( $properties as $group_name => $group_info ) {
    foreach( $group_info as $name => $value ) {
      $PROPERTIES[$group_name][$name] = $value;
    }
  }
} 
$properties = @parse_ini_file('PINFApp.rt.properties',true);
if($properties) {
  foreach( $properties as $group_name => $group_info ) {
    foreach( $group_info as $name => $value ) {
      $PROPERTIES[$group_name][$name] = $value;
    }
  }
} 

?>