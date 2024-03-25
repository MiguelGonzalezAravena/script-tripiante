<?php
if (!defined('SMF'))
	die('Hacking attempt...');

function Recomendar()
{
	global $topic, $txt, $db_prefix, $context, $scripturl, $sourcedir, $boarddir, $boardurl, $modSettings;

	if (empty($_POST['send']))
	{
		loadTemplate('Recomendar');
		$context['page_title'] = $txt[18];
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
		sprintf('Un persona te recomienda este sitio: ' . $boardurl . ', y dice:') . "\n\n" .
		sprintf($_POST['comment']) . "\n\n" .
		'Sitio Web: ' . $boardurl . '/');
		
	sendmail($_POST['r_email1'], $row['subject'],
		sprintf('Un persona te recomienda este sitio: ' . $boardurl . ', y dice:') . "\n\n" .
		sprintf($_POST['comment']) . "\n\n" .
		'Sitio Web: ' . $boardurl . '/');
		
	sendmail($_POST['r_email2'], $row['subject'],
		sprintf('Un persona te recomienda este sitio: ' . $boardurl . ', y dice:') . "\n\n" .
		sprintf($_POST['comment']) . "\n\n" .
		'Sitio Web: ' . $boardurl . '/');
		
	sendmail($_POST['r_email3'], $row['subject'],
		sprintf('Un persona te recomienda este sitio: ' . $boardurl . ', y dice:') . "\n\n" .
		sprintf($_POST['comment']) . "\n\n" .
		'Sitio Web: ' . $boardurl . '/');
		
	sendmail($_POST['r_email4'], $row['subject'],
		sprintf('Un persona te recomienda este sitio: ' . $boardurl . ', y dice:') . "\n\n" .
		sprintf($_POST['comment']) . "\n\n" .
		'Sitio Web: ' . $boardurl . '/');
		
	sendmail($_POST['r_email5'], $row['subject'],
		sprintf('Un persona te recomienda este sitio: ' . $boardurl . ', y dice:') . "\n\n" .
		sprintf($_POST['comment']) . "\n\n" .
		'Sitio Web: ' . $boardurl . '/');

		fatal_error('Muchas gracias por recomendar ' . $context['forum_name'] . '.-', false);
	
	
}
?>