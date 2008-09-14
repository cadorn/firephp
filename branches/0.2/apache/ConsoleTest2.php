<?php

function funcA($Arg1) {
  $classA = new ClassA();
  $classB = new ClassB();
  $classB->c = $classA;
  $classA->c = $classB;
  $classA->methodA($Arg1,$classA);
}

function funcB() {
  ClassA::methodB($_SERVER);
}

class ClassB{
 var $name = 'ClassB';
 var $c; 
}

class ClassA {

 var $name = 'ClassA';  
 var $name1 = 'ClassA';  
 var $name2 = 'ClassA';  
 var $name3 = 'ClassA';  
 var $name4 = 'ClassA';  
 var $name5 = 'ClassA';  
  var $c;
  
  function methodA($Arg1,$Arg2) {
    funcB($Arg1,$Arg2,array('key1'=>'val1'));
  }
  static function methodB($Server) {
    throw new Exception('Test Exception: '.$Server['UNIQUE_ID']);
  }
}


?>