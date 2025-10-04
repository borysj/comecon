<?php

if (isset($_GET['action'])) {
    $action = $_GET['action'];

    switch ($action) {
        case 'add_subscriber':
            require 'src/add_subscriber.php';
            break;
        case 'edit_comment':
            require 'src/edit_comment.php';
            break;
        case 'save_comment':
            require 'src/save_comment2.php';
            break;
        case 'unsubscribe':
            require 'src/unsubscribe.php';
            break;
        case 'send_email_notifications':
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