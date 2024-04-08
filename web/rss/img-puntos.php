<?php
@require_once($_SERVER['DOCUMENT_ROOT'] . '/Settings.php');

$request = db_query("
  SELECT points, ID_PICTURE, title
  FROM {$db_prefix}gallery_pic
  GROUP BY ID_PICTURE, title DESC
  ORDER BY points DESC
  LIMIT 0, 25", __FILE__, __LINE__);

$context['comment-img3'] = array();

while ($row = mysqli_fetch_assoc($request)) {
  $context['comment-img3'][] = array(
    'title' => $row['title'],
    'points' => $row['points'],
    'ID_PICTURE' => $row['ID_PICTURE']
  );
}

mysqli_free_result($request);

echo '<?xml version="1.0" encoding="UTF-8" ?>
  <rss version="0.92" xml:lang="spanish">
    <channel>
      <image>
        <url>' . $boardurl . '/images/rss.png</url>
        <title>' . $mbname . ' - Im&aacute;genes con m&aacute;s puntos</title>
        <link>' . $boardurl . '/</link>
        <width>111</width>
        <height>32</height>
        <description>25 im&aacute;genes con m&aacute;s puntos en ' . $mbname . '</description>
        </image>
      <title>' . $mbname . ' - Im&aacute;genes con m&aacute;s puntos</title>
      <link>' . $boardurl . '/</link>
      <description>25 im&aacute;genes con m&aacute;s puntos en ' . $mbname . '</description>';

foreach ($context['comment-img3'] as $row) {
  echo '
    <item>
      <title><![CDATA[' . htmlentities($row['title']) . '&nbsp;-&nbsp;(' . $row['points'] . ')]]></title>
      <link>' . $boardurl . '/imagenes/ver/' . $row['ID_PICTURE'] . '</link>
    </item>';
}

echo '
    </channel>
  </rss>';

?>