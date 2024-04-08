<?php
if (!defined('SMF'))
  die('Hacking attempt...');

function SendTopic2() {
  global $db_prefix, $context, $sourcedir, $boarddir, $boardurl, $modSettings;

  $img = (int) $_REQUEST['img'];

  if (empty($img)) {
    fatal_lang_error(472, false);
  }

  $request = db_query("
    SELECT title, ID_PICTURE, filename
    FROM {$db_prefix}gallery_pic
    WHERE ID_PICTURE = $img", __FILE__, __LINE__);

  if (mysqli_num_rows($request) == 0) {
    fatal_lang_error(472, false);
  }

  $row = mysqli_fetch_assoc($request);

  mysqli_free_result($request);
  censorText($row['title']);

  if (empty($_POST['send'])) {
    loadTemplate('SendTopic2');

    $context['page_title'] = $row['title'];

    return;
  }

  if (isset($_POST['send'])) {
    if (!empty($modSettings['recaptcha_enabled']) && ($modSettings['recaptcha_enabled'] == 1 && !empty($modSettings['recaptcha_public_key']) && !empty($modSettings['recaptcha_private_key']))) {
      if (!empty($_POST['recaptcha_response_field']) && !empty($_POST['recaptcha_challenge_field'])) {
        @require($sourcedir . '/recaptchalib.php');

        $resp = recaptcha_check_answer($modSettings['recaptcha_private_key'], $_SERVER['REMOTE_ADDR'], $_POST['recaptcha_challenge_field'], $_POST['recaptcha_response_field']);

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
  $message = 'Este mensaje ha sido enviado desde '. $boardurl . ':';

  $link = $boardurl . '/imagenes/ver/' . $row['ID_PICTURE'];
  $strLink = 'Enlace: ' . $link;
  $emails = array('', '1', '2', '3', '4', '5');

  // Send emails from 0 to 5
  for ($i = 0; $i < count($emails); $i++) {
    sendmail($_POST['r_email' . $emails[$i]], $row['title'], sprintf($message) . "\n\n" . sprintf($_POST['comment']) . "\n\n" . $strLink);
  }

  /*
  sendmail($_POST['r_email'], $row['title'], sprintf($message) . "\n\n" . sprintf($_POST['comment']) . "\n\n" . $strLink);
  sendmail($_POST['r_email1'], $row['title'], sprintf($message) . "\n\n" . sprintf($_POST['comment']) . "\n\n" . $strLink);
  sendmail($_POST['r_email2'], $row['title'], sprintf($message) . "\n\n" . sprintf($_POST['comment']) . "\n\n" . $strLink);
  sendmail($_POST['r_email3'], $row['title'], sprintf($message) . "\n\n" . sprintf($_POST['comment']) . "\n\n" . $strLink);
  sendmail($_POST['r_email4'], $row['title'], sprintf($message) . "\n\n" . sprintf($_POST['comment']) . "\n\n" . $strLink);
  sendmail($_POST['r_email5'], $row['title'], sprintf($message) . "\n\n" . sprintf($_POST['comment']) . "\n\n" . $strLink);
  */

  redirectexit($link);
}

?>