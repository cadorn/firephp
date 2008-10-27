<?php

$dir = dirname(dirname(dirname(__FILE__))).'/tests/ServerLibraries/HeaderDumps/';

$file = $_REQUEST['file'];

if($file) {
  
  ob_start();
  
  $lines = file($dir.$file);
  $count = 0;
  foreach( $lines as $line ) {
    if($line = trim($line)) {

      $index = strpos($line, "\t");
      if(!$index) {
        $index = strpos($line, " ");
      }
      $line = substr($line,0,$index).':'.substr($line,$index);
      
      if(substr($line,0,11)=='X-Wf-1-1-1-') {
        $count++;
        $line = substr($line,0,11).$count.substr($line,$index);
      } else
      if(substr($line,0,12)=='X-Wf-1-Index') {
        $line = substr($line,0,14).$count;
      }

      header($line);
      echo $line."<br/>\n";
    }
  }
  
} else {
  
  foreach( new DirectoryIterator($dir) as $file ) {
    
    if(!$file->isDot()) {
    
      echo '<p><a href="/plain/TestHeaderDumps.php?file='
           . $file->getFilename()
           . '&t='.time().'">'
           . $file->getFilename()
           . '</a></p>'."\n";
    }
  }
}
