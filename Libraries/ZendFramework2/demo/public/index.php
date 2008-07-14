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
 * @package    Zend_Debug
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
 

/* NOTE: You must have the Zend Frameworkin your include path! */

set_include_path('./../../library'.PATH_SEPARATOR.
                 get_include_path());

require_once 'Zend/Registry.php';
require_once 'Zend/Log.php';
require_once 'Zend/Controller/Front.php';
require_once 'Zend/Controller/Request/Http.php';
require_once 'Zend/Controller/Response/Http.php';
require_once 'Zend/Db.php';

require_once 'Zend/Debug/FirePhp.php';
require_once 'Zend/Debug/FirePhp/LogWriter.php';
require_once 'Zend/Debug/FirePhp/DbProfiler.php';



$request = new Zend_Controller_Request_Http();
$response = new Zend_Controller_Response_Http();

$firephp = Zend_Debug_FirePhp::init($request, $response); 
$firephp->setEnabled(true);
Zend_Debug::registerMethodHandler('trace', $firephp);

$controller = Zend_Controller_Front::getInstance();
$controller->setControllerDirectory('../application/controllers')
           ->registerPlugin($firephp);


/*
 * Add our FirePHP logger to the registry
 */

$writer = new Zend_Debug_FirePhp_LogWriter();
$logger = new Zend_Log($writer);

Zend_Registry::set('logger',$logger);


/*
 * Add a DB adpater with our FirePhp db profiler to the registry
 */

$db = Zend_Db::factory('PDO_SQLITE',
                       array('dbname' => ':memory:',
                             'profiler' => new Zend_Debug_FirePhp_DbProfiler()));

$db->getProfiler()->setEnabled(true);

Zend_Registry::set('db',$db);


/*
 * Run the front controller
 */

$controller->dispatch($request, $response);

