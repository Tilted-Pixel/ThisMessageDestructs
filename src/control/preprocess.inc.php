<?php

		require_once( 'config/config.inc.php' );

    // check to see if HTTPS is on, if not then redirect to HTTPS version of the url.
    if( $Config['enforcessl'] && $_SERVER["HTTPS"] != "on") 
    {
       header("HTTP/1.1 301 Moved Permanently");
       header("Location: https://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"]);
       exit();
    }

?>