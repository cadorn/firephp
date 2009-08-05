<?php

require_once dirname(__FILE__).'/.Start.php';

$firephp = Zend_Wildfire_Plugin_FirePhp::getInstance();

$firephp->setOption('maxObjectDepth',2);
$firephp->setOption('maxArrayDepth',3);


class TestObject {
  var $name = 'test data';
}

class TestObject2 {
  var $name1 = 'name 1';
  var $name2 = 'name 2';
  var $name3 = 'name 3';
}

$obj = new TestObject();
$obj->child = new TestObject();
$obj->child->child = new TestObject();
$obj->child->child->child = new TestObject();
$obj->child->child->child->child = new TestObject();

$obj->child2 = new TestObject2();
$obj->child2->name4 = 'name 4';

$firephp->setObjectFilter('TestObject2',array('name2','name4'));

Zend_Wildfire_Plugin_FirePhp::send($obj);

$array = array();
$array['name'] = 'test data';
$array['child']['name'] = 'test data';
$array['child']['obj'] = $obj;
$array['child']['child']['name'] = 'test data';
$array['child']['child']['child']['name'] = 'test data';
$obj->childArray = $array;

$firephp->setObjectFilter('TestObject2',array('name2','name3'));

Zend_Wildfire_Plugin_FirePhp::send($array);


$table = array();
$table[] = array('Col1','Col2');
$table[] = array($obj, $array);
$table[] = array($obj, $array);
$table[] = array($obj, $array);


$firephp->setOption('maxTraceDepth',2);


try {
  test($table);
} catch(Exception $e) {
  Zend_Wildfire_Plugin_FirePhp::send($e);
}

function test($table) {

  Zend_Wildfire_Plugin_FirePhp::send($table, 'Test deep table', Zend_Wildfire_Plugin_FirePhp::TABLE);

  throw new Exception('Test Exception');
}

Zend_Wildfire_Plugin_FirePhp::send('', 'Test trace', Zend_Wildfire_Plugin_FirePhp::TRACE);


require_once dirname(__FILE__).'/.End.php';

