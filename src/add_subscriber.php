---
layout: null
---
<?php
include "{{ site.dir_with_data }}/settings.php";
include "messages.php";

$subsFilePath = $homeDir . $subscribersDir . "/" . $subscribersFile;

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["email"])) {

    $userEmail = $_POST["email"];
    $userEmail = htmlspecialchars($userEmail, ENT_QUOTES);
    $userEmail = trim($userEmail);
    $userEmail = substr($userEmail, 0, 60);
    $userEmail = str_replace(array("\r\n", "\r", "\n"), "", $userEmail);

    if (substr($userEmail, -3) !== "847") {
        exit($exitmsg_badCaptchaEmail); }
    else {
        $userEmail = substr($userEmail, 0, -3);
    }

    if (filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
        if (stripos(file_get_contents($subsFilePath), $userEmail) === false) {
            $password = mt_rand(1000000,9999999);
            file_put_contents($subsFilePath, $userEmail . "<|>" . $password . PHP_EOL, FILE_APPEND | LOCK_EX)
        }
    }
}
