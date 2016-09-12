<?php
if(!defined('IN_MYBB')) { BadRequestResponse("Not in MyBB"); }

class MyShoutboxFloodProtection {

	static function IsUserAllowedToPost($user){
		global $db, $mybb;

		if (intval($mybb->settings['mysb_flood_time']) && MyShoutboxFloodProtection::IsFloodProtectionEnabled($user))
		{
			$userId = intval($user['uid']);

			$lastShout = $db->fetch_field($db->simple_select('mysb_shouts', 'MAX(shout_date) as lastShout', "uid = $userId"), 'lastShout');
			$interval = time() - $lastShout;

			if ($interval <= $mybb->settings['mysb_flood_time'])
				return false;
		}

		return true;
	}
	
	static function IsFloodProtectionEnabled($user)
	{
		global $mybb;

		$floodProtectionGroupsList = $mybb->settings['mysb_cooldown_groups'];

		if (empty($floodProtectionGroupsList)) return false;

		$floodProtectionGroups = explode(",", $floodProtectionGroupsList);

		if (in_array($user['usergroup'], $floodProtectionGroups)) return true;
		
		$shouldCheckAdditionalGroups = $mybb->settings['mysb_additional_groups'] === 1;
		if($shouldCheckAdditionalGroups === false || empty($user['additionalgroups']))return false;

		$additionalGroups = explode(",", $user['additionalgroups']);

		$anyAdditionalGroupsInFloodProtectionList = count(array_intersect($additionalGroups, $floodProtectionGroups)) > 0;
		
		return $anyAdditionalGroupsInFloodProtectionList;
	}
}