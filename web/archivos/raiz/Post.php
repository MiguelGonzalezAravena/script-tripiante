<?php
if (!defined('SMF'))
  die('Hacking attempt...');

function Post() {
  global $txt, $topic, $db_prefix, $modSettings, $board, $ID_MEMBER;
  global $user_info, $sc, $context, $sourcedir;
  global $options, $func, $language;

  loadLanguage('Post');

  $dbresult = db_query("
    SELECT t.tag, l.ID, t.ID_TAG
    FROM {$db_prefix}tags_log as l, {$db_prefix}tags as t
    WHERE t.ID_TAG = l.ID_TAG
    AND l.ID_TOPIC = " . $topic, __FILE__, __LINE__);

  $context['topic_tags'] = array();

  while($row = mysqli_fetch_assoc($dbresult)) {
    $context['topic_tags'][] = array(
      'ID' => $row['ID'],
      'ID_TAG' => $row['ID_TAG'],
      'tag' => $row['tag'],
    );
  }
  mysqli_free_result($dbresult);

  $request = db_query("
    SELECT b.ID_BOARD, b.name, c.name AS catName
    FROM {$db_prefix}boards AS b
    LEFT JOIN {$db_prefix}categories AS c ON (c.ID_CAT = b.ID_CAT)
    ORDER BY b.name ASC ", __FILE__, __LINE__);

  $context['boards'] = array();

  while ($row = mysqli_fetch_assoc($request)) {
    $context['boards'][] = array(
      'id' => $row['ID_BOARD'],
      'name' => $row['name'],
    );
  }

  mysqli_free_result($request);

  $categorias = htmlentities(addslashes($_POST['categorias']));

  require_once($sourcedir . '/Subs-Post.php');

  $context['can_hide_post'] = 0;
  $context['hidden_option'] = 0;
  $context['hidden_value'] = 0;

  if (!empty($topic)) {
    $request = db_query("
      SELECT t.locked, t.isSticky, t.numReplies, t.ID_FIRST_MSG
      FROM {$db_prefix}topics AS t
      WHERE t.ID_TOPIC = $topic
      LIMIT 1", __FILE__, __LINE__);

    list ($locked, $sticky, $ID_MEMBER_POSTER, $ID_FIRST_MSG, $first_subject) = mysqli_fetch_row($request);

    mysqli_free_result($request);

    if (empty($_REQUEST['msg'])) {
      if ($user_info['is_guest'] && !allowedTo('post_reply_any')) {
        is_not_guest();
      }

      if ($ID_MEMBER_POSTER != $ID_MEMBER) {
        isAllowedTo('post_reply_any');
      } else if (!allowedTo('post_reply_any')) {
        isAllowedTo('post_reply_own');
      }
    }

    $context['can_hide_post'] = (allowedTo('hide_post_any') ||  ($ID_MEMBER == $ID_MEMBER_POSTER && allowedTo('hide_post_own'))) && !empty($modSettings['allow_hiddenPost']);
    $context['can_lock'] = allowedTo('lock_any') || ($ID_MEMBER == $ID_MEMBER_POSTER && allowedTo('lock_own'));
    $context['can_sticky'] = allowedTo('make_sticky') && !empty($modSettings['enableStickyTopics']);

    $context['notify'] = !empty($context['notify']);
    $context['sticky'] = isset($_REQUEST['sticky']) ? !empty($_REQUEST['sticky']) : $sticky;
  } else {
    if ((!$context['make_event'] || !empty($board))) {
      isAllowedTo('post_new');
    }

    $locked = 0;
    // !!! These won't work if you're making an event.
    $context['can_hide_post'] = allowedTo(array('hide_post_any', 'hide_post_own')) && !empty($modSettings['allow_hiddenPost']);
    $context['can_lock'] = allowedTo(array('lock_any', 'lock_own'));
    $context['can_sticky'] = allowedTo('make_sticky') && !empty($modSettings['enableStickyTopics']);
    $context['notify'] = !empty($context['notify']);
    $context['sticky'] = !empty($_REQUEST['sticky']);
  }

  // !!! These won't work if you're posting an event!
  $context['max_hidden_value'] = sprintf($txt['hide_value'], $modSettings['max_hiddenValue']);
  $context['can_notify'] = allowedTo('mark_any_notify');
  $context['can_move'] = allowedTo('move_any');
  $context['can_announce'] = allowedTo('announce_topic');
  $context['locked'] = !empty($locked) || !empty($_REQUEST['lock']);

  // Don't allow a post if it's locked and you aren't all powerful.
  if ($locked && !allowedTo('moderate_board')) {
    fatal_lang_error(90, false);
  }

  if (empty($context['post_errors'])) {
    $context['post_errors'] = array();
  }

  // See if any new replies have come along.
  if (empty($_REQUEST['msg']) && !empty($topic)) {
    if (empty($options['no_new_reply_warning']) && isset($_REQUEST['num_replies'])) {
      $newReplies = $context['num_replies'] > $_REQUEST['num_replies'] ? $context['num_replies'] - $_REQUEST['num_replies'] : 0;

      if (!empty($newReplies)) {
        if ($newReplies == 1)
          $txt['error_new_reply'] = isset($_GET['num_replies']) ? $txt['error_new_reply_reading'] : $txt['error_new_reply'];
        else
          $txt['error_new_replies'] = sprintf(isset($_GET['num_replies']) ? $txt['error_new_replies_reading'] : $txt['error_new_replies'], $newReplies);

        // If they've come from the display page then we treat the error differently....
        if (isset($_GET['num_replies']))
          $newRepliesError = $newReplies;
        else
          $context['post_error'][$newReplies == 1 ? 'new_reply' : 'new_replies'] = true;

        $modSettings['topicSummaryPosts'] = $newReplies > $modSettings['topicSummaryPosts'] ? max($modSettings['topicSummaryPosts'], 5) : $modSettings['topicSummaryPosts'];
      }
    }
    // Check whether this is a really old post being bumped...
    if (!empty($modSettings['oldTopicDays']) && $lastPostTime + $modSettings['oldTopicDays'] * 86400 < time() && empty($sticky) && !isset($_REQUEST['subject']))
      $oldTopicError = true;
  }

  // Get a response prefix (like 'Re:') in the default forum language.
  if (!isset($context['response_prefix']) && !($context['response_prefix'] = cache_get_data('response_prefix'))) {
    if ($language === $user_info['language'])
      $context['response_prefix'] = $txt['response_prefix'];
    else {
      loadLanguage('index', $language, false);
      $context['response_prefix'] = $txt['response_prefix'];
      loadLanguage('index');
    }

    cache_put_data('response_prefix', $context['response_prefix'], 600);
  }

  // Previewing, modifying, or posting?
  if (isset($_REQUEST['message']) || !empty($context['post_error'])) {
    checkSession('get');

    $request = db_query("
      SELECT
        m.ID_MEMBER, m.posterName, t.ID_MEMBER_STARTED
      FROM {$db_prefix}messages AS m, {$db_prefix}topics AS t
      WHERE m.ID_MSG = " . (int) $_REQUEST['msg'], __FILE__, __LINE__);

    if (mysqli_num_rows($request) == 0) {
      fatal_lang_error('noresponder', false);
    }

    $row = mysqli_fetch_assoc($request);

    if ($row['ID_MEMBER'] != $ID_MEMBER && (!allowedTo('modify_any'))) {
      fatal_lang_error('noresponder', false);
    }

    // Validate inputs.
    if (empty($context['post_error'])) {
      if ($func['htmltrim']($_REQUEST['subject']) == '')
        $context['post_error']['no_subject'] = true;
      if ($func['htmltrim']($_REQUEST['message']) == '')
        $context['post_error']['no_message'] = true;
      if (!empty($modSettings['max_messageLength']) && $func['strlen']($_REQUEST['message']) > $modSettings['max_messageLength'])
        $context['post_error']['long_message'] = true;

      // Are you... a guest?
      if ($user_info['is_guest'])
      {
        $_REQUEST['guestname'] = !isset($_REQUEST['guestname']) ? '' : trim($_REQUEST['guestname']);
        $_REQUEST['email'] = !isset($_REQUEST['email']) ? '' : trim($_REQUEST['email']);

        // Validate the name and email.
        if (!isset($_REQUEST['guestname']) || trim(strtr($_REQUEST['guestname'], '_', ' ')) == '')
          $context['post_error']['no_name'] = true;
        elseif ($func['strlen']($_REQUEST['guestname']) > 25)
          $context['post_error']['long_name'] = true;
        else
        {
          require_once($sourcedir . '/Subs-Members.php');
          if (isReservedName(htmlspecialchars($_REQUEST['guestname']), 0, true, false))
          {

            $context['post_error']['bad_name'] = true;
          }
        }

        if (empty($modSettings['guest_post_no_email']))
        {
          if (!isset($_REQUEST['email']) || $_REQUEST['email'] == '')
            $context['post_error']['no_email'] = true;
          elseif (preg_match('~^[0-9A-Za-z=_+\-/][0-9A-Za-z=_\'+\-/\.]*@[\w\-]+(\.[\w\-]+)*(\.[\w]{2,6})$~', stripslashes($_REQUEST['email'])) == 0)
            $context['post_error']['bad_email'] = true;
        }
      }

      // This is self explanatory - got any questions?
      if (isset($_REQUEST['question']) && trim($_REQUEST['question']) == '') 
        $context['post_error']['no_question'] = true;

      // This means they didn't click Post and get an error.
      $really_previewing = true;
    } else {
      if (!isset($_REQUEST['subject']))
        $_REQUEST['subject'] = '';
      if (!isset($_REQUEST['message']))
        $_REQUEST['message'] = '';
      $really_previewing = false;
    }

    // TO-DO: No identado bien ?
    $form_subject = strtr($func['htmlspecialchars'](stripslashes($_REQUEST['subject'])), array("\r" => '', "\n" => '', "\t" => ''));
    $form_message = $func['htmlspecialchars'](stripslashes($_REQUEST['message']), ENT_QUOTES);

    if ($func['strlen']($form_subject) > 100) {
      $form_subject = $func['substr']($form_subject, 0, 100);
    }

    if ($func['htmltrim']($form_subject) === '') {
    $context['post_error']['no_subject'] = true;
    }

    if (!empty($context['post_error'])) {
      loadLanguage('Errors');
      $context['error_type'] = 'minor';
      $context['post_error']['messages'] = array();
      foreach ($context['post_error'] as $post_error => $dummy) {
        if ($post_error == 'messages') {
          continue;
        }

        $context['post_error']['messages'][] = $txt['error_' . $post_error];

        if (!in_array($post_error, array('new_reply', 'new_replies', 'old_topic'))) {
          $context['error_type'] = 'serious';
        }
      }
    }

    if ($really_previewing == true || isset($_REQUEST['xml'])) {
      $context['preview_message'] = $form_message;
      preparsecode($form_message, true);
      preparsecode($context['preview_message']);
      $context['preview_message'] = parse_bbc($context['preview_message'], isset($_REQUEST['ns']) ? 0 : 1);

      if ($form_subject != '') {
        $context['preview_subject'] = $form_subject;
        censorText($context['preview_subject']);
        censorText($context['preview_message']);
      } else {
        $context['preview_subject'] = '<i>' . $txt[24] . '</i>';
      }
    }

    if (isset($_REQUEST['xml'])) {
      $context['preview_message'] = strtr($context['preview_message'], array(']]>' => ']]]]><![CDATA[>'));
    }

    $context['use_smileys'] = !isset($_REQUEST['ns']);
    $context['destination'] = 'post2;start=' . $_REQUEST['start'] . (isset($_REQUEST['msg']) ? ';msg=' . $_REQUEST['msg'] . ';sesc=' . $sc : '');
    $context['submit_label'] = isset($_REQUEST['msg']) ? $txt[10] : $txt[105];

    if (isset($_REQUEST['msg'])) {
      if (allowedTo('moderate_forum') && !empty($topic)) {
        $request = db_query("
          SELECT ID_MEMBER, posterName, posterEmail
          FROM {$db_prefix}messages
          WHERE ID_MSG = " . (int) $_REQUEST['msg'] . "
          AND ID_TOPIC = $topic
          LIMIT 1", __FILE__, __LINE__);

        $row = mysqli_fetch_assoc($request);

        mysqli_free_result($request);

        if (empty($row['ID_MEMBER'])) {
          $context['name'] = htmlspecialchars($row['posterName']);
          $context['email'] = htmlspecialchars($row['posterEmail']);
        }
      }
    }

    checkSubmitOnce('free');
  } else if (isset($_REQUEST['topic'])) {
    $request = db_query("
      SELECT
        m.ID_MEMBER, m.modifiedTime, m.smileysEnabled, m.body, m.posterName, m.posterEmail, m.subject, m.icon, m.hiddenOption,
        m.hiddenValue, m.ID_BOARD, t.ID_BOARD, t.ID_MEMBER_STARTED AS ID_MEMBER_POSTER, m.posterTime, t.ID_FIRST_MSG
      FROM ({$db_prefix}messages AS m, {$db_prefix}topics AS t)
      WHERE m.ID_MSG = t.ID_FIRST_MSG
      AND t.ID_BOARD = m.ID_BOARD
      AND t.ID_TOPIC = " . $topic, __FILE__, __LINE__);

    if (mysqli_num_rows($request) == 0) {
      fatal_lang_error('smf232', false);
    }

    $row = mysqli_fetch_assoc($request);

    if ($row['ID_MEMBER'] == $ID_MEMBER && !allowedTo('modify_any')) {
      if (!empty($modSettings['edit_disable_time']) && $row['posterTime'] + ($modSettings['edit_disable_time'] + 5) * 60 < time()) {
        fatal_lang_error('modify_post_time_passed', false);
      } else if ($row['ID_MEMBER_POSTER'] == $ID_MEMBER && !allowedTo('modify_own')) {
        isAllowedTo('modify_replies');
      } else {
        isAllowedTo('modify_own');
      }
    } else if ($row['ID_MEMBER_POSTER'] == $ID_MEMBER && !allowedTo('modify_any')) {
      isAllowedTo('modify_replies');
    } else {
      isAllowedTo('modify_any');
    }

    if (!empty($row['modifiedTime'])) {
      $context['last_modified'] = timeformat($row['modifiedTime']);
    }

    $form_subject = $row['subject'];
    $form_message = un_preparsecode($row['body']);

    censorText($form_message);
    censorText($form_subject);

    $context['hidden_option'] = $row['hiddenOption'];
    $context['hidden_value'] = $row['hiddenValue'];
    $context['use_smileys'] = !empty($row['smileysEnabled']);

    if (allowedTo('moderate_forum') && empty($row['ID_MEMBER'])) {
      $context['name'] = htmlspecialchars($row['posterName']);
      $context['email'] = htmlspecialchars($row['posterEmail']);
    }

    $context['destination'] = 'post2;start=' . $_REQUEST['start'] . ';msg=' . $row['ID_FIRST_MSG'] . ';sesc=' . $sc;
    $context['submit_label'] = $txt[10];
  } else {
    $context['use_smileys'] = true;
    $context['hidden_option'] = 0;
    $context['hidden_value'] = 0;

    $context['destination'] = 'post2;start=' . $_REQUEST['start'];
    $context['submit_label'] = $txt[105];

    if (!empty($topic) && !empty($_REQUEST['quote'])) {
      fatal_lang_error('noresponder', false);
      checkSession('get');

      $request = db_query("
        SELECT m.subject, IFNULL(mem.realName, m.posterName) AS posterName, m.posterTime, m.body
        FROM ({$db_prefix}messages AS m, {$db_prefix}boards AS b)
        LEFT JOIN {$db_prefix}members AS mem ON (mem.ID_MEMBER = m.ID_MEMBER)
        WHERE m.ID_MSG = " . (int) $_REQUEST['quote'] . "
        AND b.ID_BOARD = m.ID_BOARD
        AND $user_info[query_see_board]
        LIMIT 1", __FILE__, __LINE__);

      if (mysqli_num_rows($request) == 0) {
        fatal_lang_error('quoted_post_deleted', false);
      }

      list($form_subject, $mname, $mdate, $form_message) = mysqli_fetch_row($request);

      mysqli_free_result($request);

      if (trim($context['response_prefix']) != '' && $func['strpos']($form_subject, trim($context['response_prefix'])) !== 0) {
        $form_subject = $context['response_prefix'] . $form_subject;
      }

      censorText($form_message);
      censorText($form_subject);

      $form_message = preg_replace("~\[hide\](.+?)\[\/hide\]~i", "&nbsp;", $form_message);
      $form_message = preg_replace(array('~\n?\[hide.*?\].+?\[/hide\]\n?~is', '~^\n~', '~\[/hide\]~'), "&nbsp;", $form_message);
      $form_message = preg_replace('~<br(?: /)?' . '>~i', "\n", $form_message);

      if (!empty($modSettings['removeNestedQuotes'])) {
        $form_message = preg_replace(array('~\n?\[quote.*?\].+?\[/quote\]\n?~is', '~^\n~', '~\[/quote\]~'), '', $form_message);
      }

      $form_message = '[quote author=' . $mname . ' link=topic=' . $topic . '.msg' . (int) $_REQUEST['quote'] . '#msg' . (int) $_REQUEST['quote'] . ' date=' . $mdate . ']' . "\n" . $form_message . "\n" . '[/quote]';
    } else if (!empty($topic) && empty($_REQUEST['quote'])) {
      fatal_lang_error('noresponder', false);

      $form_subject = $first_subject;

      if (trim($context['response_prefix']) != '' && $form_subject != '' && $func['strpos']($form_subject, trim($context['response_prefix'])) !== 0) {
        $form_subject = $context['response_prefix'] . $form_subject;
      }

      censorText($form_subject);

      $form_message = '';
    } else {
      $form_subject = isset($_GET['subject']) ? $_GET['subject'] : '';
      $form_message = '';
    }
  }

  if (isset($newRepliesError)) {
    $context['post_error']['messages'][] = $newRepliesError == 1 ? $txt['error_new_reply'] : $txt['error_new_replies'];
    $context['error_type'] = 'minor';
  }

  if (isset($oldTopicError)) {
    $context['post_error']['messages'][] = $txt['error_old_topic'];
    $context['error_type'] = 'minor';
  }

  if (isset($_REQUEST['msg'])) {
    $context['page_title'] = 'Editar Post';
  } else if (isset($_REQUEST['subject'], $context['preview_subject'])) {
    $context['page_title'] = 'Agregar nuevo post';
  } else if (empty($topic)) {
    $context['page_title'] = 'Agregar nuevo post';
  } else {
    $context['page_title'] = 'Agregar nuevo post';
  }

  $context['subject'] = addcslashes($form_subject, '"');
  $context['message'] = str_replace(array('"', '<', '>', '  '), array('&quot;', '&lt;', '&gt;', ' &nbsp;'), $form_message);

  $context['hidden_options'] = array(
    array('value' => 0, 'name' => $txt['hide_select']),
    array('value' => 1, 'name' => $txt['hide_login']),
  );

  $found = false;
  for ($i = 0, $n = count($context['hidden_options']); $i < $n; $i++) {
    $context['hidden_options'][$i]['selected'] = $context['hidden_option'] == $context['hidden_options'][$i]['value'];

    if ($context['icons'][$i]['selected']) {
      $found = true;
    }
  }

  if (isset($topic)) {
    getTopic();
  }

  $context['back_to_topic'] = isset($_REQUEST['goback']) || (isset($_REQUEST['msg']) && !isset($_REQUEST['subject']));
  $context['show_additional_options'] = !empty($_POST['additional_options']);
  $context['is_new_topic'] = empty($topic);
  $context['is_new_post'] = !isset($_REQUEST['msg']);
  $context['is_first_post'] = $context['is_new_topic'] || (isset($_REQUEST['msg']) && $_REQUEST['msg'] == $ID_FIRST_MSG);

  checkSubmitOnce('register');

  if (WIRELESS) {
    $context['sub_template'] = WIRELESS_PROTOCOL . '_post';
  } else if (!isset($_REQUEST['xml'])) {
    loadTemplate('Post');
  }
}

function Post2()
{
  global $board, $topic, $txt, $db_prefix, $modSettings, $sourcedir, $context, $boardurl;
  global $ID_MEMBER, $user_info, $board_info, $options, $func;

if (isset($_REQUEST['preview'])) {
return Post();
}
  checkSubmitOnce('check');
  $post_errors = array();

if (checkSession('post', '', false) != '') {
$post_errors[] = 'session_timeout';
}

require_once($sourcedir . '/Subs-Post.php');
loadLanguage('Post');

if (!empty($topic) && !isset($_REQUEST['msg'])) {
$request = db_query("
SELECT t.locked, t.isSticky, t.ID_POLL, t.numReplies, m.ID_MEMBER
FROM ({$db_prefix}topics AS t, {$db_prefix}messages AS m)
WHERE t.ID_TOPIC = $topic
AND m.ID_MSG = t.ID_FIRST_MSG
LIMIT 1", __FILE__, __LINE__);
list ($tmplocked, $tmpstickied, $pollID, $numReplies, $ID_MEMBER_POSTER) = mysqli_fetch_row($request);
mysqli_free_result($request);

if ($tmplocked != 0 && !allowedTo('moderate_board')) {
fatal_lang_error(90, false);
}

if ($ID_MEMBER_POSTER != $ID_MEMBER) {
isAllowedTo('post_reply_any');
} elseif (!allowedTo('post_reply_any')) {
isAllowedTo('post_reply_own');
}

if (isset($_POST['lock'])) {
if ((empty($tmplocked) && empty($_POST['lock'])) || (!empty($_POST['lock']) && !empty($tmplocked))) {
unset($_POST['lock']);
} elseif (!allowedTo(array('lock_any', 'lock_own')) || (!allowedTo('lock_any') && $ID_MEMBER != $ID_MEMBER_POSTER)) {
        unset($_POST['lock']);
} elseif (!allowedTo('lock_any')) {
if ($tmplocked == 1) {
unset($_POST['lock']);
} else {
$_POST['lock'] = empty($_POST['lock']) ? 0 : 2;
}
} else {
$_POST['lock'] = empty($_POST['lock']) ? 0 : 1;
}
}

if (isset($_POST['sticky']) && (empty($modSettings['enableStickyTopics']) || $_POST['sticky'] == $tmpstickied || !allowedTo('make_sticky'))) {
unset($_POST['sticky']);
}

if (isset($_POST['hiddenOption']) && !((allowedTo('hide_post_any') || ($ID_MEMBER == $ID_MEMBER_POSTER && allowedTo('hide_post_own'))) && !empty($modSettings['allow_hiddenPost']))) {
unset($_POST['hiddenOption']);
}

$newReplies = isset($_POST['num_replies']) && $numReplies > $_POST['num_replies'] ? $numReplies - $_POST['num_replies'] : 0;
if (empty($options['no_new_reply_warning']) && !empty($newReplies)) { 
$_REQUEST['preview'] = true;
return Post();
}
$posterIsGuest = $user_info['is_guest'];
} elseif (empty($topic)) {
if (isset($_POST['lock']))   {
if (empty($_POST['lock'])) {
unset($_POST['lock']);
} elseif (!allowedTo(array('lock_any', 'lock_own'))) {
unset($_POST['lock']);
} else {
$_POST['lock'] = allowedTo('lock_any') ? 1 : 2;
}
    }

if (isset($_POST['sticky']) && (empty($modSettings['enableStickyTopics']) || empty($_POST['sticky']) || !allowedTo('make_sticky'))) {
unset($_POST['sticky']);
}

$posterIsGuest = $user_info['is_guest'];
} elseif (isset($_REQUEST['msg']) && !empty($topic)) {
$_REQUEST['msg'] = (int) $_REQUEST['msg'];

$request = db_query("
SELECT m.ID_MEMBER, m.posterName, m.posterEmail, m.posterTime, t.ID_FIRST_MSG, t.locked, t.isSticky, t.ID_MEMBER_STARTED AS ID_MEMBER_POSTER
FROM ({$db_prefix}messages AS m, {$db_prefix}topics AS t)
WHERE m.ID_MSG = $_REQUEST[msg]
AND t.ID_TOPIC = $topic
LIMIT 1", __FILE__, __LINE__);
if (mysqli_num_rows($request) == 0) {
fatal_lang_error('smf272', false);
}
$row = mysqli_fetch_assoc($request);
mysqli_free_result($request);

if (!empty($row['locked']) && !allowedTo('moderate_board')) {
fatal_lang_error(90, false);
}

if (isset($_POST['lock'])) {
if ((empty($_POST['lock']) && empty($row['locked'])) || (!empty($_POST['lock']) && !empty($row['locked']))) {
unset($_POST['lock']);
} elseif (!allowedTo(array('lock_any', 'lock_own')) || (!allowedTo('lock_any') && $ID_MEMBER != $row['ID_MEMBER_POSTER'])) {
unset($_POST['lock']);
}
elseif (!allowedTo('lock_any')) {
if ($row['locked'] == 1) {
unset($_POST['lock']);
} else {
$_POST['lock'] = empty($_POST['lock']) ? 0 : 2;
}
} else {
$_POST['lock'] = empty($_POST['lock']) ? 0 : 1;
}
}

if (isset($_POST['sticky']) && (!allowedTo('make_sticky') || $_POST['sticky'] == $row['isSticky'])) {
unset($_POST['sticky']);
}

if ($row['ID_MEMBER'] == $ID_MEMBER && !allowedTo('modify_any')) {
if (!empty($modSettings['edit_disable_time']) && $row['posterTime'] + ($modSettings['edit_disable_time'] + 5) * 60 < time()) {
fatal_lang_error('modify_post_time_passed', false);
} elseif ($row['ID_MEMBER_POSTER'] == $ID_MEMBER && !allowedTo('modify_own')) {
isAllowedTo('modify_replies');
} else {
isAllowedTo('modify_own');
}
} elseif ($row['ID_MEMBER_POSTER'] == $ID_MEMBER && !allowedTo('modify_any')) {
isAllowedTo('modify_replies');
$moderationAction = true;
} else {
isAllowedTo('modify_any');
if ($row['ID_MEMBER'] != $ID_MEMBER) {
$moderationAction = true;
}
}

$posterIsGuest = empty($row['ID_MEMBER']);

if (!allowedTo('moderate_forum') || !$posterIsGuest) {
$_POST['guestname'] = addslashes($row['posterName']);
$_POST['email'] = addslashes($row['posterEmail']);
}
}


if (!empty($modSettings['minWordLen']) && ((int)$modSettings['minWordLen'] != 0)) {
$Temp = trim(preg_replace('~[^a-z0-9 ]~si', '', $_POST['message']));
$Temp = preg_replace('~(( )+)~si', ' ', $Temp);
$WordArr = explode(' ', $Temp);
if (count($WordArr) < (int)$modSettings['minWordLen']) {
$post_errors[] = 'minWordLen';
}
}

if (!empty($modSettings['minChar']) && ((int)$modSettings['minChar'] != 0)) {
if (strlen($_POST['message']) < (int)$modSettings['minChar']) {
$post_errors[] = 'minChar';
}
}
if (!isset($_POST['subject']) || $func['htmltrim']($_POST['subject']) === '') {
$post_errors[] = 'no_subject';
}
if (!isset($_POST['message']) || $func['htmltrim']($_POST['message']) === '') {
$post_errors[] = 'no_message';
} elseif (!empty($modSettings['max_messageLength']) && $func['strlen']($_POST['message']) > $modSettings['max_messageLength']) {
$post_errors[] = 'long_message';
} else {
$_POST['message'] = $func['htmlspecialchars']($_POST['message'], ENT_QUOTES);

if ($user_info['is_guest']) {
$user_info['name'] = $_POST['guestname'];
}
preparsecode($_POST['message']);

if ($func['htmltrim'](strip_tags(parse_bbc($_POST['message'], false), '<img>')) === '') {
$post_errors[] = 'no_message';
}
}

if (isset($_POST['message']) && strtolower($_POST['message']) == 'i am the administrator.' && !$user_info['is_admin']) {
fatal_error('Knave! Masquerader! Charlatan!', false);
}

if ($posterIsGuest) {
require_once($sourcedir . '/Subs-Members.php');
if (isReservedName($_POST['guestname'], 0, true, false) && (!isset($row['posterName']) || $_POST['guestname'] != $row['posterName']))
$post_errors[] = 'bad_name';
} elseif (!isset($_REQUEST['msg'])) {
$_POST['guestname'] = addslashes($user_info['username']);
$_POST['email'] = addslashes($user_info['email']);
}

if (!empty($post_errors)) {
loadLanguage('Errors');
$_REQUEST['preview'] = true;
$context['post_error'] = array('messages' => array());
foreach ($post_errors as $post_error) {
$context['post_error'][$post_error] = true;
$context['post_error']['messages'][] = $txt['error_' . $post_error];
}
return Post();
}

if (!isset($_REQUEST['msg'])) {
spamProtection('spam');
}

ignore_user_abort(true);
@set_time_limit(300);

$_POST['subject'] = strtr($func['htmlspecialchars']($_POST['subject']), array("\r" => '', "\n" => '', "\t" => ''));
$_POST['guestname'] = htmlspecialchars($_POST['guestname']);
$_POST['email'] = htmlspecialchars($_POST['email']);

if ($func['strlen']($_POST['subject']) > 100) {
  $_POST['subject'] = addslashes($func['substr'](stripslashes($_POST['subject']), 0, 100));
}

$newTopic = empty($_REQUEST['msg']) && empty($topic);
$msgOptions = array(
  'id' => empty($_REQUEST['msg']) ? 0 : (int) $_REQUEST['msg'],
  'subject' => $_POST['subject'],
  'body' => $_POST['message'],
  'smileys_enabled' => !isset($_POST['ns']),
  'hiddenOption' => (empty($_POST['hiddenOption']) ? 0 : $_POST['hiddenOption']),
  'hiddenValue' => (empty($_POST['hiddenValue']) ? 0 : $_POST['hiddenValue']),
);

$topicOptions = array(
  'id' => empty($topic) ? 0 : $topic,
  'board' => $board,
  'lock_mode' => isset($_POST['lock']) ? (int) $_POST['lock'] : null,
  'sticky_mode' => isset($_POST['sticky']) && !empty($modSettings['enableStickyTopics']) ? (int) $_POST['sticky'] : null,
);

$posterOptions = array(
  'id' => $ID_MEMBER,
  'name' => $_POST['guestname'],
  'email' => $_POST['email'],
  'update_post_count' => !$user_info['is_guest'] && !isset($_REQUEST['msg']) && $board_info['posts_count'],
);

if (!empty($_REQUEST['msg'])) {
  if (time() - $row['posterTime'] > $modSettings['edit_wait_time'] || $ID_MEMBER != $row['ID_MEMBER']) {
    $msgOptions['modify_time'] = time();
    $msgOptions['modify_name'] = addslashes($user_info['name']);
  }

  $msgOptions['edit_reason'] = addslashes(strtr(htmlspecialchars(isset($_POST['edit_reason']) ? $_POST['edit_reason'] : ''), array("\r" => '', "\n" => '', "\t" => '')));
  modifyPost($msgOptions, $topicOptions, $posterOptions);
} else {
  createPost($msgOptions, $topicOptions, $posterOptions);

  if (isset($topicOptions['id'])) {
    $topic = $topicOptions['id'];
  }
}

if(isset($_REQUEST['tags']) && !isset($_REQUEST['num_replies'])) {
$dbresult = db_query("SELECT COUNT(*) as total FROM {$db_prefix}tags_log WHERE ID_TOPIC = " . $topic, __FILE__, __LINE__);
$row = mysqli_fetch_assoc($dbresult);
$totaltags = $row['total'];
mysqli_free_result($dbresult);
$tags = explode(',',htmlspecialchars($_REQUEST['tags'],ENT_QUOTES));
if($totaltags < $modSettings['smftags_set_maxtags']) {
$tagcount = 0;
foreach($tags as $tag) {
if($tagcount >= $modSettings['smftags_set_maxtags']) {
continue;
}
if(empty($tag)) {
continue;
}
if(strlen($tag) < $modSettings['smftags_set_mintaglength']){
continue;
}
if(strlen($tag) > $modSettings['smftags_set_maxtaglength']) {
continue;
}
$dbresult = db_query("SELECT ID_TAG FROM {$db_prefix}tags WHERE tag = '$tag'", __FILE__, __LINE__);
if(db_affected_rows() == 0) {
db_query("INSERT INTO {$db_prefix}tags (tag, approved) VALUES ('$tag',1)", __FILE__, __LINE__);
$ID_TAG = db_insert_id();
db_query("INSERT INTO {$db_prefix}tags_log (ID_TAG,ID_TOPIC, ID_MEMBER) VALUES ($ID_TAG,$topic,$ID_MEMBER)", __FILE__, __LINE__);
$tagcount++;
} else {
$row = mysqli_fetch_assoc($dbresult);
$ID_TAG = $row['ID_TAG'];
$dbresult2= db_query("SELECT ID FROM {$db_prefix}tags_log WHERE ID_TAG  =  $ID_TAG  AND ID_TOPIC = $topic", __FILE__, __LINE__);
if(db_affected_rows() != 0) {
continue;
}
mysqli_free_result($dbresult2);
db_query("INSERT INTO {$db_prefix}tags_log (ID_TAG,ID_TOPIC, ID_MEMBER) VALUES ($ID_TAG,$topic,$ID_MEMBER)", __FILE__, __LINE__);
$tagcount++;
}
mysqli_free_result($dbresult);
}
}
}

if(!empty($_REQUEST['topic'])) {
$history = db_query("SELECT t.ID_TOPIC, m.subject, t.ID_MEMBER_STARTED, m.ID_TOPIC FROM ({$db_prefix}topics AS t, {$db_prefix}messages AS m) WHERE t.ID_TOPIC = $topic AND m.ID_TOPIC = t.ID_TOPIC AND m.ID_TOPIC = $topic", __FILE__, __LINE__);
$sacarid = mysqli_fetch_assoc($history);

$ID_MODERATOR = $context['user']['id'];
$ID_MEMBER = $sacarid['ID_MEMBER_STARTED'];
$ID_TOPIC = $topic;
$TYPE = 'Post';
$ACTION = 'modify';
$subject = $sacarid['subject'];
$reason = htmlentities($_POST['causa'], ENT_QUOTES, "UTF-8");
if (!empty($modSettings['modlog_enabled']) && allowedTo('modify_any')) {
db_query("INSERT INTO {$db_prefix}mod_history (ID_MODERATOR, ID_MEMBER, ID_TOPIC, TYPE, ACTION, subject, reason) VALUES ('" . $ID_MODERATOR . "', '" . $ID_MEMBER . "', '" . $ID_TOPIC . "', '" . $TYPE . "', '" . $ACTION . "', '" . $subject . "', '" . $reason . "')", __FILE__, __LINE__);
}
}

if (isset($_REQUEST['xml'])) {
require_once($sourcedir . '/Display.php');
$_REQUEST['msg'] = $msgOptions['id'];
call_user_func('Display');
} else {
if(isset($_REQUEST['msg']))
redirectexit($boardurl . '/post-agregado/' . $topic);

if (!empty($_POST['move']) && allowedTo('move_any')) {
redirectexit($scripturl);
}
if (isset($_REQUEST['msg']) && !empty($_REQUEST['goback'])) {
redirectexit($scripturl);
} elseif (!empty($_REQUEST['goback'])) {
redirectexit($boardurl . '/post-agregado/' . $topic);
} else {
redirectexit($boardurl . '/post-agregado/' . $topic);
}
}
}

function getTopic() {
  global $topic, $db_prefix, $modSettings, $context;

$newReplies = empty($_REQUEST['num_replies']) || $context['num_replies'] <= $_REQUEST['num_replies'] ? 0 : $context['num_replies'] - $_REQUEST['num_replies'];

if (isset($_REQUEST['xml'])) {
$limit = "LIMIT " . (empty($newReplies) ? '0' : $newReplies);
} else {
$limit = empty($modSettings['topicSummaryPosts']) ? '' : ' 
    LIMIT ' . (int) $modSettings['topicSummaryPosts'];
}
$request = db_query("
SELECT IFNULL(mem.realName, m.posterName) AS posterName, m.posterTime, m.body, m.smileysEnabled, m.ID_MSG, m.hiddenOption, m.hiddenValue, m.ID_MEMBER, m.ID_BOARD
FROM {$db_prefix}messages AS m
LEFT JOIN {$db_prefix}members AS mem ON (mem.ID_MEMBER = m.ID_MEMBER)
WHERE m.ID_TOPIC = $topic" . (isset($_REQUEST['msg']) ? "
AND m.ID_MSG < " . (int) $_REQUEST['msg'] : '') . "
ORDER BY m.ID_MSG DESC$limit", __FILE__, __LINE__);
$context['previous_posts'] = array();
while ($row = mysqli_fetch_assoc($request)) {
$row['can_view_post'] = 1;
if (!empty($modSettings['allow_hiddenPost']) && $row['hiddenOption'] > 0) {
global $sourcedir;
require_once($sourcedir . '/HidePost.php');
$row['ID_TOPIC'] = $topic;
$context['current_message'] = $row;
$row['body'] = getHiddenMessage();
$row['can_view_post'] = $context['can_view_post'];
}
censorText($row['body']);
$row['body'] = parse_bbc($row['body'], $row['smileysEnabled'], $row['ID_MSG']);
$context['previous_posts'][] = array(
'can_view_post' => $row['can_view_post'],
'poster' => $row['posterName'],
'message' => $row['body'],
'time' => timeformat($row['posterTime']),
'timestamp' => forum_time(true, $row['posterTime']),
'id' => $row['ID_MSG'],
'is_new' => !empty($newReplies),
);
if (!empty($newReplies)) {
$newReplies--;
}
}
mysqli_free_result($request);
}
?>