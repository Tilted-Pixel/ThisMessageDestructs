<?php

	$Config = array();
	
	// Database connection details. You know the drill.
	$Config['db_host'] = 'localhost';
	$Config['db_user'] = '';
	$Config['db_pass'] = '';
	$Config['db_db']   = '';
	
	
	// The IV used for encrypting messages. This is not a secret, but
	// it IS important to fill this in with a 32 character hexadecimal
	// string.
	$Config['iv'] = hex2bin( 'd4056f165b6de0c5ca8617d095bba2ea' );
	
	// The encryption key used to encrypt the final link before it is passed to
	// the user. Should be a 32 character hexadecimal string. 
	//
	// Note: this is different than the key that actually encrypts the message, 
	// which is generated randomly for every message.  32 character hexadecimal
	// string
	$Config['sharedkey'] = hex2bin( '' );
	
	// The secret key passed to hash_hmac when generating the hmac for the 
	// encrypted link. 32 character hexadecimal
	$Config['linkhmackey'] = '';	
	
	// the domain name to use when creating links to the self-destructing messsage
	$Config['linkdomain'] = 'thismessagedestructs.com';

	// enforces the use of https. Should always be true in production
	// sites, disable only for debugging.
	$Config['enforcessl'] = true;

?>