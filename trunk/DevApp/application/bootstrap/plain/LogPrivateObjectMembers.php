<?php

class Dumper
{
  protected $_properties = array();
  
  protected function _setState($class, $members) {

    $reflection_class = new ReflectionClass($class);  
    
    $this->_setProperties($reflection_class->getProperties());
  
    $dump = array('__className'=>$class);

    foreach( $this->_properties as $name => $property ) {

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
      
      if($members[$name]) {
        $dump[$name] = $members[$name];      
      } else {

        if(method_exists($property,'setAccessible')) {
//        $property->setAccessible(true);
//        $dump[$name] = $property->getValue();
        } else {
          $dump[$name] = 'Need PHP 5.3 to get value!';
        }
      }
    }    
    
    foreach( $members as $name => $value ) {
      
      if($this->_properties[$name]->isPublic()) {
        $name = 'public:'.$name;
      } else
      if($this->_properties[$name]->isPrivate()) {
        $name = 'private:'.$name;
      } else
      if($this->_properties[$name]->isProtected()) {
        $name = 'protected:'.$name;
      }
      
      $dump[$name] = $value;
    }
    return $dump;
  }
  
  public static function __set_state($class, $members)
  {    
    $dumper = new self();
    return $dumper->_setState($class,$members);
  }
  
  protected function _setProperties($Properties)
  {
    foreach( $Properties as $property) {
      $this->_properties[$property->getName()] = $property;
    }
  }
  
}

class TestObject
{
  var $publicVar = 'Public Var';
  protected static $protectedVar = 'Protected Var';
  private $privateVar = 'PrivateVar';
}

$obj = new TestObject();

$code = var_export($obj, true);

echo '<pre>';
echo $code;
echo '</pre>';

$code = str_replace('TestObject::__set_state(',
                    'Dumper::__set_state(\'TestObject\',', $code);

echo '<pre>';
echo $code;
echo '</pre>';

eval('$dump = ' . $code . ';');

echo '<pre>';
var_dump($dump);
echo '</pre>';
