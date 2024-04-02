<?php
if (!defined('SMF'))
  die('Hacking attempt...');

function PrintTopic() {
  global $db_prefix, $topic, $context;
  global $modSettings;
  global $board_info;

  if (empty($topic))
    fatal_lang_error(472, false);

  // Get the topic starter information.
  $request = db_query("
    SELECT m.posterTime, IFNULL(mem.realName, m.posterName) AS posterName
    FROM {$db_prefix}messages AS m
      LEFT JOIN {$db_prefix}members AS mem ON (mem.ID_MEMBER = m.ID_MEMBER)
    WHERE m.ID_TOPIC = $topic
    ORDER BY ID_MSG
    LIMIT 1", __FILE__, __LINE__);

  if (mysqli_num_rows($request) == 0)
    fatal_lang_error('smf232');

  $row = mysqli_fetch_assoc($request);

  mysqli_free_result($request);

  // Lets "output" all that info.
  loadTemplate('Printpage');
  $context['template_layers'] = array('print');
  $context['board_name'] = $board_info['name'];
  $context['category_name'] = $board_info['cat']['name'];
  $context['poster_name'] = $row['posterName'];
  $context['post_time'] = timeformat($row['posterTime'], false);

  // Split the topics up so we can print them.
  $request = db_query("
    SELECT m.ID_TOPIC, m.subject, m.posterTime, m.body, IFNULL(mem.realName, m.posterName) AS posterName
      ,m.hiddenOption, m.hiddenValue, m.ID_BOARD, m.ID_MEMBER, b.ID_BOARD, b.description
    FROM ({$db_prefix}messages AS m, boards as b)
      LEFT JOIN {$db_prefix}members AS mem ON (mem.ID_MEMBER = m.ID_MEMBER)
    WHERE m.ID_TOPIC = $topic
    AND m.ID_BOARD = b.ID_BOARD
    ORDER BY m.ID_MSG
    LIMIT 1", __FILE__, __LINE__);

  $context['posts'] = array();

  while ($row = mysqli_fetch_assoc($request)) {
    // Hide the post or Not? For print results. --- XD
    $row['can_view_post'] = 1;
    if (!empty($modSettings['allow_hiddenPost']) && $row['hiddenOption'] > 0) {
      global $sourcedir;

      require_once($sourcedir . '/HidePost.php');

      $row['ID_TOPIC'] = $topic;
      $context['current_message'] = $row;
      $row['body'] = getHiddenMessage(1);
      $row['can_view_post'] = $context['can_view_post'];
    }

    // Censor the subject and message.
    censorText($row['subject']);
    censorText($row['body']);

    @require_once('SSI.php');

    $context['posts'][] = array(
      'subject' => $row['subject'],
      'ID_TOPIC' => $row['ID_TOPIC'],
      'description' => $row['description'],
      'member' => $row['posterName'],
      'time' =>  timeformat($row['posterTime'], false),
      'timestamp' => forum_time(true, $row['posterTime']),
      'body' => parse_bbc($row['body'], 'print'),
      'subject_html' => ssi_amigable($row['subject']),
    );

    if (!isset($context['topic_subject']))
      $context['topic_subject'] = $row['subject'];
  }

  mysqli_free_result($request);
}

?>