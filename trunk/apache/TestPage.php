<?php

define('PINF-com.googlecode.firephp-BufferOutput',true);

require_once('./../PearPackage/FirePHP/Init.inc.php');

$sleep_time = rand(10000,50000);

?>

<p><b>FirePHP Test Page</b></p>

<p>Lets wait a random period of time ... <?php print $sleep_time/1000; ?>ms</p>

<?php

usleep($sleep_time);

?>

<p>And the included file list is:</p>

<?php

$offset = strlen(dirname(dirname(__FILE__)));

$files = get_included_files();
foreach( $files as $file ) {
  
  print '&nbsp;&nbsp;&nbsp;'.substr($file,$offset).'<br>';
}

?>
