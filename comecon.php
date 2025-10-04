<?php

include "private/settings.php";
include "src/" . $settings['general']['messages'];
include "src/utilities.php";

if (isset($_GET['action'])) {
    $action = $_GET['action'];

    switch ($action) {
        case 'add_subscriber':
            if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST["email"])) {
                exit(1);
            }
            if (!is_string($_POST["email"])) {
                exit(1);
            }
            $userEmail = prepareString($_POST["email"], 60, false, false, false);
            require 'src/add_subscriber.php';
            break;
        case 'edit_comment':
            if (!isset($_GET['p']) || !is_string($_GET['p']) || !isset($_GET['d']) || !is_string($_GET['d']) || !isset($_GET['c']) || !is_string($_GET['c'])) {
                exit(1);
            }
            $p = $_GET['p'];
            $d = $_GET['d'];
            $c = $_GET['c'];
            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                if (!isset($_POST["editedComment"]) || !is_string($_POST["editedComment"])) {
                    exit(1);
                }
                $editedComment = prepareString($_POST["editedComment"], $settings['save']['maxCommentLength'], true, true, false);
            }
            require 'src/edit_comment.php';
            break;
        case 'save_comment':
            if (
                $_SERVER["REQUEST_METHOD"] !== "POST" ||
                !isset($_POST["comment"]) ||
                !isset($_POST["name"]) ||
                !isset($_POST["captcha"]) ||
                !isset($_POST["url"])
            ) {
                exit(EXITMSG_ERRORRUNNINGCOMMENTSCRIPT);
            }
            if (!is_string($_POST["name"]) || !is_string($_POST["password"]) || !is_string($_POST["comment"]) || !is_string($_POST["webpage"])) {
                exit(1);
            }
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
            if ($_SERVER["REQUEST_METHOD"] !== "GET" || !isset($_GET["user"]) || !isset($_GET["pw"]) || !isset($_GET["what"])) {
                exit(EXITMSG_ERRORRUNNINGSUBSCRIBERSCRIPT);
            }
            if (!is_string($_GET["user"])) {
                exit(1);
            }
            $user = $_GET['user'];
            $pw = $_GET['pw'];
            $what = $_GET['what'];
            require 'src/unsubscribe.php';
            break;
        case 'send_email_notifications':
            if (
                ($_SERVER["REQUEST_METHOD"] !== "GET") ||
                (!isset($_GET["p"])) ||
                (!is_string($_GET["p"])) ||
                (hash("sha256", $_GET["p"]) !== $settings['email']['notificationPassword']) ||
                (!isset($_GET["y"])) ||
                (!isset($_GET["m"])) ||
                (!isset($_GET["d"])) ||
                (!isset($_GET["t"])) ||
                (!isset($_GET["f"]))
            ) {
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