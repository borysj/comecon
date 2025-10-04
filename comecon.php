<?php

include "private/settings.php";
include "src/" . $settings['general']['messages'];
include "src/utilities.php";

if (isset($_GET["action"])) {
    $action = $_GET["action"];

    switch ($action) {
        case "add_subscriber":
            validate_request("POST", ["email"]);
            $userEmail = prepareString($_POST["email"], 60, false, false, false);
            require "src/add_subscriber.phpr";
            break;
        case "edit_comment":
            validate_request("GET", ["p", "d", "c"]);
            $p = $_GET["p"];
            $d = $_GET["d"];
            $c = $_GET["c"];
            require 'src/edit_comment.php';
            break;
        case "save_comment":
            validate_request("POST", ["comment", "name", "captcha", "url", "password", "webpage", "email"]);
            $userName = prepareString($_POST["name"], 40, false, false, false);
            $userPassword = prepareString($_POST["password"], 40, false, false, false);
            $userComment = prepareString($_POST["comment"], $settings['save']['maxCommentLength'], true, true, false);
            $userURL = prepareString($_POST["webpage"], 60, false, false, true);
            $userEmail = prepareString($_POST["email"], 60, false, false, false);
            $vipNicks = [];
            include "private/vip.php";
            require "src/email_sending.php";
            require "src/save_comment2.php";
            break;
        case "unsubscribe":
            validate_request("GET", ["user", "pw", "what"]);
            $user = $_GET["user"];
            $pw = $_GET["pw"];
            $what = $_GET["what"];
            require "src/unsubscribe.php";
            break;
        case "notify":
            validate_request("GET", ["p", "y", "m", "d", "t", "f"]);
            if (hash("sha256", $_GET["p"]) !== $settings['email']['notificationPassword']) {
                exit(EXITMSG_WRONGEMAILNOTIFICATIONPASSWORD);
            }
            $y = $_GET["y"]; // year of the blog post
            $m = $_GET["m"]; // month
            $d = $_GET["d"]; // day
            $t = $_GET["t"]; // slugified title
            $f = $_GET["f"]; // full title (with %20 for spaces)
            require "src/email_sending.php";
            require "src/email_notification.php";
            break;
        default:
            http_response_code(400);
            echo EXITMSG_INVALIDACTION;
            break;
    }
} else {
    http_response_code(400);
    echo EXITMSG_INVALIDACTION;
}
