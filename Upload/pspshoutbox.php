<?php

/**
 * MyShoutBox for MyBB 1.4.x (MYBB_ROOT/pspshoutbox.php)
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
 * File description: version for portable devices
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

if ($mybb->input['action'] == 'shout' && $mybb->input['shout_data'] && $mybb->request_method == 'post' && verify_post_check($mybb->input['postcode'], true)) // insert shout
{
	myshoutbox_psp_add_shout();
}
elseif ($mybb->input['action'] == 'refresh')
{
	redirect("pspshoutbox.php", "Refreshing...", "Success!");
}
else { 

	$perms = myshoutbox_can_view();

	if ($perms && $perms !== 2) {

		myshoutbox_psp_show();
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
}

?>
