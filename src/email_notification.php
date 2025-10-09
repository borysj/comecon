<?php

require_once __DIR__ . "/private/settings.php";

// This is a script to notify the blog subscribers about a new post by email.
// Activate it like that:
// https://myblog.example.com/comecon.php?action=notify&p=secretpassword&y=2025&m=08&d=12&t=about-birds&f=About%20birds
// p is the admin password.
// y, m, d is the date in the format yyyy-mm-dd
// t is the slugified title of the new blog post
// f is the full title of the new blog post (use %20 for spaces and so on).
sendNotifications($y, $m, $d, $t, null, null, null, null, $f, $settings['general'], $settings['email']);
