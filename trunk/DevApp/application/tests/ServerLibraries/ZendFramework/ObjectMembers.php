<?php

require_once dirname(__FILE__).'/.Start.php';


class TestObject
{
  var $publicVar = 'Public Var';
  static $publicStaticVar = 'Public static Var';
  protected $protectedVar = 'Protected Var';
  protected static $protectedStaticVar = 'Protected static Var';
  private $privateVar = 'PrivateVar';
  private static $privateStaticVar = 'Private static Var';
  public $publicVar2 = 'Public var 2';
  public static $publicStaticVar2 = 'Public static var 2';
  
  private $lotsOfData = "jhsdfjkhsdfjh sdkjhfasjkdhf sakjdhfg skaj dfhsa dfk jhsdfgkjsa dfksadf sadf sadfh\n jksdjhfg sadjkhfsahjdfghja sdfkj sajdfhkgsadfhj sfd jahksdfhjas dfjkahsdfhjasg dfkas df jhasdf ajkshdfgjhkadfs";
}

class TestObject2
{
  var $publicVar = 'Public Var';
  private $privateVar = 'PrivateVar';
}

class TestObject3
{
}


$obj = new TestObject();

$obj2 = new TestObject2();

$obj3 = new TestObject3();

$obj->child = $obj2;
$obj->child2 = $obj3;
$obj->child3 = $obj;

$obj = array('hello'=>'world','obj'=>$obj,'last'=>30,array('foo'=>'bar'),array('first','second'));

Zend_Wildfire_Plugin_FirePhp::send($obj,'Test Object',Zend_Wildfire_Plugin_FirePhp::INFO);


$obj1 = new stdClass;
$obj2 = new stdClass;
$obj1->p = $obj2;
$obj2->p = $obj1;

Zend_Wildfire_Plugin_FirePhp::send($obj1,'$obj1',Zend_Wildfire_Plugin_FirePhp::INFO);


require_once dirname(__FILE__).'/.End.php';

