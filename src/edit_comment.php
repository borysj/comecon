---
layout: null
---
<?php
include "{{ site.dir_with_data }}/settings.php";
include $settings['general']['messages'];
include "utilities.php";

/**
 * Finds the relevant comment in the database.
 *
 * @param string $postDate The date of the commented post, YYYY-MM-DD
 * @param string $commentID Either YYYYMMDDHHMMSS if the admin is accessing,
 * or a hashed value from a cookie set for the author of the comment
 * @param string $adminAccess If true, the admin is accessing. If false, the
 * author of the comment is accessing.
 * @return string $commentLine The comment record from the database (the actual
 * comment together with its descriptors)
 */
function findComment($postDate, $commentID, $adminAccess) {
    global $settings;
    // Notice: We expect at most one post with the given date.
    // This is a tenet of Comecon (no more than one blog post per day).
    // If there are several, only the first one will be examined.
    $commentFilePath = glob("{$settings['general']['commentsDir']}/$postDate*");
    if ($commentFilePath) { $commentFile = fopen($commentFilePath[0], "r"); }
    else                  { return false; }
    // Scan through the relevant comment file
    while (($line = fgets($commentFile)) !== false) {
        $commentElements = explode("<|>", $line);
        // If it is admin, then the comment ID is its timestamp (saved as
        // YYYY-MM-DD HH:MM:SS)
        if ($adminAccess) {
            $commentDateTime = str_replace(array("-", " ", ":"), "", $commentElements[1]);
            if ($commentDateTime === $commentID) {
                $commentLine = $line;
                // Break immediately, as we expect only one comment with the
                // given timestamp
                break;
            }
        }
        // If it is not admin, then the comment ID is a hash of the timestamp,
        // the author's nickname and the salt.
        else {
            if (hash("sha256", $commentElements[1] . $commentElements[2] . $settings['edit']['commentSalt']) === $commentID) {
                $commentLine = $line;
                break;
            }
        }
    }
    fclose($commentFile);
    return isset($commentLine) ? $commentLine : false;
}

/**
 * Convert HTML to Markdown, because the comment is saved in HTML, but we
 * display it for editing in Markdown. The conversion is done with regex. We
 * look for newlines, code fragments, bolds, italics and links.
 *
 * @param string $comment The comment to be converted (it includes HTML tags)
 * @return string $comment The comment to be displayed for the editor (with Markdown)
 */
function HTML2markdown($comment) {
    $comment = str_replace("<br/>", "\n", $comment);
    $comment = preg_replace('/<code>(.*?)<\/code>/', '`$1`', $comment);
    $comment = preg_replace('/<b>(.*?)<\/b>/', '**$1**', $comment);
    $comment = preg_replace('/<i>(.*?)<\/i>/', '*$1*', $comment);
    $comment = preg_replace('/<a href="(.*?)">(.*?)<\/a>/', '[$2]($1)', $comment);
    return $comment;
}

/**
 * Check if the attempt to edit the comment has been done early enough after its
 * publication.
 *
 * @param string $commentDateTime The timestamp of the comment, YYYY-MM-DD HH:MM:SS
 * @return bool
 */
function earlyEnoughToEdit($commentDateTime) {
    global $settings;
    $commentTimestamp = strtotime($commentDateTime);
    $currentTimestamp = time();
    if ($currentTimestamp - $commentTimestamp < $settings['edit']['commentEditTimeout']) { return true; }
    else { return false; }
}

/**
 * Change the comment record in the comment file. The $commentElements array
 * contains the following elements:
 * [0] (string): The permalink to the commented blog post, /YYYY/MM/DD/title-of-the-post/
 * [1] (string): The comment timestamp, YYYY-MM-DD HH:MM:SS
 * [2] (string): The author's nickname
 * [3] (string): The author's website (can be empty)
 * [4] (string): The author's email (can be empty)
 * [5] (string): The comment itself (with HTML tags)
 * [6] (int): The author's rank (see vip.php)
 *
 * @param array $commentElements The fields of the comment record
 * @param array $newComment The edited comment
 * @param bool $editAllCommentsFile If true, edit the main comment file
 * containing all the comments. If false, edit the specific comment file
 * contianing only the comments for the relevant blog post
 */
function changeComment($commentElements, $newComment, $editAllCommentsFile) {
    global $settings;
    if ($editAllCommentsFile) {
        $commentFilepath = $settings['save']['allCommentsFile'];
    } else {
        $commentFilename = substr(str_replace("/", "-", $commentElements[0]), 1, -1) . "-COMMENTS.txt";
        $commentFilepath = $settings['general']['commentsDir'] . "/" . $commentFilename;
    }
    $commentFileContent = file($commentFilepath);
    // The relevant comment record is identified by the comment timestamp and
    // the comment's author. Notice the tacit assumption that no two comments share
    // the timestamp AND the author.
    $relevantCommentLine = $commentElements[1] . "<|>" . $commentElements[2];
    // If the comment has been deleted, we will be removing the record.
    if ($newComment === "") {
        $newCommentLine = "";
    }
    // Otherwise, we have to create a new record by merging the old descriptors
    // with the new (edited) comment
    else {
        $newCommentLine = $commentElements[0] . "<|>" . $commentElements[1] . "<|>" . $commentElements[2] . "<|>" .
            $commentElements[3] . "<|>" . $commentElements[4] . "<|>" . $newComment . "<|>" . $commentElements[6];
    }
    // We scan through the comment file to find the relevant record, and change
    // it. Other records are left without any changes.
    $newCommentFileContent = [];
    foreach ($commentFileContent as $commentLine) {
        if (stristr($commentLine, $relevantCommentLine)) {
            $newCommentFileContent[] = $newCommentLine;
        } else {
            $newCommentFileContent[] = $commentLine;
        }
    }
    file_put_contents($commentFilepath, $newCommentFileContent, LOCK_EX);
    // If we have deleted the only comment in the specific comment file, we remove the file.
    if (filesize($commentFilepath) === 0 && $commentFilepath !== $settings['save']['allCommentsFile']) {
        unlink($commentFilepath);
    }
    return true;
}

// Check if the admin is accessing. The POST key for the admin password is 'p'.
if (isset($_GET['p'])) {
    if ($settings['edit']['adminCommentPassword'] === hash("sha256", $_GET['p'])) {
        $adminAccess = true;
    } else { exit($exitmsg_wrongCommentAdminPassword); }
} else { $adminAccess = false; }
// Identify the comment record using the date of the commented blog post ('d')
// and the comment ID ('c')
$commentLine = findComment($_GET['d'], $_GET['c'], $adminAccess);
// If the comment record exists, get the comment and convert it to Markdown for
// display
if ($commentLine) {
    $commentElements = explode("<|>", $commentLine);
    $comment = HTML2markdown($commentElements[5]);
} else { exit($exitmsg_wrongCommentID); }
// If it is too late to edit (and the editor is not an admin), abort
if (!earlyEnoughToEdit($commentElements[1]) && !$adminAccess) {
    exit($exitmsg_tooLateToEditComment);
}

// After editing, check again that it is early enough or that the editor is an
// admin. If OK, change the comment both in the particular comment file and the
// global comment file.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (earlyEnoughToEdit($commentElements[1]) || $adminAccess) {
        changeComment($commentElements, prepareString($_POST["editedComment"], $settings['save']['maxCommentLength'], true, true, false), false);
        if ($settings['save']['allCommentsFile']) {
            changeComment($commentElements, prepareString($_POST["editedComment"], $settings['save']['maxCommentLength'], true, true, false), true);
        }
        header("Location: {{ site.url }}{$commentElements[0]}index.php");
    } else { exit($exitmsg_tooLateToEditComment); }
}
?>

<!DOCTYPE html>
<html lang="<?=$settings['general']['language']?>">
<head><title><?=$label_editCommentTitle?></title></head>
<body>
<form method="post">
<label for="editedComment"><p><?=$label_editCommentField?></p></label>
<textarea type="text" id="editedComment" name="editedComment" style="width: 500px; height: 500px;">
<?php echo $comment ?></textarea><br><br>
<input type="submit" value="<?=$label_editCommentButton?>">
</form>
</body>
</html>
