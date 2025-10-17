<?php

require_once __DIR__ . "/../private/settings.php";
require_once __DIR__ . "/../src/" . $settings['general']['messages'];

/**
 * Search through files in a given directory looking for a phrase
 *
 * @param string $searchDir The path to the directory to be searched through
 * @param string $searchString The phrase that we are searching for
 * @return array<string> An array of strings with links,
 * one element for each file where the phrase has been found
 */
function searchThroughFiles($searchDir, $searchString)
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
                    // The first line in the post data file or the search file
                    // stores the post URL.  We have to save it, and then look
                    // for the search phrase in the rest of it.
                    $firstLineEnd = strpos($fileContent, PHP_EOL);
                    if ($firstLineEnd !== false) {
                        $firstLine = substr($fileContent, 0, $firstLineEnd);
                        $restOfFile = substr($fileContent, $firstLineEnd + strlen(PHP_EOL));
                        if (stripos($restOfFile, $searchString) !== false) {
                            $searchResults[] = "<a href=\"$firstLine\">$firstLine</a>";
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
} else if (str_starts_with($searchString, '  ')) {
    $searchString = substr($searchString, 2);
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

$searchResultsPosts = searchThroughFiles($settings['search']['searchableDir'], $searchString);
$searchResultsComments = searchThroughFiles($settings['general']['commentsDir'], $searchString);
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
