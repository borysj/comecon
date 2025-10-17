#!/bin/bash
set -e

if ! command -v composer &> /dev/null; then
    echo "Please install composer first"
    exit 1
fi

if ! command -v php &> /dev/null; then
    if ! systemctl status php-fpm &> /dev/null; then
        echo "Warning: PHP might not be installed, or not running"
        echo "Remember that Comecon is PHP-based"
        echo "The deployment of Comecon will proceed"
    fi
fi

composer install --no-dev --optimize-autoloader

echo "Please enter the web server directory"
echo "where the Comecon private data folders will be kept"
echo "Do NOT use the trailing slash."
read -p "[/var/www]: " serverDir
serverDir=${serverDir:-/var/www}
commentsDir="${serverDir}/comecon-data/comments"
mkdir -p $commentsDir

echo "Please enter the website root directory"
echo "Do NOT use the trailing slash"
read -p "[/var/www/html]: " siteDir
siteDir=${siteDir:-/var/www/html}

echo "Please enter the name of your website"
read -p "[My Great Blog]: " blogName
blogName=${blogName:-My Great Blog}

echo "Please enter the URL of your website"
echo "Do NOT use the trailing slash"
read -p "[https://myblog.example.com] " siteURL
siteURL=${siteURL:-https://myblog.example.com}

echo "Please enter the captcha question for the comment form"
echo "Yu cluod obsufcte it lke tis"
read -p "[Wht ws Churchill fsri nme?] " captchaQuestion
captchaQuestion=${captchaQuestion:-Wht ws Churchill\' fsri nme?}

echo "Please enter the captcha answer"
read -p "[Winston] " commentCaptcha
commentCaptcha=${commentCaptcha:-Winston}

echo "Generating cookie key..."
cookieKey=$(cat /dev/urandom | tr -dc 'a-zA-Z0-9' | head -c 20)
echo $cookieKey

echo "Modifying the essential settings..."
$s=private/settings.php
sed -i "0,|siteDir|s|\"\"|\"$siteDir\"|" $s
sed -i "0,|blogName|s|My Great Blog|$blogName|" $s
sed -i "0,|siteURL|s|https://myblog.example.com|$siteURL|" $s
sed -i "0,|commentCaptcha|s|correct_anser|$commentCaptcha|" $s
sed -i "0,|cookieKey|s|\"CHANGEME\"|\"$cookieKey\"|" $s

echo "Preparing the includes..."
sed -i "0,|// \$commentsDir|s|=|= $commentsDir|" includes/display_comments.php
sed -i "0,|Poor man's captcha|s|Esay but ofubcstaed quistoen?|$captchaQuestion|" includes/form-submit_comment.html

echo "Removing examples from the commenters file..."
mv private/commenters.php private/commenters.bak
echo '<?php\n\n$commenters = [];' > private/commenters.php

echo "Linking comecon.php to your website root..."
ln -s comecon.php $siteDir/comecon.php

echo "==================================="
echo "\nAll done! Comecon has been founded!"
echo "==================================="

echo "\nRemember that in the HTML of every blog post you will have to insert three elements:"
echo "1) A post identifier written as a PHP snippet"
echo "2) This code: includes/display_comments.php"
echo "3) This form: includes/form-submit_comment.html – which should contain the full title of the blog post"
echo "Also, your blog posts files must now have PHP extension, so index.php and the_new_blog.php"
echo "instead of index.html and the_new_blog.html."
echo "For more details, see README.md or look into the examples directory."

echo "Every time you regenerate your website, you will have to recreate the symbolic link:"
echo "ln -s /var/www/comecon/comecon.php /var/www/html/comecon.php"
echo "...or something like this, depending on which directories you use."

echo "\nThe comments and the comment form are unstyled, so they will look rather ugly."
echo "You will have to modify your CSS style sheet. Take a look at examples/styles.css"
echo "to see which classes you need."

echo "\nAlso, remember to register at least yourself and your grandma in private/commenters.php."
echo "Take a look at private/commenters.bak to see how to do it."

echo "\nFinally, remember that Comecon has several optional features and bonus scripts:"
echo "- master comment file"
echo "- comment feeds"
echo "- mail notifications"
echo "- search engine"
echo "- random post selector"
echo "- random quote generator"
echo "You have to activate them manually. Follow the instructions in README.md"
