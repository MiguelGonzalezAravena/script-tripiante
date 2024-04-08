<?php
if (!defined('SMF'))
  die('Hacking attempt...');

function Contact() {
  global $context, $sourcedir, $modSettings, $user_info, $txt, $webmaster_email;

  if ($user_info['is_guest'] && empty($modSettings['contact_form_enable_guest_access']))
    redirectexit();

  if (!$user_info['is_guest'] && empty($modSettings['contact_form_enable_member_access']))
    redirectexit();

  $global_error = false;

  if (isset($_POST['enviar'])) {
    if (!empty($modSettings['recaptcha_enabled']) && ($modSettings['recaptcha_enabled'] == 1 && !empty($modSettings['recaptcha_public_key']) && !empty($modSettings['recaptcha_private_key']))) {
      if (!empty($_POST['recaptcha_response_field']) && !empty($_POST['recaptcha_challenge_field'])) //Check the input if this exists, if it doesn't, then the user didn't fill it out. 
      {
        require($sourcedir . '/recaptchalib.php');

        $resp = recaptcha_check_answer($modSettings['recaptcha_private_key'], $_SERVER['REMOTE_ADDR'], $_POST['recaptcha_challenge_field'], $_POST['recaptcha_response_field']);

        if (!$resp->is_valid)
          fatal_lang_error('visual_verification_failed', false);
      }
      else
        fatal_lang_error('visual_verification_failed', false);
    }
  }

  $content = '';
  $sender_ip = $_SERVER['REMOTE_ADDR'];
  $sender_rdns = gethostbyaddr($sender_ip);
  if ($context['user']['is_guest'])
    $sender = $txt['contact_form_sender_is_guest'];
  else {
    $sender = $context['user']['name'];
    if ($context['user']['name'] != $context['user']['username'])
      $sender .= ' (' . $context['user']['username'] . ')';
  }

  $message = isset($_POST['comentario']) ? trim($_POST['comentario']) : '';
  $empresa = isset($_POST['empresa']) ? trim($_POST['empresa']) : '';
  $tel = isset($_POST['tel']) ? trim($_POST['tel']) : '';
  $motivo = isset($_POST['motivo']) ? trim($_POST['motivo']) : '';
  $hc = isset($_POST['hc']) ? trim($_POST['hc']) : '';
  if (!$message)
    $global_error = $context['contact_form_error_no_message'] = true;

  $sender_name = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';

  if (!$sender_name && !empty($modSettings['contact_form_require_name']))
    $global_error = $context['contact_form_error_no_name'] = true;

  if (!$sender_name || !empty($modSettings['contact_form_sender_name_dont_use_form'])) {
    $name = empty($modSettings['contact_form_default_name']) ? $txt['contact_form_default_name_default'] : $modSettings['contact_form_default_name'];

    if ($sender_name)
      $content .= $txt['contact_form_label_status_name'] . $sender_name . '';
  }
  else
    $name = $sender_name;

  $sender_email = isset($_POST['email']) ? trim($_POST['email']) : '';

  if (!$sender_email && !empty($modSettings['contact_form_require_email']))
    $global_error = $context['contact_form_error_no_email'] = true;

  if ($sender_email && !valid_email($sender_email))
    $global_error = $context['contact_form_error_invalid_email'] = true;

  if (!$sender_email || !empty($modSettings['contact_form_sender_email_dont_use_form'])) {
    $email = empty($modSettings['contact_form_default_email_sender']) ? $webmaster_email : $modSettings['contact_form_default_email_sender'];

    if ($sender_email)
      $content .= $txt['contact_form_label_status_email'] . $sender_email . '';
  }
  else
    $email = $sender_email;

  $headers = 'From: ' . $name . ' <' . $email . '>';

  $sender_subject = 'Formulario de contacto';

  if (!$sender_subject && !empty($modSettings['contact_form_require_subject']))
    $global_error = $context['contact_form_error_no_subject'] = true;

  if (!$sender_subject || !empty($modSettings['contact_form_sender_subject_dont_use_form'])) {
    $subject = empty($modSettings['contact_form_default_subject']) ? $txt['contact_form_default_subject_default'] : $modSettings['contact_form_default_subject'];

    if ($sender_subject)
      $content .= $txt['contact_form_label_status_subject']  . $sender_subject . '';
  }
  else
    $subject = $sender_subject;

  if (!empty($modSettings['contact_form_subject_prefix']))
    $subject = $modSettings['contact_form_subject_prefix'] . ' ' . $subject;

  if ($content)
    $content .= $txt['contact_form_label_status_separator'];

  $content .= $txt['contact_form_label_ip'] . $sender_ip . "\n" . $txt['contact_form_label_rdns'] . $sender_rdns. "\nUsuario:" . $sender;
  $content .= "\nEmpresa: " . $empresa . "\nTel&eacute;fono: " . $tel . "\nMotivo: " . $motivo . "\nHorario de contacto: " . $hc . "\n";
  $content .= "Comentario: " . stripslashes($message) . "\n";

  $dest_email_addr = empty($modSettings['contact_form_dest_email_addr']) ? $webmaster_email : $modSettings['contact_form_dest_email_addr'];
  $context['page_title'] = $txt[18];

  if (!$global_error && isset($_POST['enviar'])) {
    require_once($sourcedir . '/Subs-Post.php');

    $context['contact_form_sendmail_override_headers'] = true;

    if (sendmail($dest_email_addr, $subject, $content, $headers)) {
      $context['contact_form_message_sent'] = true;
      $context['page_title'] .= ' - ' . $txt['contact_form_result_message_sent'];
    } else {
      $context['contact_form_message_failed'] = true;
      $context['page_title'] .= ' - ' . $txt['contact_form_result_send_mail_failed'];
    }
  } else {
    if ($global_error && isset($_POST['enviar']))
      $context['page_title'] .= ' - ' . $txt['contact_form_result_error'];
  }

  loadTemplate('Contact');
}

function valid_email($email) {
  global $modSettings;

  $at_ptr = strrpos($email, '@');
  if (is_bool($at_ptr) && !$at_ptr) return false;
  $domain = substr($email, $at_ptr+1);
  $local = substr($email, 0, $at_ptr);
  $local_len = strlen($local);
  $domain_len = strlen($domain);

  if ($local_len < 1 || $local_len > 64 || $domain_len < 1 || $domain_len > 255)
    return false;// local or domain part length invalid
  else if ($local[0] == '.' || $local[$local_len-1] == '.')
    return false; // local part cannot start or end with dot
  else if (preg_match('/\\.\\./', $local))
    return false; // local part cannot have two consecutive dots
  else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
  return false; // domain part has invalid character
  else if (preg_match('/\\.\\./', $domain))
  return false; // domain part cannot have two consecutive dots
  else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\", "", $local))) // exception for quoted
    if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\", "", $local)))
      return false; // local part character invalid except when local part is quoted

  if (!empty($modSettings['contact_form_skip_dns_check']))
    return true; // mod setting to skip the DNS A/MX record check

  if (function_exists('checkdnsrr'))
    if (!(checkdnsrr($domain, 'MX') || checkdnsrr($domain, 'A')))
      return false;// domain does not have a valid A or MX record
  else
    if (!(win_checkdnsrr($domain, 'MX') || win_checkdnsrr($domain, 'A')))
      return false; // domain does not have a valid A or MX record

  return true;
}

function win_checkdnsrr($host, $type) {
  if (!empty($host)) {
    exec('nslookup -type=' . $type . ' ' . escapeshellcmd($host), $result);

    foreach ($result as $line)
      if (eregi("^$hostName", $line))
        return true;
  }

  return false;
}

?>