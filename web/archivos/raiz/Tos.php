<?php

if (!defined('SMF'))
	die('Hacking attempt...');

function Tos()
{
	global $settings, $user_info, $language, $context, $txt;

	loadTemplate('Tos');
	loadLanguage('Manual');
	$context['page_title'] = $txt[18];

	$context['all_pages'] = array(
		'index' => 'intro',
	);

	if (!isset($_GET['page']) || !isset($context['all_pages'][$_GET['page']]))
		$_GET['page'] = 'index';

	$context['current_page'] = $_GET['page'];
	$context['sub_template'] = 'manual_' . $context['all_pages'][$context['current_page']];

	$context['template_layers'][] = 'manual';
	  $txt['Titulo'] = $txt[18]; 
	$context['page_title'] = $txt[18];

}
?>