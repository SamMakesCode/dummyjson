<?php

require 'vendor/autoload.php';

use SamMakesCode\DummyJSON\DummyJSON;

$dummy = new DummyJSON;
//$users = $dummy->users()->getPage();

//$kelly = $dummy->users()->getById(25);

$bob = $dummy->users()->create('Bob', 'Smith', 'bob@example.org');

$bob = $dummy->users()->getById(209);

var_dump($bob); exit;
