<?php

define('PEWPEWPEW', true);

require '../functions.php';

echo PHP_EOL . "Testing function 'array_to_xml'..." . PHP_EOL;
$array = array(
    array('index' => 'none', 'name' => 'Igor'),
    array('index' => 'asd', 'name' => 'Otro Igor'),
    array('index' => '0971', 'name' => 'Igordo'),
    array('index' => 'adffa', 'name' => 'No Igor')
);

$xml = array_to_xml($array);

echo $xml->asXml();

exit('---');

echo PHP_EOL . "Testing function 'config'..." . PHP_EOL;

config(array(2), 2);
config(2, 4);
var_dump(config(array(2)));
var_dump(config(2));
config(2, 5);
var_dump(config(2));
config('layout', 'my_layout');
var_dump(config('layout'));
var_dump(config(2.65));

echo PHP_EOL . "Testing function 'pr'..." . PHP_EOL;

pr(array(1, 2, 3));
pr(array(1, 2, 3), 'Titulo');
pr(12342354, 'Titulo');

echo PHP_EOL . "Testing function 'deref'..." . PHP_EOL;

function returns_array() {
    return array(1, 2, 3, 4, 5);
}

var_dump(deref(returns_array(), 3));

echo PHP_EOL . "Testing function 'pew_clean_string'..." . PHP_EOL;

var_dump(pew_clean_string('?a=select "*" from table?b=nada'));