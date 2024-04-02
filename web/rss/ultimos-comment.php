<?php
@require_once($_SERVER['DOCUMENT_ROOT'] . '/Settings.php');
@require_once($_SERVER['DOCUMENT_ROOT'] . '/SSI.php');

$conexion = mysql_connect($db_server, $db_user, $db_passwd) OR die("No se puedo conectar a la BDD ".mysql_error()."...!!!");mysql_select_db($db_name, $conexion) OR die("No se pudo seleccionar la BDD ".mysql_error()."...!!!");
$comment=mysql_query("SELECT *FROM ({$db_prefix}comments AS c, {$db_prefix}members AS mem, {$db_prefix}messages AS m)WHERE c.ID_MEMBER = mem.ID_MEMBER
AND m.ID_TOPIC = c.ID_TOPIC
GROUP BY c.ID_TOPICORDER BY c.ID_COMMENT DESC
LIMIT 25");
$context['comment'] = array();while ($row = mysqli_fetch_assoc($comment)){$row['comment'] = parse_bbc($row['comment'], 1, $row['ID_TOPIC']); censorText($row['comment']);censorText($row['subject']);$row['comment'] = strtr($func['substr'](str_replace('<br />', "\n", $row['comment']), 0, 400 - 3), array("\n" => '<br />'));$context['comment'][] = array('comment' => $row['comment'],'titulo' => $row['subject'],'nom-user' => $row['realName'],'id_comment' => $row['id_coment'],'id' => $row['id_post'],
);
}
mysqli_free_result($comment);

$contando=1;
echo'<?xml version="1.0" encoding="UTF-8"?>
<rss version="0.92" xml:lang="spanish">
	<channel>
	 <image>
    <url>'.$boardurl.'/images/rss.png</url>
    <title>'.$mbname.' - Comentarios de los post</title>
    <link>'.$boardurl.'</link>

    <width>111</width>
    <height>32</height>
    <description>Ultimos 25 comentarios de los post en '.$mbname.'</description>
  </image>
	    <title>'.$mbname.' - Comentarios de los post</title>
    <link>'.$boardurl.'</link>
    <description>Ultimos 25 comentarios de los post en '.$mbname.'</description>';
foreach($context['comment'] AS $comment){

echo '<item>
			<title><![CDATA['. $comment['nom-user'] .' - '. $comment['titulo'] .']]></title>
			<link>'.$boardurl.'/post/'. $comment['id'] .'#cmt_'. $comment['id_comment'] .'</link>
			<description><![CDATA['. $comment['comment'] .']]>
			</description>
			<comments>'.$boardurl.'/post/'. $comment_img['id'] .'#comentar</comments>
		</item>';

		}

echo'	</channel>
</rss>
';  ?> 