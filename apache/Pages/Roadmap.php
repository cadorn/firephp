<?php include('../.Init.php');
      include('.Header.tpl.php'); ?>


<div style="padding: 5px; border: 2px solid #FF6600; background-color: #FFD3B7;">
This page is a live mirror of: <a target="_blank" href="http://www.firephp.org/Reference/Status/Roadmap.htm">http://www.firephp.org/Reference/Status/Roadmap.htm</a>
</div>
<br>

<?php

$html = file_get_contents('http://www.firephp.org/Reference/Status/Roadmap.htm?Skin=ContentOnly');

print $html;

?>

<?php include('.Footer.tpl.php'); ?>