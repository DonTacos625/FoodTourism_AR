<?php


//ローカル環境での接続用
$dsn = 'pgsql:dbname=mydb_2; host=127.0.0.1';
$username= 'postgres';
$password= 'fadsh80fejhj@';
$pdo = new PDO($dsn, $username, $password);

/*
//herokuでのDB接続に必要
$db = parse_url(getenv("DATABASE_URL"));

$pdo = new PDO("pgsql:" . sprintf(
"host=%s;port=%s;user=%s;password=%s;dbname=%s",
$db["host"],
$db["port"],
$db["user"],
$db["pass"],
ltrim($db["path"], "/")
));
*/


?>