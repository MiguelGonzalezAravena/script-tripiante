<?php
if (!defined('SMF'))
  die('Hacking attempt...');

function Publicity() {
  global $context;

  loadTemplate('Publicity');
  isAllowedTo('admin_forum');
  adminIndex('publicity');

  $context['all_pages'] = array(
    'index' => 'main',
    'guardar' => 'guardar',
  );

  if (!isset($_GET['m']) || !isset($context['all_pages'][$_GET['m']]))
    $_GET['m'] = 'index';

  $context['current_page'] = $_GET['m'];
  $context['sub_template'] = $context['all_pages'][$context['current_page']];
  $context['page_title'] = 'Publicidad';
}

?>