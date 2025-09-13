# STRUCTURE AND DETAILS

## Main scripts

These scripts go to the `assets` subdirectory of your site. See DEPLOYMENT for
general instructions.


### add_subscriber

Adds email subscribers (an alternative for RSS). The input email address comes
from `form-blog_subscription.html` (from `misc`) that you have to put somewhere
on your blog with a proper explanation. Remind the would-be subscribers about
the simple captcha: The email address has to be suffixed with 847, so
`joesubscriber@gmail.com` will be declined, but `joesubscriber@gmail.com847`
will be accepted, and the suffix will of course be removed by the script.


### edit_comment

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


### email_notification

Sends email notification about a new post to all blog subscribers. The admin
activates the script using the following URL:  
`https://myblog.com/assets/email_notification.php?p=&y=&m=&d=&t=&f=`  
where:

* `p` is the admin password with hash defined through `$notificationPassword` in
  `settings.php`
* `y` is the blog post year, e.g. `2023`
* `m` is the blog post month, e.g.`08`
* `d` is the blog post day, e.g. `02`
* `t` is the shortened (slugified) title of the blog post, e.g.
  `today-i-write-about-dogs`
* `f` is the full title of the blog post, e.g.
  `Today%20I%20write%20about%20dogs` where we encode spaces with `%20`


### email_sending

Sends notifications to user. It depends on PHP Mailer and requires an email
account. It is activated automatically through `save_comment.php` (for
notifications about a new comment) or manually through `email_notification.php`
(for notifications about a new blog post).


### exit_messages

Various exit (error) messages. The English names of the variables should be
self-explanatory. The messages itself are in Polish, but will be easy to
translate.


### random_post

Redirects to a random blog post. Take a look at the first line of the script.
Change it to where the text file with all your blog post addresses are (one URL
per line). Normally, that text file would be updated using the static site
generator.


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


### search.php

Searches for a given phrase in the blog posts and comments. The search phrase
comes from `form-search.html` from `misc`. The script will sanitize it and look
for it in all data files in `$searchDataDirectory` as defined in `settings.php`.

These data files are nothing more than plain text versions of your blog posts,
i.e. blog posts without any HTML or Markdown tags. If (some of) the tags are
still around, it will deteriorate the search results because e.g. `**Tolkien**
is best` will not give match for `Tolkien is best`-search phrase.

If you are familiar with `sed`, you can use `clean_blogpost.sed` (from `misc`)
to remove Markdown tags like that:  
`sed -f clean_blogpost.sed blogpost.md > blogpost.txt`

For the same reason you should convert paragraphs into single lines if your
editor is doing hard wraps. It can be done with `tr`:
`tr "\n" " " < blogpost.txt`

Alternatively, use this cryptic sed command:
`sed ':a;N;$!ba;s|\n\([^\n]\)| \1|g' blogpost.md > blogpost.txt`

It is superior to `tr`, because `tr` while mash the entire post into a single
line. The above sed command will respect blank lines which are normally used to
divide paragraphs in Markdown.

The search script will also look for the phrase in all comment files
within `$commentsDir` as defined in `settings.php`.

Notice that the phrase will be saved to `$searchQueriesRecord` as defined in
`settings.php` unless the phrase starts with `123`. This simple "password" can
be easily changed inside `search.php`.

The rationale is that the site owner might want to use the search engine without
necessarily saving the query. The idea behind saving the phrases is to provide a
record of what the visitors are looking for.



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


### random_quote

Displays random quote from a file. See the description inside the script.



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



# POSSIBLE IMPROVEMENTS

* `vip.php` is currently updated manually by the webmaster. However, it could be
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



# LICENCE

CC BY-NC-SA 2024, Borys Jagielski
https://blogrys.pl
