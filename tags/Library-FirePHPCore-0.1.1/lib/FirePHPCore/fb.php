<?php

/* ***** BEGIN LICENSE BLOCK *****
 *  
 * This file is part of FirePHP (http://www.firephp.org/).
 * 
 * Software License Agreement (New BSD License)
 * 
 * Copyright (c) 2006-2008, Christoph Dorn
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 * 
 *     * Redistributions of source code must retain the above copyright notice,
 *       this list of conditions and the following disclaimer.
 * 
 *     * Redistributions in binary form must reproduce the above copyright notice,
 *       this list of conditions and the following disclaimer in the documentation
 *       and/or other materials provided with the distribution.
 * 
 *     * Neither the name of Christoph Dorn nor the names of its
 *       contributors may be used to endorse or promote products derived from this
 *       software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 * 
 * ***** END LICENSE BLOCK ***** */


require_once dirname(__FILE__).'/FirePHP.class.php';

/**
 * Sends the given data to FirePHP Firefox Extension.
 * The data can be displayed in the Firebug Console or in the
 * "Server" request tab.
 * 
 * Usage:
 * 
 * require('fb.php')
 * 
 * // NOTE: You must have Output Buffering enabled via
 * //       ob_start() or output_buffering ini directive.
 * 
 * fb('Hello World'); // Defaults to FirePHP::LOG
 * 
 * fb('Log message'  ,FirePHP::LOG);
 * fb('Info message' ,FirePHP::INFO);
 * fb('Warn message' ,FirePHP::WARN);
 * fb('Error message',FirePHP::ERROR);
 * 
 * fb('Message with label','Label',FirePHP::LOG);
 * 
 * fb(array('key1'=>'val1',
 *          'key2'=>array(array('v1','v2'),'v3')),
 *    'TestArray',FB_LOG);
 * 
 * function test($Arg1) {
 *   throw new Exception('Test Exception');
 * }
 * try {
 *   test(array('Hello'=>'World'));
 * } catch(Exception $e) {
 *   fb($e);
 * }
 * 
 * fb(array('2 SQL queries took 0.06 seconds',array(
 *    array('SQL Statement','Time','Result'),
 *    array('SELECT * FROM Foo','0.02',array('row1','row2')),
 *    array('SELECT * FROM Bar','0.04',array('row1','row2'))
 *   )),FirePHP::TABLE);
 * 
 * // Will show only in "Server" tab for the request
 * fb(apache_request_headers(),'RequestHeaders',FirePHP::DUMP);
 * 
 * 
 * @return Boolean  True if FirePHP was detected and headers were written, false otherwise
 * 
 * @copyright   Copyright (C) 2007-2008 Christoph Dorn
 * @author      Christoph Dorn <christoph@christophdorn.com>
 * @license     http://www.opensource.org/licenses/bsd-license.php
 */
function fb() {

  $instance = FirePHP::getInstance(true);
  
  $args = func_get_args();
  return call_user_func_array(array($instance,'fb'),$args);
      
  return true;
}

?>