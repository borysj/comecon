<!DOCTYPE html>
<html lang="en" class="html" data-theme="auto"><head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <title>
    
      A new blog post
    
  </title>

  <!-- Favicon -->
  <link rel="apple-touch-icon" sizes="180x180" href="https://blog.example.com/assets/images/favicon/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="https://blog.example.com/assets/images/favicon/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="https://blog.example.com/assets/images/favicon/favicon-16x16.png">
  <link rel="manifest" href="https://blog.example.com/assets/images/favicon/site.webmanifest">
  <link rel="shortcut icon" href="https://blog.example.com/assets/images/favicon/favicon.ico">
  <!-- Favicon -->

  <!-- Feeds -->
  <link rel="alternate" type="application/atom+xml" href="https://blog.example.com/feed.xml" title="My Blog (trailers only)">
  <link rel="alternate" type="application/atom+xml" href="https://blog.example.com/feed-full_content.xml" title="My Blog (full posts)">
  <!-- Feeds -->

  <link rel="stylesheet" href="https://blog.example.com/assets/css/main.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" crossorigin>
  <link rel="preconnect" href="https://fonts.googleapis.com/css2?family=Roboto+Mono&display=swap" crossorigin>
  <link rel="preconnect" href="https://fonts.googleapis.com/css2?family=Cutive&display=swap" crossorigin>

</head>
<body>
    <main class="page-content" aria-label="Content">

      <div class="w">
        <hgroup>
<h1>A new blog post</h1>

<article>
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec sit amet egestas erat. Nunc sed nulla in orci fermentum luctus. Integer sit amet felis semper nisi egestas feugiat. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Phasellus aliquet enim nec malesuada maximus. In hac habitasse platea dictumst. Sed eget elementum leo. Nulla finibus felis dui, pretium tempus lorem mollis in. Ut turpis quam, consectetur quis nibh ut, imperdiet malesuada augue. Vestibulum non cursus dui, non feugiat nisi. Mauris volutpat justo id risus rutrum, sit amet hendrerit libero commodo. Praesent eleifend bibendum lacus a rutrum.</p>

<p>Ut sem orci, eleifend id metus nec, bibendum aliquet ligula. Donec rutrum id mauris porttitor aliquet. Aenean pharetra tellus ut mollis bibendum. Aenean vestibulum, ex sed consectetur vestibulum, purus risus rutrum urna, sit amet fringilla sapien est ut libero. Sed eget dui porttitor diam dictum convallis. Nullam id suscipit turpis. Etiam et erat nec felis auctor blandit. Duis posuere, felis vel elementum porta, arcu orci aliquam magna, sed varius odio diam vel neque. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Duis sed nisi in diam dignissim dapibus. Phasellus id porttitor mauris. Integer sed libero varius nulla imperdiet blandit. Quisque dictum consectetur pellentesque. Donec ultricies nisl nibh, at faucibus velit tempor vel.</p>

<p>Nullam consequat, sem quis placerat imperdiet, erat lacus pretium est, vel mattis metus urna eget arcu. Praesent lobortis malesuada risus interdum sodales. Etiam et metus euismod, laoreet ligula eget, luctus eros. Interdum et malesuada fames ac ante ipsum primis in faucibus. Vivamus justo mi, accumsan ultricies egestas sit amet, gravida vitae tellus. Vestibulum mauris nulla, gravida vitae mi vel, eleifend condimentum felis. Nullam non mauris dui. Etiam ullamcorper commodo neque, sit amet efficitur sapien vehicula placerat. Maecenas dignissim suscipit tortor, a sagittis mi dignissim ac. Nulla ullamcorper id lorem ut fringilla. Integer scelerisque faucibus tincidunt. Nam ut nulla in nisi blandit eleifend rutrum id neque. Nulla felis justo, imperdiet sed lorem in, pulvinar vulputate dolor. Cras at finibus lacus. Donec accumsan, neque at interdum vulputate, diam quam cursus dolor, a tristique elit quam id lectus. Morbi at ex et nisl pellentesque ultricies eu sit amet augue.</p>
</article>

<p><br><br></p>

<!-- The post identifier -->
<?php $postID = "2021-01-16-new-blog-post"; ?>

<!-- display_comments.php -->
<?php
// Set the comment directory here and uncomment.
// It must be exactly the same as the value of
// $settings['general']['commentDir'], only that you cannot refer to settings
// from here. This snippet will be embedded in blog posts whose location
// in the directory structure will be generally uncertain.
$commentDir = "/var/www/comments";

if (!isset($postID) || $postID === "") {
    exit("I cannot display the comments, because the post identifier has not been set.");
}
$postID = preg_replace("/[^a-zA-Z0-9_\-]/", "", $postID);
$postID = ltrim($postID, "-");
$postID = substr($postID, 0, 100);
if ($postID === "") {
    exit("I cannot display the comments, because the post identifier was invalid.");
}
$commentFile = $postID . "-COMMENTS.txt");
$commentFilePath = $commentDir . "/" . $commentFile;

if (file_exists($commentFilePath)) {
    $fileContents = file_get_contents($commentFilepath);
    if (!is_string($fileContents)) {
        exit("The comment file $commentFilePath is unreadable.");
    }
    echo "<p><br><br><br></p>\n" .
         "<div class=\"comments\">\n" .
         "<h2>Comments</h2>"
    $comments = explode(PHP_EOL, $fileContents);
    foreach ($comments as $key => $c) {
        if (!empty($c)) {
            $cc = explode("<|>", $c);
            $commentAnchor = str_replace(array(" ", "-", ":"), "", $cc[1]);
            if (!empty($cc[3])) {
                $nick = "<a href=\"{$cc[3]}\">{$cc[2]}</a>";
            } else {
                $nick = $cc[2];
            }
            if ($key === array_key_last($comments) - 1) {
                echo "<a id=\"lastComment\"></a>"
            }
            $hashedEmail = $cc[4];
            ?>
            <a id="<?=$commentAnchor?>"></a>
            <p class="comm_author<?=$cc[6]?>">
            <img class ="gravatar"
                 src="https://www.gravatar.com/avatar/<?=$hashedEmail?>?s=40&d=retro"
                 alt="Gravatar">
            <b><?=$nick?></b>&nbsp;(<?=$cc[1]?>)</p>
            <?php
            $cookieName = $cc[0] . "<|>" . $commentAnchor;
            if (isset($_COOKIE[$cookieName])) {
                ?>
                <p class="comm_author_edit<?=$cc[6]?>">
                <a href="/comecon.php?action=edit&id=<?=$postID?>&c=<?=$_COOKIE[$cookieName]?>">
                ..:: You have 20 minutes to edit your comment ::..
                </a></p>
                <?php
            }
            ?>
            <p class="comm_content<?=$cc[6]?>"><?=$cc[5]?></p>
            <?php
        }
    }
    ?>
    </div>
    <?php
}
?>

<!-- form-submit_comment.html -->
<p><br><br></p>
<div class="commentFormCSS" style="text-align: center;">
<form name="commentForm" action="/comecon.php?action=save" method="POST" onsubmit="submitButton.disabled = true; return true;">
<fieldset>
<legend align="left">C O M E C O N</legend>
<input type="hidden" id="postID" name="postID" value="<?=$postID ?>">
<input type="hidden" id="postFullTitle" name="postFullTitle" value="A new blog post">

<label for="comment">Leave your opinion (max. 4000 characters):</label>
<textarea id="comment" name="comment" maxlength="3900"
 placeholder="Add links like that: [Example](www.example.com).
You can also use **bold** or *italics* (this also works for _italics_).
You can mark code and filenames `like this`.
Use paragraphs, newlines and blank lines will be preserved.
If your cookies are on, you will be able to edit your comment."
 required>
</textarea>

<label for="name">Signature:</label>
<input id="name" name="name" required>

<label for="password">Password (if you are registered):</label>
<input id="password" name="password" type="password">

<label for="webpage">Your webpage:</label>
<input id="webpage" name="webpage"
 placeholder="You can omit the initial http(s):// in the URL">

<label for="email">Your email (for <a href="https://gravatar.com">gravatar</a> and possibly notifications):</label>
<input id="email" name="email" type="email"
 placeholder="You can leave it empty" >

<!--
<label for="email2">
    <input id="email-comments" name="email-comments" type="checkbox">
    Check it if you wish to subscribe to these comments through your email
</label>
-->

<label for="captcha">
    Poor man's captcha:<br>Esay but ofubcstaed quistoen?<br><br>
</label>

<input id="captcha" name="captcha"
placeholder="">

<input type="submit" name="submitButton" value="Send">
</fieldset>
</form>
</div>

    </main>
  </body>
</html>
