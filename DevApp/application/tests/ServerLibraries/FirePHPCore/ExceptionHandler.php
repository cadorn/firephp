<?php

$firephp = FirePHP::getInstance(true);

$firephp->registerExceptionHandler();

throw new Exception('Test Exception');