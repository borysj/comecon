---
layout: null
---
<?php
include "{{ site.dir_with_data }}/settings.php";
include "messages.php";
include "utilities.php";

$subsFilePath = $homeDir . $subscribersDir . "/" . $subscribersFile;

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["email"])) {

    $userEmail = prepareString($_POST["email"], 60, false, false, false);

    if (substr($userEmail, -3) !== "847") {
        exit($exitmsg_badCaptchaEmail); }
    else {
        $userEmail = substr($userEmail, 0, -3);
    }

    if (filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
        if (stripos(file_get_contents($subsFilePath), $userEmail) === false) {
            $password = mt_rand(1000000,9999999);
            file_put_contents($subsFilePath, $userEmail . "<|>" . $password . PHP_EOL, FILE_APPEND | LOCK_EX);
        }
    }
}
