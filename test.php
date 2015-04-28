<?php

$todo = "Friends and relatives +moving +changeaddress @1stContext @2ndContext";
$completed = "x 2015-04-26 2015-01-04 Example to do +projects @contexts Due:2015-05-20";
$matches = [];
$pattern = '/\+(?P<projects>\w*)/m';

$projects = ['project1', 'project2'];
dumpIt(implode(' +', $projects));


function dumpIt($it)
{
    echo "\n";
    var_dump($it);
    echo "\n";
}