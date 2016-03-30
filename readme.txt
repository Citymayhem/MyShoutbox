This plugin was coded by Pirata Nervo but based off Asad Khan's Spicefuse Shoutbox.

I have released this plugin with his permission and I may NOT break any of the following conditions:
1. Have a link back to the spicefuse.com on the forum post or any other page that distributes your code, including any readme files. In other words, have a linked version of the Spicefuse Shoutbox text. 
2. Retain my author credits for the old code. 
3. Keep it free. :) 

Feel free to visit the official Spicefuse Shoutbox website:
http://www.spicefuse.com

Feel free to visit the official MyShoutbox website:
http://www.mybb-plugins.com

************************ License ****************************

You may NOT redistribute this plugin as it is based off Asad Khan's code so you must respect the following license:
* License: 
* This plugin is offered "as is" with no guarantees.
* You may redistribute it provided the code and credits 
* remain intact with no changes. This is not distributed
* under GPL, so you may NOT re-use the code in any other
* module, plugin, or program. 
* 
* Free for non-commercial purposes!

Credits:
Asad Khan - Spicefuse Shoutbox coder


************************ Upgrading ****************************

1.6 -> 1.7
Upload upgrade17.php to the root of your MyBB forum. Run it from your browser and delete the file after that.
Re-upload files overwriting the existing ones.
Go to ACP -> Configuration -> Settings -> MyShoutbox -> Enter a random string in the "Key" setting.
In the templates (by default: 'index'), replace {myshoutbox} with {myshoutbox_KEY} where KEY is the random string you entered before.

1.5 -> 1.6
Re-upload files.

<=1.4 -> 1.6
Uninstall the current version.
Re-upload files.
Install the new version.

************************ Installation ****************************

Upload the contents of the Upload folder to your forums's root and activate the plugin MyShoutbox from your Admin Panel.

Go to ACP -> Configuration -> Settings -> MyShoutbox -> Enter a random string in the "Key" setting. 
In the templates (by default: 'index'), replace {myshoutbox} with {myshoutbox_KEY} where KEY is the random string you entered before.

************************ Change log ****************************
1.7
- Fixed the possibility of including {myshoutbox} in posts or anywhere else and get it replaced with the Shoutbox itself.

1.6
- Fixed authorization security hole (allowed everyone to get shouts even when not authorized).

1.5
- Improved performance a lot.
- Re-coded the way the private shouting system works.
- Fixed some javascript related issues.
- Fixed plenty of minor bugs.
- The code is looking pretty different from the initial Spicefuse Shoutbox code.

1.4
- Improved performance.
- Fixed about 5 minor bugs.
- Improved group permissions.
- Removed useless features.
- Should work fine in IE.
- Added compatibility with MyBB 1.6

1.3
- Fixed a compatiblity problem with MyBB 1.4.8
- Fixed a few minor problems.

1.2
- Added a setting to set the font size
- Fixed a bug that allowed guests to report shouts
- Fixed some bugs regarding javascript
- The shoutbox won't stop working when you have no shouts anymore.
- Increased compatibility with Internet Explorer.
- Fixed a problem that would make smilies in new thread/post and private messages non-clickable when the shoutbox was in global mode
- You can now place the shoutbox wherevet you want (again), use '<mysb_shoutbox>' to do so. (without quotes)
- Added a new accepted value ('global_footer') for the place where the shoutbox is displayed and renamed an existing one. ('global' is now 'global_header')


1.1
- Fixed a few bugs related to permissions:
    # Users with access to Admin CP can view the IP of the user who shoutted by placing the mouse over the username(For some reason I had removed this feature in the previous release).
    # Super mods can now delete shouts (there was a bug :P)
- Added a new setting which allows admins to let or not let users with access to the Mod CP to delete shouts.
- Fixed 2 bugs in Send Message to user feature.
- Added an admin page which allows admins to ban and unban users from viewing the shoutbox. It also allows them to view who's banned and manage reported shouts.
- MyShoutbox doesn't require template edits anymore! Added a setting which allows you to choose where you want the shoutbox to be placed. (header (global), index header, index footer)
- Fixed a few problems with the bot.
- Added a setting which allows you to choose if you want to display a message to those who are banned. (instead of the shoutbox)
- Bot commands can only be used by moderators and admins now.
- Added a report shout feature.
- Added a setting to disable the bot. (The bot won't reply to any messages, plus the bot column and the talk to bot link are not displayed)

1.0
- Shoutbox Bot (Not very intelligent :P. Has mood and its answers depend on its mood BUT as I said it's not very intelligent, it's a really simple bot)
- Private Message command - /pvt (/pvt USER_ID_HERE Message)
- Deleted shouts are not deleted but hidden from normal users except Super Mods (if they have permission to delete(hide)) and administrators. The hidden shouts can be removed or recovered.
- Colors inserted
- MyCode inserter
- Popup Shoutbox (Full and Normal)
- Portable version (doesn't use javascript so many features are not available)
- "Tell a user that you need to talk with him" feature (A message will appear below the uid text box telling the end user that you want to talk to him)
- Smilie inserter
- Allows HTML if the setting is set to Yes
- Admins can set which usergroups can view the shoutbox as well as choose to if additional groups are checked.

To DO!
* Edit shout