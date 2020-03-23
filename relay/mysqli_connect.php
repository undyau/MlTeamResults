<?php 
DEFINE  ('DB_USER', 'bigfooto_webuser');
DEFINE  ('DB_PASSWORD', 'L0wlevel');
DEFINE ('DB_HOST', 'localhost');
DEFINE ('DB_NAME', 'bigfooto_relay');

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, 3306);
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
?>
