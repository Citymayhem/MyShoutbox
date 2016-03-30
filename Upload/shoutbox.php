<?php

/**
 * MyShoutBox for MyBB 1.4.x (MYBB_ROOT/shoutbox.php)
 * Copyright © 2009 Pirata Nervo, All Rights Reserved!
 *
 * Website: http://www.mybb-plugins.com
 * License: 
 * "This plugin is offered "as is" with no guarantees.
 * You may redistribute it provided the code and credits 
 * remain intact with no changes. This is not distributed
 * under GPL, so you may NOT re-use the code in any other
 * module, plugin, or program.
 * 
 * Free for non-commercial purposes!"
 *
 * This plugin is based off Asad Niazi's spicefuse shoutbox plugin.
 * Spicefuse Shoutbox website: www.spicefuse.com
 * But note that this file was not included with the Spicefuse Shoutbox.
 *
 *
 * File description: This file is used for the popup shoutbox.
 */

if(!defined('IN_MYBB'))
{
	define('IN_MYBB', 1);
	require_once "./global.php";
}
else 
	die("This file is not meant to be run from MyBB.");

$plugins_cache = $cache->read("plugins");
if(!isset($plugins_cache['active']['myshoutbox'])) die('MyShoutbox has not been activated.');

$lang->load("myshoutbox");
//myshoutbox_load();

$perms = myshoutbox_can_view();

if ($perms && $perms !== 2) {

	// no shout button for guests
	if ($mybb->user['usergroup'] == 1)
		$extra_js = "ShoutBox.disableShout();";
	else
		$extra_js = "";
	
	eval("\$mysb_shoutbox = \"".$templates->get("mysb_shoutbox_popup")."\";");
	
	if (!$mysb_shoutbox)
		$mysb_shoutbox = "No shoutbox data.";
	
	output_page($mysb_shoutbox);
	exit;
}
elseif ($perms === 2 && $mybb->settings['mysb_display_message'] == 1)
{
	
	$lang->mysb_error_ban = $lang->sprintf($lang->mysb_error_ban, htmlspecialchars_uni($mybb->user['mysb_banned_reason']));

	error($lang->mysb_error_ban);
}
else {
	error_no_permission();
}
	
exit;

?>
