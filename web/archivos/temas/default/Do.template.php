<?php

function template_postagregado()
{
	global $settings, $db_prefix, $context, $ID_MEMBER;
	
$idpost	=	(int) htmlentities(addslashes($_REQUEST['idpost']));	

$request = db_query("
SELECT m.ID_TOPIC, m.subject, m.ID_BOARD, b.ID_BOARD, b.description, m.ID_MEMBER
FROM ({$db_prefix}messages as m, {$db_prefix}boards as b)
WHERE m.ID_TOPIC = {$idpost}
AND m.ID_BOARD = b.ID_BOARD
LIMIT 1", __FILE__, __LINE__);
$row	=	mysql_fetch_assoc($request);
if($ID_MEMBER != $row['ID_MEMBER']) {
fatal_error('No agregastes nada.-', false);
} else {

echo '
<div align="center"><div class="box_errors"><div class="box_title" style="width: 388px"><div class="box_txt box_error" align="left">Felicitaciones</div><div class="box_rss"><img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width:14px;height:12px;" border="0" /></div></div><div class="windowbg" style="width:380px;padding:4px;"><br />Tu post "<b>';
echo $row['subject'];
echo '</b>" ha sido agregado correctamente.-<br /><br /><input class="login" style="font-size: 11px;" type="submit" title="Ir al post" value="Ir al post" onclick="location.href=\'/post/', $row['ID_TOPIC'], '/', $row['description'], '/', ssi_amigable($row['subject']),'.html\'" /> <input class="login" style="font-size: 11px;" type="submit" title="Ir a la P&aacute;gina principal" value="Ir a la P&aacute;gina principal" onclick="location.href=\'/\'" /><br /><br /></div></div><br /></div><div style="clear:both"></div></div>
';

}

}

function template_posteditado()
{

$idpost = (int) htmlentities(addslashes($_GET['idpost']));	
	
 $request = db_query("
SELECT ID_TOPIC, subject
FROM {$db_prefix}messages
WHERE ID_TOPIC = $idpost
ORDER BY subject ASC
LIMIT 1", __FILE__, __LINE__);
	$context['post1'] = array();
	while ($row = mysql_fetch_assoc($request))
		$context['post1'][] = array(
			'subject' => $row['subject'],
			);
	mysql_free_result($request);

echo'<div align="center">
<div class="box_errors">
<div class="box_title" style="width: 390px"><div class="box_txt box_error" align="left">Felicitaciones</div>
<div class="box_rss"><img  src="/Themes/default/images/blank.gif" style="width: 14px; height: 12px;" border="0"></div></div>
<div class="windowbg" style="width: 388px; font-size: 12px;">
		<br>
    Tu post "<b>'; foreach ($context['post1'] AS $npost)echo''.$npost['subject'].'';
  echo'</b>" ha sido editado correctamente.
  <br>
		<br>
	     <input class="login" style="font-size: 11px;" type="submit" title="Ir al post" value="Ir al post" onclick="location.href=\'?topic='.$idpost.'/\'" /> <input class="login" style="font-size: 11px;" type="submit" title="Ir a la P&aacute;gina principal" value="Ir a la P&aacute;gina principal" onclick="location.href=\'/\'" /><br><br></div></div></div>';

}

function template_eliminarc()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $db_prefix, $user_info, $board, $query_this_board, $func;

$ID_TOPIC	=	(int) htmlentities(addslashes($_POST['ID_TOPIC']));
$userid		=	(int) htmlentities(addslashes($_POST['userid']));
$memberid	=	(int) htmlentities(addslashes($_POST['memberid']));
if ($userid = $memberid || $context['allow_admin']) {
	if(!empty($_POST['campos'])) {
	$aLista=array_keys($_POST['campos']);
	db_query("DELETE FROM {$db_prefix}comments WHERE ID_COMMENT IN (".implode(',',$aLista).")", __FILE__, __LINE__);
	}
}
Header("Location: {$_SERVER['HTTP_REFERER']}");

}

function template_eliminarci()
{
	global $context, $db_prefix;

$idimg	=	(int) htmlentities(addslashes($_POST['idimg']));
	if(!empty($_POST['campos'])) 	{
	$aLista=array_keys($_POST['campos']);
	mysql_query("DELETE FROM {$db_prefix}gallery_comment WHERE ID_COMMENT IN (" . implode(',', $aLista) . ")");
	}
Header("Location: {$_SERVER['HTTP_REFERER']}");

}


function template_enviardenuncia()
{
global $context, $settings, $options, $txt, $scripturl, $modSettings;
global $db_prefix, $user_info, $scripturl, $modSettings, $board;
global $query_this_board, $func;


$ID_TOPIC2	=	(int) $_POST['ID_TOPIC'];
$ID_MEMBER2	=	$context['user']['id'];
$comlimpito	=	$_POST['comentario'];
$comentario	=	strip_tags($comlimpito);
$razon		=	$_POST['razon'];
$tipo		=	$_POST['tipo'];

if(!empty($ID_TOPIC2) && !empty($comlimpito)  && !empty($razon)  && !empty($tipo))
{ 
	$errorr = db_query("
				SELECT *
				FROM {$db_prefix}denunciations
				WHERE
					ID_MEMBER = $ID_MEMBER2 AND
					ID_TOPIC = $ID_TOPIC2 AND
					TYPE = '$tipo'
				LIMIT 1", __FILE__, __LINE__);
	$yadio = mysql_num_rows($errorr) != 0 ? true : false;
	mysql_free_result($errorr);
if ($yadio)
    	fatal_error('Ya has denunciado este post.', false);


Header("Location: /denuncia/enviada/");
mysql_query("INSERT INTO {$db_prefix}denunciations
			(ID_TOPIC, ID_MEMBER, reason, comment, TYPE)
			VALUES ('$ID_TOPIC2', '$ID_MEMBER2', '$razon', '$comentario', '$tipo')");
Header("Location: /denuncia/enviada/");
}
}

function template_eliminarres()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $db_prefix, $user_info, $board, $query_this_board, $func;

$userid		=	htmlentities(addslashes($_POST['userid']));
$memberid	=	htmlentities(addslashes($_POST['memberid']));
if ($userid = $memberid || $context['allow_admin']) {
	if(!empty($_POST['campos'])) 	{
	$aLista=array_keys($_POST['campos']);
	db_query("DELETE FROM {$db_prefix}community_comments WHERE ID_COMMENT IN (".implode(',',$aLista).")", __FILE__, __LINE__);
	}
}
Header("Location: {$_SERVER['HTTP_REFERER']}");

}

function template_comunidadagregada()
{
	global $context, $boardurl, $settings, $txt, $modSettings, $db_prefix;

$id	 =	htmlentities(addslashes($_GET['id']));
$context['page_title'] = $txt[18];

	$dbresult3 = db_query("
        SELECT friendly_url
		FROM {$db_prefix}communities
		WHERE friendly_url = '$id'
		LIMIT 1", __FILE__, __LINE__);
	$row = mysql_fetch_assoc($dbresult3);
	$context['crearcomunidad'] = array(
		'friendly_url' => $row['friendly_url'],
	);
	mysql_free_result($dbresult3);
echo '<div align="center"><div class="box_errors"><div class="box_title" style="width: 388px"><div class="box_txt box_error" align="left">&iexcl;Felicidades!</div><div class="box_rss"><img alt="" src="', $settings['images_url'], '/blank.gif" style="width:14px;height:12px;" border="0" /></div></div><div class="windowbg" style="width:380px;padding:4px;"><br />El mundo entero est&aacute; ante la presencia de una nueva comunidad. &iexcl;Felicitaciones!<br /><br /><input class="login" style="font-size: 11px;" type="submit" title="&iexcl;Ir a mi nueva comunidad!" value="&iexcl;Ir a mi nueva comunidad!" onclick="location.href=\'/comunidades/', $context['crearcomunidad']['friendly_url'], '/\'" /><br /><br /></div></div><br /></div><div style="clear:both"></div>';
}

function template_temaagregado()
{
	global $context, $boardurl, $settings, $txt, $modSettings, $db_prefix;

$id	 =	htmlentities(addslashes($_GET['id']));
$context['page_title'] = $txt[18];

$request	 =	db_query("SELECT * FROM {$db_prefix}communities AS c, {$db_prefix}community_topic AS ct WHERE c.ID_COMMUNITY = ct.ID_COMMUNITY AND ct.ID_TOPIC = $id ", __FILE__, __LINE__);
$row	=	mysql_fetch_assoc($request);

echo '<div align="center"><div class="box_errors"><div class="box_title" style="width: 388px"><div class="box_txt box_error" align="left">&iexcl;Atenci&oacute;n!</div><div class="box_rss"><img alt="" src="', $settings['images_url'], '/blank.gif" style="width:14px;height:12px;" border="0" /></div></div><div class="windowbg" style="width:380px;padding:4px;"><br />El nuevo tema fue agregado a la comunidad.-<br /><br /><input class="login" style="font-size: 11px;" type="submit" title="Ir al tema" value="Ir al tema" onclick="location.href=\'/comunidades/', $row['friendly_url'], '/', $row['ID_TOPIC'], '/', ssi_amigable($row['subject']), '.html\'" /><br /><br /></div></div><br /></div><div style="clear:both"></div>';

}

function template_manual_above(){}
function template_manual_below(){}
function template_intro(){Header("Location: /");}
?>