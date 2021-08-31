<?php

/*
 * This file is part of the ThisMessageDestructs package
 *
 * (c) Tilted Pixel Inc. <contact@tiltedpixel.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


/**
 * Coding standards demonstration.
 */
abstract class MessageStorage
{
		// size of encryption keys used (default to 128)
		const KEY_SIZE = 128;
		
		// The version of the system used to encrypt message. Increments when format of the link
		// data changes.
		const VERSION  = 2; 
		

    /**
     * Encrypts and stores a message in the message store. The message is stored using
     * a randomly generating encryption key. That key is then itself encrypted along with
     * the identifier of the message in the database and the version of the system using
     * the shared encryption key. The result is then returned.
     *
     * @param string $msgText The unencrypted message to be stored.     
     * @param binary $sharedKey The encryption key to use to encrypt the resulting link.
     * @param binary $iv The initialization vector to use     
     * @param string $hmacKey The secret key passed to hash_hmac to create hmac of the encryptedLink for later verification
     *
     * @return string|null The string that can be used to access the message via readMessage
     */
    public function storeMessage($msgText,$sharedKey,$iv,$hmacKey)
    {
        $encryptionKey = $this->generateEncryptionKey();
        $encryptedMessage = $this->encryptMessage($msgText, $encryptionKey,$iv);
        $msgId = $this->writeMessageToStorage( bin2hex($encryptedMessage) );
                
        $link = sprintf( '%02d', self::VERSION ) . $encryptionKey . $msgId;
        
        $encryptedLink = $this->encryptMessage($link, $sharedKey, $iv);

        // now that we have an encrypted link we can generate an hmac and store it too
        $hmac = hash_hmac("sha256",bin2hex($encryptedLink),$hmacKey);
        $this->writeHmacToStorage($msgId, $hmac);
                
        return bin2hex($encryptedLink);
    }

    /**
     * Attempts to decrypt message with given id and return it.
     *
     * @param string $msgStr The encrypted link text created by storeMessage
     * @param binary $sharedKey The encryption key to use to encrypt the resulting link.     
     * @param binary $iv The initialization vector to use
     * @param string $hmacKey The secret string used in call to hash_hmac     
     *
     * @return string|null Returns the message or null if not found.
     */    
    public function readMessage($encryptedLink,$sharedKey,$iv,$hmacKey)
    {
    	// first validate that msgStr is a valid hex string (hex chars and even length)    	
    	if( (ctype_xdigit($encryptedLink) == false) || strlen($encryptedLink)%2 )
    		return null;
    	
    	// first decrypt the message string
    	$decryptedMsgStr = $this->decryptMessage( hex2bin($encryptedLink), $sharedKey, $iv );
    	    	    		
    	// first 2 characters are ALWAYS the version number
    	$version = substr($decryptedMsgStr,0,2);

			if( !ctype_digit($version) )
				return null;	
				
			$version = intval($version);
    	
    	// can't handle messages that claim to be higher than our own!
    	if( $version > self::VERSION || $version < 1 )
    		return null;
    	
    	// format of version 1 of the message string:
    	// vvxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxi...
    	// v: version of message string
    	// x: encryption key
    	// i...: identifier of message in database (unknown length)
    	
    	// format of version 2 of the message string:
    	// vvxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxi...
    	// v: version of message string
    	// x: encryption key
    	// i...: identifier of message in database (unknown length)    	    		
    	$dataLocations = array(
    		1 => array(
    				'encryptionKey' => array(2,16),
    				'msgId' => array(18,false)
    		),
    		
    		2 => array(
    				'encryptionKey' => array(2,16),
    				'msgId' => array(18,false)
    		),    		
    	);

        // next 16 characters are the encryption key
        $encryptionKey = substr($decryptedMsgStr,$dataLocations[$version]['encryptionKey'][0],$dataLocations[$version]['encryptionKey'][1]);

        // msgId is everything else in the string
        $msgId = trim(substr($decryptedMsgStr, $dataLocations[$version]['msgId'][0]));

        // here we perform an extra validation check, since msgId should be only digits
        if( !is_int($msgId) && !ctype_digit($msgId) )
            return null;
    	
    	// attempt to read the (encrypted) message from storage. 
    	$encryptedMsg = $this->readMessageFromStorage($msgId);    	    	

    	if( is_null($encryptedMsg) )
    		return null;
    	
    	// Now that we have message retrieved, we need to check the hmac of the message against
    	// the one in the database. If they don't match, someone has tampered with the link, meaning
    	// this is not the rightful owner of the message! Chances are someone has guessed a string
    	// that maps a correct msgId and is trying to grief the system by deleting random messages.
    	if( $encryptedMsg['version'] >= 2 )
    	{
	    	$hmac = hash_hmac("sha256",$encryptedLink,$hmacKey);

            if( strcasecmp($hmac, $encryptedMsg['hmacLink']) )
	    		return null;
	    }
    		
    	// decrypt the message
    	$decryptedMsg = $this->decryptMessage( hex2bin($encryptedMsg['msgText']), $encryptionKey, $iv );
    	
    	// if the message was successfully decrypted, we delete it.
    	if( !empty($decryptedMsg) )
    		$this->deleteMessageFromStorage($msgId);    	    	    		
    		
    	return $decryptedMsg;
    }
    
    /**
     * Encrypts and stores a message in the message store.
     *
     * @param string $msgText The message text to encrypt.
     * @param binary $encryptionKey The encryption key to use
     * @param binary $iv The initialization vector to use
     *
     * @return binary Returns the binary encrypted message.
     */
    protected function encryptMessage($msgText, $encryptionKey, $iv)
    {
        return openssl_encrypt($msgText, 'aes-128-cbc', $encryptionKey, false, $iv);
    }       		

    /**
     * Encrypts and stores a message in the message store.
     *
     * @param string $msgTextEncrypted The encrypted message text
     * @param binary $encryptionKey The encryption key to use to decrypt the message.
     * @param binary $iv The iv used to encrypt the message
     *
     * @return string The decrypted message
     */
    protected function decryptMessage($msgTextEncrypted, $encryptionKey, $iv)
    {
        return openssl_decrypt($msgTextEncrypted, 'aes-128-cbc', $encryptionKey, false, $iv);
    }    
    
    /**
     * Generates a random encryption key of size self::KEY_SIZE.
     *
     * @return binary The randomly generated key.
     */
    protected function generateEncryptionKey( )
    {
    		$cstrong = false;
    		// Generate 128 bit key. Using openssl_random_pseudo_bytes as recommended by PHP.net for cryptographic random number generation.
    	  return openssl_random_pseudo_bytes(self::KEY_SIZE / 8,$cstrong);
    }    
    
    /**
     * Reads a message from storage. 
     *
     * @param string $msgId The identifier of the message to be read.
     *
     * @return string|null Returns the encrypted message or null if not found.
     */    
    abstract protected function readMessageFromStorage($msgId);       
    
    /**
     * Writes a message to storage and returns the message identifier.
     *
     * @param string $encryptedMessage The encrypted message as a string
     *
     * @return string|null Returns the message identifier.
     */    
    abstract protected function writeMessageToStorage($encryptedMessage);

    /**
     * Updates a message to store the hmac for it.
     *
     * @param string $msgId The identifier of the message to be read.
     * @param string $hmac The hmac value to store as hexidemical string     
     */    
    abstract protected function writeHmacToStorage($msgId, $hmac);
    
    /**
     * Deletes a message from storage.
     *
     * @param string $msgId The identifier of the message to be read.     
     */    
    abstract protected function deleteMessageFromStorage($msgId);


    /**
     * Deletes all messages that were created before $olderThan
     *
     * @param DateTime $olderThan
     *
     * @return void
     */
    abstract public function deleteExpiredMessages(DateTime $olderThan);
}