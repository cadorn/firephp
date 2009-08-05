<?php

set_include_path(dirname(dirname(dirname(dirname(__FILE__))))
                 . '/library/ServerLibraries/FirePHPCore/'
                 . '0.2'
                 . '/lib');

require_once('FirePHPCore/fb.php');

ob_start();

$firephp = FirePHP::getInstance(true);

$firephp->setOptions(array('useNativeJsonEncode'=>true));

$tab = array(
     'index' => 'mon numéro est le 0',
     'mon numéro est le 0' => 'index'
);
$json  = $firephp->jsonEncode($tab);
echo $json."<br/>\n";

$firephp->fb(array('useNativeJsonEncode'=>true,'characters'=>$tab));


$firephp->setOptions(array('useNativeJsonEncode'=>false));

$tab = array(
     'index' => 'mon numéro est le 0',
     'mon numéro est le 0' => 'index'
);
$json  = $firephp->jsonEncode($tab);
echo $json."<br/>\n";

$firephp->fb(array('useNativeJsonEncode'=>false,'characters'=>$tab));