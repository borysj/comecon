<?php

/**
 * Check whether a file with given path exists. If not, create it.
 *
 * @param string $path The filepath to check and possibly create
 * @return void
 */
function createNonexistentFile($path)
{
    if (!file_exists($path)) {
        touch($path);
        chmod($path, 0644);
    }
}

/**
 * Check whether the comment is the duplicate (identical to the most recent
 * comment).
 *
 * @param string $commentFilePath The comment file to look up
 * @param string $comment The comment to check
 * @return bool True if duplicate, false if not or if the comment file not found
 */
function checkIfDuplicate($commentFilePath, $comment)
{
    if (!file_exists($commentFilePath)) {
        return false;
    }
    $commentFile = file($commentFilePath);
    if (!$commentFile) {
        return false;
    }
    // Get the last saved comment record from the comment file
    $lastCommentLine = $commentFile[count($commentFile) - 1];
    $lastComment = explode("<|>", $lastCommentLine);
    // The field with the comment text is indexed as 5
    if (trim($lastComment[5]) === $comment) {
        return true;
    } else {
        return false;
    }
}

/**
 * Check if the user-password combination is among the registered users.
 * The returned array contains the following elements:
 * [0]: bool, whether the user-password combination has been recognized
 * [1]: int, the rank of the user (1 for the blog owner, 2 for an eminent user,
 *      3 for a normal registered user)
 * [2]: string, the user's website
 * [3]: string, the user's email (for subscription) or possibly only the hash of
 *      the user's email (for gravatar); see description in vip.php
 * [4]: int, 0 if user does not want to subscribe to comments after posting
 *      their own, 1 if they want to; see description in vip.php
 *
 * @param string $userName The name provided by the commenter
 * @param string $userPassword The password provided by the commenter
 * @param array<string, mixed> $vipNicks The associative array with the registered users
 *
 * @return array<mixed>
 */
function checkVip($userName, $userPassword, $vipNicks)
{
    if (array_key_exists($userName, $vipNicks)) {
        if ($vipNicks[$userName][0] === hash("sha256", $userPassword)) {
            return [true, $vipNicks[$userName][1], $vipNicks[$userName][2],
                    $vipNicks[$userName][3], $vipNicks[$userName][4]];
        } else {
            return [false, -1, "", "", 0];
        }
    } else {
        return [false, -1, "", "", 0];
    }
}

/**
 * Update the comment feed.
 *
 * @param string $dateOfPost The date of the commented blog post, YYYYMMDD
 * @param string $postTitle The title of the commented blog post
 * @param string $postURL The URL of the commented blog post
 * @param string $commentTimestamp The timestamp of the comment, from date($settings['save']['timestamp'])
 * @param string $commenter The name of the commenter
 * @param string $commenterURL The URL of the commenter's website
 * @param string $comment The comment text (may contain HTML tags)
 * @param bool $newestComments If true, update also the global feed with the newest
 * comments. If false, update only the feed with the particular feed for this blog
 * post
 *
 * @return bool Returns false if the filepath for the feed does not
 * exist or if the feed content is unreadable or if the comment timestamp is
 * unreadeable or if the preg_replace returned null somehow,
 * returns true after successfully updating the feed
 */
function updateFeed(
    $dateOfPost,
    $postTitle,
    $postURL,
    $commentTimestamp,
    $commenter,
    $commenterURL,
    $comment,
    $newestComments
) {
    global $settings;
    $feedFilename = "comments_blogpost" . $dateOfPost . ".xml";
    $feedFilepath = $settings['general']['commentFeedsDir'] . "/" . $feedFilename;
    if (!file_exists($feedFilepath)) {
        return false;
    }
    $commentAnchor = str_replace(array(" ", "-", ":"), "", $commentTimestamp);
    $commentURLWithAnchor = $postURL . "#" . $commentAnchor;
    $timestamp = strtotime($commentTimestamp);
    if (!$timestamp) {
        return false;
    }
    $formattedTimestamp = date("c", $timestamp);
    $entryTitle = MSG_COMMENTFEEDENTRYTITLE;
    $commentInContext = MSG_COMMENTINCONTEXT;
    $newEntry = <<<ENTRYENDS
    <entry>
    <title>$entryTitle $postTitle</title>
    <author><name>$commenter</name><uri>$commenterURL</uri></author>
    <link rel="alternate" type="text/html" href="$commentURLWithAnchor" />
    <id>$postTitle$formattedTimestamp</id>
    <published>$formattedTimestamp</published>
    <updated>$formattedTimestamp</updated>
    <summary>$commentInContext</summary>
    <content type="html"><![CDATA[$comment]]></content>
    </entry>
    </feed>
    ENTRYENDS;
    $feedContent = file_get_contents($feedFilepath);
    if (!$feedContent) {
        return false;
    }
    // We use regex to change the <updated>-tag of the feed with the date of the
    // update (which is the timestamp of the comment just added)
    $feedContent = preg_replace('/^\s*<updated>.*$/m', "<updated>$formattedTimestamp</updated>", $feedContent, 1);
    if ($feedContent === null) {
        return false;
    }
    // We replace the closing tag of the feed with the new item
    $feedContent = str_replace("</feed>", $newEntry, $feedContent);
    file_put_contents($feedFilepath, $feedContent);
    // We will possibly also update the global feed with the newest comments
    if ($newestComments) {
        $feedFilepath = $settings['general']['commentFeedsDir'] . "/comments_newest.xml";
        if (!file_exists($feedFilepath)) {
            return false;
        }
        $feedContent = file_get_contents($feedFilepath);
        if (!$feedContent) {
            return false;
        }
        // If there are more than 10 items in the feed, delete the first (i.e.
        // the oldest) item
        if (substr_count($feedContent, "<entry>") > 10) {
            $feedContent = preg_replace('/\R?<entry>[\s\S]+?<\/entry>\R?/m', "", $feedContent, 1);
        }
        if ($feedContent === null) {
            return false;
        }
        $feedContent = preg_replace('/^\s*<updated>.*$/m', "<updated>$formattedTimestamp</updated>", $feedContent, 1);
        if ($feedContent === null) {
            return false;
        }
        $feedContent = str_replace("</feed>", $newEntry, $feedContent);
        file_put_contents($feedFilepath, $feedContent);
    }
    return true;
}

/**
 * Check if the gravatar for the email provided exists (is registered by the
 * user)
 *
 * @param string $email The email to be checked in the gravatar database
 * @param bool $notYetHashed If true, the email is a normal email, if false, the
 * email has already been hashed (for the security purposes; sometimes we do not
 * need the email itself, but only the hash)
 * @return bool True if the gravatar is registered, false if it is not or if the
 * gravatar database gave unexpected response
 */
function gravatarExists($email, $notYetHashed)
{
    if ($notYetHashed) {
        $hashedEmail = md5(strtolower(trim($email)));
    } else {
        $hashedEmail = $email;
    }
    $url = "https://www.gravatar.com/avatar/" . $hashedEmail . "?d=404";
    $headers = @get_headers($url);
    if (!$headers) {
        return false;
    }
    if (!preg_match("|200|", $headers[0])) {
        return false;
    } else {
        return true;
    }
}


// We check if the user is registered. They could be registered if they have
// provided a password together with their name.
if (!empty($userPassword)) {
    $vipInfo = checkVip($userName, $userPassword, $vipNicks);
    if ($vipInfo[0]) {
        $userRank = $vipInfo[1];
    } else {
        exit(EXITMSG_WRONGPASSWORD);
    }
} else {
    $vipInfo = [false, 0, "", "", 0];
}

// Check captcha if the user is not registered
$captcha = trim(htmlspecialchars($_POST["captcha"], ENT_QUOTES));
if (!$vipInfo[0]) {
    if ($captcha !== $settings['save']['commentCaptcha']) {
        exit(EXITMSG_BADCOMMENTCAPTCHA);
    }
}

$userRank = $vipInfo[1];
// If the user has not provided their website, but is recognized as a
// registered user, get the website from the user database. Notice that the
// website from the database could be an empty string; it is not mandatory.
// Notice also that if the registered user has provided their website
// directly, this website will have priority over the registered website.
if (empty($userURL) && $vipInfo[0] === true) {
    $userURL = $vipInfo[2];
}

// Process the user email. Here, there are several cases.
// The user has provided email directly (in the comment form)
if (!empty($userEmail)) {
    // If the gravatar for this email exists, hash the email directly
    if (gravatarExists($userEmail, true)) {
        $hashedEmail = hash("sha256", $userEmail);
    } else {
    // If the gravatar does not exist, salt the hash for increased security
    // as it will be later accessible through gravatar links in the
    // comments. The reason that we store anything at all is that we want to
    // ensure the same random gravatar for this user across all posts and
    // comments.
        $hashedEmail = hash("sha256", $settings['save']['emailSaltA'] . $userEmail . $settings['save']['emailSaltB']);
    }
    // Check if the user wants to subscribe to comments by email
    if (isset($_POST["email-comments"]) && $_POST["email-comments"] == "on") {
        $wantsEmails = 1;
    } else {
        $wantsEmails = 0;
    }
} elseif (!empty($vipInfo[3])) {
// The user has not provided email directly, but has registered it before
    // If the user said earlier that they want to subscribe to comments by
    // email, get their email. If the gravatar does not exist, salt the hash
    // for increased security (the hash will be accessible through gravatar
    // links in the comments, see the remark above)
    if ($vipInfo[4] === 1) {
        $wantsEmails = 1;
        $userEmail = $vipInfo[3];
        if (gravatarExists($userEmail, true)) {
            $hashedEmail = hash("sha256", $userEmail);
        } else {
            $hashedEmail = hash(
                "sha256",
                $settings['save']['emailSaltA'] . $userEmail . $settings['save']['emailSaltB']
            );
        }
    } else {
    // If the user does not want to subscribe to comments by email, the
    // database stores the email hash only. But if the gravatar for this
    // hash does not exist, we mangle it (hash it again) for increased
    // security (the hash will be accessible through gravatar links in the
    // comments, see the remark above)
        $wantsEmails = 0;
        $hashedEmail = $vipInfo[3];
        if (!gravatarExists($hashedEmail, false)) {
            $hashedEmail = hash("sha256", $hashedMail);
        }
    }
}

date_default_timezone_set($settings['save']['timezone']);
$currentDateTime = date($settings['save']['timestamp']);

// We need the basic URL of the commented blog post. There could be a
// fragment identified of an earlier comment. If so, we have to remove it.
$postURL = $_POST["url"];
if (str_contains($postURL, "#")) {
    $postURL = strstr($postURL, "#", true);
}
// The basic URL should end with index.php.
// This is OK: https://blog.example.com/2020/05/20/blog-title/index.php
// This is not OK: https://blog.example.com/2020/05/20/blog-title/
// The latter works perfectly fine as far as displaying the blog post is
// concerned, but we want also to have the URL in its standard form.
if (!str_ends_with($postURL, "index.php")) {
    if (!str_ends_with($postURL, "/")) {
        $postURL .= "/index.php";
    } else {
        $postURL .= "index.php";
    }
}

$pattern = "/(\d{4})\/(\d{2})\/(\d{2})\/(.*)\//";
if (preg_match($pattern, $postURL, $matches)) {
    $year = $matches[1];
    $month = $matches[2];
    $day = $matches[3];
    $title = $matches[4];
} else {
    exit(EXITMSG_ERRORURL);
}

$filePath = "/" . $year . "/" . $month .
            "/" . $day . "/" . $title . "/";
// The email variant will be stored in the comment file specific for this
// blog post. The second variant without emails will be stored in the global
// and public comment file.
$commentLineWithEmail = $filePath . "<|>" .
                        $currentDateTime . "<|>" .
                        $userName . "<|>" . $userURL . "<|>" . $hashedEmail . "<|>" .
                        $userComment . "<|>" . $userRank . PHP_EOL;
$commentLineWithoutEmail = $filePath . "<|>" .
                           $currentDateTime . "<|>" .
                           $userName . "<|>" . $userURL . "<|>" . "<|>" .
                           $userComment . "<|>" . $userRank . PHP_EOL;
$fullFilePath = $settings['general']['commentsDir'] . "/" .
    $year . "-" . $month . "-" . $day . "-" . $title . '-COMMENTS.txt';

if (checkIfDuplicate($fullFilePath, $userComment)) {
    exit(EXITMSG_DUPLICATE);
}

createNonexistentFile($fullFilePath);

// If the commenter wants to subscribe to comments by email, we have to
// update (possible create) the subscribers file
if (!empty($userEmail) && $wantsEmails == 1) {
    if (filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
        $subsFile = $year . "-" . $month . "-" . $day . "-" . $title . "-SUBS.txt";
        $subsFilePath = $settings['general']['subscribersDir'] .  "/" . $subsFile;
        createNonexistentFile($subsFilePath);
        $fileContents = file_get_contents($subsFilePath);
        if (!$fileContents) {
            exit(EXITMSG_FILEUNREADABLE);
        }
        // If the email is not already in the subscribers file, add it
        // together with the password (used for unsubscribing)
        if (stripos($fileContents, $userEmail) === false) {
            $password = mt_rand(1000000, 9999999);
            file_put_contents($subsFilePath, $userEmail . "<|>" . $password . PHP_EOL, FILE_APPEND | LOCK_EX);
        }
    }
}

// Update the global comment file and the particular comment file. Set the
// cookie in case the user wants to edit their comment
if (file_put_contents($fullFilePath, $commentLineWithEmail, FILE_APPEND | LOCK_EX) !== false) {
    if ($settings['save']['allCommentsFile']) {
        file_put_contents($settings['save']['allCommentsFile'], $commentLineWithoutEmail, FILE_APPEND | LOCK_EX);
    }
    $cookieDateTime = str_replace(array("-", " ", ":"), "", $currentDateTime);
    setcookie(
        "{$filePath}<|>{$cookieDateTime}",
        hash("sha256", $currentDateTime . $userName . $settings['edit']['commentSalt']),
        time() + $settings['edit']['commentEditTimeout'] - 5 * 60,
        "/"
    );
    unset($_POST);
    // First, send the user back to their comment...
    header("Location: {$settings['general']['siteURL']}{$filePath}index.php#lastComment");
    // ...and update the comment feeds in the background (from the user's
    // perspective).
    if ($settings['save']['updateFeed']) {
        updateFeed($year . $month . $day, $title, $postURL, $currentDateTime, $userName, $userURL, $userComment, true);
    }
    // Notify the email subscribers about the new comment
    sendNotifications($year, $month, $day, $title, $currentDateTime, $userName, $userURL, $userComment, false);
} else {
    unset($_POST);
    exit(EXITMSG_ERRORSAVINGCOMMENT);
}
