<?php

$filePath = $settings['general']['subscribersDir'] . "/" . $what;
if (file_exists($filePath)) {
    $lines = file($filePath);
    // Be careful here, it might happen that the subscriber file is empty
    // (it is the subscription file for the entire blog, but there are no
    // subscribers yet). Thus, if (!$lines) would be erroneous.
    if ($lines === false) {
        exit(1);
    }
    $foundUser = false;
    foreach ($lines as $line) {
        if (strpos($line, $user) !== false) {
            $foundUser = true;
            list($email, $password) = explode("<|>", $line);
            if ($pw === trim($password)) {
                $lines = str_replace($email . "<|>" . trim($password) . PHP_EOL, "", $lines);
                // In case the user was in the last line
                $lines = str_replace($email . "<|>" . trim($password), "", $lines);
                file_put_contents($filePath, $lines);
                echo $user . EXITMSG_REMOVEDSUBSCRIBER . $what;
                // We can break immediately, because it is not possible for
                // the same email to be registered twice in the subscription
                // file. This has been checked when the email was being added
                break;
            } else {
                exit(EXITMSG_CANNOTREMOVESUBSCRIBER . $user);
            }
        }
    }
    if (!$foundUser) {
        exit(EXITMSG_EMAILNOTFOUND);
    }
} else {
    exit(EXITMSG_SUBSCRIBERLISTNOTFOUND);
}
// Delete the particular subscription file if this was the only email
if (file_exists($filePath) && filesize($filePath) === 0 && $what !== $settings['general']['subscribersFile']) {
    unlink($filePath);
}
