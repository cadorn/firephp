<?php

set_include_path(dirname(dirname(dirname(dirname(__FILE__))))
                 . '/library/ServerLibraries/FirePHPCore/'
                 . '0.2'
                 . '/lib');

require_once('FirePHPCore/fb.php');

ob_start();

$firephp = FirePHP::getInstance(true);


//HTTP headers (FirePHPCore-0.1.2.1):
if(false) {
header('X-FirePHP-Data-100000000001: {');
header('X-FirePHP-Data-300000000001: "FirePHP.Firebug.Console":[');
header('X-FirePHP-Data-301614728900: ["EXCEPTION",{"Class":"Exception","Message":"Missing file test.php","File":"C:\\php\\includes\\autoprepend.inc.php","Line":32,"Type":"throw","Trace":[{"file":"C:\\Apache\\htdocs\\test\\index.php","line":3,"function":"require"}]}],');
header('X-FirePHP-Data-301616285400: ["ERROR",["Posted: ",[]]],');
header('X-FirePHP-Data-399999999999: ["__SKIP__"]],');
header('X-FirePHP-Data-999999999999: "__SKIP__":"__SKIP__"}');
}

//HTTP headers (FirePHPCore-0.2.b.1):
if(false) {
header('X-Wf-1-1-1-1: |[{"Type":"EXCEPTION"},{"Class":"Exception","Message":"Missing file test.php","File":"C:\\\php/includes/autoprepend.inc.php","Line":32,"Type":"throw","Trace":[{"file":"C:/Apache/htdocs/test/index.php","line":3,"function":"require"}]}]|');
header('X-Wf-1-1-1-2: |[{"Type":"ERROR","Label":"Posted: "},[]]|');
header('X-Wf-1-Index: 2');
header('X-Wf-1-Plugin-1: http://meta.firephp.org/Wildfire/Plugin/FirePHP/Library-FirePHPCore/0.2.b.1');
header('X-Wf-1-Structure-1: http://meta.firephp.org/Wildfire/Structure/FirePHP/FirebugConsole/0.1');
header('X-Wf-Protocol-1: http://meta.wildfirehq.org/Protocol/JsonStream/0.1');
}


if(false) {
  $var = array('i'=>10, 'j'=>20);
  $firephp->log($var, 'Iterators');
}


if(true) {

  error_reporting(E_ALL);
  $firephp->registerErrorHandler();
  $firephp->registerExceptionHandler();
  
  if (in_array(10,10)) echo $empty;
  echo "OK";

}
