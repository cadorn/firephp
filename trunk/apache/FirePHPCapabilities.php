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


$vars = array();
$vars['App.Base.Domain'] = $_SERVER['HTTP_HOST'];
$vars['App.Base.URL'] = 'http://'.$_SERVER['HTTP_HOST'].'/PINF/com.googlecode.firephp/';
$vars['App.Name'] = $_SERVER['PINF_APPLICATION_NAME'];
$vars['App.ID'] = 'FirePHPTests';
$vars['App.Label'] = 'FirePHP Tests';

$response = array();
$response[] = '<firephp version="0.2">';
$response[] =   '<application id="'. $vars['App.ID'].'">';
$response[] =     '<toolbar container="FirePHPWindowResourceToolbarContainer">';
$response[] =       '<item type="tab" name="info" title="Info" source="'.$vars['App.Base.URL'].'InfoTab.php"/>';
$response[] =     '</toolbar>';
$response[] =     '<toolbar container="ContentWindowTopToolbarContainer">';
$response[] =       '<item type="button" name="test_button" title="Test Button"/>';
$response[] =       '<item type="tab" name="test_tab" title="Test Tab"/>';
$response[] =       '<item type="toggle" name="test_toggle" title="Test Toggle"/>';
$response[] =     '</toolbar>';
$response[] =     '<slideouts container="ContentWindowTopSlideoutContainer">';
$response[] =       '<slideout name="test_slideout" source="http://www.google.com/" width="400" height="500"/>';
$response[] =       '<slideout name="test_slideout2" source="http://www.firephp.org/" width="300" height="600"/>';
$response[] =     '</slideouts>';
$response[] =     '<handlers>';
$response[] =       '<handler>';
$response[] =         '<observes group="ContentWindowTopToolbarContainer" name="test_button"/>';
$response[] =         '<event name="mousedown">';
$response[] =           '<javascript><![CDATA[';
$response[] =             'FirePHPChrome.UI.ContentWindowTopSlideoutContainer.open("test_slideout");';
$response[] =           ']]></javascript>';
$response[] =         '</event>';
$response[] =       '</handler>';
$response[] =       '<handler>';
$response[] =         '<observes group="ContentWindowTopToolbarContainer" name="test_toggle"/>';
$response[] =         '<event name="mousedown">';
$response[] =           '<javascript><![CDATA[';
$response[] =             'FirePHPChrome.UI.ContentWindowTopSlideoutContainer.open("test_slideout2");';
$response[] =           ']]></javascript>';
$response[] =         '</event>';
$response[] =       '</handler>';
$response[] =       '<handler>';
$response[] =         '<observes group="ContentWindowTopToolbarContainer" name="test_tab"/>';
$response[] =         '<event name="mousedown">';
$response[] =           '<javascript><![CDATA[';
$response[] =             'var console = new ConsolePanel();';
$response[] =             'console.document = FirePHPChrome.$("idFirePHPConsoleFrame").contentDocument;';
$response[] =             'console.panelNode = console.document.getElementById("panelNode");';
$response[] =             'console.log(this);';
$response[] =             'console.warn("Yup1");';
$response[] =             'console.info("Yup2");';
$response[] =           ']]></javascript>';
$response[] =         '</event>';
$response[] =       '</handler>';
$response[] =     '</handlers>';
$response[] =     '<widget1 name="RequestVariableList" title="Request Variables" container="FirePHPWindowVariablesContainer" type="FirePHPVairableList" scope="request" label="Request"/>';
$response[] =     '<widget1 name="VariableViewer" title="Variable Viewer" container="FirePHPWindowViewerContainer" type="FirePHPVariableViewer"/>';
$response[] =     '<widget1 name="Console" title="Console" container="FirePHPWindowConsoleContainer" type="FirePHPConsole"/>';
$response[] =     '<widget name="ResourceViewer" title="Resource Viewer" container="FirePHPWindowResourceWidgetContainer" type="FirePHPResourceViewerWidget"/>';
$response[] =   '</application>';
$response[] = '</firephp>';

header('Content-Type: text/xml');
print implode("\n",$response);

?>