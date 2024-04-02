<?php

function getContent($url) {
  $ch = curl_init();
  curl_setopt ($ch, CURLOPT_URL, $url);
  curl_setopt ($ch, CURLOPT_HEADER, 0);
  ob_start();
  curl_exec ($ch);
  curl_close ($ch);

  $string = ob_get_contents();

  ob_end_clean();

  return $string;    
}
function getYoutubeToken($id) {
  $path = 'http://www.youtube.com/get_video_info?';
  $cont = getContent($path . '&video_id=' . $id);

  parse_str($cont, $opts);

  return $opts['token'];
}

$videoItem = trim($_GET['item']);
$videoType = '';
$videoPath = 'http://www.youtube.com/get_video.php';

if ($_GET['type'] != '0') {
  $videoType = '&fmt=' . $_GET['type'];
}

if ($videoItem != '') {
  $videoTokn = getYoutubeToken($videoItem);
  header('Location: ' . $videoPath . '?video_id=' . $videoItem . '&t=' . $videoTokn . $videoType);
  exit;
}

header('Location: ' . $_SERVER['HTTP_REFERER']);
?>