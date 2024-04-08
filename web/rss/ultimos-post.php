<?php
@require_once($_SERVER['DOCUMENT_ROOT'] . '/Settings.php');
@require_once($_SERVER['DOCUMENT_ROOT'] . '/SSI.php');

global $db_prefix, $boardurl, $mbname;

function reemplazar($valor) {
  $valor = str_replace('<br />', "\n", $valor);

  return $valor;
}

$existe = db_query("
  SELECT ID_TOPIC, ID_MEMBER, subject, body, hiddenOption, ID_MSG
  FROM {$db_prefix}messages
  GROUP BY ID_TOPIC
  ORDER BY ID_TOPIC DESC
  LIMIT 0, 25", __FILE__, __LINE__);

$context['rssuser'] = array();

while ($row = mysqli_fetch_assoc($existe)) {
  $row['body'] = reemplazar(parse_bbc($row['body'], 1, $row['ID_MSG']));
  censorText($row['body']);
  censorText($row['subject']);
  $context['rssuser'][] = array(
    'id' => $row['ID_TOPIC'],
    'titulo' => $row['subject'],
    'body' => $row['body'],
    'postprivado' => $row['hiddenOption'],
  );
}

mysqli_free_result($existe);

$title = $mbname . ' - &Uacute;ltimos Post';
$description = '&Uacute;ltimos 10 post de ' . $mbname;

echo '<?xml version="1.0" encoding="UTF-8" ?>
  <rss version="0.92" xml:lang="es-es">
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

foreach($context['rssuser'] as $row) {
  $link = $boardurl .  '/post/' . $row['id'];

  echo '
    <item>
      <title><![CDATA['. htmlentities($row['titulo'], ENT_QUOTES, 'UTF-8') .']]></title>
      <link>' . $link . '</link>
      <description><![CDATA[';

  if ($context['user']['is_guest']) {
    if ($row['postprivado'] == 1) {
      echo '
        <center><i>Este es un post privado, para verlo debes iniciar sesi&oacute;n. - ' . $mbname . '</i></center>
        <br />';
    }
  } else if ($row['postprivado'] == 0) {
    echo htmlentities(addslashes($row['body']), ENT_QUOTES, 'UTF-8');
  }

  if ($context['user']['is_logged']) {
    echo $row['body'];
  }

  echo ']]>
      </description>
      <comments>' . $link . '#quickreply</comments>
    </item>';
}

echo '
    </channel>
  </rss>';

?>