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

	$TemplateVars = array();
	$TemplateVars['pageTitle'] = 'Contact - Create a Self Destructing Message';

	// include the header
	require_once( 'view/header.inc.php' );	
	
	// we don't actually have a formal template system in this project
	// but to enforce some level of MVC concepts, template files should
	// only access variables in $TemplateVars variable	
	
	require_once( 'view/contact.inc.php' );
	
	// include the footer
	require_once( 'view/footer.inc.php' );
?>