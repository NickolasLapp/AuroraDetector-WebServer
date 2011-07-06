<?php
include_once 'dataParsing.php';
echo date('Y-m-d', mktime(0,0,0,date('m'),date('d'),date('Y'))).".txt";
?>