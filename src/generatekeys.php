<?php
	$cstrong = false;
	for($i=1; $i < 11; $i++ )
		echo "'" . bin2hex(openssl_random_pseudo_bytes(16, $cstrong)) . "',\n";
?>