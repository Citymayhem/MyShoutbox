<?php
if(!defined('IN_MYBB')) { BadRequestResponse("Not in MyBB"); }

require_once MYBB_ROOT . "inc/plugins/MyShoutbox/responses.php";


class GetTemplatesResponse {
	public $mysb_shout;
	public $mysb_shout_message_text;
}

function myshoutbox_get_templates(){
	
	global $templates;
	
	$response = new GetTemplatesResponse();
	
	$response->mysb_shout = stripcslashes($templates->get("mysb_shout"));
	$response->mysb_shout_message_text = stripcslashes($templates->get("mysb_shout_message_text"));
	
	OkResponseWithObject($response);
}
