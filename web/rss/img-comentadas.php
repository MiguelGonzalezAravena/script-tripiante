<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/SSI.php');

global $db_prefix, $boardurl, $mbname;

$request = db_query("
  SELECT COUNT(c.ID_PICTURE) AS cuenta, p.ID_PICTURE, c.ID_PICTURE, p.title
  FROM ({$db_prefix}gallery_pic AS p, {$db_prefix}gallery_comment AS c)
  WHERE c.ID_PICTURE = p.ID_PICTURE
  GROUP BY p.ID_PICTURE DESC
  ORDER BY cuenta DESC
  LIMIT 0, 25", __FILE__, __LINE__);

$context['topimagenescom'] = array();

while ($row = mysqli_fetch_assoc($request)) {
  $context['topimagenescom'][] = array(
    'title' => ssi_reducir($row['title']),
    'cuenta' => $row['cuenta'],
    'ID_PICTURE' => $row['ID_PICTURE'],
  );
}

mysqli_free_result($request);

$title = $mbname . ' - Im&aacute;genes m&aacute;s comentadas';
$description = '25 im&aacute;genes m&aacute;s comentadas en ' . $mbname;

echo '<?xml version="1.0" encoding="UTF-8" ?>
  <rss version="0.92" xml:lang="spanish"><channel>
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

foreach ($context['topimagenescom'] as $row) {
  echo '
    <item>
      <title><![CDATA[' . htmlentities($row['title']) . ' - (' . $row['cuenta']  . ')]]></title>
      <link>' . $boardurl . '/imagenes/ver/' . $row['ID_PICTURE'] . '</link>
    </item>';
}

echo '
    </channel>
  </rss>';
?> 
