<?php
if(!defined('IN_MYBB')) { BadRequestResponse("Not in MyBB"); }

class GetTemplatesResponse {
	public $mysb_shout;
	public $mysb_shout_message_text;
	public $mysb_shout_button_pm;
	public $mysb_shout_message_video;
}

function myshoutbox_get_templates(){
	
	global $templates;
	
	$response = new GetTemplatesResponse();
	
	$response->mysb_shout = stripcslashes($templates->get("mysb_shout"));
	
	$response->mysb_shout_button_pm = stripcslashes($templates->get("mysb_shout_button_pm"));
	
	$response->mysb_shout_message_text = stripcslashes($templates->get("mysb_shout_message_text"));
	$response->mysb_shout_message_image = stripcslashes($templates->get("mysb_shout_message_image"));
	$response->mysb_shout_message_video = stripcslashes($templates->get("mysb_shout_message_video"));
	
	OkResponseWithObject($response);
}
