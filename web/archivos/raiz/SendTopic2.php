<?php
if (!defined('SMF'))
	die('Hacking attempt...');

function SendTopic2()
{
	global $txt, $db_prefix, $context, $scripturl, $sourcedir, $boarddir, $boardurl, $modSettings;

$img	=	htmlentities(addslashes($_REQUEST['img']));
if(empty($img)) {
fatal_lang_error(472, false);
}

$request = mysql_query("
SELECT title, ID_PICTURE, filename
FROM {$db_prefix}gallery_pic
WHERE ID_PICTURE = $img
");
if (mysql_num_rows($request) == 0) {
fatal_lang_error(472, false);
}

	$row = mysql_fetch_assoc($request);
	mysql_free_result($request);
	censorText($row['title']);
	if (empty($_POST['send']))
	{
		loadTemplate('SendTopic2');
		$context['page_title'] = $row['title'];
		return;
	}

if(isset($_POST['send'])) {
	if(!empty($modSettings['recaptcha_enabled']) && ($modSettings['recaptcha_enabled'] == 1 && !empty($modSettings['recaptcha_public_key']) && !empty($modSettings['recaptcha_private_key']))) 	{
		if(!empty($_POST["recaptcha_response_field"]) && !empty($_POST["recaptcha_challenge_field"])) {
			@require($sourcedir . "/recaptchalib.php");

			$resp = recaptcha_check_answer($modSettings['recaptcha_private_key'], $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);

			if (!$resp->is_valid)
				fatal_lang_error('visual_verification_failed', false);
		}
		else
			fatal_lang_error('visual_verification_failed', false);
	}
}
	@require_once($sourcedir . '/Subs-Post.php');
	@require_once($boarddir . '/SSI.php');

	if (!isset($_POST['r_email']) || $_POST['r_email'] == '' || empty($_POST['r_email'])) {
	fatal_lang_error(76, false);
	}


	$row['title'] = un_htmlspecialchars($row['title']);

	sendmail($_POST['r_email'], $row['title'],
		sprintf('Este mensaje ha sido enviado desde '. $boardurl .':') . "\n\n" .
		sprintf($_POST['comment']) . "\n\n" .
		'Enlace: '.$boardurl.'/imagenes/ver/'.$row['ID_PICTURE']);
		
	sendmail($_POST['r_email1'], $row['title'],
		sprintf('Este mensaje ha sido enviado desde '. $boardurl .':') . "\n\n" .
		sprintf($_POST['comment']) . "\n\n" .
		'Enlace: '.$boardurl.'/imagenes/ver/'.$row['ID_PICTURE']);
		
	sendmail($_POST['r_email2'], $row['title'],
		sprintf('Este mensaje ha sido enviado desde '. $boardurl .':') . "\n\n" .
		sprintf($_POST['comment']) . "\n\n" .
		'Enlace: '.$boardurl.'/imagenes/ver/'.$row['ID_PICTURE']);
		
	sendmail($_POST['r_email3'], $row['title'],
		sprintf('Este mensaje ha sido enviado desde '. $boardurl .':') . "\n\n" .
		sprintf($_POST['comment']) . "\n\n" .
		'Enlace: '.$boardurl.'/imagenes/ver/'.$row['ID_PICTURE']);
		
	sendmail($_POST['r_email4'], $row['title'],
		sprintf('Este mensaje ha sido enviado desde '. $boardurl .':') . "\n\n" .
		sprintf($_POST['comment']) . "\n\n" .
		'Enlace: '.$boardurl.'/imagenes/ver/'.$row['ID_PICTURE']);
		
	sendmail($_POST['r_email5'], $row['title'],
		sprintf('Este mensaje ha sido enviado desde '. $boardurl .':') . "\n\n" .
		sprintf($_POST['comment']) . "\n\n" .
		'Enlace: '.$boardurl.'/imagenes/ver/'.$row['ID_PICTURE']);

	redirectexit($boardurl.'/imagenes/ver/'.$row['ID_PICTURE']);
}
?>