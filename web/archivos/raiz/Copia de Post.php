<?php
if (!defined('SMF'))
  die('Hacking attempt...');

function Post() {
  global $txt, $topic, $db_prefix, $modSettings, $board, $ID_MEMBER;
  global $user_info, $sc, $context, $settings, $sourcedir;
  global $func;

  loadLanguage('Post');

  // Tagging System
  $dbresult= db_query("
  SELECT 
    t.tag,l.ID,t.ID_TAG 
  FROM {$db_prefix}tags_log as l, {$db_prefix}tags as t 
  WHERE t.ID_TAG = l.ID_TAG && l.ID_TOPIC = $topic", __FILE__, __LINE__);
    $context['topic_tags'] = array();
     while($row = mysqli_fetch_assoc($dbresult))
      {
        $context['topic_tags'][] = array(
        'ID' => $row['ID'],
        'ID_TAG' => $row['ID_TAG'],
        'tag' => $row['tag'],
        );
    }
  mysqli_free_result($dbresult);
  // End Tagging System

  $request = db_query("
    SELECT b.ID_BOARD, b.name, b.childLevel, c.name AS catName
    FROM {$db_prefix}boards AS b
      LEFT JOIN {$db_prefix}categories AS c ON (c.ID_CAT = b.ID_CAT)
  ", __FILE__, __LINE__);
  $context['boards'] = array();
  while ($row = mysqli_fetch_assoc($request))
    $context['boards'][] = array(
      'id' => $row['ID_BOARD'],
      'name' => $row['name'],
      'category' => $row['catName'],
      'child_level' => $row['childLevel'],
    );
  mysqli_free_result($request);

    $categorias = $_POST['categorias'];

  require_once($sourcedir . '/Subs-Post.php');
  $context['can_hide_post'] = 0;
  $context['hidden_option'] = 0;
  $context['hidden_value'] = 0;
  if (!empty($topic))
  {
    $request = db_query("
      SELECT
        t.locked, IFNULL(ln.ID_TOPIC, 0) AS notify, t.isSticky, t.ID_POLL, t.numReplies, mf.ID_MEMBER,
        t.ID_FIRST_MSG, mf.subject, GREATEST(ml.posterTime, ml.modifiedTime) AS lastPostTime
      FROM {$db_prefix}topics AS t
        LEFT JOIN {$db_prefix}log_notify AS ln ON (ln.ID_TOPIC = t.ID_TOPIC AND ln.ID_MEMBER = $ID_MEMBER)
        LEFT JOIN {$db_prefix}messages AS mf ON (mf.ID_MSG = t.ID_FIRST_MSG)
        LEFT JOIN {$db_prefix}messages AS ml ON (ml.ID_MSG = t.ID_LAST_MSG)
      WHERE t.ID_TOPIC = $topic
      LIMIT 1", __FILE__, __LINE__);
    list ($locked, $context['notify'], $sticky, $context['num_replies'], $ID_MEMBER_POSTER, $ID_FIRST_MSG, $first_subject, $lastPostTime) = mysqli_fetch_row($request);
    mysqli_free_result($request);

    if (empty($_REQUEST['msg']))
    {
      if ($user_info['is_guest'] && !allowedTo('post_reply_any'))
        is_not_guest();

      if ($ID_MEMBER_POSTER != $ID_MEMBER)
        isAllowedTo('post_reply_any');
      elseif (!allowedTo('post_reply_any'))
        isAllowedTo('post_reply_own');
    }

    $context['can_hide_post'] = (allowedTo('hide_post_any') ||  ($ID_MEMBER == $ID_MEMBER_POSTER && allowedTo('hide_post_own'))) && !empty($modSettings['allow_hiddenPost']);
    $context['can_lock'] = allowedTo('lock_any') || ($ID_MEMBER == $ID_MEMBER_POSTER && allowedTo('lock_own'));
    $context['can_sticky'] = allowedTo('make_sticky') && !empty($modSettings['enableStickyTopics']);

    $context['notify'] = !empty($context['notify']);
    $context['sticky'] = isset($_REQUEST['sticky']) ? !empty($_REQUEST['sticky']) : $sticky;
    
  }
  else
  {
    if (!empty($board))
      isAllowedTo('post_new');

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
  if ($locked && !allowedTo('moderate_board'))
    fatal_lang_error(90, false);

  if (empty($context['post_errors']))
    $context['post_errors'] = array();

  // Previewing, modifying, or posting?
  if (isset($_REQUEST['message']) || !empty($context['post_error']))
  {
    checkSession('get');

  $request = db_query("
      SELECT
        m.ID_MEMBER, m.posterName, t.ID_MEMBER_STARTED
      FROM {$db_prefix}messages AS m, {$db_prefix}topics AS t
      WHERE m.ID_MSG = " . (int) $_REQUEST['msg'] . "", __FILE__, __LINE__);
    if (mysqli_num_rows($request) == 0)
      fatal_lang_error('noresponder', false);
    $row = mysqli_fetch_assoc($request);
    
    if ($row['ID_MEMBER'] != $ID_MEMBER && (!allowedTo('modify_any')))
    fatal_lang_error('noresponder', false);

    // Validate inputs.
    if (empty($context['post_error']))
    {
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
    }
    else
    {
      if (!isset($_REQUEST['subject']))
        $_REQUEST['subject'] = '';
      if (!isset($_REQUEST['message']))
        $_REQUEST['message'] = '';
      if (!isset($_REQUEST['icon']))
        $_REQUEST['icon'] = 'xx';

      $really_previewing = false;
    }

    // Set up the inputs for the form.
    $form_subject = strtr($func['htmlspecialchars'](stripslashes($_REQUEST['subject'])), array("\r" => '', "\n" => '', "\t" => ''));
    $form_message = $func['htmlspecialchars'](stripslashes($_REQUEST['message']), ENT_QUOTES);

    // Make sure the subject isn't too long - taking into account special characters.
    if ($func['strlen']($form_subject) > 100)
      $form_subject = $func['substr']($form_subject, 0, 100);

    // Have we inadvertently trimmed off the subject of useful information?
    if ($func['htmltrim']($form_subject) === '')
      $context['post_error']['no_subject'] = true;

    // Any errors occurred?
    if (!empty($context['post_error']))
    {
      loadLanguage('Errors');

      $context['error_type'] = 'minor';

      $context['post_error']['messages'] = array();
      foreach ($context['post_error'] as $post_error => $dummy)
      {
        if ($post_error == 'messages')
          continue;

        $context['post_error']['messages'][] = $txt['error_' . $post_error];

        // If it's not a minor error flag it as such.
        if (!in_array($post_error, array('new_reply', 'new_replies', 'old_topic')))
          $context['error_type'] = 'serious';
      }
    }

    // Are you... a guest?
    if ($user_info['is_guest'])
    {
      $_REQUEST['guestname'] = !isset($_REQUEST['guestname']) ? '' : trim($_REQUEST['guestname']);
      $_REQUEST['email'] = !isset($_REQUEST['email']) ? '' : trim($_REQUEST['email']);

      $_REQUEST['guestname'] = htmlspecialchars($_REQUEST['guestname']);
      $context['name'] = $_REQUEST['guestname'];
      $_REQUEST['email'] = htmlspecialchars($_REQUEST['email']);
      $context['email'] = $_REQUEST['email'];

      $user_info['name'] = $_REQUEST['guestname'];
    }

    // Only show the preview stuff if they hit Preview.
    if ($really_previewing == true || isset($_REQUEST['xml']))
    {
      // Set up the preview message and subject and censor them...
      $context['preview_message'] = $form_message;
      preparsecode($form_message, true);
      preparsecode($context['preview_message']);

      // Do all bulletin board code tags, with or without smileys.
      $context['preview_message'] = parse_bbc($context['preview_message'], isset($_REQUEST['ns']) ? 0 : 1);

      if ($form_subject != '')
      {
        $context['preview_subject'] = $form_subject;

        censorText($context['preview_subject']);
        censorText($context['preview_message']);
      }
      else
        $context['preview_subject'] = '<i>' . $txt[24] . '</i>';

      // Protect any CDATA blocks.
      if (isset($_REQUEST['xml']))
        $context['preview_message'] = strtr($context['preview_message'], array(']]>' => ']]]]><![CDATA[>'));
    }

    // Set up the checkboxes.
    $context['notify'] = !empty($_REQUEST['notify']);
    $context['use_smileys'] = !isset($_REQUEST['ns']);

    $context['icon'] = isset($_REQUEST['icon']) ? preg_replace('~[\./\\\\*\':"<>]~', '', $_REQUEST['icon']) : 'xx';

    // Set the destination action for submission.
    $context['destination'] = 'post2;start=' . $_REQUEST['start'] . (isset($_REQUEST['msg']) ? ';msg=' . $_REQUEST['msg'] . ';sesc=' . $sc : '') . (isset($_REQUEST['poll']) ? ';poll' : '');
    $context['submit_label'] = isset($_REQUEST['msg']) ? $txt[10] : $txt[105];

    // Previewing an edit?
    if (isset($_REQUEST['msg']) && !empty($topic))
    {
      // Allow moderators to change names....
      if (allowedTo('moderate_forum') && !empty($topic))
      {
        $request = db_query("
          SELECT ID_MEMBER, posterName, posterEmail
          FROM {$db_prefix}messages
          WHERE ID_MSG = " . (int) $_REQUEST['msg'] . "
            AND ID_TOPIC = $topic
          LIMIT 1", __FILE__, __LINE__);
        $row = mysqli_fetch_assoc($request);
        mysqli_free_result($request);

        if (empty($row['ID_MEMBER']))
        {
          $context['name'] = htmlspecialchars($row['posterName']);
          $context['email'] = htmlspecialchars($row['posterEmail']);
        }
      }
    }

    // No check is needed, since nothing is really posted.
    checkSubmitOnce('free');
  }
  // Editing a message...
  elseif (isset($_REQUEST['msg']) && !empty($topic))
  {
    // Get the existing message.
    $request = db_query("
      SELECT
        m.ID_MEMBER, m.modifiedTime, m.smileysEnabled, m.body,
        m.posterName, m.posterEmail, m.subject, m.icon,
        m.hiddenOption, m.hiddenValue,
        IFNULL(a.size, -1) AS filesize, a.filename, a.ID_ATTACH,
        t.ID_MEMBER_STARTED AS ID_MEMBER_POSTER, m.posterTime
      FROM ({$db_prefix}messages AS m, {$db_prefix}topics AS t)
        LEFT JOIN {$db_prefix}attachments AS a ON (a.ID_MSG = m.ID_MSG AND a.attachmentType = 0)
      WHERE m.ID_MSG = $topic
        AND m.ID_TOPIC = t.ID_TOPIC
        AND m.ID_TOPIC = $topic
        AND t.ID_TOPIC = $topic", __FILE__, __LINE__);
    // The message they were trying to edit was most likely deleted.
    // !!! Change this error message?
    if (mysqli_num_rows($request) == 0)
      fatal_lang_error('smf232', false);
    $row = mysqli_fetch_assoc($request);

    if ($row['ID_MEMBER'] == $ID_MEMBER && !allowedTo('modify_any'))
    {
      // Give an extra five minutes over the disable time threshold, so they can type.
      if (!empty($modSettings['edit_disable_time']) && $row['posterTime'] + ($modSettings['edit_disable_time'] + 5) * 60 < time())
        fatal_lang_error('modify_post_time_passed', false);
      elseif ($row['ID_MEMBER_POSTER'] == $ID_MEMBER && !allowedTo('modify_own'))
        isAllowedTo('modify_replies');
      else
        isAllowedTo('modify_own');
    }
    elseif ($row['ID_MEMBER_POSTER'] == $ID_MEMBER && !allowedTo('modify_any'))
      isAllowedTo('modify_replies');
    else
      isAllowedTo('modify_any');

    // When was it last modified?
    if (!empty($row['modifiedTime']))
      $context['last_modified'] = timeformat($row['modifiedTime']);

    // Get the stuff ready for the form.
    $form_subject = $row['subject'];
    $form_message = un_preparsecode($row['body']);
    censorText($form_message);
    censorText($form_subject);

    // Check the boxes that should be checked.
    $context['hidden_option'] = $row['hiddenOption'];
    $context['hidden_value'] = $row['hiddenValue'];

    $context['use_smileys'] = !empty($row['smileysEnabled']);
    $context['icon'] = $row['icon'];

    // Allow moderators to change names....
    if (allowedTo('moderate_forum') && empty($row['ID_MEMBER']))
    {
      $context['name'] = htmlspecialchars($row['posterName']);
      $context['email'] = htmlspecialchars($row['posterEmail']);
    }

    // Set the destinaton.
    $context['destination'] = 'post2;start=' . $_REQUEST['start'] . ';msg=' . $_REQUEST['msg'] . ';sesc=' . $sc;
    $context['submit_label'] = $txt[10];
  }
  // Posting...
  else
  {
    // By default....
    $context['use_smileys'] = true;
    $context['icon'] = 'xx';
    $context['hidden_option'] = 0;
    $context['hidden_value'] = 0;


    if ($user_info['is_guest'])
    {
      $context['name'] = '';
      $context['email'] = '';
    }
    $context['destination'] = 'post2;start=' . $_REQUEST['start'];

    $context['submit_label'] = $txt[105];

      $form_subject = isset($_GET['subject']) ? $_GET['subject'] : '';
      $form_message = '';
  }

  // If we are coming here to make a reply, and someone has already replied... make a special warning message.
  if (isset($newRepliesError))
  {
    $context['post_error']['messages'][] = $newRepliesError == 1 ? $txt['error_new_reply'] : $txt['error_new_replies'];
    $context['error_type'] = 'minor';
  }

  if (isset($oldTopicError))
  {
    $context['post_error']['messages'][] = $txt['error_old_topic'];
    $context['error_type'] = 'minor';
  }

  if (isset($_REQUEST['msg']))
    $context['page_title'] = $txt[66];
  elseif (isset($_REQUEST['subject'], $context['preview_subject']))
    $context['page_title'] = $txt[507];
  elseif (empty($topic))
    $context['page_title'] = $txt[33];
  else
    $context['page_title'] = $txt[25];

  $context['subject'] = addcslashes($form_subject, '"');
  $context['message'] = str_replace(array('"', '<', '>', '  '), array('&quot;', '&lt;', '&gt;', ' &nbsp;'), $form_message);

  // Message icons - customized icons are off?
  if (empty($modSettings['messageIcons_enable']))
  {
    $context['icons'] = array(
      array('value' => 'xx', 'name' => $txt[281]),
      array('value' => 'thumbup', 'name' => $txt[282]),
      array('value' => 'thumbdown', 'name' => $txt[283]),
      array('value' => 'exclamation', 'name' => $txt[284]),
      array('value' => 'question', 'name' => $txt[285]),
      array('value' => 'lamp', 'name' => $txt[286]),
      array('value' => 'smiley', 'name' => $txt[287]),
      array('value' => 'angry', 'name' => $txt[288]),
      array('value' => 'cheesy', 'name' => $txt[289]),
      array('value' => 'grin', 'name' => $txt[293]),
      array('value' => 'sad', 'name' => $txt[291]),
      array('value' => 'wink', 'name' => $txt[292])
    );

    foreach ($context['icons'] as $k => $dummy)
    {
      $context['icons'][$k]['url'] = $settings['images_url'] . '/post/' . $dummy['value'] . '.gif';
      $context['icons'][$k]['is_last'] = false;
    }

    $context['icon_url'] = $settings['images_url'] . '/post/' . $context['icon'] . '.gif';
  }
  // Otherwise load the icons, and check we give the right image too...
  else
  {
    // Regardless of what *should* exist, let's do this properly.
    $stable_icons = array('xx', 'thumbup', 'thumbdown', 'exclamation', 'question', 'lamp', 'smiley', 'angry', 'cheesy', 'grin', 'sad', 'wink', 'moved', 'recycled', 'wireless');
    $context['icon_sources'] = array();
    foreach ($stable_icons as $icon)
      $context['icon_sources'][$icon] = 'images_url';

    // Array for all icons that need to revert to the default theme!
    $context['javascript_icons'] = array();

    if (($temp = cache_get_data('posting_icons-' . $board, 480)) == null)
    {
      $request = db_query("
        SELECT title, filename
        FROM {$db_prefix}message_icons
        WHERE ID_BOARD IN (0, $board)", __FILE__, __LINE__);
      $icon_data = array();
      while ($row = mysqli_fetch_assoc($request))
        $icon_data[] = $row;
      mysqli_free_result($request);

      cache_put_data('posting_icons-' . $board, $icon_data, 480);
    }
    else
      $icon_data = $temp;

    $context['icons'] = array();
    foreach ($icon_data as $icon)
    {
      if (!isset($context['icon_sources'][$icon['filename']]))
        $context['icon_sources'][$icon['filename']] = file_exists($settings['theme_dir'] . '/images/post/' . $icon['filename'] . '.gif') ? 'images_url' : 'default_images_url';

      // If the icon exists only in the default theme, ensure the javascript popup respects this.
      if ($context['icon_sources'][$icon['filename']] == 'default_images_url')
        $context['javascript_icons'][] = $icon['filename'];

      $context['icons'][] = array(
        'value' => $icon['filename'],
        'name' => $icon['title'],
        'url' => $settings[$context['icon_sources'][$icon['filename']]] . '/post/' . $icon['filename'] . '.gif',
        'is_last' => false,
      );
    }

    $context['icon_url'] = $settings[isset($context['icon_sources'][$context['icon']]) ? $context['icon_sources'][$context['icon']] : 'images_url'] . '/post/' . $context['icon'] . '.gif';
  }

  if (!empty($context['icons']))
    $context['icons'][count($context['icons']) - 1]['is_last'] = true;

  $found = false;
  for ($i = 0, $n = count($context['icons']); $i < $n; $i++)
  {
    $context['icons'][$i]['selected'] = $context['icon'] == $context['icons'][$i]['value'];
    if ($context['icons'][$i]['selected'])
      $found = true;
  }
  if (!$found)
    array_unshift($context['icons'], array(
      'value' => $context['icon'],
      'name' => $txt['current_icon'],
      'url' => $context['icon_url'],
      'is_last' => empty($context['icons']),
      'selected' => true,
    ));


  $context['hidden_options'] = array(
    array('value' => 0, 'name' => $txt['hide_select']),
    array('value' => 1, 'name' => $txt['hide_login']),
  );

  $found = false;
  for ($i = 0, $n = count($context['hidden_options']); $i < $n; $i++)
  {
    $context['hidden_options'][$i]['selected'] = $context['hidden_option'] == $context['hidden_options'][$i]['value'];
    if ($context['icons'][$i]['selected'])
      $found = true;
  }
  if (!empty($topic))
    getTopic();

  $context['back_to_topic'] = isset($_REQUEST['goback']) || (isset($_REQUEST['msg']) && !isset($_REQUEST['subject']));
  $context['show_additional_options'] = !empty($_POST['additional_options']);

  $context['is_new_topic'] = empty($topic);
  $context['is_new_post'] = !isset($_REQUEST['msg']);
  $context['is_first_post'] = $context['is_new_topic'] || (isset($_REQUEST['msg']) && $_REQUEST['msg'] == $ID_FIRST_MSG);

  checkSubmitOnce('register');

  if (WIRELESS)
    $context['sub_template'] = WIRELESS_PROTOCOL . '_post';
  elseif (!isset($_REQUEST['xml']))
    loadTemplate('Post');
    
}

function Post2()
{
  global $board, $topic, $txt, $db_prefix, $modSettings, $sourcedir, $context;
  global $ID_MEMBER, $user_info, $board_info, $options, $func, $boardurl;

  if (isset($_REQUEST['preview']))
    return Post();

  checkSubmitOnce('check');

  // No errors as yet.
  $post_errors = array();

  // If the session has timed out, let the user re-submit their form.
  if (checkSession('post', '', false) != '')
    $post_errors[] = 'session_timeout';

  require_once($sourcedir . '/Subs-Post.php');
  loadLanguage('Post');

  // Replying to a topic?
  if (!empty($topic) && !isset($_REQUEST['msg']))
  {
    $request = db_query("
      SELECT t.locked, t.isSticky, t.ID_POLL, t.numReplies, m.ID_MEMBER
      FROM ({$db_prefix}topics AS t, {$db_prefix}messages AS m)
      WHERE t.ID_TOPIC = $topic
        AND m.ID_MSG = t.ID_FIRST_MSG
      LIMIT 1", __FILE__, __LINE__);
    list ($tmplocked, $tmpstickied, $numReplies, $ID_MEMBER_POSTER) = mysqli_fetch_row($request);
    mysqli_free_result($request);

    // Don't allow a post if it's locked.
    if ($tmplocked != 0 && !allowedTo('moderate_board'))
      fatal_lang_error(90, false);

    if ($ID_MEMBER_POSTER != $ID_MEMBER)
      isAllowedTo('post_reply_any');
    elseif (!allowedTo('post_reply_any'))
      isAllowedTo('post_reply_own');

    if (isset($_POST['lock']))
    {
      // Nothing is changed to the lock.
      if ((empty($tmplocked) && empty($_POST['lock'])) || (!empty($_POST['lock']) && !empty($tmplocked)))
        unset($_POST['lock']);
      // You're have no permission to lock this topic.
      elseif (!allowedTo(array('lock_any', 'lock_own')) || (!allowedTo('lock_any') && $ID_MEMBER != $ID_MEMBER_POSTER))
        unset($_POST['lock']);
      // You are allowed to (un)lock your own topic only.
      elseif (!allowedTo('lock_any'))
      {
        // You cannot override a moderator lock.
        if ($tmplocked == 1)
          unset($_POST['lock']);
        else
          $_POST['lock'] = empty($_POST['lock']) ? 0 : 2;
      }
      // Hail mighty moderator, (un)lock this topic immediately.
      else
        $_POST['lock'] = empty($_POST['lock']) ? 0 : 1;
    }

    // So you wanna (un)sticky this...let's see.
    if (isset($_POST['sticky']) && (empty($modSettings['enableStickyTopics']) || $_POST['sticky'] == $tmpstickied || !allowedTo('make_sticky')))
      unset($_POST['sticky']);

    if (isset($_POST['hiddenOption']) && !((allowedTo('hide_post_any') || ($ID_MEMBER == $ID_MEMBER_POSTER && allowedTo('hide_post_own'))) && !empty($modSettings['allow_hiddenPost'])))
      unset($_POST['hiddenOption']);
    // If the number of replies has changed, if the setting is enabled, go back to Post() - which handles the error.
    $newReplies = isset($_POST['num_replies']) && $numReplies > $_POST['num_replies'] ? $numReplies - $_POST['num_replies'] : 0;
    if (empty($options['no_new_reply_warning']) && !empty($newReplies))
    {
      $_REQUEST['preview'] = true;
      return Post();
    }

    $posterIsGuest = $user_info['is_guest'];
  }

  // Posting a new topic.
  elseif (empty($topic))
  {
    if (isset($_POST['lock']))
    {
      // New topics are by default not locked.
      if (empty($_POST['lock']))
        unset($_POST['lock']);
      // Besides, you need permission.
      elseif (!allowedTo(array('lock_any', 'lock_own')))
        unset($_POST['lock']);
      // A moderator-lock (1) can override a user-lock (2).
      else
        $_POST['lock'] = allowedTo('lock_any') ? 1 : 2;
    }

    if (isset($_POST['sticky']) && (empty($modSettings['enableStickyTopics']) || empty($_POST['sticky']) || !allowedTo('make_sticky')))
      unset($_POST['sticky']);

    $posterIsGuest = $user_info['is_guest'];
  }

  // Modifying an existing message?
  elseif (isset($_REQUEST['msg']) && !empty($topic))
  {
    $_REQUEST['msg'] = (int) $_REQUEST['msg'];

    $request = db_query("
      SELECT
        m.ID_MEMBER, m.posterName, m.posterEmail, m.posterTime, 
        t.ID_FIRST_MSG, t.locked, t.isSticky, t.ID_MEMBER_STARTED AS ID_MEMBER_POSTER
      FROM ({$db_prefix}messages AS m, {$db_prefix}topics AS t)
      WHERE m.ID_MSG = $_REQUEST[msg]
        AND t.ID_TOPIC = $topic
      LIMIT 1", __FILE__, __LINE__);
    if (mysqli_num_rows($request) == 0)
      fatal_lang_error('smf272', false);
    $row = mysqli_fetch_assoc($request);
    mysqli_free_result($request);

    if (!empty($row['locked']) && !allowedTo('moderate_board'))
      fatal_lang_error(90, false);

    if (isset($_POST['lock']))
    {
      // Nothing changes to the lock status.
      if ((empty($_POST['lock']) && empty($row['locked'])) || (!empty($_POST['lock']) && !empty($row['locked'])))
        unset($_POST['lock']);
      // You're simply not allowed to (un)lock this.
      elseif (!allowedTo(array('lock_any', 'lock_own')) || (!allowedTo('lock_any') && $ID_MEMBER != $row['ID_MEMBER_POSTER']))
        unset($_POST['lock']);
      // You're only allowed to lock your own topics.
      elseif (!allowedTo('lock_any'))
      {
        // You're not allowed to break a moderator's lock.
        if ($row['locked'] == 1)
          unset($_POST['lock']);
        // Lock it with a soft lock or unlock it.
        else
          $_POST['lock'] = empty($_POST['lock']) ? 0 : 2;
      }
      // You must be the moderator.
      else
        $_POST['lock'] = empty($_POST['lock']) ? 0 : 1;
    }

    // Change the sticky status of this topic?
    if (isset($_POST['sticky']) && (!allowedTo('make_sticky') || $_POST['sticky'] == $row['isSticky']))
      unset($_POST['sticky']);

    if ($row['ID_MEMBER'] == $ID_MEMBER && !allowedTo('modify_any'))
    {
      if (!empty($modSettings['edit_disable_time']) && $row['posterTime'] + ($modSettings['edit_disable_time'] + 5) * 60 < time())
        fatal_lang_error('modify_post_time_passed', false);
      elseif ($row['ID_MEMBER_POSTER'] == $ID_MEMBER && !allowedTo('modify_own'))
        isAllowedTo('modify_replies');
      else
        isAllowedTo('modify_own');
    }
    elseif ($row['ID_MEMBER_POSTER'] == $ID_MEMBER && !allowedTo('modify_any'))
    {
      isAllowedTo('modify_replies');

      // If you're modifying a reply, I say it better be logged...
      $moderationAction = true;
    }
    else
    {
      isAllowedTo('modify_any');

      // Log it, assuming you're not modifying your own post.
      if ($row['ID_MEMBER'] != $ID_MEMBER)
        $moderationAction = true;
    }

    $posterIsGuest = empty($row['ID_MEMBER']);

    if (!allowedTo('moderate_forum') || !$posterIsGuest)
    {
      $_POST['guestname'] = addslashes($row['posterName']);
      $_POST['email'] = addslashes($row['posterEmail']);
    }
  }

  // If the poster is a guest evaluate the legality of name and email.
  if ($posterIsGuest)
  {
    $_POST['guestname'] = !isset($_POST['guestname']) ? '' : trim($_POST['guestname']);
    $_POST['email'] = !isset($_POST['email']) ? '' : trim($_POST['email']);

    if ($_POST['guestname'] == '' || $_POST['guestname'] == '_')
      $post_errors[] = 'no_name';
    if ($func['strlen']($_POST['guestname']) > 25)
      $post_errors[] = 'long_name';

    if (empty($modSettings['guest_post_no_email']))
    {
      // Only check if they changed it!
      if (!isset($row) || $row['posterEmail'] != $_POST['email'])
      {
        if (!allowedTo('moderate_forum') && (!isset($_POST['email']) || $_POST['email'] == ''))
          $post_errors[] = 'no_email';
        if (!allowedTo('moderate_forum') && preg_match('~^[0-9A-Za-z=_+\-/][0-9A-Za-z=_\'+\-/\.]*@[\w\-]+(\.[\w\-]+)*(\.[\w]{2,6})$~', stripslashes($_POST['email'])) == 0)
          $post_errors[] = 'bad_email';
      }

      // Now make sure this email address is not banned from posting.
      isBannedEmail($_POST['email'], 'cannot_post', sprintf($txt['you_are_post_banned'], $txt[28]));
    }
  }
  // Check the subject and message.
  if (!empty($modSettings['minWordLen']) && ((int)$modSettings['minWordLen'] != 0))
  {
    $Temp = trim(preg_replace('~[^a-z0-9 ]~si', '', $_POST['message']));
    $Temp = preg_replace('~(( )+)~si', ' ', $Temp);
    $WordArr = explode(' ', $Temp);
    if (count($WordArr) < (int)$modSettings['minWordLen'])
    $post_errors[] = 'minWordLen';
  }

  if (!empty($modSettings['minChar']) && ((int)$modSettings['minChar'] != 0))
  {
    if (strlen($_POST['message']) < (int)$modSettings['minChar'])
      $post_errors[] = 'minChar';
  }
  if (!isset($_POST['subject']) || $func['htmltrim']($_POST['subject']) === '')
    $post_errors[] = 'no_subject';
  if (!isset($_POST['message']) || $func['htmltrim']($_POST['message']) === '')
    $post_errors[] = 'no_message';
  elseif (!empty($modSettings['max_messageLength']) && $func['strlen']($_POST['message']) > $modSettings['max_messageLength'])
    $post_errors[] = 'long_message';
  else
  {
    // Prepare the message a bit for some additional testing.
    $_POST['message'] = $func['htmlspecialchars']($_POST['message'], ENT_QUOTES);

    // Preparse code. (Zef)
    if ($user_info['is_guest'])
      $user_info['name'] = $_POST['guestname'];
    preparsecode($_POST['message']);

    // Let's see if there's still some content left without the tags.
    if ($func['htmltrim'](strip_tags(parse_bbc($_POST['message'], false), '<img>')) === '')
      $post_errors[] = 'no_message';
  }

  // You are not!
  if (isset($_POST['message']) && strtolower($_POST['message']) == 'i am the administrator.' && !$user_info['is_admin'])
    fatal_error('Knave! Masquerader! Charlatan!', false);

  if ($posterIsGuest)
  {
    // If user is a guest, make sure the chosen name isn't taken.
    require_once($sourcedir . '/Subs-Members.php');
    if (isReservedName($_POST['guestname'], 0, true, false) && (!isset($row['posterName']) || $_POST['guestname'] != $row['posterName']))
      $post_errors[] = 'bad_name';
  }
  // If the user isn't a guest, get his or her name and email.
  elseif (!isset($_REQUEST['msg']))
  {
    $_POST['guestname'] = addslashes($user_info['username']);
    $_POST['email'] = addslashes($user_info['email']);
  }

  // Any mistakes?
  if (!empty($post_errors))
  {
    loadLanguage('Errors');
    // Previewing.
    $_REQUEST['preview'] = true;

    $context['post_error'] = array('messages' => array());
    foreach ($post_errors as $post_error)
    {
      $context['post_error'][$post_error] = true;
      $context['post_error']['messages'][] = $txt['error_' . $post_error];
    }

    return Post();
  }

  // Make sure the user isn't spamming the board.
  if (!isset($_REQUEST['msg']))
    spamProtection('spam');

  // At about this point, we're posting and that's that.
  ignore_user_abort(true);
  @set_time_limit(300);

  // Add special html entities to the subject, name, and email.
  $_POST['subject'] = strtr($func['htmlspecialchars']($_POST['subject']), array("\r" => '', "\n" => '', "\t" => ''));
  $_POST['guestname'] = htmlspecialchars($_POST['guestname']);
  $_POST['email'] = htmlspecialchars($_POST['email']);

  // At this point, we want to make sure the subject isn't too long.
  if ($func['strlen']($_POST['subject']) > 100)
    $_POST['subject'] = addslashes($func['substr'](stripslashes($_POST['subject']), 0, 100));

  // Creating a new topic?
  $newTopic = empty($_REQUEST['msg']) && empty($topic);

  // Collect all parameters for the creation or modification of a post.
  $msgOptions = array(
    'id' => empty($_REQUEST['msg']) ? 0 : (int) $_REQUEST['msg'],
    'subject' => $_POST['subject'],
    'body' => $_POST['message'],
    'icon' => preg_replace('~[\./\\\\*\':"<>]~', '', $_POST['icon']),
    'smileys_enabled' => !isset($_POST['ns']),
    'hiddenOption' => (empty($_POST['hiddenOption']) ? 0 : $_POST['hiddenOption']),
    'hiddenValue' => (empty($_POST['hiddenValue']) ? 0 : $_POST['hiddenValue']),
  );
  $topicOptions = array(
    'id' => empty($topic) ? 0 : $topic,
    'board' => $board,
    'lock_mode' => isset($_POST['lock']) ? (int) $_POST['lock'] : null,
    'sticky_mode' => isset($_POST['sticky']) && !empty($modSettings['enableStickyTopics']) ? (int) $_POST['sticky'] : null,
    'mark_as_read' => true,
  );
  $posterOptions = array(
    'id' => $ID_MEMBER,
    'name' => $_POST['guestname'],
    'email' => $_POST['email'],
    'update_post_count' => !$user_info['is_guest'] && !isset($_REQUEST['msg']) && $board_info['posts_count'],
  );

  // This is an already existing message. Edit it.
  if (!empty($_REQUEST['msg']))
  {
    // Have admins allowed people to hide their screwups?
    if (time() - $row['posterTime'] > $modSettings['edit_wait_time'] || $ID_MEMBER != $row['ID_MEMBER'])
    {
      $msgOptions['modify_time'] = time();
      $msgOptions['modify_name'] = addslashes($user_info['name']);
    }

    modifyPost($msgOptions, $topicOptions, $posterOptions);
  }
  // This is a new topic or an already existing one. Save it.
  else
  {
    createPost($msgOptions, $topicOptions, $posterOptions);

  $result_shop = db_query("
      SELECT countMoney
      FROM {$db_prefix}boards
      WHERE ID_BOARD = $board
      LIMIT 1", __FILE__, __LINE__);
    $row_shop = mysqli_fetch_array($result_shop, MYSQL_ASSOC);
    
    if (isset($row_shop['countMoney']) && $row_shop['countMoney'] == "1") {
      if ($newTopic)
        $points = $modSettings['shopPointsPerTopic'];
      else
        $points = $modSettings['shopPointsPerPost'];
    
      $plaintext = preg_replace('[\[(.*?)\]]', ' ', $_POST['message']);
      $plaintext = str_replace(array('<br />', "\r", "\n"), ' ', $plaintext);
      $plaintext = preg_replace('/\s+/', ' ', $plaintext);
      
      $points += ($modSettings['shopPointsPerWord'] * str_word_count($plaintext));
      $points += ($modSettings['shopPointsPerChar'] * strlen($plaintext));
      
      if (isset($modSettings['shopPointsLimit']) && $modSettings['shopPointsLimit'] != 0 && $points > $modSettings['shopPointsLimit'])
        $points = $modSettings['shopPointsLimit'];
      
      $result_shop = db_query("
        UPDATE {$db_prefix}members
         SET money = money + {$points}
         WHERE ID_MEMBER = {$ID_MEMBER}
         LIMIT 1", __FILE__, __LINE__);
    }
    if (isset($topicOptions['id']))
      $topic = $topicOptions['id'];
  }

  if(isset($_REQUEST['tags']))
  {
    //Get how many tags there have been for the topic
    $dbresult = db_query("SELECT COUNT(*) as total FROM {$db_prefix}tags_log WHERE ID_TOPIC = " . $topic, __FILE__, __LINE__);
    $row = mysqli_fetch_assoc($dbresult);
    $totaltags = $row['total'];
    mysqli_free_result($dbresult);

    //Check Tag restrictions
    $tags = explode(',',htmlspecialchars($_REQUEST['tags'],ENT_QUOTES));

    if($totaltags < $modSettings['smftags_set_maxtags'])
    {
      $tagcount = 0;
      foreach($tags as $tag)
      {
        if($tagcount >= $modSettings['smftags_set_maxtags'])
          continue;


        if(empty($tag))
          continue;

        if(strlen($tag) < $modSettings['smftags_set_mintaglength'])
          continue;
        if(strlen($tag) > $modSettings['smftags_set_maxtaglength'])
          continue;

        $dbresult = db_query("SELECT ID_TAG FROM {$db_prefix}tags WHERE tag = '$tag'", __FILE__, __LINE__);
        if(db_affected_rows() == 0)
        {
          db_query("INSERT INTO {$db_prefix}tags
            (tag, approved)
          VALUES ('$tag',1)", __FILE__, __LINE__);
          $ID_TAG = db_insert_id();
          //Insert into Tags log
          db_query("INSERT INTO {$db_prefix}tags_log
            (ID_TAG,ID_TOPIC, ID_MEMBER)
          VALUES ($ID_TAG,$topic,$ID_MEMBER)", __FILE__, __LINE__);

          $tagcount++;
        }
        else 
        {
          $row = mysqli_fetch_assoc($dbresult);
          $ID_TAG = $row['ID_TAG'];
          $dbresult2= db_query("SELECT ID FROM {$db_prefix}tags_log WHERE ID_TAG  =  $ID_TAG  AND ID_TOPIC = $topic", __FILE__, __LINE__);
          if(db_affected_rows() != 0)
          {
            continue;

          }
          mysqli_free_result($dbresult2);
          db_query("INSERT INTO {$db_prefix}tags_log
            (ID_TAG,ID_TOPIC, ID_MEMBER)
          VALUES ($ID_TAG,$topic,$ID_MEMBER)", __FILE__, __LINE__);
          $tagcount++;

        }
        mysqli_free_result($dbresult);
      }
    }
  }

  
  if (!$user_info['is_guest'])
  {
    if (!empty($board_info['parent_boards']))
    {
      db_query("
        UPDATE {$db_prefix}log_boards
        SET ID_MSG = $modSettings[maxMsgID]
        WHERE ID_MEMBER = $ID_MEMBER
          AND ID_BOARD IN (" . implode(',', array_keys($board_info['parent_boards'])) . ")", __FILE__, __LINE__);
    }
  }

  if (!empty($_POST['notify']))
  {
    if (allowedTo('mark_any_notify'))
      db_query("
        INSERT IGNORE INTO {$db_prefix}log_notify
          (ID_MEMBER, ID_TOPIC, ID_BOARD)
        VALUES ($ID_MEMBER, $topic, 0)", __FILE__, __LINE__);
  }
  elseif (!$newTopic)
    db_query("
      DELETE FROM {$db_prefix}log_notify
      WHERE ID_MEMBER = $ID_MEMBER
        AND ID_TOPIC = $topic
      LIMIT 1", __FILE__, __LINE__);

  // Log an act of moderation - modifying.
  if (!empty($moderationAction))
    logAction('modify', array('topic' => $topic, 'message' => (int) $_REQUEST['msg'], 'member' => $row['ID_MEMBER']));

  if (isset($_POST['lock']) && $_POST['lock'] != 2)
    logAction('lock', array('topic' => $topicOptions['id']));

  if (isset($_POST['sticky']) && !empty($modSettings['enableStickyTopics']))
    logAction('sticky', array('topic' => $topicOptions['id']));


  // Notify any members who have notification turned on for this topic.
  if ($newTopic)
    notifyMembersBoard();
  elseif (empty($_REQUEST['msg']))
    sendNotifications($topic, 'reply');

  // Returning to the topic?
  if (!empty($_REQUEST['goback']))
  {
    db_query("
      UPDATE {$db_prefix}log_boards
      SET ID_MSG = $modSettings[maxMsgID]
      WHERE ID_MEMBER = $ID_MEMBER
        AND ID_BOARD = $board", __FILE__, __LINE__);
  }
  
    if (isset($_REQUEST['xml'])) {
    require_once($sourcedir . '/Display.php');
    $_REQUEST['msg'] = $msgOptions['id'];
    
    call_user_func('Display');
  }
  else {
  if(isset($_REQUEST['msg']))
  redirectexit($boardurl . '/post-agregado/' . $topic);
  
  if (!empty($_POST['move']) && allowedTo('move_any'))
    redirectexit($boardurl);

  if (isset($_REQUEST['msg']) && !empty($_REQUEST['goback']))
    redirectexit(''. $scripturl .'');
  elseif (!empty($_REQUEST['goback']))
    redirectexit($boardurl . '/post-agregado/' . $topic);

  else
    redirectexit($boardurl . '/post-agregado/' . $topic);
    
}
}

function AnnounceTopic()
{
  global $context, $txt;

  isAllowedTo('announce_topic');

  validateSession();

  loadLanguage('Post');
  loadTemplate('Post');

  $subActions = array(
    'selectgroup' => 'AnnouncementSelectMembergroup',
    'send' => 'AnnouncementSend',
  );

  $context['page_title'] = $txt['announce_topic'];

  // Call the function based on the sub-action.
  $subActions[isset($_REQUEST['sa']) && isset($subActions[$_REQUEST['sa']]) ? $_REQUEST['sa'] : 'selectgroup']();
}

function AnnouncementSelectMembergroup()
{
  global $db_prefix, $txt, $context, $topic, $board, $board_info;

  $groups = array_merge($board_info['groups'], array(1));
  foreach ($groups as $id => $group)
    $groups[$id] = (int) $group;

  $context['groups'] = array();
  if (in_array(0, $groups))
  {
    $context['groups'][0] = array(
      'id' => 0,
      'name' => $txt['announce_regular_members'],
      'member_count' => 'n/a',
    );
  }

  // Get all membergroups that have access to the board the announcement was made on.
  $request = db_query("
    SELECT mg.ID_GROUP, mg.groupName, COUNT(mem.ID_MEMBER) AS num_members
    FROM {$db_prefix}membergroups AS mg
      LEFT JOIN {$db_prefix}members AS mem ON (mem.ID_GROUP = mg.ID_GROUP OR FIND_IN_SET(mg.ID_GROUP, mem.additionalGroups) OR mg.ID_GROUP = mem.ID_POST_GROUP)
    WHERE mg.ID_GROUP IN (" . implode(', ', $groups) . ")
    GROUP BY mg.ID_GROUP
    ORDER BY mg.minPosts, IF(mg.ID_GROUP < 4, mg.ID_GROUP, 4), mg.groupName", __FILE__, __LINE__);
  while ($row = mysqli_fetch_assoc($request))
  {
    $context['groups'][$row['ID_GROUP']] = array(
      'id' => $row['ID_GROUP'],
      'name' => $row['groupName'],
      'member_count' => $row['num_members'],
    );
  }
  mysqli_free_result($request);

  // Get the subject of the topic we're about to announce.
  $request = db_query("
    SELECT m.subject
    FROM ({$db_prefix}messages AS m, {$db_prefix}topics AS t)
    WHERE t.ID_TOPIC = $topic
      AND m.ID_MSG = t.ID_FIRST_MSG", __FILE__, __LINE__);
  list ($context['topic_subject']) = mysqli_fetch_row($request);
  mysqli_free_result($request);

  censorText($context['announce_topic']['subject']);

  $context['move'] = isset($_REQUEST['move']) ? 1 : 0;
  $context['go_back'] = isset($_REQUEST['goback']) ? 1 : 0;

  $context['sub_template'] = 'announce';
}

function AnnouncementSend()
{
  global $db_prefix, $topic, $board, $board_info, $context, $modSettings;
  global $language, $scripturl, $txt, $ID_MEMBER, $sourcedir;

  checkSession();

  // !!! Might need an interface?
  $chunkSize = 50;
  $context['start'] = empty($_REQUEST['start']) ? 0 : (int) $_REQUEST['start'];
  $groups = array_merge($board_info['groups'], array(1));

  if (!empty($_POST['membergroups']))
    $_POST['who'] = explode(',', $_POST['membergroups']);

  // Check whether at least one membergroup was selected.
  if (empty($_POST['who']))
    fatal_lang_error('no_membergroup_selected');

  // Make sure all membergroups are integers and can access the board of the announcement.
  foreach ($_POST['who'] as $id => $mg)
    $_POST['who'][$id] = in_array((int) $mg, $groups) ? (int) $mg : 0;

  // Get the topic subject and censor it.
  $request = db_query("
    SELECT m.ID_MSG, m.subject, m.body
    FROM ({$db_prefix}messages AS m, {$db_prefix}topics AS t)
    WHERE t.ID_TOPIC = $topic
      AND m.ID_MSG = t.ID_FIRST_MSG", __FILE__, __LINE__);
  list ($ID_MSG, $context['topic_subject'], $message) = mysqli_fetch_row($request);
  mysqli_free_result($request);

  censorText($context['topic_subject']);
  censorText($message);

  $message = trim(un_htmlspecialchars(strip_tags(strtr(parse_bbc($message, false, $ID_MSG), array('<br />' => "\n", '</div>' => "\n", '</li>' => "\n", '&#91;' => '[', '&#93;' => ']')))));

  // We need this in order to be able send emails.
  require_once($sourcedir . '/Subs-Post.php');

  // Select the email addresses for this batch.
  $request = db_query("
    SELECT mem.ID_MEMBER, mem.emailAddress, mem.lngfile
    FROM {$db_prefix}members AS mem
    WHERE mem.ID_MEMBER != $ID_MEMBER" . (!empty($modSettings['allow_disableAnnounce']) ? '
      AND mem.notifyAnnouncements = 1' : '') . "
      AND mem.is_activated = 1
      AND (mem.ID_GROUP IN (" . implode(', ', $_POST['who']) . ") OR mem.ID_POST_GROUP IN (" . implode(', ', $_POST['who']) . ") OR FIND_IN_SET(" . implode(", mem.additionalGroups) OR FIND_IN_SET(", $_POST['who']) . ", mem.additionalGroups))
      AND mem.ID_MEMBER > $context[start]
    ORDER BY mem.ID_MEMBER
    LIMIT $chunkSize", __FILE__, __LINE__);

  // All members have received a mail. Go to the next screen.
  if (mysqli_num_rows($request) == 0)
  {
    if (!empty($_REQUEST['move']) && allowedTo('move_any'))
      redirectexit('action=movetopic;topic=' . $topic . '.0' . (empty($_REQUEST['goback']) ? '' : ';goback'));
    elseif (!empty($_REQUEST['goback']))
      redirectexit('topic=' . $topic . '.new;boardseen#new', $context['browser']['is_ie']);
    else
      redirectexit('board=' . $board . '.0');
  }

  // Loop through all members that'll receive an announcement in this batch.
  while ($row = mysqli_fetch_assoc($request))
  {
    $cur_language = empty($row['lngfile']) || empty($modSettings['userLanguage']) ? $language : $row['lngfile'];

    // If the language wasn't defined yet, load it and compose a notification message.
    if (!isset($announcements[$cur_language]))
    {
      loadLanguage('Post', $cur_language, false);

      $announcements[$cur_language] = array(
        'subject' => $txt['notifyXAnn2'] . ': ' . $context['topic_subject'],
        'body' => $message . "\n\n" . $txt['notifyXAnn3'] . "\n\n" . $scripturl . '?topic=' . $topic . ".0\n\n" . $txt[130],
        'recipients' => array(),
      );
    }

    $announcements[$cur_language]['recipients'][$row['ID_MEMBER']] = $row['emailAddress'];
    $context['start'] = $row['ID_MEMBER'];
  }
  mysqli_free_result($request);

  // For each language send a different mail.
  foreach ($announcements as $lang => $mail)
    sendmail($mail['recipients'], $mail['subject'], $mail['body']);

  $context['percentage_done'] = round(100 * $context['start'] / $modSettings['latestMember'], 1);

  $context['move'] = empty($_REQUEST['move']) ? 0 : 1;
  $context['go_back'] = empty($_REQUEST['goback']) ? 0 : 1;
  $context['membergroups'] = implode(',', $_POST['who']);
  $context['sub_template'] = 'announcement_send';

  // Go back to the correct language for the user ;).
  if (!empty($modSettings['userLanguage']))
    loadLanguage('Post');
}

// Notify members of a new post.
function notifyMembersBoard()
{
  global $board, $topic, $txt, $scripturl, $db_prefix, $language, $user_info;
  global $ID_MEMBER, $modSettings, $sourcedir;

  // Can't do it if there's no board. (won't happen but let's check for safety and not sending a zillion email's sake.)
  if (empty($board))
    trigger_error('notifyMembersBoard(): Can\'t send a notification without a board id!', E_USER_NOTICE);

  require_once($sourcedir . '/Subs-Post.php');

  $message = stripslashes($_POST['message']);

  // Censor the subject and body...
  censorText($_POST['subject']);
  censorText($message);

  $_POST['subject'] = un_htmlspecialchars($_POST['subject']);
  $message = trim(un_htmlspecialchars(strip_tags(strtr(parse_bbc($message, false), array('<br />' => "\n", '</div>' => "\n", '</li>' => "\n", '&#91;' => '[', '&#93;' => ']')))));

  // Find the members with notification on for this board.
  $members = db_query("
    SELECT
      mem.ID_MEMBER, mem.emailAddress, mem.notifyOnce, mem.notifySendBody, mem.lngfile,
      ln.sent, mem.ID_GROUP, mem.additionalGroups, b.memberGroups, mem.ID_POST_GROUP
    FROM ({$db_prefix}log_notify AS ln, {$db_prefix}members AS mem, {$db_prefix}boards AS b)
    WHERE ln.ID_BOARD = $board
      AND b.ID_BOARD = $board
      AND mem.ID_MEMBER != $ID_MEMBER
      AND mem.is_activated = 1
      AND mem.notifyTypes != 4
      AND ln.ID_MEMBER = mem.ID_MEMBER
    GROUP BY mem.ID_MEMBER
    ORDER BY mem.lngfile", __FILE__, __LINE__);
  while ($rowmember = mysqli_fetch_assoc($members))
  {
    if ($rowmember['ID_GROUP'] != 1)
    {
      $allowed = explode(',', $rowmember['memberGroups']);
      $rowmember['additionalGroups'] = explode(',', $rowmember['additionalGroups']);
      $rowmember['additionalGroups'][] = $rowmember['ID_GROUP'];
      $rowmember['additionalGroups'][] = $rowmember['ID_POST_GROUP'];

      if (count(array_intersect($allowed, $rowmember['additionalGroups'])) == 0)
        continue;
    }

    loadLanguage('Post', empty($rowmember['lngfile']) || empty($modSettings['userLanguage']) ? $language : $rowmember['lngfile'], false);

    // Setup the string for adding the body to the message, if a user wants it.
    $body_text = empty($modSettings['disallow_sendBody']) ? $txt['notification_new_topic_body'] . "\n\n" . $message . "\n\n" : '';

    $send_subject = sprintf($txt['notify_boards_subject'], $_POST['subject']);

    // Send only if once is off or it's on and it hasn't been sent.
    if (!empty($rowmember['notifyOnce']) && empty($rowmember['sent']))
      sendmail($rowmember['emailAddress'], $send_subject,
        sprintf($txt['notify_boards'], $_POST['subject'], $scripturl . '?topic=' . $topic . '.new#new', un_htmlspecialchars($user_info['name'])) .
        $txt['notify_boards_once'] . "\n\n" .
        (!empty($rowmember['notifySendBody']) ? $body_text : '') .
        $txt['notify_boardsUnsubscribe'] . ': ' . $scripturl . '?action=notifyboard;board=' . $board . ".0\n\n" .
        $txt[130], null, 't' . $topic);
    elseif (empty($rowmember['notifyOnce']))
      sendmail($rowmember['emailAddress'], $send_subject,
        sprintf($txt['notify_boards'], $_POST['subject'], $scripturl . '?topic=' . $topic . '.new#new', un_htmlspecialchars($user_info['name'])) .
        (!empty($rowmember['notifySendBody']) ? $body_text : '') .
        $txt['notify_boardsUnsubscribe'] . ': ' . $scripturl . '?action=notifyboard;board=' . $board . ".0\n\n" .
        $txt[130], null, 't' . $topic);
  }
  mysqli_free_result($members);

  // Sent!
  db_query("
    UPDATE {$db_prefix}log_notify
    SET sent = 1
    WHERE ID_BOARD = $board
      AND ID_MEMBER != $ID_MEMBER", __FILE__, __LINE__);
}

// Get the topic for display purposes.
function getTopic()
{
  global $topic, $db_prefix, $modSettings, $context;

  // Calculate the amount of new replies.
  $newReplies = empty($_REQUEST['num_replies']) || $context['num_replies'] <= $_REQUEST['num_replies'] ? 0 : $context['num_replies'] - $_REQUEST['num_replies'];

  if (isset($_REQUEST['xml']))
    $limit = "
    LIMIT " . (empty($newReplies) ? '0' : $newReplies);
  else
    $limit = empty($modSettings['topicSummaryPosts']) ? '' : '
    LIMIT ' . (int) $modSettings['topicSummaryPosts'];

  // If you're modifying, get only those posts before the current one. (otherwise get all.)
  $request = db_query("
    SELECT IFNULL(mem.realName, m.posterName) AS posterName, m.posterTime, m.body, m.smileysEnabled, m.ID_MSG
      ,m.hiddenOption, m.hiddenValue, m.hiddenInfo, m.ID_MEMBER, m.ID_BOARD
    FROM {$db_prefix}messages AS m
      LEFT JOIN {$db_prefix}members AS mem ON (mem.ID_MEMBER = m.ID_MEMBER)
    WHERE m.ID_TOPIC = $topic" . (isset($_REQUEST['msg']) ? "
      AND m.ID_MSG < " . (int) $_REQUEST['msg'] : '') . "
    ORDER BY m.ID_MSG DESC$limit", __FILE__, __LINE__);
  $context['previous_posts'] = array();
  while ($row = mysqli_fetch_assoc($request))
  {
    // Hide the post in preview or not? --- XD
    $row['can_view_post'] = 1;
    if (!empty($modSettings['allow_hiddenPost']) && $row['hiddenOption'] > 0)
    {
      global $sourcedir;
      require_once($sourcedir . '/HidePost.php');
      $row['ID_TOPIC'] = $topic;
      $context['current_message'] = $row;
      $row['body'] = getHiddenMessage();
      $row['can_view_post'] = $context['can_view_post'];
    }
    // Censor, BBC, ...
    censorText($row['body']);
    $row['body'] = parse_bbc($row['body'], $row['smileysEnabled'], $row['ID_MSG']);

    // ...and store.
    $context['previous_posts'][] = array(
      'can_view_post' => $row['can_view_post'],
      'poster' => $row['posterName'],
      'message' => $row['body'],
      'time' => timeformat($row['posterTime']),
      'timestamp' => forum_time(true, $row['posterTime']),
      'id' => $row['ID_MSG'],
      'is_new' => !empty($newReplies),
    );

    if (!empty($newReplies))
      $newReplies--;
  }
  mysqli_free_result($request);
}

function QuoteFast()
{
  global $db_prefix, $modSettings, $user_info, $txt, $settings, $context;
  global $sourcedir, $func;

  loadLanguage('Post');
  if (!isset($_REQUEST['xml']))
    loadTemplate('Post');

  checkSession('get');

  include_once($sourcedir . '/Subs-Post.php');

  $moderate_boards = boardsAllowedTo('moderate_board');

  $request = db_query("
    SELECT IFNULL(mem.realName, m.posterName) AS posterName, m.posterTime, m.body, m.ID_TOPIC, m.subject, t.locked
    FROM ({$db_prefix}messages AS m, {$db_prefix}boards AS b, {$db_prefix}topics AS t)
      LEFT JOIN {$db_prefix}members AS mem ON (mem.ID_MEMBER = m.ID_MEMBER)
    WHERE m.ID_MSG = " . (int) $_REQUEST['quote'] . "
      AND b.ID_BOARD = m.ID_BOARD
      AND t.ID_TOPIC = m.ID_TOPIC
      AND $user_info[query_see_board]" . (!isset($_REQUEST['modify']) || (!empty($moderate_boards) && $moderate_boards[0] == 0) ? '' : '
       AND (t.locked = 0' . (empty($moderate_boards) ? '' : ' OR b.ID_BOARD IN (' . implode(', ', $moderate_boards) . ')') . ')') . "
    LIMIT 1", __FILE__, __LINE__);
  $context['close_window'] = mysqli_num_rows($request) == 0;

  $context['sub_template'] = 'quotefast';
  if (mysqli_num_rows($request) != 0)
  {
    $row = mysqli_fetch_assoc($request);
    mysqli_free_result($request);

    // Remove special formatting we don't want anymore.
    $row['body'] = un_preparsecode($row['body']);

    // Censor the message!
    censorText($row['body']);

    $row['body'] = preg_replace('~<br(?: /)?' . '>~i', "\n", $row['body']);

    // Want to modify a single message by double clicking it?
    if (isset($_REQUEST['modify']))
    {
      censorText($row['subject']);

      $context['sub_template'] = 'modifyfast';
      $context['message'] = array(
        'id' => $_REQUEST['quote'],
        'body' => $row['body'],
        'subject' => addcslashes($row['subject'], '"'),
      );
      
      return;
    }

    // Remove any nested quotes.
    if (!empty($modSettings['removeNestedQuotes']))
      $row['body'] = preg_replace(array('~\n?\[quote.*?\].+?\[/quote\]\n?~is', '~^\n~', '~\[/quote\]~'), '', $row['body']);

    // Add a quote string on the front and end.
    $context['quote']['xml'] = '[quote author=' . $row['posterName'] . ' link=topic=' . $row['ID_TOPIC'] . '.msg' . (int) $_REQUEST['quote'] . '#msg' . (int) $_REQUEST['quote'] . ' date=' . $row['posterTime'] . ']' . "\n" . $row['body'] . "\n" . '[/quote]';
    $context['quote']['text'] = strtr(un_htmlspecialchars($context['quote']['xml']), array('\'' => '\\\'', '\\' => '\\\\', "\n" => '\\n', '</script>' => '</\' + \'script>'));
    $context['quote']['xml'] = strtr($context['quote']['xml'], array('&nbsp;' => '&#160;', '<' => '&lt;', '>' => '&gt;'));

    $context['quote']['mozilla'] = strtr($func['htmlspecialchars']($context['quote']['text']), array('&quot;' => '"'));
  }
  // !!! Needs a nicer interface.
  // In case our message has been removed in the meantime.
  elseif (isset($_REQUEST['modify']))
  {
    $context['sub_template'] = 'modifyfast';
    $context['message'] = array(
      'id' => 0,
      'body' => '',
      'subject' => '',
    );
  }
  else
    $context['quote'] = array(
      'xml' => '',
      'mozilla' => '',
      'text' => '',
    );
}

function JavaScriptModify()
{
  global $db_prefix, $sourcedir, $modSettings, $board, $topic, $txt;
  global $user_info, $ID_MEMBER, $context, $func, $language;

  // We have to have a topic!
  if (empty($topic))
    obExit(false);

  checkSession('get');
  require_once($sourcedir . '/Subs-Post.php');

  // Assume the first message if no message ID was given.
  $request = db_query("
      SELECT 
        t.locked, t.numReplies, t.ID_MEMBER_STARTED, t.ID_FIRST_MSG,
        m.ID_MSG, m.ID_MEMBER, m.posterTime, m.subject, m.smileysEnabled, m.body,
        m.modifiedTime, m.modifiedName
      FROM ({$db_prefix}messages AS m, {$db_prefix}topics AS t)
      WHERE m.ID_MSG = " . (empty($_REQUEST['msg']) ? 't.ID_FIRST_MSG' : (int) $_REQUEST['msg']) . "
        AND m.ID_TOPIC = $topic
        AND t.ID_TOPIC = $topic", __FILE__, __LINE__);
  if (mysqli_num_rows($request) == 0)
    fatal_lang_error('smf232', false);
  $row = mysqli_fetch_assoc($request);
  mysqli_free_result($request);

  // Change either body or subject requires permissions to modify messages.
  if (isset($_POST['message']) || isset($_POST['subject']) || isset($_POST['icon']))
  {
    if (!empty($row['locked']))
      isAllowedTo('moderate_board');

    if ($row['ID_MEMBER'] == $ID_MEMBER && !allowedTo('modify_any'))
    {
      if (!empty($modSettings['edit_disable_time']) && $row['posterTime'] + ($modSettings['edit_disable_time'] + 5) * 60 < time())
        fatal_lang_error('modify_post_time_passed', false);
      elseif ($row['ID_MEMBER_STARTED'] == $ID_MEMBER && !allowedTo('modify_own'))
        isAllowedTo('modify_replies');
      else
        isAllowedTo('modify_own');
    }
    // Otherwise, they're locked out; someone who can modify the replies is needed.
    elseif ($row['ID_MEMBER_STARTED'] == $ID_MEMBER && !allowedTo('modify_any'))
      isAllowedTo('modify_replies');
    else
      isAllowedTo('modify_any');

    // Only log this action if it wasn't your message.
    $moderationAction = $row['ID_MEMBER'] != $ID_MEMBER;
  }

  $post_errors = array();
  if (isset($_POST['subject']) && $func['htmltrim']($_POST['subject']) !== '')
  {
    $_POST['subject'] = strtr($func['htmlspecialchars']($_POST['subject']), array("\r" => '', "\n" => '', "\t" => ''));

    // Maximum number of characters.
    if ($func['strlen']($_POST['subject']) > 100)
      $_POST['subject'] = addslashes($func['substr'](stripslashes($_POST['subject']), 0, 100));
  }
  else
  {
    $post_errors[] = 'no_subject';
    unset($_POST['subject']);
  }

  if (isset($_POST['message']))
  {
    if ($func['htmltrim']($_POST['message']) === '')
    {
      $post_errors[] = 'no_message';
      unset($_POST['message']);
    }
    elseif (!empty($modSettings['max_messageLength']) && $func['strlen']($_POST['message']) > $modSettings['max_messageLength'])
    {
      $post_errors[] = 'long_message';
      unset($_POST['message']);
    }
    else
    {
      $_POST['message'] = $func['htmlspecialchars']($_POST['message'], ENT_QUOTES);

      preparsecode($_POST['message']);

      if ($func['htmltrim'](strip_tags(parse_bbc($_POST['message'], false), '<img>')) === '')
      {
        $post_errors[] = 'no_message';
        unset($_POST['message']);
      }
    }
  }

  if (isset($_POST['lock']))
  {
    if (!allowedTo(array('lock_any', 'lock_own')) || (!allowedTo('lock_any') && $ID_MEMBER != $row['ID_MEMBER']))
      unset($_POST['lock']);
    elseif (!allowedTo('lock_any'))
    {
      if ($row['locked'] == 1)
        unset($_POST['lock']);
      else
        $_POST['lock'] = empty($_POST['lock']) ? 0 : 2;
    }
    elseif (!empty($row['locked']) && !empty($_POST['lock']) || $_POST['lock'] == $row['locked'])
      unset($_POST['lock']);
    else
      $_POST['lock'] = empty($_POST['lock']) ? 0 : 1;
  }

  if (isset($_POST['sticky']) && !allowedTo('make_sticky'))
    unset($_POST['sticky']);


  if (empty($post_errors))
  {
    $msgOptions = array(
      'id' => $row['ID_MSG'],
      'subject' => isset($_POST['subject']) ? $_POST['subject'] : null,
      'body' => isset($_POST['message']) ? $_POST['message'] : null,
      'icon' => isset($_POST['icon']) ? preg_replace('~[\./\\\\*\':"<>]~', '', $_POST['icon']) : null,
    );
    $topicOptions = array(
      'id' => $topic,
      'board' => $board,
      'lock_mode' => isset($_POST['lock']) ? (int) $_POST['lock'] : null,
      'sticky_mode' => isset($_POST['sticky']) && !empty($modSettings['enableStickyTopics']) ? (int) $_POST['sticky'] : null,
      'mark_as_read' => true,
    );
    $posterOptions = array();

    // Only consider marking as editing if they have edited the subject, message or icon.
    if ((isset($_POST['subject']) && $_POST['subject'] != $row['subject']) || (isset($_POST['message']) && $_POST['message'] != $row['body']) || (isset($_POST['icon']) && $_POST['icon'] != $row['icon']))
    {
      // And even then only if the time has passed...
      if (time() - $row['posterTime'] > $modSettings['edit_wait_time'] || $ID_MEMBER != $row['ID_MEMBER'])
      {
        $msgOptions['modify_time'] = time();
        $msgOptions['modify_name'] = addslashes($user_info['name']);
      }
    }

    modifyPost($msgOptions, $topicOptions, $posterOptions);

    // If we didn't change anything this time but had before put back the old info.
    if (!isset($msgOptions['modify_time']) && !empty($row['modifiedTime']))
    {
      $msgOptions['modify_time'] = $row['modifiedTime'];
      $msgOptions['modify_name'] = $row['modifiedName'];
    }

    // Changing the first subject updates other subjects to 'Re: new_subject'.
    if (isset($_POST['subject']) && isset($_REQUEST['change_all_subjects']) && $row['ID_FIRST_MSG'] == $row['ID_MSG'] && !empty($row['numReplies']) && (allowedTo('modify_any') || ($row['ID_MEMBER_STARTED'] == $ID_MEMBER && allowedTo('modify_replies'))))
    {
      // Get the proper (default language) response prefix first.
      if (!isset($context['response_prefix']) && !($context['response_prefix'] = cache_get_data('response_prefix')))
      {
        if ($language === $user_info['language'])
          $context['response_prefix'] = $txt['response_prefix'];
        else
        {
          loadLanguage('index', $language, false);
          $context['response_prefix'] = $txt['response_prefix'];
          loadLanguage('index');
        }
        cache_put_data('response_prefix', $context['response_prefix'], 600);
      }

      db_query("
        UPDATE {$db_prefix}messages
        SET subject = '$context[response_prefix]$_POST[subject]'
        WHERE ID_TOPIC = $topic
          AND ID_MSG != $row[ID_FIRST_MSG]
        LIMIT $row[numReplies]", __FILE__, __LINE__);
    }

    if ($moderationAction)
      logAction('modify', array('topic' => $topic, 'message' => $row['ID_MSG'], 'member' => $row['ID_MEMBER_STARTED']));
  }

  if (isset($_REQUEST['xml']))
  {
    $context['sub_template'] = 'modifydone';
    if (empty($post_errors) && isset($msgOptions['subject']) && isset($msgOptions['body']))
    {
      $context['message'] = array(
        'id' => $row['ID_MSG'],
        'modified' => array(
          'time' => isset($msgOptions['modify_time']) ? timeformat($msgOptions['modify_time']) : '',
          'timestamp' => isset($msgOptions['modify_time']) ? forum_time(true, $msgOptions['modify_time']) : 0,
          'name' => isset($msgOptions['modify_time']) ? stripslashes($msgOptions['modify_name']) : '',
        ),
        'subject' => stripslashes($msgOptions['subject']),
        'first_in_topic' => $row['ID_MSG'] == $row['ID_FIRST_MSG'],
        'body' => strtr(stripslashes($msgOptions['body']), array(']]>' => ']]]]><![CDATA[>')),
      );

      censorText($context['message']['subject']);
      censorText($context['message']['body']);

      $context['message']['body'] = parse_bbc($context['message']['body'], $row['smileysEnabled'], $row['ID_MSG']);
    }
    // Topic?
    elseif (empty($post_errors) && isset($msgOptions['subject']))
    {
      $context['sub_template'] = 'modifytopicdone';
      $context['message'] = array(
        'id' => $row['ID_MSG'],
        'modified' => array(
          'time' => isset($msgOptions['modify_time']) ? timeformat($msgOptions['modify_time']) : '',
          'timestamp' => isset($msgOptions['modify_time']) ? forum_time(true, $msgOptions['modify_time']) : 0,
          'name' => isset($msgOptions['modify_time']) ? stripslashes($msgOptions['modify_name']) : '',
        ),
        'subject' => stripslashes($msgOptions['subject']),
      );

      censorText($context['message']['subject']);
    }
    else
    {
      $context['message'] = array(
        'id' => $row['ID_MSG'],
        'errors' => array(),
        'error_in_subject' => in_array('no_subject', $post_errors),
        'error_in_body' => in_array('no_message', $post_errors) || in_array('long_message', $post_errors),
      );

      loadLanguage('Errors');
      foreach ($post_errors as $post_error)
        $context['message']['errors'][] = $txt['error_' . $post_error];
    }
  }
  else
    obExit(false);
}

?>