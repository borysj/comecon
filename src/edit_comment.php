<?php

// After editing, check again that it is early enough or that the editor is an
// admin. If OK, change the comment in the particular comment file and in the
// global comment file (if the latter is in use)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    validate_request("POST", ["editedComment"]);
    if (
        earlyEnoughToEdit($commentElements[1], $settings['edit']['commentEditTimeout']) ||
        $adminAccess
    )
    {
        $editedComment = prepareString(
            $_POST["editedComment"],
            $settings['save']['maxCommentLength'],
            true,
            true,
            false
        );
        // We change the specific comment file
        changeComment($commentFilePath, $commentElements, $editedComment, false);
        // Possible change also the master comment file
        if ($settings['save']['allComments']) {
            $commentFilepath = $settings['save']['allCommentsFile'];
            changeComment($commentFilepath, $commentElements, $editedComment, true);
        }
        header("Location: {$settings['general']['siteURL']}{$commentElements[0]}index.php");
    } else {
        exit(EXITMSG_TOOLATETOEDITCOMMENT . " ::: " . __FILE__ . ":" . __LINE__);
    }
    exit(0 . " ::: " . __FILE__ . ":" . __LINE__);
}

// Check if the admin is accessing
if ($settings['edit']['adminCommentPassword'] === hash("sha256", $adminPassword)) {
    $adminAccess = true;
} else {
    $adminAccess = false;
}
// Identify the comment record using the blog post ID and the comment ID
[$commentFilePath, $commentLine] = findComment(
    $postID,
    $commentID,
    $adminAccess,
    $settings['general']['commentsDir'],
    $settings['edit']['cookieKey']
);
// If the comment record exists, get the comment and convert it to Markdown for
// display
if ($commentLine) {
    $commentElements = explode("<|>", $commentLine);
    $comment = HTML2markdown($commentElements[5]);
} else {
    exit(EXITMSG_WRONGCOMMENTID . " ::: " . __FILE__ . ":" . __LINE__);
}
// If it is too late to edit (and the editor is not an admin), abort
if (!earlyEnoughToEdit($commentElements[1]) && !$adminAccess) {
    exit(EXITMSG_TOOLATETOEDITCOMMENT . " ::: " . __FILE__ . ":" . __LINE__);
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
