<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require __DIR__ . '/../vendor/autoload.php';

/**
 * Create an email using PHPMailer. If it is a notification about a new blog
 * post, set $title to null and use $fullTitle. Vice versa if it is a
 * notification about a new comment.
 *
 * @param bool $newBlogPost If true, it is a notification about a new blog post.
 * If false, it is a notification about a comment.
 * @param string $fullTitle The full title of the blog post that the
 * notification is about (the blog post is either new or has been commented on)
 * @param array<string> $sEmail Email settings (host, username, password, contact/reply-to email)
 * @param string $sBlogName The name of the blog
 * @return PHPMailer $mail The mail object
 */
function createMail($newBlogPost, $fullTitle, $sEmail, $sBlogName)
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
    if ($newBlogPost) {
        $mail->Subject = "A new blog post on $sBlogName: $fullTitle";
    } else {
        $mail->Subject = "A new comment on $sBlogName for post: $fullTitle)";
    }
    return $mail;
}

/**
 * Send an email using PHPMailer. If it is a notification about a new blog
 * post, set $title to null and use $fullTitle. Vice versa if it is a
 * notification about a new comment.
 *
 * @param string $year The year of the relevant blog post, YYYY
 * @param string $month The month of the relevant blog post, MM
 * @param string $day The day of the relevant blog post, DD
 * @param string $title The slugified title of the relevant blog post
 * @param string $commentTimestamp The comment timestamp (YYYY-MM-DD HH:MM:SS),
 * pass null if it is a notification about a new blog post
 * @param string $userName The author of the comment, pass null if it is not a
 * comment
 * @param string $userURL The website of the author of the comment, can be null/empty
 * @param string $userComment The comment, can be null/empty
 * @param string $fullTitle The full title of the relevant blog post
 * @param array<mixed> $sGeneral General settings (the blog URL, the blog name,
 * the filepath for the blog subscribers, the directory with the comment subscribers)
 * @param array<string> $sEmail Email settings (host, username, password, contact/reply-to email,
 * owner's private email for receiving a copy of notification, whether we are
 * sending notifications to subscribers at all)
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
    // Validate parameters
    if ($commentTimestamp === "") {
        exit(EXITMSG_NOTIFICATIONERROR . " ::: " . __FILE__ . ":" . __LINE__);
    }
    if (!preg_match('/^\d{4}$/', $year)) {
        exit(EXITMSG_NOTIFICATIONERROR . " ::: " . __FILE__ . ":" . __LINE__);
    }
    if (!preg_match('/^\d{2}$/', $month)) {
        exit(EXITMSG_NOTIFICATIONERROR . " ::: " . __FILE__ . ":" . __LINE__);
    }
    if (!preg_match('/^\d{2}$/', $day)) {
        exit(EXITMSG_NOTIFICATIONERROR . " ::: " . __FILE__ . ":" . __LINE__);
    }
    if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $title)) {
        exit(EXITMSG_NOTIFICATIONERROR . " ::: " . __FILE__ . ":" . __LINE__);
    }

    // Create a link to the blog post (possibly to the specific comment),
    // and get the proper subscriber file
    $link = $sGeneral['siteURL'] . "/" . $year . "/" . $month . "/" . $day . "/" . $title;
    if ($commentTimestamp !== null) {
        if ($userName === null || $userName === "" && $userComment === null || $userComment === "") {
        exit(EXITMSG_NOTIFICATIONERROR . " ::: " . __FILE__ . ":" . __LINE__);
        } else {
            $commentTimestamp = str_replace(array(" ", "-", ":"), "", $commentTimestamp);
            $link = $link . "/index.php#" . $commentTimestamp;
            $filename = $year . "-" . $month . "-" . $day . "-" . $title . "-SUBS.txt";
            $text1 = "commented";
            $text2 = "comments";
            $newBlogPost = false;
        }
    } else {
        $filename = $sGeneral['subscribersFile'];
        $text1 = "new";
        $text2 = "blog";
        $newBlogPost = true;
    }
    $path = $sGeneral['subscribersDir'] . "/" . $filename;

    // Start creating a notification email. It will be very short...
    $body1 = "<html><body><p><a href=\"$link\">Link to the $text1 blog post</a></p>";
    // ...unless it is a comment notification because then we want to add the
    // comment itself.
    if ($commentTimestamp !== null) {
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

        // If the owner private mail is set, the owner will receive an automatic
        // notification about any comment. They will be able to react quickly in
        // case of spam or similar
        if ($sEmail['ownerPrivateMail']) {
            $body1 = $body1 . "</body></html>";
            $mail = createMail($newBlogPost, $fullTitle, $sEmail, $sGeneral['blogName']);
            $mail->addAddress($sEmail['ownerPrivateMail']);
            $mail->Body = $body1;
            $mail->send();
        }
    }

    // If there is no subscription file for this particular post, or if the
    // notify-settings is false, we do not send notifications
    if (!file_exists($path) || !$sEmail('notify']) {
        return false;
    }
    $subscribers = fopen($path, "r");
    while (!feof($subscribers)) {
        $line = fgets($subscribers);
        if (empty($line)) {
            break;
        }
        list($subscriber, $password) = explode("<|>", $line);
        // We add a footer with an unsubscribe link
        $unsubLink = $sGeneral['siteURL'] .
                     "comecon.php?action=unsubscribe&user=$subscriber&pw=$password&what=$filename";
        $body2 = "<p style=\"font-size: small;\"><a href=\"$unsubLink\">
                  Use this link to unsubscribe from the $text2</a></p>
                  <p style=\"font-size: small;\">Do not reply to this email.
                  If you encounter technical problems, contact me here:
                  {$sEmail['blogContactMail']}</p></body></html>";
        $mail = createMail($newBlogPost, $fullTitle, $sEmail, $sGeneral['blogName']);
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
    $mail = createMail(true, "Test message", $sEmail, $sBlogName);
    $mail->addAddress($recipient);
    $mail->Body = "This is test.";
    $mail->send();
    return;
}
