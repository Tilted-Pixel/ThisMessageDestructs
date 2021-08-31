<?php

/*
 * This file is part of the ThisMessageDestructs package. 
 *
 * (c) Tilted Pixel Inc. <contact@tiltedpixel.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once( 'lib/MessageStorage.class.php' );

/**
 * Implements message storage using mysqli extension as database layer.
 * This is a very basic implementation that should dependably run on 
 * most mySQL/PHP hosts. Can probably stand to use some improvements
 * in error handling and the way queries are setup.
 */
class MessageStorageMySqli extends MessageStorage
{    
    const MESSAGE_TABLE = 'messages';
    	
    protected $mysqli;    
	
    /**
     * Constructor.
     *
     * @param mysqli $mysqli A mysqli object with established database connection.
     */    	
    public function __construct( $mysqli )
    {
    	$this->mysqli = $mysqli;
    }
    
    /**
     * {@inheritdoc}
     */    
    protected function readMessageFromStorage($msgId)
    {
			if(!($stmt = $this->mysqli->prepare('SELECT version, messagetext, linkhmac FROM ' . self::MESSAGE_TABLE . ' WHERE id=?')))
    		throw new Exception("Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error );
			
			if(!$stmt->bind_param("i", $msgId))
			    throw new Exception("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
			
			if(!$stmt->execute())
			    throw new Exception("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
			
			$version = null;
			$messageText = null;
			$hmacLink = null;
			$stmt->bind_result($version,$messageText,$hmacLink);
			
			if( $stmt->fetch() == false )
				return null;			
			
			return array( 'version' => $version, 'msgText' => $messageText, 'hmacLink' => $hmacLink );
    }
    
    /**
     * {@inheritdoc}
     */    
    protected function writeMessageToStorage($encryptedMessage)
    {
    	// We save the IP address to prevent abuse of the system (such as flooding)
    	// this gets deleted when the message is read.
    	// Is this an appropriate mechanism? Worth discussing.
    	if( isset($_SERVER["REMOTE_ADDR"]) )
    		$ip = $_SERVER["REMOTE_ADDR"];
    	else
    		$ip = '';
    	
        if(!($stmt = $this->mysqli->prepare('INSERT INTO ' . self::MESSAGE_TABLE . ' (version,messagetext,createdon,ipaddress) VALUES (?,?,NOW(),?)')))
            throw new Exception("Prepare failed: (" . $stmt->errno . ") " . $stmt->error );

        $version = self::VERSION;

        if (!$stmt->bind_param("iss", $version, $encryptedMessage, $ip))
            throw new Exception("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);

        if (!$stmt->execute())
            throw new Exception("Execute failed: (" . $stmt->errno . ") " . $stmt->error);

        // return the message identifier
        return $stmt->insert_id;
    }
    
    /**
     * {@inheritdoc}
     */    
    protected function writeHmacToStorage($msgId, $hmac)
    {    	
        if(!($stmt = $this->mysqli->prepare('UPDATE ' . self::MESSAGE_TABLE . ' set linkhmac=? WHERE id=?')))
            throw new Exception("Prepare failed: (" . $stmt->errno . ") " . $stmt->error );

        if (!$stmt->bind_param("si", $hmac,$msgId))
            throw new Exception("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
			
        if (!$stmt->execute())
            throw new Exception("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
    }    
    
    /**
     * {@inheritdoc}
     */    
    protected function deleteMessageFromStorage($msgId)
    {
        if(!($stmt = $this->mysqli->prepare('DELETE FROM ' . self::MESSAGE_TABLE . ' WHERE id=?')))
            throw new Exception("Prepare failed: (" . $stmt->errno . ") " . $stmt->error );

        if (!$stmt->bind_param("i", $msgId))
            throw new Exception("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);

        if (!$stmt->execute())
            throw new Exception("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteExpiredMessages(DateTime $olderThan)
    {
        if(!($stmt = $this->mysqli->prepare('DELETE FROM ' . self::MESSAGE_TABLE . ' WHERE createdon < ?')))
            throw new Exception("Prepare failed: (" . $stmt->errno . ") " . $stmt->error );

        $dateStr = $olderThan->format('Y-m-d');

        if (!$stmt->bind_param("s", $dateStr ) )
            throw new Exception("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);

        if (!$stmt->execute())
            throw new Exception("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
    }
}