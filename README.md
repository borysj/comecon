# INTRODUCTION

Comecon is a commenting system for your (static) blog. It is written in vanilla
PHP meaning no frameworks and no JavaScript. PHP is employed to both save and
display comments.

Comecon is **not** a WordPress-plugin nor any other kind of plugin. It is a
collection of PHP scripts that you must set up and upload to your webpage server
following the instructions in [Deployment](#deployment). The entry point is
`comecon.php`.

Furthermore, Comecon does not use any database.  There is no MySQL involved.
The comments are written to and read from flat files (TXT with custom delimiter
<|>). This weird design decision is explained below in [FAQ](#faq-db).

Comecon has **not** been optimized for a large commenting traffic to the website.
It probably will stutter lot if you are a hot blogger receiving multiple
comment submissions simultaneously across your blog.

However, Comecon works **very** quickly and reliably if comments are reasonably
sparse. I guess you won't experience any problems unless there are more than one
comment per five seconds.  But that number is my intuition only; Comecon has
been tested thoroughly, but not stress-tested.

Comecon requires that you insert a permalink identifier into the HTML body of
every blog post, something like `/YYYY/MM/DD/post-title` or
`/this-is-a-blog-post.php`. How and why is explained below.

[Features](#features)  
[Deployment: Short version](#deployment)  
[Deployment: Some details](#basic-functionality-some-details)  
[Bonus scripts](#bonus-scripts)  
[Other language support](#other-language-support)  
[FAQ](#faq)  
[Possible improvements](#possible-improvements)  
[Contributing](#contributing)  
[Licence](#licence)

For an even more detailed description, see
[DETAILS.md](https://github.com/borysj/comecon/blob/main/DETAILS.md).

# Features

Comecon:

- adds comments to your blog posts through an HTML form;
- displays the comments under the blog post;
- lets the commenter attach a link to their nickname;
- lets the commenter edit or delete their comment within predefined time after
  submitting;
- lets the commenter register their nickname in order to simplify the comment
  submission and "upgrade" the color of the nick;
- is integrated with [Gravatar](https://gravatar.com);
- has a basic captcha to stop spam bots trying their luck in input fields;
- has an email notification system;
- updates comment feeds (in
  [Atom](https://en.wikipedia.org/wiki/Atom_(web_standard)) format; this is
  practically the same as RSS).

There are also three bonus scripts that have nothing to do with commenting, but
you might want them for your blog. At least I wanted them for mine:

- a simple internal search engine for the blog posts and the comments
  (hilariously crude, but very thorough and surprisingly fast);
- random post selector;
- random quote generator.

# Deployment

These are instructions for deploying the core part of Comecon. 

## Basic functionality: Short version

1. Clone or unzip Comecon into a non-public directory of your website.
2. Run `compose installer` to install the
   [PHPMailer](https://github.com/PHPMailer/PHPMailer) dependency.
3. Fill out `private/settings.php` and `private/commenters.php`.
4. Create the directory that `commentsDir` from the settings points to.
5. Add the same directory manually to the top part of
   `includes/display_comments.html`.
6. Set the captcha question in the HTML form
   `includes/form-submit_comment.html`.
7. In the HTML of every blog post (that you want to connect to Comecon), you
   have to include:
   - a PHP snippet with the post identifier: `<?php $postID="post_identifier" ?>`
     (the post identifier can contain only ASCII letters (both cases), numbers,
     underscores, dashes)
   - the PHP script for displaying the comments: `includes/display_comments.php`
   - the HTML form for submitting a comment: `includes/form-submit_comment.html`
     which contains a hidden field for the full title of the blog post being
     commented on
8. The submission form is unstyled, so you might want to add some new classes to
   your CSS. See `examples/styles.css`.
9. If you self-host, link `comecon.php` to the root of your website, like this:
   `ln -s /var/www/comecon/comecon.php /var/www/html/comecon.php`. If you are on
   shared hosting, move `comecon.php` to the root of your website, and adjust
   all file paths from requires and includes.

Assuming that your WWW server can process PHP, you are now ready to go.

## Basic functionality: Some details

1. The non-public directory should be something like `/var/www/comecon` if you
   self-host, or something like `/home/username/comecon` on shared hosting. If
   your are on shared hosting and can upload your files to the public HTML directory only,
   check FAQ. 
2. On shared hosting you won't be able to install anything with composer. You
   have to download Comecon first to your local machine, then run `composer install`,
   and then upload the Comecon folder to your server with the dependency already
   inside.
3. To begin with, in `private/settings.php` you must fill out only the settings
   marked as essential. In `private/commenters.php`, you should remove the example
   users, and add at least yourself and your grandmother.
4. As above.
5. You have to enter the comment directory manually, because this PHP script
   will be a part of every blog post, and the posts might be spread around in
   your directory hierarchy. I think it is easiest to enter it manually once and
   for all instead of tracing the relative position of `settings.php`.
6. As above.
7. A few remarks:
   - If you are unsure how to insert these three elements, take a look at
     `examples/blog_post_plain.php`.
   - The ordering of the PHP snippet with the post identifier, the PHP script
     and the comment form is important
   - The idea is that you include the snippet, the script and the form using a
     static blog generator like Jekyll (check `examples/blog_post_jekyll.html`).
     However, they can of course be entered manually, or semi-manually with a
     template. It all depends on how you run your non-WordPress blog.
8. As above.
9. If you are on shared hosting, you cannot create a symbolic link to the main
   script from the website root directory. As this script must be public, you
   will have to move it into the website root (there where you hold `index.html`
   or `index.php` for your website). However, then the main script will get
   confused, and will look for other (private) Comecon scripts in wrong places.
   You have to manually adjust the file paths after **all** the require- and
   include-statements. For instance, you will have to change `require_once
   __DIR__ . "/private/settings.php";` to something like `require_once __DIR__ .
   "../comecon/private/settings.php";`. Here `..` means to go up one directory
   level (out of the website root) and then descend into the (private) Comecon
   directory.

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

**You have to create this file yourself (an empty file with the correct name in
the chosen directory).**

No worries, Comecon will remember to edit the master comment file as well when someone
edit their comment. But the comments to be displayed will be always read from
the particular comment files.

## Feeds

Comecon can update Atom feeds on your blog site every time someone leaves a
comment. There are two kinds of feeds: The master feed containing the newest
comments only, and feeds with particular comments for separate blog posts. To
turn these functions on, adjust the `feed` category in the settings, and create
a directory for the feeds. Obviously, the directory must be public.

Comecon **does not** create the feed files, it only updates them. In `examples`
you will find the template for the master feed, and the template for a
particular feed.

In the master feed, only the 10 newest comments will be retained. This number is
hard-coded, but you can change it easily inside the function `updateFeed` in
`src/utilities.php`.

**To prepare the master feed**, simply copy the master feed file into the feed
directory. Then share the link to the master feed with your readers.

**To prepare particular feeds**, you have to create a new particular feed file
for every new blog post. Give it the correct name
(`comments-YYYYMMDD-post-title.xml`) and move it into the feed directory. Also,
remember to add the link to the particular feed within every blog post.
Otherwise, no one will know about it. :-)

## Mail notifications: New blog posts

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

## Mail notifications: New comments

If you suspect that some of your readers are unfamiliar with RSS/Atom, you may
want to turn on email notifications about new comments (per blog post).

1. Edit the `email` category in the settings. Here, you have to connect your
   email account to Comecon. `notify` gets `true`.
2. Create the directory that the `subscribersDir` setting points to.
3. Uncomment the label for `email2` in `includes/form-submit_comment.html`.

Now, if someone subscribes to a blog post by email, a subscriber file with their
email address will be created. If someone published a new comment under this
blog post, a notification will be sent to every subscriber.

## Register new users

You have to manually add new commenters to `private/commenters.php`. You can encourage
your readers to send you a registration mail with the necessary information
(nickname, password, website, email, whether they want to receive comment
notifications by email).

# Bonus scripts

The scripts below have nothing to do with commenting, but might be useful for a
static blog.

## Search

You can use `misc/search.php` as a crude PHP-only search engine for your blog.
It asks the server to look ("grep") through all your post and comment files for
a given phrase. Sounds primitive and slow. Surprisingly enough, it works better
and faster than the standard WordPress search engine. At least for a website
where the traffic is reasonably small. 

The search phrase comes from `misc/form-search.html` that you have to insert
into your blog website. You must upload the search script to the website root.
If you upload it somewhere else, you will have to edit the script link in the
search form.

The search script will sanitize the search phrase and look for it in the search
data directory as defined in `private/settings.php`. 

What is the search data directory? It consists of a "data" file, one such file
for each of your blog posts. These are simply blog posts without any HTML or
Markdown tags. If (some of) the tags are still around, it will deteriorate the
search results because e.g. `**Tolkien** is best` will not give match for
`Tolkien is best`-search phrase.

If you are familiar with `sed`, you can use `misc/clean_blogpost.sed`
to remove Markdown tags:  
`sed -f clean_blogpost.sed blogpost.md > blogpost.txt`

Additionally, you should convert paragraphs into single lines if your
editor is doing hard wraps. It could be done with `tr`:
`tr "\n" " " < blogpost.txt`

...but I recommend a cryptic sed command:
`sed ':a;N;$!ba;s|\n\([^\n]\)| \1|g' blogpost.md > blogpost.txt`

It is superior to `tr`, because `tr` while mash the entire post into a single
line. The above sed command will respect blank lines which are normally used to
divide paragraphs in Markdown.

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

# Other language support

The text strings displayed by the PHP part of Comecon to the user are all
gathered as constants in `src/messages_en.php`. A few more strings are in
`src/email_sending.php`.

If you want to translate Comecon to your language you must:

- translate `messages_en.php` to `messages_xx.php` (the messages beginning with
  MSG and LABEL are the most important one; EXITMSG are the error messages);
- change the two language settings in `settings.php`;
- translate all the strings in `src/email_sending.php`;
- translate all the forms you use (first and foremost
  `includes/form-submit_comment.html).

Notice that the Polish translation is already provided in `lang/pl`.

# FAQ

**Why Comecon?**

<a id="faq-db"></a>
**Why no database?**

**Vibe-coded?**

**Requirements?**

**Shared hosting, everything must be public**

**Tests**

**Complicated deployment?**

**Hidden fields?**

**Full title**

**Sed? Manual feed updates?**



# Possible improvements

* captcha
* better search
* `commenters.php` is currently updated manually by the webmaster. However, it could be
done through a form and with a script.
* `save_comment.php` should probably be refactored. There is some redundancy
between it and `add_subscriber.php`. Also, `edit_comment.php` contains
effectively the same function for converting Markdown to HTML as
`save_comment.php`.
* My inutition tells me that `email_notification.php` is a bad way of sending
notifications about new blog posts, since the activation URL is accessible to
anyone. On the other hand, the whole idea is to make the notification system
simple; and as long as it is protected by password, it should work fine... I
guess.
* Captcha consists of one question only, so it is very easy to flood Comecon
with fake comments if someone is up for pranks. A more diverse captcha should be
designed or integrated into the system.

# Contributing


# Licence

CC BY-NC-SA 2024, Borys Jagielski
https://blogrys.pl
