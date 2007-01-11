<?php

/* Used by the FirePHP extension to detect if the domain supports
 * the FirePHPServer components
 */

$vars = array();
$vars['App.Base.URL'] = 'http://'.$_SERVER['HTTP_HOST'].'/';


$response = array();

switch($_GET['PINFURI']) {

  case 'Detect':
    $response[] = '<pinf>';
    $response[] =   '<package name="com.googlecode.firephp">';
    $response[] =     '<vargroup name="Application">';
    $response[] =       '<var name="App.Base.URL"><value>'.$vars['App.Base.URL'].'</value></var>';
    $response[] =     '</vargroup>';
    $response[] =   '</package>';
    $response[] = '</pinf>';
    break;

}

header('Content-Type: text/xml');
print implode("\n",$response);

?>