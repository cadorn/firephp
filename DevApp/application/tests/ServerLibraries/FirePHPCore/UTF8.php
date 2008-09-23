<?php

$firephp = FirePHP::getInstance(true);

$firephp->fb("Отладочный");


$firephp->fb(array('characters'=>"Отладочный"));


$firephp->fb("Отладочный", 'var1', FirePHP::DUMP);
$firephp->fb(array('characters'=>"Отладочный"), 'var2', FirePHP::DUMP);
