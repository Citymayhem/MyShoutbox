<?php
if(!defined('IN_MYBB')) { BadRequestResponse("Not in MyBB"); }

class DatabaseMigrator {
	
	private $installDirectory;
	
	function __construct($installDirectory){
		$this->installDirectory = $installDirectory;
	}
	
	function performMigration($currentVersion, $newVersion){
		global $db;
		
		if($currentVersion >= $newVersion){
			return;
		}
		
		for($version = $currentVersion + 1; $version <= $newVersion; $version++){
			$fileName = $this->formatFileName($version);
			
			$migrationQueries = explode(";", file_get_contents($this->installDirectory . "migrations/{$fileName}"));
			
			foreach($migrationQueries AS $migrationQuery) {
				$trimmedQuery = trim($migrationQuery);
				if(empty($trimmedQuery)){
					continue;
				}
				
				$expandedQuery = str_replace("{MYBB_TABLE_PREFIX}", TABLE_PREFIX, $trimmedQuery);
				$db->write_query($expandedQuery);

				if(!empty($db->error)){
					error_log("Error running database migration $version for MyShoutbox. Stopping\nQuery: $expandedQuery\nError: " . $db->error);
					InternalServerErrorResponse();
				}
			}

			$db->write_query("INSERT INTO " . TABLE_PREFIX . "mysb_version(`Version`, `DateTime`) VALUES ($version, UTC_TIMESTAMP())");
		}
	}
	
	private function formatFileName($version){
		return str_pad($version, 4, '0', STR_PAD_LEFT) . ".sql";
	}
}