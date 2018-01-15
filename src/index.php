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

	require_once( 'control/preprocess.inc.php' );
	require_once( 'lib/MessageStorageMySqli.class.php' );
	
	$TemplateVars = array();
	$TemplateVars['linkCreated'] = false;
	
	// make sure the msg exists and is of acceptable length
	if( isset($_POST['msg']) && is_string($_POST['msg']) )
	{					
		
		$mysqli = new mysqli($Config['db_host'], $Config['db_user'], $Config['db_pass'], $Config['db_db']);
		
		if ($mysqli->connect_errno) {
			throw new Exception( "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error );
		}
		
		$messageStorage = new MessageStorageMySqli($mysqli);    
		$result = $messageStorage->storeMessage($_POST['msg'],$Config['sharedkey'], $Config['iv'],$Config['linkhmackey']);
		
		$TemplateVars['linkCreated'] = true;
		$TemplateVars['link'] = 'https://' . $Config['linkdomain'] . '/m/' . $result;
	}

	$TemplateVars['pageTitle'] = 'Create a Self Destructing Message';

	// include the header
	require_once( 'view/header.inc.php' );
	
	// include the actual index page's view
	require_once( 'view/index.inc.php' );
	
	// include the footer
	require_once( 'view/footer.inc.php' );    
?>