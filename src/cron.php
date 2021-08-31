<?php

// don't process cron unless it comes from cli
if (substr(php_sapi_name(), 0, 3) != 'cli')
    return;

require_once( 'config/config.inc.php' );
require_once( 'lib/MessageStorageMySqli.class.php' );

$mysqli = new mysqli($Config['db_host'], $Config['db_user'], $Config['db_pass'], $Config['db_db']);

if ($mysqli->connect_errno) {
    throw new Exception( "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error );
}

// delete any messages that are older than specified config
$messageStorage = new MessageStorageMySqli($mysqli);
$expireDate = new DateTime('-' . $Config['msgretention'] . ' days');

$messageStorage->deleteExpiredMessages($expireDate);

echo "complete";