<?php include('pdu_script.php'); ?><html>
<head>
<title>PDU</title>
</head>
<body>
<form method='POST'>
	<input type='hidden' name='com' value='sms'>
	<input type='hidden' name="hex2" value='<?php echo $_POST['hex2']; ?>'>
	<textarea style='width:800px; height:300px;' name="hex1"><?php echo $_POST['hex1']; ?></textarea><br/>
	<button>Convert</button>
</form>
<hr/>
<form method='POST'>
	<input type='hidden' name='com' value='ussd'>
	<input type='hidden' name="hex1" value='<?php echo $_POST['hex1']; ?>'>
	<input type='text' name="hex2" value='<?php echo $_POST['hex2']; ?>'>
	<button>Convert</button>
</form>
<hr/>
<?php

	if($_POST['com']=='sms'){
		echo getPDUMetaInfo($_POST['hex1']);
	}elseif($_POST['com']=='ussd'){
		echo decodePDU($_POST['hex2']);
	}

?>
</body></html>
