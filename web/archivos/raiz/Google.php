<?php
if (!defined('SMF'))
	die('Hacking attempt...');

function Google()
{
	global $settings, $user_info, $language, $context, $txt;

	loadTemplate('Google');
	loadLanguage('Manual');

	$context['all_pages'] = array(
		'index' => 'intro',
	);

	if (!isset($_GET['page']) || !isset($context['all_pages'][$_GET['page']]))
		$_GET['page'] = 'index';

	$context['current_page'] = $_GET['page'];
	$context['sub_template'] = 'manual_' . $context['all_pages'][$context['current_page']];

	$context['template_layers'][] = 'manual';
	  $txt['Titulo'] = "Buscador"; 
	$context['page_title'] = $txt['Titulo'];

	$context['html_headers'] .= '
		<link rel="stylesheet" type="text/css" href="' . (file_exists($settings['theme_dir'] . '/style.css') ? $settings['theme_url'] : $settings['default_theme_url']) . '/style.css" />';
}
?>