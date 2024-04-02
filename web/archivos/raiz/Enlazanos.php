<?php
/*
Enlazanos por Peludo_08
*/

if (!defined('SMF'))
  die('Hacking attempt...');

function ShowHelp() {
  global $context, $txt;

  // Load the main template file
  loadtemplate('Enlazanos');

  // Load the language files
  if (loadlanguage('Enlazanos') == false)
    loadLanguage('Enlazanos', 'english');

  // All the available pages.
  $context['all_pages'] = array(
    'index' => 'intro',
  );

  if (!isset($_GET['page']) || !isset($context['all_pages'][$_GET['page']]))
    $_GET['page'] = 'index';

  $context['current_page'] = $_GET['page'];
  $context['sub_template'] = 'manual_' . $context['all_pages'][$context['current_page']];
  $context['template_layers'][] = 'manual';
  $context['page_title'] = $txt['enlazanos'];
}

function ShowAdminHelp() {
  global $txt, $helptxt, $context, $scripturl;

  if (!isset($_GET['help']))
    fatal_lang_error('no_access');

  loadLanguage('Enlazanos');

  if (isset($_GET['help']) && substr($_GET['help'], 0, 14) == 'permissionhelp')
    loadLanguage('ManagePermissions');

  loadTemplate('Enlazanos');

  $context['page_title'] = $txt['enlazanos'];
  $context['template_layers'] = array();
  $context['sub_template'] = 'popup';

  if (isset($helptxt[$_GET['help']]))
    $context['help_text'] = &$helptxt[$_GET['help']];
  else if (isset($txt[$_GET['help']]))
    $context['help_text'] = &$txt[$_GET['help']];
  else
    $context['help_text'] = $_GET['help'];

  if (preg_match('~%([0-9]+\$)?s\?~', $context['help_text'], $match)) {
    $context['help_text'] = sprintf($context['help_text'], $scripturl, $context['session_id']);
  }
}

?>