<?php

$mysqli = new Mysqli('localhost', 't95610id_cloudbe', 'tester', 't95610id_cloudbe');
$mysqli->query('TRUNCATE table price_by_interval');

header('Location: ' . $_SERVER['HTTP_REFERER']);

