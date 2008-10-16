<?php

set_include_path(dirname(dirname(dirname(dirname(__FILE__))))
                 . '/library/ServerLibraries/FirePHPCore/'
                 . '0.2'
                 . '/lib');

require_once('FirePHPCore/fb.php');

ob_start();

$firephp = FirePHP::getInstance(true);

$firephp->setOptions(array('useNativeJsonEncode'=>false));


$tab = array(
     'index' => 'mon numéro est le 0',
     'mon numéro est le 0' => 'index'
);
$json  = json_encode($tab);
echo $json;


$firephp->fb(array('characters'=>'mon numéro est le 0'));