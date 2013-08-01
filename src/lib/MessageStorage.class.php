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
		const KEY_SIZE = 128/8;
		
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
    	return mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $encryptionKey, $msgText, MCRYPT_MODE_CBC , $iv);
    }
    
    /**
     * Generates a random encryption key of size self::KEY_SIZE.
     *
     * @return binary The randomly generated key.
     */
    protected function generateEncryptionKey( )
    {
    		// Generate 128 bit key. Using openssl_random_pseudo_bytes as recommended by PHP.net for cryptographic random number generation.
    	  return openssl_random_pseudo_bytes(self::KEY_SIZE, $cstrong);
    }    
		

    /**
     * Encrypts and stores a message in the message store.
     *
     * @param string $msgTextEncrypted The encrypted message text
     * @param string $encryptionKey The encryption key to use to decrypt the message.
     *
     * @return array|null Associative array with 'msgId' (identifier of message) and 'encryptionKey' (encryption key used)
     */
    protected function decryptMessage($msgTextEncrypted, $encryptionKey)
    {
    	  $encryptionKey = $this->encryptMessage($msgText);
        $msgId = $this->writeMessageToStorage();
                
        return array('msgId' => $msgId, 'encryptionKey' => $encryptionKey);
    }


    /**
     * Encrypts and stores a message in the message store.
     *
     * @param string $msgText The unencrypted message to be stored.
     * @param binary $iv The initialization vector to use
     *
     * @return array|null Associative array with 'msgId' (identifier of message) and 'encryptionKey' (encryption key used)
     */
    public function storeMessage($msgText,$iv)
    {
    	  $encryptionKey = $this->generateEncryptionKey();
    	  $this->encryptMessage($msgText, $encryptionkey,$iv);
        $msgId = $this->writeMessageToStorage();
                
        return array('msgId' => $msgId, 'encryptionKey' => $encryptionKey);
    }

    /**
     * Attempts to decrypt message with given id and return it.
     *
     * @param string $msgId The identifier of the message
     *
     * @return string|null Returns the message or null if not found.
     */    
    public function readMessage($msgId, $encryptionKey)
    {
    	$encryptedMsg = $this->readMessageFromStorage($msgId);
    	return $this->decryptMessage( );
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
     * Deletes a message from storage.
     *
     * @param string $msgId The identifier of the message to be read.     
     */    
    abstract protected function deleteMessageFromStorage($msgId);
}