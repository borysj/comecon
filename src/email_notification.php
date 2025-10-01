---
layout: null
---
<?php
require "{{ site.dir_with_data }}/settings.php";
require "email_sending.php";
include $messages;

// This is a script to notify the blog subscribers about a new post by email.
// p is the admin password.
// y, m, d is the date in the format yyyy-mm-dd
// t is the slugified title of the new blog post
// f is the full title of the new blog post (use %20 for spaces and so on).
// For instance:
// https://myblog.example.com/email_notification.php?p=secretpassword&y=2025&m=08&d=12&t=about-birds&f=About%20birds
if (($_SERVER["REQUEST_METHOD"] === "GET") &&
    (isset($_GET["p"])) && (hash("sha256", $_GET["p"]) === $notificationPassword) &&
    (isset($_GET["y"])) &&
    (isset($_GET["m"])) &&
    (isset($_GET["d"])) &&
    (isset($_GET["t"])) &&
    (isset($_GET["f"]))) {
    sendNotifications($_GET["y"], $_GET["m"], $_GET["d"], $_GET["t"], "", "none", "", "", $_GET["f"]);
} else { exit($exitmsg_wrongEmailNotificationPassword); }

