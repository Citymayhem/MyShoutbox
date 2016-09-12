<?php
if(!defined('IN_MYBB')) { BadRequestResponse("Not in MyBB"); }

abstract class AddVideoShoutError {
	const FloodProtection = "add_video_shout_error_flood_protection";
	const InvalidVideoUrl = "add_video_shout_error_invalid_video_url";
}

function GetYoutubeVideoId($url)
{
	$pattern = '/^(https?:\/\/)?(www\.)?(youtube.[A-Za-z.]+\/.*[?&]v=|youtu.be\/)([A-Za-z0-9\-_]+)/';
	preg_match($pattern, $url, $matches, PREG_OFFSET_CAPTURE);
	
	if(count($matches) !== 5){
		return null;
	}
	
	return $matches[4][0];
}

function myshoutbox_add_video_shout($url)
{
	global $db, $mybb;
	
	$perms = myshoutbox_can_view();
	$userIsGuest = $mybb->user['usergroup'] == 1 || $mybb->user['uid'] < 1 || !$perms;
	$userIsBanned = $perms === 2;

	if ($userIsGuest || $userIsBanned)
	{
		UnauthorisedResponse();
	}
	
	if(MyShoutboxFloodProtection::IsUserAllowedToPost($mybb->user) === false)
	{
		BadRequestResponse(AddVideoShoutError::FloodProtection);
	}
	
	if($url == null || empty(trim($url)))
	{
		BadRequestResponse(AddVideoShoutError::InvalidVideoUrl);
	}
	
	$url = trim($url);
	
	$videoId = GetYoutubeVideoId($url);
	
	if($videoId === null || empty($videoId))
	{
		BadRequestResponse(AddVideoShoutError::InvalidVideoUrl);
	}

	$fullUrl = "https://www.youtube.com/embed/" . $videoId;
	
	$shout_data = array(
		'uid' => intval($mybb->user['uid']),
		'shout_msg' => $fullUrl,
		'shout_date' => time(),
		'shout_ip' => get_ip(),
		'hidden' => "no",
		'type' => ShoutboxShoutType::Video
	);
		
	if (!$db->insert_query('mysb_shouts', $shout_data)) {
		error_log("Error adding shoutbox video shout. " . $db->error);
		InternalServerErrorResponse();
	}
	
	OkResponse();
}