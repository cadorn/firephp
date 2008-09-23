<?php

$firephp = FirePHP::getInstance(true);


$firephp->fb('Hello World');
$firephp->fb('Hello World', 'Label');

$firephp->dump('key', 'value');

$firephp->log('log');
$firephp->log('log', 'Label');

$firephp->info('info');
$firephp->info('info', 'Label');

$firephp->warn('warn');
$firephp->warn('warn', 'Label');

$firephp->error('err');
$firephp->error('err', 'Label');

