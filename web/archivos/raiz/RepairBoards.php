<?php
if (!defined('SMF'))
  die('Hacking attempt...');

function RepairBoards() {
  global $db_prefix, $txt, $scripturl, $db_connection, $sc, $context, $sourcedir;
  global $salvageCatID, $salvageBoardID;

  isAllowedTo('admin_forum');
  adminIndex('maintain_forum');

  $context['page_title'] = $txt[610];
  $context['sub_template'] = 'rawdata';

  if (isset($_GET['fixErrors'])) {
    checkSession('get');
  }

  if (!isset($_GET['fixErrors'])) {
    $context['repair_errors'] = array();
    $to_fix = findForumErrors();
    if (!empty($to_fix)) {
      $_SESSION['repairboards_to_fix'] = $to_fix;
      $_SESSION['repairboards_to_fix2'] = null;
      if (empty($context['repair_errors'])) {
        $context['repair_errors'][] = '???';
      }
    }

    $context['raw_data'] = '
      <table width="100%" border="0" cellspacing="0" cellpadding="4" class="tborder">
        <tr class="titlebg">
          <td>' . $txt['smf73'] . '</td>
        </tr>
        <tr>
          <td class="windowbg">';

    if (!empty($to_fix)) {
      $context['raw_data'] .= '
            ' . $txt['smf74'] . ':<br />
            ' . implode('
            <br />', $context['repair_errors']) . '<br />
            <br />
            ' . $txt['smf85'] . '<br />
            <b><a href="' . $scripturl . '?action=repairboards;fixErrors;sesc=' . $sc . '">' . $txt[163] . '</a> - <a href="' . $scripturl . '?action=maintain">' . $txt[164] . '</a></b>';
    }
    else
      $context['raw_data'] .= '
            ' . $txt['maintain_no_errors'] . '<br />
            <br />
            <a href="' . $scripturl . '?action=maintain">' . $txt['maintain_return'] . '</a>';

    $context['raw_data'] .= '
          </td>
        </tr>
      </table>';
  }
  else
  {
    $to_fix = isset($_SESSION['repairboards_to_fix']) ? $_SESSION['repairboards_to_fix'] : array();

    require_once($sourcedir . '/Subs-Boards.php');

    // Get the MySQL version for future reference.
    $mysql_version = mysqli_get_server_info($db_connection);

    if (empty($to_fix) || in_array('zero_ids', $to_fix))
    {
      // We don't allow 0's in the IDs...
      db_query("
        UPDATE {$db_prefix}topics
        SET ID_TOPIC = NULL
        WHERE ID_TOPIC = 0", __FILE__, __LINE__);

      db_query("
        UPDATE {$db_prefix}messages
        SET ID_MSG = NULL
        WHERE ID_MSG = 0", __FILE__, __LINE__);
    }

    // Remove all topics that have zero messages in the messages table.
    if (empty($to_fix) || in_array('missing_messages', $to_fix))
    {
      $resultTopic = db_query("
        SELECT t.ID_TOPIC, COUNT(m.ID_MSG) AS numMsg
        FROM {$db_prefix}topics AS t
          LEFT JOIN {$db_prefix}messages AS m ON (m.ID_TOPIC = t.ID_TOPIC)
        GROUP BY t.ID_TOPIC
        HAVING numMsg = 0", __FILE__, __LINE__);
      if (mysqli_num_rows($resultTopic) > 0)
      {
        $stupidTopics = array();
        while ($topicArray = mysqli_fetch_assoc($resultTopic))
          $stupidTopics[] = $topicArray['ID_TOPIC'];
        db_query("
          DELETE FROM {$db_prefix}topics
          WHERE ID_TOPIC IN (" . implode(',', $stupidTopics) . ')
          LIMIT ' . count($stupidTopics), __FILE__, __LINE__);
        db_query("
          DELETE FROM {$db_prefix}log_topics
          WHERE ID_TOPIC IN (" . implode(',', $stupidTopics) . ')', __FILE__, __LINE__);
      }
      mysqli_free_result($resultTopic);
    }

    // Fix all messages that have a topic ID that cannot be found in the topics table.
    if (empty($to_fix) || in_array('missing_topics', $to_fix))
    {
      $result = db_query("
        SELECT
          m.ID_BOARD, m.ID_TOPIC, MIN(m.ID_MSG) AS myID_FIRST_MSG, MAX(m.ID_MSG) AS myID_LAST_MSG,
          COUNT(*) - 1 AS myNumReplies
        FROM {$db_prefix}messages AS m
          LEFT JOIN {$db_prefix}topics AS t ON (t.ID_TOPIC = m.ID_TOPIC)
        WHERE t.ID_TOPIC IS NULL
        GROUP BY m.ID_TOPIC", __FILE__, __LINE__);
      while ($row = mysqli_fetch_assoc($result))
      {
        // Only if we don't have a reasonable idea of where to put it.
        if ($row['ID_BOARD'] == 0)
        {
          createSalvageArea();
          $row['ID_BOARD'] = $salvageBoardID;
        }

        $memberStartedID = getMsgMemberID($row['myID_FIRST_MSG']);
        $memberUpdatedID = getMsgMemberID($row['myID_LAST_MSG']);

        db_query("
          INSERT INTO {$db_prefix}topics
            (ID_BOARD, ID_MEMBER_STARTED, ID_MEMBER_UPDATED, ID_FIRST_MSG, ID_LAST_MSG, numReplies)
          VALUES ($row[ID_BOARD], $memberStartedID, $memberUpdatedID,
            $row[myID_FIRST_MSG], $row[myID_LAST_MSG], $row[myNumReplies])", __FILE__, __LINE__);
        $newTopicID = db_insert_id();

        db_query("
          UPDATE {$db_prefix}messages
          SET ID_TOPIC = $newTopicID, ID_BOARD = $row[ID_BOARD]
          WHERE ID_TOPIC = $row[ID_TOPIC]", __FILE__, __LINE__);
      }
      mysqli_free_result($result);
    }

    // Fix all ID_FIRST_MSG, ID_LAST_MSG and numReplies in the topic table.
    if (empty($to_fix) || in_array('stats_topics', $to_fix))
    {
      $resultTopic = db_query("
        SELECT
          t.ID_TOPIC, MIN(m.ID_MSG) AS myID_FIRST_MSG, t.ID_FIRST_MSG,
          MAX(m.ID_MSG) AS myID_LAST_MSG, t.ID_LAST_MSG, COUNT(m.ID_MSG) - 1 AS myNumReplies,
          t.numReplies
        FROM {$db_prefix}topics AS t
          LEFT JOIN {$db_prefix}messages AS m ON (m.ID_TOPIC = t.ID_TOPIC)
        GROUP BY t.ID_TOPIC
        HAVING ID_FIRST_MSG != myID_FIRST_MSG OR ID_LAST_MSG != myID_LAST_MSG OR numReplies != myNumReplies", __FILE__, __LINE__);
      while ($topicArray = mysqli_fetch_assoc($resultTopic))
      {
        $memberStartedID = getMsgMemberID($topicArray['myID_FIRST_MSG']);
        $memberUpdatedID = getMsgMemberID($topicArray['myID_LAST_MSG']);
        db_query("
          UPDATE {$db_prefix}topics
          SET ID_FIRST_MSG = '$topicArray[myID_FIRST_MSG]',
            ID_MEMBER_STARTED = '$memberStartedID', ID_LAST_MSG = '$topicArray[myID_LAST_MSG]',
            ID_MEMBER_UPDATED = '$memberUpdatedID', numReplies = '$topicArray[myNumReplies]'
          WHERE ID_TOPIC = $topicArray[ID_TOPIC]
          LIMIT 1", __FILE__, __LINE__);
      }
      mysqli_free_result($resultTopic);
    }

    // Fix all topics that have a board ID that cannot be found in the boards table.
    if (empty($to_fix) || in_array('missing_boards', $to_fix))
    {
      $resultTopics = db_query("
        SELECT t.ID_BOARD, COUNT(*) AS myNumTopics, COUNT(m.ID_MSG) AS myNumPosts
        FROM {$db_prefix}topics AS t
          LEFT JOIN {$db_prefix}boards AS b ON (b.ID_BOARD = t.ID_BOARD)
          LEFT JOIN {$db_prefix}messages AS m ON (m.ID_TOPIC = t.ID_TOPIC)
        WHERE b.ID_BOARD IS NULL
        GROUP BY t.ID_BOARD", __FILE__, __LINE__);
      if (mysqli_num_rows($resultTopics) > 0)
        createSalvageArea();
      while ($topicArray = mysqli_fetch_assoc($resultTopics))
      {
        db_query("
          INSERT INTO {$db_prefix}boards
            (ID_CAT, name, description, numTopics, numPosts, memberGroups)
          VALUES ($salvageCatID, 'Salvaged board', '', $topicArray[myNumTopics], $topicArray[myNumPosts], '1')", __FILE__, __LINE__);
        $newBoardID = db_insert_id();

        db_query("
          UPDATE {$db_prefix}topics
          SET ID_BOARD = $newBoardID
          WHERE ID_BOARD = $topicArray[ID_BOARD]", __FILE__, __LINE__);
        db_query("
          UPDATE {$db_prefix}messages
          SET ID_BOARD = $newBoardID
          WHERE ID_BOARD = $topicArray[ID_BOARD]", __FILE__, __LINE__);
      }
      mysqli_free_result($resultTopics);
    }

    // Fix all boards that have a cat ID that cannot be found in the cats table.
    if (empty($to_fix) || in_array('missing_categories', $to_fix))
    {
      $resultBoards = db_query("
        SELECT b.ID_CAT
        FROM {$db_prefix}boards AS b
          LEFT JOIN {$db_prefix}categories AS c ON (c.ID_CAT = b.ID_CAT)
        WHERE c.ID_CAT IS NULL
        GROUP BY b.ID_CAT", __FILE__, __LINE__);
      if (mysqli_num_rows($resultBoards) > 0)
        createSalvageArea();
      while ($boardArray = mysqli_fetch_assoc($resultBoards))
      {
        db_query("
          UPDATE {$db_prefix}boards
          SET ID_CAT = $salvageCatID
          WHERE ID_CAT = $boardArray[ID_CAT]", __FILE__, __LINE__);

      }
      mysqli_free_result($resultBoards);
    }

    // Last step-make sure all non-guest posters still exist.
    if (empty($to_fix) || in_array('missing_posters', $to_fix))
    {
      $result = db_query("
        SELECT m.ID_MSG
        FROM {$db_prefix}messages AS m
          LEFT JOIN {$db_prefix}members AS mem ON (mem.ID_MEMBER = m.ID_MEMBER)
        WHERE m.ID_MEMBER != 0
          AND mem.ID_MEMBER IS NULL", __FILE__, __LINE__);
      if (mysqli_num_rows($result) > 0)
      {
        $guestMessages = array();
        while ($row = mysqli_fetch_assoc($result))
          $guestMessages[] = $row['ID_MSG'];
        db_query("
          UPDATE {$db_prefix}messages
          SET ID_MEMBER = 0
          WHERE ID_MSG IN (" . implode(',', $guestMessages) . ')
          LIMIT ' . count($guestMessages), __FILE__, __LINE__);
      }
      mysqli_free_result($result);
    }

    // Fix all boards that have a parent ID that cannot be found in the boards table.
    if (empty($to_fix) || in_array('missing_parents', $to_fix))
    {
      $resultParents = db_query("
        SELECT b.ID_PARENT
        FROM {$db_prefix}boards AS b
          LEFT JOIN {$db_prefix}boards AS p ON (p.ID_BOARD = b.ID_PARENT)
        WHERE b.ID_PARENT != 0
          AND (p.ID_BOARD IS NULL OR p.ID_BOARD = b.ID_BOARD)
        GROUP BY b.ID_PARENT", __FILE__, __LINE__);
      if (mysqli_num_rows($resultParents) > 0)
        createSalvageArea();
      while ($parentArray = mysqli_fetch_assoc($resultParents))
      {
        db_query("
          UPDATE {$db_prefix}boards
          SET ID_PARENT = $salvageBoardID, ID_CAT = $salvageCatID, childLevel = 1
          WHERE ID_PARENT = $parentArray[ID_PARENT]", __FILE__, __LINE__);
      }
      mysqli_free_result($resultParents);
    }

    if (empty($to_fix) || in_array('missing_log_topics', $to_fix))
    {
      $result = db_query("
        SELECT lt.ID_TOPIC
        FROM {$db_prefix}log_topics AS lt
          LEFT JOIN {$db_prefix}topics AS t ON (t.ID_TOPIC = lt.ID_TOPIC)
        WHERE t.ID_TOPIC IS NULL
        GROUP BY lt.ID_TOPIC", __FILE__, __LINE__);
      $topics = array();
      while ($row = mysqli_fetch_assoc($result))
        $topics[] = $row['ID_TOPIC'];
      mysqli_free_result($result);

      if (!empty($topics))
      {
        db_query("
          DELETE FROM {$db_prefix}log_topics
          WHERE ID_TOPIC IN (" . implode(', ', $topics) . ")", __FILE__, __LINE__);
      }
    }

    if (empty($to_fix) || in_array('missing_log_topics_members', $to_fix))
    {
      $result = db_query("
        SELECT lt.ID_MEMBER
        FROM {$db_prefix}log_topics AS lt
          LEFT JOIN {$db_prefix}members AS mem ON (mem.ID_MEMBER = lt.ID_MEMBER)
        WHERE mem.ID_MEMBER IS NULL
        GROUP BY lt.ID_MEMBER", __FILE__, __LINE__);
      $members = array();
      while ($row = mysqli_fetch_assoc($result))
        $members[] = $row['ID_MEMBER'];
      mysqli_free_result($result);

      if (!empty($members))
      {
        db_query("
          DELETE FROM {$db_prefix}log_topics
          WHERE ID_MEMBER IN (" . implode(', ', $members) . ")", __FILE__, __LINE__);
      }
    }

    if (empty($to_fix) || in_array('missing_log_boards', $to_fix))
    {
      $result = db_query("
        SELECT lb.ID_BOARD
        FROM {$db_prefix}log_boards AS lb
          LEFT JOIN {$db_prefix}boards AS b ON (b.ID_BOARD = lb.ID_BOARD)
        WHERE b.ID_BOARD IS NULL
        GROUP BY lb.ID_BOARD", __FILE__, __LINE__);
      $boards = array();
      while ($row = mysqli_fetch_assoc($result))
        $boards[] = $row['ID_BOARD'];
      mysqli_free_result($result);

      if (!empty($boards))
      {
        db_query("
          DELETE FROM {$db_prefix}log_boards
          WHERE ID_BOARD IN (" . implode(', ', $boards) . ")", __FILE__, __LINE__);
      }
    }

    if (empty($to_fix) || in_array('missing_log_boards_members', $to_fix))
    {
      $result = db_query("
        SELECT lb.ID_MEMBER
        FROM {$db_prefix}log_boards AS lb
          LEFT JOIN {$db_prefix}members AS mem ON (mem.ID_MEMBER = lb.ID_MEMBER)
        WHERE mem.ID_MEMBER IS NULL
        GROUP BY lb.ID_MEMBER", __FILE__, __LINE__);
      $members = array();
      while ($row = mysqli_fetch_assoc($result))
        $members[] = $row['ID_MEMBER'];
      mysqli_free_result($result);

      if (!empty($members))
      {
        db_query("
          DELETE FROM {$db_prefix}log_boards
          WHERE ID_MEMBER IN (" . implode(', ', $members) . ")", __FILE__, __LINE__);
      }
    }

    if (empty($to_fix) || in_array('missing_log_mark_read', $to_fix))
    {
      $result = db_query("
        SELECT lmr.ID_BOARD
        FROM {$db_prefix}log_mark_read AS lmr
          LEFT JOIN {$db_prefix}boards AS b ON (b.ID_BOARD = lmr.ID_BOARD)
        WHERE b.ID_BOARD IS NULL
        GROUP BY lmr.ID_BOARD", __FILE__, __LINE__);
      $boards = array();
      while ($row = mysqli_fetch_assoc($result))
        $boards[] = $row['ID_BOARD'];
      mysqli_free_result($result);

      if (!empty($boards))
      {
        db_query("
          DELETE FROM {$db_prefix}log_mark_read
          WHERE ID_BOARD IN (" . implode(', ', $boards) . ")", __FILE__, __LINE__);
      }
    }

    if (empty($to_fix) || in_array('missing_log_mark_read_members', $to_fix))
    {
      $result = db_query("
        SELECT lmr.ID_MEMBER
        FROM {$db_prefix}log_mark_read AS lmr
          LEFT JOIN {$db_prefix}members AS mem ON (mem.ID_MEMBER = lmr.ID_MEMBER)
        WHERE mem.ID_MEMBER IS NULL
        GROUP BY lmr.ID_MEMBER", __FILE__, __LINE__);
      $members = array();
      while ($row = mysqli_fetch_assoc($result))
        $members[] = $row['ID_MEMBER'];
      mysqli_free_result($result);

      if (!empty($members))
      {
        db_query("
          DELETE FROM {$db_prefix}log_mark_read
          WHERE ID_MEMBER IN (" . implode(', ', $members) . ")", __FILE__, __LINE__);
      }
    }

    if (empty($to_fix) || in_array('missing_pms', $to_fix))
    {
      $result = db_query("
        SELECT pmr.ID_PM
        FROM {$db_prefix}pm_recipients AS pmr
          LEFT JOIN {$db_prefix}personal_messages AS pm ON (pm.ID_PM = pmr.ID_PM)
        WHERE pm.ID_PM IS NULL
        GROUP BY pmr.ID_PM", __FILE__, __LINE__);
      $pms = array();
      while ($row = mysqli_fetch_assoc($result))
        $pms[] = $row['ID_PM'];
      mysqli_free_result($result);

      if (!empty($pms))
      {
        db_query("
          DELETE FROM {$db_prefix}pm_recipients
          WHERE ID_PM IN (" . implode(', ', $pms) . ")", __FILE__, __LINE__);
      }
    }

    if (empty($to_fix) || in_array('missing_recipients', $to_fix))
    {
      $result = db_query("
        SELECT pmr.ID_MEMBER
        FROM {$db_prefix}pm_recipients AS pmr
          LEFT JOIN {$db_prefix}members AS mem ON (mem.ID_MEMBER = pmr.ID_MEMBER)
        WHERE pmr.ID_MEMBER != 0
          AND mem.ID_MEMBER IS NULL
        GROUP BY pmr.ID_MEMBER", __FILE__, __LINE__);
      $members = array();
      while ($row = mysqli_fetch_assoc($result))
        $members[] = $row['ID_MEMBER'];
      mysqli_free_result($result);

      if (!empty($members))
      {
        db_query("
          DELETE FROM {$db_prefix}pm_recipients
          WHERE ID_MEMBER IN (" . implode(', ', $members) . ")", __FILE__, __LINE__);
      }
    }

    if (empty($to_fix) || in_array('missing_senders', $to_fix))
    {
      $result = db_query("
        SELECT pm.ID_PM
        FROM {$db_prefix}personal_messages AS pm
          LEFT JOIN {$db_prefix}members AS mem ON (mem.ID_MEMBER = pm.ID_MEMBER_FROM)
        WHERE pm.ID_MEMBER_FROM != 0
          AND mem.ID_MEMBER IS NULL", __FILE__, __LINE__);
      if (mysqli_num_rows($result) > 0)
      {
        $guestMessages = array();
        while ($row = mysqli_fetch_assoc($result))
          $guestMessages[] = $row['ID_PM'];

        db_query("
          UPDATE {$db_prefix}personal_messages
          SET ID_MEMBER_FROM = 0
          WHERE ID_PM IN (" . implode(',', $guestMessages) . ')
          LIMIT ' . count($guestMessages), __FILE__, __LINE__);
      }
      mysqli_free_result($result);
    }

    if (empty($to_fix) || in_array('missing_notify_members', $to_fix))
    {
      $result = db_query("
        SELECT ln.ID_MEMBER
        FROM {$db_prefix}log_notify AS ln
          LEFT JOIN {$db_prefix}members AS mem ON (mem.ID_MEMBER = ln.ID_MEMBER)
        WHERE mem.ID_MEMBER IS NULL
        GROUP BY ln.ID_MEMBER", __FILE__, __LINE__);
      $members = array();
      while ($row = mysqli_fetch_assoc($result))
        $members[] = $row['ID_MEMBER'];
      mysqli_free_result($result);

      if (!empty($members))
      {
        db_query("
          DELETE FROM {$db_prefix}log_notify
          WHERE ID_MEMBER IN (" . implode(', ', $members) . ")", __FILE__, __LINE__);
      }
    }

    if (empty($to_fix) || in_array('missing_cached_subject', $to_fix))
    {
      $request = db_query("
        SELECT t.ID_TOPIC, m.subject
        FROM ({$db_prefix}topics AS t, {$db_prefix}messages AS m)
          LEFT JOIN {$db_prefix}log_search_subjects AS lss ON (lss.ID_TOPIC = t.ID_TOPIC)
        WHERE m.ID_MSG = t.ID_FIRST_MSG
          AND lss.ID_TOPIC IS NULL", __FILE__, __LINE__);
      $insertRows = array();
      while ($row = mysqli_fetch_assoc($request))
      {
        foreach (text2words($row['subject']) as $word)
          $insertRows[] = "'$word', $row[ID_TOPIC]";
        if (count($insertRows) > 500)
        {
          db_query("
            INSERT IGNORE INTO {$db_prefix}log_search_subjects
              (word, ID_TOPIC)
            VALUES (" . implode('),
              (', $insertRows) . ")", __FILE__, __LINE__);
          $insertRows = array();
        }

      }
      mysqli_free_result($request);

      if (!empty($insertRows))
        db_query("
          INSERT IGNORE INTO {$db_prefix}log_search_subjects
            (word, ID_TOPIC)
          VALUES (" . implode('),
            (', $insertRows) . ")", __FILE__, __LINE__);
    }

    if (empty($to_fix) || in_array('missing_topic_for_cache', $to_fix))
    {
      $request = db_query("
        SELECT lss.ID_TOPIC
        FROM {$db_prefix}log_search_subjects AS lss
          LEFT JOIN {$db_prefix}topics AS t ON (t.ID_TOPIC = lss.ID_TOPIC)
        WHERE t.ID_TOPIC IS NULL
        GROUP BY lss.ID_TOPIC", __FILE__, __LINE__);
      $deleteTopics = array();
      while ($row = mysqli_fetch_assoc($request))
        $deleteTopics[] = $row['ID_TOPIC'];
      mysqli_free_result($request);

      if (!empty($deleteTopics))
        db_query("
          DELETE FROM {$db_prefix}log_search_subjects
          WHERE ID_TOPIC IN (" . implode(', ', $deleteTopics) . ')', __FILE__, __LINE__);
    }

    updateStats('message');
    updateStats('topic');

    $context['raw_data'] = '
      <table width="100%" border="0" cellspacing="0" cellpadding="4" class="tborder">
        <tr class="titlebg">
          <td>' . $txt['smf86'] . '</td>
        </tr><tr>
          <td class="windowbg">
            ' . $txt['smf92'] . '<br />
            <br />
            <a href="' . $scripturl . '?action=maintain">' . $txt['maintain_return'] . '</a>
          </td>
        </tr>
      </table>';

    $_SESSION['repairboards_to_fix'] = null;
    $_SESSION['repairboards_to_fix2'] = null;
  }
}

function pauseRepairProcess($to_fix, $max_substep = 0)
{
  global $context, $txt, $time_start;

  // More time, I need more time!
  @set_time_limit(600);
  if (function_exists('apache_reset_timeout'))
    apache_reset_timeout();

  // Errr, wait.  How much time has this taken already?
  if (time() - array_sum(explode(' ', $time_start)) < 3)
    return;

  $context['continue_get_data'] = '?action=repairboards' . (isset($_GET['fixErrors']) ? ';fixErrors' : '') . ';step=' . $_GET['step'] . ';substep=' . $_GET['substep'];
  $context['page_title'] = $txt['not_done_title'];
  $context['continue_post_data'] = '';
  $context['continue_countdown'] = '2';
  $context['sub_template'] = 'not_done';

  // Change these two if more steps are added!
  if (empty($max_substep))
    $context['continue_percent'] = round(($_GET['step'] * 100) / 25);
  else
    $context['continue_percent'] = round(($_GET['step'] * 100 + ($_GET['substep'] * 100) / $max_substep) / 25);

  // Never more than 100%!
  $context['continue_percent'] = min($context['continue_percent'], 100);

  $_SESSION['repairboards_to_fix'] = $to_fix;
  $_SESSION['repairboards_to_fix2'] = $context['repair_errors'];

  obExit();
}

function findForumErrors()
{
  global $db_prefix, $context, $txt;

  // This may take some time...
  @set_time_limit(600);

  $to_fix = !empty($_SESSION['repairboards_to_fix']) ? $_SESSION['repairboards_to_fix'] : array();
  $context['repair_errors'] = isset($_SESSION['repairboards_to_fix2']) ? $_SESSION['repairboards_to_fix2'] : array();

  $_GET['step'] = empty($_GET['step']) ? 0 : (int) $_GET['step'];
  $_GET['substep'] = empty($_GET['substep']) ? 0 : (int) $_GET['substep'];

  if ($_GET['step'] <= 0)
  {
    // Make a last-ditch-effort check to get rid of topics with zeros..
    $result = db_query("
      SELECT COUNT(*)
      FROM {$db_prefix}topics
      WHERE ID_TOPIC = 0", __FILE__, __LINE__);
    list ($zeroTopics) = mysqli_fetch_row($result);
    mysqli_free_result($result);

    // This is only going to be 1 or 0, but...
    $result = db_query("
      SELECT COUNT(*)
      FROM {$db_prefix}messages
      WHERE ID_MSG = 0", __FILE__, __LINE__);
    list ($zeroMessages) = mysqli_fetch_row($result);
    mysqli_free_result($result);

    if (!empty($zeroTopics) || !empty($zeroMessages))
    {
      $context['repair_errors'][] = $txt['repair_zero_ids'];
      $to_fix[] = 'zero_ids';
    }

    $_GET['step'] = 1;
    $_GET['substep'] = 0;
    pauseRepairProcess($to_fix);
  }

  if ($_GET['step'] <= 1)
  {
    // Find messages that don't have existing topics.
    $result = db_query("
      SELECT m.ID_TOPIC, m.ID_MSG
      FROM {$db_prefix}messages AS m
        LEFT JOIN {$db_prefix}topics AS t ON (t.ID_TOPIC = m.ID_TOPIC)
      WHERE t.ID_TOPIC IS NULL
      ORDER BY m.ID_TOPIC, m.ID_MSG", __FILE__, __LINE__);
    while ($row = mysqli_fetch_assoc($result))
      $context['repair_errors'][] = sprintf($txt['repair_missing_topics'], $row['ID_MSG'], $row['ID_TOPIC']);
    if (mysqli_num_rows($result) != 0)
      $to_fix[] = 'missing_topics';
    mysqli_free_result($result);

    $_GET['step'] = 2;
    $_GET['substep'] = 0;
    pauseRepairProcess($to_fix);
  }

  if ($_GET['step'] <= 2)
  {
    // Find messages that don't have existing topics.
    $result = db_query("
      SELECT m.ID_TOPIC, m.ID_MSG
      FROM {$db_prefix}messages AS m
        LEFT JOIN {$db_prefix}topics AS t ON (t.ID_TOPIC = m.ID_TOPIC)
      WHERE t.ID_TOPIC IS NULL
      ORDER BY m.ID_TOPIC, m.ID_MSG", __FILE__, __LINE__);
    while ($row = mysqli_fetch_assoc($result))
      $context['repair_errors'][] = sprintf($txt['repair_missing_topics'], $row['ID_MSG'], $row['ID_TOPIC']);
    if (mysqli_num_rows($result) != 0)
      $to_fix[] = 'missing_topics';
    mysqli_free_result($result);

    $_GET['step'] = 3;
    $_GET['substep'] = 0;
    pauseRepairProcess($to_fix);
  }

  if ($_GET['step'] <= 3)
  {
    $result = db_query("
      SELECT MAX(ID_TOPIC)
      FROM {$db_prefix}topics", __FILE__, __LINE__);
    list ($topics) = mysqli_fetch_row($result);
    mysqli_free_result($result);

    // Find topics with no messages.
    for (; $_GET['substep'] < $topics; $_GET['substep'] += 1000)
    {
      pauseRepairProcess($to_fix, $topics);

      $result = db_query("
        SELECT t.ID_TOPIC, COUNT(m.ID_MSG) AS numMsg
        FROM {$db_prefix}topics AS t
          LEFT JOIN {$db_prefix}messages AS m ON (m.ID_TOPIC = t.ID_TOPIC)
        WHERE t.ID_TOPIC BETWEEN $_GET[substep] AND $_GET[substep] + 999
        GROUP BY t.ID_TOPIC
        HAVING numMsg = 0", __FILE__, __LINE__);
      while ($row = mysqli_fetch_assoc($result))
        $context['repair_errors'][] = sprintf($txt['repair_missing_messages'], $row['ID_TOPIC']);
      if (mysqli_num_rows($result) != 0)
        $to_fix[] = 'missing_messages';
      mysqli_free_result($result);
    }

    $_GET['step'] = 4;
    $_GET['substep'] = 0;
    pauseRepairProcess($to_fix);
  }

  if ($_GET['step'] <= 4)
  {
    $result = db_query("
      SELECT MAX(ID_TOPIC)
      FROM {$db_prefix}topics", __FILE__, __LINE__);
    list ($topics) = mysqli_fetch_row($result);
    mysqli_free_result($result);

    // Find topics with incorrect ID_FIRST_MSG/ID_LAST_MSG/numReplies.
    for (; $_GET['substep'] < $topics; $_GET['substep'] += 1000)
    {
      pauseRepairProcess($to_fix, $topics);

      $result = db_query("
        SELECT
          t.ID_TOPIC, t.ID_FIRST_MSG, t.ID_LAST_MSG, t.numReplies,
          MIN(m.ID_MSG) AS myID_FIRST_MSG, MAX(m.ID_MSG) AS myID_LAST_MSG,
          COUNT(m.ID_MSG) - 1 AS myNumReplies
        FROM {$db_prefix}topics AS t
          LEFT JOIN {$db_prefix}messages AS m ON (m.ID_TOPIC = t.ID_TOPIC)
        WHERE t.ID_TOPIC BETWEEN $_GET[substep] AND $_GET[substep] + 999
        GROUP BY t.ID_TOPIC
        HAVING ID_FIRST_MSG != myID_FIRST_MSG OR ID_LAST_MSG != myID_LAST_MSG OR numReplies != myNumReplies
        ORDER BY t.ID_TOPIC", __FILE__, __LINE__);
      while ($row = mysqli_fetch_assoc($result))
      {
        if ($row['ID_FIRST_MSG'] != $row['myID_FIRST_MSG'])
          $context['repair_errors'][] = sprintf($txt['repair_stats_topics_1'], $row['ID_TOPIC'], $row['ID_FIRST_MSG']);
        if ($row['ID_LAST_MSG'] != $row['myID_LAST_MSG'])
          $context['repair_errors'][] = sprintf($txt['repair_stats_topics_2'], $row['ID_TOPIC'], $row['ID_LAST_MSG']);
        if ($row['numReplies'] != $row['myNumReplies'])
          $context['repair_errors'][] = sprintf($txt['repair_stats_topics_3'], $row['ID_TOPIC'], $row['numReplies']);
      }
      if (mysqli_num_rows($result) != 0)
        $to_fix[] = 'stats_topics';
      mysqli_free_result($result);
    }

    $_GET['step'] = 5;
    $_GET['substep'] = 0;
    pauseRepairProcess($to_fix);
  }

  if ($_GET['step'] <= 5)
  {
    $result = db_query("
      SELECT MAX(ID_TOPIC)
      FROM {$db_prefix}topics", __FILE__, __LINE__);
    list ($topics) = mysqli_fetch_row($result);
    mysqli_free_result($result);

    // Find topics with nonexistent boards.
    for (; $_GET['substep'] < $topics; $_GET['substep'] += 1000)
    {
      pauseRepairProcess($to_fix, $topics);

      $result = db_query("
        SELECT t.ID_TOPIC, t.ID_BOARD
        FROM {$db_prefix}topics AS t
          LEFT JOIN {$db_prefix}boards AS b ON (b.ID_BOARD = t.ID_BOARD)
        WHERE b.ID_BOARD IS NULL
          AND t.ID_TOPIC BETWEEN $_GET[substep] AND $_GET[substep] + 999
        ORDER BY t.ID_BOARD, t.ID_TOPIC", __FILE__, __LINE__);
      while ($row = mysqli_fetch_assoc($result))
        $context['repair_errors'][] = sprintf($txt['repair_missing_boards'], $row['ID_TOPIC'], $row['ID_BOARD']);
      if (mysqli_num_rows($result) != 0)
        $to_fix[] = 'missing_boards';
      mysqli_free_result($result);
    }

    $_GET['step'] = 6;
    $_GET['substep'] = 0;
    pauseRepairProcess($to_fix);
  }

  if ($_GET['step'] <= 6)
  {
    // Find boards with nonexistent categories.
    $result = db_query("
      SELECT b.ID_BOARD, b.ID_CAT
      FROM {$db_prefix}boards AS b
        LEFT JOIN {$db_prefix}categories AS c ON (c.ID_CAT = b.ID_CAT)
      WHERE c.ID_CAT IS NULL
      ORDER BY b.ID_CAT, b.ID_BOARD", __FILE__, __LINE__);
    while ($row = mysqli_fetch_assoc($result))
      $context['repair_errors'][] = sprintf($txt['repair_missing_categories'], $row['ID_BOARD'], $row['ID_CAT']);
    if (mysqli_num_rows($result) != 0)
      $to_fix[] = 'missing_categories';
    mysqli_free_result($result);

    $_GET['step'] = 7;
    $_GET['substep'] = 0;
    pauseRepairProcess($to_fix);
  }

  if ($_GET['step'] <= 7)
  {
    $result = db_query("
      SELECT MAX(ID_MSG)
      FROM {$db_prefix}messages", __FILE__, __LINE__);
    list ($messages) = mysqli_fetch_row($result);
    mysqli_free_result($result);

    // Find messages with nonexistent members.
    for (; $_GET['substep'] < $messages; $_GET['substep'] += 2000)
    {
      pauseRepairProcess($to_fix, $messages);

      $result = db_query("
        SELECT m.ID_MSG, m.ID_MEMBER
        FROM {$db_prefix}messages AS m
          LEFT JOIN {$db_prefix}members AS mem ON (mem.ID_MEMBER = m.ID_MEMBER)
        WHERE mem.ID_MEMBER IS NULL
          AND m.ID_MEMBER != 0
          AND m.ID_MSG BETWEEN $_GET[substep] AND $_GET[substep] + 1999
        ORDER BY m.ID_MSG", __FILE__, __LINE__);
      while ($row = mysqli_fetch_assoc($result))
        $context['repair_errors'][] = sprintf($txt['repair_missing_posters'], $row['ID_MSG'], $row['ID_MEMBER']);
      if (mysqli_num_rows($result) != 0)
        $to_fix[] = 'missing_posters';
      mysqli_free_result($result);
    }

    $_GET['step'] = 8;
    $_GET['substep'] = 0;
    pauseRepairProcess($to_fix);
  }

  if ($_GET['step'] <= 8)
  {
    // Find boards with nonexistent parents.
    $result = db_query("
      SELECT b.ID_BOARD, b.ID_PARENT
      FROM {$db_prefix}boards AS b
        LEFT JOIN {$db_prefix}boards AS p ON (p.ID_BOARD = b.ID_PARENT)
      WHERE b.ID_PARENT != 0
        AND (p.ID_BOARD IS NULL OR p.ID_BOARD = b.ID_BOARD)
      ORDER BY b.ID_PARENT, b.ID_BOARD", __FILE__, __LINE__);
    while ($row = mysqli_fetch_assoc($result))
      $context['repair_errors'][] = sprintf($txt['repair_missing_parents'], $row['ID_BOARD'], $row['ID_PARENT']);
    if (mysqli_num_rows($result) != 0)
      $to_fix[] = 'missing_parents';
    mysqli_free_result($result);

    $_GET['step'] = 9;
    $_GET['substep'] = 0;
    pauseRepairProcess($to_fix);
  }

  if ($_GET['step'] <= 9)
  {
    $result = db_query("
      SELECT MAX(ID_POLL)
      FROM {$db_prefix}topics", __FILE__, __LINE__);
    list ($polls) = mysqli_fetch_row($result);
    mysqli_free_result($result);

    for (; $_GET['substep'] < $polls; $_GET['substep'] += 500)
    {
      pauseRepairProcess($to_fix, $polls);

      $result = db_query("
        SELECT t.ID_POLL, t.ID_TOPIC
        FROM {$db_prefix}topics AS t
          LEFT JOIN {$db_prefix}polls AS p ON (p.ID_POLL = t.ID_POLL)
        WHERE t.ID_POLL != 0
          AND t.ID_POLL BETWEEN $_GET[substep] AND $_GET[substep] + 499
          AND p.ID_POLL IS NULL
        GROUP BY t.ID_POLL", __FILE__, __LINE__);
      while ($row = mysqli_fetch_assoc($result))
        $context['repair_errors'][] = sprintf($txt['repair_missing_polls'], $row['ID_TOPIC'], $row['ID_POLL']);
      if (mysqli_num_rows($result) != 0)
        $to_fix[] = 'missing_polls';
      mysqli_free_result($result);
    }

    $_GET['step'] = 10;
    $_GET['substep'] = 0;
    pauseRepairProcess($to_fix);
  }

  if ($_GET['step'] <= 10)
  {
    $_GET['step'] = 11;
    $_GET['substep'] = 0;
    pauseRepairProcess($to_fix);
  }

  if ($_GET['step'] <= 11)
  {
    $result = db_query("
      SELECT MAX(ID_MEMBER)
      FROM {$db_prefix}members", __FILE__, __LINE__);
    list ($members) = mysqli_fetch_row($result);
    mysqli_free_result($result);

    for (; $_GET['substep'] < $members; $_GET['substep'] += 250)
    {
      pauseRepairProcess($to_fix, $members);

      $result = db_query("
        SELECT lt.ID_TOPIC
        FROM {$db_prefix}log_topics AS lt
          LEFT JOIN {$db_prefix}topics AS t ON (t.ID_TOPIC = lt.ID_TOPIC)
        WHERE t.ID_TOPIC IS NULL
          AND lt.ID_MEMBER BETWEEN $_GET[substep] AND $_GET[substep] + 249", __FILE__, __LINE__);
      while ($row = mysqli_fetch_assoc($result))
        $context['repair_errors'][] = sprintf($txt['repair_missing_log_topics'], $row['ID_TOPIC']);
      if (mysqli_num_rows($result) != 0 && !in_array('missing_log_topics', $to_fix))
        $to_fix[] = 'missing_log_topics';
      mysqli_free_result($result);
    }

    $_GET['step'] = 12;
    $_GET['substep'] = 0;
    pauseRepairProcess($to_fix);
  }

  if ($_GET['step'] <= 12)
  {
    $result = db_query("
      SELECT MAX(ID_MEMBER)
      FROM {$db_prefix}log_topics", __FILE__, __LINE__);
    list ($members) = mysqli_fetch_row($result);
    mysqli_free_result($result);

    for (; $_GET['substep'] < $members; $_GET['substep'] += 150)
    {
      pauseRepairProcess($to_fix, $members);

      $result = db_query("
        SELECT lt.ID_MEMBER
        FROM {$db_prefix}log_topics AS lt
          LEFT JOIN {$db_prefix}members AS mem ON (mem.ID_MEMBER = lt.ID_MEMBER)
        WHERE mem.ID_MEMBER IS NULL
          AND lt.ID_MEMBER BETWEEN $_GET[substep] AND $_GET[substep] + 149
        GROUP BY lt.ID_MEMBER", __FILE__, __LINE__);
      while ($row = mysqli_fetch_assoc($result))
        $context['repair_errors'][] = sprintf($txt['repair_missing_log_topics_members'], $row['ID_MEMBER']);
      if (mysqli_num_rows($result) != 0 && !in_array('missing_log_topics_members', $to_fix))
        $to_fix[] = 'missing_log_topics_members';
      mysqli_free_result($result);
    }

    $_GET['step'] = 13;
    $_GET['substep'] = 0;
    pauseRepairProcess($to_fix);
  }

  if ($_GET['step'] <= 13)
  {
    $result = db_query("
      SELECT MAX(ID_MEMBER)
      FROM {$db_prefix}log_boards", __FILE__, __LINE__);
    list ($members) = mysqli_fetch_row($result);
    mysqli_free_result($result);

    for (; $_GET['substep'] < $members; $_GET['substep'] += 500)
    {
      pauseRepairProcess($to_fix, $members);

      $result = db_query("
        SELECT lb.ID_BOARD
        FROM {$db_prefix}log_boards AS lb
          LEFT JOIN {$db_prefix}boards AS b ON (b.ID_BOARD = lb.ID_BOARD)
        WHERE b.ID_BOARD IS NULL
          AND lb.ID_MEMBER BETWEEN $_GET[substep] AND $_GET[substep] + 499
        GROUP BY lb.ID_BOARD", __FILE__, __LINE__);
      while ($row = mysqli_fetch_assoc($result))
        $context['repair_errors'][] = sprintf($txt['repair_missing_log_boards'], $row['ID_BOARD']);
      if (mysqli_num_rows($result) != 0)
        $to_fix[] = 'missing_log_boards';
      mysqli_free_result($result);
    }

    $_GET['step'] = 14;
    $_GET['substep'] = 0;
    pauseRepairProcess($to_fix);
  }

  if ($_GET['step'] <= 14)
  {
    $result = db_query("
      SELECT MAX(ID_MEMBER)
      FROM {$db_prefix}log_boards", __FILE__, __LINE__);
    list ($members) = mysqli_fetch_row($result);
    mysqli_free_result($result);

    for (; $_GET['substep'] < $members; $_GET['substep'] += 500)
    {
      pauseRepairProcess($to_fix, $members);

      $result = db_query("
        SELECT lb.ID_MEMBER
        FROM {$db_prefix}log_boards AS lb
          LEFT JOIN {$db_prefix}members AS mem ON (mem.ID_MEMBER = lb.ID_MEMBER)
        WHERE mem.ID_MEMBER IS NULL
          AND lb.ID_MEMBER BETWEEN $_GET[substep] AND $_GET[substep] + 499
        GROUP BY lb.ID_MEMBER", __FILE__, __LINE__);
      while ($row = mysqli_fetch_assoc($result))
        $context['repair_errors'][] = sprintf($txt['repair_missing_log_boards_members'], $row['ID_MEMBER']);
      if (mysqli_num_rows($result) != 0)
        $to_fix[] = 'missing_log_boards_members';
      mysqli_free_result($result);
    }

    $_GET['step'] = 15;
    $_GET['substep'] = 0;
    pauseRepairProcess($to_fix);
  }

  if ($_GET['step'] <= 15)
  {
    $result = db_query("
      SELECT MAX(ID_MEMBER)
      FROM {$db_prefix}log_mark_read", __FILE__, __LINE__);
    list ($members) = mysqli_fetch_row($result);
    mysqli_free_result($result);

    for (; $_GET['substep'] < $members; $_GET['substep'] += 500)
    {
      pauseRepairProcess($to_fix, $members);

      $result = db_query("
        SELECT lmr.ID_BOARD
        FROM {$db_prefix}log_mark_read AS lmr
          LEFT JOIN {$db_prefix}boards AS b ON (b.ID_BOARD = lmr.ID_BOARD)
        WHERE b.ID_BOARD IS NULL
          AND lmr.ID_MEMBER BETWEEN $_GET[substep] AND $_GET[substep] + 499
        GROUP BY lmr.ID_BOARD", __FILE__, __LINE__);
      while ($row = mysqli_fetch_assoc($result))
        $context['repair_errors'][] = sprintf($txt['repair_missing_log_mark_read'], $row['ID_BOARD']);
      if (mysqli_num_rows($result) != 0)
        $to_fix[] = 'missing_log_mark_read';
      mysqli_free_result($result);
    }

    $_GET['step'] = 16;
    $_GET['substep'] = 0;
    pauseRepairProcess($to_fix);
  }

  if ($_GET['step'] <= 16)
  {
    $result = db_query("
      SELECT MAX(ID_MEMBER)
      FROM {$db_prefix}log_mark_read", __FILE__, __LINE__);
    list ($members) = mysqli_fetch_row($result);
    mysqli_free_result($result);

    for (; $_GET['substep'] < $members; $_GET['substep'] += 500)
    {
      pauseRepairProcess($to_fix, $members);

      $result = db_query("
        SELECT lmr.ID_MEMBER
        FROM {$db_prefix}log_mark_read AS lmr
          LEFT JOIN {$db_prefix}members AS mem ON (mem.ID_MEMBER = lmr.ID_MEMBER)
        WHERE mem.ID_MEMBER IS NULL
          AND lmr.ID_MEMBER BETWEEN $_GET[substep] AND $_GET[substep] + 499
        GROUP BY lmr.ID_MEMBER", __FILE__, __LINE__);
      while ($row = mysqli_fetch_assoc($result))
        $context['repair_errors'][] = sprintf($txt['repair_missing_log_mark_read_members'], $row['ID_MEMBER']);
      if (mysqli_num_rows($result) != 0)
        $to_fix[] = 'missing_log_mark_read_members';
      mysqli_free_result($result);
    }

    $_GET['step'] = 17;
    $_GET['substep'] = 0;
    pauseRepairProcess($to_fix);
  }

  if ($_GET['step'] <= 17)
  {
    $result = db_query("
      SELECT MAX(ID_PM)
      FROM {$db_prefix}pm_recipients", __FILE__, __LINE__);
    list ($pms) = mysqli_fetch_row($result);
    mysqli_free_result($result);

    for (; $_GET['substep'] < $pms; $_GET['substep'] += 500)
    {
      pauseRepairProcess($to_fix, $pms);

      $result = db_query("
        SELECT pmr.ID_PM
        FROM {$db_prefix}pm_recipients AS pmr
          LEFT JOIN {$db_prefix}personal_messages AS pm ON (pm.ID_PM = pmr.ID_PM)
        WHERE pm.ID_PM IS NULL
          AND pmr.ID_PM BETWEEN $_GET[substep] AND $_GET[substep] + 499
        GROUP BY pmr.ID_PM", __FILE__, __LINE__);
      while ($row = mysqli_fetch_assoc($result))
        $context['repair_errors'][] = sprintf($txt['repair_missing_pms'], $row['ID_PM']);
      if (mysqli_num_rows($result) != 0)
        $to_fix[] = 'missing_pms';
      mysqli_free_result($result);
    }

    $_GET['step'] = 18;
    $_GET['substep'] = 0;
    pauseRepairProcess($to_fix);
  }

  if ($_GET['step'] <= 18)
  {
    $result = db_query("
      SELECT MAX(ID_MEMBER)
      FROM {$db_prefix}pm_recipients", __FILE__, __LINE__);
    list ($members) = mysqli_fetch_row($result);
    mysqli_free_result($result);

    for (; $_GET['substep'] < $members; $_GET['substep'] += 500)
    {
      pauseRepairProcess($to_fix, $members);

      $result = db_query("
        SELECT pmr.ID_MEMBER
        FROM {$db_prefix}pm_recipients AS pmr
          LEFT JOIN {$db_prefix}members AS mem ON (mem.ID_MEMBER = pmr.ID_MEMBER)
        WHERE pmr.ID_MEMBER != 0
          AND pmr.ID_MEMBER BETWEEN $_GET[substep] AND $_GET[substep] + 499
          AND mem.ID_MEMBER IS NULL
        GROUP BY pmr.ID_MEMBER", __FILE__, __LINE__);
      while ($row = mysqli_fetch_assoc($result))
        $context['repair_errors'][] = sprintf($txt['repair_missing_recipients'], $row['ID_MEMBER']);
      if (mysqli_num_rows($result) != 0)
        $to_fix[] = 'missing_recipients';
      mysqli_free_result($result);
    }

    $_GET['step'] = 19;
    $_GET['substep'] = 0;
    pauseRepairProcess($to_fix);
  }

  if ($_GET['step'] <= 19)
  {
    $result = db_query("
      SELECT MAX(ID_PM)
      FROM {$db_prefix}personal_messages", __FILE__, __LINE__);
    list ($pms) = mysqli_fetch_row($result);
    mysqli_free_result($result);

    for (; $_GET['substep'] < $pms; $_GET['substep'] += 500)
    {
      pauseRepairProcess($to_fix, $pms);

      $result = db_query("
        SELECT pm.ID_PM, pm.ID_MEMBER_FROM
        FROM {$db_prefix}personal_messages AS pm
          LEFT JOIN {$db_prefix}members AS mem ON (mem.ID_MEMBER = pm.ID_MEMBER_FROM)
        WHERE pm.ID_MEMBER_FROM != 0
          AND pm.ID_PM BETWEEN $_GET[substep] AND $_GET[substep] + 499
          AND mem.ID_MEMBER IS NULL", __FILE__, __LINE__);
      while ($row = mysqli_fetch_assoc($result))
        $context['repair_errors'][] = sprintf($txt['repair_missing_senders'], $row['ID_PM'], $row['ID_MEMBER_FROM']);
      if (mysqli_num_rows($result) != 0)
        $to_fix[] = 'missing_senders';
      mysqli_free_result($result);
    }

    $_GET['step'] = 20;
    $_GET['substep'] = 0;
    pauseRepairProcess($to_fix);
  }

  if ($_GET['step'] <= 20)
  {
    $result = db_query("
      SELECT MAX(ID_MEMBER)
      FROM {$db_prefix}log_notify", __FILE__, __LINE__);
    list ($members) = mysqli_fetch_row($result);
    mysqli_free_result($result);

    for (; $_GET['substep'] < $members; $_GET['substep'] += 500)
    {
      pauseRepairProcess($to_fix, $members);

      $result = db_query("
        SELECT ln.ID_MEMBER
        FROM {$db_prefix}log_notify AS ln
          LEFT JOIN {$db_prefix}members AS mem ON (mem.ID_MEMBER = ln.ID_MEMBER)
        WHERE ln.ID_MEMBER BETWEEN $_GET[substep] AND $_GET[substep] + 499
          AND mem.ID_MEMBER IS NULL
        GROUP BY ln.ID_MEMBER", __FILE__, __LINE__);
      while ($row = mysqli_fetch_assoc($result))
        $context['repair_errors'][] = sprintf($txt['repair_missing_notify_members'], $row['ID_MEMBER']);
      if (mysqli_num_rows($result) != 0)
        $to_fix[] = 'missing_notify_members';
      mysqli_free_result($result);
    }

    $_GET['step'] = 21;
    $_GET['substep'] = 0;
    pauseRepairProcess($to_fix);
  }

  if ($_GET['step'] <= 21)
  {
    $request = db_query("
      SELECT t.ID_TOPIC, fm.subject
      FROM ({$db_prefix}topics AS t, {$db_prefix}messages AS fm)
        LEFT JOIN {$db_prefix}log_search_subjects AS lss ON (lss.ID_TOPIC = t.ID_TOPIC)
      WHERE fm.ID_MSG = t.ID_FIRST_MSG
        AND lss.ID_TOPIC IS NULL", __FILE__, __LINE__);
    $found_error = false;
    while ($row = mysqli_fetch_assoc($request))
      if (count(text2words($row['subject'])) != 0)
      {
        $context['repair_errors'][] = sprintf($txt['repair_missing_cached_subject'], $row['ID_TOPIC']);
        $found_error = true;
      }
    mysqli_free_result($request);

    if ($found_error)
      $to_fix[] = 'missing_cached_subject';

    $_GET['step'] = 22;
    $_GET['substep'] = 0;
    pauseRepairProcess($to_fix);
  }

  if ($_GET['step'] <= 22)
  {
    $request = db_query("
      SELECT lss.word
      FROM {$db_prefix}log_search_subjects AS lss
        LEFT JOIN {$db_prefix}topics AS t ON (t.ID_TOPIC = lss.ID_TOPIC)
      WHERE t.ID_TOPIC IS NULL", __FILE__, __LINE__);
    while ($row = mysqli_fetch_assoc($request))
      $context['repair_errors'][] = sprintf($txt['repair_missing_topic_for_cache'], htmlspecialchars($row['word']));
    if (mysqli_num_rows($request) != 0)
      $to_fix[] = 'missing_topic_for_cache';
    mysqli_free_result($request);

    $_GET['step'] = 23;
    $_GET['substep'] = 0;
    pauseRepairProcess($to_fix);
  }
  return $to_fix;
}

// Create a salvage area for repair purposes.
function createSalvageArea()
{
  global $db_prefix, $txt, $language, $salvageBoardID, $salvageCatID;
  static $createOnce = false;

  // Have we already created it?
  if ($createOnce)
    return;
  else
    $createOnce = true;

  // Back to the forum's default language.
  loadLanguage('Admin', $language);

  // Check to see if a 'Salvage Category' exists, if not => insert one.
  $result = db_query("
    SELECT ID_CAT
    FROM {$db_prefix}categories
    WHERE name = '" . addslashes($txt['salvaged_category_name']) . "'
    LIMIT 1", __FILE__, __LINE__);
  if (mysqli_num_rows($result) != 0)
    list ($salvageCatID) = mysqli_fetch_row($result);
  mysqli_free_result($result);

  if (empty($salveageCatID))
  {
    db_query("
      INSERT INTO {$db_prefix}categories
        (name, catOrder)
      VALUES (SUBSTRING('" . addslashes($txt['salvaged_category_name']) . "', 1, 255), -1)", __FILE__, __LINE__);
    if (db_affected_rows() <= 0)
    {
      loadLanguage('Admin');
      fatal_lang_error('salvaged_category_error', false);
    }

    $salvageCatID = db_insert_id();
  }

  // Check to see if a 'Salvage Board' exists, if not => insert one.
  $result = db_query("
    SELECT ID_BOARD
    FROM {$db_prefix}boards
    WHERE ID_CAT = $salvageCatID
      AND name = '" . addslashes($txt['salvaged_board_name']) . "'
    LIMIT 1", __FILE__, __LINE__);
  if (mysqli_num_rows($result) != 0)
    list ($salvageBoardID) = mysqli_fetch_row($result);
  mysqli_free_result($result);

  if (empty($salvageBoardID))
  {
    db_query("
      INSERT INTO {$db_prefix}boards
        (name, description, ID_CAT, memberGroups, boardOrder)
      VALUES (SUBSTRING('" . addslashes($txt['salvaged_board_name']) . "', 1, 255), SUBSTRING('" . addslashes($txt['salvaged_board_description']) . "', 1, 255), $salvageCatID, '1', -1)", __FILE__, __LINE__);
    if (db_affected_rows() <= 0)
    {
      loadLanguage('Admin');
      fatal_lang_error('salvaged_board_error', false);
    }

    $salvageBoardID = db_insert_id();
  }

  db_query("
    ALTER TABLE {$db_prefix}boards
    ORDER BY boardOrder", __FILE__, __LINE__);

  // Restore the user's language.
  loadLanguage('Admin');
}

?>