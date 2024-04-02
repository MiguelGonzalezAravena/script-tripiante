<?php
if (!defined('SMF'))
  die('Hacking attempt...');

function checkUserIsInSpecialGroup() {
  global $user_info, $board_info, $context, $ID_MEMBER;

  $context['user_special'] = 0;

  if ($user_info['is_admin'])
    $context['user_special'] = 1;
  else if (!$user_info['is_guest']) {
    if (in_array(2, $user_info['groups'])) {
      $context['user_special'] = 1;
      return;
    }

    // Moderators
    foreach ($board_info['moderators'] as $mod)
      if ($mod['id'] == $ID_MEMBER) {
        $context['user_special'] = 1;
        return;
      }
  }
  else
    $context['user_special'] = 0;

  return $context['user_special'];
}

function checkUserRepliedToTopic($topic) {
  global $user_info, $board_info, $context, $db_prefix, $ID_MEMBER;
  
  if ($user_info['is_admin'])
    $context['user_post_avaible'] = 1;
  else if (!$user_info['is_guest']) {
    if (empty($topic) || in_array(2, $user_info['groups'])) {
      $context['user_post_avaible'] = 1;
      return $context['user_post_avaible'];
    }

    foreach ($board_info['moderators'] as $mod)
      if ($mod['id'] == $ID_MEMBER) {
        $context['user_post_avaible'] = 1;
        return $context['user_post_avaible'];
      }

    $request = db_query("
      SELECT ID_MSG, ID_MEMBER
      FROM {$db_prefix}messages
      WHERE ID_TOPIC = $topic AND ID_MEMBER = $ID_MEMBER
      LIMIT 1", __FILE__, __LINE__);

    if (mysqli_num_rows($request))
      $context['user_post_avaible'] = 1;
    else
      $context['user_post_avaible'] = 0;

    mysqli_free_result($request);
  }
  else
    $context['user_post_avaible'] = 0;

  return $context['user_post_avaible'];
}

function getHiddenMessage($disable_hidden_msg_color = 0) {
  global $context;

  $message = $context['current_message'];

  return $message['body'];
}

?>