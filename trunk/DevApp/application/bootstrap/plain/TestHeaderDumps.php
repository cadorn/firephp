<?php

$dir = dirname(dirname(dirname(__FILE__))).'/tests/ServerLibraries/HeaderDumps/';

$file = $_REQUEST['file'];

if($file) {
  
  ob_start();
  
  $lines = file($dir.$file);
  $count = 0;
  foreach( $lines as $line ) {
    if($line = trim($line)) {

      if(!strpos($line,':') || strpos($line,':')>strpos($line,' ')) {
        $index = strpos($line, "\t");
        if(!$index) {
          $index = strpos($line, " ");
        }
        $line = substr($line,0,$index).':'.substr($line,$index);
      } else {
        $index = strpos($line, ':');
      }
            
      if(substr($line,0,11)=='X-Wf-1-1-1-') {
        $count++;

        $line_data = trim(substr($line,$index+1));
        $line_size = substr($line_data,0,strpos($line_data,'|'));
        $line_data = substr($line_data,strlen($line_size));
        
        // Adjust line size
        $line_size = strlen($line_data)-2;
        
        $line = substr($line,0,11).$count.': '.$line_size.$line_data;
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
