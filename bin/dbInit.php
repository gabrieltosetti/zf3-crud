<?php
$sql = file_get_contents(__DIR__.'/dump.sql');
$db = new \PDO("mysql:host=db;dbname=zf3-crud", 'root', 'root');

$stmt = $db->prepare($sql);
$stmt->execute();