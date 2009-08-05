<?php

class FirePHP_ObjectEncoder
{
  private static $_maxDepth = 20;
  private static $_refStack = array();
  
  public static function encode($Object, $Depth = 1)
  {
    $return = array();
    
    if ($Depth > self::$_maxDepth) return;
    
    if (is_object($Object)) {
        
        if(in_array($Object,self::$_refStack)) {
          return '* RECURSION *';
        }
        array_push(self::$_refStack, $Object);
                
        $return['__className'] = $class = get_class($Object);

        $reflectionClass = new ReflectionClass($class);  
        $properties = array();
        foreach( $reflectionClass->getProperties() as $property) {
          $properties[$property->getName()] = $property;
        }
            
        $members = (array)$Object;
            
        foreach( $properties as $raw_name => $property ) {

          $name = $raw_name;
          if($property->isStatic()) {
            $name = 'static:'.$name;
          }
          if($property->isPublic()) {
            $name = 'public:'.$name;
          } else
          if($property->isPrivate()) {
            $name = 'private:'.$name;
            $raw_name = "\0".$class."\0".$raw_name;
          } else
          if($property->isProtected()) {
            $name = 'protected:'.$name;
            $raw_name = "\0".'*'."\0".$raw_name;
          }
          
          if(isset($members[$raw_name])
             && !$property->isStatic()) {
            
            $return[$name] = self::encode($members[$raw_name], $Depth + 1);      
          
          } else {
            if(method_exists($property,'setAccessible')) {
              $property->setAccessible(true);
              $return[$name] = self::encode($property->getValue(), $Depth + 1);
            } else {
              $return[$name] = '* Need PHP 5.3 to get value! *';
            }
          }
        }
        
        // Include all members that are not defined in the class
        // but exist in the object
        foreach( $members as $name => $value ) {
          if ($name{0} == "\0") {
            $parts = explode("\0", $name);
            $name = $parts[2];
          }
          if(!isset($properties[$name])) {
            $name = 'undeclared:'.$name;
            $return[$name] = self::encode($value, $Depth + 1);
          }
        }
        
        array_pop(self::$_refStack);
        
    } elseif (is_array($Object)) {
        foreach ($Object as $key => $val) {
          $return[$key] = self::encode($val, $Depth + 1);
        }
    } else {
      return $Object;
    }
    return $return;
  }
}





class TestObject
{
  var $publicVar = 'Public Var';
  protected static $protectedStaticVar = 'Protected Static Var';
  protected $protectedVar = 'Protected Var';
  private $privateVar = 'PrivateVar';
}


class TestObject2
{
  var $publicVar = 'Public Var';
  private $privateVar = 'PrivateVar';
}


$obj1 = $obj = new TestObject();

$obj2 = new TestObject2();

$obj->child = $obj2;
$obj->child2 = $obj;

$obj = array('hello'=>'world','obj'=>$obj,'last'=>30,array('foo'=>'bar'),array('first','second'));


echo '<pre>';
var_dump($obj);
echo '</pre>';

$dump = FirePHP_ObjectEncoder::encode($obj);

echo '<pre>';
var_dump($dump);
echo '</pre>';
