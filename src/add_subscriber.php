---
layout: null
---
<?php
include "{{ site.dir_with_data }}/settings.php";
include $settings['general']['messages'];
include "utilities.php";

$subsFilePath = $settings['general']['subscribersDir'] . "/" . $settings['general']['subscribersFile'];

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST["email"])) { exit(1); }

$userEmail = $_POST["email"];
if (!is_string($userEmail)) { exit(1); }
$userEmail = prepareString($userEmail, 60, false, false, false);

// Check for captcha, and remove it if present.
// The captcha is a three characters long "secret code" at the end of the
// email, therefore -3.
if (substr($userEmail, -3) !== $settings['email']['captchaEmail']) { exit(EXITMSG_BADCAPTCHAEMAIL); }
else { $userEmail = substr($userEmail, 0, -3); }

// If the email is not yet present in the subscribers file,
// add it together with a random numeric password.
// This password will be necessary to remove the subscriber from the file,
// and it will be provided in the link to cancel the subscription.
if (filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
    $fileContents = file_get_contents($subsFilePath);
    if (!$fileContents) { exit(1); }
    if (stripos($fileContents, $userEmail) === false) {
        $password = mt_rand(1000000,9999999);
        file_put_contents($subsFilePath, $userEmail . "<|>" . $password . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}
