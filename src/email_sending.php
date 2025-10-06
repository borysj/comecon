<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require __DIR__ . '/../vendor/autoload.php';

/**
 * Create an email using PHPMailer
 *
 * @param string $title The slugified title of the commented blog post (use for
 * comment notification)
 * @param string $fullTitle The full title of the new blog post (use for post
 * notification)
 * @param array<string> $sEmail Email settings (host, username, password, contact/reply-to email)
 * @param string $sBlogName The name of the blog
 * @return PHPMailer $mail The mail object
 */
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

/**
 * Send an email using PHPMailer
 *
 * @param string $year The year of the relevant blog post, YYYY
 * @param string $month The month of the relevant blog post, MM
 * @param string $day The day of the relevant blog post, DD
 * @param string $title The slugified title of the relevant blog post, if
 * non-empty the notification is about a comment
 * @param string $commentTimestamp The comment timestamp (YYYY-MM-DD HH:MM:SS),
 * leave empty if it is a notification about a new blog post
 * @param string $userName The author of the comment, pass null if it is not a
 * comment
 * @param string $userURL The website of the author of the comment, can be empty
 * @param string $userComment The comment, can be empty
 * @param string $fullTitle The full title of the relevant blog post, if
 * non-empty the notificiation is about a new blog post
 * @param array<mixed> $sGeneral General settings (the blog URL, the blog name,
 * the filepath for the blog subscribers, the directory with the comment subscribers)
 * @param array<string> $sEmail Email settings (host, username, password, contact/reply-to email,
 * owner's private email for receiving a copy of notification)
 * @return void
 */
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

/*
 * Send a test email using PHPMailer
 *
 * @param string $recipient The email of the recipient
 * @param array<string> $sEmail Email settings (host, username, password, contact/reply-to email)
 * @param string $sBlogName The name of the blog
 * @return void
 */
function sendTestEmail($recipient, $sEmail, $sBlogName)
{
    $mail = createMail("Test message", false, $sEmail, $sBlogName);
    $mail->addAddress($recipient);
    $mail->Body = "This is test.";
    $mail->send();
    return;
}
