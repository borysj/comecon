<?php

$filePath = $settings['general']['subscribersDir'] . "/" . $what;
if (file_exists($filePath)) {
    $lines = file($filePath);
    if ($lines === false) {
        // This can happen if the file is unreadable.
        exit(1);
    }

    $foundUser = false;
    $userRemoved = false;
    $outputLines = [];

    foreach ($lines as $line) {
        // Check if the line is not empty and contains the separator to avoid errors with explode
        if (trim($line) !== '' && strpos($line, '<|>') !== false) {
            list($email, $password) = explode("<|>", $line);
            if ($email === $user) {
                $foundUser = true;
                if (trim($password) === $pw) {
                    // User found and password matches, so we skip adding this line to output, effectively deleting it.
                    $userRemoved = true;
                } else {
                    // User found but password doesn't match. Keep the line.
                    $outputLines[] = $line;
                }
            } else {
                // Not the user we are looking for, keep the line.
                $outputLines[] = $line;
            }
        }
    }

    if ($userRemoved) {
        file_put_contents($filePath, $outputLines);
        echo $user . EXITMSG_REMOVEDSUBSCRIBER . $what;
    } elseif ($foundUser) {
        // User was found, but password was wrong
        exit(EXITMSG_CANNOTREMOVESUBSCRIBER . $user);
    } else {
        // User was not found in the file
        exit(EXITMSG_EMAILNOTFOUND);
    }

} else {
    exit(EXITMSG_SUBSCRIBERLISTNOTFOUND);
}
// Delete the particular subscription file if this was the only email
if (file_exists($filePath) && filesize($filePath) === 0 && $what !== $settings['general']['subscribersFile']) {
    unlink($filePath);
}