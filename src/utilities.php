---
layout: null
---
<?php

/**
 * Prepares and sanitizes a string.
 *
 * @param string $string The string to be prepared
 * @param int $length The string will be trimmed to this length
 * @param bool $breaklines If true, replace the newlines and breaklines with
 * <br />. If false, remove them altogether.
 * @param bool $markdown If false, do nothing. If true, convert Markdown to HTML.
 * @param bool $http If true, add http:// to the beginning of the string if it
 * is not already present.
 * @return string $string The prepared string
 */
function prepareString($string, $length, $breaklines, $markdown, $http)
{
    if (empty($string)) {
        return "";
    }
    $string = htmlspecialchars($string, ENT_QUOTES);
    $string = trim($string);
    $string = substr($string, 0, $length);
    if ($breaklines) {
        $string = str_replace(array("\r\n", "\r", "\n"), "<br/>", $string);
    } else {
        $string = str_replace(array("\r\n", "\r", "\n"), "", $string);
    }
    if ($markdown) {
        $string = preg_replace('/`(.*?)`/', '<code>$1</code>', $string) ?? $string;
        $string = preg_replace('/\[(.*?)\]\((https?:\/\/)?(.*?)\)/', '<a href="http://$3">$1</a>', $string) ?? $string;
        $string = preg_replace('/\*\*(.*?)\*\*/', '<b>$1</b>', $string) ?? $string;
        $string = preg_replace('/\*(.*?)\*/', '<i>$1</i>', $string) ?? $string;
    }
    if ($http && stripos($string, "http") !== 0) {
        $string = "http://" . $string;
    }
    return $string;
}
