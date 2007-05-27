<?php

/* Set a FirePHP-AccessKey cookie if not already set */
if($_COOKIE['FirePHP-AccessKey']) {
  setcookie('FirePHP-AccessKey', md5(uniqid(rand(), true)));
}

/* Load properties for this companion app */

$PROPERTIES = parse_ini_file('PINFApp.properties',true);

?>