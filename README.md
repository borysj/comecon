# INTRODUCTION

Comecon is a commenting system for your (static) blog. It is written in vanilla
PHP meaning no frameworks and no JavaScript. PHP is employed to both save and
display comments.

Comecon is **not** a WordPress-plugin nor any other kind of plugin. It is a
collection of PHP scripts that you must set up and upload to your webpage server
following the instructions in DEPLOYMENT. The entry point is `comecon.php`.

Furthermore, Comecon does not use any database.  There is no MySQL involved.
The comments are written to and read from flat files (TXT with custom delimiter
<|>). This weird design decision is explained below in FAQ.

Comecon has not been optimized for a large (commenting) traffic to the website.
It probably will stuttering lot if you are a hot blogger receiving multiple
comment submissions simultaneously across your blog. However, Comecon works
*very* quickly and reliably if comments are reasonably sparse. I guess you won't
experience any problems unless there are more than one comment per five seconds.
But that number is my intuition only; Comecon has been tested thoroughly, but
not stress-tested.

Comecon requires an identifier in the HTML body of every blog post:
`YYYY-MM-DD-post-title`. As you can see, you cannot have two posts with the same
title on the same day. Microbloggers beware.


# FEATURES

Comecon:

- adds comments to your blog posts through an HTML form;
- displays the comments under the blog post;
- lets the commenter attach a link (presumably to their website) to their
  nickname;
- lets the commenter edit or delete their comment within predefined time after
  submitting;
- lets the commenter register their nickname in order to simplify the comment
  submission and "upgrade" the color of the nick;
- is integrated with [Gravatar](https://gravatar.com);
- has a basic captcha to stop spam bots trying their luck randomly in input
  fields;
- has an email notification system;
- updates comment feeds (in
  [Atom](https://en.wikipedia.org/wiki/Atom_(web_standard) format; this is
  practically the same as RSS).

There are also three bonus scripts that have nothing to do with commenting, but
you might want them for your blog. At least I wanted them for mine:

- a simple internal search engine for the blog posts and the comments
  (hilariously crude, but very thorough and surprisingly fast);
- random post selector;
- random quote generator.



# DEPLOYMENT

These are instructions for deploying the core part of Comecon. For information
about the bonus scripts(search engine, random post selector, and random quote
generator), see [DETAILS](blob/main/DETAILS.md).

## Basic functionality: Short version

1. Clone or unzip Comecon into a non-public directory of your website.
2. Fill out `private/settings.php` and `private/vip.php`.
3. Create the directory that `commentsDir` from the settings points to.
4. In the HTML of every blog post (that you want to connect to Comecon), you
   have to include:
   - a PHP snippet with the post identifier: `<?php $postID="YYYY-MM-DD-post-title" ?>`
   - the PHP script for displaying the comments: `includes/add_comments.php`
     (remember to set the correct comment directory in the top part of the
     script)
   - the HTML form for submitting a comment: `includes/form-submit_comment.html`
     which contains three hidden fields:
     - the blog post URL;
     - the blog post identifier;
     - the blog post full title.
5. The submission form is unstyled, so you might want to add some CSS. See
   `examples/styles.css`.

Assuming that your WWW server can process PHP, you are now ready to go.

## Basic functionality: Some details

1. The non-public directory should be something like `/var/www/comecon` if you
   self-host, or something like `/home/username/comecon` on shared hosting. If
   your are on shared hosting and can upload your files to the public HTML directory only,
   check FAQ. Also, if you want the optional feature of email notifications, I
   will tell you below to run `composer install`. On shared hosting, you should
   run this command locally first, and then copy the Comecon folder (with
   dependencies already installed) to the server.
2. To begin with, in `private/settings.php` you must fill out only the settings
   marked as essential. In `private/vip.php`, you should remove the example
   users, and add at least yourself and your grandmother.
3. As above.
4. A few remarks:
   - If you are unsure how to insert these three elements, take a look at
     `examples/blog_post_plain.html`.
   - Also, remember that the HTML form could be placed before the comment
     display. The ordering is up to you. However, the PHP snippet with the post
     identifier must come before the comment display.
   - You have to enter the comment directory manually in `add_comments.php`,
     because this PHP script will be part of blog posts that might be spread
     around in your directory hierarchy. I think it is easiest to enter it
     manually once and for all instead of tracing the relative position of
     `settings.php`.
   - The idea is that you include the snippet, the script and the form using a
     static blog generator like Jekyll (check `examples/blog_post_jekyll.html`).
     However, they can of course be entered manually, or semi-manually with a
     template. It all depends on how you run your non-WordPress blog.
5. As above.

## Optional features

### Master comment file

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

### Feeds

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

### Mail notifications: New blog posts

If you suspect that some of your readers are unfamiliar with RSS/Atom, you may
want to turn on email notifications about new blog posts.

1. Run `compose install` in the Comecon folder to install
   [PHPMailer](https://github.com/PHPMailer/PHPMailer).
2. Edit the `email` category in the settings. Here, you have to connect your
   email account to Comecon. `notify` stays `false`; that one is about about
   sending the comment notifications, not the blog posts notifications.
3. Make `misc/form-blog_subscription` available for you readers. The form
   mentions captcha; you choose it in the `email` category of the settings. You
   have to inform them somehow about "the secret code".
4. Every time you publish a new blog post, you have to run
   `comecon.php?action=notify` manually. See the instruction in
   `src/email_notifications.php`.


