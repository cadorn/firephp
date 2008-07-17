<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Wildfire
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/* NOTE: You must have the Zend Framework in your include path! */

set_include_path('./../../library'.PATH_SEPARATOR.
                 get_include_path());

/*
 * Add our Firebug Log Writer to the registry
 */

require_once 'Zend/Registry.php';
require_once 'Zend/Log.php';
require_once 'Zend/Wildfire/FirebugLogWriter.php';

$writer = new Zend_Wildfire_FirebugLogWriter();
$writer->setPriorityStyle(8, 'TABLE');

$logger = new Zend_Log($writer);
$logger->addPriority('TABLE', 8);

Zend_Registry::set('logger',$logger);

/*
 * Run the front controller
 */

require_once 'Zend/Controller/Front.php';

Zend_Controller_Front::run('./../application/controllers');
