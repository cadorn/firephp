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


require_once('./../Libraries/FirePHPCore/lib/FirePHPCore/fb.php');



require_once('ConsoleTest2.php');

try {
  funcA('Arg1');
} catch(Exception $e) {
  fb($e);
}



fb('Hello World');

fb('<a onclick="alert(\'Hello World\');">ClickMe!</a>');



$log = array();
$log[] = array('SQL Statement','Time','Result');
$log[] = array('SELECT * FROM Foo','0.02',array('row1','row2'));
$log[] = array('SELECT * FROM Bar','0.04',array('row1','row2'));

fb(array('2 SQL queries took 0.06 seconds',$log),FirePHP::TABLE);


var_dump($log);



fb('Log message',FirePHP::LOG);
fb('Info message',FirePHP::INFO);
fb('Warn message',FirePHP::WARN);
fb('Error message',FirePHP::ERROR);

fb('Message with label','Label',FirePHP::LOG);

fb('Trace to here',FirePHP::TRACE);

fb(array('key1'=>'val1','key2'=>array(array('v1','v2'),'v3')),'TestArray',FirePHP::LOG);

function test($Arg1) {
  throw new Exception('Test Exception');
}
try {
  test(array('Hello'=>'World'));
} catch(Exception $e) {
  fb($e);
}


fb(apache_request_headers(),'RequestHeaders',FirePHP::DUMP);


//header('X-FirePHP-ProcessorURL: http://'.$_SERVER['HTTP_HOST'].'/Version1-Processor.js');
//header('X-FirePHP-RendererURL: http://'.$_SERVER['HTTP_HOST'].'/Version1-Renderer.js');

//header('X-FirePHP-ProcessorURL: http://www.google.com/');
//header('X-FirePHP-RendererURL: http://www.msn.com/');

print 'Hello World';

?>