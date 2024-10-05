<?php

require_once '../vendor/autoload.php';

use Inilim\Dump\Dump;

Dump::init();

interface Test {}
abstract class TestClass {}

$a = Test::class;
$b = TestClass::class;

dd(\class_exists($a, true));
dd(\class_exists($b, true));
