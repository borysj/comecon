<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require __DIR__ . '/../vendor/autoload.php';

function createMail($title, $fullTitle, $sEmail, $sBlogName)
{
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = $sEmail['mailNotificationsHost'];
    $mail->SMTPAuth = true;
    $mail->Username = $sEmail['mailNotificationsUsername'];
    $mail->Password = $sEmail['mailNotificationsPassword'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;
    $mail->setFrom($sEmail['mailNotificationsUsername']);
    $mail->addReplyTo($sEmail['blogContactMail']);
    $mail->isHTML(true);
    $mail->CharSet = "UTF-8";
    if ($fullTitle) {
        $mail->Subject = "A new blog post on $sBlogName: $fullTitle";
    } else {
        $mail->Subject = "A new comment on $sBlogName ($title)";
    }
    return $mail;
}

function sendNotifications(
    $year,
    $month,
    $day,
    $title,
    $commentTimestamp,
    $userName,
    $userURL,
    $userComment,
    $fullTitle,
    $sGeneral,
    $sEmail
) {
    $link = $sGeneral['siteURL'] . "/" . $year . "/" . $month . "/" . $day . "/" . $title;
    if ($commentTimestamp !== "") {
        $commentTimestamp = str_replace(array(" ", "-", ":"), "", $commentTimestamp);
        $link = $link . "/index.php#" . $commentTimestamp;
    }
    if ($fullTitle) {
        $filename = $sGeneral['subscribersFile'];
        $text1 = "new";
        $text2 = "blog";
    } else {
        $filename = $year . "-" . $month . "-" . $day . "-" . $title . "-SUBS.txt";
        $text1 = "commented";
        $text2 = "comments";
    }
    $path = $sGeneral['subscribersDir'] . "/" . $filename;
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
        $mail = createMail($title, $fullTitle, $sEmail, $sGeneral['blogName']);
        $mail->addAddress($sEmail['ownerPrivateMail']);
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
        $unsubLink = $sGeneral['siteURL'] .
                     "/assets/unsubscribe.php?user=$subscriber&pw=$password&what=$filename";
        $body2 = "<p style=\"font-size: small;\"><a href=\"$unsubLink\">
                  Use this link to unsubscribe from the $text2</a></p>
                  <p style=\"font-size: small;\">Do not reply to this email.
                  If you encounter technical problems, contact me here:
                  {$sEmail['blogContactMail']}</p></body></html>";
        $mail = createMail($title, $fullTitle, $sEmail, $sGeneral['blogName']);
        $mail->addAddress($subscriber);
        $mail->Body = $body1 . $body2;
        $mail->send();
    }
    fclose($subscribers);
    return;
}

function sendTestEmail($recipient, $sEmail, $sBlogName)
{
    $mail = createMail("Test message", false, $sEmail, $sBlogName);
    $mail->addAddress($recipient);
    $mail->Body = "This is test.";
    $mail->send();
    return;
}
