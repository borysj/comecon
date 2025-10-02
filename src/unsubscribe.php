---
layout: null
---
<?php
require "{{ site.dir_with_data }}/settings.php";
include $settings['general']['messages'];

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["user"]) && isset($_GET["pw"]) && isset($_GET["what"])) {
    $filePath = $settings['general']['subscribersDir'] . "/" . $_GET["what"];
        if (file_exists($filePath)) {
        $lines = file($filePath);
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
                    echo $_GET['user'] . $EXITMSG_REMOVEDSUBSCRIBER . $_GET['what'] . "<br>";
                    // We can break immediately, because it is not possible for
                    // the same email to be registered twice in the subscription
                    // file. This has been checked when the email was being added
                    break;
                } else { echo $EXITMSG_CANNOTREMOVESUBSCRIBER . $_GET['user'] . "<br>"; }
            }
        }
        if (!$foundUser) { echo $EXITMSG_EMAILNOTFOUND; }
    } else { echo $EXITMSG_SUBSCRIBERLISTNOTFOUND; }
    // Delete the subscription file if this was the only email
    if (filesize($filePath) === 0 && $_GET["what"] !== $settings['general']['subscribersFile']) {
        unlink($filePath);
    }
} else { echo $EXITMSG_ERRORRUNNINGSUBSCRIBERSCRIPT; }
