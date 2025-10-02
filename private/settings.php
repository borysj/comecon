<?php
// Server path for your home directory, one level above where your site is.
// On shared hosting it can be something like /home/johndoe
// On a VPS it is up to you, but usually you want /var/www
$homeDir = "";
$settings = [
    "general" => [
        // The name of your blog
        "blogName" => "My Blog",
        // Server subpath for your comments directory (preferably non-public)
        "commentsDir" => $homeDir . "/data/comments",
        // Server subpath for your subscribers directory (preferably non-public)
        "subscribersDir" => $homeDir . "/data/subscribers",
        // File with emails that are notified about new blog posts
        "subscribersFile" => "subscribers.txt",
        // Server subpath for the directory with comment feeds
        "commentFeedsDir" => $homeDir . "/html/commfeeds",
        // Language of the blog, for lang attribute of html in edit_comment.php
        "language" => "en",
        // Message file in proper language
        "messages" => "messages_en.php",
    ],
    "email" => [
        // Server subpath for PHPMailer
        "phpMailerDir" => $homeDir . "/html/modules/PHPMailer-master",
        // Email server parameters for sending notifications
        "mailNotificationsHost" => "smtp.myblog.com",
        "mailNotificationsUsername" => "notifications@myblog.com",
        "mailNotificationsPassword" => "myemailpassword_unhashed",
        // Notifications reach you through this email
        // (to confirm that they are coming as they should)
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
    "save" => [
        // If you want to change the time zone and the timestamp format,
        // you can look up PHP functions date_default_timezone_set() and
        // date() to learn about possible choices. However, if you change
        // the timestamp format, something may easily get broken.
        "timezone" => "Europe/Oslo",
        "timestamp" => "Y-m-d H:i:s",
        // There can be a master file with ALL comments in the blog's main folder.
        // There might be a reason why you need it; I was using it to generate
        // some comment statistics when generating the blog locally.
        // Here you can choose the name for the file, but if you leave it empty,
        // the master file won't be used at all.
        "allCommentsFile" => "all_comments.txt",
        // Maximal length of a comment, in characters.
        // Remember to set `maxlength` in `form-submit_comment.php` at slightly smaller
        // value for a safety margin for tags.
        "maxCommentLength" => 4000,
        // Use any random sequence of characters for the email salts.
        // These salts will be used for email if the owner does not have gravatar.
        // Then the true email is irrelevant, and another layer of obfuscation
        // (on top of SHA-256) will be added.
        "emailSaltA" => "rtald5kss",
        "emailSaltB" => "34arAsrqA",
        // Write here the answer to the captcha question for the comment form
        "commentCaptcha" => "correct_answer",
        // Set to true if you want to update comment feeds.
        // However, read the manual first:
        // You will need a feed file for the newest comments,
        // and a separate feed file for each blog post.
        // Comecon does NOT create this files, you have to create them when you
        // generate and update the blog
        "updateFeed" => false,
    ],
    "edit" => [
        // Use any short random string of characters.
        "commentSalt" => "Sq235k",
        // This is your admin backdoor for editing comments.
        // Use this command to calculate hash:
        // php -r "echo(hash('sha256', 'unhashed_password'));"
        "adminCommentPassword" => "hashed_password",
        // User has 25 minutes to edit their comment.
        // 25 minutes is the real deadline; but the user will be informed that they have
        // only 20 to give them some extra margin.
        "commentEditTimeout" => 25*60,
    ],
    "search" => [
        // Server subpath (public) for where you keep searchable blog posts.
        // "Searchable" means "in plain text without any tags".
        // See the explanation in README.
        "searchDataDirectory" => $homeDir . "/html/db_posts",
        // Server subpath for search queries (preferably non-public).
        // This is for the curios; you will see what keywords people are looking for on
        // your blog.
        "searchQueriesRecord" => $homeDir . "/data/search_queries.txt",
    ],
    "random" => [
        // The public file with the listing of all blog posts, one URL per line
        "listOfAllPosts" => $homeDir . "/html/contents/all_posts.txt",
    ],
];
