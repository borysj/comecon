# INTRODUCTION

Comecon is a commenting system for your (static) blog. It is written in vanilla
PHP without any frameworks and with almost no JavaScript. PHP is employed to
both save and display comments.

Comecon is **not** a WordPress-plugin nor any other kind of plugin. It is
a collection of PHP-files that you must set up and upload to your webpage
server following the instructions in DEPLOYMENT.

Furthermore, Comecon does not use any database.  There is no MySQL and the
comments are written to and read from flat files (TXT with custom delimiter
<|>). This decision is explained below in the FAQ.

Comecon has two core limitations you should be aware of from the outset:

* Since there is no database, Comecon might not work correctly if there are
  multiple comment submissions at once. Thus, Comecon is probably not the best
  choice if your blog has a lot of (commenting) traffic. However, it works
  *very* quickly if comments are reasonably sparse. I guess the problems won't
  start if there is less than one comment per ten seconds.
* Comecon assumes you won't be blogging more than once per day, and it requires
  that all blog posts have the following URL:
  `https://myblog.com/YYYY/MM/DD/post-title/index.php`  The scripts rely heavily
  on the YYYY/MM/DD/post-title identifiers to manage the comment files.



# FEATURES

Comecon allows blog readers to:

* add and read comments under blog posts (obviously);
* attach links under their nicknames;
* receive email notifications about new comments (or about new blog posts, for
  that matter);
* edit or delete their comments within predefined time after submitting;
* register their nicknames and feature their comments.

Furthermore, Comecon has:

* a simple captcha;
* an internal search engine for the blog posts and the comments.

Bonus features:

* random post selection;
* random quote generator.



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



