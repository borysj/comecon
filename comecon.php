<?php

require_once __DIR__ . "/private/settings.php";
require_once __DIR__ . "/src/" . $settings['general']['messages'];
require_once __DIR__ . "/src/utilities.php";

if (isset($_GET["action"])) {
    $action = $_GET["action"];

    switch ($action) {
        case "edit":
            if (isset($_GET["p"])) {
                validate_request("GET", ["p", "id", "c"]);
                $adminPassword = $_GET["p"];
            } else {
                validate_request("GET", ["id", "c"]);
                $adminPassword = "";
            }
            $postID = $_GET["id"];
            $commentID = $_GET["c"];
            require __DIR__ . '/src/edit_comment.php';
            break;
        case "notify":
            validate_request("GET", ["p", "id", "f", "u"]);
            if (hash("sha256", $_GET["p"]) !== $settings['email']['notificationPassword']) {
                exit(EXITMSG_WRONGEMAILNOTIFICATIONPASSWORD);
            }
            $id = $_GET["id"]; // post identifier
            $f = $_GET["f"]; // full title of the blog post
            $u = $_GET["u"]; // URL of the blog post
            require __DIR__ . "/src/email_sending.php";
            require __DIR__ . "/src/email_notification.php";
            break;
        case "save":
            // We expect $_POST["postID"], but we do not need to check for
            // its existence. It has already been done by display_comment.php
            // which should come before the comment submission form. If
            // something has gone wrong there, the entire script has died
            // already.
            validate_request("POST", ["comment", "name", "captcha", "url", "password", "webpage", "email"]);
            $postID = $_POST["postID"];
            $postID = preg_replace("/[^a-zA-Z0-9_\-]/", "", $postID);
            $postID = ltrim($postID, "-");
            $postID = substr($postID, 0, 100);
            if ($postID === "") {
                exit("I cannot display the comments, because the post identifier was invalid.");
            }
            $userName = prepareString($_POST["name"], 40, false, false, false);
            $userPassword = prepareString($_POST["password"], 40, false, false, false);
            $userComment = prepareString($_POST["comment"], $settings['save']['maxCommentLength'], true, true, false);
            $userURL = prepareString($_POST["webpage"], 60, false, false, true);
            $userEmail = prepareString($_POST["email"], 60, false, false, false);
            // $_POST["postFullTitle"] is set by the blog author in the form,
            // and at the very least should be an empty string
            if ($_POST["postFullTitle"] === "") {
                $postFullTitle = getFullTitle($_SERVER["REQUEST_URI"]);
            } else {
                $postFullTitle = $_POST["postFullTitle"];
            }
            $commenters = [];
            include "private/commenters.php";
            require __DIR__ . "/src/email_sending.php";
            require __DIR__ . "/src/save_comment2.php";
            break;
        case "subscribe":
            validate_request("POST", ["email"]);
            $userEmail = prepareString($_POST["email"], 60, false, false, false);
            require __DIR__ . "/src/add_subscriber.php";
            break;
        case "unsubscribe":
            validate_request("GET", ["user", "pw", "what"]);
            $user = $_GET["user"];
            $pw = $_GET["pw"];
            $what = $_GET["what"];
            require __DIR__ . "/src/unsubscribe.php";
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
