---
layout: null
---
<?php
require "{{ site.dir_with_data }}/settings.php";
include $settings['general']['messages'];

if ($_SERVER["REQUEST_METHOD"] !== "GET" || !isset($_GET["user"]) || !isset($_GET["pw"]) || !isset($_GET["what"])) {
    exit(EXITMSG_ERRORRUNNINGSUBSCRIBERSCRIPT);
}

$filePath = $settings['general']['subscribersDir'] . "/" . $_GET["what"];
if (file_exists($filePath)) {
    $lines = file($filePath);
    // Be careful here, it might happen that the subscriber file is empty
    // (it is the subscription file for the entire blog, but there are no
    // subscribers yet). Thus, if (!$lines) would be erroneous.
    if ($lines === false) { exit(1); }
    $foundUser = false;
    foreach ($lines as $line) {
        if (strpos($line, $_GET["user"]) !== false) {
            $foundUser = true;
            list($email, $password) = explode("<|>", $line);
            if ($_GET["pw"] === trim($password)) {
                $lines = str_replace($email . "<|>" . trim($password) . PHP_EOL, "", $lines);
                // In case the user was in the last line
                $lines = str_replace($email . "<|>" . trim($password), "", $lines);
                file_put_contents($filePath, $lines);
                echo $_GET['user'] . EXITMSG_REMOVEDSUBSCRIBER . $_GET['what'];
                // We can break immediately, because it is not possible for
                // the same email to be registered twice in the subscription
                // file. This has been checked when the email was being added
                break;
            } else { exit(EXITMSG_CANNOTREMOVESUBSCRIBER . $_GET['user']); }
        }
    }
    if (!$foundUser) { exit(EXITMSG_EMAILNOTFOUND); }
} else { exit(EXITMSG_SUBSCRIBERLISTNOTFOUND); }
// Delete the particular subscription file if this was the only email
if (filesize($filePath) === 0 && $_GET["what"] !== $settings['general']['subscribersFile']) {
    unlink($filePath);
}
