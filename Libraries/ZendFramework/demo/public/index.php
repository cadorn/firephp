<?php

/* ***** BEGIN LICENSE BLOCK *****
 *  
 * This file is part of FirePHP (http://www.firephp.org/).
 * 
 * Copyright (C) 2007 Christoph Dorn
 * 
 * FirePHP is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * FirePHP is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public License
 * along with FirePHP.  If not, see <http://www.gnu.org/licenses/lgpl.html>.
 * 
 * ***** END LICENSE BLOCK ***** */


/*
 * @copyright  Copyright (C) 2007 Christoph Dorn
 * @license    http://www.gnu.org/licenses/lgpl.html
 * @author     Jean-Marc Fontaine
 * @author     Christoph Dorn <christoph@christophdorn.com>
 */
 

/* If set to TRUE logger will not log to FirePHP. Debug messages are sent regardless. */

define('LIVE',false);


/* NOTE: You must have the Zend Framework, Zend Framework FirePHP library and
         FirePHPCore library in your include path! */

set_include_path('./../../lib'.PATH_SEPARATOR.
                 './../../../FirePHPCore/lib'.PATH_SEPARATOR.
                 get_include_path());

require_once 'FirePhp/Core.php';
require_once 'Zend/Registry.php';
require_once 'Zend/Log.php';
require_once 'Zend/Log/Filter/Suppress.php';
require_once 'FirePhp/Log/Writer/FirePhp.php';
require_once 'Zend/Controller/Front.php';
require_once 'Zend/Controller/Request/Http.php';
require_once 'Zend/Controller/Response/Http.php';

/*
 * Initialize the HTTP Request and Response Objects
 */
 
$request = new Zend_Controller_Request_Http();
$response = new Zend_Controller_Response_Http();


/*
 * Initialize FirePHP
 */
 
FirePhp_Core::init($request, $response); 


/*
 * Optionally set custom processor and renderer
 */

//FirePhp_Core::getInstance()->setProcessorURL('http://'.$_SERVER['HTTP_HOST'].'/Libraries/ZendFramework/demo/js/RequestProcessor.js');
//FirePhp_Core::getInstance()->setRendererURL('http://'.$_SERVER['HTTP_HOST'].'/Libraries/ZendFramework/demo/js/ServerNetPanelRenderer.js');


/*
 * Add our FirePHP logger to the registry
 */

$writer = new FirePhp_Log_Writer_FirePhp();
$logger = new Zend_Log($writer);
        
$filter = new Zend_Log_Filter_Suppress();
$filter->suppress(LIVE);
$logger->addFilter($filter); 

Zend_Registry::set('logger',$logger);


/*
 * Run the front controller
 */
 
$controller = Zend_Controller_Front::getInstance();
$controller->setControllerDirectory('../application/controllers');
$controller->dispatch($request, $response);

