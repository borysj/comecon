<?php
// Keys are registered nicknames.
// Values are arrays with four elements each:
// 1) Password in plain-text format
// 2) Nickname status: 1 for owner, 2 for eminent registered user, 3 for normal registered
// user
// 3) Webpage associated with the user (optional)
// 4) Email address if the user wants to automatically subscribe to new
// comments under each blog post which they have commented at least once
// (optional)

$vipNicks = [
    "JohnAuthor" => ["123love", 1, "https://mypage.com/blog", ""],
    "Jane Wife" => ["secret12", 2, "https://example.com/stuff", ""],
    "Max" => ["hal9000", 2, "", ""],
    "Katana8" => ["samurai", 3, "https://ilovejapan", "katana@ilovejapan.jp"],
    "Steven Smith" => ["somepassword", 3, "", "stevensmith2819@gmail.com"],
];
?>
