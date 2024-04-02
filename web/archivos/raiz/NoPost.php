<?php
if (!defined('SMF'))
  die('Hacking attempt...');

function NoPost() {
  global $context;
  
  loadLanguage('Errors');
  loadTemplate('NoPost');
  $context['sub_template'] = 'NoPost';
}

?>