<?php

$firephp = FirePHP::getInstance(true);

$firephp->setOptions(array('maxObjectDepth'=>2));

FB::setOptions(array('maxArrayDepth'=>3));


class TestObject {
  var $name = 'test data';
}

$obj = new TestObject();
$obj->child = new TestObject();
$obj->child->child = new TestObject();
$obj->child->child->child = new TestObject();
$obj->child->child->child->child = new TestObject();

$firephp->fb($obj);

$array = array();
$array['name'] = 'test data';
$array['child']['name'] = 'test data';
$array['child']['child']['name'] = 'test data';
$array['child']['child']['child']['name'] = 'test data';

$firephp->fb($array);
