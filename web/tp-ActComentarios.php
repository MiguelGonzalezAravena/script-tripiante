<?php
include($_SERVER['DOCUMENT_ROOT'] . '/Settings.php');
include($_SERVER['DOCUMENT_ROOT'] . '/SSI.php');
global $context, $db_prefix, $modSettings;
if($context['user']['is_guest']) {
echo '<div class="noesta-am">Solo Usuarios REGISTRADOS pueden actualizar los comentarios.<br /><a href="/registrarse/">REG&Iacute;STRATE</a> - <a href="/ingresar/">CON&Eacute;CTATE</a></div>
';
} elseif($context['user']['is_logged']) {
$request = mysql_query("
SELECT c.ID_COMMENT, c.ID_TOPIC AS ID_TOPIC2, c.ID_MEMBER AS ID_MEMBER2, m.ID_MEMBER, m.realName,
t.ID_TOPIC, b.ID_BOARD, t.ID_BOARD, b.description, m2.subject, m2.ID_TOPIC, m2.subject AS subject2
FROM ({$db_prefix}comments AS c, {$db_prefix}members AS m, {$db_prefix}topics AS t, {$db_prefix}boards as b, {$db_prefix}messages AS m2)
WHERE c.ID_TOPIC = t.ID_TOPIC
AND c.ID_MEMBER = m.ID_MEMBER
AND b.ID_BOARD = t.ID_BOARD
AND m2.ID_TOPIC = t.ID_TOPIC
ORDER BY c.ID_COMMENT DESC
LIMIT " . $modSettings['number_comments']);
while ($row = mysql_fetch_assoc($request)) {
$ID_COMMENT	=	$row['ID_COMMENT'];
$ID_TOPIC	=	$row['ID_TOPIC2'];
$ID_MEMBER	=	$row['ID_MEMBER2'];
$realName	=	$row['realName'];
$description	=	$row['description'];
$subject	=	htmlentities(ssi_reducir($row['subject']));
echo '<font class="size11" ><b><a title="" href="/perfil/' . $realName . '">' . $realName . '</a></b> - <a title="' . $subject . '"  href="/post/' . $ID_TOPIC . '/' . $description . '/' . ssi_amigable($subject) . '.html#cmt_' . $ID_COMMENT . '">' . $subject . '</a></font><br style="margin:0px;padding:0px;" />
';
}
}
?>