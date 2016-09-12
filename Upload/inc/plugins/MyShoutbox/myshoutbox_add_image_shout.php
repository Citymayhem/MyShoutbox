<?php
if(!defined('IN_MYBB')) { BadRequestResponse("Not in MyBB"); }

function myshoutbox_add_image_shout($url){
	$response = new StdClass();
	$response->url = $url;
	OkResponseWithObject($response);
}