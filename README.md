# INTRODUCTION

Comecon is a commenting system for your (static) blog. It is written in vanilla
PHP without any frameworks and without JavaScript. PHP is employed to
both save and display comments.

Comecon is **not** a WordPress-plugin nor any other kind of plugin. It is
a collection of PHP-files that you must set up and upload to your webpage
server following the instructions in DEPLOYMENT.

Furthermore, Comecon does not use any database.  There is no MySQL involved.
The comments are written to and read from flat files (TXT with custom delimiter
<|>). This weird design decision is explained below in FAQ.

Comecon has not been optimized for a large (commenting) traffic to the website.
It probably will not work correctly if you are a hot blogger receiving multiple
comment submissions across your blog simultaneously. However, Comecon works
*very* quickly and reliably if comments are reasonably sparse. I guess you won't
experience any problems unless there are more than one comment per five seconds.
But this number is my intuition only; Comecon has been tested thoroughly, but
not stress-tested.

Comecon requires an identifier in the HTML body of every blog post:
`/YYYY/MM/DD/post-title`. This identifier has actually to be inserted twice per
blog post: next to the snippet that displays comments below the post, and into
the form for submitting comments. See below under DEPLOYMENT for more
information. As you can see, you cannot have two posts with the same title on
the same day.


# FEATURES

Comecon allows your readers to:

- add and later read comments under blog posts (obviously);
- attach links (of their websites) to their nicknames;
- receive email notifications about new comments (or about new blog posts, for
  that matter);
- edit or delete their comments within predefined time after submitting;
- register their nicknames in order to simplify the comment submission and
  "upgrade" the color of the nick.

Comecon features also:

- a simple captcha;
- comment feeds (using Atom format).

There are also bonus scripts that have nothing to do with commenting, but you
might want them for your blog. At least I wanted them for mine:

- a simple internal search engine for the blog posts and the comments;
- random post selection;
- random quote generator.



# DEPLOYMENT

These are instructions for deploying the core part of Comecon. For information
about search engine, random post selection and random quote generator, see
DETAILS.

1. Copy all PHP-files from `src` to your server into
`https://myblog.com/assets` or `https://myblog.com/sub/path/assets` or similar.
The final subdirectory **must** be called `assets`.
2. Copy all PHP-files from `private` to a non-public directory on your server.
This directory could be e.g. `/home/johndoe/data`. You can set the name of the
directory through `site.dir_with_data`, see below.
3. Fill out `settings.php` and `vip.php` (that you have just copied from
`private`).
4. Create an empty file called `all_comments.txt` in your assets subdirectory.
This file could have another name; change `$allCommentsFile` in `settings.php`
accordingly.
5. In every blog post you must include `form-submit_comment.html` and
`add_comments.php` (the latter will be displaying comments).
6. Finally, unpack [PHP Mailer](https://github.com/PHPMailer/PHPMailer) to a
directory on your server. `$phpMailerDir` from `settings.php` must be pointing
to that directory. Notice that you only need `src` of PHP Mailer. Take a look at
the first lines of `email_sending.php` to understand exactly what you need.

Comecon is meant to be used with a static site generator (SSG), specifically
[Jekyll](https://jekyllrb.com/).  However, it is NOT required that you use
Jekyll, nor any other SSG for that matter.

**If you do use Jekyll:**

Most of the PHP-scripts are meant to be parsed by Jekyll. This is why they are
preluded with `layout: null` headers. Within these scripts you will namely find
Jekyll tags `{{ site.url }}` and `{{ site.dir_with_data }}`. These must be
defined in the Jekyll config file. `site.url` is obviously your webpage, e.g.
`https://myblog.com`.  `site.dir_with_data` is the server directory where you
keep `settings.php` and `vip.php`, e.g. `/home/johndoe/data`.

**If you do not use Jekyll:**

In all PHP-scripts:

* Remove `layout: null` headers together with the delimiting `---`.
* Replace `{{ site.url }} with your blog URL, e.g. `https://myblog.com`.
* Replace `{{ site.dir_with_data }} with the server directory where you keep the
  `vip.php` and `settings.php`, e.g. `/home/johndoe/data`.



