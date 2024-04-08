<?php
if (!defined('SMF'))
  die('Hacking attempt...');

function RemoveTopic2() {
  global $ID_MEMBER, $db_prefix, $topic, $sourcedir, $boardurl, $modSettings, $context;

  @require_once($sourcedir . '/Subs-Post.php');

  $request = db_query("
    SELECT t.ID_MEMBER_STARTED, ms.subject
    FROM ({$db_prefix}topics AS t, {$db_prefix}messages AS ms)
    WHERE t.ID_TOPIC = {$topic}
    AND ms.ID_MSG = t.ID_FIRST_MSG
    LIMIT 1", __FILE__, __LINE__);

  list($starter, $subject) = mysqli_fetch_row($request);
  mysqli_free_result($request);

  if ($starter == $ID_MEMBER && !allowedTo('remove_any'))
    isAllowedTo('remove_own');
  else
    isAllowedTo('remove_any');

$ID_MODERATOR = $context['user']['id'];
$ID_MEMBER = $starter;
$ID_TOPIC = $topic;
$TYPE = 'Post';
$ACTION = 'remove';
$subject = $subject;
$reason = htmlentities($_POST['causa'], ENT_QUOTES, "UTF-8");
if (!empty($modSettings['modlog_enabled']) && allowedTo('modify_any')) {
db_query("INSERT INTO {$db_prefix}mod_history (ID_MODERATOR, ID_MEMBER, ID_TOPIC, TYPE, ACTION, subject, reason) VALUES ('" . $ID_MODERATOR . "', '" . $ID_MEMBER . "', '" . $ID_TOPIC . "', '" . $TYPE . "', '" . $ACTION . "', '" . $subject . "', '" . $reason . "')", __FILE__, __LINE__);
}
  removeTopics($topic);
  redirectexit($boardurl);
}

// Remove just a single post.
function DeleteMessage()
{
  global $ID_MEMBER, $db_prefix, $topic, $board, $modSettings, $boardurl;

  checkSession('get');

  $_REQUEST['msg'] = (int) $_REQUEST['msg'];

  if (empty($topic) && isset($_REQUEST['topic']))
    $topic = (int) $_REQUEST['topic'];

  $request = db_query("
    SELECT t.ID_MEMBER_STARTED, m.ID_MEMBER, m.subject, m.posterTime
    FROM ({$db_prefix}topics AS t, {$db_prefix}messages AS m)
    WHERE t.ID_TOPIC = $topic
      AND m.ID_TOPIC = $topic
      AND m.ID_MSG = $_REQUEST[msg]
    LIMIT 1", __FILE__, __LINE__);
  list ($starter, $poster, $subject, $post_time) = mysqli_fetch_row($request);
  mysqli_free_result($request);

  if ($poster == $ID_MEMBER)
  {
    if (!allowedTo('delete_own'))
    {
      if ($starter == $ID_MEMBER && !allowedTo('delete_any'))
        isAllowedTo('delete_replies');
      else if (!allowedTo('delete_any'))
        isAllowedTo('delete_own');
    }
    else if (!allowedTo('delete_any') && ($starter != $ID_MEMBER || !allowedTo('delete_replies')) && !empty($modSettings['edit_disable_time']) && $post_time + $modSettings['edit_disable_time'] * 60 < time())
      fatal_lang_error('modify_post_time_passed', false);
  }
  else if ($starter == $ID_MEMBER && !allowedTo('delete_any'))
    isAllowedTo('delete_replies');
  else
    isAllowedTo('delete_any');

  // If the full topic was removed go back to the board.
  $full_topic = removeMessage($_REQUEST['msg']);

  if (allowedTo('delete_any') && (!allowedTo('delete_own') || $poster != $ID_MEMBER))
    logAction('delete', array('topic' => $topic, 'subject' => $subject, 'member' => $starter));

  if (isset($_REQUEST['recent']))
    redirectexit('action=recent');
  else if ($full_topic)
    redirectexit($boardurl);
  else
    redirectexit($boardurl);
}

// So long as you are sure... all old posts will be gone.
function RemoveOldTopics2()
{
  global $db_prefix, $modSettings;

  isAllowedTo('admin_forum');
  checkSession('post', 'maintain');

  // No boards at all?  Forget it then :/.
  if (empty($_POST['boards']))
    redirectexit('action=maintain');

  // This should exist, but we can make sure.
  $_POST['delete_type'] = isset($_POST['delete_type']) ? $_POST['delete_type'] : 'nothing';

  // Custom conditions.
  $condition = '';

  // Just moved notice topics?
  if ($_POST['delete_type'] == 'moved')
    $condition .= '
      AND m.icon = \'moved\'
      AND t.locked = 1';
  // Otherwise, maybe locked topics only?
  else if ($_POST['delete_type'] == 'locked')
    $condition .= '
      AND t.locked = 1';

  // Exclude stickies?
  if (isset($_POST['delete_old_not_sticky']))
    $condition .= '
      AND t.isSticky = 0';

  // All we're gonna do here is grab the ID_TOPICs and send them to removeTopics().
  $request = db_query("
    SELECT t.ID_TOPIC
    FROM ({$db_prefix}topics AS t, {$db_prefix}messages AS m)
    WHERE m.ID_MSG = t.ID_LAST_MSG
      AND m.posterTime < " . (time() - 3600 * 24 * $_POST['maxdays']) . "$condition
      AND t.ID_BOARD IN (" . implode(', ', array_keys($_POST['boards'])) . ')', __FILE__, __LINE__);
  $topics = array();
  while ($row = mysqli_fetch_assoc($request))
    $topics[] = $row['ID_TOPIC'];
  mysqli_free_result($request);

  removeTopics($topics, false, true);

  // Log an action into the moderation log.
  logAction('pruned', array('days' => $_POST['maxdays']));

  redirectexit('action=maintain;done');
}

// Removes the passed ID_TOPICs. (permissions are NOT checked here!)
function removeTopics($topics, $decreasePostCount = true, $ignoreRecycling = false)
{
  global $db_prefix, $sourcedir, $modSettings;

  // Nothing to do?
  if (empty($topics))
    return;
  // Only a single topic.
  else if (is_numeric($topics))
  {
    $condition = '= ' . $topics;
    $topics = array($topics);
  }
  else if (count($topics) == 1)
    $condition = '= ' . $topics[0];
  // More than one topic.
  else
    $condition = 'IN (' . implode(', ', $topics) . ')';

  // Decrease the post counts.
  if ($decreasePostCount)
  {
    $requestMembers = db_query("
      SELECT m.ID_MEMBER, COUNT(*) AS posts
      FROM ({$db_prefix}messages AS m, {$db_prefix}boards AS b)
      WHERE m.ID_TOPIC $condition
        AND b.ID_BOARD = m.ID_BOARD
        AND m.icon != 'recycled'
        AND b.countPosts = 0
      GROUP BY m.ID_MEMBER", __FILE__, __LINE__);
    if (mysqli_num_rows($requestMembers) > 0)
    {
      //while ($rowMembers = mysqli_fetch_assoc($requestMembers))
      //  updateMemberData($rowMembers['ID_MEMBER'], array('posts' => 'posts - ' . $rowMembers['posts']));
        
      //BEGIN SMFShop 2.0 (Build 8) MOD code
      while ($rowMembers = mysqli_fetch_assoc($requestMembers)) {
        updateMemberData($rowMembers['ID_MEMBER'], array('posts' => 'posts - ' . $rowMembers['posts']));
      global $modSettings;
      db_query("UPDATE {$db_prefix}members
            SET money = money - {$modSettings['shopPointsPerPost']}
            WHERE ID_MEMBER = {$rowMembers['ID_MEMBER']}
            LIMIT 1", __FILE__, __LINE__);
      }
      //END SMFShop 2.0 code
    }
    mysqli_free_result($requestMembers);
  }

  // Decrease the topic count for member.
  if ($decreasePostCount)
  {
    $requestMembers = db_query("
      SELECT t.ID_MEMBER_STARTED
      FROM ({$db_prefix}topics AS t, {$db_prefix}boards AS b)
      WHERE t.ID_TOPIC $condition
        AND b.ID_BOARD = t.ID_BOARD
        AND b.countPosts = 0
      ", __FILE__, __LINE__);

      while ($rowMembers = mysqli_fetch_assoc($requestMembers))
        updateMemberData($rowMembers['ID_MEMBER_STARTED'], array('topics' => 'topics - 1'));
    mysqli_free_result($requestMembers);
  }
  
  // Recycle topics that aren't in the recycle board...
  if (!empty($modSettings['recycle_enable']) && $modSettings['recycle_board'] > 0 && !$ignoreRecycling)
  {
    $request = db_query("
      SELECT ID_TOPIC
      FROM {$db_prefix}topics
      WHERE ID_TOPIC $condition
        AND ID_BOARD != $modSettings[recycle_board]
      LIMIT " . count($topics), __FILE__, __LINE__);
    if (mysqli_num_rows($request) > 0)
    {
      // Get topics that will be recycled.
      $recycleTopics = array();
      while ($row = mysqli_fetch_assoc($request))
        $recycleTopics[] = $row['ID_TOPIC'];
      mysqli_free_result($request);

      // Mark recycled topics as recycled.
      db_query("
        UPDATE {$db_prefix}messages
        SET icon = 'recycled'
        WHERE ID_TOPIC IN (" . implode(', ', $recycleTopics) . ")", __FILE__, __LINE__);

      // De-sticky and unlock topics.
      db_query("
        UPDATE {$db_prefix}topics
        SET 
          locked = 0,
          isSticky = 0
        WHERE ID_TOPIC IN (" . implode(', ', $recycleTopics) . ")", __FILE__, __LINE__);

      // Move the topics to the recycle board.
      require_once($sourcedir . '/MoveTopic.php');
      moveTopics($recycleTopics, $modSettings['recycle_board']);

      // Topics that were recycled don't need to be deleted, so subtract them.
      $topics = array_diff($topics, $recycleTopics);

      // Topic list has changed, so does the condition to select topics.
      $condition = 'IN (' . implode(', ', $topics) . ')';
    }
    else
      mysqli_free_result($request);
  }

  // Still topics left to delete?
  if (empty($topics))
    return;

  $adjustBoards = array();

  // Find out how many posts we are deleting.
  $request = db_query("
    SELECT ID_BOARD, COUNT(*) AS numTopics, SUM(numReplies) AS numReplies
    FROM {$db_prefix}topics
    WHERE ID_TOPIC $condition
    GROUP BY ID_BOARD", __FILE__, __LINE__);
  while ($row = mysqli_fetch_assoc($request))
  {
    // The numReplies is only the *replies*.  There're also the first posts in the topics.
    $adjustBoards[] = array(
      'numPosts' => $row['numReplies'] + $row['numTopics'],
      'numTopics' => $row['numTopics'],
      'ID_BOARD' => $row['ID_BOARD']
    );
  }
  mysqli_free_result($request);

  // Decrease the posts/topics...
  foreach ($adjustBoards as $stats)
    db_query("
      UPDATE {$db_prefix}boards
      SET 
        numTopics = if ($stats[numTopics] > numTopics, 0, numTopics - $stats[numTopics]),
        numPosts = IF ($stats[numPosts] > numPosts, 0, numPosts - $stats[numPosts])
      WHERE ID_BOARD = $stats[ID_BOARD]
      LIMIT 1", __FILE__, __LINE__);

  // Delete possible search index entries.
  if (!empty($modSettings['search_custom_index_config']))
  {
    $customIndexSettings = unserialize($modSettings['search_custom_index_config']);

    $words = array();
    $messages = array();
    $request = db_query("SELECT ID_MSG, body FROM {$db_prefix}messages WHERE ID_TOPIC $condition", __FILE__, __LINE__);
    while ($row = mysqli_fetch_assoc($request))
    {
      $words = array_merge($words, text2words($row['body'], $customIndexSettings['bytes_per_word'], true));
      $messages[] = $row['ID_MSG'];
    }
    mysqli_free_result($request);
    $words = array_unique($words);

    if (!empty($words) && !empty($messages))
      db_query("
        DELETE FROM {$db_prefix}log_search_words
        WHERE ID_WORD IN (" . implode(', ', $words) . ")
          AND ID_MSG IN (" . implode(', ', $messages) . ')', __FILE__, __LINE__);
  }

  db_query("
    DELETE FROM {$db_prefix}messages
    WHERE ID_TOPIC $condition", __FILE__, __LINE__);
  db_query("
    DELETE FROM {$db_prefix}bookmarks
    WHERE ID_TOPIC $condition", __FILE__, __LINE__);
  db_query("
    DELETE FROM {$db_prefix}comments
    WHERE ID_TOPIC $condition", __FILE__, __LINE__);
  db_query("
    DELETE FROM {$db_prefix}topics
    WHERE ID_TOPIC $condition
    LIMIT " . count($topics), __FILE__, __LINE__);
  db_query("
    DELETE FROM {$db_prefix}tags_log 
    WHERE ID_TOPIC $condition", __FILE__, __LINE__);
  db_query("
    DELETE FROM {$db_prefix}log_search_subjects
    WHERE ID_TOPIC $condition", __FILE__, __LINE__);

  // Update the totals...
  updateStats('message');
  updateStats('topic');

  require_once($sourcedir . '/Subs-Post.php');
  $updates = array();
  foreach ($adjustBoards as $stats)
    $updates[] = $stats['ID_BOARD'];
  updateLastMessages($updates);
}

?>