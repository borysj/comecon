---
layout: null
---
<?php
require "{{ site.dir_with_data }}/settings.php";

function searchThroughFiles($searchDir, $searchString, $pattern, $trailingChars) {
    $searchResults = [];
    if ($handle = opendir($searchDir)) {
        while(false !== ($filename = readdir($handle))) {
            if ($filename != "." && $filename != "..") {
                $filePath = $searchDir . '/' . $filename;
                if (is_file($filePath)) {
                    $fileContent = file_get_contents($filePath);
                    if (stripos($fileContent, $searchString) !== false) {
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

function saveSearchString($searchString) {
    global $timezone, $timestamp, $searchQueriesRecord;
    date_default_timezone_set($timezone);
    $currentDateTime = date($timestamp);
    file_put_contents($searchQueriesRecord, $currentDateTime . "<|>" . $searchString . PHP_EOL, FILE_APPEND | LOCK_EX);
    return;
}

$searchString = htmlspecialchars($_POST["searchPhrase"], ENT_QUOTES);
if (empty($searchString)) {
    exit("No phrase to search for");
}
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
echo "<h1>Searched phrase: $searchString</h1>\n";
echo "<h2>Number of posts with the phrase: $m</h2>\n";
echo "<p>\n";
foreach ($searchResultsPosts as $searchResult) {
    echo $searchResult . "<br><br>\n";
}
echo "</p>";

echo "<h2>Number of posts where the phrase is among the comments: $n</h2>\n";
echo "<p>\n";
foreach ($searchResultsComments as $searchResult) {
    echo $searchResult . "<br><br>\n";
}
echo "</p></body></html>";
