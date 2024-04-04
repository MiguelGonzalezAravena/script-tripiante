<?php
if (!defined('SMF'))
  die('Hacking attempt...');

function TagsMain() {
  loadtemplate('Tags');

  if (loadlanguage('Tags') == false)
    loadLanguage('Tags','english');

  $subActions = array(
    'suggest' => 'SuggestTag',
    'suggest2' => 'SuggestTag2',
    'addtag' => 'AddTag',
    'addtag2' => 'AddTag2',
    'deletetag' => 'DeleteTag',
    'admin' => 'TagsSettings',
    'admin2' => 'TagsSettings2',
    'cleanup' => 'TagCleanUp',
  );

  if (!empty($subActions[@$_GET['sa']]))
    $subActions[$_GET['sa']]();
  else
    ViewTags();
}

function ViewTags() {
  global $context, $boardurl, $txt, $db_prefix, $modSettings;

  $context['page_title'] = $txt[18];

  if ($_REQUEST['order'] == 1 || empty($_REQUEST['order'])) {
    $result = db_query("
      SELECT t.tag AS tag, l.ID_TAG, COUNT(l.ID_TAG) AS quantity
      FROM ({$db_prefix}tags as t, {$db_prefix}tags_log as l)
      WHERE t.ID_TAG = l.ID_TAG 
      GROUP BY l.ID_TAG
      ORDER BY t.tag ASC
      LIMIT " . $modSettings['smftags_set_cloud_tags_to_show'], __FILE__, __LINE__);
  } else if ($_REQUEST['order'] == 2) {
    $result = db_query("
      SELECT t.tag AS tag, l.ID_TAG, COUNT(l.ID_TAG) AS quantity
      FROM ({$db_prefix}tags as t, {$db_prefix}tags_log as l)
      WHERE t.ID_TAG = l.ID_TAG 
      GROUP BY l.ID_TAG
      ORDER BY COUNT(l.ID_TAG) DESC
      LIMIT " . $modSettings['smftags_set_cloud_tags_to_show'], __FILE__, __LINE__);
  }

  $tags = array();
  $tags2 = array();
    
  while ($row = mysqli_fetch_array($result)) {
    $tags[$row['tag']] = $row['quantity'];
    $tags2[$row['tag']] = $row['ID_TAG'];
  }
    
  if(count($tags2) > 0) {
    $max_size = $modSettings['smftags_set_cloud_max_font_size_precent'];
    $min_size = $modSettings['smftags_set_cloud_min_font_size_precent'];
    $max_qty = max(array_values($tags));
    $min_qty = min(array_values($tags));
    $spread = $max_qty - $min_qty;

    if (0 == $spread) {
      $spread = 1;
    }

    $step = ($max_size - $min_size) / $spread;
    $context['poptags'] = '';
    $row_count = 0;

    foreach ($tags as $key => $value) {
      $row_count++;
      $size = $min_size - $max_size - (($value - $min_qty) / $step);
      $context['poptags'] .= '<a href="' . $boardurl . '/tags/' . $key . '" style="font-size: ' . number_format($size) . 'pt; margin-right: 2px; margin-bottom: 5px;" title="' . $value . ' post con el tag ' . $key . '">' . $key . '</a> ';
      if ($row_count > $modSettings['smftags_set_cloud_tags_per_row']) {
        $context['poptags'] .= '<br />';
        $row_count =0;
      }
    }
  }
}

function AddTag() {
  global $context, $txt, $db_prefix, $ID_MEMBER;

  // Get the Topic
  $topic = (int) $_REQUEST['topic'];

  if (empty($topic))
    fatal_error($txt['smftags_err_notopic'], false);

  // Check permission
  $a_manage = allowedTo('smftags_manage');
  $dbresult = db_query("
    SELECT m.ID_MEMBER
    FROM {$db_prefix}topics as t, {$db_prefix}messages as m
    WHERE t.ID_FIRST_MSG = m.ID_MSG
    AND t.ID_TOPIC = $topic
    LIMIT 1", __FILE__, __LINE__);
  
  $row = mysqli_fetch_assoc($dbresult);

  mysqli_free_result($dbresult);
  
  if ($ID_MEMBER != $row['ID_MEMBER'] && $a_manage == false)
    fatal_error($txt['smftags_err_permaddtags'], false);

  $context['tags_topic'] = $topic;
  $context['sub_template'] = 'addtag';
  $context['page_title'] = $txt['smftags_addtag2'];
}

function AddTag2() {
  global $db_prefix, $txt, $modSettings, $ID_MEMBER;

  $topic = (int) $_REQUEST['topic'];

  if (empty($topic))
    fatal_error($txt['smftags_err_notopic'], false);

  // Check Permission
  $a_manage = allowedTo('smftags_manage');
    
  $dbresult = db_query("
    SELECT m.ID_MEMBER
    FROM {$db_prefix}topics as t, {$db_prefix}messages as m
    WHERE t.ID_FIRST_MSG = m.ID_MSG
    AND t.ID_TOPIC = $topic
    LIMIT 1", __FILE__, __LINE__);

  $row = mysqli_fetch_assoc($dbresult);

  mysqli_free_result($dbresult);

  if ($ID_MEMBER != $row['ID_MEMBER'] && $a_manage == false)
    fatal_error($txt['smftags_err_permaddtags'], false);

  $dbresult = db_query("
    SELECT COUNT(*) as total
    FROM {$db_prefix}tags_log 
    WHERE ID_TOPIC = " . $topic, __FILE__, __LINE__);
  
  $row = mysqli_fetch_assoc($dbresult);
  $totaltags = $row['total'];

  mysqli_free_result($dbresult);

  if ($totaltags >= $modSettings['smftags_set_maxtags'])
    fatal_error($txt['smftags_err_toomaxtag'], false);

  // Check Tag restrictions
  $tag = htmlspecialchars(trim($_REQUEST['tag']), ENT_QUOTES);
  
  if (empty($tag))
    fatal_error($txt['smftags_err_notag'], false);

  if (strlen($tag) < $modSettings['smftags_set_mintaglength'])
    fatal_error($txt['smftags_err_mintag'] .  $modSettings['smftags_set_mintaglength'], false);

  if (strlen($tag) > $modSettings['smftags_set_maxtaglength'])
    fatal_error($txt['smftags_err_maxtag'] . $modSettings['smftags_set_maxtaglength'], false);

  $dbresult = db_query("
    SELECT ID_TAG
    FROM {$db_prefix}tags
    WHERE tag = '$tag'", __FILE__, __LINE__);

  if (db_affected_rows() == 0) {
    db_query("
      INSERT INTO {$db_prefix}tags(tag, approved)
      VALUES ('$tag',1)", __FILE__, __LINE__);

    $ID_TAG = db_insert_id();

    db_query("
      INSERT INTO {$db_prefix}tags_log(ID_TAG, ID_TOPIC, ID_MEMBER)
      VALUES ($ID_TAG, $topic, $ID_MEMBER)", __FILE__, __LINE__);
  } else  {
    $row = mysqli_fetch_assoc($dbresult);
    $ID_TAG = $row['ID_TAG'];
    $dbresult2 = db_query("
      SELECT ID
      FROM {$db_prefix}tags_log
      WHERE ID_TAG = $ID_TAG
      AND ID_TOPIC = $topic", __FILE__, __LINE__);

    if (db_affected_rows() != 0) {
      fatal_error($$txt['smftags_err_alreadyexists'], false);
    }

    mysqli_free_result($dbresult2);

    db_query("
      INSERT INTO {$db_prefix}tags_log(ID_TAG, ID_TOPIC, ID_MEMBER)
      VALUES ($ID_TAG, $topic, $ID_MEMBER)", __FILE__, __LINE__);
  }

  mysqli_free_result($dbresult);
  redirectexit('topic=' . $topic);
}

function DeleteTag() {
  global $db_prefix, $ID_MEMBER, $txt;
  
  $id = (int) $_REQUEST['tagid'];
  $a_manage = allowedTo('smftags_manage');
  $dbresult = db_query("
    SELECT ID_MEMBER, ID_TOPIC, ID_TAG
    FROM {$db_prefix}tags_log
    WHERE ID = $id
    LIMIT 1", __FILE__, __LINE__);

  $row = mysqli_fetch_assoc($dbresult);

  mysqli_free_result($dbresult);

  if ($row['ID_MEMBER'] != $ID_MEMBER && $a_manage == false)
    fatal_error($txt['smftags_err_deletetag'], false);

  db_query("
    DELETE FROM {$db_prefix}tags_log
    WHERE ID = $id
    LIMIT 1", __FILE__, __LINE__);

  TagCleanUp($row['ID_TAG']);
  redirectexit('topic=' . $row['ID_TOPIC']);
}

function TagsSettings() {
  global $context, $txt, $mbname;

  adminIndex('tags_settings');
  isAllowedTo('smftags_manage');

  $context['sub_template']  = 'admin_settings';
  $context['page_title'] = $mbname . ' - ' . $txt['smftags_settings'];
}

function TagsSettings2() {
  isAllowedTo('smftags_manage');

  $smftags_set_mintaglength = (int) $_REQUEST['smftags_set_mintaglength'];
  $smftags_set_maxtaglength = (int) $_REQUEST['smftags_set_maxtaglength'];
  $smftags_set_maxtags = (int) $_REQUEST['smftags_set_maxtags'];
  $smftags_set_cloud_tags_per_row = (int) $_REQUEST['smftags_set_cloud_tags_per_row'];
  $smftags_set_cloud_tags_to_show = (int) $_REQUEST['smftags_set_cloud_tags_to_show'];
  $smftags_set_cloud_max_font_size_precent = (int) $_REQUEST['smftags_set_cloud_max_font_size_precent'];
  $smftags_set_cloud_min_font_size_precent = (int) $_REQUEST['smftags_set_cloud_min_font_size_precent'];

  updateSettings(
    array('smftags_set_maxtags' => $smftags_set_maxtags,
    'smftags_set_mintaglength' => $smftags_set_mintaglength,
    'smftags_set_maxtaglength' => $smftags_set_maxtaglength,
    'smftags_set_cloud_tags_per_row' => $smftags_set_cloud_tags_per_row,
    'smftags_set_cloud_tags_to_show' => $smftags_set_cloud_tags_to_show,
    'smftags_set_cloud_max_font_size_precent' => $smftags_set_cloud_max_font_size_precent,
    'smftags_set_cloud_min_font_size_precent' => $smftags_set_cloud_min_font_size_precent,
    )
  );

  redirectexit('action=tags;sa=admin');
}

function TagCleanUp($ID_TAG) {
  global $db_prefix;

  $dbresult2 = db_query("
    SELECT ID
    FROM {$db_prefix}tags_log
    WHERE ID_TAG = " . $ID_TAG, __FILE__, __LINE__);
  
  if (db_affected_rows() == 0) {
    db_query("
      DELETE FROM {$db_prefix}tags
      WHERE ID_TAG = " . $ID_TAG, __FILE__, __LINE__);
  }

  mysqli_free_result($dbresult2);
}

function SuggestTag() {
  global $context, $txt, $mbname;

  isAllowedTo('smftags_suggest');
  
  $context['sub_template'] = 'suggest';
  $context['page_title'] = $mbname . ' - ' . $txt['smftags_suggest'];
}

function SuggestTag2() {
  isAllowedTo('smftags_suggest');
}

?>