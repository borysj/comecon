<?php

$listOfPosts = file_get_contents("../www/allposts.txt");
if ($listOfPosts !== false) {
    $posts = explode("\n", $listOfPosts);
    $numberOfPosts = count($posts);
    $randomPostNumber = rand(0, $numberOfPosts - 1);
    $randomPostURL = trim($posts[$randomPostNumber]);
    header("Location: $randomPostURL");
}
