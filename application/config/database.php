<?php
$active_group = "work";
$active_record = true;
$dev = 0;
$dbc = array(
    'work' => array(
        'hostname' => 'localhost',
        'username' => 'root',
        'password' => 'Rb9Ls64',
        'database' => 'klimat-komfort54.ru'
    ),
);

$common = array(
    'dbdriver' => 'mysql',
    'pconnect' => false,
    'db_debug' => true,
    'cache_on' => false,
    'char_set' => 'utf8',
    'dbcollat' => 'utf8_general_ci',
);

$db = array();
foreach($dbc as $key=>$val) {
    foreach($val as $k=>$v) {
        $db[$key][$k] = $v;
    }
    foreach($common as $k=>$v) {
        $db[$key][$k] = $v;
    }
}
