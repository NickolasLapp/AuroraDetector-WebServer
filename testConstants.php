<?php
include('csvProcessing.php');
$date = "2011-06-28";
$ID = "BZN2";
$column = 6;
$array = getData($date, 6, 7);
print_r($array);
?>