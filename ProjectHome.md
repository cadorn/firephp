FirePHP enables you to log to your Firebug Console using a simple PHP method call.

All data is sent via a set of X-FirePHP-Data response headers. This means that the debugging data will not interfere with the content on your page. Thus FirePHP is ideally suited for AJAX development where clean JSON or XML responses are required.

Visit the [Project Website](http://www.firephp.org/) for more information.

```
<?php

require('FirePHPCore/fb.php')

/* NOTE: You must have Output Buffering enabled via
         ob_start() or output_buffering ini directive. */

fb('Hello World'); /* Defaults to FirePHP::LOG */

fb('Log message'  ,FirePHP::LOG);
fb('Info message' ,FirePHP::INFO);
fb('Warn message' ,FirePHP::WARN);
fb('Error message',FirePHP::ERROR);

fb('Message with label','Label',FirePHP::LOG);

fb(array('key1'=>'val1',
         'key2'=>array(array('v1','v2'),'v3')),
   'TestArray',FirePHP::LOG);


function test($Arg1) {
  throw new Exception('Test Exception');
}
try {
  test(array('Hello'=>'World'));
} catch(Exception $e) {
  /* Log exception including stack trace & variables */
  fb($e);
}

fb(array('2 SQL queries took 0.06 seconds',array(
   array('SQL Statement','Time','Result'),
   array('SELECT * FROM Foo','0.02',array('row1','row2')),
   array('SELECT * FROM Bar','0.04',array('row1','row2'))
  )),FirePHP::TABLE);

?>
```


![http://www.firephp.org/images/Screenshots/Sample1a.png](http://www.firephp.org/images/Screenshots/Sample1a.png)
