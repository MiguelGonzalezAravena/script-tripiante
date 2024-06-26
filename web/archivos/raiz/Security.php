<?php
if (!defined('SMF'))
  die('Hacking attempt...');

function validateSession() {
  global $modSettings, $sourcedir, $user_info, $sc;

  is_not_guest();
  if (!empty($modSettings['securityDisable']) || (!empty($_SESSION['admin_time']) && $_SESSION['admin_time'] + 3600 >= time()))
    return;
  require_once($sourcedir . '/Subs-Auth.php');
  if (isset($_POST['admin_hash_pass']) && strlen($_POST['admin_hash_pass']) == 40)
  {
    checkSession();

    $good_password = false;
    if (isset($modSettings['integrate_verify_password']) && function_exists($modSettings['integrate_verify_password']))
      if (call_user_func($modSettings['integrate_verify_password'], $user_info['username'], $_POST['admin_hash_pass'], true) === true)
        $good_password = true;

    if ($good_password || $_POST['admin_hash_pass'] == sha1($user_info['passwd'] . $sc))
    {
      $_SESSION['admin_time'] = time();
      return;
    }
  }
  // Posting the password... check it.
  if (isset($_POST['admin_pass']))
  {
    checkSession();

    $good_password = false;
    if (isset($modSettings['integrate_verify_password']) && function_exists($modSettings['integrate_verify_password']))
      if (call_user_func($modSettings['integrate_verify_password'], $user_info['username'], $_POST['admin_pass'], false) === true)
        $good_password = true;

    // Password correct?
    if ($good_password || sha1(strtolower($user_info['username']) . $_POST['admin_pass']) == $user_info['passwd'])
    {
      $_SESSION['admin_time'] = time();
      return;
    }
  }

  // Need to type in a password for that, man.
  adminLogin();
}

// Require a user who is logged in. (not a guest.)
function is_not_guest($message = '')
{
  global $user_info, $txt, $context;

  // Luckily, this person isn't a guest.
  if (!$user_info['is_guest'])
    return;

  // People always worry when they see people doing things they aren't actually doing...
  $_GET['action'] = '';
  $_GET['board'] = '';
  $_GET['topic'] = '';
  writeLog(true);

  // Just die.
  if (isset($_REQUEST['xml']))
    obExit(false);

  $_SESSION['login_url'] = $_SERVER['REQUEST_URL'];

  // Load the Login template and language file.
  loadLanguage('Login');
  loadTemplate('Login');

  // Use the kick_guest sub template...
  $context['kick_message'] = $message;
  $context['sub_template'] = 'kick_guest';
  $context['page_title'] = $txt[34];

  obExit();

  // We should never get to this point, but if we did we wouldn't know the user isn't a guest.
  trigger_error('Hacking attempt...', E_USER_ERROR);
}

// Do banning related stuff.  (ie. disallow access....)
function is_not_banned($forceCheck = false)
{
  global $txt, $db_prefix, $ID_MEMBER, $modSettings, $context, $user_info;
  global $sourcedir, $cookiename, $user_settings;

  // You cannot be banned if you are an admin - doesn't help if you log out.
  if ($user_info['is_admin'])
    return;

  // Only check the ban every so often. (to reduce load.)
  if ($forceCheck || !isset($_SESSION['ban']) || empty($modSettings['banLastUpdated']) || ($_SESSION['ban']['last_checked'] < $modSettings['banLastUpdated']) || $_SESSION['ban']['ID_MEMBER'] != $ID_MEMBER || $_SESSION['ban']['ip'] != $user_info['ip'] || $_SESSION['ban']['ip2'] != $user_info['ip2'] || (isset($user_info['email'], $_SESSION['ban']['email']) && $_SESSION['ban']['email'] != $user_info['email']))
  {
    // Innocent until proven guilty.  (but we know you are! :P)
    $_SESSION['ban'] = array(
      'last_checked' => time(),
      'ID_MEMBER' => $ID_MEMBER,
      'ip' => $user_info['ip'],
      'ip2' => $user_info['ip2'],
      'email' => $user_info['email'],
    );

    $ban_query = array();
    $flag_is_activated = false;

    // Check both IP addresses.
    foreach (array('ip', 'ip2') as $ip_number)
    {
      // Check if we have a valid IP address.
      if (preg_match('/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/', $user_info[$ip_number], $ip_parts) == 1)
      {
        $ban_query[] = "(($ip_parts[1] BETWEEN bi.ip_low1 AND bi.ip_high1)
              AND ($ip_parts[2] BETWEEN bi.ip_low2 AND bi.ip_high2)
              AND ($ip_parts[3] BETWEEN bi.ip_low3 AND bi.ip_high3)
              AND ($ip_parts[4] BETWEEN bi.ip_low4 AND bi.ip_high4))";

        // IP was valid, maybe there's also a hostname...
        if (empty($modSettings['disableHostnameLookup']))
        {
          $hostname = host_from_ip($user_info[$ip_number]);
          if (strlen($hostname) > 0)
            $ban_query[] = "('" . addslashes($hostname) . "' LIKE bi.hostname)";
        }
      }
      // We use '255.255.255.255' for 'unknown' since it's not valid anyway.
      else if ($user_info[$ip_number] == 'unknown')
        $ban_query[] = "(bi.ip_low1 = 255 AND bi.ip_high1 = 255
              AND bi.ip_low2 = 255 AND bi.ip_high2 = 255
              AND bi.ip_low3 = 255 AND bi.ip_high3 = 255
              AND bi.ip_low4 = 255 AND bi.ip_high4 = 255)";
    }

    // Is their email address banned?
    if (strlen($user_info['email']) != 0)
      $ban_query[] = "('" . addslashes($user_info['email']) . "' LIKE bi.email_address)";

    // How about this user?
    if (!$user_info['is_guest'] && !empty($ID_MEMBER))
      $ban_query[] = "bi.ID_MEMBER = $ID_MEMBER";

    // Check the ban, if there's information.
    if (!empty($ban_query))
    {
      $restrictions = array(
        'cannot_access',
        'cannot_login',
        'cannot_post',
        'cannot_register',
      );
      $request = db_query("
        SELECT bi.ID_BAN, bi.email_address, bi.ID_MEMBER, bg.cannot_access, bg.cannot_register,
          bg.cannot_post, bg.cannot_login, bg.reason
        FROM ({$db_prefix}ban_groups AS bg, {$db_prefix}ban_items AS bi)
        WHERE bg.ID_BAN_GROUP = bi.ID_BAN_GROUP
          AND (bg.expire_time IS NULL OR bg.expire_time > " . time() . ")
          AND (" . implode(' OR ', $ban_query) . ')', __FILE__, __LINE__);
      // Store every type of ban that applies to you in your session.
      while ($row = mysqli_fetch_assoc($request))
      {
        foreach ($restrictions as $restriction)
          if (!empty($row[$restriction]))
          {
            $_SESSION['ban'][$restriction]['reason'] = $row['reason'];
            $_SESSION['ban'][$restriction]['ids'][] = $row['ID_BAN'];

            if (!$user_info['is_guest'] && $restriction == 'cannot_access' && ($row['ID_MEMBER'] == $ID_MEMBER || $row['email_address'] == $user_info['email']))
              $flag_is_activated = true;
          }
      }
      mysqli_free_result($request);
    }

    // Mark the cannot_access and cannot_post bans as being 'hit'.
    if (isset($_SESSION['ban']['cannot_access']) || isset($_SESSION['ban']['cannot_post']))
      log_ban(array_merge(isset($_SESSION['ban']['cannot_access']) ? $_SESSION['ban']['cannot_access']['ids'] : array(), isset($_SESSION['ban']['cannot_post']) ? $_SESSION['ban']['cannot_post']['ids'] : array()));

    // If for whatever reason the is_activated flag seems wrong, do a little work to clear it up.
    if ($ID_MEMBER && (($user_settings['is_activated'] >= 10 && !$flag_is_activated)
      || ($user_settings['is_activated'] < 10 && $flag_is_activated)))
    {
      require_once($sourcedir . '/ManageBans.php');
      updateBanMembers();
    }
  }

  // Hey, I know you! You're ehm...
  if (!isset($_SESSION['ban']['cannot_access']) && !empty($_COOKIE[$cookiename . '_']))
  {
    $bans = explode(',', $_COOKIE[$cookiename . '_']);
    foreach ($bans as $key => $value)
      $bans[$key] = (int) $value;
    $request = db_query("
      SELECT bi.ID_BAN, bg.reason
      FROM ({$db_prefix}ban_items AS bi, {$db_prefix}ban_groups AS bg)
      WHERE bg.ID_BAN_GROUP = bi.ID_BAN_GROUP
        AND (bg.expire_time IS NULL OR bg.expire_time > " . time() . ")
        AND bg.cannot_access = 1
        AND bi.ID_BAN IN (" . implode(', ', $bans) . ")
      LIMIT " . count($bans), __FILE__, __LINE__);
    while ($row = mysqli_fetch_assoc($request))
    {
      $_SESSION['ban']['cannot_access']['ids'][] = $row['ID_BAN'];
      $_SESSION['ban']['cannot_access']['reason'] = $row['reason'];
    }
    mysqli_free_result($request);

    // My mistake. Next time better.
    if (!isset($_SESSION['ban']['cannot_access']))
    {
      require_once($sourcedir . '/Subs-Auth.php');
      $cookie_url = url_parts(!empty($modSettings['localCookies']), !empty($modSettings['globalCookies']));
      setcookie($cookiename . '_', '', time() - 3600, $cookie_url[1], $cookie_url[0], 0);
    }
  }

  // If you're fully banned, it's end of the story for you.
  if (isset($_SESSION['ban']['cannot_access']))
  {
    // We don't wanna see you!
    if (!$user_info['is_guest'])
      db_query("
        DELETE FROM {$db_prefix}log_online
        WHERE ID_MEMBER = $ID_MEMBER
        LIMIT 1", __FILE__, __LINE__);

    // 'Log' the user out.  Can't have any funny business... (save the name!)
    $old_name = isset($user_info['name']) && $user_info['name'] != '' ? $user_info['name'] : $txt[28];
    $user_info['name'] = '';
    $user_info['username'] = '';
    $user_info['is_guest'] = true;
    $user_info['is_admin'] = false;
    $user_info['permissions'] = array();
    $ID_MEMBER = 0;
    $context['user'] = array(
      'id' => 0,
      'username' => '',
      'name' => $txt[28],
      'is_guest' => true,
      'is_logged' => false,
      'is_admin' => false,
      'is_mod' => false,
      'language' => $user_info['language']
    );

    // A goodbye present.
    require_once($sourcedir . '/Subs-Auth.php');
    $cookie_url = url_parts(!empty($modSettings['localCookies']), !empty($modSettings['globalCookies']));
    setcookie($cookiename . '_', implode(',', $_SESSION['ban']['cannot_access']['ids']), time() + 3153600, $cookie_url[1], $cookie_url[0], 0);

    // Don't scare anyone, now.
    $_GET['action'] = '';
    $_GET['board'] = '';
    $_GET['topic'] = '';
    writeLog(true);

    // You banned, sucka!
    fatal_error(sprintf($txt[430], $old_name) . (empty($_SESSION['ban']['cannot_access']['reason']) ? '' : '<br />' . $_SESSION['ban']['cannot_access']['reason']));

    // If we get here, something's gone wrong.... but let's try anyway.
    trigger_error('Hacking attempt...', E_USER_ERROR);
  }
  // You're not allowed to log in but yet you are. Let's fix that.
  else if (isset($_SESSION['ban']['cannot_login']) && !$user_info['is_guest'])
  {
    // !!! Why doesn't this use the function made for logging bans?
    db_query("
      UPDATE {$db_prefix}ban_items
      SET hits = hits + 1
      WHERE ID_BAN IN (" . implode(', ', $_SESSION['ban']['cannot_login']['ids']) . ')', __FILE__, __LINE__);

    // Log this ban.
    db_query("
      INSERT INTO {$db_prefix}log_banned
        (ID_MEMBER, ip, email, logTime)
      VALUES ($ID_MEMBER, SUBSTRING('$user_info[ip]', 1, 16), SUBSTRING('$user_info[email]', 1, 255), " . time() . ')', __FILE__, __LINE__);

    $_GET['action'] = '';
    $_GET['board'] = '';
    $_GET['topic'] = '';
    writeLog(true);

    // Logged in, but not for long...
    require_once($sourcedir . '/LogInOut.php');
    Logout(true);
  }

  // Fix up the banning permissions.
  if (isset($user_info['permissions']))
    banPermissions();
}

// Fix permissions according to ban status.
function banPermissions()
{
  global $user_info;

  // Somehow they got here, at least take away all permissions...
  if (isset($_SESSION['ban']['cannot_access']))
    $user_info['permissions'] = array();
  // Okay, well, you can watch, but don't touch a thing.
  else if (isset($_SESSION['ban']['cannot_post']))
  {
    $denied_permissions = array(
      'pm_send',
      'manage_smileys', 'manage_boards', 'admin_forum', 'manage_permissions',
      'moderate_forum', 'manage_membergroups', 'manage_bans', 'send_mail', 'edit_news',
      'profile_identity_any', 'profile_extra_any', 'profile_title_any',
      'post_new', 'post_reply_own', 'post_reply_any',
      'delete_own', 'delete_any', 'delete_replies',
      'make_sticky',
      'merge_any', 'split_any',
      'modify_own', 'modify_any', 'modify_replies',
      'move_any',
      'send_topic',
      'lock_own', 'lock_any',
      'remove_own', 'remove_any',
    );
    $user_info['permissions'] = array_diff($user_info['permissions'], $denied_permissions);
  }
}

// Log a ban in the database.
function log_ban($ban_ids = array(), $email = null)
{
  global $db_prefix, $user_info, $ID_MEMBER;

  // Don't log web accelerators, it's very confusing...
  if (isset($_SERVER['HTTP_X_MOZ']) && $_SERVER['HTTP_X_MOZ'] == 'prefetch')
    return;

  db_query("
    INSERT INTO {$db_prefix}log_banned
      (ID_MEMBER, ip, email, logTime)
    VALUES ($ID_MEMBER, SUBSTRING('$user_info[ip]', 1, 16), '" . ($email === null ? ($user_info['is_guest'] ? '' : $user_info['email']) : $email) . "', " . time() . ')', __FILE__, __LINE__);

  // One extra point for these bans.
  if (!empty($ban_ids))
    db_query("
      UPDATE {$db_prefix}ban_items
      SET hits = hits + 1
      WHERE ID_BAN IN (" . implode(', ', $ban_ids) . ')', __FILE__, __LINE__);
}

// Checks if a given email address might be banned.
function isBannedEmail($email, $restriction, $error)
{
  global $db_prefix, $txt;

  // Can't ban an empty email
  if (empty($email) || trim($email) == '')
    return;

  // Let's start with the bans based on your IP/hostname/memberID...
  $ban_ids = isset($_SESSION['ban'][$restriction]) ? $_SESSION['ban'][$restriction]['ids'] : array();
  $ban_reason = isset($_SESSION['ban'][$restriction]) ? $_SESSION['ban'][$restriction]['reason'] : '';

  // ...and add to that the email address you're trying to register.
  $request = db_query("
    SELECT bi.ID_BAN, bg.$restriction, bg.cannot_access, bg.reason
    FROM ({$db_prefix}ban_items AS bi, {$db_prefix}ban_groups AS bg)
    WHERE bg.ID_BAN_GROUP = bi.ID_BAN_GROUP
      AND '$email' LIKE bi.email_address
      AND (bg.$restriction = 1 OR bg.cannot_access = 1)", __FILE__, __LINE__);
  while ($row = mysqli_fetch_assoc($request))
  {
    if (!empty($row['cannot_access']))
    {
      $_SESSION['ban']['cannot_access']['ids'][] = $row['ID_BAN'];
      $_SESSION['ban']['cannot_access']['reason'] = $row['reason'];
    }
    if (!empty($row[$restriction]))
    {
      $ban_ids[] = $row['ID_BAN'];
      $ban_reason = $row['reason'];
    }
  }
  mysqli_free_result($request);

  // You're in biiig trouble.  Banned for the rest of this session!
  if (isset($_SESSION['ban']['cannot_access']))
  {
    log_ban($_SESSION['ban']['cannot_access']['ids']);
    $_SESSION['ban']['last_checked'] = time();

    fatal_error(sprintf($txt[430], $txt[28]) . $_SESSION['ban']['cannot_access']['reason'], false);
  }

  if (!empty($ban_ids))
  {
    // Log this ban for future reference.
    log_ban($ban_ids, $email);
    fatal_error($error . $ban_reason, false);
  }
}

// Make sure the user's correct session was passed, and they came from here. (type can be post, get, or request.)
function checkSession($type = 'post', $from_action = '', $is_fatal = true) {
  global $sc, $modSettings, $boardurl;

  
  echo '<br />$_POST["sc"]: ' . $_POST['sc'] . '<br />';
  echo '$sc: ' . $sc;
  // $_SESSION['admin_time'] = time();

  if ($type == 'post' && (!isset($_POST['sc']) || $_POST['sc'] != $sc)) {
    $error = 'smf304';
  } else if ($type == 'get' && (!isset($_GET['sesc']) || $_GET['sesc'] != $sc)) {
    $error = 'smf305';
  } else if ($type == 'request' && (!isset($_GET['sesc']) || $_GET['sesc'] != $sc) && (!isset($_POST['sc']) || $_POST['sc'] != $sc)) {
    $error = 'smf305';
  }

  if ((!isset($_SESSION['USER_AGENT']) || $_SESSION['USER_AGENT'] != $_SERVER['HTTP_USER_AGENT']) && empty($modSettings['disableCheckUA'])) {
    $error = 'smf305';
  }
  /**/

  if (isset($_SERVER['HTTP_X_MOZ']) && $_SERVER['HTTP_X_MOZ'] == 'prefetch') {
    ob_end_clean();
    header('HTTP/1.1 403 Forbidden');
    die;
  }

  $referrer = isset($_SERVER['HTTP_REFERER']) ? @parse_url($_SERVER['HTTP_REFERER']) : array();

  if (!empty($referrer['host'])) {
    if (strpos($_SERVER['HTTP_HOST'], ':') !== false) {
      $real_host = substr($_SERVER['HTTP_HOST'], 0, strpos($_SERVER['HTTP_HOST'], ':'));
    } else {
      $real_host = $_SERVER['HTTP_HOST'];
    }

    $parsed_url = parse_url($boardurl);

    if (!empty($modSettings['globalCookies'])) {
      if (preg_match('~(?:[^\.]+\.)?([^\.]{3,}\..+)\z~i', $parsed_url['host'], $parts) == 1) {
        $parsed_url['host'] = $parts[1];
      }

      if (preg_match('~(?:[^\.]+\.)?([^\.]{3,}\..+)\z~i', $referrer['host'], $parts) == 1) {
        $referrer['host'] = $parts[1];
      }

      if (preg_match('~(?:[^\.]+\.)?([^\.]{3,}\..+)\z~i', $real_host, $parts) == 1) {
        $real_host = $parts[1];
      }
    }

    if (isset($parsed_url['host']) && strtolower($referrer['host']) != strtolower($parsed_url['host']) && strtolower($referrer['host']) != strtolower($real_host)) {
      $error = 'smf306';
      $log_error = true;
    }
  }

  // Well, first of all, if a from_action is specified you'd better have an old_url.
  if (!empty($from_action) && (!isset($_SESSION['old_url']) || preg_match('~[?;&]action=' . $from_action . '([;&]|$)~', $_SESSION['old_url']) == 0)) {
    $error = 'smf306';
    $log_error = true;
  }

  if (strtolower($_SERVER['HTTP_USER_AGENT']) == 'hacker') {
    fatal_error('Sound the alarm!  It\'s a hacker!  Close the castle gates!!', false);
  }

  // Everything is ok, return an empty string.
  if (!isset($error)) {
    return '';
  }
  // A session error occurred, show the error.
  else if ($is_fatal) {
    fatal_lang_error($error, isset($log_error));
  // A session error occurred, return the error to the calling function.
  } else {
    return $error;
  }

  // We really should never fall through here, for very important reasons.  Let's make sure.
  trigger_error('Hacking attempt...', E_USER_ERROR);
}

function checkConfirm($action) {
  global $modSettings;
  
  if (isset($_GET['confirm']) && isset($_SESSION['confirm_' . $action]) && md5($_GET['confirm'] . $_SERVER['HTTP_USER_AGENT']) == $_SESSION['confirm_' . $action])
    return true;
    
  else
  {
    $token = md5(mt_rand() . session_id() . (string) microtime() . $modSettings['rand_seed']);
    $_SESSION['confirm_' . $action] = md5($token . $_SERVER['HTTP_USER_AGENT']);
    
    return $token;
  }
}

// Check whether a form has been submitted twice.
function checkSubmitOnce($action, $is_fatal = true)
{
  global $context;

  if (!isset($_SESSION['forms']))
    $_SESSION['forms'] = array();

  // Register a form number and store it in the session stack. (use this on the page that has the form.)
  if ($action == 'register')
  {
    $context['form_sequence_number'] = 0;
    while (empty($context['form_sequence_number']) || in_array($context['form_sequence_number'], $_SESSION['forms']))
      $context['form_sequence_number'] = mt_rand(1, 16000000);
  }
  // Check whether the submitted number can be found in the session.
  else if ($action == 'check')
  {
    if (!isset($_REQUEST['seqnum']))
      return true;
    else if (!in_array($_REQUEST['seqnum'], $_SESSION['forms']))
    {
      $_SESSION['forms'][] = (int) $_REQUEST['seqnum'];
      return true;
    }
    else if ($is_fatal)
      fatal_lang_error('error_form_already_submitted', false);
    else
      return false;
  }
  // Don't check, just free the stack number.
  else if ($action == 'free' && isset($_REQUEST['seqnum']) && in_array($_REQUEST['seqnum'], $_SESSION['forms']))
    $_SESSION['forms'] = array_diff($_SESSION['forms'], array($_REQUEST['seqnum']));
  else if ($action != 'free')
    trigger_error('checkSubmitOnce(): Invalid action \'' . $action . '\'', E_USER_WARNING);
}

// Check the user's permissions.
function allowedTo($permission, $boards = null)
{
  global $user_info, $db_prefix, $modSettings, $ID_MEMBER;

  // You're always allowed to do nothing. (unless you're a working man, MR. LAZY :P!)
  if (empty($permission))
    return true;

  // You're never allowed to do something if your data hasn't been loaded yet!
  if (empty($user_info))
    return false;

  // Administrators are supermen :P.
  if ($user_info['is_admin'])
    return true;

  // Are we checking the _current_ board, or some other boards?
  if ($boards === null)
  {
    // Check if they can do it.
    if (!is_array($permission) && in_array($permission, $user_info['permissions']))
      return true;
    // Search for any of a list of permissions.
    else if (is_array($permission) && count(array_intersect($permission, $user_info['permissions'])) != 0)
      return true;
    // You aren't allowed, by default.
    else
      return false;
  }
  else if (!is_array($boards))
    $boards = array($boards);

  // Determine which permission mode is still acceptable.
  if (empty($modSettings['permission_enable_by_board']) && !in_array('moderate_board', $user_info['permissions']))
  {
    // Make an array of the permission.
    $temp = is_array($permission) ? $permission : array($permission);

    if (in_array('post_reply_own', $temp) || in_array('post_reply_any', $temp))
      $max_allowable_mode = 3;
    else if (in_array('post_new', $temp))
      $max_allowable_mode = 2;
    else if (in_array('poll_post', $temp))
      $max_allowable_mode = 0;
  }

  $request = db_query("
    SELECT MIN(bp.addDeny) AS addDeny
    FROM ({$db_prefix}boards AS b, {$db_prefix}board_permissions AS bp)
      LEFT JOIN {$db_prefix}moderators AS mods ON (mods.ID_BOARD = b.ID_BOARD AND mods.ID_MEMBER = $ID_MEMBER)
    WHERE b.ID_BOARD IN (" . implode(', ', $boards) . ")" . (isset($max_allowable_mode) ? "
      AND b.permission_mode <= $max_allowable_mode" : '') . "
      AND bp.ID_BOARD = " . (empty($modSettings['permission_enable_by_board']) ? '0' : 'if (b.permission_mode = 1, b.ID_BOARD, 0)') . "
      AND bp.ID_GROUP IN (" . implode(', ', $user_info['groups']) . ", 3)
      AND bp.permission " . (is_array($permission) ? "IN ('" . implode("', '", $permission) . "')" : " = '$permission'") . "
      AND (mods.ID_MEMBER IS NOT NULL OR bp.ID_GROUP != 3)
    GROUP BY b.ID_BOARD", __FILE__, __LINE__);

  // Make sure they can do it on all of the boards.
  if (mysqli_num_rows($request) != count($boards))
    return false;

  $result = true;
  while ($row = mysqli_fetch_assoc($request))
    $result &= !empty($row['addDeny']);
  mysqli_free_result($request);

  // If the query returned 1, they can do it... otherwise, they can't.
  return $result;
}

function isAllowedTo($permission, $boards = null)
{
  global $user_info, $txt;

  static $heavy_permissions = array(
    'admin_forum',
    'manage_smileys',
    'manage_boards',
    'edit_news',
    'moderate_forum',
    'manage_bans',
    'manage_membergroups',
    'manage_permissions',
  );

  $permission = is_array($permission) ? $permission : array($permission);
  if (!allowedTo($permission, $boards))
  {
    $error_permission = array_shift($permission);

    if ($user_info['is_guest'])
    {
      loadLanguage('Errors');
      is_not_guest($txt['cannot_' . $error_permission]);
    }

    $_GET['action'] = '';
    $_GET['board'] = '';
    $_GET['topic'] = '';
    writeLog(true);

    fatal_lang_error('cannot_' . $error_permission, false);

    trigger_error('Hacking attempt...', E_USER_ERROR);
  }

  if (!allowedTo(array_diff($permission, $heavy_permissions), $boards))
    validateSession();
}

function boardsAllowedTo($permission)
{
  global $db_prefix, $ID_MEMBER, $user_info, $modSettings;

  if ($user_info['is_admin'])
    return array(0);

  // All groups the user is in except 'moderator'.
  $groups = array_diff($user_info['groups'], array(3));

  // With no local permissions, there might be some other restrictions.
  if (empty($modSettings['permission_enable_by_board']) && !in_array('moderate_board', $user_info['permissions']))
  {
    $needed_level = array(
      'post_reply_own' => 3,
      'post_reply_any' => 3,
      'post_new' => 2,
      'poll_post' => 0,
    );
    if (isset($needed_level[$permission]))
      $max_allowable_mode = $needed_level[$permission];
  }

  $request = db_query("
    SELECT b.ID_BOARD, b.permission_mode, bp.addDeny
    FROM ({$db_prefix}boards AS b, {$db_prefix}board_permissions AS bp)
      LEFT JOIN {$db_prefix}moderators AS mods ON (mods.ID_BOARD = b.ID_BOARD AND mods.ID_MEMBER = $ID_MEMBER)
    WHERE bp.ID_BOARD = " . (empty($modSettings['permission_enable_by_board']) ? '0' : 'if (b.permission_mode = 1, b.ID_BOARD, 0)') . "
      AND bp.ID_GROUP IN (" . implode(', ', $groups) . ", 3)
      AND bp.permission = '$permission'" . (isset($max_allowable_mode) ? "
      AND (mods.ID_MEMBER IS NOT NULL OR b.permission_mode <= $max_allowable_mode)" : '') . "
      AND (mods.ID_MEMBER IS NOT NULL OR bp.ID_GROUP != 3)", __FILE__, __LINE__);
  $boards = array();
  $deny_boards = array();
  while ($row = mysqli_fetch_assoc($request))
  {
    if (empty($row['addDeny']))
      $deny_boards[] = $row['ID_BOARD'];
    else
      $boards[] = $row['ID_BOARD'];
  }
  mysqli_free_result($request);

  $boards = array_values(array_diff($boards, $deny_boards));

  return $boards;
}

function is_admin()
{
  isAllowedTo('admin_forum');
}

?>