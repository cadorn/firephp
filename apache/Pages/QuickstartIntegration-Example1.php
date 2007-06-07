<?php

/* Include the companion context which ensures that the Access Key cookie is available */
include('../.Init.php');


/* Initialize the FirePHP API */
require_once('FirePHP/Init.inc.php');

/* Set the FirePHP-AccessKey which will be compared to the cookie */
FirePHP::SetAccessKey($_COOKIE['FirePHP-AccessKey']);

/* Initialize the default FirePHP Wrapper */
FirePHP::Init();

/* YOUR CODE GOES HERE - For Example */
print 'Hello World';

?>