<?php

require_once __DIR__ . "/private/settings.php";

// This is a script to notify the blog subscribers about a new post by email.
// First, you have to follow the activation instruction in README.md.
// Then you can run the script like that:
// https://myblog.example.com/comecon.php?action=notify&p=secretpassword&id=20230507-about-birds&f=About%20birds&u=https%3A%2F%2Fmyblog.example.com%2F2025%2Fabout-birds.php
// p is the admin password from the "email" category in settings.php
// id is the post identifier
// f is the URL-encoded full title of the new blog post (use %20 or + for spaces and so on).
// u is the URL-encoded URL of the new blog post
sendNotifications($id, $f, $u, null, null, null, null, $settings['general'], $settings['email']);
