<?php

/* ***** BEGIN LICENSE BLOCK *****
 *  
 * This file is part of FirePHP (http://www.firephp.org/).
 * 
 * Copyright (C) 2007-2008 Christoph Dorn
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


/* NOTE: You must have the FirePHPCore library in your include path */

set_include_path('./../lib/'.PATH_SEPARATOR.get_include_path());
 

require('FirePHPCore/FirePHP.class.php');

/* NOTE: You must have Output Buffering enabled via
         ob_start() or output_buffering ini directive. */


$firephp = FirePHP::getInstance(true);


$firephp->fb('Hello World'); /* Defaults to FirePHP::LOG */

$firephp->fb('Log message'  ,FirePHP::LOG);
$firephp->fb('Info message' ,FirePHP::INFO);
$firephp->fb('Warn message' ,FirePHP::WARN);
$firephp->fb('Error message',FirePHP::ERROR);

$firephp->fb('Message with label','Label',FirePHP::LOG);

$firephp->fb(array('key1'=>'val1',
         'key2'=>array(array('v1','v2'),'v3')),
   'TestArray',FirePHP::LOG);

function test($Arg1) {
  throw new Exception('Test Exception');
}
try {
  test(array('Hello'=>'World'));
} catch(Exception $e) {
  /* Log exception including stack trace & variables */
  $firephp->fb($e);
}

$firephp->fb(array('2 SQL queries took 0.06 seconds',array(
   array('SQL Statement','Time','Result'),
   array('SELECT * FROM Foo','0.02',array('row1','row2')),
   array('SELECT * FROM Bar','0.04',array('row1','row2'))
  )),FirePHP::TABLE);

/* Will show only in "Server" tab for the request */
$firephp->fb(apache_request_headers(),'RequestHeaders',FirePHP::DUMP);


print 'Hello World';
