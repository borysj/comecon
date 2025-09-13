<?php
$postURI = $_SERVER["REQUEST_URI"];
if (str_contains($postURI, "index.php")) {
    $a = -10;
} else {
    if (substr($postURI, -1) !== "/") { $postURI = $postURI . "/"; }
    $a = -1;
}
$commentFile = str_replace("/", "-", substr($postURI, 1, $a) . "-COMMENTS.txt");
$commentFilePath = "{{ site.dir_with_comments }}/" . $commentFile;
if (file_exists($commentFilePath)) {
?>
    <p><br><br><br></p>
    <div class="comments">
    <h2>Comments</h2>
<?php
    $comments = explode(PHP_EOL, file_get_contents($commentFilePath));
    foreach ($comments as $key => $c) {
        if (!empty($c)) {
            $cc = explode("<|>", $c);
            $commentAnchor = str_replace(array(" ", "-", ":"), "", $cc[1]);
            if (!empty($cc[3])) {
                $nick = '<a href="' . $cc[3] . '">' . $cc[2] . '</a>';
            } else {
                $nick = $cc[2];
            }
            if ($key === array_key_last($comments) - 1) {
?>
            <a id="lastComment"></a>
<?php
            }
            $email = $cc[4];
            $emailhash = md5(strtolower(trim($email ?: "default@example.com")));
?>
            <a id="<?=$commentAnchor?>"></a>
            <p class="comm_author<?=$cc[6]?>">
            <img class ="gravatar"
                 src="https://www.gravatar.com/avatar/<?=$emailhash?>?s=40&d=retro"
                 alt="Gravatar">
            <b><?=$nick?></b>&nbsp;(<?=$cc[1]?>)</p>
<?php
            $cookieName = $cc[0] . "<|>" . str_replace(array("-", " ", ":"), "", $cc[1]);
            if (isset($_COOKIE[$cookieName])) {
                $pattern = "/(\d{4})\/(\d{2})\/(\d{2})\/(.*)\//";
                if (preg_match($pattern, $cc[0], $matches)) {
                    $year = $matches[1];      $month = $matches[2];
                    $day = $matches[3];       $title = $matches[4];
                }
                $dateDashed = $year . "-" . $month . "-" . $day;
?>
                <p class="comm_author_edit<?=$cc[6]?>">
                <a href="{{ site.url }}/assets/edit_comment.php?d=<?=$dateDashed?>&c=<?=$_COOKIE[$cookieName]?>">
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
