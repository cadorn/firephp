<?php

class Dumper
{
  public static function __set_state($class, $members)
  {    
    $dump = array('__className'=>$class);
    foreach( $members as $name => $value ) {
      $dump[$name] = $value;
    }
    return $dump;
  }
}

class TestObject
{
  var $publicVar = 'Public Var';
  protected $protectedVar = 'Protected Var';
  private $privateVar = 'PrivateVar';
}

$obj = new TestObject();

$code = var_export($obj, true);

$code = str_replace('TestObject::__set_state(',
                    'Dumper::__set_state(\'TestObject\',', $code);

echo '<pre>';
echo $code;
echo '</pre>';

eval('$dump = ' . $code . ';');

echo '<pre>';
var_dump($dump);
echo '</pre>';
