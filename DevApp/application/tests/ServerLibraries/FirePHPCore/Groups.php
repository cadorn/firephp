<?php

$firephp = FirePHP::getInstance(true);


$firephp->group('Group 1');

$firephp->fb('Test message 1');

$firephp->group('Group 2');

$firephp->fb('Test message 2');

$firephp->groupEnd();

$firephp->fb('Test message 3');

$firephp->groupEnd();

