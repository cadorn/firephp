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

fb('Hello World');
fb('Hello World','Label1');

fb('Log message',array('Group 1'));
fb('Info message',array('Group 1'),FirePHP::INFO);
fb('Warn message',array('Group 2','WarnLabel'),FirePHP::WARN);
fb('Error message',array('Group 2'),FirePHP::ERROR);

fb('Dump message 1',array('Group 1','DumpLabel1'),FirePHP::DUMP);
fb('Dump message 2',array('Group 2','DumpLabel2'),FirePHP::DUMP);
fb('Dump message 3',array('Group 2'),FirePHP::DUMP);

fb('Dump message with label','DumpLabel',FirePHP::DUMP);
fb('Dump message without label',FirePHP::DUMP);

fb('Set Value','SetKey1',FirePHP::SET);

/* This should throw an exception as you may not specify a group when using FirePHP::SET */
//fb('Set Value',array('sss','SetKey1'),FirePHP::SET);

/* This should throw an exception as you must specify a name when using FirePHP::SET */
//fb('Set Value',FirePHP::SET);

?>