<?php
$listOfPosts = file_get_contents("/home/johndoe/public_html/listings/allposts.txt");
if ($listOfPosts !== false) {
  $posts = explode("\n", $listOfPosts);
  $numberOfPosts = count($posts);
  $randomPostNumber = rand(0, $numberOfPosts - 1);
  $randomPostURL = trim($posts[$randomPostNumber]);
  header("Location: $randomPostURL");
}
