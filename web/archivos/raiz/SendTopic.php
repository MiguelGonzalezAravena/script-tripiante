<?php
if (!defined('SMF'))
	die('Hacking attempt...');

function SendTopic()
{
	global $topic, $txt, $db_prefix, $context, $scripturl, $sourcedir, $boarddir, $boardurl, $modSettings;

	isAllowedTo('send_topic');

	if (empty($topic))
		fatal_lang_error(472, false);

	$request = mysql_query("
		SELECT m.subject, m.ID_TOPIC, m.ID_BOARD, b.ID_BOARD, b.description, t.ID_TOPIC, t.ID_BOARD
		FROM ({$db_prefix}messages AS m, {$db_prefix}topics AS t, {$db_prefix}boards AS b)
		WHERE t.ID_TOPIC = $topic
		AND b.ID_BOARD = m.ID_BOARD
		AND b.ID_BOARD = t.ID_BOARD
		AND m.ID_TOPIC = t.ID_TOPIC
		LIMIT 1");
	if (mysql_num_rows($request) == 0)
	fatal_lang_error(472, false);
	$row = mysql_fetch_assoc($request);
	mysql_free_result($request);
	censorText($row['subject']);
	if (empty($_POST['send']))
	{
		loadTemplate('SendTopic');
		$context['page_title'] = $row['subject'];
		$context['start'] = $_REQUEST['start'];

		return;
	}

if(isset($_POST['send']))
{
	if(!empty($modSettings['recaptcha_enabled']) && ($modSettings['recaptcha_enabled'] == 1 && !empty($modSettings['recaptcha_public_key']) && !empty($modSettings['recaptcha_private_key'])))
	{
		if(!empty($_POST["recaptcha_response_field"]) && !empty($_POST["recaptcha_challenge_field"])) //Check the input if this exists, if it doesn't, then the user didn't fill it out.
		{
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

	if (!isset($_POST['r_email']) || $_POST['r_email'] == '')
	fatal_lang_error(76, false);


	$row['subject'] = un_htmlspecialchars($row['subject']);

	sendmail($_POST['r_email'], $row['subject'],
		sprintf('Este mensaje ha sido enviado desde '. $boardurl .':') . "\n\n" .
		sprintf($_POST['comment']) . "\n\n" .
		'Enlace: '.$boardurl.'/post/'.$row['ID_TOPIC'].'/'.$row['description'].'/'.ssi_amigable($row['subject']).'.html');
		
	sendmail($_POST['r_email1'], $row['subject'],
		sprintf('Este mensaje ha sido enviado desde '. $boardurl .':') . "\n\n" .
		sprintf($_POST['comment']) . "\n\n" .
		'Enlace: '.$boardurl.'/post/'.$row['ID_TOPIC'].'/'.$row['description'].'/'.ssi_amigable($row['subject']).'.html');
		
	sendmail($_POST['r_email2'], $row['subject'],
		sprintf('Este mensaje ha sido enviado desde '. $boardurl .':') . "\n\n" .
		sprintf($_POST['comment']) . "\n\n" .
		'Enlace: '.$boardurl.'/post/'.$row['ID_TOPIC'].'/'.$row['description'].'/'.ssi_amigable($row['subject']).'.html');
		
	sendmail($_POST['r_email3'], $row['subject'],
		sprintf('Este mensaje ha sido enviado desde '. $boardurl .':') . "\n\n" .
		sprintf($_POST['comment']) . "\n\n" .
		'Enlace: '.$boardurl.'/post/'.$row['ID_TOPIC'].'/'.$row['description'].'/'.ssi_amigable($row['subject']).'.html');
		
	sendmail($_POST['r_email4'], $row['subject'],
		sprintf('Este mensaje ha sido enviado desde '. $boardurl .':') . "\n\n" .
		sprintf($_POST['comment']) . "\n\n" .
		'Enlace: '.$boardurl.'/post/'.$row['ID_TOPIC'].'/'.$row['description'].'/'.ssi_amigable($row['subject']).'.html');
		
	sendmail($_POST['r_email5'], $row['subject'],
		sprintf('Este mensaje ha sido enviado desde '. $boardurl .':') . "\n\n" .
		sprintf($_POST['comment']) . "\n\n" .
		'Enlace: '.$boardurl.'/post/'.$row['ID_TOPIC'].'/'.$row['description'].'/'.ssi_amigable($row['subject']).'.html');

	redirectexit($boardurl.'/post/'.$row['ID_TOPIC'].'/'.$row['description'].'/'.ssi_amigable($row['subject']).'.html');
}
?>