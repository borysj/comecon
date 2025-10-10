<?php

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
$postFullTitle = getPostFullTitle($postURL);

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
    if ($settings['save']['allComments']) {
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
        updateFeed(
            $year . $month . $day,
            $title,
            $postURL,
            $currentDateTime,
            $userName,
            $userURL,
            $userComment,
            true,
            $settings['general']['commentFeedsDir']
        );
    }
    // Notify the email subscribers about the new comment
    sendNotifications(
        $year,
        $month,
        $day,
        $title,
        $currentDateTime,
        $userName,
        $userURL,
        $userComment,
        $postFullTitle,
        $settings['general'],
        $settings['email']
    );
} else {
    unset($_POST);
    exit(EXITMSG_ERRORSAVINGCOMMENT);
}
