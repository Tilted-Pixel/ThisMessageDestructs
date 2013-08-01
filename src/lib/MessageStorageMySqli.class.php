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
class MessageStorageMySqli extends MessageStore
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
			if(!($stmt = $this->mysqli->prepare('SELECT * FROM ' . self::MESSAGE_TABLE . ' WHERE id=?')))
    		throw new Exception("Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error );
			
			if(!$stmt->bind_param("i", $msgId))
			    throw new Exception("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
			
			if(!$stmt->execute())
			    throw new Exception("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
			
			$res = $stmt->get_result();
			$row = $res->fetch_assoc();			
			
			return $row;
    }
    
    /**
     * {@inheritdoc}
     */    
    protected function writeMessageToStorage($encryptedMessage)
    {
			if(!($stmt = $this->mysqli->prepare('INSERT INTO ' . self::MESSAGE_TABLE . ' VALUES (messagetext) (?)')))
    		throw new Exception("Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error );
			
			if (!$stmt->bind_param("s", $encryptedMessage))
			    throw new Exception("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
			
			if (!$stmt->execute())
			    throw new Exception("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
			
			// return the message identifier
			return $stmt->insertId;
    }
    
    /**
     * {@inheritdoc}
     */    
    protected function deleteMessageFromStorage($msgId)
    {
			if(!($stmt = $this->mysqli->prepare('DELETE FROM ' . self::MESSAGE_TABLE . ' WHERE id=?')))
    		throw new Exception("Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error );
			
			if (!$stmt->bind_param("i", $msgId))
			    throw new Exception("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
			
			if (!$stmt->execute())
			    throw new Exception("Execute failed: (" . $stmt->errno . ") " . $stmt->error);    	
    }
}