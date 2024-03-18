<?php
require_once('_simplehtmldom/vendor/simplehtmldom/simplehtmldom/simple_html_dom.php');

if (isset($_GET['selectedOption'])) {
    $artist = explode('|', $_GET['selectedOption']);
    $artist = strtolower(str_replace(' ', '-', $artist[1]));
    $composition = str_replace(' ', '-', strtolower($_GET['inputValue']));

    $html = file_get_html("http://www.notediscover.com/song/".$artist. "-" . $composition);
    $title = $html->find('h1');

   $bpm = substr(strip_tags($title[1]), strpos($stringWithoutTags, "BPM of ") + strlen("BPM of "));
   $tonality = substr(strip_tags($title[2]), strpos($stringWithoutTags, "Key of ") + strlen("Key of "));

   echo json_encode(array("valueForInput" => $bpm, "selectedValue" => $tonality));

} else {
    echo "http://www.notediscover.com/song/".$artist. "-" . $composition;
}
?>