INSERT INTO `{MYBB_TABLE_PREFIX}settings`(`name`, `title`, `description`, `value`, `optionscode`, `disporder`, `gid`)
VALUES ("mysb_image_max_width", "Max image width", "Enter the maximum allowed width of images in pixels. E.g. 500", "500", "text", "21", (SELECT gid FROM mybb_settinggroups WHERE name = 'mysb_shoutbox' ORDER BY gid DESC LIMIT 1));

INSERT INTO `{MYBB_TABLE_PREFIX}settings`(`name`, `title`, `description`, `value`, `optionscode`, `disporder`, `gid`)
VALUES ("mysb_image_max_height", "Max image height", "Enter the maximum allowed height of images in pixels. E.g. 100", "100", "text", "22", (SELECT gid FROM mybb_settinggroups WHERE name = 'mysb_shoutbox' ORDER BY gid DESC LIMIT 1));
