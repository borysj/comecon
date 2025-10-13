<?php
// Set the correct comment directory here (same as
// $settings['general']['commentDir'] and uncomment:
// $commentDir =
$postURI = $_SERVER["REQUEST_URI"];
if (!is_string($postURI)) {
    exit("The post URL is unreadable" . " ::: " . __FILE__ . ":" . __LINE__);
}
if (str_contains($postURI, "index.php")) {
    $a = -10;
} else {
    if (substr($postURI, -1) !== "/") {
        $postURI = $postURI . "/";
    }
    $a = -1;
}
$commentFile = str_replace("/", "-", substr($postURI, 1, $a) . "-COMMENTS.txt");
$commentFilePath = $commentDir . "/" . $commentFile;
if (file_exists($commentFilePath)) {
    ?>
    <p><br><br><br></p>
    <div class="comments">
    <h2>Comments</h2>
    <?php
    $fileContents = file_get_contents($commentFilepath);
    if (!is_string($fileContents)) {
        exit("The comment file is unreadable" . " ::: " . __FILE__ . ":" . __LINE__);
    }
    $comments = explode(PHP_EOL, $fileContents);
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
            $hashedEmail = $cc[4];
            ?>
            <a id="<?=$commentAnchor?>"></a>
            <p class="comm_author<?=$cc[6]?>">
            <img class ="gravatar"
                 src="https://www.gravatar.com/avatar/<?=$hashedEmail?>?s=40&d=retro"
                 alt="Gravatar">
            <b><?=$nick?></b>&nbsp;(<?=$cc[1]?>)</p>
            <?php
            $cookieName = $cc[0] . "<|>" . str_replace(array("-", " ", ":"), "", $cc[1]);
            if (isset($_COOKIE[$cookieName])) {
                $pattern = "/(\d{4})\/(\d{2})\/(\d{2})\/(.*)\//";
                if (preg_match($pattern, $cc[0], $matches)) {
                    $year = $matches[1];
                    $month = $matches[2];
                    $day = $matches[3];
                    $title = $matches[4];
                } else {
                    exit("I cannot parse the URL" . " ::: " . __FILE__ . ":" . __LINE__);
                }
                $dateDashed = $year . "-" . $month . "-" . $day;
                ?>
                <p class="comm_author_edit<?=$cc[6]?>">
                <a href="/comecon.php?action=edit&d=<?=$dateDashed?>&c=<?=$_COOKIE[$cookieName]?>">
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
