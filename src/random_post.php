<?php

include "../comecon/src/" . $settings['general']['messages'];

$listOfPosts = file_get_contents($settings['random']['listOfAllPosts']);
if ($listOfPosts !== false) {
    $posts = explode("\n", $listOfPosts);
    $numberOfPosts = count($posts);
    $randomPostNumber = rand(0, $numberOfPosts - 1);
    $randomPostURL = trim($posts[$randomPostNumber]);
    header("Location: $randomPostURL");
}
