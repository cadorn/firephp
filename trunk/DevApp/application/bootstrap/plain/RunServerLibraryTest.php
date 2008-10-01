<?php

$Library = $_REQUEST['Library'];
$Branch = $_REQUEST['Branch'];
$Test = $_REQUEST['Test'];

switch($Library) {
    
    case 'FirePHPCore':
      
      // Include a pre-test file if we have one
      
      $file = dirname(dirname(dirname(__FILE__)))
              . '/tests/ServerLibraries/FirePHPCore/'
              . $Test
              . '.pre.php';
     
      if(file_exists($file)) {
          require_once($file);
      }
          
      // Include server library
      
      set_include_path(dirname(dirname(dirname(dirname(__FILE__))))
                       . '/library/ServerLibraries/FirePHPCore/'
                       . $Branch
                       . '/lib');
      
      $file = 'FirePHPCore/fb.php';

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
            
      // Print out test file

      $file = dirname(dirname(dirname(__FILE__)))
              . '/tests/ServerLibraries/FirePHPCore/'
              . $Test
              . '.php';
      
      highlight_file($file);
      
      // Show all included files
      
      $html = array();
      $html[] = '<table><tr><td nowrap style="color: white; margin: 10px; padding: 10px; border: 1px solid #B7B7B7; background-color: #bbbbbb; font-family: verdana,arial,helvetica,sans-serif; font-size: 80%;">';
      foreach( get_included_files() as $f ) {
        $html[] = $f . '<br/>';
      }      
      $html[] = '</td></tr></table>';
      echo implode("\n",$html);
      
      // Now include test file
      
      require_once($file);
      
      break;  

    
    case 'ZendFramework':
      
      // Include a pre-test file if we have one
      
      $file = dirname(dirname(dirname(__FILE__)))
              . '/tests/ServerLibraries/ZendFramework/'
              . $Test
              . '.pre.php';
     
      if(file_exists($file)) {
          require_once($file);
      }
          
      // Include server library
      
      set_include_path(dirname(dirname(dirname(dirname(__FILE__))))
                       . '/library/ServerLibraries/ZendFramework/'
                       . $Branch
                       . '/library');

      require_once('Zend/Loader.php');
      Zend_Loader::registerAutoload();
      
      // Start output buffering
      
      ob_start();
      
      // Display some info to confirm the library we are using
      
      $html = array();
      $html[] = '<div style="margin: 10px; padding: 10px; border: 1px solid #B7B7B7; background-color: #bbbbbb; font-family: verdana,arial,helvetica,sans-serif; font-size: 80%;">';
      $html[] = 'Library: <span style="color: white;">'.'/library/ServerLibraries/ZendFramework/<b>'.$Branch.'</b></span><br/>';
      $html[] = 'Test: <span style="color: white; font-weight: bold;">'.$Test.'</span>';
      $html[] = '</div>';
      echo implode("\n",$html);
            
      // Print out test file
      
      $file = dirname(dirname(dirname(__FILE__)))
              . '/tests/ServerLibraries/ZendFramework/'
              . $Test
              . '.php';

      highlight_file($file);
      
      // Show all included files
      
      $html = array();
      $html[] = '<table><tr><td nowrap style="color: white; margin: 10px; padding: 10px; border: 1px solid #B7B7B7; background-color: #bbbbbb; font-family: verdana,arial,helvetica,sans-serif; font-size: 80%;">';
      foreach( get_included_files() as $f ) {
        $html[] = $f . '<br/>';
      }      
      $html[] = '</td></tr></table>';
      echo implode("\n",$html);    
      
      // Now include test file
     
      require_once($file);
        
      break;      
      
}
