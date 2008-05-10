<?php

/* ***** BEGIN LICENSE BLOCK *****
 * Version: MPL 1.1/GPL 2.0/LGPL 2.1
 *
 * The contents of this file are subject to the Mozilla Public License Version
 * 1.1 (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Software distributed under the License is distributed on an "AS IS" basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
 * for the specific language governing rights and limitations under the
 * License.
 *
 * The Initial Developer of the Original Code is Christoph Dorn.
 *
 * Portions created by the Initial Developer are Copyright (C) 2006
 * the Initial Developer. All Rights Reserved.
 *
 * Contributor(s):
 *     Christoph Dorn <christoph@christophdorn.com>
 *
 * Alternatively, the contents of this file may be used under the terms of
 * either the GNU General Public License Version 2 or later (the "GPL"), or
 * the GNU Lesser General Public License Version 2.1 or later (the "LGPL"),
 * in which case the provisions of the GPL or the LGPL are applicable instead
 * of those above. If you wish to allow use of your version of this file only
 * under the terms of either the GPL or the LGPL, and not to allow others to
 * use your version of this file under the terms of the MPL, indicate your
 * decision by deleting the provisions above and replace them with the notice
 * and other provisions required by the GPL or the LGPL. If you do not delete
 * the provisions above, a recipient may use your version of this file under
 * the terms of any one of the MPL, the GPL or the LGPL.
 *
 * ***** END LICENSE BLOCK ***** */


set_include_path(get_include_path().PATH_SEPARATOR.'/pinf/packages/org.pinf.Extended/src/lib');
set_include_path(get_include_path().PATH_SEPARATOR.'/pinf/packages/com.googlecode.firephp/Libraries/FirePHPCore/lib');
set_include_path(get_include_path().PATH_SEPARATOR.'/pinf/packages/com.googlecode.firephp/Libraries/ZendFramework/lib');

require_once('Zend/Loader.php');

Zend_Loader::registerAutoload();


/* If set to TRUE logger will not log to FirePHP. Debug messages are sent regardless. */

define('LIVE',false);


/* NOTE: You must have the Zend Framework and FirePHP.class.php core class in your include path! */

set_include_path(dirname(dirname(dirname(__FILE__))).'/lib'.PATH_SEPARATOR.get_include_path());

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
 
FirePhp_Core::init(); 


/*
 * FirePhp_Debug
 */

FirePhp_Debug::fb('Hello World'); /* Defaults to FB_LOG */

FirePhp_Debug::fb('Log message'  ,FirePHP::LOG);
FirePhp_Debug::fb('Info message' ,FirePHP::INFO);
FirePhp_Debug::fb('Warn message' ,FirePHP::WARN);
FirePhp_Debug::fb('Error message',FirePHP::ERROR);

FirePhp_Debug::fb('Message with label','Label',FirePHP::LOG);

FirePhp_Debug::fb(array('key1'=>'val1',
                        'key2'=>array(array('v1','v2'),'v3')),
                  'TestArray',FirePHP::LOG);

function test($Arg1) {
  throw new Exception('Test Exception');
}
try {
  test(array('Hello'=>'World'));
} catch(Exception $e) {
  /* Log exception including stack trace & variables */
  FirePhp_Debug::fb($e);
}

/* Will show only in "Server" tab for the request */
FirePhp_Debug::fb(apache_request_headers(),'RequestHeaders',FirePHP::DUMP);



FirePhp_Debug::log('Var1');
FirePhp_Debug::log('Var1', 'Var2');
FirePhp_Debug::log('Var1', 'Var2', 'Var3');


FirePhp_Debug::dump('DummyLabel', 'Dummy string with a label');


?>