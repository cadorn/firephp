<?php

class FirePHP_VariableEncoder
{
  
  public static function encode($variable)
  {
    $code = var_export($variable, true);
    
    if(preg_match_all('/[\s>=](\S*?)::__set_state\(/si',$code,$m)) {
      for( $i=0; $i < count($m[0]); $i++ ) {
        $code = preg_replace('/'.preg_quote($m[0][$i],'/').'/',
                                'FirePHP_VariableEncoder::__set_state(\''.$m[1][$i].'\',',
                                $code);
    	}
    }
    
    eval('$dump = ' . $code . ';');
    
    return $dump;
  }

  public static function __set_state($class, $members)
  {    
    $reflection_class = new ReflectionClass($class);  
    
    $props = array();
    foreach( $reflection_class->getProperties() as $property) {
      $props[$property->getName()] = $property;
    }
  
    $dump = array('__className'=>$class);

    foreach( $props as $raw_name => $property ) {

      $name = $raw_name;
      if($property->isStatic()) {
        $name = 'static:'.$name;
      }

      if($property->isPublic()) {
        $name = 'public:'.$name;
      } else
      if($property->isPrivate()) {
        $name = 'private:'.$name;
      } else
      if($property->isProtected()) {
        $name = 'protected:'.$name;
      }
      
      if($members[$raw_name]) {
        $dump[$name] = $members[$raw_name];      
      } else {

        if(method_exists($property,'setAccessible')) {
          $property->setAccessible(true);
          $dump[$name] = $property->getValue();
        } else {
          $dump[$name] = 'Need PHP 5.3 to get value!';
        }
      }
    }    
    
    foreach( $members as $name => $value ) {
      // Include all members that are not defined in the class
      // but exist in the object
      if(!$props[$name]) {
        $name = 'undeclared:'.$name;
        $dump[$name] = $value;
      }
    }
    return $dump;
  }
}



class TestObject
{
  var $publicVar = 'Public Var';
  protected static $protectedVar = 'Protected Var';
  private $privateVar = 'PrivateVar';
}


class TestObject2
{
  var $publicVar = 'Public Var';
  private $privateVar = 'PrivateVar';
}


$obj = new TestObject();

$obj2 = new TestObject2();

$obj->child = $obj2;

$obj = array('hello'=>'world','obj'=>$obj,'last'=>30,array('foo'=>'bar'),array('first','second'));


echo '<pre>';
var_dump($obj);
echo '</pre>';

$dump = FirePHP_VariableEncoder::encode($obj);

echo '<pre>';
var_dump($dump);
echo '</pre>';
