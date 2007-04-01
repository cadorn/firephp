<?php

session_start();

/* Add some include paths */

$include_paths = array(dirname(dirname(__FILE__)),
                       dirname(dirname(dirname(__FILE__)))); 

set_include_path(implode(PATH_SEPARATOR,$include_paths).
                 PATH_SEPARATOR.
                 get_include_path());

/* Include the FirePHP server code */
require_once('com.googlecode.firephp/init/Init.inc.php');
register_shutdown_function('ShutdownFirePHPWebsiteCall');
function ShutdownFirePHPWebsiteCall() {
  global $FirePHP;
  $FirePHP->endContent();
  $FirePHP->dumpFirePHPData();
}

global $FirePHP;
$FirePHP->setApplicationID('FirePHPTests');
$FirePHP->setProtocolMode('TemporaryFiles');
$FirePHP->setTemporaryDirpath('/pinf/tmp/com.googlecode.firephp/');
$FirePHP->setCapabilitiesURL('http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['REQUEST_URI']).'/PINF/org.firephp/Capabilities');
$FirePHP->setVariableCallback('variable_id_resolver');

function variable_id_resolver(&$ID,&$Options,&$Value,&$Key,&$Scope,&$Label) {

  $Key = $ID;
  $Label = $ID;

  switch($ID) {
    case '$_SERVER':
      $Scope = 'APPLICATION';
      break;
    case '$_SESSION':
      $Scope = 'SESSION';
      break;
    case '$_GET':
      $Scope = 'REQUEST';
  }

  return true;
}

if(!$_SESSION['RequestStack']) $_SESSION['RequestStack'] = array();
array_unshift($_SESSION['RequestStack'],$_GET['File']);
if(sizeof($_SESSION['RequestStack'])>3) array_pop($_SESSION['RequestStack']);


FirePHP::SetVariable(true,'$_SERVER',$_SERVER);
FirePHP::SetVariable(true,'$_SESSION',$_SESSION);
FirePHP::SetVariable(true,'$_GET',$_GET);

$script_name = 'Multipart.php';

switch($_GET['File']) {

  case 'Test':
    $content_type = 'text/html';
    $data = '
<html>

<head>
  <script src="/PINF/com.googlecode.firephp/prototype.js"></script>
  <link href="'.$script_name.'?File=Style" rel="stylesheet"></link>
</head>
<body>

<p>Firebug Version: <script>document.write(FirePHPChannel.getFirePHPVersion());</script></p>
<p>FirePHP Extension Version: <script>document.write(FirePHPChannel.getExtensionVersion());</script></p>

<p>Selected Request: <div id="SelectedRequestID-div"></div></p>
<script>
/*
  FirePHPChannel.addListener("State", {
    notifyFirePHPEvent: function(Event,Flags) {
      if(Event.getName()=="SelectedRequestChanged") {
        document.getElementById("SelectedRequestID-div").innerHTML = Event.getValue("RequestID");
      }
    }
  });
*/
</script>

<p>The HTML Test.</p>

<p><b>The AJAX JSON Result:</b></p>
<div id="JSONResultDiv" style="background-color: #F0F0F0;"></div>
<script>
var myAjax = new Ajax.Request("'.$script_name.'?File=JSON", { method: "get", onComplete: showJSONResponse});
function showJSONResponse(originalRequest) {
  var data = originalRequest.responseText;
  data = FirePHPChannel.parseContentResponse(data);
  $("JSONResultDiv").innerHTML = data;
}
</script>    

<p><b>The AJAX XML Result:</b></p>
<div id="XMLResultDiv" style="background-color: #F0F0F0;"></div>
<script>
var myAjax = new Ajax.Request("'.$script_name.'?File=XML", { method: "get", onComplete: showXMLResponse});
function showXMLResponse(originalRequest) {
  var data = originalRequest.responseText;
  data = FirePHPChannel.parseContentResponse(data);
  data = data.replace(/</g,"&lt;");
  data = data.replace(/>/g,"&gt;");
  data = data.replace(/\n/g,"<br>");
  data = data.replace(/\s/g,"&nbsp;");
  $("XMLResultDiv").innerHTML = data;
}
</script>    

<p><b>The Image Result:</b></p>
<iframe name="ImageTestFrame" src="'.$script_name.'?File=Image" width="150" height="40"></iframe>

</body>
';  
    break;

  case 'Image':
    $content_type = 'image/png';
    $data = file_get_contents(dirname(dirname(__FILE__)).'/FirefoxExtension/chrome/skin/classic/PoweredByFirePHP_v1.png');
    break;

  case 'Style':
    $content_type = 'text/css';
    $data = '
HTML, BODY {
  PADDING: 2px;
  background-color: #EEEEEE;
}

HTML, BODY, P {
  FONT-FAMILY: verdana;
  FONT-SIZE: 10px;
}
';
    break;
    
  case 'XML':
    $content_type = 'text/xml';
    $data = '
<doc>
  <item name="test">TestValue</item>
</doc>
';
    break;
    
  case 'JSON':
    $content_type = 'text/plain';
    $data = '
{ "abc": 12, "foo": "bar", "bool0": false, "bool1": true, "arr": [ 1, 2, 3, null, 5 ], "float": 1.234500 }
';
    break;
    
  case 'HTML':
  default:
    $content_type = 'text/html';
    $data = '
<p>This is HTML text</p>
<p><b>And some bold text</b></p>
';
    break;
}


global $FirePHP;

$FirePHP->startContent($content_type,$_GET['File']);

print trim($data);

?>