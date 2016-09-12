<?php

define('IN_MYBB', 1);
define('TABLE_PREFIX', 'mybb_');

class FakeDb {
	function write_query($query){
		echo "Running query: \"$query\"" . "\n";
	}
}

abstract class MyShoutboxConfiguration {
	const DatabaseVersion = 1;
}

$db = new FakeDb();

require_once("DatabaseMigrator.php");

$dbMigrator = new DatabaseMigrator("./");
$dbMigrator->performMigration(0, 2);
