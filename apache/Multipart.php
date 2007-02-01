<?php

header('Content-type: multipart/firephp; boundary="gc0p4Jq0M2Yt08jU534c0p"');
header('PINF-org.firephp-RequestID: '.md5(uniqid(rand(), true)));

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

<p>The HTML Test.</p>

<p><b>The AJAX JSON Result:</b></p>
<div id="JSONResultDiv" style="background-color: #F0F0F0;"></div>
<script>
var myAjax = new Ajax.Request("'.$script_name.'?File=JSON", { method: "get", onComplete: showJSONResponse});
function showJSONResponse(originalRequest) {
  $("JSONResultDiv").innerHTML = "<pre>"+originalRequest.responseText+"</pre>";
}
</script>    

<p><b>The AJAX XML Result:</b></p>
<div id="XMLResultDiv" style="background-color: #F0F0F0;"></div>
<script>
var myAjax = new Ajax.Request("'.$script_name.'?File=XML", { method: "get", onComplete: showXMLResponse});
function showXMLResponse(originalRequest) {
  var data = originalRequest.responseText;
  data = data.replace(/</g,"&lt;");
  data = data.replace(/>/g,"&gt;");
  $("XMLResultDiv").innerHTML = "<pre>"+data+"</pre>";
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
  PADDING: 20px;
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


header('PINF-org.firephp-PrimaryContentType: '.$content_type);

?>

--gc0p4Jq0M2Yt08jU534c0p

<?php print trim($data); ?>

--gc0p4Jq0M2Yt08jU534c0p 
Content-type: text/firephp 

FIREPHP DATA: <?php print rand(100,10000); ?>

--gc0p4Jq0M2Yt08jU534c0p-- 

