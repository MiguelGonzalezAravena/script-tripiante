<?php
if (!defined('SMF'))
	die('Hacking attempt...');

function Hacer()
{
	global $settings, $user_info, $language, $context, $txt, $db_prefix;

	@require_once('SSI.php');
	ssi_grupos();

	loadTemplate('Do');
	
	$context['all_pages'] = array(
		'index' => 'intro',
		'editari' => 'editari',
		'post-agregado' => 'postagregado',
		'post-editado' => 'posteditado',
		'eliminarc' => 'eliminarc',
		'eliminarci' => 'eliminarci',
		'enviardenuncia' => 'enviardenuncia',
		'comunidadagregada' => 'comunidadagregada',
		'temaagregado' => 'temaagregado',
		'eliminarres' => 'eliminarres',
		
	);
	if (!isset($_GET['m']) || !isset($context['all_pages'][$_GET['m']]))
		$_GET['m'] = 'index';

	$context['current_page'] = $_GET['m'];
	$context['sub_template'] = '' . $context['all_pages'][$context['current_page']];

	$context['page_title'] = $txt[18];
}

?>