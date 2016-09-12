<?php
require_once MYBB_ROOT . "inc/plugins/MyShoutbox/responses.php";

if(!defined('IN_MYBB')) { BadRequestResponse("Not in MyBB"); }

require_once MYBB_ROOT . "inc/plugins/MyShoutbox/myshoutbox_get_shouts.php";
require_once MYBB_ROOT . "inc/plugins/MyShoutbox/myshoutbox_get_templates.php";
require_once MYBB_ROOT . "inc/plugins/MyShoutbox/myshoutbox_add_image_shout.php";

require_once MYBB_ROOT . "inc/plugins/MyShoutbox/DatabaseMigrator.php";

require_once MYBB_ROOT . "inc/plugins/MyShoutbox/FloodProtection.php";
