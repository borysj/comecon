<?php

// -- USED BY comecon.php OR BY SEVERAL SCRIPTS -- //

/**
 * Validates the request method and the presence and type of required keys.
 *
 * @param string $expectedMethod The expected request method ('GET' or 'POST')
 * @param array<string> $requiredKeys An array of keys that must be present in the request
 * @return void
 */
function validate_request($expectedMethod, $requiredKeys)
{
    if ($_SERVER['REQUEST_METHOD'] !== $expectedMethod) {
        exit(EXITMSG_WRONGREQUESTMETHOD . " ::: " . __FILE__ . ":" . __LINE__);
    }
    $requestData = ($expectedMethod === 'POST') ? $_POST : $_GET;
    foreach ($requiredKeys as $key) {
        if (!isset($requestData[$key]) || !is_string($requestData[$key])) {
            exit(EXITMSG_KEYISWRONG . " ::: " . __FILE__ . ":" . __LINE__);
        }
    }
}

/**
 * Sanitizes a post ID.
 *
 * @param string $postID The post ID to be sanitized
 * @return string The sanitized post ID
 */
function sanitizePostID($postID)
{
    $postID = preg_replace("/[^a-zA-Z0-9_\-]/", "", $postID);
    $postID = ltrim($postID, "-");
    $postID = substr($postID, 0, 100);
    return $postID;
}

/**
 * Attempts to grab the full title of a blog post directly from the HTML
 *
 * @param string $url The URL of the blog post
 * @return string|null The candidate for the full title, or null if
 * parsing of the HTML did not work
 */
function getFullTitle($url)
{
    // Simple SSRF protection
    if (!preg_match('/^https?:\/\//i', $url)) {
        return null;
    }
    $html = file_get_contents($url);
    $n = $settings['general']['locationFullTitle'];
    if (preg_match('/<h' . $n . '[^>]*>(.*?)<\/h' . $n . '>/is', $html, $matches)) {
        $fullTitle = strip_tags($matches[1]);
        $fullTitle = trim($fullTitle);
    } else {
        $fullTitle = null;
    }
    return $fullTitle;
}

/**
 * Prepares and sanitizes a string.
 *
 * @param string $string The string to be prepared
 * @param int $length The string will be trimmed to this length
 * @param bool $breaklines If true, replace the newlines and breaklines with
 * <br />. If false, remove them altogether.
 * @param bool $markdown If false, do nothing. If true, convert Markdown to HTML (but without <p>)
 * @param bool $http If true, add http:// to the beginning of the string if it
 * is not already present.
 * @return string The prepared string
 */
function prepareString($string, $length, $breaklines, $markdown, $http)
{
    if (empty($string)) {
        return "";
    }
    $string = htmlspecialchars($string, ENT_QUOTES);
    $string = trim($string);
    $string = mb_substr($string, 0, $length, "UTF-8");
    if ($breaklines) {
        $string = str_replace(array("\r\n", "\r", "\n"), "<br/>", $string);
    } else {
        $string = str_replace(array("\r\n", "\r", "\n"), "", $string);
    }
    if ($markdown) {
        // Process links with validation
        $string = preg_replace_callback(
        '/\[([^\]\n]+)\]\(([^)\n]+)\)/',
        function($matches) {
            $linkText = $matches[1];
            $url = $matches[2];
            $validURL = isValidURL($url);
            if ($validURL === false) {
                return $linkText;
            }
            $safeURL = htmlspecialchars($validURL, ENT_QUOTES);
            return "<a href=\"" . $safeURL . "\">" . $linkText . "</a>";
        },
        $string
    ) ?? $string;

        // Process style blocks
        $string = preg_replace('/`([^`\n]+)`/', '<code>$1</code>', $string) ?? $string;
        $string = preg_replace('/\*\*([^\*\n]+)\*\*/', '<strong>$1</strong>', $string) ?? $string;
        $string = preg_replace('/\*(?! )([^\*\n]+)\*/', '<em>$1</em>', $string) ?? $string;
        $string = preg_replace('/(?<=^|\s)_([^_\n]+)_(?=\s|$)/', '<em>$1</em>', $string) ?? $string;
    }
    if ($http && stripos($string, "http") !== 0) {
        $string = "http://" . $string;
    }
    return $string;
}

/**
 * Validate the Markdown URL provided by the user in a comment.
 *
 * @param string $url The provided URL
 * @return string|false Validated URL or false if invalid
 */
function isValidURL($url) {
    $url = trim($url);
    if (stripos($url, 'http://') !== 0 && stripos($url, 'https://') !== 0) {
        $url = 'http://' . $url;
    }
    if (filter_var($url, FILTER_VALIDATE_URL) === false) {
        return false;
    }
    $parsedURL = parse_url($url);
    if (
        !isset($parsedURL['scheme']) ||
        !in_array(strtolower($parsedURL['scheme']), ['http', 'https']))
    {
        return false;
    }
    return $url;
}


// -- USED BY edit_comment.php -- //

/**
 * Finds the relevant comment in the database.
 *
 * @param string $postID The ID of the commented post
 * @param string $commentID Either YYYYMMDDHHMMSS if the admin is accessing,
 * or a hashed value from a cookie set for the author of the comment
 * @param bool $adminAccess If true, the admin is accessing. If false, the
 * author of the comment is accessing.
 * @param string $sCommentsDir The filepath for the comment directory
 * @param string $sCookieKey The cookie key that the comment ID has been
 * hashed with
 * @return string The comment record from the database (the actual
 * comment together with its descriptors)
 */
function findComment($postID, $commentID, $adminAccess, $sCommentsDir, $sCookieKey)
{
    $commentFilePath = glob("$sCommentsDir/$postID*");
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
        $expectedValue = hash("sha256", $commentElements[1] . $commentElements[2] . $sCookieKey);
            if (hash_equals($expectedValue, $commentID)) {
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
 * @return string The comment to be displayed for the editor (with Markdown)
 */
function HTML2markdown($comment)
{
    $comment = str_replace("<br/>", "\n", $comment);
    $comment = preg_replace('/<code>(.*?)<\/code>/', '`$1`', $comment) ?? $comment;
    $comment = preg_replace('/<strong>(.*?)<\/strong>/', '**$1**', $comment) ?? $comment;
    $comment = preg_replace('/<em>(.*?)<\/em>/', '*$1*', $comment) ?? $comment;
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
 * [6] (string): The author's rank (see commenters.php)
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
 * @return bool True if the comment has been changed, false if
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


// -- USED BY save_comment.php -- //

/**
 * Check whether a file with given path exists. If not, create it.
 *
 * @param string $path The filepath to check and possibly create
 * @return void
 */
function createNonexistentFile($path)
{
    if (!file_exists($path)) {
        touch($path);
        chmod($path, 0644);
    }
}

/**
 * Check whether the comment is the duplicate (identical to the most recent
 * comment).
 *
 * @param string $commentFilePath The comment file to look up
 * @param string $comment The comment to check
 * @return bool True if duplicate, false if not or if the comment file not found
 */
function checkIfDuplicate($commentFilePath, $comment)
{
    if (!file_exists($commentFilePath)) {
        return false;
    }
    $commentFile = file($commentFilePath);
    if (!$commentFile) {
        return false;
    }
    // Get the last saved comment record from the comment file
    $lastCommentLine = $commentFile[count($commentFile) - 1];
    $lastComment = explode("<|>", $lastCommentLine);
    // The field with the comment text is indexed as 5
    if (trim($lastComment[5]) === $comment) {
        return true;
    } else {
        return false;
    }
}

/**
 * Check if the user-password combination is among the registered users.
 * The returned array contains the following elements:
 * [0]: bool, whether the user-password combination has been recognized
 * [1]: int, the rank of the user (1 for the blog owner, 2 for an eminent user,
 *      3 for a normal registered user)
 * [2]: string, the user's website
 * [3]: string, the user's email (for subscription) or possibly only the hash of
 *      the user's email (for gravatar); see description in commenters.php
 * [4]: int, 0 if user does not want to subscribe to comments after posting
 *      their own, 1 if they want to; see description in commenters.php
 *
 * @param string $userName The name provided by the commenter
 * @param string $userPassword The password provided by the commenter
 * @param array<string, mixed> $commenters The associative array with the registered users
 * @return array<mixed>
 */
function checkVip($userName, $userPassword, $commenters)
{
    if (array_key_exists($userName, $commenters)) {
        if ($commenters[$userName][0] === hash("sha256", $userPassword)) {
            return [true, $commenters[$userName][1], $commenters[$userName][2],
                    $commenters[$userName][3], $commenters[$userName][4]];
        } else {
            return [false, -1, "", "", 0];
        }
    } else {
        return [false, -1, "", "", 0];
    }
}

/**
 * Update the comment feed.
 *
 * @param string $postID The identifier of the commented blog post
 * @param string $fullTitle The full title of the commented blog post
 * @param string $postURL The URL of the commented blog post
 * @param string $commentTimestamp The timestamp of the comment, from date($settings['save']['timestamp'])
 * @param string $commenter The name of the commenter
 * @param string $commenterURL The URL of the commenter's website
 * @param string $comment The comment text (may contain HTML tags)
 * @param array<string, mixed> $sFeed Feed settings (wheter we update the
 * newest comments feed, whether we update the specific feeds,
 * the filepath to the directory with comment feeds, the number of
 * the newest comments to be retained)
 *
 * @return bool Returns false if the filepath for the feed does not
 * exist or if the feed content is unreadable or if the comment timestamp is
 * unreadeable or if the preg_replace returned null somehow,
 * returns true after successfully updating the feed
 */
function updateFeed(
    $postID,
    $fullTitle,
    $postURL,
    $commentTimestamp,
    $commenter,
    $commenterURL,
    $comment,
    $sFeed
) {
    $commentAnchor = str_replace(array(" ", "-", ":"), "", $commentTimestamp);
    $commentURLWithAnchor = $postURL . "#" . $commentAnchor;
    $timestamp = strtotime($commentTimestamp);
    if (!$timestamp) {
        return false;
    }
    $formattedTimestamp = date("c", $timestamp);
    $entryTitle = MSG_COMMENTFEEDENTRYTITLE;
    $commentInContext = MSG_COMMENTINCONTEXT;

    $newEntry = <<<ENTRYENDS
    <entry>
    <title>$entryTitle $fullTitle</title>
    <author><name>$commenter</name><uri>$commenterURL</uri></author>
    <link rel="alternate" type="text/html" href="$commentURLWithAnchor" />
    <id>$postID$formattedTimestamp</id>
    <published>$formattedTimestamp</published>
    <updated>$formattedTimestamp</updated>
    <summary>$commentInContext</summary>
    <content type="html"><![CDATA[$comment]]></content>
    </entry>
    </feed>
    ENTRYENDS;

    if ($sFeed['updateNewest']) {
        $feedFilepath = $sFeed['commentFeedsDir'] . "/newest-comments.xml";
        if (!file_exists($feedFilepath)) {
            return false;
        }
        $feedContent = file_get_contents($feedFilepath);
        if (!$feedContent) {
            return false;
        }
        // If there are more than 10 items in the feed, delete the first (i.e.
        // the oldest) item
        if (substr_count($feedContent, "<entry>") > $sFeed['newestComments']) {
            $feedContent = preg_replace('/\R?<entry>[\s\S]+?<\/entry>\R?/m', "", $feedContent, 1);
        }
        if ($feedContent === null) {
            return false;
        }
        $feedContent = preg_replace('/^\s*<updated>.*$/m', "<updated>$formattedTimestamp</updated>", $feedContent, 1);
        if ($feedContent === null) {
            return false;
        }
        $feedContent = str_replace("</feed>", $newEntry, $feedContent);
        file_put_contents($feedFilepath, $feedContent);
    }

    if ($sFeed['updatePost']) {
        $feedFilename = "comments-" . $postID . ".xml";
        $feedFilepath = $sFeed['commentFeedsDir'] . "/" . $feedFilename;
        if (!file_exists($feedFilepath)) {
            return false;
        }
        $feedContent = file_get_contents($feedFilepath);
        if (!$feedContent) {
            return false;
        }
        // We use regex to change the <updated>-tag of the feed with the date of the
        // update (which is the timestamp of the comment just added)
        $feedContent = preg_replace('/^\s*<updated>.*$/m', "<updated>$formattedTimestamp</updated>", $feedContent, 1);
        if ($feedContent === null) {
            return false;
        }
        // We replace the closing tag of the feed with the new item
        $feedContent = str_replace("</feed>", $newEntry, $feedContent);
        file_put_contents($feedFilepath, $feedContent);
    }

    return true;
}

/**
 * Check if the gravatar for the email provided exists (is registered by the
 * user)
 *
 * @param string $email The email to be checked in the gravatar database
 * @param bool $notYetHashed If true, the email is a normal email, if false, the
 * email has already been hashed (for the security purposes; sometimes we do not
 * need the email itself, but only the hash)
 * @return bool True if the gravatar is registered, false if it is not or if the
 * gravatar database gave unexpected response
 */
function gravatarExists($email, $notYetHashed)
{
    if ($notYetHashed) {
        $hashedEmail = md5(strtolower(trim($email)));
    } else {
        $hashedEmail = $email;
    }
    $url = "https://www.gravatar.com/avatar/" . $hashedEmail . "?d=404";
    $headers = @get_headers($url);
    if (!$headers) {
        return false;
    }
    if (!preg_match("|200|", $headers[0])) {
        return false;
    } else {
        return true;
    }
}

function getPostFullTitle($postURL)
{
    $html = file_get_contents($postURL);
    if (preg_match('/<h1[^>]*>(.*?)<\/h1>/is', $html, $matches)) {
        $postFullTitle = strip_tags($matches[1]);
    } else {
        return null;
    }
    return rawurlencode($postFullTitle);
}
