---
layout: null
---
<?php
require "{{ site.dir_with_data }}/settings.php";
include "messages.php";

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["user"]) && isset($_GET["pw"]) && isset($_GET["what"])) {
    $filePath = $subscribersDir . "/" . $_GET["what"];
        if (file_exists($filePath)) {
        $lines = file($filePath);
        $foundUser = false;
        foreach ($lines as $line) {
            if (strpos($line, $_GET["user"]) !== false) {
                $foundUser = true;
                list($email, $password) = explode("<|>", $line);
                if ($_GET["pw"] === trim($password)) {
                    $lines = str_replace($email . "<|>" . trim($password) . PHP_EOL, "", $lines);
                    $lines = str_replace($email . "<|>" . trim($password), "", $lines);
                    file_put_contents($filePath, $lines);
                    echo $_GET['user'] . $exitmsg_removedSubscriber . $_GET['what'] . "<br>";
                    break;
                } else { echo $exitmsg_cannotRemoveSubscriber . $_GET['user'] . "<br>"; }
            }
        } 
        if (!$foundUser) { echo $exitmsg_emailNotFound; }
    } else { echo $exitmsg_subscriberListNotFound; }
    if (filesize($filePath) === 0 && $_GET["what"] !== $subscribersFile) {
        unlink($filePath);
    }
} else { echo $exitmsg_errorRunningSubscriberScript; }
