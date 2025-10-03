---
layout: null
---
<?php
include "{{ site.dir_with_data }}/settings.php";

$listOfPosts = file_get_contents($settings['random']['listOfAllPosts']);
if ($listOfPosts !== false) {
    $posts = explode("\n", $listOfPosts);
    $numberOfPosts = count($posts);
    $randomPostNumber = rand(0, $numberOfPosts - 1);
    $randomPostURL = trim($posts[$randomPostNumber]);
    header("Location: $randomPostURL");
}
