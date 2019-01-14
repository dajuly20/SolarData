<?php

$_sqlDbName 	= "MeasurementData";
$_sqlTableName 	= "SolarPower";
$_sqlTableNameFull = $_sqlDbName . "." . $_sqlTableName;



$mysqli = new mysqli("localhost", "solar", "solar", $_sqlDbName);
if ($mysqli->connect_errno) {
    die("Verbindung fehlgeschlagen: " . $mysqli->connect_error);
}


$mysqli->query("SET lc_time_names = 'de_DE';");




?>