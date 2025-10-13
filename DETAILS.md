# src

## add_subscriber & email_notification

It is easiest to subscribe to a blog by RSS, but your readers can also subscribe
by email. They enter their email in `misc/form-blog_subscription.html` that you
have to put somewhere on your blog with a proper explanation.

Remind the would-be subscribers about the simple captcha: The email address has
to be suffixed with 847, so `joesubscriber@gmail.com` will be declined, but
`joesubscriber@gmail.com847` will be accepted, and the suffix will of course be
removed by the script. You can change the captcha code in the settings.

What happens next? After you publish a new blog post, you have to use the email
notification script like that:

`https://myblog.com/comecon.php?action=notify&p=&y=&m=&d=&t=&f=`  

Here:

* `p` is the admin password, its hash must be entered into settings
* `y` is the blog post year, e.g. `2023`
* `m` is the blog post month, e.g.`08`
* `d` is the blog post day, e.g. `02`
* `t` is the shortened (slugified) title of the blog post, e.g.
  `today-i-write-about-dogs`
* `f` is the full title of the blog post, e.g.
  `Today%20I%20write%20about%20dogs` where we encode spaces with `%20`

## edit_comment

Allows to edit a comment using the following URL:
`https://myblog.com/assets/edit_comment.php?d=YYYY-MM-DD&c=IDENTIFICATOR`  
or:  
`https://myblog.com/assets/edit_comment.php?d=YYYY-MM-DD&c=YYYYMMDDHHMMSS&p=
ADMINPASSWORD`.

The relevant blog post (`d`) is always identified by its date timestamp (notice
the dashes).

The comment (`c`) can be either identified directly through its full timestamp
(without dashes), or by an identificator. If the direct timestamp is used, an
admin password must be provided (`p`). The password hash is defined in
`$adminCommentPassword` in `settings.php`.

There will be ambiguity in `c`-as-timestamp if two comments have been added
under a blog post at exactly the same second! The admin will be able to edit
only the first of these (following the line order in the comment file). Notice,
however, that it is not obvious that Comecon will accept two comments submitted
at the same second. One (or even both) authors may receive an error message, and
will have to try again. In the end, the comments might actually get different
timestamps.

The comment identificator is generated the moment the comment is published. It
is made from the comment timestamp, the author's nick, and the salt string
defined through `$commentSalt` in `settings.php`. The identificator is then
saved in a cookie. The cookie makes the browser display an edit link under the
comment.

There is a time limit for editing comment defined through `$commentEditTimeout`
in `settings.php`. If the admin password is used, the time limit is not checked
for.

## email_sending

Sends email notifications to user. It depends on PHPMailer and requires an email
account (or, strictly speaking, an SMTP server); you have to provide your
credentials in the settings.

The script will be activated automatically after each comment, and a
notification will be sent to each subscriber of this specific blog post. 

The script can also be activated manually by the blog owner after publishing a
new blog post. Look into the section **add_subscriber and email_notification**
above.


### messages_en

Various messages employed by Comecon. There are three types:

- EXITMSG are error messages;
- MSG are used when updating a comment feed;
- LABEL are used in forms and similar.

--------------------------

### save_comment

Is to Comecon-commenting system what USSR was to Comecon-organization. The
workflow of this script is roughly as follows:

* grab input from `form-submit_comment.html`;
* validate and sanitize the input fields;
* parse Markdown tags in the comment and convert them to HTML tags;
* if the password field was used, attempt to recognize the user;
* set timestamp for the comment;
* parse the URL of the blog post that the comment is for;
* check for duplicates; did someone try to add the same comment several times
  in a row?
* create the comment string `Blog post timestamp<|>Comment timestamp<|>Author of
  comment<|>Author's URL<|>The comment itself<|>Author's rank (if registered)`;
* add the author to the comment subscription list if an email address has been
  entered;
* add the comment string to the flat file with comments (the file must be
  created if this is the first comment under the post);
* redirect back to the blog post after clearing the input fields;
* send email notifications in the background (see `email_sending.php`).





## Includes

### add_comments

Adds comments under a blog post. Also, checks whether `save_comment.php` has
recently set a cookie. If yes, a link for comment edit will be displayed (see
description of `edit_comment.php`).


### form-submit_comments

An HTML form for comment submission. You have to add it under each blog post.
See description of `save_comment.php` for additional details.  Remember that the
URL of each post must be of the form
`https://myblog.com/YYYY/MM/DD/post-title/index.php` as stressed in the
INTRODUCTION.

## Misc

### clean_blogpost

A small `sed` script used to clean blog posts from Markdown tags so that they
can serve as raw data for the search engine. See the description of
`search.php` above.


### form-blog_subscription

An HTML form for subscribing to new blog posts through email. See the
description of `add_subscriber.php` above.


### form_search

An HTML form for searching for a phrase. See the description of `search.php`
above.



## Private

These scripts go to a non-public part of your server. See DEPLOYMENT for
general instructions.


### settings

Settings for PHP scripts. The file contains detailed comments; look into it and
prepare it before deployment.


### vip

An array with registered users. The file contains detailed comments; look into
it and prepare before deployment.



