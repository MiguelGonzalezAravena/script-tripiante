<?php
if (!defined('SMF'))
  die('Hacking attempt...');

function ShowHelp() {
  global $settings, $user_info, $context, $txt;

  loadTemplate('Widget');
  loadLanguage('Manual');

  $context['all_pages'] = array(
    'index' => 'intro',
  );

  if (!isset($_GET['page']) || !isset($context['all_pages'][$_GET['page']]))
    $_GET['page'] = 'index';

  $context['current_page'] = $_GET['page'];
  $context['sub_template'] = 'manual_' . $context['all_pages'][$context['current_page']];

  $context['template_layers'][] = 'manual';
  $txt['Titulo'] = 'Widget'; 
  $context['page_title'] = $txt['Titulo'];

  $context['html_headers'] .= '
    <link rel="stylesheet" type="text/css" href="' . (file_exists($settings['theme_dir'] . '/style.css') ? $settings['theme_url'] : $settings['default_theme_url']) . '/style.css" />';

  $request = db_query("
    SELECT b.ID_BOARD, b.ID_PARENT, b.childLevel, b.name, b.description, b.numTopics, b.numPosts
    FROM {$db_prefix}boards as b
    WHERE $user_info[query_see_board]
    ORDER BY b.boardOrder", __FILE__, __LINE__);

  // And assign it to an array
  while ($row = mysqli_fetch_assoc($request)) {
    $context['sitemap']['board'][$row['ID_BOARD']] = array(
      'id' => $row['ID_BOARD'],
      'level' => $row['childLevel'],
      'has_children' => false,
      'name' => $row['name'],
      'description' => $row['description'],
      'numt' => $row['numTopics'],
      'nump' => $row['numPosts'],
    );

    if(!empty($row['childLevel']) && $row['childLevel'] == '1') {
      $context['sitemap']['board'][$row['ID_PARENT']]['has_children'] = true;
      $context['sitemap']['collapsible'] = $context['sitemap']['collapsible'] + array($row['ID_PARENT'] => $row['ID_PARENT']);
    }
  }

  $context['sitemap']['collapsible'] = '\'parent' . implode('\', \'parent', $context['sitemap']['collapsible']) . '\'';

  mysqli_free_result($request);
}

?>