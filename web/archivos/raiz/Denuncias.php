<?php
if (!defined('SMF'))
	die('Hacking attempt...');

function Denuncias()
{
	global $settings, $user_info, $language, $context, $txt;

	loadTemplate('Denuncias');
	isAllowedTo('manage_bans');
	adminIndex('denuncias');
	
	$context['all_pages'] = array(
		'index' => 'main',
		'imagen' => 'imagen',	
		'user' => 'user',	
		'comunidades' => 'comunidades',	
		'eliminar' => 'eliminar',		
	);
	if (!isset($_GET['m']) || !isset($context['all_pages'][$_GET['m']]))
		$_GET['m'] = 'index';

	$context['current_page'] = $_GET['m'];
	$context['sub_template'] = $context['all_pages'][$context['current_page']];
	$context['page_title'] = 'Panel de Denuncias';

}
?>