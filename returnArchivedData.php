<?php
require_once("./include/membersite_config.php");
    echo $fgmembersite->GetArchivedData('2015-03-18', '2015-03-19', array("aurorapd"));
    //echo $fgmembersite->GetArchivedData(filter_input(INPUT_POST, 'startDate'), filter_input(INPUT_POST, 'endDate'), array("aurorapd"));
?>