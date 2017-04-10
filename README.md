# MyShoutbox
Modifications made to the MyShoutbox plugin

* This plugin was coded by Pirata Nervo but based off Asad Khan's Spicefuse Shoutbox.
* http://mods.mybb.com/view/myshoutbox
* http://www.spicefuse.com

## Changes to MyShoutbox
#### Feature Changes
* Allows users to toggle order of shouts displayed (newest at bottom/newest at top)
* Uses a textarea rather than a text input to enable spellcheckers and auto-capitalisation of the first letter in sentences on phones.
* Restyled to look like a modern messaging system
* Supports images properly (max height & width)
* Added option to embed YouTube videos via url (users don't need to post BBCode)
* Popup shoutbox is gone

#### Development Changes
* Moves templates into separate files
* Newer code responds using a consistent JSON response format and proper HTTP status codes
* Adds an automatic database migration system
* Extracted a lot of PHP code into separate files in the upload/inc/plugins/MyShoutbox directory
* Adds support for different types of shout messages (e.g. images, videos) with their own render template


## When updating from the previous version of MyShoutbox
* Make sure you are on the latest version of the old MyShoutbox
* Follow the typical update instructions

## Typical  Update Instructions
1. Deactivate MyShoutbox
2. Upload the latest version
3. Activate MyShoutbox

## To Enable Full Unicode Support (Emojis, etc.)

Edit the file inc/config.php in your MyBB installation.

Change<br>
`$config['database']['encoding'] = 'utf8';`<br>
To<br>
`$config['database']['encoding'] = 'utf8mb4';`<br>

