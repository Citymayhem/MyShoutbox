# MyShoutbox
Modifications made to the MyShoutbox plugin

* This plugin was coded by Pirata Nervo but based off Asad Khan's Spicefuse Shoutbox.
* http://mods.mybb.com/view/myshoutbox
* http://www.spicefuse.com

##Additions to MyShoutbox##
* Toggle order of shouts displayed (newest at bottom/newest at top)
* Uses a textarea rather than a text input to enable spellcheckers and auto-capitalisation of the first letter in sentences on phones.
* Shiny font-awesome icons for deleting/hiding/reporting
* Style improvements


##Full Unicode Support (Emojis, etc.)##

Edit inc/config.php

Change<br>
`$config['database']['encoding'] = 'utf8';`<br>
To<br>
`$config['database']['encoding'] = 'utf8mb4';`<br>

###Existing Installation###

Run the following SQL (replace <DATABASENAMEHERE> with your database name):
```sql
USE <DATABASENAMEHERE>;
ALTER TABLE mybb_mysb_shouts CHARACTER SET = utf8mb4 , COLLATE = utf8mb4_general_ci;
ALTER TABLE mybb_mysb_shouts CHANGE COLUMN shout_msg shout_msg TEXT CHARACTER SET 'utf8mb4' NOT NULL;
```