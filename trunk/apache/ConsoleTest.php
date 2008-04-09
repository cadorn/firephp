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


require_once('./../FirefoxExtension/chrome/content/firephp/fb.php');


//fb('Sample log message skjadfhg sakjhdfg ahjksdgf hjsad fgjhkasgfdja sdfhjk sdfhj sdafhj sadhjfg sahkjdf hjsadfg ahjksd fgjkhasdg fkjhasdfg',FB_LOG);

//fb(array('server'=>$_SERVER),FB_LOG);

$t = array();
for( $i=0 ; $i<10 ; $i++ ) {
  $t['key'.$i] = $_SERVER;
}

//fb($t,FB_LOG);


//fb("Hello \n World",FB_LOG);

//fb("This is a <b>BOLD</b> test!",FB_LOG);

fb('Sample info message',FB_INFO);

fb('Sample info message');

fb($_SERVER,'$_SERVER',FB_INFO);



fb($_SERVER,'$_SERVER',FB_DUMP);




/*
fb('Sample log message',FB_LOG);
fb('Sample info message',FB_INFO);
fb('Sample warn message',FB_WARN);
fb('Sample error message',FB_ERROR);
*/
//fb('Sample error message',FB_ERROR);

try {
  
  throw new Exception('Test Exception');
  
} catch(Exception $e) {
//  fb($e);
}

?>