<?php
@require_once($_SERVER['DOCUMENT_ROOT'] . '/Settings.php');
@require_once($_SERVER['DOCUMENT_ROOT'] . '/SSI.php');

global $db_prefix, $boardurl, $mbname;

$request = db_query("
  SELECT realName, posts
  FROM {$db_prefix}members
  ORDER BY posts DESC
  LIMIT 0, 25", __FILE__, __LINE__);

$context['rssuser'] = array();

while ($row = mysqli_fetch_assoc($request)) {
  $row['realName'] = parse_bbc($row['realName'], 1, $row['posts']); 
  $row['realName'] = strtr($func['substr'](str_replace('<br />', "\n", $row['realName']), 0, 400 - 3), array("\n" => '<br />'));

  $context['rssuser'][] = array(
    'posts' => $row['posts'],
    'realName' => censorText($row['realName']),
  );
}

mysqli_free_result($request);

$title = $mbname . ' - Top poster';
$description = 'Usuarios con m&aacute;s posts en ' . $mbname;

echo '<?xml version="1.0" encoding="UTF-8"?>
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
      <link>' . $boardurl . '</link>
      <description>' . $description . '</description>';

foreach ($context['rssuser'] as $row) {
  $memberName = censorText($row['realName']);
  $link = $boardurl . '/perfil/' . $memberName;

  echo '
    <item>
      <title><![CDATA[' . $memberName . '&nbsp;(' . $row['posts'] . ')]]></title>
      <link>' . $link . '</link>
      <description><![CDATA[]]></description>
      <comments>' . $link . '</comments>
    </item>';
}

echo '
    </channel>
  </rss>';

?>