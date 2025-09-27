---
layout: null
---
<?php
include "{{ site.dir_with_data }}/settings.php";
include $messages;
include "utilities.php";
include "email_sending.php";
$vipNicks = [];
include "{{ site.dir_with_data }}/vip.php";

function createNonexistentFile($path) {
    if (!file_exists($path)) { touch($path); chmod($path, 0644); }
}

function checkIfDuplicate($commentFilePath, $comment) {
    if (file_exists($commentFilePath)) {
        $commentFile = file($commentFilePath);
        $lastCommentLine = $commentFile[count($commentFile)-1];
        $lastComment = explode("<|>", $lastCommentLine);
        if (trim($lastComment[5]) === $comment) {
            return true;
        } else { return false; }
    } else { return false; }
}

function checkVip($userName, $userPassword, $vipNicks) {
    if (array_key_exists($userName, $vipNicks)) {
        if($vipNicks[$userName][0] === hash("xxh3", $userPassword)) {
            return [true, $vipNicks[$userName][1], $vipNicks[$userName][2],
                    $vipNicks[$userName][3], $vipNicks[$userName][4]];
        } else {
            return [false, -1, "", "", 0];
        }
    } else {
        return [false, -1, "", "", 0];
    }
}

function updateFeed($dateOfPost, $postTitle, $postURL, $commentTimestamp, $commenter, $commenterURL, $comment, $newestComments) {
    global $commentFeedsDir, $msg_commentFeedEntryTitle, $msg_commentInContext;
    $feedFilename = "comments_blogpost" . $dateOfPost . ".xml";
    $feedFilepath = $commentFeedsDir . "/" . $feedFilename;
    if (!file_exists($feedFilepath)) { return false; }
    $commentAnchor = str_replace(array(" ", "-", ":"), "", $commentTimestamp);
    $commentURLWithAnchor = $postURL . "#" . $commentAnchor;
    $commentTimestamp = date("c", strtotime($commentTimestamp));
    $newEntry = <<<ENTRYENDS
    <entry>
    <title>$msg_commentFeedEntryTitle $postTitle</title>
    <author><name>$commenter</name><uri>$commenterURL</uri></author>
    <link rel="alternate" type="text/html" href="$commentURLWithAnchor" />
    <id>$postTitle$commentTimestamp</id>
    <published>$commentTimestamp</published>
    <updated>$commentTimestamp</updated>
    <summary>$msg_commentInContext</summary>
    <content type="html"><![CDATA[$comment]]></content>
    </entry>
    </feed>
    ENTRYENDS;
    $feedContent = file_get_contents($feedFilepath);
    $feedContent = preg_replace('/^\s*<updated>.*$/m', "<updated>$commentTimestamp</updated>", $feedContent, 1);
    $feedContent = str_replace("</feed>", $newEntry, $feedContent);
    file_put_contents($feedFilepath, $feedContent);
    if ($newestComments) {
        $feedFilepath = $commentFeedsDir . "/comments_newest.xml";
        if (!file_exists($feedFilepath)) { return false; }
        $feedContent = file_get_contents($feedFilepath);
        if (substr_count($feedContent, "<entry>") > 10) {
            $feedContent = preg_replace('/\R?<entry>[\s\S]+?<\/entry>\R?/m', "", $feedContent, 1);
        }
        $feedContent = preg_replace('/^\s*<updated>.*$/m', "<updated>$commentTimestamp</updated>", $feedContent, 1);
        $feedContent = str_replace("</feed>", $newEntry, $feedContent);
        file_put_contents($feedFilepath, $feedContent);
    }
    return true;
}

function gravatarExists($email, $notYetHashed) {
    if ($notYetHashed) { $hashedEmail = md5(strtolower(trim($email))); }
    else { $hashedEmail = $email; }
    $url = "https://www.gravatar.com/avatar/" . $hashedEmail . "?d=404";
    $headers = @get_headers($url);
    if (!preg_match("|200|", $headers[0])) { $gravatar = false; }
    else { $gravatar = true; }
    return $gravatar;
}


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["comment"]) && isset($_POST["name"]) && isset($_POST["captcha"]) && isset($_POST["url"])) {

    $userName = prepareString($_POST["name"], 40, false, false, false);
    $userPassword = prepareString($_POST["password"], 40, false, false, false);
    if (!empty($userPassword)) {
        $vipInfo = checkVip($userName, $userPassword, $vipNicks);
        if ($vipInfo[0]) {
            $userRank = $vipInfo[1];
        } else { exit($exitmsg_wrongPassword); }
    } else { $vipInfo = [false, 0, "", "", 0]; }
    $userComment = prepareString($_POST["comment"], $maxCommentLength, true, true, false);
    $userURL = prepareString($_POST["webpage"], 60, false, false, true);
    $userRank = $vipInfo[1];
    if (empty($userURL) && $vipInfo[0] === true) { $userURL = $vipInfo[2]; }

    $userEmail = prepareString($_POST["email"], 60, false, false, false);
    if (!empty($userEmail)) {
        if (gravatarExists($userEmail, true)) { $hashedEmail = hash("sha256", $userEmail); }
        else { $hashedEmail = hash("sha256", $emailSaltA . $userEmail . $emailSaltB); }
        if (isset($_POST["email-comments"]) && $_POST["email-comments"] == "on") { $wantsEmails = 1; }
        else { $wantsEmails = 0; }
    }
    elseif (!empty($vipInfo[3])) {
        if($vipInfo[4] === 1) {
            $wantsEmails = 1;
            $userEmail = $vipInfo[3];
            if (gravatarExists($userEmail, true)) { $hashedEmail = hash("sha256", $userEmail); }
            else { $hashedEmail = hash("sha256", $emailSaltA . $userEmail . $emailSaltB); }
        }
        else {
            $wantsEmails = 0;
            $hashedEmail = $vipInfo[3];
            if (!gravatarExists($hashedEmail, false)) { $hashedEmail = hash("sha256", $hashedMail); }
        }
    }

    if (!$vipInfo[0]) {
        $captcha = trim(htmlspecialchars($_POST["captcha"], ENT_QUOTES));
        if ($captcha !== $commentCaptcha) { exit($exitmsg_badCommentCaptcha); }
    }

    date_default_timezone_set($timezone);
    $currentDateTime = date($timestamp);

    $postURL = $_POST["url"];
    if (str_contains($postURL, "#")) {
        $postURL = strstr($postURL, "#", true);
    }
    if (!str_ends_with($postURL, "index.php")) {
        if (!str_ends_with($postURL, "/")) {
            $postURL .= "/index.php";
        } else {
            $postURL .= "index.php";
        }
    }

    $pattern = "/(\d{4})\/(\d{2})\/(\d{2})\/(.*)\//";
    if (preg_match($pattern, $postURL, $matches)) {
        $year = $matches[1];      $month = $matches[2];
        $day = $matches[3];       $title = $matches[4];
    } else { exit($exitmsg_errorURL); }

    $filePath = "/" . $year . "/" . $month .
                "/" . $day . "/" . $title . "/";
    $commentLineWithEmail = $filePath . "<|>" .
                            $currentDateTime . "<|>" .
                            $userName . "<|>" . $userURL . "<|>" . $hashedEmail . "<|>" .
                            $userComment . "<|>" . $userRank . PHP_EOL;
    $commentLineWithoutEmail = $filePath . "<|>" .
                               $currentDateTime . "<|>" .
                               $userName . "<|>" . $userURL . "<|>" . "<|>" .
                               $userComment . "<|>" . $userRank . PHP_EOL;
    $fullFilePath = $commentsDir . "/" . $year . "-" . $month . "-" . $day . "-" . $title . '-COMMENTS.txt';

    if (checkIfDuplicate($fullFilePath, $userComment)) { exit($exitmsg_duplicate); }

    createNonexistentFile($fullFilePath);

    if (!empty($userEmail) && $wantsEmails == 1) {
        if (filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
            $subsFile = $year . "-" . $month . "-" . $day . "-" . $title . "-SUBS.txt";
            $subsFilePath = $subscribersDir.  "/" . $subsFile;
            createNonexistentFile($subsFilePath);
            if (stripos(file_get_contents($subsFilePath), $userEmail) === false) {
                $password = mt_rand(1000000,9999999);
                file_put_contents($subsFilePath, $userEmail . "<|>" . $password . PHP_EOL, FILE_APPEND | LOCK_EX);
            }
        }
    }

    if (file_put_contents($fullFilePath, $commentLineWithEmail, FILE_APPEND | LOCK_EX) !== false) {
        file_put_contents($allCommentsFile, $commentLineWithoutEmail, FILE_APPEND | LOCK_EX);
        $cookieDateTime = str_replace(array("-", " ", ":"), "", $currentDateTime);
        setcookie("{$filePath}<|>{$cookieDateTime}",
                  hash("xxh3", $currentDateTime . $userName . $commentSalt),
                  time() + $commentEditTimeout - 5*60,
                  "/");
        unset($_POST);
        header("Location: {{ site.url }}{$filePath}index.php#lastComment");
        if ($commentFeed) {
            updateFeed($year.$month.$day, $title, $postURL, $currentDateTime, $userName, $userURL, $userComment, true);
        }
        sendNotifications($year, $month, $day, $title, $currentDateTime, $userName, $userURL, $userComment, false);
    } else { unset($_POST); echo $exitmsg_errorSavingComment; }
}
else { echo $exitmsg_errorRunningCommentScript; }
