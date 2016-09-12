<?php
if(!defined('IN_MYBB')) { BadRequestResponse("Not in MyBB"); }

require_once MYBB_ROOT . "inc/plugins/MyShoutbox/models/ShoutboxShout.php";
require_once MYBB_ROOT . "inc/plugins/MyShoutbox/models/ShoutboxShoutType.php";

function myshoutbox_parse_message($parser, $message, $me_username){
	global $mybb;
	
	$parser_options = array(
		'allow_mycode' => $mybb->settings['mysb_allow_mycode'],
		'allow_smilies' => $mybb->settings['mysb_allow_smilies'],
		'allow_imgcode' => $mybb->settings['mysb_allow_imgcode'],
		'allow_html' => $mybb->settings['mysb_allow_html'],
		"allow_videocode" => $mybb->settings['mysb_allow_video'],
		'me_username' => $me_username
	);		
	
	if($parser_options['allow_mycode']){
		return $parser->parse_message(strip_mycode($message), $parser_options);
	}
	
	return $parser->parse_message($message, $parser_options);
}

class ShoutboxGetShoutsResponse {
	public $currentUserId;
	public $lastShoutId = 0;
	public $canSeeIps = false;
	public $canDelete = false;
	public $messages = array();
}

function myshoutbox_get_shouts($last_id = 0)
{
	global $db, $mybb, $parser;
	
	$allowedAccess = myshoutbox_can_view();
	if ($allowedAccess !== true){
		UnauthorisedResponse();
	}
	
	if(!isInteger($last_id)){
		BadRequestResponse("Invalid last shout message Id");
	}
	
	require_once MYBB_ROOT.'inc/class_parser.php';
	$parser = new postParser;

	$last_id = (int) $last_id;
	
	$query = $db->write_query("SELECT s.*, u.username, u.usergroup, u.displaygroup, u.avatar FROM ".TABLE_PREFIX."mysb_shouts s 
							LEFT JOIN ".TABLE_PREFIX."users u ON (u.uid = s.uid) 
						WHERE s.id > {$last_id} AND (s.uid = " . $mybb->user['uid'] . " OR s.shout_msg NOT LIKE '/pvt%' OR s.shout_msg LIKE '/pvt " . $mybb->user['uid'] . " %') AND s.hidden = 'no' 
						ORDER by s.id DESC LIMIT {$mybb->settings['mysb_shouts_main']}");
						// TODO: This limit logic is flawed. 
						// It only works for first load.
							// For consecutive get shouts, there is a chance this will ignore messages
							// We still need protection against DOS
						// This logic also doesn't work for hidden shouts


	// fetch results
	$response = new ShoutboxGetShoutsResponse();
	$response->canSeeIps = $mybb->usergroup['cancp'] === "1";
	$response->canDelete = myshoutbox_can_delete();
	$response->messages = array();
	
	$response->currentUserId = $mybb->user['uid'];
	$maxId = $last_id;
	$usernames_cache = array();
	while ($row = $db->fetch_array($query))
	{
		$shout = new ShoutboxShout();
		
		$shoutId = $row['id'];
		$shoutUserId = $row['uid'];
		$shoutUsername = $row['username'];
		
		$message = myshoutbox_parse_message($parser, $row['shout_msg'], $shoutUsername);
		
		$shout->isHidden = $row['hidden'] == "yes";
		
		if($shout->isHidden && !$response->canDelete){
			continue;
		}
		
		$isPm = stripos($message, "/pvt") === 0;
		
		if($isPm)
		{
			sscanf($message, "/pvt %d", $pmTargetUserId);
			$pmTargetUserId = intval((int)$pmTargetUserId);
			if ($response->currentUserId != $pmTargetUserId && $response->currentUserId != $shoutUserId)
			{
				continue;
			}
			
			if ($response->currentUserId == $pmTargetUserId)
			{
				$pmTargetUsername = $mybb->user['username'];
			}
			else {
				// Unfortunately, we do not have this username...let's check our cache, if it's not in cache, query it
				if (!empty($usernames_cache[$pmTargetUserId]))
				{
					$pmTargetUsername = $usernames_cache[$pmTargetUserId];
				}
				else {
					$pmTargetUsername = $db->fetch_field($db->simple_select('users', 'username', 'uid=\''.$pmTargetUserId.'\''), 'username');
					$usernames_cache[$pmTargetUserId] = $pmTargetUsername;
				}
			}
			
			$message = str_replace("/pvt ".$pmTargetUserId." ", "", $message);
			$shout->pmTargetUserId = $pmTargetUserId;
			$shout->pmTargetUsername = $pmTargetUsername;
		}
		
		array_push($response->messages, $shout);
		$shout->id = $shoutId;
		$shout->type = ShoutboxShoutType::Text;
		$shout->message = $message;
		$shout->userId = $shoutUserId;
		
		$shout->avatarUrl = $row['avatar'];
		
		if(empty($shout->avatarUrl)){
			$shout->avatarUrl = "./images/default_avatar.png";
		}
		
		$shout->isPm = $isPm;
		
		if($shoutId > $maxId){
			$maxId = $shoutId;
		}
		
		// Format their username & make it link to their profile
		$formattedUsername = format_name($shoutUsername, $row['usergroup'], $row['displaygroup']);
		$shout->formattedUsername = build_profile_link($formattedUsername, $shoutUserId);
		
		$shout->dateTime = $row['shout_date'];
		
		if($response->canSeeIps){
			$shout->userIp = $row[shout_ip];
		}
	}
	
	$response->lastShoutId = $maxId;
	
	OkResponseWithObject($response);
}