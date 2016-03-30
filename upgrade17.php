<?php
/*  Upgrade from MyShoutbox 1.6 to 1.7 */

if(!defined('IN_MYBB'))
{
	define('IN_MYBB', 1);
	require_once "./global.php";
}

echo "Adding new setting...";

$q = $db->simple_select('settinggroups', 'gid', 'name=\'mysb_shoutbox\'');
$gid = (int)$db->fetch_field($q, 'gid');

if ($gid <= 0)
{
	echo "ERROR. Settings Group not found.";
}
else {
	$shoutbox_setting_18 = array(
		"name"			=> "mysb_key",
		"title"			=> "Key",
		"description"	=> "Enter a random string for your key. All {myshoutbox_KEY} entries found in your templates or anywhere else will be replaced with the actual shoutbox.",
		"value"			=> "abcd",
		"optionscode"	=> "text",
		"disporder"		=> "18",
		"gid"			=> intval($gid),
	);
	$db->insert_query("settings", $shoutbox_setting_18);
	
	rebuild_settings();

	echo "Done!<br />Please follow the rest of the upgrade instructions found in the readme file.";
}
exit;

?>
