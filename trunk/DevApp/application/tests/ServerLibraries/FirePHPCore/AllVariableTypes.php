<?php

$firephp = FirePHP::getInstance(true);


$array = array();
$array['key1'] = 'string';
$array['key2'] = true;
$array['key3'] = false;
$array['key4'] = null;
$array['key5'] = 1;
$array['key6'] = 1.1;
$array['key7'] = array();
$array['key8'] = array('string');
$array['key9'] = array('key'=>'value');
$array['key10'] = new TestObject();
$array[1] = 'string';

$obj = new TestObject();
$obj->child = new TestObject();

$array[99] = $obj;
$array[] = 'Append';

$firephp->fb($array);

$firephp->fb(new TestObject());

$firephp->fb(array('key'=>'value'));
$firephp->fb(array('string',true,false,10,1.1));


class TestObject {
  
  var $member1 = 'string';
  var $member2 = true;
  var $member3 = false;
  var $member4 = null;
  var $member5 = 1;
  var $member6 = 1.1;
  var $member7 = array();
  var $member8 = array('string');

}
