<?php

include "private/settings.php";
include "src/" . $settings['general']['messages'];
include "src/utilities.php";

if (isset($_GET['action'])) {
    $action = $_GET['action'];

    switch ($action) {
        case 'add_subscriber':
            validate_request('POST', ['email']);
            $userEmail = prepareString($_POST["email"], 60, false, false, false);
            require 'src/add_subscriber.php';
            break;
        case 'edit_comment':
            validate_request('GET', ['p', 'd', 'c']);
            $p = $_GET['p'];
            $d = $_GET['d'];
            $c = $_GET['c'];

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                validate_request('POST', ['editedComment']);
                $editedComment = prepareString($_POST["editedComment"], $settings['save']['maxCommentLength'], true, true, false);
            }
            require 'src/edit_comment.php';
            break;
        case 'save_comment':
            validate_request('POST', ['comment', 'name', 'captcha', 'url', 'password', 'webpage', 'email']);
            $userName = prepareString($_POST["name"], 40, false, false, false);
            $userPassword = prepareString($_POST["password"], 40, false, false, false);
            $userComment = prepareString($_POST["comment"], $settings['save']['maxCommentLength'], true, true, false);
            $userURL = prepareString($_POST["webpage"], 60, false, false, true);
            $userEmail = prepareString($_POST["email"], 60, false, false, false);
            $postURL = prepareString($_POST["url"], 200, false, false, true);
            $captcha = prepareString($_POST["captcha"], 10, false, false, false);
            $vipNicks = [];
            include "private/vip.php";
            require "src/email_sending.php";
            require 'src/save_comment2.php';
            break;
        case 'unsubscribe':
            validate_request('GET', ['user', 'pw', 'what']);
            $user = $_GET['user'];
            $pw = $_GET['pw'];
            $what = $_GET['what'];
            require 'src/unsubscribe.php';
            break;
        case 'send_email_notifications':
            validate_request('GET', ['p', 'y', 'm', 'd', 't', 'f']);
            if (hash("sha256", $_GET["p"]) !== $settings['email']['notificationPassword']) {
                exit(EXITMSG_WRONGEMAILNOTIFICATIONPASSWORD);
            }
            $y = $_GET['y'];
            $m = $_GET['m'];
            $d = $_GET['d'];
            $t = $_GET['t'];
            $f = $_GET['f'];
            require 'src/email_sending.php';
            require 'src/email_notification.php';
            break;
        default:
            // Handle unknown actions, maybe show an error or a default page
            http_response_code(400);
            echo "Invalid action.";
            break;
    }
} else {
    // Default behavior if no action is specified
    http_response_code(400);
    echo "Action not specified.";
}