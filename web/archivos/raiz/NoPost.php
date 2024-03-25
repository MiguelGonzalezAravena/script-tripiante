<?php
if (!defined('SMF'))
	die('Hacking attempt...');

function NoPost();
{
	global $settings, $context, $txt;
	
	loadLanguage('Errors');
	loadTemplate('NoPost');
	$context['sub_template'] = 'NoPost';

}
?>