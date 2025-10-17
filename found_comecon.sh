#!/bin/bash
set -e

if ! command -v composer &> /dev/null; then
    printf "\nPlease install composer first"
    exit 1
fi

if ! command -v php &> /dev/null; then
    if ! systemctl status php-fpm &> /dev/null; then
        printf "\nWarning: PHP might not be installed, or not running"
        printf "Remember that Comecon is PHP-based"
        printf "The deployment of Comecon will proceed"
    fi
fi

composer install --no-dev --optimize-autoloader

printf "\nPlease enter the web server directory"
printf "where the Comecon private data folders will be kept"
printf "Do NOT use the trailing slash."
read -p "[/var/www]: " serverDir
serverDir=${serverDir:-/var/www}
commentsDir="${serverDir}/comecon-data/comments"
mkdir -p $commentsDir

printf "\nPlease enter the website root directory"
printf "Do NOT use the trailing slash"
read -p "[/var/www/html]: " siteDir
siteDir=${siteDir:-/var/www/html}

printf "\nPlease enter the name of your website"
read -p "[My Great Blog]: " blogName
blogName=${blogName:-My Great Blog}

printf "\nPlease enter the URL of your website"
printf "Do NOT use the trailing slash"
read -p "[https://myblog.example.com] " siteURL
siteURL=${siteURL:-https://myblog.example.com}

printf "\nPlease enter the captcha question for the comment form"
printf "Yu cluod obsufcte it lke tis"
read -p "[Wht ws Churchill fsri nme?] " captchaQuestion
captchaQuestion=${captchaQuestion:-Wht ws Churchill fsrit nme?}

printf "\nPlease enter the captcha answer"
read -p "[Winston] " commentCaptcha
commentCaptcha=${commentCaptcha:-Winston}

s=private/settings.php
if command -v sha256sum &> /dev/null; then
    printf "\nPlease enter the admin password for editing comments"
    read -sp "" adminCommentPassword
    if [ -z "$adminCommentPassword" ]; then
        printf "\nWarning! You have not set the password."
        printf "I will continue, but you MUST enter the password hash"
        printf "manually into settings.php. Otherwise Comecon won't work"
    else
        hashedPassword=$(printf -n "$adminCommentPassword" | sha256sum | awk '{print $1}')
        sed -i "0,|adminCommmentPassword|s|CHANGEME|$hashedPassword|" $s
    fi
else
    printf "\nI would like to create a hashed password for editing comments,"
    printf "but I cannot find sha256sum in the system."
    printf "I will continue, but you MUST enter the password hash"
    printf "manually into settings.php. Otherwise Comecon won't work"
fi

printf "\nGenerating cookie key..."
cookieKey=$(cat /dev/urandom | tr -dc 'a-zA-Z0-9' | head -c 20)
printf $cookieKey

printf "\nModifying the essential settings..."
sed -i "0,|siteDir|s|\"\"|\"$siteDir\"|" $s
sed -i "0,|blogName|s|My Great Blog|$blogName|" $s
sed -i "0,|siteURL|s|https://myblog.example.com|$siteURL|" $s
sed -i "0,|commentCaptcha|s|correct_anser|$commentCaptcha|" $s
sed -i "0,|cookieKey|s|CHANGEME|$cookieKey|" $s

printf "\nPreparing the includes..."
sed -i "0,|// \$commentsDir|s|=|= $commentsDir|" includes/display_comments.php
sed -i "0,|Poor man\'s captcha|s|Esay but ofubcstaed quistoen?|$captchaQuestion|" includes/form-submit_comment.html

printf "\nRemoving examples from the commenters file..."
mv private/commenters.php private/commenters.bak
echo '<?php\n\n$commenters = [];' > private/commenters.php

printf "\nLinking comecon.php to your website root..."
ln -s comecon.php $siteDir/comecon.php

printf "\n==================================="
printf "All done! Comecon has been founded!"
printf "==================================="

printf "\nRemember that in the HTML of every blog post you will have to insert three elements:"
printf "1) A post identifier written as a PHP snippet"
printf "2) This code: includes/display_comments.php"
printf "3) This form: includes/form-submit_comment.html – which should contain the full title of the blog post"
printf "Also, your blog posts files must now have PHP extension, so index.php and the_new_blog.php"
printf "instead of index.html and the_new_blog.html."
printf "For more details, see README.md or look into the examples directory."

printf "Every time you regenerate your website, you will have to recreate the symbolic link:"
printf "ln -s /var/www/comecon/comecon.php /var/www/html/comecon.php"
printf "...or something like this, depending on which directories you use."

printf "\nThe comments and the comment form are unstyled, so they will look rather ugly."
printf "You will have to modify your CSS style sheet. Take a look at examples/styles.css"
printf "to see which classes you need."

printf "\nAlso, remember to register at least yourself and your grandma in private/commenters.php."
printf "Take a look at private/commenters.bak to see how to do it."

printf "\nFinally, remember that Comecon has several optional features and bonus scripts:"
printf "- master comment file"
printf "- comment feeds"
printf "- mail notifications"
printf "- search engine"
printf "- random post selector"
printf "- random quote generator"
printf "You have to activate them manually. Follow the instructions in README.md"
