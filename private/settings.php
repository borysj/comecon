<?php
// Server path for your home directory
$homeDir = "/home/johndoe";
// Server subpath for your comments directory (preferably non-public)
$commentsDir = $homeDir . "/data/comments";
// Server subpath for your subscribers directory (preferably non-public)
$subscribersDir = $homeDir . "/data/subscribers";
// Name of file with people subscribing to new blog posts through mail
$subscribersFile = "subscribers.txt";

# email_sending
// Server subpath for PHPMailer
$phpMailerDir = $homeDir . "/public_html/assets/modules/PHPMailer-master";
// Email server parameters for sending notifications
$mailNotificationsHost = "smtp.mymail.com";
$mailNotificationsUsername = "notifications@mymail.com";
$mailNotificationsPassword = "mypassword_unhashed";
// Notifications reach you through this email:
$ownerPrivateMail = "johndoe@gmail.com";
// ...but users are encouraged to contact you through this one:
$blogContactMail = "john@myblog.com";
// When subscribing to the blog posts by email, the user has to add this captcha
// at the end of his email address.
$captchaEmail = "847";

# email_notification
// Use this command to calculate hash:
// php -r "echo(hash('xxh3', 'unhashed_password'));"
$notificationPassword = "hashed_password_for_sending_notifications";

# email_storage
// Use any random sequence of characters.
// These salts will be used for email if the owner does not have gravatar.
// Then the true email is irrelevant, and another layer of obfuscation
// (on top of SHA-256) will be added.
$emailSaltA = "45klk231";
$emailSaltB = "xa56kj12";

# save_comment
// If you want to change the time zone and the timestamp format,
// you can look up PHP functions date_default_timezone_set() and
// date() to learn about possible choices.
$timezone = "Europe/Oslo";
$timestamp = "Y-m-d H:i:s";
// There will be a master file with ALL comments in the blog's main folder.
// Here you can choose the name for the file:
$allCommentsFile = "all_comments.txt";
// Maximal length of a comment, in characters.
// Remember to set `maxlength` in `form-submit_comment.php` at slightly smaller
// value for a safety margin for tags.
$maxCommentLength = 4000;

# search
// Server subpath for where you keep searchable blog posts.
// "Searchable" means "in plain text without any tags".
// See the explanation in README.
$searchDataDirectory = $homeDir . "/public_html/assets/db_posts";
// Server subpath for search queries (preferably non-public).
// This is for the curios; you will see what keywords people are looking for on
// your blog. 
$searchQueriesRecord = $homeDir . "/data/search_queries.txt";

# edit_comment
// Use any short random string of characters.
$commentSalt = "rk365-A";
// This is your admin backdoor for editing comments.
// Use this command to calculate hash:
// php -r "echo(hash('xxh3', 'unhashed_password'));"
$adminCommentPassword = "hashed_password_for_editing_any_comment";
// User has 25 minutes to edit their password.
// 25 minutes is the real deadline; but the user will be informed that they have
// only 20 to give them some extra margin.
$commentEditTimeout = 25*60;
