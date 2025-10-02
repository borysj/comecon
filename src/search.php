---
layout: null
---
<?php
require "{{ site.dir_with_data }}/settings.php";
include $messages;

/**
 * Search through files in a given directory looking for a phrase
 *
 * @param string $searchDir The path to the directory to be searched through
 * @param string $searchString The phrase that we are searching for
 * @param string $pattern The regex name pattern of every relevant file in the
 * directory, used to extract the date and the title
 * @param string $trailingCharacters The number of trailing characters to be
 * removed from the title
 * @return array $searchResults An array of strings with date, link and title,
 * one element for each file where the phrase has been found
 */
function searchThroughFiles($searchDir, $searchString, $pattern, $trailingChars) {
    $searchResults = [];
    // Try to open the directory
    if ($handle = opendir($searchDir)) {
        // Iterate through all files in the directory
        while(false !== ($filename = readdir($handle))) {
            if ($filename != "." && $filename != "..") {
                $filePath = $searchDir . '/' . $filename;
                // Extra check: Confirm that the filepath is real
                if (is_file($filePath)) {
                    $fileContent = file_get_contents($filePath);
                    // Look for the phrase in the file
                    if (stripos($fileContent, $searchString) !== false) {
                        // If found, extract the link, the date and the title
                        // from the filename using the regex pattern
                        if (preg_match($pattern, $filename, $matches)) {
                            $link = "{{ site.url }}/" . $matches[1] . "/" . $matches[2] . "/" . $matches[3] . "/" . $matches[4];
                            $date = $matches[1] . "/" . $matches[2] . "/" . $matches[3];
                            $title = substr($filename, 11, $trailingChars);  // 11 because the leading YYYY-MM-DD is 10 characters long
                            $searchResults[] = "<b>$date:</b> <a href=\"$link\">$title</a>";
                        } } } } }
        closedir($handle);
    }
    return $searchResults;
}

/** Save the phrase that we search for in a file, for statistical purposes
 *
 * @param string $searchString The phrase that we look for
 * @return void
 */
function saveSearchString($searchString) {
    global $settings;
    date_default_timezone_set($timezone);
    $currentDateTime = date($timestamp);
    file_put_contents($searchQueriesRecord, $currentDateTime . "<|>" . $searchString . PHP_EOL, FILE_APPEND | LOCK_EX);
    return;
}

$searchString = htmlspecialchars($_POST["searchPhrase"], ENT_QUOTES);
if (empty($searchString)) {
    exit($exitmsg_noSearchPhrase);
}

// If the search string starts with "123", ignore it and do not save the search
// string. This can be used by the site admin who do not want to pollute the
// query file with their queries
if (str_starts_with($searchString, '123')) {
    $searchString = substr($searchString, 3);
} else {
    saveSearchString($searchString);
}

$patternPost = "/(\d{4})-(\d{2})-(\d{2})-([^.]*)/";
$patternComment = "/(\d{4})-(\d{2})-(\d{2})-(.*)-COMMENTS.txt/";

// -4 to cut the final .txt, -13 to cut the final -COMMENTS.txt from the
// relevant filename
$searchResultsPosts = searchThroughFiles($searchDataDirectory, $searchString, $patternPost, -4);
$searchResultsComments = searchThroughFiles($commentsDir, $searchString, $patternComment, -13);
rsort($searchResultsPosts);
rsort($searchResultsComments);
$m = count($searchResultsPosts);
$n = count($searchResultsComments);

echo "<html><body>\n";
echo "<h1>$label_searchTitle: $searchString</h1>\n";
echo "<h2>$label_searchResult: $m</h2>\n";
echo "<p>\n";
foreach ($searchResultsPosts as $searchResult) {
    echo $searchResult . "<br><br>\n";
}
echo "</p>";

echo "<h2>$label_searchResultComments: $n</h2>\n";
echo "<p>\n";
foreach ($searchResultsComments as $searchResult) {
    echo $searchResult . "<br><br>\n";
}
echo "</p></body></html>";
