<?php

/**
 * Finds the relevant comment in the database.
 *
 * @param string $postDate The date of the commented post, YYYY-MM-DD
 * @param string $commentID Either YYYYMMDDHHMMSS if the admin is accessing,
 * or a hashed value from a cookie set for the author of the comment
 * @param bool $adminAccess If true, the admin is accessing. If false, the
 * author of the comment is accessing.
 * @param string $sCommentsDir The filepath for the comment directory
 * @param string $sCommentSalt The comment salt for recognizing the comment ID
 * @return string $commentLine The comment record from the database (the actual
 * comment together with its descriptors)
 */
function findComment($postDate, $commentID, $adminAccess, $sCommentsDir, $sCommentSalt)
{
    // Notice: We expect at most one post with the given date.
    // This is a tenet of Comecon (no more than one blog post per day).
    // If there are several, only the first one will be examined.
    $commentFilePath = glob("$sCommentsDir/$postDate*");
    if (!$commentFilePath) {
        return "";
    }
    $commentFile = fopen($commentFilePath[0], "r");
    if (!$commentFile) {
        return "";
    }
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
        } else {
        // If it is not admin, then the comment ID is a hash of the timestamp,
        // the author's nickname and the salt.
            if (hash("sha256", $commentElements[1] . $commentElements[2] . $sCommentSalt) === $commentID) {
                $commentLine = $line;
                break;
            }
        }
    }
    fclose($commentFile);
    return isset($commentLine) ? $commentLine : "";
}

/**
 * Convert HTML to Markdown, because the comment is saved in HTML, but we
 * display it for editing in Markdown. The conversion is done with regex. We
 * look for newlines, code fragments, bolds, italics and links.
 *
 * @param string $comment The comment to be converted (it includes HTML tags)
 * @return string $comment The comment to be displayed for the editor (with Markdown)
 */
function HTML2markdown($comment)
{
    $comment = str_replace("<br/>", "\n", $comment);
    $comment = preg_replace('/<code>(.*?)<\/code>/', '`$1`', $comment) ?? $comment;
    $comment = preg_replace('/<b>(.*?)<\/b>/', '**$1**', $comment) ?? $comment;
    $comment = preg_replace('/<i>(.*?)<\/i>/', '*$1*', $comment) ?? $comment;
    $comment = preg_replace('/<a href="(.*?)">(.*?)<\/a>/', '[$2]($1)', $comment) ?? $comment;
    return $comment;
}

/**
 * Check if the attempt to edit the comment has been done early enough after its
 * publication.
 *
 * @param string $commentDateTime The timestamp of the comment, YYYY-MM-DD HH:MM:SS
 * @param int $sCommentEditTimeout The time limit (in seconds) for editing one's comment
 * @return bool
 */
function earlyEnoughToEdit($commentDateTime, $sCommentEditTimeout)
{
    $commentTimestamp = strtotime($commentDateTime);
    $currentTimestamp = time();
    if ($currentTimestamp - $commentTimestamp < $sCommentEditTimeout) {
        return true;
    } else {
        return false;
    }
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
 * [6] (string): The author's rank (see vip.php)
 *
 * @param string $commentFilepath The filepath of the comment file where the
 * change will be made
 * @param array<string> $commentElements The fields of the comment record that
 * we are changing
 * @param string $newComment The new (edited) comment that will replace the old
 * one
 * @param bool $permanentFile True if the comment file should stay even if the
 * edition is actually a deletion, and we are deleting the last comment. Use
 * true for the master comment file, and false for a particular comment file
 * that can be deleted
 * @return bool $commentChanged True if the comment has been changed, false if
 * the comment file has not been found or the comment record in the comment file
 * has not been found
 */
function changeComment($commentFilepath, $commentElements, $newComment, $permanentFile)
{
    $commentChanged = false;
    if (!file_exists($commentFilepath)) {
        return $commentChanged;
    }
    $commentFileContent = file($commentFilepath);
    if (!$commentFileContent) {
        return $commentChanged;
    }
    // The relevant comment record is identified by the comment timestamp and
    // the comment's author. Notice the tacit assumption that no two comments share
    // the timestamp AND the author.
    $relevantCommentLine = $commentElements[1] . "<|>" . $commentElements[2];
    // If the comment has been deleted, we will be removing the record.
    if ($newComment === "") {
        $newCommentLine = "";
    } else {
    // Otherwise, we have to create a new record by merging the old descriptors
    // with the new (edited) comment
        $newCommentLine = $commentElements[0] . "<|>" . $commentElements[1] . "<|>" . $commentElements[2] . "<|>" .
            $commentElements[3] . "<|>" . $commentElements[4] . "<|>" . $newComment . "<|>" . $commentElements[6];
    }
    // We scan through the comment file to find the relevant record, and change
    // it. Other records are left without any changes.
    $newCommentFileContent = [];
    foreach ($commentFileContent as $commentLine) {
        if (stristr($commentLine, $relevantCommentLine)) {
            $newCommentFileContent[] = $newCommentLine;
            $commentChanged = true;
        } else {
            $newCommentFileContent[] = $commentLine;
        }
    }
    file_put_contents($commentFilepath, $newCommentFileContent, LOCK_EX);
    // If we have deleted the only comment in a comment file that is not
    // permanent (i.e. not the master comment file), delete the file
    if (filesize($commentFilepath) === 0 && !$permanentFile) {
        unlink($commentFilepath);
    }
    return $commentChanged;
}

// After editing, check again that it is early enough or that the editor is an
// admin. If OK, change the comment in the particular comment file and in the
// global comment file (if the latter is in use)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    validate_request("POST", ["editedComment"]);
    if (earlyEnoughToEdit($commentElements[1], $settings['edit']['commentEditTimeout']) || $adminAccess) {
        $editedComment = prepareString(
            $_POST["editedComment"],
            $settings['save']['maxCommentLength'],
            true,
            true,
            false
        );
        // First and foremost, we are changing the particular comment file.
        // We have to transform the blog post link /YYYY/MM/DD/title/ into
        // the comment filename YYYY-MM-DD-title-COMMENTS.txt
        $commentFilename = substr(str_replace("/", "-", $commentElements[0]), 1, -1) . "-COMMENTS.txt";
        $commentFilepath = $settings['general']['commentsDir'] . "/" . $commentFilename;
        changeComment($commentFilepath, $commentElements, $editedComment, false);
        // Possible change also the master comment file
        if ($settings['save']['allComments']) {
            $commentFilepath = $settings['save']['allCommentsFile'];
            changeComment($commentFilepath, $commentElements, $editedComment, true);
        }
        header("Location: {$settings['general']['siteURL']}{$commentElements[0]}index.php");
    } else {
        exit(EXITMSG_TOOLATETOEDITCOMMENT);
    }
    exit(0);
}

// Check if the admin is accessing. The POST key for the admin password is 'p'.
if ($settings['edit']['adminCommentPassword'] === hash("sha256", $p)) {
    $adminAccess = true;
} else {
    $adminAccess = false;
}
// Identify the comment record using the date of the commented blog post ('d')
// and the comment ID ('c')
$commentLine = findComment(
    $d,
    $c,
    $adminAccess,
    $settings['general']['commentsDir'],
    $settings['edit']['commentSalt']
);
// If the comment record exists, get the comment and convert it to Markdown for
// display
if ($commentLine) {
    $commentElements = explode("<|>", $commentLine);
    $comment = HTML2markdown($commentElements[5]);
} else {
    exit(EXITMSG_WRONGCOMMENTID);
}
// If it is too late to edit (and the editor is not an admin), abort
if (!earlyEnoughToEdit($commentElements[1]) && !$adminAccess) {
    exit(EXITMSG_TOOLATETOEDITCOMMENT);
}

?>

<!DOCTYPE html>
<html lang="<?=$settings['general']['language']?>">
<head><title><?=LABEL_EDITCOMMENTTITLE?></title></head>
<body>
<form method="post">
<label for="editedComment"><p><?=LABEL_EDITCOMMENTFIELD?></p></label>
<textarea type="text" id="editedComment" name="editedComment" style="width: 500px; height: 500px;">
<?php echo $comment ?></textarea><br><br>
<input type="submit" value="<?=LABEL_EDITCOMMENTBUTTON?>">
</form>
</body>
</html>
