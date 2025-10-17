<?php
// Set the comment directory here and uncomment.
// It must be exactly the same as the value of
// $settings['general']['commentsDir'], only that you cannot refer to settings
// from here. This snippet will be embedded in blog posts whose location
// in the directory structure will be generally uncertain.
// $commentsDir =

if (!isset($postID) || $postID === "") {
    exit("I cannot display the comments, because the post identifier has not been set.");
}
$postID = preg_replace("/[^a-zA-Z0-9_\-]/", "", $postID);
$postID = ltrim($postID, "-");
$postID = substr($postID, 0, 100);
if ($postID === "") {
    exit("I cannot display the comments, because the post identifier was invalid.");
}
$commentFile = $postID . "-COMMENTS.txt";
$commentFilePath = $commentsDir . "/" . $commentFile;

if (file_exists($commentFilePath)) {
    $fileContents = file_get_contents($commentFilepath);
    if (!is_string($fileContents)) {
        exit("The comment file $commentFilePath is unreadable.");
    }
    echo "<p><br><br><br></p>\n" .
         "<div class=\"comments\">\n" .
         "<h2>Comments</h2>";
    $comments = explode(PHP_EOL, $fileContents);
    // Omit the first line, it is the post URL
    foreach (array_slice($comments, 1) as $key => $c) {
        if (!empty($c)) {
            $cc = explode("<|>", $c);
            $commentAnchor = str_replace(array(" ", "-", ":"), "", $cc[1]);
            if (!empty($cc[3])) {
                $nick = "<a href=\"{$cc[3]}\">{$cc[2]}</a>";
            } else {
                $nick = $cc[2];
            }
            if ($key === array_key_last($comments) - 1) {
                echo "<a id=\"lastComment\"></a>";
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
                <!-- Remember to edit the time if you have changed it in the setting -->
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
