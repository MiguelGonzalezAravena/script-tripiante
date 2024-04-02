<?php
if (!defined('SMF'))
  die('Hacking attempt...');

function LockTopic() {
  global $db_prefix, $topic, $ID_MEMBER, $sourcedir;

  // Just quit if there's no topic to lock.
  if (empty($topic))
    fatal_lang_error(472, false);

  checkSession('get');

  // Get Subs-Post.php for sendNotifications.
  require_once($sourcedir . '/Subs-Post.php');

  // Find out who started the topic - in case User Topic Locking is enabled.
  $request = db_query("
    SELECT ID_MEMBER_STARTED, locked
    FROM {$db_prefix}topics
    WHERE ID_TOPIC = $topic
    LIMIT 1", __FILE__, __LINE__);

  list($starter, $locked) = mysqli_fetch_row($request);

  mysqli_free_result($request);

  // Can you lock topics here, mister?
  $user_lock = !allowedTo('lock_any');
  if ($user_lock && $starter == $ID_MEMBER)
    isAllowedTo('lock_own');
  else
    isAllowedTo('lock_any');

  // Locking with high privileges.
  if ($locked == '0' && !$user_lock)
    $locked = '1';
  // Locking with low privileges.
  else if ($locked == '0')
    $locked = '2';
  // Unlocking - make sure you don't unlock what you can't.
  else if ($locked == '2' || ($locked == '1' && !$user_lock))
    $locked = '0';
  // You cannot unlock this!
  else
    fatal_lang_error('smf31');

  // Actually lock the topic in the database with the new value.
  db_query("
    UPDATE {$db_prefix}topics
    SET locked = $locked
    WHERE ID_TOPIC = $topic
    LIMIT 1", __FILE__, __LINE__);

  // If they are allowed a "moderator" permission, log it in the moderator log.
  if (!$user_lock)
    logAction('lock', array('topic' => $topic));

  // Notify people that this topic has been locked?
  sendNotifications($topic, empty($locked) ? 'unlock' : 'lock');

  // Back to the topic!
  redirectexit('topic=' . $topic . '.' . $_REQUEST['start']);
}

// Sticky a topic.  Can't be done by topic starters - that would be annoying!
function Sticky() {
  global $db_prefix, $modSettings, $topic, $sourcedir;

  // Make sure the user can sticky it, and they are stickying *something*.
  isAllowedTo('make_sticky');

  // You shouldn't be able to (un)sticky a topic if the setting is disabled.
  if (empty($modSettings['enableStickyTopics']))
    fatal_lang_error('cannot_make_sticky', false);

  // You can't sticky a board or something!
  if (empty($topic))
    fatal_lang_error(472, false);

  checkSession('get');

  // We need Subs-Post.php for the sendNotifications() function.
  require_once($sourcedir . '/Subs-Post.php');

  // Is this topic already stickied, or no?
  $request = db_query("
    SELECT isSticky
    FROM {$db_prefix}topics
    WHERE ID_TOPIC = $topic
    LIMIT 1", __FILE__, __LINE__);

  list($isSticky) = mysqli_fetch_row($request);

  mysqli_free_result($request);

  // Toggle the sticky value.... pretty simple ;).
  db_query("
    UPDATE {$db_prefix}topics
    SET isSticky = " . (empty($isSticky) ? 1 : 0) . "
    WHERE ID_TOPIC = $topic
    LIMIT 1", __FILE__, __LINE__);

  // Log this sticky action - always a moderator thing.
  logAction('sticky', array('topic' => $topic));

  // Notify people that this topic has been stickied?
  if (empty($isSticky))
    sendNotifications($topic, 'sticky');

  // Take them back to the now stickied topic.
  redirectexit('topic=' . $topic . '.' . $_REQUEST['start']);
}

?>