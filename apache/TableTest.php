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


class TestClass {
  var $yup = 'Yes!!!';  
}

$obj = new TestClass();

$log = array();
$log[] = array('Type','Value');

$log[] = array('String','This is a string');
$log[] = array('Numeric','1');
$log[] = array('Numeric','1.0');
$log[] = array('Array',array('nam'=>'val',3=>'val2',4=>array('Yes'),'val3'=>'444','val4'=>344,'val5'=>33.5,'val6'=>true,'val7'=>false,'val8'=>null,'val9'=>$obj));
$log[] = array('Array',array('nam'=>'val',3=>'val2',31=>'val2',32=>'val2',33=>'val2',34=>'val2',4=>array('Yes')));
$log[] = array('Array',array('nam'=>'val',array('Yes2')));
$log[] = array('Object',$obj);


fb(array('2 SQL queries took 0.06 seconds',$log),FirePHP::TABLE);

print '<pre>';
var_dump($log);
print '</pre>';

?>