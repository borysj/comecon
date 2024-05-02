<?php
// Keys are registered nicknames.
// Values are arrays with four elements each:
// 1) Password are hashed, generate hashes like that:
//    php -r "echo(hash('xxh3', 'ORIGINAL_PASSWORD'))";
// 2) Nickname status: 1 for owner, 2 for eminent registered user, 3 for normal registered
// user
// 3) Webpage associated with the user (optional)
// 4) Email address if the user wants to automatically subscribe to new
// comments under each blog post which they have commented at least once
// (optional)

$vipNicks = [
    "JohnAuthor" => ["32bdkjkjakj25", 1, "https://mypage.com/blog", ""],
    "Jane Wife" => ["bkg5621ldslr35", 2, "https://example.com/stuff", ""],
    "Max" => ["29gjvmryd76s", 2, "", ""],
    "Katana8" => ["2kgj6kgur48", 3, "https://ilovejapan", "katana@ilovejapan.jp"],
    "Steven Smith" => ["ghti6783skflc", 3, "", "stevensmith2819@gmail.com"],
];
?>
