---
layout: null
---
<?php
require "{{ site.dir_with_data }}/settings.php";
require "email_sending.php";

if (($_SERVER["REQUEST_METHOD"] === "GET") &&
    (isset($_GET["p"])) && (hash("xxh3", $_GET["p"]) === $notificationPassword) &&
    (isset($_GET["y"])) &&
    (isset($_GET["m"])) &&
    (isset($_GET["d"])) &&
    (isset($_GET["t"])) &&
    (isset($_GET["f"]))) {
    sendNotifications($_GET["y"], $_GET["m"], $_GET["d"], $_GET["t"], "none", $_GET["f"]);
} else { exit("Are you trying to hack me?!"); }

