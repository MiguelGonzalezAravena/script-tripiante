<?php
@require_once($_SERVER['DOCUMENT_ROOT'] . '/Settings.php');
@require_once($_SERVER['DOCUMENT_ROOT'] . '/SSI.php');

global $db_prefix, $boardurl, $mbname;

$request = db_query("
  SELECT COUNT(p.ID_MEMBER) AS cuenta, p.ID_PICTURE, mem.ID_MEMBER, mem.memberName, mem.realName, p.ID_MEMBER
  FROM ({$db_prefix}gallery_pic AS p, {$db_prefix}members AS mem)
  WHERE p.ID_MEMBER = mem.ID_MEMBER
  GROUP BY mem.ID_MEMBER DESC
  ORDER BY cuenta DESC
  LIMIT 0, 25", __FILE__, __LINE__);

$context['imagenuser'] = array();

while ($row = mysqli_fetch_assoc($request)) {
  $context['imagenuser'][] = array(
    'realName' => $row['realName'],
    'memberName' => $row['memberName'],
    'cuenta' => $row['cuenta'],
  );
}

mysqli_free_result($request);

$title = $mbname . ' - Usuarios con m&aacute;s im&aacute;genes';
$description = '25 Usuarios con m&aacute;s im&aacute;genes en ' . $mbname;

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

foreach ($context['imagenuser'] as $row) {
  $link = $boardurl . '/perfil/' . censorText($row['memberName']);

  echo '
    <item>
      <title><![CDATA[' . censorText($row['realName']) . '&nbsp;(' . $row['cuenta'] . ')]]></title>
      <link>' . $link . '</link>
      <description><![CDATA[]]></description>
      <comments>' . $link . '</comments>
    </item>';
}

echo '
    </channel>
  </rss>';

?>