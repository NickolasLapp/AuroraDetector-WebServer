<?php

include("externalDBFunctions.php");



echo "Full Name: ".$name."</br>";
echo "Email: ".$email."</br>";
echo "Phone Number: ".$phoneNumber."</br>";
echo "Carrier: ".$carrier."</br>";
echo "Username: ".$username."</br>";

if(isset($_POST['submitted'])) {
	if($fgmembersite->UpdateDBRecForData(json_encode($_POST), TRUE))
		$fgmembersite->RedirectToURL("settings-updated.html");
}
?>



<html>
<body>
<form name="myform" method="post" action='<?php echo $fgmembersite->GetSelfScript(); ?>'>
<input type='hidden' name='submitted' id='submitted' value='1'/>
<h1>Aurora Alerts</h1>
<p style="width:60em">This page can be used to select which Aurora alerts you would like to receive. 
    Though the functionality is not entirely in place, we will save your selection and let you know when you will start receiving customized alerts.</p>
<div align="left"><br>
<?php

include("constants.php");

$userSettings = $fgmembersite->getAlertSettings();

foreach ($detectors["abbreviations"] as $value) {
	if ($userSettings['checkbox'][$value] == "on")
		echo "<input type='checkbox' name='checkbox[$value]' checked> $value";
	else
		echo "<input type='checkbox' name='checkbox[$value]'> $value";
	for ($i = 1; $i <= 3; $i++) {
		if ($userSettings['radio'][$value] == $i)
			echo "<input type='radio' name='radio[$value]' value='$i' checked> ".$i;
		else
			echo "<input type='radio' name='radio[$value]' value='$i'> ".$i;
	}
	echo "<br>";
}

?>
<input type="submit" value="Submit" />
</div>
</form>
</body>
</html>