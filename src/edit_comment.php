---
layout: null
---
<?php
include "{{ site.dir_with_data }}/settings.php";
include "exit_messages.php";

function findComment($postDate, $commentID, $adminAccess) {
    global $commentsDir, $commentSalt;
    $commentFilePath = glob("$commentsDir/$postDate*");
    if ($commentFilePath) {
        $commentFile = fopen($commentFilePath[0], "r");
    } else { return false; }
    while (($line = fgets($commentFile)) !== false) {
        $commentElements = explode("<|>", $line);
        if ($adminAccess) {
            $commentDateTime = str_replace(array("-", " ", ":"), "", $commentElements[1]);
            if ($commentDateTime === $commentID) {
                $commentLine = $line;
                break;
            }
        } else {
            if (hash("xxh3", $commentElements[1] . $commentElements[2] . $commentSalt) === $commentID) {
                $commentLine = $line;
                break;
            }
        }
    }
    fclose($commentFile);
    return isset($commentLine) ? $commentLine : false;
}

function HTML2markdown($comment) {
    $comment = str_replace("<br>", "\n", $comment);
    $comment = preg_replace('/<b>(.*?)<\/b>/', '**$1**', $comment);
    $comment = preg_replace('/<i>(.*?)<\/i>/', '*$1*', $comment);
    $comment = preg_replace('/<a href="(.*?)">(.*?)<\/a>/', '[$2]($1)', $comment);
    $comment = preg_replace('/<code>(.*?)<\/code>/', '`$1`', $comment);
    return $comment;
}

function markdown2HTML($comment) {
    $comment = htmlspecialchars($comment, ENT_QUOTES);
    $comment = trim($comment);
    $comment = substr($comment, 0, $maxCommentLength);
    $comment = str_replace(array("\r\n", "\r", "\n"), "<br>", $comment);
    $comment = preg_replace('/`(.*?)`/', '<code>$1</code>', $comment);
    $comment = preg_replace('/\[(.*?)\]\((https?:\/\/)?(.*?)\)($|\s|\.|,)/', '<a href="http://$3">$1</a>$4', $comment);
    $comment = preg_replace('/\*\*(.*?)\*\*/', '<b>$1</b>', $comment);
    $comment = preg_replace('/\*(.*?)\*/', '<i>$1</i>', $comment);
    return $comment;
}

function earlyEnoughToEdit($commentDateTime) {
    global $commentEditTimeout;
    $commentTimestamp = strtotime($commentDateTime);
    $currentTimestamp = time();
    if ($currentTimestamp - $commentTimestamp < $commentEditTimeout) { return true; }
    else { return false; }
}

function changeComment($commentElements, $newComment, $editAllCommentsFile) {
    global $commentsDir, $allCommentsFile;
    if ($editAllCommentsFile) {
        $commentFilepath = $allCommentsFile;
    } else {
        $commentFilename = substr(str_replace("/", "-", $commentElements[0]), 1, -1) . "-COMMENTS.txt";
        $commentFilepath = $commentsDir . "/" . $commentFilename;
    }
    $commentFileContent = file($commentFilepath);
    $relevantCommentLine = $commentElements[1] . "<|>" . $commentElements[2];
    if ($newComment === "") {
        $newCommentLine = "";
    } else {
        $newCommentLine = $commentElements[0] . "<|>" . $commentElements[1] . "<|>" . $commentElements[2] . "<|>" .
            $commentElements[3] . "<|>" . $newComment . "<|>" . $commentElements[5];
    }
    $newCommentFileContent = [];
    foreach ($commentFileContent as $commentLine) {
        if (stristr($commentLine, $relevantCommentLine)) {
            $newCommentFileContent[] = $newCommentLine;
        } else {
            $newCommentFileContent[] = $commentLine;
        }
    }
    file_put_contents($commentFilepath, $newCommentFileContent, LOCK_EX);
    if (filesize($commentFilepath) === 0 && $commentFilepath !== $allCommentsFile) {
        unlink($commentFilepath);
    }
    return true;
}

if (isset($_GET['p'])) {
    if ($adminCommentPassword === hash("xxh3", $_GET['p'])) {
        $adminAccess = true;
    } else { exit($exitmsg_wrongCommentAdminPassword); }
} else { $adminAccess = false; }
$commentLine = findComment($_GET['d'], $_GET['c'], $adminAccess);
if ($commentLine) {
    $commentElements = explode("<|>", $commentLine);
    $comment = HTML2markdown($commentElements[4]);
} else { exit($exitmsg_wrongCommentID); }
if (!earlyEnoughToEdit($commentElements[1]) && !$adminAccess) {
    exit($exitmsg_tooLateToEditComment);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (earlyEnoughToEdit($commentElements[1]) || $adminAccess) {
        changeComment($commentElements, markdown2HTML($_POST["editedComment"]), false);
        changeComment($commentElements, markdown2HTML($_POST["editedComment"]), true);
        header("Location: {{ site.url }}{$commentElements[0]}index.php");
    } else { exit($exitmsg_tooLateToEditComment); }
}
?>

<!DOCTYPE html>
<html lang="en">
<head><title>Edit comment</title></head>
<body>
<form method="post">
<label for="editedComment"><p>Edit your comment. <br>If you wish to remove it, delete everything and confirm with the button.</p></label>
<textarea type="text" id="editedComment" name="editedComment" style="width: 500px; height: 500px;">
<?php echo $comment ?></textarea><br><br>
<input type="submit" value="Confirm edit">
</form>
</body>
</html>
