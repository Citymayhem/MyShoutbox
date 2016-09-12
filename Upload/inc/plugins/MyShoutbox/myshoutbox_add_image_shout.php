<?php
if(!defined('IN_MYBB')) { BadRequestResponse("Not in MyBB"); }

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
	$userIsGuest = $mybb->user['usergroup'] == 1 || $mybb->user['uid'] < 1 || !$perms;
	$userIsBanned = $perms === 2;

	if ($userIsGuest || $userIsBanned)
	{
		UnauthorisedResponse();
	}
	
	if(MyShoutboxFloodProtection::IsUserAllowedToPost($mybb->user) === false)
	{
		BadRequestResponse(AddImageShoutError::FloodProtection);
	}
	
	if($url == null || empty(trim($url)))
	{
		BadRequestResponse(AddImageShoutError::InvalidImageUrl);
	}
	
	$url = trim($url);
	
	$headResponse = PerformHeadRequest($url);
	
	if($headResponse->StatusCode != 200)
	{
		BadRequestResponse(AddImageShoutError::CouldNotRetrieveImage);
	}
	
	$contentType = $headResponse->ContentType;
	if($contentType != "image/jpeg" && $contentType != "image/gif" && $contentType != "image/png")
	{
		BadRequestResponse(AddImageShoutError::InvalidFileType);
	}
	
	if($headResponse->ContentLength > 1048576)
	{
		BadRequestResponse(AddImageShoutError::ImageFileSizeTooBig);
	}
	
	$shout_data = array(
		'uid' => intval($mybb->user['uid']),
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