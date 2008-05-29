<?php

require_once('./../Libraries/FirePHPCore/lib/FirePHPCore/fb.php');

fb('Main Page');

?>
<html>
<head>
  <script type="application/x-javascript" src="jquery.js"></script>
</head>

<body>
  
<iframe src="Version1.php?frame=1" width="500" height="300"></iframe>

<iframe src="Version1.php?frame=2" width="500" height="300"></iframe>

<p><a href="#" onClick="$.get('Version1.php?var=1');">Version1.php?var=1</a></p>
<p><a href="#" onClick="$.get('ConsoleTest.php', {var1:'val1'});">ConsoleTest.php (GET)</a></p>
<p><a href="#" onClick="$.post('ConsoleTest.php', {var1:'val1'});">ConsoleTest.php (POST)</a></p>
<p><a href="#" onClick="$.get('ZendTest.php');">ZendTest.php</a></p>


<p><a href="/Test1.php">Home</a></p>
</body>
</html>