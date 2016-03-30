<?php

/**
 * MyShoutBox for MyBB 1.4.x (MYBB_ROOT/inc/plugins/shoutbox.php)
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
 *
 *
 * File description: MyShoutbox main file
 */

if(!defined('IN_MYBB'))
	die('This file cannot be accessed directly.');

$plugins->add_hook("index_end", "myshoutbox_index");
$plugins->add_hook("xmlhttp", "myshoutbox_load");
$plugins->add_hook("pre_output_page", "myshoutbox_output_control");

$plugins->add_hook('admin_load', 'myshoutbox_admin');
$plugins->add_hook('admin_tools_menu', 'myshoutbox_admin_tools_menu');
$plugins->add_hook('admin_tools_action_handler', 'myshoutbox_admin_tools_action_handler');
$plugins->add_hook('admin_tools_permissions', 'myshoutbox_admin_permissions');

// reported shouts notice
$plugins->add_hook('admin_home_menu', 'myshoutbox_admin_home_menu');

function myshoutbox_info()
{
	return array(
		'name'			=> 'MyShoutbox',
		'description'	=> 'A powerful AJAX shoutbox for MyBB.',
		'website'		=> 'http://consoleaddicted.com/',
		'author'		=> 'Pirata Nervo',
		'authorsite'	=> 'http://consoleaddicted.com/',
		'version'		=> '1.7',
		'guid'			=> 'c7e5e6c1a57f0639ea52d7813b23579f',
		'compatibility' => '14*,15*,16*',
	);
}

function myshoutbox_install()
{
	global $db;
    
	$shoutbox_group = array(
		"name"		=> "mysb_shoutbox",
		"title"		=> "MyShoutbox",
		"description"	=> "Settings for the MyShoutbox plugin.",
		"disporder"	=> "1",
		"isdefault"	=> "no",
	);
    
	$db->insert_query("settinggroups", $shoutbox_group);
	$gid = $db->insert_id();
	
	$shoutbox_setting_1 = array(
		"name"		=> "mysb_shouts_main",
		"title"		=> "# of Shouts to display",
		"description"	=> "The maximum number of shouts you want to be displayed.",
		"optionscode"	=> "text",
		"value"		=> "30",
		"disporder"	=> "1",
		"gid"		=> intval($gid),
	);
	
	$shoutbox_setting_2 = array(
		"name"		=> "mysb_refresh_interval",
		"title"		=> "Refresh Interval",
		"description"	=> "How many seconds before the shoutbox is reloaded using AJAX transparently. ",
		"optionscode"	=> "text",
		"value"		=> "15",
		"disporder"	=> "2",
		"gid"		=> intval($gid),
	);
	
	$shoutbox_setting_3 = array(
		"name"		=> "mysb_allow_mycode",
		"title"		=> "Allow MyCode?",
		"description"	=> "Allow MyBB code in shouts to format text using [b], [i] etc..?",
		"optionscode"	=> "yesno",
		"value"		=> "yes",
		"disporder"	=> "3",
		"gid"		=> intval($gid),
	);
	
	$shoutbox_setting_4 = array(
		"name"		=> "mysb_allow_smilies",
		"title"		=> "Allow Smilies?",
		"description"	=> "Allow smilies in shouts?",
		"optionscode"	=> "yesno",
		"value"		=> "yes",
		"disporder"	=> "4",
		"gid"		=> intval($gid),
	);
	
	$shoutbox_setting_5 = array(
		"name"		=> "mysb_allow_imgcode",
		"title"		=> "Allow IMGCode?",
		"description"	=> "Allow images in shoutbox? Note: Some can post too big images and mess up your layout.",
		"optionscode"	=> "yesno",
		"value"		=> "no",
		"disporder"	=> "5",
		"gid"		=> intval($gid),
	);
	
	$shoutbox_setting_6 = array(
		"name"		=> "mysb_height",
		"title"		=> "ShoutBox Height",
		"description"	=> "Set the height for shoutbox here.",
		"optionscode"	=> "text",
		"value"		=> "125",
		"disporder"	=> "6",
		"gid"		=> intval($gid),
	);
	
	$shoutbox_setting_7 = array(
		"name"		=> "mysb_datetime",
		"title"		=> "ShoutBox Date/Time",
		"description"	=> "PHP date time format for shoutbox. <a href=\"http://php.net/date\" target=\"_blank\">check here</a> for more info.",
		"optionscode"	=> "text",
		"value"		=> "d-m-H:i",
		"disporder"	=> "7",
		"gid"		=> intval($gid),
	);
	
	$shoutbox_setting_8 = array(
		"name"		=> "mysb_full_ppage",
		"title"		=> "Shouts per page on full view?",
		"description"	=> "The number of shouts you want to be displayed, per page, on the full shoutbox view.",
		"optionscode"	=> "text",
		"value"		=> "50",
		"disporder"	=> "8",
		"gid"		=> intval($gid),
	);
	
	$shoutbox_setting_9 = array(
		"name"		=> "mysb_allow_smods",
		"title"		=> "Allow super moderators to delete?",
		"description"	=> "Allow super mods to delete shouts in the shoutbox?",
		"optionscode"	=> "yesno",
		"value"		=> "yes",
		"disporder"	=> "9",
		"gid"		=> intval($gid),
	);
	
	$shoutbox_setting_10 = array(
		"name"		=> "mysb_allow_html",
		"title"		=> "Allow HTML?",
		"description"	=> "Allow html in shoutbox?",
		"optionscode"	=> "yesno",
		"value"		=> "no",
		"disporder"	=> "10",
		"gid"		=> intval($gid),
	);
	
	$shoutbox_setting_11 = array(
		"name"		=> "mysb_allow_video",
		"title"		=> "Allow Videos?",
		"description"	=> "Allow videos in shoutbox? (MyBB 1.6 only)",
		"optionscode"	=> "yesno",
		"value"		=> "no",
		"disporder"	=> "11",
		"gid"		=> intval($gid),
	);
	
	$shoutbox_setting_12 = array(
		"name"		=> "mysb_flood_time",
		"title"		=> "Flood Check?",
		"description"	=> "Add a flood check for everyone but the moderators. Enter a time in seconds here. Enter 0 to disable.",
		"optionscode"	=> "text",
		"value"		=> "5",
		"disporder"	=> "12",
		"gid"		=> intval($gid),
	);
	
	$shoutbox_setting_13 = array(
		"name"		=> "mysb_usergroups",
		"title"		=> "Groups allowed to view the shoutbox",
		"description"	=> "The groupd ids of the users allowed to view the shoutbox. (Seperated by a comma. Leave blank to allow all.)",
		"optionscode"	=> "text",
		"value"		=> "",
		"disporder"	=> "13",
		"gid"		=> intval($gid),
	);
	
	$shoutbox_setting_14 = array(
		"name"		=> "mysb_additional_groups",
		"title"		=> "Check additional groups?",
		"description"	=> "Set this to yes if you want additional groups to be checked. (This setting will only take effect if the above one is not blank)",
		"optionscode"	=> "yesno",
		"value"		=> "no",
		"disporder"	=> "14",
		"gid"		=> intval($gid),
	);
		
	$shoutbox_setting_15 = array(
		"name"		=> "mysb_allow_mods",
		"title"		=> "Allow moderators to delete?",
		"description"	=> "Allow moderators to delete shouts in the shoutbox?",
		"optionscode"	=> "yesno",
		"value"		=> "yes",
		"disporder"	=> "15",
		"gid"		=> intval($gid),
	);
	
	$shoutbox_setting_16 = array(
		"name"		=> "mysb_display_message",
		"title"		=> "Do you want to show a message to banned users?",
		"description"	=> "Do you want to show a message to banned users? The message can be changed in the language files of MyShoutbox. (it is displayed instead of the shoutbox)",
		"value"		=> 1,
		"optionscode"	=> "yesno",
		"disporder"	=> "16",
		"gid"		=> intval($gid),
	);
	
	$shoutbox_setting_17 = array(
		"name"		=> "mysb_text_size",
		"title"		=> "Font size",
		"description"	=> "Enter the font size of the shouts. Default is 12.",
		"value"		=> 12,
		"optionscode"	=> "text",
		"disporder"	=> "17",
		"gid"		=> intval($gid),
	);
	
	$shoutbox_setting_18 = array(
		"name"			=> "mysb_key",
		"title"			=> "Key",
		"description"	=> "Enter a random string for your key. All {myshoutbox_KEY} entries found in your templates or anywhere else will be replaced with the actual shoutbox.",
		"value"			=> "abcd",
		"optionscode"	=> "text",
		"disporder"		=> "18",
		"gid"			=> intval($gid),
	);
    
	$db->insert_query("settings", $shoutbox_setting_1);
	$db->insert_query("settings", $shoutbox_setting_2);
	$db->insert_query("settings", $shoutbox_setting_3);
	$db->insert_query("settings", $shoutbox_setting_4);
	$db->insert_query("settings", $shoutbox_setting_5);
	$db->insert_query("settings", $shoutbox_setting_6);
	$db->insert_query("settings", $shoutbox_setting_7);
	$db->insert_query("settings", $shoutbox_setting_8);
	$db->insert_query("settings", $shoutbox_setting_9);
	$db->insert_query("settings", $shoutbox_setting_10);
	$db->insert_query("settings", $shoutbox_setting_11);
	$db->insert_query("settings", $shoutbox_setting_12);
	$db->insert_query("settings", $shoutbox_setting_13);
	$db->insert_query("settings", $shoutbox_setting_14);
	$db->insert_query("settings", $shoutbox_setting_15);
	$db->insert_query("settings", $shoutbox_setting_16);
	$db->insert_query("settings", $shoutbox_setting_17);
	$db->insert_query("settings", $shoutbox_setting_18);
	
	// create table
	$db->write_query("CREATE TABLE `".TABLE_PREFIX."mysb_shouts` (
	  `id` int(10) NOT NULL auto_increment,
	  `uid` int(10) NOT NULL,
	  `shout_msg` text NOT NULL,
	  `shout_date` int(10) NOT NULL,
	  `shout_ip` varchar(30) NOT NULL,
	  `hidden` varchar(10) NOT NULL,
	  PRIMARY KEY  (`id`)
		) ENGINE=MyISAM");
	
	// create reports table
	$db->write_query("CREATE TABLE `".TABLE_PREFIX."mysb_reports` (
	  `rid` int(10) NOT NULL auto_increment,
	  `username` varchar(100) NOT NULL DEFAULT '',
	  `uid` int(10) NOT NULL DEFAULT 0,
	  `reason` varchar(255) NOT NULL DEFAULT '',
	  `date` bigint(30) NOT NULL DEFAULT 0,
	  `sid` int(10) NOT NULL DEFAULT 0,
	  `marked` tinyint(1) NOT NULL DEFAULT 0,
	  `author_uid` int(10) NOT NULL DEFAULT 0,
	  `author_username` varchar(30) NOT NULL DEFAULT '',
	  PRIMARY KEY  (`rid`), KEY(`date`)
		) ENGINE=MyISAM");
		
	$db->write_query("INSERT INTO ".TABLE_PREFIX."mysb_shouts VALUES (NULL, 1, 'Test Shout! Without any shout, shoutbox will display Loading... forever.. you need at least one shout, so here it is.', ".time().", '127.0.0.1', 'no')");
	
	$db->write_query("ALTER TABLE `".TABLE_PREFIX."users` ADD `mysb_banned` smallint(1) NOT NULL DEFAULT 0;");
	$db->write_query("ALTER TABLE `".TABLE_PREFIX."users` ADD `mysb_banned_reason` varchar(255) NOT NULL DEFAULT '';");
	
	// rebuild settings...
	rebuild_settings();
}

function myshoutbox_activate()
{
	global $db, $mybb;
	
	// load templates
	$mysb_shoutbox_tpl = '
	<script type="text/javascript" src="jscripts/myshoutbox.js?ver=1400"></script>
<style type="text/css">

.shoutbox {
	margin: 0;
	padding: 0;
	left: 0;
}


li.shoutbox_normal {
	list-style: none;
	margin: 0;
	position: relative;
	cursor: pointer;
	color: transparent;
	display: inline ;
	border: 1px;
	border-color: #FFFFFF;
}

li.shoutbox_color {
	list-style: none;
	position: relative;
	cursor: pointer;
	color: transparent;
	display: inline ;
	border: 0px;
	float: left;
	margin: 1px;
}

.shoutbox_button_color a {
	width: 9px;
	height: 9px;
	display: block;
	border: 1px solid #FFF;
}

</style>

<table border="0" cellspacing="1" cellpadding="4" class="tborder">
<thead>
<tr>
<td class="thead" colspan="2">
<div class="expcolimage"><img src="{$theme[\'imgdir\']}/collapse.gif" id="shoutbox_img" class="expander" alt="[-]" /></div>
<div><strong>{$lang->mysb_shoutbox}</strong> (<a href="index.php?action=full_shoutbox">{$lang->mysb_fullsbox}</a> - <a href="pspshoutbox.php">{$lang->mysb_portable}</a>)<br /></div>
</td>
</tr>
</thead>

<tbody id="shoutbox_e">
<tr>
 <td class="trow2" width="66%" align="center"><form onsubmit="ShoutBox.postShout(); $(\'shout_data\').value = \'\'; return false;">{$lang->mysb_shout} <input type="text" id="shout_data" size="50" /> - <input type="submit" value="{$lang->mysb_shoutnow}" id="shouting-status" /></form></td>
 <td class="trow2" width="12%" align="center">{$lang->mysb_options}</td>
</tr>
<tr>
 <td class="trow1" width="76%"><div id="shoutbox_data" style="height: {$mybb->settings[\'mysb_height\']}px; overflow: auto;">{$lang->mysb_loading}</div></td>
 <td class="trow1" width="12%" align="center">
	<a style="cursor: pointer;" id="smilies" onclick="window.open(\'misc.php?action=smilies&amp;popup=true&amp;editor=clickableEditor\',\'{$lang->mysb_smilies}\',\'scrollbars=yes, menubar=no,width=460,height=360,toolbar=no\');">{$lang->mysb_smilies}</a>
	<br />
	<a style="cursor: pointer;" onclick="window.open(\'shoutbox.php\',\'{$lang->mysb_shoutbox}\',\'scrollbars=yes, menubar=no,width=825,height=449,toolbar=no\');">{$lang->mysb_popup_shoutbox}</a>
 </td>
</tr>
</tbody>
</table>

<script type="text/javascript">
ShoutBox.refreshInterval = {$mybb->settings[\'mysb_refresh_interval\']};
ShoutBox.MaxEntries = {$mybb->settings[\'mysb_shouts_main\']};
ShoutBox.lang = [\'{$lang->mysb_posting}\', \'{$lang->mysb_shoutnow}\', \'{$lang->mysb_loading}\', \'{$lang->mysb_flood_check}\', \'{$lang->mysb_no_perform}\', \'{$lang->mysb_already_sent}\', \'{$lang->mysb_deleted}\', \'{$lang->mysb_invalid}\', \'{$lang->mysb_self}\', \'{$lang->mysb_report_invalid_sid}\', \'{$lang->mysb_shout_reported}\', \'{$lang->mysb_shout_already_reported}\'];
{$extra_js}
Event.observe(window, \'load\', ShoutBox.showShouts); 
</script>

<br />';

	$mysb_boxfull_tpl = '<html>
<head>
<title>Full Shoutbox</title>
{$headerinclude}
</head>
<body>
{$header}

<table border="0" cellspacing="1" cellpadding="4" class="tborder">
<thead>
<tr>
<td class="thead" colspan="2">
<div><strong>{$lang->mysb_shoutbox}</strong><br /></div>
</td>
</tr>
</thead>

<tr>
 {$mysb_shoutbox_data}
</tr>
</table>

<br />

<center>$multipage</center>

{$footer}
</body>
</html>
';

	$mysb_popup_shoutbox_tpl = '
	<html>
<head>
<title>{$lang->mysb_shoutbox}</title>
{$headerinclude}
<script type="text/javascript" src="jscripts/myshoutbox.js?ver=1400"></script>
</head>
<body>

<style type="text/css">

.shoutbox {
	margin: 0;
	padding: 0;
	left: 0;
}


li.shoutbox_normal {
	list-style: none;
	margin: 0;
	position: relative;
	cursor: pointer;
	color: transparent;
	display: inline ;
	border: 1px;
	border-color: #FFFFFF;
}

li.shoutbox_color {
	list-style: none;
	position: relative;
	cursor: pointer;
	color: transparent;
	display: inline ;
	border: 0px;
	float: left;
	margin: 1px;
}

.shoutbox_button_color a {
	width: 9px;
	height: 9px;
	display: block;
	border: 1px solid #FFF;
}

</style>

<table border="0" cellspacing="1" cellpadding="4" class="tborder">
<thead>
<tr>
<td class="thead" colspan="2">
<div class="expcolimage"><img src="{$theme[\'imgdir\']}/collapse.gif" id="shoutbox_img" class="expander" alt="[-]" /></div>
<div><strong>{$lang->mysb_shoutbox}</strong> (<a href="index.php?action=full_shoutbox">{$lang->mysb_fullsbox}</a> - <a href="pspshoutbox.php">{$lang->mysb_portable}</a>)<br /></div>
</td>
</tr>
</thead>

<tbody id="shoutbox_e">
<tr>
 <td class="trow2" width="66%" align="center"><form onSubmit="ShoutBox.postShout(); $(\'shout_data\').value = \'\'; return false;">{$lang->mysb_shout} <input type="text" id="shout_data" size="50" /> - <input type="submit" value="{$lang->mysb_shoutnow}" id="shouting-status" /></form></td>
 <td class="trow2" width="12%" align="center">{$lang->mysb_options}</td>
</tr>
<tr>
 <td class="trow1" width="76%"><div align="left" id="shoutbox_data" style="height: {$mybb->settings[\'mysb_height\']}px; overflow: auto;">{$lang->mysb_loading}</div></td>
  <td class="trow1" width="12%" align="center">
	<a style="cursor: pointer;" id="smilies" onclick="window.open(\'misc.php?action=smilies&popup=true&editor=clickableEditor\',\'{$lang->mysb_smilies}\',\'scrollbars=yes, menubar=no,width=460,height=360,toolbar=no\');">{$lang->mysb_smilies}</a>
	<br />
	<a style="cursor: pointer;" onclick="window.open(\'shoutbox.php\',\'{$lang->mysb_shoutbox}\',\'scrollbars=yes, menubar=no,width=825,height=449,toolbar=no\');">{$lang->mysb_popup_shoutbox}</a>
 </td>
</tbody>
</table>

<script>
ShoutBox.refreshInterval = {$mybb->settings[\'mysb_refresh_interval\']};
ShoutBox.MaxEntries = {$mybb->settings[\'mysb_shouts_main\']};
ShoutBox.lang = [\'{$lang->mysb_posting}\', \'{$lang->mysb_shoutnow}\', \'{$lang->mysb_loading}\', \'{$lang->mysb_flood_check}\', \'{$lang->mysb_no_perform}\', \'{$lang->mysb_already_sent}\', \'{$lang->mysb_deleted}\', \'{$lang->mysb_invalid}\', \'{$lang->mysb_self}\', \'{$lang->mysb_report_invalid_sid}\', \'{$lang->mysb_shout_reported}\', \'{$lang->mysb_shout_already_reported}\'];
{$extra_js}
Event.observe(window, \'load\', ShoutBox.showShouts); 
</script>

</body>
</html>';

	$mysb_portable_tpl = '
	<html>
<head>
<title>{$lang->mysb_shoutbox}</title>
{$headerinclude}
<!--<SCRIPT>var timeID = setTimeout("document.forms[0].submit()", 30000)</SCRIPT>-->
</head>
<body>

<form id="0" action="pspshoutbox.php"></form>

<table border="0" cellspacing="1" cellpadding="4" class="tborder">
<thead>
<tr>
<td class="thead" colspan="2">
<form id="1" action="pspshoutbox.php?action=shout" method="post">{$lang->mysb_shout} <input type="hidden" name="postcode" value="{$mybb->post_code}" /> <input type="text" name="shout_data" size="50" /> - <input type="submit" value="{$lang->mysb_shoutnow}" id="shouting-status" /></form>
</td>
</tr>
<tr>
<td class="thead" colspan="2">
<div><strong>{$lang->mysb_shoutbox}</strong> - <!--<a href="pspshoutbox.php">Refresh</a> --><small>(<a href="pspshoutbox.php?action=refresh">Refresh</a>)</small> <br /></div>
</td>
</tr>
</thead>

<tr>
 {$mysb_shoutbox_data}
</tr>
</table>

</body>

</html>';

	$mysb_banned = '<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td class="thead"><strong>{$lang->mysb_shoutbox}</strong></td>
</tr>
<tr>
<td class="trow1">{$lang->mysb_error_ban}</td>
</tr>
</table><br />';

	
	// insert templates
	$db->insert_query('templates', array('title' => 'mysb_shoutbox', 'sid' => '-1', 'template' => $db->escape_string($mysb_shoutbox_tpl), 'version' => '1411', 'status' => '', 'dateline' => TIME_NOW));
	$db->insert_query('templates', array('title' => 'mysb_shoutbox_full', 'sid' => '-1', 'template' => $db->escape_string($mysb_boxfull_tpl), 'version' => '1411', 'status' => '', 'dateline' => TIME_NOW));
	$db->insert_query('templates', array('title' => 'mysb_shoutbox_popup', 'sid' => '-1', 'template' => $db->escape_string($mysb_popup_shoutbox_tpl), 'version' => '1411', 'status' => '', 'dateline' => TIME_NOW));
	$db->insert_query('templates', array('title' => 'mysb_shoutbox_psp', 'sid' => '-1', 'template' => $db->escape_string($mysb_portable_tpl), 'version' => '1411', 'status' => '', 'dateline' => TIME_NOW));
	$db->insert_query('templates', array('title' => 'mysb_shoutbox_banned', 'sid' => '-1', 'template' => $db->escape_string($mysb_banned), 'version' => '1411', 'status' => '', 'dateline' => TIME_NOW));
	
	require_once MYBB_ROOT.'inc/adminfunctions_templates.php';
	
	find_replace_templatesets('index', '#{\$boardstats}#', "{myshoutbox_".$mybb->settings['mysb_key']."}\n{\$boardstats}");

}

function myshoutbox_uninstall()
{
	global $db;
	
	$db->write_query("DROP TABLE ".TABLE_PREFIX."mysb_shouts");
	$db->write_query("DROP TABLE ".TABLE_PREFIX."mysb_reports");
	$db->write_query("DELETE FROM ".TABLE_PREFIX."settinggroups WHERE name = 'mysb_shoutbox'");
	$db->write_query("DELETE FROM ".TABLE_PREFIX."settings WHERE name IN('mysb_shouts_main','mysb_refresh_interval','mysb_allow_mycode',
							'mysb_allow_smilies','mysb_allow_imgcode','mysb_height','mysb_datetime','mysb_full_ppage','mysb_allow_smods',
							'mysb_allow_html','mysb_flood_time','mysb_usergroups','mysb_additional_groups','mysb_allow_mods','mysb_display_message','mysb_allow_video')");
	
	$db->write_query("ALTER TABLE `".TABLE_PREFIX."users` DROP `mysb_banned`;");
	$db->write_query("ALTER TABLE `".TABLE_PREFIX."users` DROP `mysb_banned_reason`;");
}

function myshoutbox_is_installed()
{
	global $db;
	
	if ($db->table_exists('mysb_shouts'))
		return true;
	
	return false;
}

function myshoutbox_deactivate()
{
	global $db, $mybb;
	$db->write_query("DELETE FROM ".TABLE_PREFIX."templates WHERE title IN('mysb_shoutbox','mysb_shoutbox_full','mysb_shoutbox_popup','mysb_shoutbox_popup_full','mysb_shoutbox_psp','mysb_shoutbox_banned') AND sid='-1'");
	
	require_once MYBB_ROOT.'inc/adminfunctions_templates.php';

	find_replace_templatesets('index', '#{myshoutbox_'.$mybb->settings['mysb_key'].'}#', '', 0);
}

function myshoutbox_load()
{
	global $mybb, $lang, $charset;

	$lang->load("myshoutbox");
	
	// Send our headers.
	header("Content-type: text/html; charset={$charset}");
	
	switch ($mybb->input['action'])
	{
		case 'show_shouts':
			myshoutbox_show_shouts(intval($mybb->input['last_id']));
		break;
			
		case 'add_shout':
			myshoutbox_add_shout();
		break;
		
		case 'delete_shout':
			myshoutbox_delete_shout(intval($mybb->input['id']));
		break;
			
		case 'remove_shout':
			myshoutbox_remove_shout(intval($mybb->input['id']));
		break;
		
		case 'recover_shout':
			myshoutbox_recover_shout(intval($mybb->input['id']));
		break;
		
		case 'report_shout':
			myshoutbox_report_shout($mybb->input['reason'], intval($mybb->input['sid']));
		break;
	}
}

function myshoutbox_psp_show()
{
	global $db, $mybb, $templates, $lang, $footer, $headerinclude, $header, $charset;
	
	$lang->load('myshoutbox');
	
	// Send our headers.
	header("Content-type: text/html; charset={$charset}");
	
	// Make navigation
	add_breadcrumb($lang->mysb_shoutbox, "pspshoutbox.php");
	$per_page = intval($mybb->settings['mysb_full_ppage']);

	// pagination
	$query = $db->simple_select("mysb_shouts", "COUNT(*) as shouts_count");
	$shouts_count = $db->fetch_field($query, 'shouts_count');
	
	// Pagination
	$per_page = intval($mybb->settings['mysb_full_ppage']);;
	if(intval($mybb->input['page']) > 0)
	{
		$page = (int)$mybb->input['page'];
		$start = ($page-1) * $per_page;
		$pages = $shouts_count / $per_page;
		$pages = ceil($pages);
		if($page > $pages)
		{
			$start = 0;
			$page = 1;
		}
	}
	else
	{
		$start = 0;
		$page = 1;
	}
	
	// multi-page
	if ($shouts_count > $per_page) {
		$multipage = multipage($shouts_count, $per_page, $page, "pspshoutbox.php?action=full");		
	}
	
	// get data
	require_once MYBB_ROOT.'inc/class_parser.php';
	$parser = new postParser;
	
	$usernames_cache = array();
	
	$query = $db->write_query("SELECT s.*, u.username, u.usergroup, u.displaygroup FROM ".TABLE_PREFIX."mysb_shouts s 
							LEFT JOIN ".TABLE_PREFIX."users u ON (u.uid = s.uid) 
						 ORDER by s.id DESC LIMIT {$start}, {$per_page}");
	
	while ($row = $db->fetch_array($query))
	{
		$parser_options = array(
				'allow_mycode' => $mybb->settings['mysb_allow_mycode'],
				'allow_smilies' => $mybb->settings['mysb_allow_smilies'],
				'allow_imgcode' => $mybb->settings['mysb_allow_imgcode'],
				'allow_html' => $mybb->settings['mysb_allow_html'],
				"allow_videocode" => $mybb->settings['mysb_allow_video'],
				'me_username' => $row['username']
			);
			
		$message = $parser->parse_message($row['shout_msg'], $parser_options);
		
		$find = stripos($message, "/pvt");
		if($find == 0 && $find !== false)
		{
			sscanf($message, "/pvt %d", $userID);
			$userID = (int)$userID;
			$message = str_replace("/pvt ".$userID." ", "", $message);
			if ($mybb->user['uid'] == intval($userID) || $mybb->user['uid'] == $row['uid'])
			{
				if ($mybb->user['uid'] == intval($userID))
				{
					$userName = $mybb->user['username'];
				}
				else {
					// Unfortunately, we do not have this username...let's check our cache, if it's not in cache, query it
					if (!empty($usernames_cache[$userID]))
					{
						$userName = $usernames_cache[$userID];
					}
					else {
						$userName = $db->fetch_field($db->simple_select('users', 'username', 'uid=\''.$userID.'\''), 'username');
						$usernames_cache[$userID] = $userName;
					}
				}
				
				$message = "<span style=\"background-color: #AF4300; font-weight: bold;\">{$lang->mysb_pvt_to} ".htmlspecialchars_uni($userName).": ".$message."</span>";
		
				$row['username'] = format_name($row['username'], $row['usergroup'], $row['displaygroup']);

				$extra = ($mybb->usergroup['cancp'] == 1 ? "title='{$row[shout_ip]}'" : "");

				$username = '<a href="./member.php?action=profile&uid='.$row['uid'].'" {$extra}>'.$row['username'].'</a>';
				$date_time = my_date($mybb->settings['mysb_datetime'], $row['shout_date']);
				$class = alt_trow();
				
				if (myshoutbox_can_delete() && $row['hidden'] == "yes") { 
					$mysb_shoutbox_data .= "<tr id='shout-{$row[id]}'><td align=\"left\" class='{$class}'>&raquo; <strong><span style=\"color: #FF0000\";>{$lang->mysb_deleted_info}</span></strong> &raquo; {$username} - {$date_time} -- {$message}</td></tr>";
				}
				elseif ($row['hidden'] == "no")
				{
					$mysb_shoutbox_data .= "<tr id='shout-{$row[id]}'><td align=\"left\" class='{$class}'>&raquo; {$username} - {$date_time} -- {$message}</td></tr>";
				}
			}
		}		
		else {
			$row['username'] = format_name($row['username'], $row['usergroup'], $row['displaygroup']);

			$extra = ($mybb->usergroup['cancp'] == 1 ? "title='{$row[shout_ip]}'" : "");

			$username = '<a href="./member.php?action=profile&uid='.$row['uid'].'" {$extra}>'.$row['username'].'</a>';
			$date_time = my_date($mybb->settings['mysb_datetime'], $row['shout_date']);
			$class = alt_trow();
			
			if (myshoutbox_can_delete() && $row['hidden'] == "yes") { 
				$mysb_shoutbox_data .= "<tr id='shout-{$row[id]}'><td align=\"left\" class='{$class}'><span style=\"font-size: {$mybb->settings['mysb_text_size']}px\">&raquo; <strong><span style=\"color: #FF0000\";>{$lang->mysb_deleted_info}</span></strong> &raquo; {$username} - {$date_time} -- {$message}</span></td></tr>";
			}
			elseif ($row['hidden'] == "no")
			{
				$mysb_shoutbox_data .= "<tr id='shout-{$row[id]}'><td align=\"left\" class='{$class}'><span style=\"font-size: {$mybb->settings['mysb_text_size']}px\">&raquo; {$username} - {$date_time} -- {$message}</span></td></tr>";
			}
		}
	}
	
	
	eval("\$shoutbox = \"".$templates->get("mysb_shoutbox_psp")."\";");
	
	$db->write_query("SELECT * FROM ".TABLE_PREFIX."mysb_shouts ORDER by id DESC LIMIT 10");
	
	output_page($shoutbox);
	exit;
}

function myshoutbox_show_full()
{
	global $db, $mybb, $templates, $lang, $footer, $headerinclude, $header, $charset;
	
	$lang->load('myshoutbox');
	
	// Send our headers.
	header("Content-type: text/html; charset={$charset}");
	
	// Make navigation
	add_breadcrumb($lang->mysb_shoutbox, "index.php?action=full_shoutbox");

	// pagination
	$query = $db->simple_select("mysb_shouts", "COUNT(*) as shouts_count");
	$shouts_count = $db->fetch_field($query, 'shouts_count');
	
	// Pagination
	$per_page = intval($mybb->settings['mysb_full_ppage']);;
	if(intval($mybb->input['page']) > 0)
	{
		$page = (int)$mybb->input['page'];
		$start = ($page-1) * $per_page;
		$pages = $shouts_count / $per_page;
		$pages = ceil($pages);
		if($page > $pages)
		{
			$start = 0;
			$page = 1;
		}
	}
	else
	{
		$start = 0;
		$page = 1;
	}
	
	// multi-page
	if ($shouts_count > $per_page) {
		$multipage = multipage($shouts_count, $per_page, $page, "index.php?action=full_shoutbox");		
	}
	
	// get data
	require_once MYBB_ROOT.'inc/class_parser.php';
	$parser = new postParser;
	
	$usernames_cache = array();
	
	$query = $db->write_query("SELECT s.*, u.username, u.usergroup, u.displaygroup FROM ".TABLE_PREFIX."mysb_shouts s 
							LEFT JOIN ".TABLE_PREFIX."users u ON (u.uid = s.uid) 
						 ORDER by s.id DESC LIMIT {$start}, {$per_page}");
	
	while ($row = $db->fetch_array($query))
	{
		$parser_options = array(
				'allow_mycode' => $mybb->settings['mysb_allow_mycode'],
				'allow_smilies' => $mybb->settings['mysb_allow_smilies'],
				'allow_imgcode' => $mybb->settings['mysb_allow_imgcode'],
				'allow_html' => $mybb->settings['mysb_allow_html'],
				"allow_videocode" => $mybb->settings['mysb_allow_video'],
				'me_username' => $row['username']
			);		
			
		$message = $parser->parse_message($row['shout_msg'], $parser_options);

		$find = stripos($message, "/pvt");
		if($find == 0 && $find !== false)
		{
			sscanf($message, "/pvt %d", $userID);
			$userID = (int)$userID;
			$message = str_replace("/pvt ".$userID." ", "", $message);
			if ($mybb->user['uid'] == intval($userID) || $mybb->user['uid'] == $row['uid'])
			{
				if ($mybb->user['uid'] == intval($userID))
				{
					$userName = $mybb->user['username'];
				}
				else {
					// Unfortunately, we do not have this username...let's check our cache, if it's not in cache, query it
					if (!empty($usernames_cache[$userID]))
					{
						$userName = $usernames_cache[$userID];
					}
					else {
						$userName = $db->fetch_field($db->simple_select('users', 'username', 'uid=\''.$userID.'\''), 'username');
						$usernames_cache[$userID] = $userName;
					}
				}
				
				$message = "<span style=\"background-color: #AF4300; font-weight: bold;\">{$lang->mysb_pvt_to} ".htmlspecialchars_uni($userName).": ".$message."</span>";
		
				$row['username'] = format_name($row['username'], $row['usergroup'], $row['displaygroup']);

				$extra = ($mybb->usergroup['cancp'] == 1 ? "title='{$row[shout_ip]}'" : "");

				$username = '<a href="./member.php?action=profile&uid='.$row['uid'].'" {$extra}>'.$row['username'].'</a>';
				$date_time = my_date($mybb->settings['mysb_datetime'], $row['shout_date']);
				$class = alt_trow();
	
				$mysb_shoutbox_data .= "<tr id='shout-{$row[id]}'><td class='{$class}'><span style=\"font-size: {$mybb->settings['mysb_text_size']}px\">&raquo; {$report}{$username} - {$date_time} -- {$message}</span></td></tr>";
			}
		}		
		else {
			$row['username'] = format_name($row['username'], $row['usergroup'], $row['displaygroup']);

			$extra = ($mybb->usergroup['cancp'] == 1 ? "title='{$row[shout_ip]}'" : "");

			$username = '<a href="./member.php?action=profile&uid='.$row['uid'].'" {$extra}>'.$row['username'].'</a>';
			$date_time = my_date($mybb->settings['mysb_datetime'], $row['shout_date']);
			$class = alt_trow();
		
			$mysb_shoutbox_data .= "<tr id='shout-{$row[id]}'><td class='{$class}'><span style=\"font-size: {$mybb->settings['mysb_text_size']}px\">&raquo; {$report}{$username} - {$date_time} -- {$message}</span></td></tr>";
		}
	}
	
	
	eval("\$shoutbox_full = \"".$templates->get("mysb_shoutbox_full")."\";");
	
	$db->write_query("SELECT * FROM ".TABLE_PREFIX."mysb_shouts ORDER by id DESC LIMIT 10");
	
	output_page($shoutbox_full);
	exit;
}

function myshoutbox_index()
{
	global $mybb, $lang;
	
	// show full shoutbox
	if ($mybb->input['action'] == 'full_shoutbox')
	{
		$lang->load('myshoutbox');
	
		$perms = myshoutbox_can_view();

		if ($perms && $perms !== 2) {

			myshoutbox_show_full();
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
}

/**
 * Add shoutbox template before output 
 */
function myshoutbox_output_control(&$page_data)
{
	global $mybb, $templates, $mysb_shoutbox, $lang, $theme, $db, $mysb_message;
	
	$perms = myshoutbox_can_view();
	
	if ($perms && $perms !== 2) {
		$lang->load('myshoutbox');
	
		// no shout button for guests
		if ($mybb->user['usergroup'] == 1)
			$extra_js = "ShoutBox.disableShout();";
		else
			$extra_js = "";
		
		eval("\$mysb_shoutbox = \"".$templates->get("mysb_shoutbox")."\";");
	}
	elseif ($perms === 2 && $mybb->settings['mysb_display_message'] == 1)
	{
		$lang->load('myshoutbox');
		
		$lang->mysb_error_ban = $lang->sprintf($lang->mysb_error_ban, htmlspecialchars_uni($mybb->user['mysb_banned_reason']));
	
		// display banned from shoutbox message
		eval("\$mysb_shoutbox = \"".$templates->get("mysb_shoutbox_banned")."\";");
	}
	else {
		$mysb_shoutbox = '';
	}

	return str_replace('{myshoutbox_'.$mybb->settings['mysb_key'].'}', $mysb_shoutbox, $page_data); // still allow the shoutbox to be placed anywhere the admin wants
}

function myshoutbox_show_shouts($last_id = 0)
{
	global $db, $mybb, $parser, $charset, $lang;
	
	$perms = myshoutbox_can_view();
	if (!$perms || $perms === 2) return;
	
	require_once MYBB_ROOT.'inc/class_parser.php';
	$parser = new postParser;
	
	$last_id = (int)$last_id; // not needed here since when we call the function it converts $last_id to int already

	$query = $db->write_query("SELECT s.*, u.username, u.usergroup, u.displaygroup FROM ".TABLE_PREFIX."mysb_shouts s 
							LEFT JOIN ".TABLE_PREFIX."users u ON (u.uid = s.uid) 
						WHERE s.id>{$last_id} ORDER by s.id DESC LIMIT {$mybb->settings['mysb_shouts_main']}");
	
	// fetch results 
	$messages = "";
	$entries = 0;
	$usernames_cache = array();
	while ($row = $db->fetch_array($query))
	{
		$report = "(<a id=\"report_".$row['id']."\" href=\"#shoutbox\" onclick=\"javascript: return ShoutBox.promptReason(".$row['id'].");\" style=\"cursor: pointer;\">{$lang->mysb_report_button}</a>) ";
		
		$parser_options = array(
				'allow_mycode' => $mybb->settings['mysb_allow_mycode'],
				'allow_smilies' => $mybb->settings['mysb_allow_smilies'],
				'allow_imgcode' => $mybb->settings['mysb_allow_imgcode'],
				'allow_html' => $mybb->settings['mysb_allow_html'],
				"allow_videocode" => $mybb->settings['mysb_allow_video'],
				'me_username' => $row['username']
			);		
			
		$message = $parser->parse_message($row['shout_msg'], $parser_options);
		
		$find = stripos($message, "/pvt");
		if($find == 0 && $find !== false)
		{
			sscanf($message, "/pvt %d", $userID);
			$userID = (int)$userID;
			$message = str_replace("/pvt ".$userID." ", "", $message);
			if ($mybb->user['uid'] == intval($userID) || $mybb->user['uid'] == $row['uid'])
			{
				if ($mybb->user['uid'] == intval($userID))
				{
					$userName = $mybb->user['username'];
				}
				else {
					// Unfortunately, we do not have this username...let's check our cache, if it's not in cache, query it
					if (!empty($usernames_cache[$userID]))
					{
						$userName = $usernames_cache[$userID];
					}
					else {
						$userName = $db->fetch_field($db->simple_select('users', 'username', 'uid=\''.$userID.'\''), 'username');
						$usernames_cache[$userID] = $userName;
					}
				}
				
				$message = "<span style=\"background-color: #AF4300; font-weight: bold;\">{$lang->mysb_pvt_to} ".htmlspecialchars_uni($userName).": ".$message."</span>";
			
				$row['username'] = format_name($row['username'], $row['usergroup'], $row['displaygroup']);
				$extra = ($mybb->usergroup['cancp'] == 1 ? "title='{$row[shout_ip]}'" : "");
		
				$username = $row['username'];
				$date_time = my_date($mybb->settings['mysb_datetime'], $row['shout_date']);

				if (myshoutbox_can_delete()) {
					$delete = "(<a href='#shoutbox' onclick='javascript: return ShoutBox.deleteShout({$row[id]}, 1,\"{$lang->mysb_delconfirm}\");'>{$lang->mysb_delete}</a>) ";
					if ($row['hidden'] == "yes"){
						$recover = "(<a href='#shoutbox' onclick='javascript: return ShoutBox.recoverShout({$row[id]}, 1, \"{$lang->mysb_recconfirm}\");'>{$lang->mysb_recover}</a>) ";
						$remove = "(<a href='#shoutbox' onclick='javascript: return ShoutBox.removeShout({$row[id]}, 1, \"{$lang->mysb_remconfirm}\");'>{$lang->mysb_remove}</a>) ";
					}
				}
				else {
					$delete = '&nbsp;';
					$recover = '&nbsp;';
					$remove = '&nbsp;';
				}
		
				if (myshoutbox_can_delete() && $row['hidden'] == "yes") { 
					$messages .= "<span style=\"font-size: {$mybb->settings['mysb_text_size']}px\">&raquo; <strong><span style=\"color: #FF0000\";>{$lang->mysb_deleted_info}</span></strong> &raquo; {$remove}{$recover}{$report}<a href='#' onClick=\"javascript: ShoutBox.pvtAdd(".$row['uid']."); return false;\" {$extra}>{$username}</a> - {$date_time} -- {$message}</span><br />\r\n";
				}
				elseif ($row['hidden'] == "no") $messages .= "<span style=\"font-size: {$mybb->settings['mysb_text_size']}px\">&raquo; {$delete}{$recover}{$report}<span style=\"\"><a href='#' onClick=\"javascript: ShoutBox.pvtAdd(".$row['uid']."); return false;\" {$extra}>{$username}</a></span> - {$date_time} -- {$message}</span><br />\r\n";
		
				$entries++;
		
				if ($entries == 1) {
					$maxid = $row['id'];
				}
			}
		}
		else {
			$row['username'] = format_name($row['username'], $row['usergroup'], $row['displaygroup']);
		
			$extra = ($mybb->usergroup['cancp'] == 1 ? "title='{$row[shout_ip]}'" : "");
		
			$username = ''.$row['username'].'';
			$date_time = my_date($mybb->settings['mysb_datetime'], $row['shout_date']);

			if (myshoutbox_can_delete()) {
				$delete = "(<a href='#shoutbox' onclick='javascript: return ShoutBox.deleteShout({$row[id]}, 1,\"{$lang->mysb_delconfirm}\");'>{$lang->mysb_delete}</a>) ";
				if ($row['hidden'] == "yes"){
					$recover = "(<a href='#shoutbox' onclick='javascript: return ShoutBox.recoverShout({$row[id]}, 1, \"{$lang->mysb_recconfirm}\");'>{$lang->mysb_recover}</a>) ";
					$remove = "(<a href='#shoutbox' onclick='javascript: return ShoutBox.removeShout({$row[id]}, 1, \"{$lang->mysb_remconfirm}\");'>{$lang->mysb_remove}</a>) ";
				}
			}
			else {
				$delete = '&nbsp;';
				$recover = '&nbsp;';
				$remove = '&nbsp;';
			}
		
			if (myshoutbox_can_delete() && $row['hidden'] == "yes") { 
				$messages .= "<span style=\"font-size: {$mybb->settings['mysb_text_size']}px\">&raquo; <strong><span style=\"color: #FF0000\";>{$lang->mysb_deleted_info}</span></strong> &raquo; {$remove}{$recover}{$report}<a href='#' onClick=\"javascript: ShoutBox.pvtAdd(".$row['uid']."); return false;\" {$extra}>{$username}</a> - {$date_time} -- {$message}</span><br />\r\n";
			}
			elseif ($row['hidden'] == "no") $messages .= "<span style=\"font-size: {$mybb->settings['mysb_text_size']}px\">&raquo; {$delete}{$recover}{$report}<a href='#' onClick=\"javascript: ShoutBox.pvtAdd(".$row['uid']."); return false;\" {$extra}>{$username}</a> - {$date_time} -- {$message}</span><br />\r\n";
		
			$entries++;
		
			if ($entries == 1) {
				$maxid = $row['id'];
			}
		}
	}
	
	if (!$maxid) {
		$maxid = $last_id;
	}
	
	echo "{$maxid}^--^{$entries}^--^{$messages}^--^{$chat_messages}";
	exit;
}

function myshoutbox_report_shout($reason, $sid)
{
	global $db, $mybb;
	
	$sid = intval($sid); // shout id
	
	if ($mybb->user['uid'] <= 0)
		return false; // guests can't report shouts
	
	// cannot report an invalid shout
	// get shout
	$query = $db->simple_select('mysb_shouts', '*', 'id=\''.intval($sid).'\'');
	$shout = $db->fetch_array($query);
	if (empty($shout))
	{
		echo "invalid_shout";
		exit;
	}

	// make sure we haven't reported it already
	if (($rid = $db->fetch_field($db->simple_select('mysb_reports', 'rid', 'sid='.intval($sid).' AND username=\''.$db->escape_string($mybb->user['username']).'\''),'rid')))
	{
		echo "already_reported";
		exit;
	}
	
	// get username of the author of the shout
	$query = $db->simple_select('users', 'username', 'uid=\''.intval($shout['uid']).'\'');
	$username = $db->fetch_field($query, 'username');
	
	$report = array(
			'username' => $db->escape_string($mybb->user['username']),
			'uid' => intval($mybb->user['uid']),
			'reason' => $db->escape_string($reason),
			'date' => TIME_NOW,
			'sid' => $sid,
			'author_uid' => intval($shout['uid']),
			'author_username' => $db->escape_string($username)
	);
		
	$db->insert_query('mysb_reports', $report);
	
	echo 'shout_reported';
	exit;
}

function myshoutbox_psp_add_shout()
{
	global $db, $mybb;
	
	$perms = myshoutbox_can_view();
	
	// guests not allowed! neither banned people
	if (!$perms || $perms === 2 || $mybb->user['usergroup'] == 1 || !$mybb->user['uid'])
	{
		die("failed!");
	}
	
	$shout_data = array(
			'uid' => (int)$mybb->user['uid'],
			'shout_msg' => $db->escape_string(str_replace('^--^', '-', $mybb->input['shout_data'])),
			'shout_date' => TIME_NOW,
			'shout_ip' => get_ip(),
			'hidden' => "no"
		);
		
	if ($db->insert_query('mysb_shouts', $shout_data)) {
		redirect("pspshoutbox.php", "Success! Redirecting..", "Success!");
	} else {
		redirect("pspshoutbox.php", "Failed! Redirecting..", "Failed!");
	}
	
	exit;
}

function myshoutbox_add_shout()
{
	global $db, $mybb;
	
	$perms = myshoutbox_can_view();

	// guests not allowed! neither banned users
	if (!$perms || $perms === 2 || $mybb->user['usergroup'] == 1 || $mybb->user['uid'] < 1)
	{
		die("failed!");
	}
	
	// purge database?
	$postData = trim($mybb->input['shout_data']);
	if ($mybb->usergroup['cancp'] == 1 && substr($postData, 0, 7) == '/delete') {
		
		preg_match('/\/delete\s{1,}(all|older than|newer than)($|\s{1,}([0-9]+\-[0-9]+\-[0-9]+)|\s{1,}[0-9]+)/i', $postData, $match);
		
		// we have date?
		if (stristr($match[2], '-'))
			$date = explode('-', $match[2]);
		
		// purge?
		if ($match[1] == 'all') {
			$db->delete_query('mysb_shouts');
			$db->insert_query('mysb_shouts', array('uid' => intval($mybb->user['uid']), 'shout_msg' => 'First shout', 'shout_date' => time(), 'shout_ip' => get_ip()));
		}
		elseif (strtolower($match[1]) == 'older than') 
		{
			
			if ($date) {
				$timeStamp = mktime(23, 59, 59, $date[0], ($date[1]-1), $date[2]);
				$db->delete_query('mysb_shouts', 'shout_date <= ' . $timeStamp);
			}
			else // delete based on id
				$db->delete_query('mysb_shouts', 'id < ' . intval($match[2]));
		}
		elseif (strtolower($match[1]) == 'newer than') 
		{
			if ($date) {

				$timeStamp = mktime(1, 1, 1, $date[0], ($date[1]+1), $date[2]);
				$db->delete_query('mysb_shouts', 'shout_date >= ' . $timeStamp);
			}
			else // delete based on id
				$db->delete_query('mysb_shouts', 'id > ' . intval($match[2]));
		}
		
		die("deleted");
	}
	
	// flood check
	if (intval($mybb->settings['mysb_flood_time']) && !is_moderator()) {
		$lastShout = $db->fetch_field($db->simple_select('mysb_shouts', 'MAX(shout_date) as lastShout', 'uid = '.intval($mybb->user['uid'])), 'lastShout');
		$interval = time() - $lastShout;
		
		if ($interval <= $mybb->settings['mysb_flood_time'])
			die("flood|" . ($mybb->settings['mysb_flood_time'] - $interval));
	}
	
	// Uid cannot be lower than 1
	$ret = sscanf($postData, "/pvt %d", $userID);
	if ($ret)
	{
		$userID = (int)$userID;
		if ($userID < 1)
			die("failed!");
	}
	
	$shout_data = array(
			'uid' => $mybb->user['uid'],
			'shout_msg' => $db->escape_string(str_replace('^--^', '-', $mybb->input['shout_data'])),
			'shout_date' => time(),
			'shout_ip' => get_ip(),
			'hidden' => "no"
		);
		
	if ($db->insert_query('mysb_shouts', $shout_data)) {
		echo "success!!";
	} else {
		echo "failed!";
	}
	
	exit;
}

function myshoutbox_delete_shout($shout_id)
{
	global $db;
	
	$shout_id = intval($shout_id);
	
	if (myshoutbox_can_delete()) {
		$db->update_query("mysb_shouts", array('hidden' => "yes"), "id='".$shout_id."'", 1);
		echo "success!";
	}
	else
		echo "failed!";
	
	exit;
}

function myshoutbox_remove_shout($shout_id)
{
	global $db;
	
	$shout_id = intval($shout_id);
	
	if (myshoutbox_can_delete()) {
		$db->write_query("DELETE FROM ".TABLE_PREFIX."mysb_shouts WHERE id = {$shout_id}");
		echo "success!";
	}
	else
		echo "failed!";
	
	exit;
}

function myshoutbox_recover_shout($shout_id)
{
	global $db;
	
	$shout_id = intval($shout_id);
	
	if (myshoutbox_can_delete()) {
		$db->update_query("mysb_shouts", array('hidden' => "no"), "id='".$shout_id."'", 1);
		echo "success!";
	}
	else
		echo "failed!";
	
	exit;
}

function myshoutbox_can_delete()
{
	global $mybb;

	if ($mybb->usergroup['cancp'] == 1 || ($mybb->settings['mysb_allow_smods'] == 1 && $mybb->usergroup['issupermod'] == 1) || ($mybb->settings['mysb_allow_mods'] == 1 && $mybb->usergroup['canmodcp'] == 1))
		return true;
	
	return false;
}

function myshoutbox_can_view()
{
	global $mybb;
	
	if ($mybb->usergroup['canview'] == 0) // can view the board?
		return false;	
		
	// verify if is banned user before checking usergroups
	if (intval($mybb->user['mysb_banned']) == 1) // banned from the shoutbox
		return 2; // 2 means we have been banned
	
	if (empty($mybb->settings['mysb_usergroups']))
		return true;

	// can guests view the shoutbox?
	/*if ($mybb->settings['mysb_guestview'] == 0 && $mybb->user['usergroup'] == 1)
		return false;*/
		
	$groups = explode(",", $mybb->settings['mysb_usergroups']);
	$add_groups = "";
	
	if ($mybb->settings['mysb_additional_groups'] == 1 && $mybb->user['additionalgroups'])
		$add_groups = explode(",", $mybb->user['additionalgroups']);
	
	if (!in_array($mybb->user['usergroup'], $groups)) { // is the user allowed to view the shoutbox?
		// didn't find gid (primary) in allowed list, check additonal groups if setting is set to yes and if any were found
		
		if ($add_groups) {
			if (count(array_intersect($add_groups, $groups)) == 0)
				return false;
		}
		else 
			return false;
	}

	return true;

}

function myshoutbox_admin_home_menu(&$sub_menu)
{
	global $lang, $db;
	
	$lang->load('myshoutbox');

	$reports = $db->fetch_field($db->simple_select("mysb_reports", "COUNT(rid) as reports", "marked='0'"), "reports");
	
	//$reports = $db->fetch_field($db->simple_select("plaza_mydownloads_downloads", "COUNT(did) as comments", "hidden=0"), "comments");
	
	$sub_menu[] = array('id' => 'myshoutbox', 'title' => $lang->sprintf($lang->myshoutbox_unread_reports, $reports), 'link' => 'index.php?module=tools/myshoutbox&action=reports');
}

function myshoutbox_admin_tools_menu(&$sub_menu)
{
	global $lang;
	
	$lang->load('myshoutbox');
	$sub_menu[] = array('id' => 'myshoutbox', 'title' => $lang->myshoutbox_index, 'link' => 'index.php?module=tools/myshoutbox');
}

function myshoutbox_admin_tools_action_handler(&$actions)
{
	$actions['myshoutbox'] = array('active' => 'myshoutbox', 'file' => 'myshoutbox');
}

function myshoutbox_admin_permissions(&$admin_permissions)
{
  	global $db, $mybb, $lang;
  
	$lang->load("mysb_shoutbox", false, true);
	$admin_permissions['mysb_shoutbox'] = $lang->mysb_shoutbox_canmanage;
	
}

function myshoutbox_admin()
{
	global $db, $lang, $mybb, $page, $run_module, $action_file, $mybbadmin, $plugins;
	
	$lang->load("myshoutbox", false, true);
	
	if($run_module == 'tools' && $action_file == 'myshoutbox')
	{	
		if ($mybb->input['action'] == 'ban')
		{
			if ($mybb->request_method == "post")
			{
				if(!isset($mybb->input['my_post_key']) || $mybb->post_code != $mybb->input['my_post_key'] || !$mybb->input['username'])
				{
					$mybb->request_method = "get";
					flash_message($lang->myshoutbox_error, 'error');
					admin_redirect("index.php?module=tools/myshoutbox");
				}

				$db->update_query('users', array('mysb_banned' => 1, 'mysb_banned_reason' => $db->escape_string($mybb->input['reason'])), 'username=\''.$db->escape_string($mybb->input['username']).'\'', 1);
				
				$lang->myshoutbox_log_banned = $lang->sprintf($lang->myshoutbox_log_banned, $mybb->input['username']);
				log_admin_action($lang->myshoutbox_log_banned);
				
				flash_message($lang->myshoutbox_user_banned, 'success');
				admin_redirect("index.php?module=tools/myshoutbox");
			}
		}
		elseif ($mybb->input['action'] == 'unban')
		{
			if ($mybb->request_method == "post")
			{
				if(!isset($mybb->input['my_post_key']) || $mybb->post_code != $mybb->input['my_post_key'] || !$mybb->input['username'])
				{
					$mybb->request_method = "get";
					flash_message($lang->myshoutbox_error, 'error');
					admin_redirect("index.php?module=tools/myshoutbox");
				}
				
				$db->update_query('users', array('mysb_banned' => 0, 'mysb_banned_reason' => ''), 'username=\''.$db->escape_string($mybb->input['username']).'\'', 1);
				
				$lang->myshoutbox_log_unbanned = $lang->sprintf($lang->myshoutbox_log_unbanned, $mybb->input['username']);
				log_admin_action($lang->myshoutbox_log_unbanned);
				
				flash_message($lang->myshoutbox_user_unbanned, 'success');
				admin_redirect("index.php?module=tools/myshoutbox");
			}
		}
		elseif ($mybb->input['action'] == 'delete_report')
		{
			if ($mybb->request_method == "post")
			{
				if(!isset($mybb->input['my_post_key']) || $mybb->post_code != $mybb->input['my_post_key'] || !$mybb->input['rid'])
				{
					$mybb->request_method = "get";
					flash_message($lang->myshoutbox_error, 'error');
					admin_redirect("index.php?module=tools/myshoutbox");
				}
				
				// don't check if the report id exists, just try to delete it
				$db->delete_query('mysb_reports', 'rid='.intval($mybb->input['rid']), 1);
				
				log_admin_action($lang->myshoutbox_log_deleted_report);
				
				flash_message($lang->myshoutbox_report_deleted, 'success');
				admin_redirect("index.php?module=tools/myshoutbox&amp;action=reports");
			}
		}
		elseif ($mybb->input['action'] == 'mark_report')
		{
			if ($mybb->request_method == "post")
			{
				if(!isset($mybb->input['my_post_key']) || $mybb->post_code != $mybb->input['my_post_key'] || !$mybb->input['rid'])
				{
					$mybb->request_method = "get";
					flash_message($lang->myshoutbox_error, 'error');
					admin_redirect("index.php?module=tools/myshoutbox");
				}
				
				// don't check if the report id exists, just try to mark it as read
				$db->update_query('mysb_reports', array('marked' => 1), 'rid=\''.intval($mybb->input['rid']).'\'', 1);
				
				log_admin_action($lang->myshoutbox_log_marked_report);
				
				flash_message($lang->myshoutbox_report_marked, 'success');
				admin_redirect("index.php?module=tools/myshoutbox&amp;action=reports");
			}
		}
		elseif ($mybb->input['action'] == 'reports')
		{
			$page->add_breadcrumb_item($lang->myshoutbox_reported_shouts, 'index.php?module=tools/myshoutbox');
		
			$page->output_header($lang->myshoutbox_reported_shouts);
			
			$sub_tabs['myshoutbox'] = array(
				'title'			=> $lang->myshoutbox_home,
				'link'			=> 'index.php?module=tools/myshoutbox',
				'description'	=> $lang->myshoutbox_description
			);
			
			$sub_tabs['myshoutbox_reports'] = array(
				'title'			=> $lang->myshoutbox_reported_shouts." (".intval($db->fetch_field($db->simple_select("mysb_reports", "COUNT(rid) as reports", "marked='0'"), "reports")).")",
				'link'			=> 'index.php?module=tools/myshoutbox&amp;action=reports',
				'description'	=> $lang->myshoutbox_myshoutbox_reported_shouts_description
			);
			
			$page->output_nav_tabs($sub_tabs, 'myshoutbox_reports');

			// table
			$table = new Table;
			$table->construct_header($lang->myshoutbox_sid, array('width' => '10%'));
			$table->construct_header($lang->myshoutbox_username);
			$table->construct_header($lang->myshoutbox_reported_by);
			$table->construct_header($lang->myshoutbox_reason);
			$table->construct_header($lang->myshoutbox_date, array('width' => '15%'));
			$table->construct_header($lang->myshoutbox_view);
			$table->construct_header($lang->myshoutbox_delete);
			$table->construct_header($lang->myshoutbox_mark);
			
			// pagination
			$per_page = 15;
			if($mybb->input['page'] && intval($mybb->input['page']) > 1)
			{
				$mybb->input['page'] = intval($mybb->input['page']);
				$start = ($mybb->input['page']*$per_page)-$per_page;
			}
			else
			{
				$mybb->input['page'] = 1;
				$start = 0;
			}
			
			$query = $db->simple_select("mysb_reports", "COUNT(rid) as reports");
			$total_rows = $db->fetch_field($query, "reports");
		
			echo "<br />".draw_admin_pagination($mybb->input['page'], $per_page, $total_rows, "index.php?module=tools/myshoutbox&amp;action=reports&amp;page={page}");
			
			$query = $db->write_query("
				SELECT s.*, r.*
				FROM ".TABLE_PREFIX."mysb_reports r
				LEFT JOIN ".TABLE_PREFIX."mysb_shouts s ON (s.id=r.sid)
				ORDER BY r.date DESC LIMIT {$start}, {$per_page}
			");
			while($r = $db->fetch_array($query)) {
				
				if ($r['marked'] == 0)
				{
					$styles = 'background-color: #FFD7D7';
				}
				else
					$styles = '';
				
				$table->construct_cell(htmlspecialchars_uni($r['sid']), array('width' => '10%', 'style' => $styles));
				$table->construct_cell(build_profile_link($r['author_username'], $r['author_uid']), array('style' => $styles));
				$table->construct_cell(build_profile_link($r['username'], $r['uid']), array('style' => $styles));
				$table->construct_cell(htmlspecialchars_uni($r['reason']), array('style' => $styles));
				
				$html_data = " <input type=\"submit\" class=\"submit_button\" value=\"{$lang->myshoutbox_view}\" onclick=\"alert('".myshoutbox_jsspecialchars(htmlspecialchars_uni($r['shout_msg']))."')\" />";
				
				$table->construct_cell(my_date($mybb->settings['dateformat'], $r['date'], '', false).", ".my_date($mybb->settings['timeformat'], $r['date']), array('width' => '15%', 'style' => $styles));
				
				$table->construct_cell($html_data, array('width' => '10%', 'style' => $styles));
				
				$form = new Form("index.php?module=tools/myshoutbox&amp;action=delete_report", "post", 'myshoutbox" onsubmit="return confirm(\''.myshoutbox_jsspecialchars($lang->myshoutbox_delete_report_confirm).'\');', 0, "", true);
				$html_data = $form->construct_return;
				$html_data .= $form->generate_hidden_field("rid", $r['rid']);
				$html_data .= "<input type=\"submit\" class=\"submit_button\" value=\"{$lang->myshoutbox_delete}\" />";
				$html_data .= $form->end();
				
				$table->construct_cell($html_data, array('width' => '10%', 'style' => $styles));
				
				$form = new Form("index.php?module=tools/myshoutbox&amp;action=mark_report", "post", 'myshoutbox" onsubmit="return confirm(\''.myshoutbox_jsspecialchars($lang->myshoutbox_mark_report_confirm).'\');', 0, "", true);
				$html_data = $form->construct_return;
				$html_data .= $form->generate_hidden_field("rid", $r['rid']);
				$html_data .= "<input type=\"submit\" class=\"submit_button\" value=\"{$lang->myshoutbox_mark}\" />";
				$html_data .= $form->end();
				
				$table->construct_cell($html_data, array('width' => '10%', 'style' => $styles));
				
				$table->construct_row();
				$found = true;
			}
			
			if (!$found)
			{
				$table->construct_cell($lang->myshoutbox_no_shouts_reported, array('colspan' => 8));
				$table->construct_row();
			}
			
			$table->output($lang->myshoutbox_reported_shouts);
			
			$page->output_footer();
		
			exit;
		}
		
		// no action
		$page->add_breadcrumb_item($lang->myshoutbox_home, 'index.php?module=tools/myshoutbox');
		
		$page->output_header($lang->myshoutbox_home);
		
		$sub_tabs['myshoutbox'] = array(
			'title'			=> $lang->myshoutbox_home,
			'link'			=> 'index.php?module=tools/myshoutbox',
			'description'	=> $lang->myshoutbox_description
		);
		
		$sub_tabs['myshoutbox_reports'] = array(
			'title'			=> $lang->myshoutbox_reported_shouts." (".intval($db->fetch_field($db->simple_select("mysb_reports", "COUNT(rid) as reports", "marked='0'"), "reports")).")",
			'link'			=> 'index.php?module=tools/myshoutbox&amp;action=reports',
			'description'	=> $lang->myshoutbox_myshoutbox_reported_shouts_description
		);
		
		$page->output_nav_tabs($sub_tabs, 'myshoutbox');
		
		$tabs = array(
			'banuser' => $lang->myshoutbox_quick_ban,
			'unbanuser' => $lang->myshoutbox_quick_unban
		);
		
		$page->output_tab_control($tabs);
		
		// quick ban user form
		echo "<div id=\"tab_banuser\">\n";
		$form = new Form("index.php?module=tools/myshoutbox&amp;action=ban", "post", "myshoutbox");
		
		$form_container = new FormContainer($lang->myshoutbox_ban_user);
		$form_container->output_row($lang->myshoutbox_ban_username, htmlspecialchars_uni($lang->myshoutbox_ban_username_desc), $form->generate_text_box('username', htmlspecialchars_uni($mybb->input['username']), array('id' => 'username')), 'username');
		$form_container->output_row($lang->myshoutbox_ban_reason, htmlspecialchars_uni($lang->myshoutbox_ban_reason_desc), $form->generate_text_box('reason', htmlspecialchars_uni($mybb->input['reason']), array('id' => 'reason')), 'reason');
		
		$form_container->end();
		
		$buttons = "";
		$buttons[] = $form->generate_submit_button($lang->myshoutbox_submit);
		$buttons[] = $form->generate_reset_button($lang->myshoutbox_reset);
		$form->output_submit_wrapper($buttons);
		$form->end();
		
		echo "</div>\n";
		
		// quick unban user form
		echo "<div id=\"tab_unbanuser\">\n";
		$form = new Form("index.php?module=tools/myshoutbox&amp;action=unban", "post", "myshoutbox");
		
		$form_container = new FormContainer($lang->myshoutbox_unban_user);
		$form_container->output_row($lang->myshoutbox_unban_username, htmlspecialchars_uni($lang->myshoutbox_unban_username_desc), $form->generate_text_box('username', htmlspecialchars_uni($mybb->input['username']), array('id' => 'username')), 'username');
		
		$form_container->end();
	
		$buttons = "";
		$buttons[] = $form->generate_submit_button($lang->myshoutbox_submit);
		$buttons[] = $form->generate_reset_button($lang->myshoutbox_reset);
		$form->output_submit_wrapper($buttons);
		$form->end();
		
		echo "</div>\n";
		
		// pagination
		$per_page = 15;
		if($mybb->input['page'] && intval($mybb->input['page']) > 1)
		{
			$mybb->input['page'] = intval($mybb->input['page']);
			$start = ($mybb->input['page']*$per_page)-$per_page;
		}
		else
		{
			$mybb->input['page'] = 1;
			$start = 0;
		}
		
		$query = $db->simple_select("users", "COUNT(uid) as users", 'mysb_banned=1');
		$total_rows = $db->fetch_field($query, "users");
	
		echo "<br />".draw_admin_pagination($mybb->input['page'], $per_page, $total_rows, "index.php?module=tools/myshoutbox&amp;page={page}");
		
		// table
		$table = new Table;
		$table->construct_header("<div style=\"width: 30%;\">".$lang->myshoutbox_username."</div>");
		$table->construct_header("<div style=\"width: 70%;\">".$lang->myshoutbox_reason."</div>");
		
		$query = $db->simple_select('users', 'uid,username,mysb_banned_reason', 'mysb_banned=1', array('order_by' => 'uid', 'order_dir' => 'ASC', 'limit' => "{$start}, {$per_page}"));
		while($r = $db->fetch_array($query)) {
			$table->construct_cell(htmlspecialchars_uni($r['username']));
			$table->construct_cell(htmlspecialchars_uni($r['mysb_banned_reason']));
			$table->construct_row();
			$found = true;
		}
		
		if (!$found)
		{
			$table->construct_cell($lang->myshoutbox_no_users_banned, array('colspan' => 2));
			$table->construct_row();
		}
		
		$table->output($lang->myshoutbox_banned_users);
		
		$page->output_footer();
		
		exit;
	}
}

/**
 * Somewhat like htmlspecialchars_uni but for JavaScript strings
 * 
 * @param string: The string to be parsed
 * @return string: Javascript compatible string
 */
function myshoutbox_jsspecialchars($str)
{
	// Converts & -> &amp; allowing Unicode
	// Parses out HTML comments as the XHTML validator doesn't seem to like them
	$string = preg_replace(array("#\<\!--.*?--\>#", "#&(?!\#[0-9]+;)#"), array('','&amp;'), $str);
	return strtr($string, array("\n" => '\n', "\r" => '\r', '\\' => '\\\\', '"' => '\x22', "'" => '\x27', '<' => '&lt;', '>' => '&gt;'));
}

?>
