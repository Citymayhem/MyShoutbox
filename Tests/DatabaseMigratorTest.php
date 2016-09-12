<?php

define('IN_MYBB', 1);
define('TABLE_PREFIX', 'mybb_');

class FakeDb {
	function write_query($query){
		echo "Running query: \"$query\"" . "\n";
	}
}

$db = new FakeDb();

require_once("../Upload/inc/plugins/MyShoutbox/DatabaseMigrator.php");

$dbMigrator = new DatabaseMigrator("../Upload/inc/plugins/MyShoutbox/");
$dbMigrator->performMigration(0, 1);
