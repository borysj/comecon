<?php

// The settings marked with ESSENTIAL must be set, otherwise Comecon won't work.
// The settings marked with IMPORTANT should be set as soon as possible.
// The other settings can be left with their default values for now. However,
// you have to edit several of them if you want the optional features. Look into
// README.md for details.

// The path for your server directory, usually one level above where your site is.
// On shared hosting it can be something like /home/johndoe
// On a VPS it is up to you, but usually you want /var/www
// Remember, no trailing slash!
$serverDir = "";  //ESSENTIAL

// Server path for your site directory. Typically /home/johndoe/public_html on a
// shared hosting, or /var/www/html on a VPS
// Remember, no trailing slash!
$siteDir = "";  //ESSENTIAL

$settings = [
    "general" => [
        // The name of your blog
        "blogName" => "My Great Blog", //IMPORTANT
        // The URL of your blog without the trailing slash
        "siteURL" => "https://myblog.example.com", //ESSENTIAL
        // Server subpath for your comments directory (preferably non-public)
        "commentsDir" => $serverDir . "/comecon-data/comments",
        // Server subpath for your subscribers directory (preferably non-public)
        "subscribersDir" => $serverDir . "/comecon-data/subscribers",
        // File with emails that are notified about new blog posts
        "subscribersFile" => "subscribers.txt",
        // Language of the blog, for lang attribute of html in edit_comment.php
        "language" => "en",
        // Message file in your language
        "messages" => "messages_en.php",
        // If the full titles of your posts are not entered manually in the
        // comment form (as they should be), Comecon will try to find them
        // in the first header in the post page. But which header? Set 1 for h1,
        // 2 for h2, and so on.
        "locationFullTitle" => "1",
    ],
    "save" => [
        // If you want to change the time zone and the timestamp format,
        // you can look up PHP functions date_default_timezone_set() and
        // date() to learn about possible choices. However, if you change
        // the timestamp format, something may easily get broken.
        "timezone" => "Europe/Oslo", //IMPORTANT
        "timestamp" => "Y-m-d H:i:s",
        // There can be a master file with ALL comments in the blog's main folder.
        // There might be a reason why you need it; I was using it to generate
        // some comment statistics when generating the blog locally.
        // Here you can choose whether you want to use it, and set the name for
        // the file. If you want to use it, remember to create an empty file
        // with that name!
        "allComments" => false,
        "allCommentsFile" => $siteDir . "/all_comments.txt",
        // Maximal length of a comment, in characters.
        // Remember to set `maxlength` in `form-submit_comment.html` at slightly smaller
        // value for a safety margin for tags.
        "maxCommentLength" => 4000,
        // Use any random sequence of characters for the email salts.
        // These salts will be used for email if the owner does not have gravatar.
        // Then the true email is irrelevant, and another layer of obfuscation
        // (on top of SHA-256) will be added.
        "emailSaltA" => "", //IMPORTANT
        "emailSaltB" => "", //IMPORTANT
        // Write here the answer to the captcha question for the comment form
        // The question itself is formulated in the form in
        // includes/form-submit_comment.html
        "commentCaptcha" => "correct_answer", //ESSENTIAL
    ],
    "feed" => [
        // Set to true if you want to update comment feeds.
        // There can be two feeds: for the newest comments, and specifically for
        // the given blog post. However, read the manual first!
        // These feeds must be around already. Comecon DOES not create them.
        // The idea is that a new particular feed will be created empty when the
        // blog is updated with a new blog post
        "updateFeedNewest" => false,
        "updateFeedPost" => false,
        // Server subpath for the directory with comment feeds
        "commentFeedsDir" => $siteDir . "/commfeeds",
        // Number of newest comments to be retained in the master feed
        "newestComments" = 10,
    ],
    "edit" => [
        // Use any random string of characters.
        "cookieKey" => "", //IMPORTANT
        // This is your admin backdoor for editing comments.
        // Use this command to calculate hash:
        // php -r "echo(hash('sha256', 'unhashed_password'));"
        "adminCommentPassword" => "hashed_password", //IMPORTANT
        // The commenter has 25 minutes to edit their comment.
        // 25 minutes is the real deadline, but the commenter will be informed that they have
        // only 20 to give them some extra margin while editing.
        // If you change this value, remember to change the message in includes/display_comments.php
        "commentEditTimeout" => 25 * 60,
    ],
    "email" => [
        // Set to true if you want to notify subscribers about new comments by
        // email. If it is set to false, but the ownerPrivateMail below is
        // filled out, you will nonetheless receive automatic notifications (and
        // only you)
        "notify" => false,
        // Email server parameters for sending notifications
        "mailNotificationsHost" => "smtp.myblog.com",
        "mailNotificationsUsername" => "notifications@myblog.com",
        "mailNotificationsPassword" => "myemailpassword_unhashed",
        // Comment notifications reach you automatically through this email.
        // Set the string to null or empty if you do not want to receive them
        "ownerPrivateMail" => "owner@mail.com",
        // ...but this is the official contact email for your blog
        "blogContactMail" => "contact@myblog.com",
        // When subscribing to the blog posts by email, the user has to add this captcha
        // at the end of his email address.
        "captchaEmail" => "847",
        // Use this command to calculate hash:
        // php -r "echo(hash('sha256', 'unhashed_password'));"
        "notificationPassword" => "hashed_password_for_sending_notifications",
    ],
    "search" => [
        // Server subpath (public) for where you keep searchable blog posts.
        // "Searchable" means "in plain text without any tags".
        // See the explanation in README.
        "searchDataDirectory" => $siteDir . "/db_posts",
        // Server subpath for search queries (preferably non-public).
        // This is for the curios; you will see what keywords people are looking for on
        // your blog.
        "searchQueriesRecord" => $serverDir . "/comecon-data/search_queries.txt",
    ],
];
