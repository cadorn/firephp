<?php

$Library = $_REQUEST['Library'];
$Branch = $_REQUEST['Branch'];
$Test = $_REQUEST['Test'];


switch($Library) {
    
    case 'FirePHPCore':
      
      // First include server library
      
      $file = dirname(dirname(dirname(dirname(__FILE__))))
              . '/library/ServerLibraries/FirePHPCore/'
              . $Branch
              . '/lib/FirePHPCore/fb.php';

      require_once($file);
      
      // Start output buffering
      
      ob_start();
      
      // Display some info to confirm the library we are using
      
      $html = array();
      $html[] = '<div style="margin: 10px; padding: 10px; border: 1px solid #B7B7B7; background-color: #bbbbbb; font-family: verdana,arial,helvetica,sans-serif; font-size: 80%;">';
      $html[] = 'Library: <span style="color: white;">'.'/library/ServerLibraries/FirePHPCore/<b>'.$Branch.'</b>/lib/FirePHPCore/</span><br/>';
      $html[] = 'Test: <span style="color: white; font-weight: bold;">'.$Test.'</span>';
      $html[] = '</div>';
      echo implode("\n",$html);
      
      // Now include test file
      
      $file = dirname(dirname(dirname(__FILE__)))
              . '/tests/ServerLibraries/FirePHPCore/'
              . $Test
              . '.php';
     
      require_once($file);
      
      // Lastly print out test file
      
      highlight_file($file);
      break;  
}
