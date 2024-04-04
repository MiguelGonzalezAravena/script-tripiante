<?php
@require_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');

function reemplazar($valor) {
$valor = str_replace('<br />', "\n", $valor);
return $valor;
}

$existe = mysql_query("SELECT m.ID_TOPIC, m.ID_MEMBER, m.subject, m.body, m.hiddenOption, m.ID_MSG FROM {$db_prefix}messages AS m GROUP BY m.ID_TOPIC
ORDER BY m.ID_TOPIC DESC LIMIT 0, 25");
$context['rssuser'] = array();
while ($row = mysqli_fetch_assoc($existe)){
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
echo '<?xml version="1.0" encoding="UTF-8" ?>
<rss version="0.92" xml:lang="es-es">
<channel>
<image><url>/images/rss.png</url>
<title>'.$mbname.' - Ultimos Post</title>
<link>'.$boardurl.'/</link>
<width>111</width>
<height>32</height>
<description>Ultimos 10 post de '.$mbname.'</description>
</image>
<title>'.$mbname.' - Ultimos Post</title>
<link>'.$boardurl.'/</link>
<description>Ultimos 10 post de '.$mbname.'</description>';
foreach($context['rssuser'] AS $rssuser){
echo '<item>
<title><![CDATA['. htmlentities($rssuser['titulo'], ENT_QUOTES, "UTF-8") .']]></title>
<link>'.$boardurl.'/?topic='. $rssuser['id'] .'</link>
<description><![CDATA[';
if($context['user']['is_guest']) {
if($rssuser['postprivado']=='1') echo '<center><i>Este es un post privado, para verlo debes autentificarte. - ' . $mbname . '</i></center><br />';
} elseif($rssuser['postprivado']=='0') {
echo htmlentities($rssuser['body']);
}
if($context['user']['is_logged']) {
echo $rssuser['body'];
}
echo ']]>
</description>
<comments>', $boardurl,  '/post/'. $rssuser['id'] .'#quickreply</comments>
</item>';
}
echo '</channel></rss>';
?>