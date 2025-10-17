<?php

require_once __DIR__ . "/../private/settings.php";
require_once __DIR__ . "/../src/" . $settings['general']['messages'];

/**
 * Search through files in a given directory looking for a phrase
 *
 * @param string $searchDir The path to the directory to be searched through
 * @param string $searchString The phrase that we are searching for
 * @param string $pattern The regex name pattern of every relevant file in the
 * directory, used to extract the date and the title
 * @param int $trailingChars The number of trailing characters to be
 * removed from the title
 * @return array<string> An array of strings with date, link and title,
 * one element for each file where the phrase has been found
 */
function searchThroughFiles($searchDir, $searchString, $pattern, $trailingChars)
{
    $searchResults = [];
    // Try to open the directory
    if ($handle = opendir($searchDir)) {
        // Iterate through all files in the directory
        while (false !== ($filename = readdir($handle))) {
            if ($filename != "." && $filename != "..") {
                $filePath = $searchDir . '/' . $filename;
                // Extra check: Confirm that the filepath is real
                if (is_file($filePath)) {
                    $fileContent = file_get_contents($filePath);
                    if (!$fileContent) {
                        exit(EXITMSG_FILEUNREADABLE . " ::: " . __FILE__ . ":" . __LINE__);
                    }
                    // Look for the phrase in the file
                    if (stripos($fileContent, $searchString) !== false) {
                        // If found, extract the link, the date and the title
                        // from the filename using the regex pattern
                        if (preg_match($pattern, $filename, $matches)) {
                            $link = $settings['general']['siteURL'] . "/" .
                                $matches[1] . "/" . $matches[2] . "/" . $matches[3] . "/" . $matches[4];
                            $date = $matches[1] . "/" . $matches[2] . "/" . $matches[3];
                            // 11 because the leading YYYY-MM-DD is 10 characters long
                            $title = substr($filename, 11, $trailingChars);
                            $searchResults[] = "<b>$date:</b> <a href=\"$link\">$title</a>";
                        }
                    }
                }
            }
        }
        closedir($handle);
    }
    return $searchResults;
}

/** Save the phrase that we search for in a file, for statistical purposes
 *
 * @param string $searchString The phrase that we look for
 * @return void
 */
function saveSearchString($searchString, $sTimezone, $sTimestamp, $sRecord)
{
    date_default_timezone_set($sTimezone);
    $currentDateTime = date($sTimestamp);
    file_put_contents($sRecord, $currentDateTime . "<|>" . $searchString . PHP_EOL, FILE_APPEND | LOCK_EX);
    return;
}

if (!is_string($_POST["searchPhrase"])) {
    exit(EXITMSG_NOTSTRING . " ::: " . __FILE__ . ":" . __LINE__);
}
$searchString = htmlspecialchars($_POST["searchPhrase"], ENT_QUOTES);
if (empty($searchString)) {
    exit(EXITMSG_NOSEARCHPHRASE . " ::: " . __FILE__ . ":" . __LINE__);
}

// If the search string starts with "123", ignore it and do not save the search
// string. This can be used by the site admin who do not want to pollute the
// query file with their queries
if (str_starts_with($searchString, '123')) {
    $searchString = substr($searchString, 3);
    $saveQuery = false;
} else if (str_starts_with($searchString, '  ') {
    $searchString = substr($searchString, '  ');
    $saveQuery = true;
} else {
    exit(EXITMSG_BADCAPTCHA . " ::: " . __FILE__ . ":" . __LINE__);
}

if ($saveQuery) {
    saveSearchString(
        $searchString,
        $settings['save']['timezone'],
        $settings['save']['timestamp'],
        $settings['search']['searchQueriesRecord']
    );
}

$patternPost = "/(\d{4})-(\d{2})-(\d{2})-([^.]*)/";
$patternComment = "/(\d{4})-(\d{2})-(\d{2})-(.*)-COMMENTS.txt/";

// -4 to cut the final .txt, -13 to cut the final -COMMENTS.txt from the
// relevant filename
$searchResultsPosts = searchThroughFiles($settings['search']['searchDataDirectory'], $searchString, $patternPost, -4);
$searchResultsComments = searchThroughFiles($settings['general']['commentsDir'], $searchString, $patternComment, -13);
rsort($searchResultsPosts);
rsort($searchResultsComments);
$m = count($searchResultsPosts);
$n = count($searchResultsComments);

echo "<html><body>\n";
echo "<h1>" . LABEL_SEARCHTITLE . ": $searchString</h1>\n";
echo "<h2>" . LABEL_SEARCHRESULT . ": $m</h2>\n";
echo "<p>\n";
foreach ($searchResultsPosts as $searchResult) {
    echo $searchResult . "<br><br>\n";
}
echo "</p>";

echo "<h2>" . LABEL_SEARCHRESULTCOMMENTS . ": $n</h2>\n";
echo "<p>\n";
foreach ($searchResultsComments as $searchResult) {
    echo $searchResult . "<br><br>\n";
}
echo "</p></body></html>";
