# MyShoutbox
Modifications made to the MyShoutbox plugin

* This plugin was coded by Pirata Nervo but based off Asad Khan's Spicefuse Shoutbox.
* http://mods.mybb.com/view/myshoutbox
* http://www.spicefuse.com

## Additions to MyShoutbox
* Toggle order of shouts displayed (newest at bottom/newest at top)
* Uses a textarea rather than a text input to enable spellcheckers and auto-capitalisation of the first letter in sentences on phones.
* Restyled to look like a modern messaging system
* Supports images properly (max height & width)
* Added option to embed YouTube videos via url (users don't need to post BBCode)


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

