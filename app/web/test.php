<?php

echo 'test';
$dsn="mysql:host=mysql:3306;dbname=flower;";
$pdo = new \PDO($dsn,"root","root123");

$pdo->query("SET NAMES 'UTF8'");
$sql="show databases;";
$rs=$pdo->query($sql);
$row=$rs->fetch();

print_r(["ok",$row]);


$redis=new \Redis();
$redis->connect("redis","6379",3);
$redis->auth("666888");
$redis->select(0);
#$redis->set("abc","翌年以后");exit();
$val=$redis->get("abc");
print_r($val);echo PHP_EOL;
