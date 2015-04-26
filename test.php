<?php

$todo = "Example to do";
$completed = "x 2015-04-26 2015-01-04 Example to do +project @context Due:2015-05-20";
$matches = [];
$pattern = '/\A\((?P<priority>[A-Z])\) /m';

preg_match($pattern, $todo, $matches);

dumpIt($matches);

echo substr($completed, 0, 1) . "\n";


function dumpIt($it)
{
    echo "\n";
    var_dump($it);
    echo "\n";
}