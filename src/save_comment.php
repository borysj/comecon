---
layout: null
---
<?php
include "{{ site.dir_with_data }}/settings.php";
//include "email_messages.php";
include "exit_messages.php";
include "email_sending.php";
$vipNicks = [];
include "{{ site.dir_with_data }}/vip.php";

function prepareString($string, $length, $breaklines, $markdown, $http) {
    if (empty($string)) { return ""; }
    $string = htmlspecialchars($string, ENT_QUOTES);
    $string = trim($string);
    $string = substr($string, 0, $length);
    if ($breaklines) { $string = str_replace(array("\r\n", "\r", "\n"), "<br>", $string); }
    else { $string = str_replace(array("\r\n", "\r", "\n"), "", $string); }
    if ($markdown) {
        $string = preg_replace('/`(.*?)`/', '<code>$1</code>', $string);
        $string = preg_replace('/\[(.*?)\]\((https?:\/\/)?(.*?)\)/', '<a href="http://$3">$1</a>', $string);
        $string = preg_replace('/\*\*(.*?)\*\*/', '<b>$1</b>', $string);
        $string = preg_replace('/\*(.*?)\*/', '<i>$1</i>', $string);
    }
    if ($http && stripos($string, "http") !== 0) { $string = "http://" . $string; }
    return $string;
}

function createNonexistentFile($path) {
    if (!file_exists($path)) { touch($path); chmod($path, 0644); }
}

function checkIfDuplicate($commentFilePath, $comment) {
    if (file_exists($commentFilePath)) {
        $commentFile = file($commentFilePath);
        $lastCommentLine = $commentFile[count($commentFile)-1];
        $lastComment = explode("<|>", $lastCommentLine);
        if (trim($lastComment[4]) === $comment) {
            return true;
        } else { return false; }
    } else { return false; }
}

function checkVip($userName, $userPassword, $vipNicks) {
    if (array_key_exists($userName, $vipNicks)) {
        if($vipNicks[$userName][0] === hash("xxh3", $userPassword)) {
            return [true, $vipNicks[$userName][1], $vipNicks[$userName][2], $vipNicks[$userName][3]];
        } else {
            return [false, -1, "", ""];
        }
    } else {
        return [false, -1, "", ""];
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["comment"]) && isset($_POST["name"]) && isset($_POST["captcha"]) && isset($_POST["url"])) {

    $userName = prepareString($_POST["name"], 40, false, false, false);
    $userPassword = prepareString($_POST["password"], 40, false, false, false);
    if (!empty($userPassword)) {
        $vipInfo = checkVip($userName, $userPassword, $vipNicks);
        if ($vipInfo[0]) {
            $userRank = $vipInfo[1];
        } else { exit($exitmsg_wrongPassword); }
    } else { $vipInfo = [false, 0, "", ""]; }
    $userComment = prepareString($_POST["comment"], $maxCommentLength, true, true, false);
    $userURL = prepareString($_POST["webpage"], 60, false, false, true);
    $userRank = $vipInfo[1];
    if (empty($userURL) && $vipInfo[0] === true) { $userURL = $vipInfo[2]; }
    $userEmail = prepareString($_POST["email"], 60, false, false, false);
    if (empty($userEmail) && $vipInfo[0] === true) { $userEmail = $vipInfo[3]; }

    if (!$vipInfo[0]) {
        $captcha = trim(htmlspecialchars($_POST["captcha"], ENT_QUOTES));
        if ($captcha !== "Captcha answer goes here") { exit($exitmsg_badCaptchaComment); }
    }

    date_default_timezone_set($timezone);
    $currentDateTime = date($timestamp);

    $postURL = $_POST["url"];
    $pattern = "/(\d{4})\/(\d{2})\/(\d{2})\/(.*)\//";
    if (preg_match($pattern, $postURL, $matches)) {
        $year = $matches[1];      $month = $matches[2];
        $day = $matches[3];       $title = $matches[4];
    } else { exit($exitmsg_errorURL); }

    $filePath = "/" . $year . "/" . $month .
                "/" . $day . "/" . $title . "/";
    $commentLine = $filePath . "<|>" .
                   $currentDateTime . "<|>" .
                   $userName . "<|>" . $userURL . "<|>" .
                   $userComment . "<|>" . $userRank . PHP_EOL;
    $fullFilePath = $commentsDir . "/" . $year . "-" . $month . "-" . $day . "-" . $title . '-COMMENTS.txt';

    if (checkIfDuplicate($fullFilePath, $userComment)) { exit($exitmsg_duplicate); }

    createNonexistentFile($fullFilePath);

    $subAdded = 0;  // Result flag for registering email
    $subsFilePath = "";
    if (!empty($userEmail)) {
        if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
            $subAdded = -1;
        }
        else {
            $subsFile = $year . "-" . $month . "-" . $day . "-" . $title . "-SUBS.txt";
            $subsFilePath = $subscribersDir.  "/" . $subsFile;
            createNonexistentFile($subsFilePath);
            if (stripos(file_get_contents($subsFilePath), $userEmail) === false) {
                $password = mt_rand(1000000,9999999);
                if (file_put_contents($subsFilePath, $userEmail . "<|>" . $password . PHP_EOL, FILE_APPEND | LOCK_EX) !== false) {
                    $subAdded = 2;
                } else { $subAdded = 3; }
            } else { $subAdded = 1; }
        }
    }

    if (file_put_contents($fullFilePath, $commentLine, FILE_APPEND | LOCK_EX) !== false) {
        file_put_contents($allCommentsFile, $commentLine, FILE_APPEND | LOCK_EX);
        $cookieDateTime = str_replace(array("-", " ", ":"), "", $currentDateTime);
        setcookie("{$filePath}<|>{$cookieDateTime}",
                  hash("xxh3", $currentDateTime . $userName . $commentSalt),
                  time() + $commentEditTimeout - 5*60,
                  "/");
        unset($_POST);
        //echo $exitmsg_success;
        //subscriptionResult($subAdded, true);
        //fastcgi_finish_request();
        header("Location: {{ site.url }}{$filePath}index.php#lastComment");
        sendNotifications($year, $month, $day, $title, $userName, $userURL, $userComment, false);
    } else { unset($_POST); echo $exitmsg_errorSavingComment; }
}
else { echo $exitmsg_errorRunningCommentScript; }
