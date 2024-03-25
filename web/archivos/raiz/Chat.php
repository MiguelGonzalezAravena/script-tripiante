<?php
/*
Chat por Peludo_08 & Miguelithox
*/

if (!defined('SMF'))
	die('Hacking attempt...');

function Chat()
{
	global $settings, $user_info, $language, $context, $txt, $slogan;

	// Load the main template file
	loadtemplate('Chat');

	// Load the language files
	if (loadlanguage('Chat') == false)
		loadLanguage('Chat','english');

	// All the available pages.
	$context['all_pages'] = array(
		'index' => 'intro',
	);

	if (!isset($_GET['page']) || !is_string($_GET['page']) || !isset($context['all_pages'][$_GET['page']]))
		$_GET['page'] = 'index';

	$context['current_page'] = $_GET['page'];
	$context['sub_template'] = 'manual_' . $context['all_pages'][$context['current_page']];

	$context['template_layers'][] = 'manual';
	$context['page_title'] = $slogan;

	// We actually need a special style sheet for help ;)
	$context['html_headers'] .= '
		<link rel="stylesheet" type="text/css" href="' . (file_exists($settings['theme_dir'] . '/help.css') ? $settings['theme_url'] : $settings['default_theme_url']) . '/help.css" />';
}
?>