# INTRODUCTION

Comecon is a commenting system for a static blog (or website). It is written in
vanilla PHP meaning no frameworks, no JavaScript, and no Docker. PHP is employed
to save comments and to display them as a part of a blog post (a webpage).

Comecon is **not** a WordPress-plugin nor any other kind of plugin. It is a
collection of PHP scripts that you must set up and upload to your webpage server
following the instructions in [Deployment](#deployment). The entry point is
`comecon.php`.

Furthermore, Comecon does not use any binary database. There is no SQL involved.
The comments are written to and read from flat text files. This <s>weird</s>
conservative design decision is explained below in [FAQ](#faq-db).

Comecon has **not** been optimized for a large commenting traffic.  It probably
will stutter if you are a hot blogger receiving multiple comment submissions
simultaneously across your blog.

However, Comecon works **very** quickly and reliably if comments are reasonably
sparse. I guess you won't experience any problems unless there are more than one
comment per ten seconds.  But that number is my intuition only; Comecon has been
tested thoroughly, but not stress-tested.

Comecon requires that you insert an identifier into the HTML body of every blog
post, something like `YYYY-MM-DD-post-title` or `some-simple-title` or `241`.
How and why is explained below.

[Features](#features)  
[Deployment](#deployment)  
:: [Short version](#short-version)  
:: [Some details](#some-details)  
[Admin access to comments](#admin-access-to-comments)
[Optional features](#optional-features)
:: [Master comment file](#master-comment-file)
:: [Feeds](#feeds)
:: [Commenter registration](#commenter-registration)
:: [Mail notifications](#mail-notifications)
:: :: [New blog posts](#new-blog-posts)
:: :: [New comments](#new-comments)
[Bonus scripts](#bonus-scripts)  
:: [Search](#search)
:: [Random post selector](#random-post-selector)
:: [Random quote generator](#random-quote-generator)
[Language support](#language-support)  
[FAQ](#faq)  
[Possible improvements](#possible-improvements)  
[Contributing](#contributing)  
[Acknowledgments](#acknowledgments)
[Licence](#licence)

# Features

Comecon:

- adds comments to your blog posts through an HTML form;
- displays the comments under the blog post;
- lets the commenter attach a link to their nickname;
- lets the commenter edit or delete their comment within a time limit;
- lets the commenter register their nickname in order to simplify the comment
  submission and "upgrade" the color of the nick;
- lets the admin edit or delete any comment through the browser;
- is integrated with [Gravatar](https://gravatar.com);
- has a basic captcha and some lightweight barriers to stop spam bots trying
  their luck in input fields;
- has an email notification system;
- updates comment feeds (in
  [Atom](https://en.wikipedia.org/wiki/Atom_(web_standard)) format; this is
  practically the same as RSS).

There are also three bonus scripts that have nothing to do with commenting, but
you might want them for your blog. At least I wanted them for mine:

- a simple internal search engine for the blog posts and the comments
  (hilariously crude, but very thorough and surprisingly fast);
- a random post selector;
- a random quote generator.

# Deployment

These are instructions for deploying the core part of Comecon.

If you are self-hosting, clone the repo and run `found_comecon.sh`. If you want
use `var/html` and `var/html/www`, you should run the script as superuser
because of the access rights.

The deployment script will do points 2-6 and 9 from the list below.

You can also follow the deployment steps manually; you must do it if you are on
shared hosting. In this case, see also [Some details](#some-details) for more
information.

## Short version

1. Clone or unzip Comecon into a directory outside of your website.
2. Run `compose installer` to install the
   [PHPMailer](https://github.com/PHPMailer/PHPMailer) dependency.
3. Fill out `private/settings.php` and `private/commenters.php`.
4. Create the directory that `commentsDir` from the settings points to.
5. Add the same directory manually to the top part of
   `inserts/display_comments.html`.
6. Set the captcha question in the HTML form
   `inserts/form-submit_comment.html`.
7. Into the HTML of every blog post or webpage that you want to connect to
   Comecon, you must insert:
   - a PHP snippet with an unique identifier: `<?php
     $postID="here_goes_identifier" ?>` (the post identifier can contain only
     ASCII letters (both cases), numbers, underscores, dashes; it must not be
     longer than 100 characters);
   - the PHP script for displaying the comments: `inserts/display_comments.php`;
   - the HTML form for submitting a comment: `inserts/form-submit_comment.html`,
     with a hidden field for the full title of the blog post or webpage being
     commented on.
8. The submission form is unstyled, so you might want to add relevant classes to
   your CSS. See `examples/styles.css`.
9. If you self-host, link `comecon.php` to the root of your website, like this:
   `ln -s /var/www/comecon/comecon.php /var/www/html/comecon.php`. If you are on
   shared hosting, move `comecon.php` to the root of your website, and adjust
   all file paths for requires and includes in this script file.

Assuming that your server can process PHP, you are now ready to go. Remember
that your commentable webpages must be now PHP files, since they contain PHP snippets!
Thus, you have to give up the nice old-fashioned file extension `.html`.

## Some details

1. The directory should be something like `/var/www/comecon` if you self-host,
   or something like `/home/username/comecon` on shared hosting. If your are on
   shared hosting and can upload your files to the public HTML directory only,
   check FAQ. 
2. On shared hosting you won't be able to run composer. You have to download
   Comecon first to your local machine, run `composer install`, and then upload
   the Comecon folder to your server with the dependency already inside.
3. To begin with, in `private/settings.php` you must fill out the settings
   marked as ESSENTIAL. Otherwise Comecon won't work at all or will be insecure
   to use. Eventually, you should also fill out the IMPORTANT settings. In
   `private/commenters.php`, you should remove the example users, and add at
   least yourself and your grandmother.
4. Nothing to add.
5. You have to enter the comment directory manually, because this PHP script
   will be a part of every blog post and webpage, and as such will be spread
   around in your directory structure. It is easiest to enter it manually once
   and for all. Tracing the relative position of `settings.php` would be more
   troublesome.
6. Nothing to add.
7. A few remarks:
   - If you are unsure how to insert these three elements, take a look at
     `examples/blog_post_full.php`.
   - The ordering of the PHP snippet with the post identifier, the PHP script
     and the comment form is important.
   - The idea is that you include the snippet, the script and the form using a
     static blog generator like Jekyll (check `examples/blog_post_jekyll.html`).
     However, they can of course be entered manually, or semi-manually with a
     template. It all depends on how you run your non-WordPress blog.
8. Nothing to add.
9. If you are on shared hosting, you cannot create a symbolic link to the main
   script in the website root. As this script must be public, you will have to
   move it into the website root (there where you hold `index.html` or
   `index.php`). However, then the main script look for other (non-public)
   Comecon scripts in wrong places.  Therefore, in this scenario, you have to
   manually patch the file paths after **all** the require- and
   include-statements in `comecon.php`. For instance, you will have to change
   `require_once __DIR__ . "/private/settings.php";` to something like
   `require_once __DIR__ .  "../comecon/private/settings.php";`. Here `..` means
   to go up one directory level (out of the website root) and then descend into
   the (non-public) Comecon directory.

# Admin access to comments

# Optional features

## Master comment file

The comments will be saved in text sidefiles. Every blog gets its own
comment file on the arrival of the first comment (so there will be no empty comment files
spamming your server). However, you may also wish, in addition, to save each
comment into a single master file. Why? I had two reasons:

- I wanted to have a kind of backup.
- I wanted the master comment file to be available in the public directory,
  because upon every blog update, I had a script that counted the total number
  of comments and updated this information in a post listing of my blog.

If you wish to activate the master comment file, set the `allComments` setting
to `true`, and possibly change the filepath under `allCommentsFile`. The
default location is the public root, but you can put the file wherever you wish.

**You have to create this (empty) file yourself.**

No worries, Comecon will remember to change the master comment file when someone
edit their comment. But the comments to be displayed will be always read from
the specific comment files. Also, only the specific comment files will save the
(hashes of) the commenters' emails. In the master comment file this field will
always be left blank (since it might be publicly available).

## Feeds

Comecon can update Atom feeds on your blog site every time someone leaves a
comment. We speak here about two kinds of feeds: The master feed containing the
newest comments, and specific feeds with comments for separate blog posts. To turn
on one or both of these feed types, adjust the `feed` category in the settings,
and create a directory for the feeds. Obviously, the directory must be public.

Comecon **does not** create the feed files, it only updates them. In `examples`
you will find the template for the master feed, and the template for a
specific feed.

**To prepare the master feed**, simply copy the master feed file
`newest-comments.xml` into the feed directory. Adjust all parameters marked with
"Change:". Then share the link to the master feed with your readers.

**To prepare specific feeds**, you will have to create a new feed file for every
new blog post yourself. First, you should adjust the general template (look for
parameters marked with "Change:"). Then, you will have to replace "PLACEHOLDERS"
when you create a specific feed (the title of the specific blog post, its URL,
the URL of the feed file itself, the date). Call the feed file for
`comments-postID.xml` where `postID` is the blog post identifier. Finally, move
the feed file into the feed directory.

## Commenter registration

You have to manually add new commenters to `private/commenters.php`. You can encourage
your readers to send you a registration mail with the necessary information
(nickname, password, website, email, whether they want to receive comment
notifications by email).

## Mail notifications

### New blog posts

If you suspect that some of your readers are unfamiliar with RSS/Atom, you may
want to turn on email notifications about new blog posts.

1. Edit the `email` category in the settings. Here, you have to connect your
   email account to Comecon. `notify` stays `false`; that one is about about
   sending the comment notifications, not the blog posts notifications.
2. Create the directory that the `subscribersDir` setting points to.
3. Make `misc/form-blog_subscription.html` available for you readers. The form
   mentions captcha; you choose it in the `email` category of the settings. You
   have to inform them somehow about "the secret code".
4. Every time you publish a new blog post, you have to run
   `comecon.php?action=notify` manually. See the instruction in
   `src/email_notifications.php`.

The subscribers will receive an email notification about each new blog post you
publish. There will be an unsubscribe link in the email.

### New comments

If you suspect that some of your readers are unfamiliar with RSS/Atom, you may
want to turn on email notifications about new comments (per blog post).

1. Edit the `email` category in the settings. Here, you have to connect your
   email account to Comecon. `notify` gets `true`.
2. Create the directory that the `subscribersDir` setting points to.
3. Uncomment the label for `email2` in `inserts/form-submit_comment.html`.

Now, if someone subscribes to a blog post by email, a subscriber file with their
email address will be created. If someone published a new comment under this
blog post, a notification will be sent to every subscriber.

# Bonus scripts

The scripts below have nothing to do with commenting, but might be useful for a
static blog.

## Search

You can use `misc/search.php` as a crude PHP-only search engine for your blog.
It asks the server to look ("grep") through all your post and comment files for
a given phrase. Sounds primitive and slow. Surprisingly enough, it works better
and faster than the standard WordPress search engine; at least for a website
where the traffic is reasonably small. 

You must upload the search script to the website root. The search phrase comes
from `misc/form-search.html` that you have to insert into your blog website.

If you upload the search script somewhere else than the website root, you will
have to edit the script link in the search form.

The search script will sanitize the search phrase and look for it in the
searchable post directory as defined in `private/settings.php`. 

The searchable post directory consists of your blog posts in plain TXT, meaning
no Markdown and no HTML.  If the tags were still around, it would deteriorate
the search results because e.g. `I believe **Tolkien** is best` would not give
match for the `I believe Tolkien is best`-search phrase.

Thus, if you want to implement the search in your blog, you have to put a plain
copy of each blog post in the searchable post directory. If you are familiar
with `sed`, you can use `misc/clean_blogpost.sed` to remove Markdown tags:  
`sed -f clean_blogpost.sed blogpost.md > blogpost.txt`

**The first line of each plain post copy must be the post URL**. Otherwise, the
search engine cannot know where the real versions of these posts are, and cannot
create a link if a phrase is found.

The search script will also look for the search phrase in all comment files
within the standard comments directory (defined in settings).

Notice that the phrase will be saved to the search queries record (again,
settings) unless the phrase starts with `123`. this simple "password" can be
easily changed inside the search script. The rationale is that the site owner
might want to use the search engine without necessarily saving the query. The
idea behind saving the phrases is, after all, to provide a record of what the
**visitors** are looking for.

## Random post selector

The script `misc/random_post.php` redirects to a random blog post. Take a look
at the first line of the script.  Change it to where the text file with all your
blog post addresses are (one URL per line). Normally, that text file would be
updated using the static site generator.

## Random quote generator

Displays a random quote from a file. See the description inside the script.

# Language support

The text strings displayed by the PHP part of Comecon to the user are all
gathered as constants in `src/messages_en.php`. A few more strings are in
`src/email_sending.php`.

If you want to translate Comecon to your language you must:

- translate `messages_en.php` to `messages_xx.php` (the messages beginning with
  MSG and LABEL are the most important one; EXITMSG are the error messages);
- change the two language settings in `settings.php`;
- translate all the relevant strings in `inserts/display_comments.php`;
- translate all the forms you use (first and foremost
  `inserts/form-submit_comment.html);
- translate all the relevant strings in `src/email_sending.php`.

Notice that the Polish translation is already provided in `lang/pl`. Just
replace the English files with the Polish files. They only differ in their
strings.

# FAQ

**Why Comecon?**

<a id="faq-db"></a>
**Why no database?**

**Vibe-coded?**

**Requirements? Only blogs?**

**Shared hosting, everything must be public**

**Tests**

**Complicated deployment?**

**Hidden fields?**

**Full title**

**Sed? Manual feed updates?**

**Name? Ussr?**

# Possible improvements

* captcha
* better search with captcha
* choice between database or flat files
* real tests
* `commenters.php` is currently updated manually by the webmaster. However, it could be
done through a form and with a script.
* `ussr.php` should probably be refactored. There is some redundancy
between it and `add_subscriber.php`. Also, `edit_comment.php` contains
effectively the same function for converting Markdown to HTML as
`ussr.php`.
* My inutition tells me that `email_notification.php` is a bad way of sending
notifications about new blog posts, since the activation URL is accessible to
anyone. On the other hand, the whole idea is to make the notification system
simple; and as long as it is protected by password, it should work fine... I
guess.
* Captcha consists of one question only, so it is very easy to flood Comecon
with fake comments if someone is up for pranks. A more diverse captcha should be
designed or integrated into the system.

# Contributing

# Acknowledgments

# Licence

CC BY-NC-SA 2024, Borys Jagielski
https://blogrys.pl
