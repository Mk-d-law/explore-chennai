<?php

$databasePath = __DIR__ . '/datab.db'; 

$connection = new SQLite3($databasePath);

if (!$connection) {
    die("Connection failed: " . $connection->lastErrorMsg());
}

?>
