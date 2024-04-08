<?php
@require_once($_SERVER['DOCUMENT_ROOT'] . '/Settings.php');
@require_once($_SERVER['DOCUMENT_ROOT'] . '/SSI.php');

global $db_prefix, $boardurl, $mbname;

$id = htmlentities(addslashes($_REQUEST['id']), ENT_QUOTES, 'UTF-8'); 

$request = db_query("
  SELECT *
  FROM ({$db_prefix}messages AS m, {$db_prefix}members AS mem, {$db_prefix}boards AS b)
  WHERE m.ID_MEMBER = mem.ID_MEMBER
  AND m.ID_BOARD = b.ID_BOARD
  AND mem.memberName = '$id'
  ORDER BY m.ID_TOPIC DESC
  LIMIT 0, 25", __FILE__, __LINE__);

$row = mysqli_fetch_assoc($request);
$memberName = censorText($row['memberName']);

$title = $mbname . ' - Post creados por el usuario: ' . $memberName;
$description = '&Uacute;ltimos 25 post creados por el usuario ' . $memberName . ' en ' . $mbname;

echo '<?xml version="1.0" encoding="UTF-8" ?>
  <rss version="0.92" xml:lang="spanish">
    <channel>
      <image>
      <url>' . $boardurl . '/images/rss.png</url>
      <title>' . $title . '</title>
      <link>' . $boardurl . '/</link>
      <width>111</width>
      <height>32</height>
      <description>' . $description . '</description>
    </image>
    <title>' . $title . '</title>
    <link>' . $boardurl . '/</link>
    <description>' . $description . '</description>';

while ($row = mysqli_fetch_assoc($request)) {
  $link = $boardurl . '/post/' . $row['ID_TOPIC'] . '/' . $row['description'] . '/' . ssi_amigable($row['subject']) . '.html';

  echo '
    <item>
      <title><![CDATA[' . htmlentities(addslashes($row['subject']), ENT_QUOTES, 'UTF-8') . ']]></title>
      <link>' . $link . '</link>
      <description><![CDATA[]]></description>
      <comments>' . $link . '</comments>
    </item>';
}

echo '
    </channel>
  </rss>';

?>