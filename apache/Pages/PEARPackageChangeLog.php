<?php include('../.Init.php');
      include('.Header.tpl.php'); ?>

<pre>

<?php

$changelog_file = './../../PEARPackage/FirePHP/CHANGELOG';

if($changelog_file && file_exists($changelog_file)) {
  $data = array();
  foreach( file($changelog_file) as $line ) {
    $data[] = $line;
  }
  print implode('',$data); 
}

?>

</pre>

<?php include('.Footer.tpl.php'); ?>