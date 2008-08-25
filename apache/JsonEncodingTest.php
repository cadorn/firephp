<?php

class TestClass {}

$obj1 = new TestClass();
$obj2 = new TestClass();

$obj1->child = $obj2;
$obj2->child = $obj1;

try {
  @json_encode($obj1);
} catch(Exception $e) {
}