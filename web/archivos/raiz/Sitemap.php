<?php
if (!defined('SMF'))
  die('Hacking attempt...');

function ShowSiteMap() {
  global $context, $scripturl, $txt, $user_info, $db_prefix, $modSettings;

  $context['page_title'] = $txt['sitemap'];
  loadtemplate('Sitemap');
  $request = db_query("
    SELECT t.ID_TOPIC
    FROM {$db_prefix}messages as m, {$db_prefix}topics as t, {$db_prefix}boards as b
    WHERE m.ID_MSG = t.ID_LAST_MSG
    AND b.ID_BOARD = m.ID_BOARD
    AND $user_info[query_see_board]
    LIMIT 10", __FILE__, __LINE__);
    
  $_REQUEST['start'] = ($_REQUEST['start'] > $modSettings['sitemap_topic_count']) ? $modSettings['sitemap_topic_count'] : $_REQUEST['start'];
  $context['page_index'] = constructPageIndex($scripturl . '?action=sitemap', $_REQUEST['start'], mysqli_num_rows($request), 100);

  if (strpos($_SERVER['QUERY_STRING'], 'start') === false)
    $context['page_index'] = str_replace('[<b>1</b>]', '<a class="navPages" href="' . $scripturl . '?action=sitemap;start=0">1</a>', $context['page_index']);

  // Check to see if we're viewing topics or the boards or xml sitemap
  if (isset($_REQUEST['xml'])) {
    XMLDisplay();
  } else if (strpos($_SERVER['QUERY_STRING'], 'start') !== false) {
    TopicDisplay(($_REQUEST['start']));
  } else {
    BoardDisplay();
  }
}

function BoardDisplay() {
  global $context, $db_prefix, $user_info;

  $context['sub_template'] = 'Boards';
  $context['sitemap']['collapsible'] = array();
  $request = db_query("
    SELECT b.ID_BOARD, b.ID_PARENT, b.childLevel, b.name, b.description, b.numTopics, b.numPosts
    FROM {$db_prefix}boards as b
    WHERE $user_info[query_see_board]
    ORDER BY b.boardOrder", __FILE__, __LINE__);

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

function TopicDisplay($start) {
  global $context, $db_prefix, $user_info, $scripturl, $modSettings;

  $context['sub_template'] = 'Topics';
  $end = $modSettings['sitemap_topic_count'] - $start < 100 ? $modSettings['sitemap_topic_count'] - $start : 100;

  $request = db_query("
    SELECT
      m.ID_MSG, m.ID_TOPIC, t.puntos, t.numViews, m.ID_BOARD, m.subject, m.posterName,
      m.posterTime, m.posterName, t.ID_FIRST_MSG, b.name, m.hiddenOption
    FROM {$db_prefix}messages as m, {$db_prefix}topics as t, {$db_prefix}boards as b
    WHERE m.ID_MSG = t.ID_FIRST_MSG
    AND b.ID_BOARD = m.ID_BOARD
    AND $user_info[query_see_board]
    ORDER BY m.ID_TOPIC DESC
    LIMIT $start, $end", __FILE__, __LINE__);

  while ($row = mysqli_fetch_assoc($request)) {
    $context['sitemap']['topic'][] = array(
      'privado' => $row['hiddenOption'],
      'subject' => $row['subject'],
      'poster' => $row['posterName'],
      'views' => $row['numViews'],
      'puntos' => $row['puntos'],
      'id' => $row['ID_TOPIC'],
      'fecha' => timeformat($row['posterTime']),		
      'href' => $scripturl . '?topic=' . $row['ID_TOPIC'] . '',
      'board_name' => $row['name'],
      'ID_BOARD' => $row['ID_BOARD'],
      'board_href' => $scripturl . '?id=' . $row['ID_BOARD'] . '',
    );
  }

  // Free the result
  mysqli_free_result($request);
}

function XMLDisplay() {
  global $db_prefix, $context, $user_info, $modSettings;

  $context['sub_template'] = 'XMLDisplay';
  $context['sitemap']['main'] = array('time' => date_iso8601());

  $request = db_query("
    SELECT b.ID_BOARD, m.posterTime
    FROM {$db_prefix}boards as b, {$db_prefix}messages as m
    WHERE m.ID_MSG = b.ID_LAST_MSG
    AND $user_info[query_see_board]
    ORDER BY m.posterTime DESC", __FILE__, __LINE__);

  while ($row = mysqli_fetch_assoc($request)) {
    $context['sitemap']['board'][] = array(
      'id' => $row['ID_BOARD'] . '',
      'time' => date_iso8601($row['posterTime']),
    );
  }

  mysqli_free_result($request);

  $request = db_query("
    SELECT t.ID_TOPIC, m.posterTime
    FROM {$db_prefix}messages as m, {$db_prefix}topics as t, {$db_prefix}boards as b
    WHERE m.ID_MSG = t.ID_LAST_MSG
    AND b.ID_BOARD = m.ID_BOARD
    AND $user_info[query_see_board]
    ORDER BY m.posterTime DESC
    LIMIT $modSettings[sitemap_topic_count]", __FILE__, __LINE__);

  while ($row = mysqli_fetch_assoc($request)) {
    $context['sitemap']['topic'][] = array(
      'id' => $row['ID_TOPIC'] . '',
      'time' => date_iso8601($row['posterTime']),
    );
  }

  mysqli_free_result($request);
}

function date_iso8601($timestamp = '') {
  $timestamp = empty($timestamp) ? time() : $timestamp;
  $gmt = substr(date("O", $timestamp), 0, 3) . ':00';

  return date('Y-m-d\TH:i:s', $timestamp) . $gmt;
}

?>