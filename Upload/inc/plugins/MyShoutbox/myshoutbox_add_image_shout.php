<?php
if(!defined('IN_MYBB')) { BadRequestResponse("Not in MyBB"); }


function myshoutbox_IsUserAllowedByFloodProtectionToPost($userId){
	global $db, $mybb;
	
	if (intval($mybb->settings['mysb_flood_time']) && mysb_obey_cooldown()) {
		$lastShout = $db->fetch_field($db->simple_select('mysb_shouts', 'MAX(shout_date) as lastShout', "uid = $userId", 'lastShout'));
		$interval = time() - $lastShout;
		
		if ($interval <= $mybb->settings['mysb_flood_time'])
			return false;
	}
	
	return true;
}

class HeadRequestResponse {
	public $StatusCode;
	public $ContentLength;
	public $ContentType;
}

function PerformHeadRequest($url){
	$headRequest = curl_init();

	curl_setopt($headRequest, CURLOPT_URL, $url);
	curl_setopt($headRequest, CURLOPT_NOBODY, true);
	curl_setopt($headRequest, CURLOPT_CONNECTTIMEOUT, 10);

	curl_exec($headRequest);
	
	$response = new HeadRequestResponse();

	$response->StatusCode = curl_getinfo($headRequest, CURLINFO_HTTP_CODE);
	$response->ContentLength =  curl_getinfo($headRequest, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
	$response->ContentType =  curl_getinfo($headRequest, CURLINFO_CONTENT_TYPE);

	curl_close($headRequest);
	
	return $response;
}

abstract class AddImageShoutError {
	const FloodProtection = "add_image_shout_error_flood_protection";
	const InvalidImageUrl = "add_image_shout_error_invalid_image_url";
	const CouldNotRetrieveImage = "add_image_shout_error_could_not_retrieve_image";
	const InvalidFileType = "add_image_shout_error_invalid_file_type";
	const ImageFileSizeTooBig = "add_image_shout_error_image_file_size_too_big";
}

function myshoutbox_add_image_shout($url)
{	
	global $db, $mybb;
	
	$perms = myshoutbox_can_view();

	// guests not allowed! neither banned users
	if (!$perms || $perms === 2 || $mybb->user['usergroup'] == 1 || $mybb->user['uid'] < 1)
	{
		UnauthorisedResponse();
	}
	
	$userId = intval($mybb->user['uid']);
	
	if(!myshoutbox_IsUserAllowedByFloodProtectionToPost($userId)){
		BadRequestResponse(AddImageShoutError::FloodProtection);
	}
	
	if($url == null || empty(trim($url)))
	{
		BadRequestResponse(AddImageShoutError::InvalidImageUrl);
	}
	
	$headResponse = PerformHeadRequest($url);
	
	if($headResponse->StatusCode != 200){
		BadRequestResponse(AddImageShoutError::CouldNotRetrieveImage);
	}
	
	$contentType = $headResponse->ContentType;
	if($contentType != "image/jpeg" && $contentType != "image/gif" && $contentType != "image/png"){
		BadRequestResponse(AddImageShoutError::InvalidFileType);
	}
	
	if($headResponse->ContentLength > 1048576){
		// TODO: Replace with a thumbnail of the image if it's a reasonable size
		BadRequestResponse(AddImageShoutError::ImageFileSizeTooBig);
	}
	
	$shout_data = array(
		'uid' => $userId,
		'shout_msg' => $url,
		'shout_date' => time(),
		'shout_ip' => get_ip(),
		'hidden' => "no",
		'type' => ShoutboxShoutType::Image
	);
		
	if (!$db->insert_query('mysb_shouts', $shout_data)) {
		error_log("Error adding shoutbox image shout. " . $db->error);
		InternalServerErrorResponse();
	}
	
	OkResponse();
}