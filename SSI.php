<?php
if (defined('SMF'))
	return true;

define('SMF', 'SSI');

global $time_start, $maintenance, $msubject, $mmessage, $mbname, $language;
global $boardurl, $boarddir, $sourcedir, $webmaster_email, $cookiename;
global $db_server, $db_name, $db_user, $db_prefix, $db_persist, $db_error_send, $db_last_error;
global $db_connection, $modSettings, $context, $sc, $user_info, $topic, $board, $txt;

$ssi_magic_quotes_runtime = @get_magic_quotes_runtime();
@set_magic_quotes_runtime(0);
$time_start = microtime();

foreach (array('db_character_set') as $variable)
	if (isset($GLOBALS[$variable]))
		unset($GLOBALS[$variable]);

@require_once(dirname(__FILE__) . '/Settings.php');
$ssi_error_reporting = error_reporting(E_ALL);

if ($maintenance == 2 && (!isset($ssi_maintenance_off) || $ssi_maintenance_off !== true))
	die($mmessage);

if (substr($sourcedir, 0, 1) == '.' && substr($sourcedir, 1, 1) != '.')
	$sourcedir = dirname(__FILE__) . substr($sourcedir, 1);


@require_once($sourcedir . '/QueryString.php');
@require_once($sourcedir . '/Subs.php');
@require_once($sourcedir . '/Errors.php');
@require_once($sourcedir . '/Load.php');
@require_once($sourcedir . '/Security.php');

if (@version_compare(PHP_VERSION, '4.2.3') != 1)
	@require_once($sourcedir . '/Subs-Compat.php');

if (empty($db_persist))
	$db_connection = @mysql_connect($db_server, $db_user, $db_passwd);
else
	$db_connection = @mysql_pconnect($db_server, $db_user, $db_passwd);
if ($db_connection === false)
	return false;

if (strpos($db_prefix, '.') === false)
	$db_prefix = is_numeric(substr($db_prefix, 0, 1)) ? $db_name . '.' . $db_prefix : '`' . $db_name . '`.' . $db_prefix;
else
	@mysql_select_db($db_name, $db_connection);

reloadSettings();
cleanRequest();

if (empty($modSettings['rand_seed']) || mt_rand(1, 250) == 69) {
smf_seed_generator();
}

if (isset($_REQUEST['GLOBALS']) || isset($_COOKIE['GLOBALS']))
	die('Hacking attempt...');
elseif (isset($_REQUEST['ssi_theme']) && (int) $_REQUEST['ssi_theme'] == (int) $ssi_theme)
	die('Hacking attempt...');
elseif (isset($_COOKIE['ssi_theme']) && (int) $_COOKIE['ssi_theme'] == (int) $ssi_theme)
	die('Hacking attempt...');
elseif (isset($_REQUEST['ssi_layers'], $ssi_layers) && (@get_magic_quotes_gpc() ? stripslashes($_REQUEST['ssi_layers']) : $_REQUEST['ssi_layers']) == $ssi_layers)
	die('Hacking attempt...');
if (isset($_REQUEST['context']))
	die('Hacking attempt...');

define('WIRELESS', false);

if (isset($ssi_gzip) && $ssi_gzip === true && @ini_get('zlib.output_compression') != '1' && @ini_get('output_handler') != 'ob_gzhandler' && @version_compare(PHP_VERSION, '4.2.0') != -1)
	ob_start('ob_gzhandler');
else
	$modSettings['enableCompressedOutput'] = '0';

ob_start('ob_sessrewrite');

if (!headers_sent())
	loadSession();
else
{
	if (isset($_COOKIE[session_name()]) || isset($_REQUEST[session_name()]))
	{
		$temp = error_reporting(error_reporting() & !E_WARNING);
		loadSession();
		error_reporting($temp);
	}

	if (!isset($_SESSION['rand_code']))
		$_SESSION['rand_code'] = '';
	$sc = &$_SESSION['rand_code'];
}

unset($board);
unset($topic);
$user_info['is_mod'] = false;
$context['user']['is_mod'] = false;
$context['linktree'] = array();

loadUserSettings();
loadTheme(isset($ssi_theme) ? (int) $ssi_theme : 0);

if (isset($_REQUEST['ssi_ban']) || (isset($ssi_ban) && $ssi_ban === true))
	is_not_banned();

loadPermissions();

if (isset($ssi_layers))
{
	$context['template_layers'] = $ssi_layers;
	template_header();
}
else
	setupThemeContext();

if (isset($_SERVER['REMOTE_ADDR']) && !isset($_SERVER['is_cli']) && session_id() == '')
	trigger_error($txt['ssi_session_broken'], E_USER_NOTICE);

elseif (basename($_SERVER['PHP_SELF']) == 'SSI.php')
	die(sprintf('Hacking attempt...', $user_info['is_admin'] ? '\'' . addslashes(__FILE__) . '\'' : '\'SSI.php\''));

error_reporting($ssi_error_reporting);
@set_magic_quotes_runtime($ssi_magic_quotes_runtime);

return true;

function ssi_eliminar_respuesta()
{
	global $context, $txt, $scripturl, $db_prefix, $ID_MEMBER, $boardurl, $settings;

$id = (int) htmlentities(addslashes($_REQUEST['id']));
$request = db_query("
SELECT *
FROM ({$db_prefix}community_comments AS cc, {$db_prefix}community_topic AS ct, {$db_prefix}communities AS c)
WHERE cc.ID_COMMENT = $id
AND cc.ID_TOPIC = ct.ID_TOPIC
AND c.ID_COMMUNITY = ct.ID_COMMUNITY
", __FILE__, __LINE__);
$row = mysql_fetch_assoc($request);

if($context['allow_admin'] || $row['ID_MEMBER'] == $ID_MEMBER) {
db_query("DELETE FROM {$db_prefix}community_comments WHERE ID_COMMENT = " . $row['ID_COMMENT'] . " LIMIT 1", __FILE__, __LINE__);
redirectexit($boardurl . '/comunidades/' . $row['friendly_url'] . '/' . $row['ID_TOPIC'] . '/' . ssi_amigable($row['subject']) . '.html');
} else {
redirectexit($boardurl . '/comunidades/');
}
}


function ssi_eliminar_comunidad()
{
	global $context, $txt, $scripturl, $db_prefix, $ID_MEMBER, $boardurl, $settings;

$id = htmlentities(addslashes($_REQUEST['id']));
$request = db_query("
SELECT * FROM
{$db_prefix}communities
WHERE friendly_url = '$id'
", __FILE__, __LINE__);
$row = mysql_fetch_assoc($request);

$result = db_query("
SELECT cm.ID_MEMBER, cm.ID_COMMUNITY, cm.grade, c.friendly_url, c.ID_COMMUNITY, cm.grade
FROM ({$db_prefix}community_members AS cm, {$db_prefix}communities AS c)
WHERE cm.ID_MEMBER = $ID_MEMBER
AND cm.ID_COMMUNITY = c.ID_COMMUNITY
AND c.friendly_url = '$id'
LIMIT 1", __FILE__, __LINE__);
$context['usercomunidad'] = mysql_num_rows($result);
$row2 = mysql_fetch_assoc($result);
$context['rango'] = array(
'grade' => $row2['grade'],
);
mysql_free_result($result);
if($context['usercomunidad'] == 1 && $context['rango']['grade'] == 1 || $context['allow_admin']) {
db_query("DELETE FROM {$db_prefix}communities WHERE ID_COMMUNITY = " . $row['ID_COMMUNITY'] . " LIMIT 1", __FILE__, __LINE__);
db_query("DELETE FROM {$db_prefix}community_topic WHERE ID_COMMUNITY = " . $row['ID_COMMUNITY'] . " LIMIT 1", __FILE__, __LINE__);
db_query("DELETE FROM {$db_prefix}community_members WHERE ID_COMMUNITY = " . $row['ID_COMMUNITY'] . " LIMIT 1", __FILE__, __LINE__);
db_query("DELETE FROM {$db_prefix}community_user WHERE ID_COMMUNITY = " . $row['ID_COMMUNITY'] . " LIMIT 1", __FILE__, __LINE__);
db_query("DELETE FROM {$db_prefix}community_comments WHERE ID_COMMUNITY = " . $row['ID_COMMUNITY'] . " LIMIT 1", __FILE__, __LINE__);
redirectexit($boardurl . '/comunidades/');
} else {
redirectexit($boardurl . '/comunidades/');
}
}

function ssi_ultimas_respuestas()
{
	global $context, $txt, $scripturl, $db_prefix, $ID_MEMBER, $boardurl, $settings, $modSettings;

$id			=	htmlentities(addslashes($_REQUEST['id']));
$request	=	db_query("
SELECT cc.ID_COMMUNITY, cc.ID_TOPIC, cc.posterName, cc.ID_COMMENT, c.ID_COMMUNITY, c.friendly_url, ct.ID_COMMUNITY, ct.ID_TOPIC, ct.subject
FROM ({$db_prefix}community_comments AS cc, {$db_prefix}communities AS c, {$db_prefix}community_topic AS ct)
WHERE cc.ID_COMMUNITY = c.ID_COMMUNITY
AND cc.ID_COMMUNITY = ct.ID_COMMUNITY
AND c.ID_COMMUNITY = ct.ID_COMMUNITY
AND cc.ID_TOPIC = ct.ID_TOPIC
AND c.friendly_url = '$id'
ORDER BY cc.ID_COMMENT DESC
LIMIT " . $modSettings['community_comments'], __FILE__, __LINE__);
$context['ultimas_respuestas'] = array();
while($row = mysql_fetch_assoc($request))
	$context['ultimas_respuestas'][] = array(
		'posterName' => $row['posterName'],
		'ID_TOPIC' => $row['ID_TOPIC'],
		'subject' => $row['subject'],
		'friendly_url' => $row['friendly_url'],
	);
$context['contar']	=	mysql_num_rows($request);

if($context['contar'] <= 0) {
echo '<div class="noesta">No hay nuevos comentarios.</div>';
} else {
foreach($context['ultimas_respuestas'] as $ult) {
echo '<font class="size11"><b><a href="/perfil/', $ult['posterName'], '" title="', $ult['posterName'], '" alt="', $ult['posterName'], '">', $ult['posterName'], '</a></b> - <a title="', $ult['subject'], '" href="/comunidades/', $ult['friendly_url'], '/', $ult['ID_TOPIC'], '/', ssi_amigable($ult['subject']), '.html#comentarios">', ssi_reducir2($ult['subject']), '</a></font><br style="margin: 0px; padding: 0px;">';
}
}
}
function ssi_respuestas_temas()
{
	global $txt, $context, $scripturl, $db_prefix, $ID_MEMBER, $boardurl, $Respuestas, $settings;

$Comentarios	=	(int) htmlentities(addslashes($_REQUEST['tema']));

if(empty($Comentarios) || !is_numeric($Comentarios)) {
echo 'Faltan datos.-';
} else {
$RegistrosAMostrar		=	10;
if(isset($_GET['pag'])) {
$RegistrosAEmpezar	=	($_GET['pag']-1)*$RegistrosAMostrar;
$PagAct	=	(int) $_GET['pag'];
} else {
$RegistrosAEmpezar	=	0;
$PagAct				=	1;
}
$request2	= db_query("
SELECT c.comment, c.comment AS comentario2, c.ID_TOPIC, c.ID_MEMBER, c.ID_COMMENT, c.posterTime, c.posterName, c.ID_COMMUNITY
FROM ({$db_prefix}community_comments AS c) 
WHERE c.ID_TOPIC = '$Comentarios'
ORDER BY c.ID_COMMENT ASC
LIMIT {$RegistrosAEmpezar}, {$RegistrosAMostrar}", __FILE__, __LINE__);
$count	=	mysql_num_rows($request2);
if($count <= 0) {
echo '<div class="coment-user"><div class="noesta">Este tema no tiene comentarios.-</div></div>';
} else {
echo '<div class="post-com" id="carando" style="display:none;padding:4px 0px 4px 0px;margin-bottom:4px;"><center><img alt="" src="', $settings['images_url'], '/comunidades/cargando.gif" /></center></div>

<div class="post-com">
';

$cantidad++;
$cantidad2++;
$cantidad3++;
$cantidad4++;
$cantidad5++;
while($row	=	mysql_fetch_assoc($request2)) {
echo '<div class="coment-user" id="' . $cantidad++ . '" ><div style="float:left;"><div class="com-com-info"><a href="#' . $cantidad2++ . '">#' . $cantidad3++ . '</a></div><b id="autor_cmnt_' . $row['ID_COMMENT'] . '" user_comment="' . $row['posterName'] . '" text_comment=\'' . $row['comentario2'] . '\'><a href="/perfil/' . $row['posterName'] . '" title="' . $row['posterName'] . '">' . $row['posterName'] . '</a></b> | <span class="size10">' . date("d.n.y H:i:s", $row['posterTime']) . '</span> dijo:</div>';
echo '<div style="float:right;"><a href="/mensajes/a/' . $row['posterName'] . '" title="Enviar MP a: ' . $row['posterName'] . '"><img src="', $settings['images_url'], '/icons/mensaje_para.gif" alt="" /></a>';
if($context['user']['is_logged']) {
if($context['usercomunidad'] == 1 && $context['rango']['grade'] == 1 || $context['usercomunidad'] == 1 && $context['rango']['grade'] == 2 || $context['usercomunidad'] == 1 && $context['rango']['grade'] == 3 || $context['usercomunidad'] == 1 && $context['rango']['grade'] == 4) {
echo '<a onclick="citar_comment(' . $row['ID_COMMENT'] . ')" href="javascript:void(0)" title="Citar Comentario"><img src="', $settings['images_url'], '/comunidades/respuesta.png" alt="" /></a>';
}
if($coment['ID_MEMBER'] == $ID_MEMBER || $context['allow_admin'] || $context['can_remove']) {
echo '<a href="/web/tp-comunidadesEliCom.php?id=' . $row['ID_COMMENT'] . '" title="Eliminar Comentario" onclick="if (!confirm(\'\xbfEstas seguro que desea eliminar este comentario?\')) return false;"><img src="/images/comunidades/eliminar.png" alt="" /></a>';
}
}
echo '</div>';
echo '<div class="clearBoth"></div></div>
<div class="post-contenido">' . parse_bbc($row['comment']) . '<div class="clearBoth"></div></div>
<div align="right" style="padding:0px 4px 4px 0px;"><a href="#top" title="Ir arriba"><img src="', $settings['images_url'], '/comunidades/arriba-com.png" alt="" /></a></div>';
}
$NroRegistros	=	mysql_num_rows(db_query("
SELECT c.comment, c.comment AS comentario2, c.ID_TOPIC, c.ID_MEMBER, c.ID_COMMENT, c.posterTime, c.posterName, c.ID_COMMUNITY
FROM ({$db_prefix}community_comments AS c) 
WHERE c.ID_TOPIC = '$Comentarios'
ORDER BY c.ID_COMMENT ASC", __FILE__, __LINE__));
}

$PagAnt=$PagAct-1;
$PagSig=$PagAct+1;
$PagUlt=$NroRegistros/$RegistrosAMostrar;
$Res=$NroRegistros%$RegistrosAMostrar;
if($Res>0) $PagUlt=floor($PagUlt)+1;
echo '</div><div align="right" class="paginacion2"><b>';
if($PagAct>1) echo '<a style="cursor:pointer;" onclick="comentarioscom(' . $Comentarios . ',' . $PagAnt . ');">&#171;</a>';
if($PagAct<$PagUlt)  echo '<a style="cursor:pointer;" onclick="comentarioscom(' . $Comentarios . ',' . $PagSig . ');">&#187;</a>';

echo '</b></div>';
}
}
function ssi_denunciar_comunidad()
{
	global $txt, $context, $scripturl, $db_prefix, $ID_MEMBER, $boardurl;

	loadlanguage('Post');
	
	$id	=	(int) htmlentities(addslashes($_REQUEST['comu']));
	$razon = htmlentities(addslashes($_REQUEST['razon']), ENT_QUOTES, "UTF-8");
	$comentario = htmlentities(addslashes($_REQUEST['comentario']), ENT_QUOTES, "UTF-8");
if(empty($id)) {
echo '0: Debes seleccionar una comunidad.-';
} elseif(empty($razon)) {
echo '0: Debes rellenar la raz&oacute;n.-';
} elseif(empty($comentario)) {
echo '0: Debes rellenar el comentario.-';
} elseif(!empty($id) && !empty($razon) && !empty($comentario)) {
$result1 = db_query("
	INSERT INTO {$db_prefix}denunciations
	(ID_TOPIC, ID_MEMBER, reason, comment, TYPE)
	VALUES
	($id, $ID_MEMBER, '$razon', '$comentario', 'comunidad')", __FILE__, __LINE__);
redirectexit($boardurl . '/denuncia/enviada/');
}
}
function ssi_abandonar_comunidad()
{
	global $txt, $context, $scripturl, $db_prefix, $ID_MEMBER, $boardurl;

is_not_guest();
loadlanguage('Post');
$context['page_title']	=	'Salir de la Comunidad';
$id						=	htmlentities(addslashes($_REQUEST['id']));
$result = db_query("
	SELECT cm.ID_MEMBER, cm.ID_COMMUNITY, cm.grade, c.friendly_url, c.ID_COMMUNITY, cm.grade
	FROM ({$db_prefix}community_members AS cm, {$db_prefix}communities AS c)
	WHERE cm.ID_MEMBER = $ID_MEMBER
	AND cm.ID_COMMUNITY = c.ID_COMMUNITY
	AND c.friendly_url = '$id'
	LIMIT 1", __FILE__, __LINE__);
$context['usercomunidad'] = mysql_num_rows($result);
$row = mysql_fetch_assoc($result);
$context['rango'] = array(
	'grade' => $row['grade'],
);
mysql_free_result($result);
// Otro c&#243;digo 
$verify	=	db_query("
SELECT c.ID_COMMUNITY, cm.ID_COMMUNITY, cm.grade, c.ID_MEMBER, c.friendly_url, cm.ID_MEMBER, mem.ID_MEMBER
FROM ({$db_prefix}communities AS c, {$db_prefix}community_members AS cm, {$db_prefix}members AS mem)
WHERE cm.grade = 1
AND cm.ID_COMMUNITY = c.ID_COMMUNITY
AND c.friendly_url = '$id'
AND cm.ID_MEMBER = mem.ID_MEMBER
", __FILE__, __LINE__);
$rowk	=	mysql_fetch_assoc($verify);
$rowv	=	mysql_num_rows($verify);
if($context['rango']['grade'] == 1 && $rowv == 1) {
echo '0: No puedes dejar a la comunidad sin administrador.-';
} else {

$result = db_query("
	SELECT cm.ID_MEMBER, cm.ID_COMMUNITY, c.ID_COMMUNITY, c.friendly_url
	FROM ({$db_prefix}community_members AS cm, {$db_prefix}communities AS c)
	WHERE cm.ID_MEMBER = $ID_MEMBER
	AND cm.ID_COMMUNITY = c.ID_COMMUNITY
	AND c.friendly_url = '$id'
	LIMIT 1", __FILE__, __LINE__);
	$alreadyAdded = mysql_num_rows($result) != 1 ? true : false;
	mysql_free_result($result);

	$dbresult = db_query("
        SELECT ID_COMMUNITY, numMembers, friendly_url, grade
		FROM {$db_prefix}communities
		WHERE friendly_url = '$id'
		LIMIT 1", __FILE__, __LINE__);
	$row = mysql_fetch_assoc($dbresult);

	$context['selectcom'] = array(
		'numMembers' => $row['numMembers'],
		'ID_COMMUNITY' => $row['ID_COMMUNITY'],
	);
	mysql_free_result($dbresult);

$id_comunidades	=	$context['selectcom']['ID_COMMUNITY'];
$rango			=	$context['selectcom']['grade'];
$miembros		=	$context['selectcom']['numMembers'];

if ($alreadyAdded) {
echo '0: No eres parte de esta comunidad';
die($text);
} else {
$result1 = db_query("DELETE FROM {$db_prefix}community_members WHERE ID_MEMBER = $ID_MEMBER AND ID_COMMUNITY = {$context['selectcom']['ID_COMMUNITY']} LIMIT 1", __FILE__, __LINE__);
$result2 = db_query("UPDATE {$db_prefix}communities SET numMembers = $miembros - 1 WHERE friendly_url = '$id'  LIMIT 1", __FILE__, __LINE__);
if ($result1){
redirectexit($boardurl . '/comunidades/' . $id . '/');
}
}
}
}

function ssi_unir_comunidad()
{
	global $txt, $context, $scripturl, $db_prefix, $ID_MEMBER, $boardurl;

if($context['user']['is_logged']) {
$id		=	htmlentities(addslashes($_REQUEST['id']));
$fecha	=	time();

$result = db_query("
		SELECT cm.ID_MEMBER, cm.ID_COMMUNITY, c.ID_COMMUNITY, c.friendly_url
		FROM ({$db_prefix}community_members AS cm, {$db_prefix}communities as c)
		WHERE cm.ID_MEMBER = $ID_MEMBER
		AND cm.ID_COMMUNITY = c.ID_COMMUNITY
		AND c.friendly_url = '$id'
		LIMIT 1", __FILE__, __LINE__);
	$alreadyAdded = mysql_num_rows($result) != 0 ? true : false;
	mysql_free_result($result);

	$dbresult = db_query("
        SELECT ID_COMMUNITY, grade, numMembers, ID_MEMBER, friendly_url
		FROM {$db_prefix}communities
		WHERE friendly_url = '$id'
		LIMIT 1", __FILE__, __LINE__);
	$row = mysql_fetch_assoc($dbresult);

	$context['selectcom'] = array(
		'ID_COMMUNITY' => $row['ID_COMMUNITY'],
		'grade' => $row['grade'],
		'numMembers' => $row['numMembers'],
		'ID_MEMBER' => $row['ID_MEMBER'],
		'friendly_url' => $row['friendly_url'],
	);
	mysql_free_result($dbresult);

$id_comunidades	=	$context['selectcom']['ID_COMMUNITY'];
$rango			=	$context['selectcom']['grade'];
$miembros		=	$context['selectcom']['numMembers'];
$id_user		=	$context['selectcom']['ID_MEMBER'];
$friendly_url	=	$context['selectcom']['friendly_url'];

if ($alreadyAdded) {
echo '0: Ya eres miembro de esta comunidad.-';
} else {
$result1 = db_query("INSERT INTO {$db_prefix}community_members (ID_COMMUNITY, ID_MEMBER, grade, date, name) VALUES ($id_comunidades, $ID_MEMBER, $rango, $fecha, '" . $context['user']['name'] . "')", __FILE__, __LINE__);
if ($result1) {
redirectexit($boardurl . '/comunidades/' . $friendly_url . '/');
}
}
} else {
echo '0: Solo usuarios conectados pueden unirse a las comunidades.-';
}
}
function ssi_respuestas()
{
	global $context, $settings, $db_prefix, $modSettings;

$request	 =	db_query("
SELECT c.ID_COMMUNITY, cc.ID_COMMUNITY, cc.ID_TOPIC, ct.ID_TOPIC, ct.ID_COMMUNITY, cc.posterName, c.friendly_url, ct.subject, cc.ID_COMMENT
FROM ({$db_prefix}communities AS c, {$db_prefix}community_comments AS cc, {$db_prefix}community_topic AS ct)
WHERE c.ID_COMMUNITY = cc.ID_COMMUNITY
AND cc.ID_TOPIC = ct.ID_TOPIC
AND ct.ID_COMMUNITY = c.ID_COMMUNITY
AND ct.ID_COMMUNITY = cc.ID_COMMUNITY
ORDER BY cc.ID_COMMENT DESC
LIMIT " . $modSettings['community_comments_general'], __FILE__, __LINE__);
while($row = mysql_fetch_assoc($request)){
echo '<font class="size11"><b><a href="/perfil/', $row['posterName'], '" target="_self" title="', $row['posterName'], '" alt="', $row['posterName'], '">', $row['posterName'], '</a></b> - <a href="/comunidades/', $row['friendly_url'], '/', $row['ID_TOPIC'], '/', ssi_amigable($row['subject']), '.html" target="_self" title="', $row['subject'], '" alt="', $row['subject'], '">', ssi_reducir($row['subject']), '</a></font><br style="margin: 0px; padding: 0px;">';
}
}

function ssi_responder_tema()
{
	global $context, $settings, $ID_MEMBER, $db_prefix, $modSettings;

$comentario			=	htmlentities($_POST['comentario'], ENT_QUOTES, "UTF-8");
$cuerpo_comment_bbc	=	parse_bbc($comentario);
$tema				=	htmlentities(addslashes($_POST['tema']));
$posterTime			=	time();
$posterName			=	$context['user']['name'];
$cantidad++;

if(empty($context['user']['id'])) {
echo '0: Solo usuarios registrados pueden comentar.-';
} elseif(empty($tema)) {
echo '0: Debes seleccionar el tema que deseas comentar.-';
} elseif(strlen($_POST['comentario']) > $modSettings['characters_limit_comments']) {
echo '0: El comentario es demasiado extenso, abr&eacute;vialo.-';
} else {
db_query("INSERT INTO {$db_prefix}community_comments (ID_TOPIC, ID_MEMBER, posterName, posterTime, comment) VALUES ('$tema', '$ID_MEMBER', '$posterName', '$posterTime', '$comentario')", __FILE__, __LINE__);
$query				=	db_query("SELECT ID_COMMENT FROM {$db_prefix}community_comments ORDER BY ID_COMMENT DESC", __FILE__, __LINE__);
$obj				=	mysql_fetch_object($query);
$ultimo_id_coment	=	$obj->ID_COMMENT;
$query				=	db_query("SELECT ID_COMMENT FROM {$db_prefix}community_comments WHERE ID_TOPIC = '$tema' ", __FILE__, __LINE__);
$cantidad			=	mysql_num_rows($query);
$cantidad2			=	mysql_num_rows($query);
$cantidad3			=	mysql_num_rows($query);
	$result = db_query("
		SELECT cm.ID_MEMBER, cm.ID_COMMUNITY, cm.grade, c.friendly_url, c.ID_COMMUNITY, cm.grade
		FROM ({$db_prefix}community_members AS cm, {$db_prefix}communities AS c)
		WHERE cm.ID_MEMBER = $ID_MEMBER
		LIMIT 1", __FILE__, __LINE__);
	$context['usercomunidad'] = mysql_num_rows($result);
	$row = mysql_fetch_assoc($result);
	$context['rango'] = array(
		'grade' => $row['grade'],
	);
	mysql_free_result($result);

echo '1: ';
}
}

function ssi_permitir_mp()
{
	global $context, $txt, $settings, $db_prefix, $ID_MEMBER, $modSettings;

$action			=	htmlentities(addslashes($_REQUEST['action']));
$user			=	htmlentities(addslashes($_REQUEST['user']));
$request_user	=	db_query("SELECT * FROM {$db_prefix}members WHERE memberName = '" . $user . "'", __FILE__, __LINE__);
$row_user		=	mysql_fetch_assoc($request_user);
$request	=	db_query("SELECT * FROM {$db_prefix}members WHERE ID_MEMBER = " . $context['user']['id'] . "", __FILE__, __LINE__);
$row		=	mysql_fetch_assoc($request);
$verify		=	db_query("SELECT * FROM {$db_prefix}ignored", __FILE__, __LINE__);
$verify2	=	mysql_fetch_assoc($verify);

if(empty($action)) {
echo '0: No puedes estar aca.-';
} elseif(empty($user)) {
echo '0: Debes seleccionar alguien a quien ignorar.-';
} elseif($verify2['ID_MEMBER'] == $context['user']['id'] && $verify['ID_IGNORED'] == $row_user['ID_MEMBER']) {
echo '0: Ya estas ignorando a este usuario.-';
} else {
echo '1: si';
if($action == 'agregar') {
$change		=	mysql_query("INSERT INTO {$db_prefix}ignored (ID_MEMBER, ID_IGNORED) VALUES ('" . $context['user']['id'] . "', '" . $row_user['ID_MEMBER'] . "')");
} elseif($action == 'eliminar') {
$change		=	mysql_query("DELETE FROM {$db_prefix}ignored WHERE ID_MEMBER = " . $context['user']['id'] . " AND ID_IGNORED = " . $row_user['ID_MEMBER'] . "");
}
}
}

function ssi_enviar_quehago()
{
	global $context, $txt, $settings, $db_prefix, $ID_MEMBER, $modSettings;

$user		=	$ID_MEMBER;
$quehago	=	htmlentities($_POST['quehago'], ENT_QUOTES, "UTF-8");

$commentdate	=	time();

$query2			=	mysql_query("SELECT date FROM {$db_prefix}profile_comments WHERE COMMENT_MEMBER_ID = {$user} ORDER BY date DESC");
$obj2			=	mysql_fetch_object($query2);
$ultimo_fecha	=	$obj2->date;
$resta			=	$commentdate - $ultimo_fecha;

if($context['user']['is_guest']) {
echo '0: Usuarios no logueados no pueden hacer esta acci&oacute;n.';
} elseif(empty($quehago)) {
echo '0: Debes escribir algo en el muro.-';
} elseif($quehago == '&#191;Qu&#233; est&#225;s haciendo ahora?' && empty($muro) || empty($quehago)) {
echo '0: Debes escribir algo en el muro.-';
} elseif(strlen($quehago) > $modSettings['characters_limit_quehago']) {
echo '0: No se aceptan escritos mayor a ' . $modSettings['characters_limit_quehago'] . ' letras.-';
} elseif($resta < $modSettings['time_profile_comment']) {
echo '0: No es posible comentar muros con tan poca diferencia de tiempo.-';
} else {
db_query("INSERT INTO {$db_prefix}profile_comments (ID_MEMBER, subject, date, COMMENT_MEMBER_ID) VALUES ($ID_MEMBER, '$quehago', $commentdate, $user)", __FILE__, __LINE__);
$query				=	db_query("SELECT ID_COMMENT FROM {$db_prefix}profile_comments ORDER BY ID_COMMENT DESC", __FILE__, __LINE__);
$obj				=	mysql_fetch_object($query);
$ultimo_id_coment	=	$obj->ID_COMMENT;
$avatar				=	db_query("SELECT avatar FROM {$db_prefix}members WHERE ID_MEMBER = {$ID_MEMBER}", __FILE__, __LINE__);
$row				=	mysql_fetch_array($avatar);

echo '1: ';
if($context['member']['id'] == $ID_MEMBER || $context['allow_admin'])  {
echo '<a onclick="if (!confirm(\'\xbfEstas seguro que deseas borrar este mensaje?\')) return false;" href="/eliminar-muro/' . $ultimo_id_coment . '/" title="Eliminar Mensaje"><img alt="Eliminar Mensaje" src="' . $settings['images_url'] . '/eliminar.gif" width="8px" height="8px" /></a>&#32;-&#32;';
}
echo '<span style="margin-left:8px;"><img alt="" src="' . $settings['images_url'] . '/user.gif" /><b class="size13">' . parse_bbc2($quehago) . '</b><center><span style="color:grey;font-size:11px;">(' . timeformat($commentdate) . ')</span></center></span><hr />';
}
}

function ssi_votar()
{
	global $context, $txt, $settings, $db_prefix, $ID_MEMBER;

$tema		=	htmlentities(addslashes($_POST['tema']));
$voto		=	htmlentities(addslashes($_POST['voto']));
$result = db_query("
	SELECT cm.ID_MEMBER, cm.ID_COMMUNITY, cm.grade, c.friendly_url, c.ID_COMMUNITY, cm.grade, ct.ID_TOPIC, ct.ID_COMMUNITY
	FROM ({$db_prefix}community_members AS cm, {$db_prefix}communities AS c, {$db_prefix}community_topic AS ct)
	WHERE cm.ID_MEMBER = $ID_MEMBER
	AND ct.ID_TOPIC = $tema
	AND ct.ID_COMMUNITY = c.ID_COMMUNITY
	AND cm.ID_COMMUNITY = ct.ID_COMMUNITY
	AND cm.ID_COMMUNITY = c.ID_COMMUNITY
	LIMIT 1", __FILE__, __LINE__);
$context['usercomunidad'] = mysql_num_rows($result);
$row = mysql_fetch_assoc($result);
$context['rango'] = array(
	'grade' => $row['grade'],
);
mysql_free_result($result);
$request	=	db_query("
SELECT c.ID_COMMUNITY, c.ID_COMMUNITY, ct.ID_TOPIC, ct.ID_MEMBER, c.friendly_url, ct.subject, ct.points
FROM ({$db_prefix}communities AS c, {$db_prefix}community_topic AS ct)
WHERE c.ID_COMMUNITY = ct.ID_COMMUNITY
AND ct.ID_TOPIC = '$tema'
", __FILE__, __LINE__);
$row	=	mysql_fetch_assoc($request);
$request2	=	db_query("
SELECT ID_MEMBER, ID_TOPIC
FROM {$db_prefix}community_votes
WHERE ID_MEMBER = '$ID_MEMBER'
AND ID_TOPIC = " . $row['ID_TOPIC'] . "
", __FILE__, __LINE__);
$row2	=	mysql_num_rows($request2);
if($context['user']['is_logged']) {
if($row['ID_MEMBER'] == $ID_MEMBER) {
echo '0: No a tus temas';
} elseif($row2 > 0) {
echo '0: Ya puntuastes';
} elseif($context['usercomunidad'] == 0) {
echo '0: No eres miembro de la comunidad';
} else {
if($voto == 1) {
mysql_query("UPDATE {$db_prefix}community_topic SET points = points + 1 WHERE ID_TOPIC = " . $row['ID_TOPIC'] . " LIMIT 1");
mysql_query("INSERT INTO {$db_prefix}community_votes(ID_MEMBER, ID_TOPIC) VALUES ('$ID_MEMBER', '" . $row['ID_TOPIC'] . "')");
} elseif($voto == -1) {
mysql_query("UPDATE {$db_prefix}community_topic SET points = points - 1 WHERE ID_TOPIC = " . $row['ID_TOPIC'] . " LIMIT 1");
mysql_query("INSERT INTO {$db_prefix}community_votes(ID_MEMBER, ID_TOPIC) VALUES ('$ID_MEMBER', '" . $row['ID_TOPIC'] . "')");
}
}
} else {
echo '0: Usuario no conectado';
}
}

function ssi_comunidades_nooficial()
{
	global $context, $txt, $settings, $db_prefix, $ID_MEMBER;

$id			=	htmlentities(addslashes($_REQUEST['id']));
$request	=	mysql_query("SELECT ID_MEMBER, ID_COMMUNITY, friendly_url FROM {$db_prefix}communities WHERE ID_MEMBER = $ID_MEMBER LIMIT 1");
$row		=	mysql_fetch_assoc($request);
$context['comunidad'] = array(
	'ID_COMMUNITY' => $row['ID_COMMUNITY'],
	'ID_MEMBER' => $row['ID_MEMBER'],
	'friendly_url' => $row['friendly_url'],
	);
mysql_free_result($request);

$result = mysql_query("SELECT cm.ID_MEMBER, cm.ID_COMMUNITY, cm.grade, c.friendly_url, c.ID_COMMUNITY, cm.grade
FROM ({$db_prefix}community_members AS cm, {$db_prefix}communities AS c)
WHERE cm.ID_MEMBER = $ID_MEMBER
AND cm.ID_COMMUNITY = c.ID_COMMUNITY
AND c.friendly_url = '$id'
LIMIT 1");
$context['usercomunidad'] = mysql_num_rows($result);
$row = mysql_fetch_assoc($result);
$context['rango'] = array(
'grade' => $row['grade'],
);
mysql_free_result($result);

if(!empty($id) && $context['allow_admin'] || $context['comunidad']['ID_MEMBER'] == $ID_MEMBER && $context['rango']['grade'] == 4) {
$request	=	mysql_query("UPDATE {$db_prefix}communities SET oficial = 0 WHERE friendly_url = '$id' LIMIT 1");
Header('Location: /comunidades/' . $id . '/');
} elseif(!$context['allow_admin'] || $context['comunidad']['ID_MEMBER'] != $ID_MEMBER && $context['rango']['grade'] != 4) {
echo '0: No tienes el rango necesario para realizar esta acci&oacute;n;.';
}
}

function ssi_comunidades_oficial()
{
	global $context, $txt, $settings, $db_prefix, $ID_MEMBER;

$id			=	htmlentities(addslashes($_REQUEST['id']));
$request	=	mysql_query("SELECT ID_MEMBER, ID_COMMUNITY, friendly_url FROM {$db_prefix}communities WHERE ID_MEMBER = $ID_MEMBER  LIMIT 1");
$row		=	mysql_fetch_assoc($request);
$context['comunidad'] = array(
	'ID_COMMUNITY' => $row['ID_COMMUNITY'],
	'ID_MEMBER' => $row['ID_MEMBER'],
	'friendly_url' => $row['friendly_url'],
	);
mysql_free_result($request);

$result = mysql_query("SELECT cm.ID_MEMBER, cm.ID_COMMUNITY, cm.grade, c.friendly_url, c.ID_COMMUNITY, cm.grade
FROM ({$db_prefix}community_members AS cm, {$db_prefix}communities AS c)
WHERE cm.ID_MEMBER = $ID_MEMBER
AND cm.ID_COMMUNITY = c.ID_COMMUNITY
AND c.friendly_url = '$id'
LIMIT 1");
$context['usercomunidad'] = mysql_num_rows($result);
$row = mysql_fetch_assoc($result);
$context['rango'] = array(
'grade' => $row['grade'],
);
mysql_free_result($result);

if(!empty($id) && $context['allow_admin'] || $context['comunidad']['ID_MEMBER'] == $ID_MEMBER && $context['rango']['grade'] == 4) {
$request	=	mysql_query("UPDATE {$db_prefix}communities SET oficial = 1 WHERE friendly_url = '$id' LIMIT 1");
Header('Location: /comunidades/' . $id . '/');
} else {
echo '0: No tienes el rango necesario para realizar esta acci&oacute;n;.';
}
}

function ssi_eliminar_muro()
{
	global $txt, $context, $db_prefix, $ID_MEMBER, $settings, $modSettings;

$id =	(int) htmlentities(addslashes($_REQUEST['id']));

if($context['user']['is_guest']) {
Header("Location: / ");
} elseif(empty($id)) {
echo '0: Debes selecciona la id del comentario que deseas borrar.-';
} else {
$dbresult	=	db_query("SELECT p.ID_COMMENT, p.ID_MEMBER, p.COMMENT_MEMBER_ID FROM {$db_prefix}profile_comments as p WHERE p.ID_COMMENT = {$id}", __FILE__, __LINE__);
$row		=	mysql_fetch_assoc($dbresult);
mysql_free_result($dbresult);
db_query("DELETE FROM {$db_prefix}profile_comments WHERE ID_COMMENT = {$id} LIMIT 1", __FILE__, __LINE__);
header("Location: {$_SERVER['HTTP_REFERER']} ");
}
}

function ssi_enviar_muro()
{
	global $txt, $context, $db_prefix, $ID_MEMBER, $settings, $modSettings;

$user			=	(int) htmlentities(addslashes($_POST['user']));
$muro			=	htmlentities($_POST['muro'], ENT_QUOTES, "UTF-8");
$commentdate	=	time();

$query2			=	mysql_query("SELECT date FROM {$db_prefix}profile_comments WHERE COMMENT_MEMBER_ID = {$user} ORDER BY date DESC");
$obj2			=	mysql_fetch_object($query2);
$ultimo_fecha	=	$obj2->date;
$resta			=	$commentdate - $ultimo_fecha;

if($context['user']['is_guest']) {
echo '0: Usuarios no logueados no pueden hacer esta acci&oacute;n.';
} elseif(empty($muro)) {
echo '0: Debes escribir algo en el muro.-';
} elseif($muro == 'Escribe algo...') {
echo '0: Debes escribir algo en el muro.-';
} elseif(strlen($muro) > $modSettings['characters_limit_profile_comment']) {
echo '0: No se aceptan escritos tan grandes.-';
} elseif($resta < $modSettings['time_profile_comment']) {
echo '0: No es posible comentar muros con tan poca diferencia de tiempo.-';
} elseif(empty($user)) {
Header("Location: / ");
} else {
db_query("INSERT INTO {$db_prefix}profile_comments (ID_MEMBER, comment, date, COMMENT_MEMBER_ID) VALUES ($ID_MEMBER, '$muro', $commentdate, $user)", __FILE__, __LINE__);

$query				=	db_query("SELECT ID_COMMENT FROM {$db_prefix}profile_comments ORDER BY ID_COMMENT DESC", __FILE__, __LINE__);
$obj				=	mysql_fetch_object($query);
$ultimo_id_coment	=	$obj->ID_COMMENT;
$avatar				=	db_query("SELECT avatar FROM {$db_prefix}members WHERE ID_MEMBER = {$context['user']['id']}", __FILE__, __LINE__);
$row				=	mysql_fetch_array($avatar);

echo '1: ';
if($context['member']['id'] == $context['user']['id'] || $context['allow_admin']) {
echo '<a onclick="if (!confirm(\'\xbfEstas seguro que deseas borrar este mensaje?\')) return false;" href="/eliminar-muro/' . $ultimo_id_coment . '/" title="Eliminar Mensaje"><img alt="Eliminar Mensaje" src="' . $settings['images_url'] . '/eliminar.gif" width="8px" height="8px" /></a>&#32;-&#32;';
}
echo '<b><span style="font-size:12px;"><a href="/perfil/' . $context['user']['name'] . '" title="' . $context['user']['name'] . '">' . $context['user']['name'] . '</a></span> escribi&oacute; </b><span style="color:grey;font-size:10px;">(' . timeformat($commentdate) . ')</span><table><tr><td valign="top"><img style="width:50px;height:50px;" alt="" src="' . $row['avatar'] . '" onerror="error_avatar(this)" /></td><td valign="top" style="margin:0px;padding:4px;">' . parse_bbc2($muro) . '<br /><br /><a href="/perfil/' . $context['user']['name'] . '/muro/" title="Escribe en el Muro de ' . $context['user']['name'] . '">Escribe en el Muro de ' . $context['user']['name'] . '</a></td></tr></table><hr />';
}
}

function ssi_editar_estado()
{
	global $txt, $context, $db_prefix, $ID_MEMBER, $settings;

$estado		=	htmlentities(addslashes($_REQUEST['estado']));

if($estado == 'mcontento' || $estado == 'contento' || $estado == 'sueno' || $estado == 'descansar' || $estado == 'triste' || $estado == 'enferm' || $estado == 'emusic' || $estado == '') {
$change		=	db_query("UPDATE {$db_prefix}members SET estado_icon = '$estado' WHERE ID_MEMBER = $ID_MEMBER LIMIT 1 ", __FILE__, __LINE__);
Header("Location: /perfil");
} elseif(!empty($context['user']['name'])) {
Header("Location: /perfil");
} elseif(empty($context['user']['name'])) {
Header("Location: / ");
} else {
Header("Location: /perfil");
}
}
function ssi_agregar_favoritos()
{
	global $txt, $context, $db_prefix, $ID_MEMBER, $settings;

$tipo	=	htmlentities(addslashes($_REQUEST['tipo']));
$post	=	(int) htmlentities(addslashes($_REQUEST['post']));
if($context['user']['is_logged']) {
$result		=	db_query("SELECT * FROM {$db_prefix}bookmarks WHERE ID_MEMBER = {$ID_MEMBER} AND ID_TOPIC = {$post} AND TYPE = '$tipo' LIMIT 1", __FILE__, __LINE__);
$verificar	=	db_query("SELECT * FROM {$db_prefix}topics WHERE ID_TOPIC = {$post} LIMIT 1", __FILE__, __LINE__);
$verificar2	=	db_query("SELECT * FROM {$db_prefix}gallery_pic WHERE ID_PICTURE = {$post} LIMIT 1", __FILE__, __LINE__);
$row		=	mysql_fetch_assoc($verificar);
$row2		=	mysql_fetch_assoc($verificar2);
$alreadyAdded = mysql_num_rows($result) != 0 ? true : false;
mysql_free_result($result);

if($alreadyAdded) {
$text = '0: Este post ya est&aacute; en tus favoritos.-';
die($text);
} elseif($ID_MEMBER == $row['ID_MEMBER_STARTED']) {
echo '0: No puedes agregar a favoritos tus posts.-';
} elseif($ID_MEMBER == $row2['ID_MEMBER']) {
echo '0: No puedes agregar a favoritos tus im&aacute;genes.-';
} else {
$result = db_query("INSERT INTO {$db_prefix}bookmarks (ID_MEMBER, TYPE, ID_TOPIC) VALUES ({$ID_MEMBER}, '$tipo', {$post})", __FILE__, __LINE__);
if($result) {
$text = '1: Agregado a favoritos!';
die($text);
}
}
} else {
echo '0: Debes iniciar sesi&oacute;n para tener favoritos.-';
}
}

function ssi_comentar_img()
{
	global $context, $settings, $ID_MEMBER, $db_prefix, $modSettings;

$cuerpo_comment		=	htmlentities($_POST['cuerpo_comment'],ENT_QUOTES,"UTF-8");
$cuerpo_comment_bbc	=	parse_bbc($cuerpo_comment);
$id			=	htmlentities(addslashes($_POST['id']));
$posterTime		=	time();
$cantidad++;

if(empty($context['user']['id'])) {
echo '0: Solo usuarios registrados pueden comentar.-';
} elseif(empty($id)) {
echo '0: Debes seleccionar la imagen que deseas comentar.-';
} elseif(strlen($_POST['cuerpo_comment']) > $modSettings['characters_limit_comments']) {
echo '0: El comentario es demasiado extenso, abr&eacute;vialo.-';
} else {
db_query("INSERT INTO {$db_prefix}gallery_comment (ID_PICTURE, ID_MEMBER, date, comment) VALUES ('$id', '$ID_MEMBER', '$posterTime', '$cuerpo_comment')", __FILE__, __LINE__);
$query				=	db_query("SELECT ID_COMMENT FROM {$db_prefix}gallery_comment ORDER BY ID_COMMENT DESC", __FILE__, __LINE__);
$obj				=	mysql_fetch_object($query);
$ultimo_id_coment	=	$obj->ID_COMMENT;
$query				=	db_query("SELECT ID_COMMENT FROM {$db_prefix}gallery_comment WHERE ID_PICTURE = '$id' ", __FILE__, __LINE__);
$cantidad			=	mysql_num_rows($query);

echo '1: <div id="cmt_', $ultimo_id_coment, '"><span class="size12">';
if($context['can_remove']) {
echo '<input type="checkbox" name="campos['. $ultimo_id_coment, ']">';
}
echo ' <a onclick="citar_comment(', $ultimo_id_coment, ')" href="javascript:void(0)">#', $cantidad++, '</a> <b id="', $ultimo_id_coment, '" user_comment="', $context['user']['name'], '" text_comment=\'', $cuerpo_comment, '\'><a href="/perfil/', $context['user']['name'], '">', $context['user']['name'], '</a></b> |

<span class="size10">', date("d.n.y H:i:s", $posterTime), '</span> ';
echo '<a  href="/mensajes/a/', $context['user']['name'], '" title="Enviar MP a: ', $context['user']['name'], '"><img alt="" src="', $settings['images_url'], '/icons/mensaje_para.gif" style="margin-top:2px;margin-rigth:2px;" align="top" border="0" /></a><a class="icons citar" onclick="citar_comment(', $ultimo_id_coment, ')" href="javascript:void(0)" title="Citar Comentario"><img alt="" src="', $settings['images_url'], '/espacio.gif" align="top" border="0" /></a>';
echo 'dijo:<br /><div style="overflow: hidden;">', $cuerpo_comment_bbc, '</div></span></div><hr class="divider" />';
}
}

function ssi_comentar_post()
{
	global $context, $settings, $ID_MEMBER, $db_prefix, $message, $modSettings;

$cuerpo_comment		=	htmlentities($_POST['cuerpo_comment'], ENT_QUOTES, "UTF-8");
$cuerpo_comment_bbc	=	parse_bbc($cuerpo_comment);
$id			=	htmlentities(addslashes($_POST['id']));
$posterTime		=	time();
$cantidad++;

if(empty($context['user']['id'])) {
echo '0: Solo usuarios registrados pueden comentar.-';
} elseif(empty($id)) {
echo '0: Debes seleccionar el post que deseas comentar.-';
} elseif(strlen($_POST['cuerpo_comment']) > $modSettings['characters_limit_comments']) {
echo '0: El comentario es demasiado extenso, abr&eacute;vialo.-';
} else {
db_query("INSERT INTO {$db_prefix}comments (ID_TOPIC, ID_MEMBER, posterTime, comment) VALUES ('$id', '$ID_MEMBER', '$posterTime', '$cuerpo_comment')", __FILE__, __LINE__);
$query				=	db_query("SELECT ID_COMMENT FROM {$db_prefix}comments ORDER BY ID_COMMENT DESC", __FILE__, __LINE__);
$obj				=	mysql_fetch_object($query);
$ultimo_id_coment	=	$obj->ID_COMMENT;
$query				=	db_query("SELECT ID_COMMENT FROM {$db_prefix}comments WHERE ID_TOPIC = '$id' ", __FILE__, __LINE__);
$cantidad			=	mysql_num_rows($query);

echo '1: ';
echo '<div id="cmt_', $ultimo_id_coment, '"><span class="size12">';
if($message['can_remove']) {
echo '<input type="checkbox" name="campos[', $ultimo_id_coment, ']">';
}
echo ' <a onclick="citar_comment(', $ultimo_id_coment, ')" href="javascript:void(0)">#', $cantidad++, '</a> <b id="', $ultimo_id_coment, '" user_comment="', $context['user']['name'], '" text_comment=\'', $cuerpo_comment, '\'><a href="/perfil/', $context['user']['name'], '">', $context['user']['name'], '</a></b> |

<span class="size10">', date("d.n.y H:i:s", $posterTime), '</span> ';
echo '<a  href="/mensajes/a/', $context['user']['name'], '" title="Enviar MP a: ', $context['user']['name'], '"><img alt="" src="', $settings['images_url'], '/icons/mensaje_para.gif" style="margin-top:2px;margin-rigth:2px;" align="top" border="0" /></a><a class="icons citar" onclick="citar_comment(', $ultimo_id_coment, ')" href="javascript:void(0)" title="Citar Comentario"><img alt="" src="', $settings['images_url'], '/espacio.gif" align="top" border="0" /></a>';
echo 'dijo:<br /><div style="overflow: hidden;">', $cuerpo_comment_bbc, '</div></span></div><hr class="divider" />';
}
}

function ssi_votar_post()
{
	global $db_prefix, $post, $puntos, $context, $ID_MEMBER, $txt;

$post		=	(int) htmlentities(addslashes($_REQUEST['post']));
$puntos		=	(int) htmlentities(addslashes($_REQUEST['puntos']));
$time = time();

if($context['user']['is_logged']) {
if(empty($puntos)) {
echo '0: Debes agregar la cantidad que desea dar.-';
} elseif(empty($post)) {
echo '0: Debes seleccionar el post que le queres dar puntos.-';
} else {
$result		=	db_query("SELECT * FROM {$db_prefix}topics WHERE ID_TOPIC = {$post} LIMIT 1", __FILE__, __LINE__);
$row		=	mysql_fetch_array($result);
$creador	=	$row['ID_MEMBER_STARTED'];

if ($context['Turista']) {
echo '0: Usuarios Turistas no pueden dar puntos.';
}

$errorr	=	db_query("SELECT * FROM {$db_prefix}points WHERE ID_MEMBER = {$ID_MEMBER} AND ID_TOPIC = {$post} AND TYPE = 'post' LIMIT 1", __FILE__, __LINE__);
$yadio	=	mysql_num_rows($errorr) != 0 ? true : false;

$error2	=	db_query("SELECT * FROM {$db_prefix}members WHERE ID_MEMBER = {$ID_MEMBER} ", __FILE__, __LINE__);
$verify	=	mysql_fetch_assoc($error2);

if($creador == $context['user']['id']) {
echo '0: No puedes dar puntos a tus post.-';
} elseif($yadio) {
echo '0: Ya has dado puntos a este post.-';
} elseif($puntos > 10) {
echo '0: No puedes dar m&aacute;s de 10 puntos.-';
} elseif ($context['user']['money'] < $puntos) {
echo '0: No tiene esa cantidad de puntos para dar.-';
} elseif ($puntos < 0) {
echo '0: No puedes dar puntos negativos.-';
} elseif ($puntos == 0) {
echo '0: Ingresa una cantidad de puntos que sea v&aacute;lida.-';
} else {

db_query("UPDATE {$db_prefix}members SET money = money - {$puntos} WHERE ID_MEMBER = {$ID_MEMBER} LIMIT 1", __FILE__, __LINE__);
db_query("UPDATE {$db_prefix}members SET moneyBank = moneyBank + {$puntos} WHERE ID_MEMBER = {$creador} LIMIT 1", __FILE__, __LINE__);
db_query("UPDATE {$db_prefix}topics SET points = points + {$puntos} WHERE ID_TOPIC = {$post} LIMIT 1", __FILE__, __LINE__);
db_query("INSERT INTO {$db_prefix}points (ID_TOPIC, ID_MEMBER, POINTS, TYPE, time) VALUES ({$post}, {$ID_MEMBER}, {$puntos}, 'post', $time)", __FILE__, __LINE__);
$request = db_query("SELECT ID_TOPIC, subject FROM {$db_prefix}messages WHERE ID_TOPIC = {$post} ORDER BY subject ASC LIMIT 1", __FILE__, __LINE__);
while ($row = mysql_fetch_assoc($request)) {
echo '1: Puntos agregados!';
}
}
}
} else {
echo '0: Debes iniciar sesion para poder dar puntos a un post.-';
}
}

function ssi_votar_img()
{
	global $scripturl, $context, $txt, $db_prefix, $ID_MEMBER, $modSettings, $boardurl, $memberContext, $themeUser, $imagen, $puntos;

$imagen	=	(int) htmlentities(addslashes($_REQUEST['imagen']));
$puntos	=	(int) htmlentities(addslashes($_REQUEST['puntos']));
$time = time();

if($context['user']['is_logged']) {
if(empty($imagen)) {
echo '0: Debes seleccionar una imagen.-';
} elseif(empty($puntos)) {
echo '0: Debes ingresar una cantidad v&aacute;lida.-';
} else {
$result		=	db_query("SELECT * FROM {$db_prefix}gallery_pic WHERE ID_PICTURE = {$imagen} LIMIT 1", __FILE__, __LINE__);
$row		=	mysql_fetch_assoc($result);
$creador	=	$row['ID_MEMBER'];

if($context['Turista']) {
echo '0: Usuarios Turistas no pueden dar puntos.';
}

$errorr	=	db_query("SELECT * FROM {$db_prefix}points WHERE ID_MEMBER = {$ID_MEMBER} AND ID_TOPIC = {$imagen} AND TYPE = 'imagen' LIMIT 1", __FILE__, __LINE__);
$yadio	=	mysql_num_rows($errorr) != 0 ? true : false;

$error2	=	db_query("SELECT * FROM {$db_prefix}members WHERE ID_MEMBER = {$ID_MEMBER} ", __FILE__, __LINE__);
$verify	=	mysql_fetch_assoc($error2);

if($creador == $context['user']['id']) {
echo '0: No puedes dar puntos a tus im&aacute;genes.-';
} elseif($yadio) {
echo '0: Ya has dado puntos a esta imagen.-';
} elseif($puntos > 10) {
echo '0: No puedes dar m&aacute;s de 10 puntos.-';
} elseif ($context['user']['money'] < $puntos) {
echo '0: No tiene esa cantidad de puntos para dar.-';
} elseif ($puntos < 0) {
echo '0: No puedes dar puntos negativos.-';
} elseif ($puntos == 0) {
echo '0: Ingresa una cantidad de puntos que sea v&aacute;lida.-';
} else {
db_query("UPDATE {$db_prefix}members SET money = money - {$puntos} WHERE ID_MEMBER = {$ID_MEMBER} LIMIT 1 ", __FILE__, __LINE__);
db_query("UPDATE {$db_prefix}members SET moneyBank = moneyBank + {$puntos} WHERE ID_MEMBER = {$creador} LIMIT 1", __FILE__, __LINE__);
db_query("UPDATE {$db_prefix}gallery_pic SET points = points + {$puntos} WHERE ID_PICTURE = {$imagen} LIMIT 1 ", __FILE__, __LINE__);
db_query("INSERT INTO {$db_prefix}points (ID_TOPIC, ID_MEMBER, POINTS, TYPE, time) VALUES('$imagen', '$ID_MEMBER', '$puntos', 'imagen', $time) ", __FILE__, __LINE__);
$request = db_query("SELECT ID_PICTURE, title FROM {$db_prefix}gallery_pic WHERE ID_PICTURE = {$imagen} ORDER BY title ASC LIMIT 1 ", __FILE__, __LINE__);
while ($row = mysql_fetch_assoc($request)) {
echo '1: Puntos agregados!';
}
}
}
} else {
echo '0: Debes estar conectado.-';
}
}

function ssi_friendlyurl_verificar()
{
	global $db_prefix, $settings, $txt, $context;

$shortname	=	$_POST['shortname'];

if(!empty($shortname)) {
$request	=	db_query("SELECT friendly_url FROM {$db_prefix}communities WHERE friendly_url = '{$shortname}' ", __FILE__, __LINE__);
$total		=	mysql_num_rows($request);
if(!ereg("^[A-Za-z0-9]{5,32}$", $shortname)) {
echo '0: Solo se permiten letras, n&uacute;meros y guiones medios (-)';
} elseif($total>0) {
echo '0: El nick no est&aacute; disp';
} elseif(strlen($shortname) < 5 || strlen($shortname) > 32) {
echo '0: El nombre debe tener entre 5 y 32 car&aacute;cteres';
} else {
echo '1: El nombre est&aacute; disponible! :)';
}
} else {
echo '0: Debes agregar la url';
}
}

function ssi_smileys()
{
	global $context, $db_prefix, $settings;

echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="es" xml:lang="es" >
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>', $context['forum_name'], ' - Emoticones</title></head>
<body onload="javascript:resizeTo(225,500)" style="font-size:12px;font-family:Arial;">
<table width="190px">
<tbody>
<tr align="center">
<td width="40"><strong>Emotic&oacute;n:</strong></td>
<td width="80"><strong>C&oacute;digo:</strong></td>

</tr>
';
$existe	=	mysql_query("
SELECT *
FROM {$db_prefix}smileys
WHERE hidden = 2
ORDER BY smileyOrder ASC");
while ($row = mysql_fetch_assoc($existe))
{

echo '<tr align="center"><td><img alt="'.$row['description'].'" style="border: medium none;" src="', $settings['images_url'], '/emoticones/'.$row['filename'].'" title="'.$row['description'].'" /></td><td>'.$row['code'].'</td></tr>
';
}
mysql_free_result($existe);

echo '</tbody></table></body></html>';
}
function ssi_nick_verificar()
{
	global $db_prefix, $settings, $txt, $context;

$user	=	$_POST['verificacion'];

if(!empty($user))
{
$request	=	db_query("SELECT memberName FROM {$db_prefix}members WHERE memberName = '{$user}' ", __FILE__, __LINE__);
$total		=	mysql_num_rows($request);
if($total>0)
{
echo '<div style="border: 1px solid rgb(194, 91, 67); padding: 2px; height: 16px; width: 122px; background-color: rgb(247, 171, 161); font-size: 11px; font-family: Arial;"><img alt="" src="' . $settings['images_url'] . '/icons/no.gif"> El nick no est&aacute; disp</div>';
}
else
{
echo '<div style="border: 1px solid rgb(45, 131, 42); padding: 2px; height: 16px; width: 122px; font-family: Arial; background-color: rgb(178, 219, 168); font-size: 11px;"><img alt="" src="' . $settings['images_url'] . '/icons/si.gif"> El nick est&aacute; disp</div>';
}
}
else
{
echo '<div style="height:16px;width:122px;border:solid 1px #C25B43;background-color:#F7ABA1;font-size:11px;font-family:Arial;padding:2px;"><img alt="" src="' . $settings['images_url'] . '/icons/no.gif" /> Debes agregar el nick</div>';
}
}

function ssi_email_verificar()
{
	global $email, $db_prefix, $settings, $txt, $context;

$email		=	$_POST['emailverificar'];
$term_dom = substr(strrchr($email, '.'), 1);

if(empty($email))
{
echo '<div style="height:16px;width:122px;border:solid 1px #C25B43;background-color:#F7ABA1;font-size:11px;font-family:Arial;padding:2px;"><img alt="" src="' . $settings['images_url'] . '/icons/no.gif" /> Debes agregar el e-mail</div>
';
}
elseif((strlen($email) >= 6) && (!substr_count($email,"@") == 1))
{
echo '<div style="border: 1px solid rgb(194, 91, 67); padding: 2px; height: 16px; width: 122px; background-color: rgb(247, 171, 161); font-size: 11px; font-family: Arial;"><img alt="" src="' . $settings['images_url'] . '/icons/no.gif"> E-mail inv&aacute;lido</div>';
}
elseif(!strlen($term_dom)>1 && !strlen($term_dom)<5 && (strstr($term_dom,"@")))
{
echo '<div style="border: 1px solid rgb(194, 91, 67); padding: 2px; height: 14px; width: 122px; background-color: rgb(247, 171, 161); font-size: 11px; font-family: Arial;"><img alt="" src="' . $settings['images_url'] . '/icons/no.gif">  No puedes estar ac&aacute;</div>';
}
else
{
$request	=	db_query("SELECT memberName FROM {$db_prefix}members WHERE emailAddress='{$email}' ", __FILE__, __LINE__);
$total		=	mysql_num_rows($request);
if($total>0)
{
echo '<div style="border: 1px solid rgb(194, 91, 67); padding: 2px; height: 16px; width: 122px; background-color: rgb(247, 171, 161); font-size: 11px; font-family: Arial;"><img alt="" src="' . $settings['images_url'] . '/icons/no.gif"> E-mail no disponible</div>
';
}
else
{
echo '<div style="border: 1px solid rgb(45, 131, 42); padding: 2px; height: 16px; width: 122px; font-family: Arial; background-color: rgb(178, 219, 168); font-size: 11px;"><img alt="" src="' . $settings['images_url'] . '/icons/si.gif"> E-mail disponible</div>
';
}
}
}

function ssi_actualizar_puntos()
{
	global $context, $settings, $txt, $db_prefix, $ID_MEMBER, $modSettings;


// Casero
mysql_query("UPDATE {$db_prefix}members SET money = 25 WHERE ID_POST_GROUP = 9") or die("Error al dar los puntos a los Caseros: " . mysql_error());

// Familiar
mysql_query("UPDATE {$db_prefix}members SET money = 20 WHERE ID_POST_GROUP = 8") or die("Error al dar los puntos a los Familiares: " . mysql_error());

// Amigo
mysql_query("UPDATE {$db_prefix}members SET money = 15 WHERE ID_POST_GROUP = 7") or die("Error al dar los puntos a los Amigos: " . mysql_error());

// Vecino
mysql_query("UPDATE {$db_prefix}members SET money = 10 WHERE ID_POST_GROUP = 6") or die("Error al dar los puntos a los Vecinos: " . mysql_error());

// Conocido
mysql_query("UPDATE {$db_prefix}members SET money = 10 WHERE ID_POST_GROUP = 5") or die("Error al dar los puntos a los Conocidos: " . mysql_error());

// Padre
mysql_query("UPDATE {$db_prefix}members SET money = 35 WHERE ID_GROUP = 1") or die("Error al dar los puntos a los Padres: " . mysql_error());

// Hermano mayor
mysql_query("UPDATE {$db_prefix}members SET money = 35 WHERE ID_GROUP = 2") or die("Error al dar los puntos a los Hermanos Mayores: " . mysql_error());

// Abastecedor
mysql_query("UPDATE {$db_prefix}members SET money = 30 WHERE ID_GROUP = 10") or die("Error al dar los puntos a los Abastecedores: " . mysql_error());

// Heredero
mysql_query("UPDATE {$db_prefix}members SET money = 30 WHERE ID_GROUP = 11 LIMIT 1") or die("Error al dar los puntos a los Herederos: " . mysql_error());

echo 'Puntos dados el ' . date('d \d\e F \a \l\a\s h:i:s A');
}
?>