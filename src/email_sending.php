---
layout: null
---
<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

$phpMailerException = $settings['email']['phpMailerDir'] . "/src/Exception.php";
$phpMailerMain = $settings['email']['phpMailerDir'] . "/src/PHPMailer.php";
$phpMailerSMTP = $settings['email']['phpMailerDir'] . "/src/SMTP.php";
require $phpMailerException;
require $phpMailerMain;
require $phpMailerSMTP;

function createMail($title, $fullTitle)
{
    global $settings;
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = $settings['email']['mailNotificationsHost'];
    $mail->SMTPAuth = true;
    $mail->Username = $settings['email']['mailNotificationsUsername'];
    $mail->Password = $settings['email']['mailNotificationsPassword'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;
    $mail->setFrom($settings['email']['mailNotificationsUsername']);
    $mail->addReplyTo($settings['email']['blogContactMail']);
    $mail->isHTML(true);
    $mail->CharSet = "UTF-8";
    if ($fullTitle) {
        $mail->Subject = "A new blog post on {$settings['general']['blogName']}: $fullTitle";
    } else {
        $mail->Subject = "A new comment on {$settings['general']['blogName']} ($title)";
    }
    return $mail;
}

function sendNotifications($year, $month, $day, $title, $commentTimestamp, $userName, $userURL, $userComment, $fullTitle)
{
    global $settings;
    $link = "{{ site.url }}/" . $year . "/" . $month . "/" . $day . "/" . $title;
    if ($commentTimestamp !== "") {
        $commentTimestamp = str_replace(array(" ", "-", ":"), "", $commentTimestamp);
        $link = $link . "/index.php#" . $commentTimestamp;
    }
    if ($fullTitle) {
        $filename = $settings['general']['subscribersFile'];
        $text1 = "new";
        $text2 = "blog";
    } else {
        $filename = $year . "-" . $month . "-" . $day . "-" . $title . "-SUBS.txt";
        $text1 = "commented";
        $text2 = "comments";
    }
    $path = $settings['general']['subscribersDir'] . "/" . $filename;
    $body1 = "<html><body><p><a href=\"$link\">Link to the $text1 blog post</a></p>";
    if ($userName != "none") {
        if (!empty($userURL)) {
            $nick = "<a href=\"$userURL\">$userName</a>";
        } else {
            $nick = $userName;
        }
        $body1 = $body1 . "<p><b>Author of comment:</b> $nick</p>";
        $body1 = $body1 . "<p>$userComment</p><br><br>";
        $body1 = $body1 . "<p style=\"font-size: small;\">Above you see the original version of the comment.
                           The author could have edited it.
                           Follow the link to see the newest version.</p>";
    }

    if (!$fullTitle) {
        $body1 = $body1 . "</body></html>";
        $mail = createMail($title, $fullTitle);
        $mail->addAddress($settings['email']['ownerPrivateMail']);
        $mail->Body = $body1;
        $mail->send();
    }

    if (!file_exists($path)) {
        return;
    }
    $subscribers = fopen($path, "r");
    while (!feof($subscribers)) {
        $line = fgets($subscribers);
        if (empty($line)) {
            break;
        }
        list($subscriber, $password) = explode("<|>", $line);
        $unsubLink = "{{ site.url }}/assets/unsubscribe.php?user=$subscriber&pw=$password&what=$filename";
        $body2 = "<p style=\"font-size: small;\"><a href=\"$unsubLink\">Use this link to unsubscribe from the $text2</a></p>
                  <p style=\"font-size: small;\">Do not reply to this email. If you encounter technical problems, contact me here: {$settings['email']['blogContactMail']}</p></body></html>";
        $mail = createMail($title, $fullTitle);
        $mail->addAddress($subscriber);
        $mail->Body = $body1 . $body2;
        $mail->send();
    }
    fclose($subscribers);
    return;
}

function sendTestEmail($recipient)
{
    $mail = createMail("Test message", false);
    $mail->addAddress($recipient);
    $mail->Body = "This is test.";
    $mail->send();
    return;
}
