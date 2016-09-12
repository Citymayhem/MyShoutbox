<?php
if(!defined('IN_MYBB')) { BadRequestResponse("Not in MyBB"); }

class GetTemplatesResponse {
	public $mysb_shout;
	public $mysb_shout_message_text;
	public $mysb_shout_button_pm;
}

function myshoutbox_get_templates(){
	
	global $templates;
	
	$response = new GetTemplatesResponse();
	
	$response->mysb_shout = stripcslashes($templates->get("mysb_shout"));
	$response->mysb_shout_message_text = stripcslashes($templates->get("mysb_shout_message_text"));
	$response->mysb_shout_button_pm = stripcslashes($templates->get("mysb_shout_button_pm"));
	
	OkResponseWithObject($response);
}
