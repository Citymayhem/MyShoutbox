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

	$url = trim($url);
	$urlValidationResult = myshoutbox_validate_image_url($url);
	if($urlValidationResult !== true){
		BadRequestResponse($urlValidationResult);
	}
	
	$imageSize = myshoutbox_get_remote_image_size($url);
	$width = $imageSize[0];
	$height = $imageSize[1];
	
	$shoutContent = new StdClass();
	$shoutContent->url = $url;
	$shoutContent->width = $width;
	$shoutContent->height = $height;
	
	$shout_data = array(
		'uid' => intval($mybb->user['uid']),
		'shout_msg' => json_encode($shoutContent),
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

function myshoutbox_get_remote_image_size($url){
  $tempFileName = tempnam(sys_get_temp_dir(), "");

  $ch = curl_init($url);
  $fp = fopen($tempFileName, 'wb');
  curl_setopt($ch, CURLOPT_FILE, $fp);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_exec($ch);
  curl_close($ch);
  fclose($fp);

  $imageSize = getimagesize($tempFileName);
  $response = array($imageSize[0], $imageSize[1]);

  unlink($tempFileName);

  return $response;
}

function myshoutbox_validate_image_url($url)
{
	if(empty($url))
	{
		return AddImageShoutError::InvalidImageUrl;
	}

	$headResponse = PerformHeadRequest($url);

	if($headResponse->StatusCode != 200)
	{
		return AddImageShoutError::CouldNotRetrieveImage;
	}

	$contentType = $headResponse->ContentType;
	if($contentType != "image/jpeg" && $contentType != "image/gif" && $contentType != "image/png")
	{
		return AddImageShoutError::InvalidFileType;
	}

	if($headResponse->ContentLength > 10485760)
	{
		return AddImageShoutError::ImageFileSizeTooBig;
	}

	return true;
}