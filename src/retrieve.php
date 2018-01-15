<?php
/*  ThisMessageDestructs.com
    Copyright (C) 2013 Tilted Pixel Inc.

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
	// include preprocessing that should happen with every file
	require_once( 'control/preprocess.inc.php' );
	require_once( 'lib/MessageStorageMySqli.class.php' );
	
	
	// if there is no msg set or it's not the expected length, or not a hexadecimal string
	// or the msgId part is not integer then we should be redirecting back to the index
	if( !isset($_GET['msg']) 
		|| !is_string($_GET['msg'])
		)
	{
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: https://" . $_SERVER["SERVER_NAME"] . '/');
		exit();	
	}

	
	$mysqli = new mysqli($Config['db_host'], $Config['db_user'], $Config['db_pass'], $Config['db_db']);
  
  if ($mysqli->connect_errno) {
      throw new Exception( "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error );
  }
	
	$messageStorage = new MessageStorageMySqli($mysqli);    
  $result = $messageStorage->readMessage($_GET['msg'],$Config['sharedkey'],$Config['iv'],$Config['linkhmackey']);
  
  $TemplateVars = array();
  
  if( !is_null($result) )
  {
  	$TemplateVars['foundMsg'] = true;
  	$TemplateVars['msgText']  = $result;
  }
  else
  	$TemplateVars['foundMsg'] = false;  
	
	$TemplateVars['pageTitle'] = 'This Message Will Self Destruct';
	
	// include the header
	require_once( 'view/header.inc.php' );	
	
	// we don't actually have a formal template system in this project
	// but to enforce some level of MVC concepts, template files should
	// only access variables in $TemplateVars variable	
	
	require_once( 'view/retrieve.inc.php' );
	
	// include the footer
	require_once( 'view/footer.inc.php' );
?>