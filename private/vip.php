<?php
// Keys are registered nicknames.
// Values are arrays with four elements each:
// 1) Password are hashed, generate hashes like that:
//    php -r "echo(hash('xxh3', 'ORIGINAL_PASSWORD'))";
// 2) Nickname status: 1 for owner, 2 for eminent registered user, 3 for normal registered
// user
// 3) Webpage associated with the user (optional)
// 4) Email address for gravatar and possibly subscribing to comments (optional)
// 5) Does the user want to email-subscribe to comments for any post he/she will comment under?
// 0 for no, 1 for yes
// (notice that the combination of empty email and 1 for subscribtion will not
// cause any trouble; of course, no email will be then attempted to be sent)

$vipNicks = [
    "JohnAuthor" => ["32bdkjkjakj25", 1, "https://mypage.com/blog", "", 0],
    "Jane Wife" => ["bkg5621ldslr35", 2, "https://example.com/stuff", "", 0],
    "Max" => ["29gjvmryd76s", 2, "", "", 0],
    "Katana8" => ["2kgj6kgur48", 3, "https://ilovejapan", "katana@ilovejapan.jp", 1],
    "Steven Smith" => ["ghti6783skflc", 3, "", "stevensmith2819@gmail.com", 0],
];
?>
