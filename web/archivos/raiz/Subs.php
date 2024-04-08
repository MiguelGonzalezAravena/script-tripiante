<?php
if (!defined('SMF'))
  die('Hacking attempt...');

function db_query($db_string, $file, $line) {
  global $db_cache, $db_count, $db_connection, $db_show_debug, $modSettings;

  $db_count = !isset($db_count) ? 1 : $db_count + 1;

  if (isset($db_show_debug) && $db_show_debug === true)   {
    if (!isset($db_cache))
      $db_cache = array();

    if (!empty($_SESSION['debug_redirect'])) {
      $db_cache = array_merge($_SESSION['debug_redirect'], $db_cache);
      $db_count = count($db_cache) + 1;
      $_SESSION['debug_redirect'] = array();
    }

    $db_cache[$db_count]['q'] = $db_string;
    $db_cache[$db_count]['f'] = $file;
    $db_cache[$db_count]['l'] = $line;
    $st = microtime();
  }

  if (empty($modSettings['disableQueryCheck'])) {
    $clean = '';
    $old_pos = 0;
    $pos = -1;

    while (true) {
      $pos = strpos($db_string, '\'', $pos + 1);
      if ($pos === false)
        break;

      $clean .= substr($db_string, $old_pos, $pos - $old_pos);

      while (true) {
        $pos1 = strpos($db_string, '\'', $pos + 1);
        $pos2 = strpos($db_string, '\\', $pos + 1);

        if ($pos1 === false)
          break;
        else if ($pos2 == false || $pos2 > $pos1) {
          $pos = $pos1;
          break;
        }

        $pos = $pos2 + 1;
      }
      $clean .= ' %s ';

      $old_pos = $pos + 1;
    }
    $clean .= substr($db_string, $old_pos);
    $clean = trim(strtolower(preg_replace(array('~\s+~s', '~/\*!40001 SQL_NO_CACHE \*/~', '~/\*!40000 USE INDEX \([A-Za-z\_]+?\) \*/~'), array(' ', '', ''), $clean)));

    if (strpos($clean, 'union') !== false && preg_match('~(^|[^a-z])union($|[^[a-z])~s', $clean) != 0)
      $fail = true;
    // Comments?  We don't use comments in our queries, we leave 'em outside!
    else if (strpos($clean, '/*') > 2 || strpos($clean, '--') !== false || strpos($clean, ';') !== false)
      $fail = true;
    // Trying to change passwords, slow us down, or something?
    else if (strpos($clean, 'sleep') !== false && preg_match('~(^|[^a-z])sleep($|[^[a-z])~s', $clean) != 0)
      $fail = true;
    else if (strpos($clean, 'benchmark') !== false && preg_match('~(^|[^a-z])benchmark($|[^[a-z])~s', $clean) != 0)
      $fail = true;
    // Sub selects?  We don't use those either.
    else if (preg_match('~\([^)]*?select~s', $clean) != 0)
      $fail = true;

    if (!empty($fail)) {
      log_error('Hacking attempt...' . "\n" . $db_string, $file, $line);
      fatal_error('Hacking attempt...', false);
    }
  }

  $ret = mysqli_query($db_string, $db_connection);
  if ($ret === false && $file !== false)
    $ret = db_error($db_string, $file, $line);

  // Debugging.
  if (isset($db_show_debug) && $db_show_debug === true)
    $db_cache[$db_count]['t'] = array_sum(explode(' ', microtime())) - array_sum(explode(' ', $st));

  return $ret;
}

function db_affected_rows() {
  global $db_connection;

  return mysqli_affected_rows($db_connection);
}

function db_insert_id() {
  global $db_connection;

  return mysqli_insert_id($db_connection);
}

// Update some basic statistics...
function updateStats($type, $parameter1 = null, $parameter2 = null) {
  global $db_prefix, $modSettings;

  switch ($type) {
  case 'member':
    $changes = array(
      'memberlist_updated' => time(),
    );

    // Are we using registration approval?
    if (!empty($modSettings['registration_method']) && $modSettings['registration_method'] == 2) {
      // Update the latest activated member (highest ID_MEMBER) and count.
      $result = db_query("
        SELECT COUNT(*), MAX(ID_MEMBER)
        FROM {$db_prefix}members
        WHERE is_activated = 1", __FILE__, __LINE__);

      list($changes['totalMembers'], $changes['latestMember']) = mysqli_fetch_row($result);
      mysqli_free_result($result);

      // Get the latest activated member's display name.
      $result = db_query("
        SELECT realName
        FROM {$db_prefix}members
        WHERE ID_MEMBER = " . (int) $changes['latestMember'] . "
        LIMIT 1", __FILE__, __LINE__);
      list ($changes['latestRealName']) = mysqli_fetch_row($result);
      mysqli_free_result($result);

      // Update the amount of members awaiting approval - ignoring COPPA accounts, as you can't approve them until you get permission.
      $result = db_query("
        SELECT COUNT(*)
        FROM {$db_prefix}members
        WHERE is_activated IN (3, 4)", __FILE__, __LINE__);

      list ($changes['unapprovedMembers']) = mysqli_fetch_row($result);
      mysqli_free_result($result);
    }
    // If $parameter1 is a number, it's the new ID_MEMBER and #2 is the real name for a new registration.
    else if ($parameter1 !== null && $parameter1 !== false) {
      $changes['latestMember'] = $parameter1;
      $changes['latestRealName'] = $parameter2;

      updateSettings(array('totalMembers' => true), true);
    }
    // If $parameter1 is false, and approval is off, we need change nothing.
    else if ($parameter1 !== false) {
      // Update the latest member (highest ID_MEMBER) and count.
      $result = db_query("
        SELECT COUNT(*), MAX(ID_MEMBER)
        FROM {$db_prefix}members", __FILE__, __LINE__);

      list($changes['totalMembers'], $changes['latestMember']) = mysqli_fetch_row($result);
      mysqli_free_result($result);

      // Get the latest member's display name.
      $result = db_query("
        SELECT realName
        FROM {$db_prefix}members
        WHERE ID_MEMBER = " . (int) $changes['latestMember'] . "
        LIMIT 1", __FILE__, __LINE__);

      list ($changes['latestRealName']) = mysqli_fetch_row($result);
      mysqli_free_result($result);
    }

    updateSettings($changes);
    break;

  case 'message':
    if ($parameter1 === true && $parameter2 !== null)
      updateSettings(array('totalMessages' => true, 'maxMsgID' => $parameter2), true);
    else {
      // SUM and MAX on a smaller table is better for InnoDB tables.
      $result = db_query("
        SELECT SUM(numPosts) AS totalMessages, MAX(ID_LAST_MSG) AS maxMsgID
        FROM {$db_prefix}boards", __FILE__, __LINE__);

      $row = mysqli_fetch_assoc($result);
      mysqli_free_result($result);

      updateSettings(array(
        'totalMessages' => $row['totalMessages'],
        'maxMsgID' => $row['maxMsgID'] === null ? 0 : $row['maxMsgID']
      ));
    }

    break;

  case 'subject':
    // Remove the previous subject (if any).
    db_query("
      DELETE FROM {$db_prefix}log_search_subjects
      WHERE ID_TOPIC = " . (int) $parameter1, __FILE__, __LINE__);

    // Insert the new subject.
    if ($parameter2 !== null) {
      $parameter1 = (int) $parameter1;
      $parameter2 = text2words($parameter2);

      $inserts = array();
      foreach ($parameter2 as $word)
        $inserts[] = "'$word', $parameter1";

      if (!empty($inserts))
        db_query("
          INSERT IGNORE INTO {$db_prefix}log_search_subjects
            (word, ID_TOPIC)
          VALUES (" . implode('),
            (', array_unique($inserts)) . ")", __FILE__, __LINE__);
    }

    break;

  case 'topic':
    if ($parameter1 === true)
      updateSettings(array('totalTopics' => true), true);
    else {
      // Get the number of topics - a SUM is better for InnoDB tables.
      // We also ignore the recycle bin here because there will probably be a bunch of one-post topics there.
      $result = db_query("
        SELECT SUM(numTopics) AS totalTopics
        FROM {$db_prefix}boards" . (!empty($modSettings['recycle_enable']) && $modSettings['recycle_board'] > 0 ? "
        WHERE ID_BOARD != $modSettings[recycle_board]" : ''), __FILE__, __LINE__);

      $row = mysqli_fetch_assoc($result);
      mysqli_free_result($result);

      updateSettings(array('totalTopics' => $row['totalTopics']));
    }

    break;

  case 'postgroups':
    // Parameter two is the updated columns: we should check to see if we base groups off any of these.
    if ($parameter2 !== null && !in_array('posts', $parameter2))
      return;

    if (($postgroups = cache_get_data('updateStats:postgroups', 360)) == null) {
      // Fetch the postgroups!
      $request = db_query("
        SELECT ID_GROUP, minPosts
        FROM {$db_prefix}membergroups
        WHERE minPosts != -1", __FILE__, __LINE__);
      $postgroups = array();
      while ($row = mysqli_fetch_assoc($request))
        $postgroups[$row['ID_GROUP']] = $row['minPosts'];
      mysqli_free_result($request);

      // Sort them this way because if it's done with MySQL it causes a filesort :(.
      arsort($postgroups);

      cache_put_data('updateStats:postgroups', $postgroups, 360);
    }

    // Oh great, they've screwed their post groups.
    if (empty($postgroups))
      return;

    // Set all membergroups from most posts to least posts.
    $conditions = '';
    foreach ($postgroups as $id => $minPosts) {
      $conditions .= '
          WHEN moneyBank >= ' . $minPosts . (!empty($lastMin) ? ' AND moneyBank <= ' . $lastMin : '') . ' THEN ' . $id;
      $lastMin = $minPosts;
    }

    // A big fat CASE WHEN... END is faster than a zillion UPDATE's ;).
    db_query("
      UPDATE {$db_prefix}members
      SET ID_POST_GROUP = CASE$conditions
          ELSE 0
        END" . ($parameter1 != null ? "
      WHERE $parameter1" : ''), __FILE__, __LINE__);
    break;

    default:
      trigger_error('updateStats(): Invalid statistic type \'' . $type . '\'', E_USER_NOTICE);
  }
}

// Assumes the data has been slashed.
function updateMemberData($members, $data) {
  global $db_prefix, $modSettings, $ID_MEMBER, $user_info;

  if (is_array($members))
    $condition = 'ID_MEMBER IN (' . implode(', ', $members) . ')
    LIMIT ' . count($members);
  else if ($members === null)
    $condition = '1';
  else
    $condition = 'ID_MEMBER = ' . $members . '
    LIMIT 1';

  if (isset($modSettings['integrate_change_member_data']) && function_exists($modSettings['integrate_change_member_data']))   {
    // Only a few member variables are really interesting for integration.
    $integration_vars = array(
      'memberName',
      'realName',
      'emailAddress',
      'ID_GROUP',
      'gender',
      'birthdate',
      'websiteTitle',
      'websiteUrl',
      'location',
      'hideEmail',
      'timeFormat',
      'timeOffset',
      'avatar',
      'lngfile',
    );

    $vars_to_integrate = array_intersect($integration_vars, array_keys($data));

    if (count($vars_to_integrate) != 0) {
      if ((!is_array($members) && $members === $ID_MEMBER) || (is_array($members) && count($members) == 1 && in_array($ID_MEMBER, $members)))
        $memberNames = array($user_info['username']);
      else {
        $memberNames = array();
        $request = db_query("
          SELECT memberName
          FROM {$db_prefix}members
          WHERE $condition", __FILE__, __LINE__);

        while ($row = mysqli_fetch_assoc($request))
          $memberNames[] = $row['memberName'];

        mysqli_free_result($request);
      }

      if (!empty($memberNames))
        foreach ($vars_to_integrate as $var)
          call_user_func($modSettings['integrate_change_member_data'], $memberNames, $var, stripslashes($data[$var]));
    }
  }

  foreach ($data as $var => $val) {
    if ($val === '+')
      $data[$var] = $var . ' + 1';
    else if ($val === '-')
      $data[$var] = $var . ' - 1';
  }

  // Ensure posts, instantMessages, and unreadMessages never go below 0.
  foreach(array('posts', 'instantMessages', 'unreadMessages') as $type)
    if (isset($data[$type]) && preg_match('~^' . $type . ' - ([\d]+)~', $data[$type], $match) === 1)
      $data[$type] = 'CASE WHEN ' . $type . ' <= ' . $match[1] . ' THEN 0 ELSE ' . $data[$type] . ' END';

  $setString = '';
  foreach ($data as $var => $val) {
    $setString .= "
      $var = $val,";
  }

  db_query("
    UPDATE {$db_prefix}members
    SET" . substr($setString, 0, -1) . '
    WHERE ' . $condition, __FILE__, __LINE__);

  updateStats('postgroups', $condition, array_keys($data));

  // Clear any caching?
  if (!empty($modSettings['cache_enable']) && $modSettings['cache_enable'] >= 2 && !empty($members)) {
    if (!is_array($members))
      $members = array($members);

    foreach ($members as $member) {
      if ($modSettings['cache_enable'] == 3) {
        cache_put_data('member_data-profile-' . $member, null, 120);
        cache_put_data('member_data-normal-' . $member, null, 120);
        cache_put_data('member_data-minimal-' . $member, null, 120);
      }

      cache_put_data('user_settings-' . $member, null, 60);
    }
  }
}

// Updates the settings table as well as $modSettings... only does one at a time if $update is true.
// All input variables and values are assumed to have escaped apostrophes(')!
function updateSettings($changeArray, $update = false) {
  global $db_prefix, $modSettings;

  if (empty($changeArray) || !is_array($changeArray))
    return;

  // In some cases, this may be better and faster, but for large sets we don't want so many UPDATEs.
  if ($update) {
    foreach ($changeArray as $variable => $value) {
      db_query("
        UPDATE {$db_prefix}settings
        SET value = " . ($value === true ? 'value + 1' : ($value === false ? 'value - 1' : "'$value'")) . "
        WHERE variable = '$variable'
        LIMIT 1", __FILE__, __LINE__);

      $modSettings[$variable] = $value === true ? $modSettings[$variable] + 1 : ($value === false ? $modSettings[$variable] - 1 : stripslashes($value));
    }

    // Clean out the cache and make sure the cobwebs are gone too.
    cache_put_data('modSettings', null, 90);

    return;
  }

  $replaceArray = array();
  foreach ($changeArray as $variable => $value) {
    // Don't bother if it's already like that ;).
    if (isset($modSettings[$variable]) && $modSettings[$variable] == stripslashes($value))
      continue;
    // If the variable isn't set, but would only be set to nothing'ness, then don't bother setting it.
    else if (!isset($modSettings[$variable]) && empty($value))
      continue;

    $replaceArray[] = "(SUBSTRING('$variable', 1, 255), SUBSTRING('$value', 1, 65534))";
    $modSettings[$variable] = stripslashes($value);
  }

  if (empty($replaceArray))
    return;

  db_query("
    REPLACE INTO {$db_prefix}settings (variable, value)
    VALUES " . implode(',
      ', $replaceArray), __FILE__, __LINE__);

  // Kill the cache - it needs redoing now, but we won't bother ourselves with that here.
  cache_put_data('modSettings', null, 90);
}

// Constructs a page list.
// $pageindex = constructPageIndex($scripturl . '?board=' . $board, $_REQUEST['start'], $num_messages, $maxindex, true);
function constructPageIndex($base_url, &$start, $max_value, $num_per_page, $flexible_start = false) {
  global $modSettings;

  // Save whether $start was less than 0 or not.
  $start_invalid = $start < 0;

  // Make sure $start is a proper variable - not less than 0.
  if ($start_invalid)
    $start = 0;
  // Not greater than the upper bound.
  else if ($start >= $max_value)
    $start = max(0, (int) $max_value - (((int) $max_value % (int) $num_per_page) == 0 ? $num_per_page : ((int) $max_value % (int) $num_per_page)));
  // And it has to be a multiple of $num_per_page!
  else
    $start = max(0, (int) $start - ((int) $start % (int) $num_per_page));

  // Wireless will need the protocol on the URL somewhere.
  if (WIRELESS)
    $base_url .= ';' . WIRELESS_PROTOCOL;

  $base_link = '<a href="' . ($flexible_start ? $base_url : strtr($base_url, array('%' => '%%')) . ';start=%d') . '">%s</a> ';

  // Compact pages is off or on?
  if (empty($modSettings['compactTopicPagesEnable'])) {
    $pageindex = $start == 0 ? ' ' : sprintf($base_link, $start - $num_per_page, '&#171;');
    $display_page = 1;
    for ($counter = 0; $counter < $max_value; $counter += $num_per_page)
      $pageindex .= $start == $counter && !$start_invalid ? '<b>' . $display_page++ . '</b> ' : sprintf($base_link, $counter, $display_page++);
    $display_page = ($start + $num_per_page) > $max_value ? $max_value : ($start + $num_per_page);
    if ($start != $counter - $max_value && !$start_invalid)
      $pageindex .= $display_page > $counter - $num_per_page ? ' ' : sprintf($base_link, $display_page, '&#187;');
  } else {
    // If they didn't enter an odd value, pretend they did.
    $PageContiguous = (int) ($modSettings['compactTopicPagesContiguous'] - ($modSettings['compactTopicPagesContiguous'] % 2)) / 2;

    $tmpStart = $start + $num_per_page * -1;
    if (($start / $num_per_page + 1)>1) {
      $pageindex = sprintf($base_link, $tmpStart, "&#171; anterior");
      }

    $tmpMaxPages = (int) (($max_value - 1) / $num_per_page) * $num_per_page;
    $tmpStart = $start + $num_per_page * 1;
    if ($tmpStart <= $tmpMaxPages)
      $pageindex .= sprintf($base_link, $tmpStart, "siguiente &#187;");
  }

  return $pageindex;
}

// Formats a number to display in the style of the admin's choosing.
function comma_format($number, $override_decimal_count = false) {
  global $modSettings;
  static $thousands_separator = null, $decimal_separator = null, $decimal_count = null;

  // !!! Should, perhaps, this just be handled in the language files, and not a mod setting?
  // (French uses 1 234,00 for example... what about a multilingual forum?)

  // Cache these values...
  if ($decimal_separator === null) {
    // Not set for whatever reason?
    if (empty($modSettings['number_format']) || preg_match('~^1([^\d]*)?234([^\d]*)(0*?)$~', $modSettings['number_format'], $matches) != 1)
      return $number;

    // Cache these each load...
    $thousands_separator = $matches[1];
    $decimal_separator = $matches[2];
    $decimal_count = strlen($matches[3]);
  }

  // Format the string with our friend, number_format.
  return number_format($number, is_float($number) ? ($override_decimal_count === false ? $decimal_count : $override_decimal_count) : 0, $decimal_separator, $thousands_separator);
}

// Format a time to make it look purdy.
function timeformat($logTime, $show_today = true) {
  global $user_info, $txt, $modSettings, $func;

  // Offset the time.
  $time = $logTime + ($user_info['time_offset'] + $modSettings['time_offset']) * 3600;

  // We can't have a negative date (on Windows, at least.)
  if ($time < 0)
    $time = 0;

  $str = !is_bool($show_today) ? $show_today : $user_info['time_format'];

  // TO-DO: Cambiar strftime deprecated
  if (setlocale(LC_TIME, $txt['lang_locale'])) {
    foreach (array('%a', '%A', '%b', '%B') as $token)
      if (strpos($str, $token) !== false)
        $str = str_replace($token, $func['ucwords'](strftime($token, $time)), $str);
  } else {
    // Do-it-yourself time localization.  Fun.
    foreach (array('%a' => 'days_short', '%A' => 'days', '%b' => 'months_short', '%B' => 'months') as $token => $text_label)
      if (strpos($str, $token) !== false)
        $str = str_replace($token, $txt[$text_label][(int) strftime($token === '%a' || $token === '%A' ? '%w' : '%m', $time)], $str);
    if (strpos($str, '%p'))
      $str = str_replace('%p', (strftime('%H', $time) < 12 ? 'am' : 'pm'), $str);
  }

  // Format any other characters..
  return strftime($str, $time);
}

// Removes special entities from strings.  Compatibility...
function un_htmlspecialchars($string) {
  return strtr($string, array_flip(get_html_translation_table(HTML_SPECIALCHARS, ENT_QUOTES)) + array('&#039;' => '\'', '&nbsp;' => ' '));
}

if (!function_exists('stripos')) {
  function stripos($haystack, $needle, $offset = 0) {
    return strpos(strtolower($haystack), strtolower($needle), $offset);
  }
}

// Shorten a subject + internationalization concerns.
function shorten_subject($subject, $len) {
  global $func;

  // It was already short enough!
  if ($func['strlen']($subject) <= $len)
    return $subject;

  // Shorten it by the length it was too long, and strip off junk from the end.
  return $func['substr']($subject, 0, $len) . '...';
}

// The current time with offset.
function forum_time($use_user_offset = true, $timestamp = null) {
  global $user_info, $modSettings;

  if ($timestamp === null)
    $timestamp = time();
  else if ($timestamp == 0)
    return 0;

  return $timestamp + ($modSettings['time_offset'] + ($use_user_offset ? $user_info['time_offset'] : 0)) * 3600;
}

// This gets all possible permutations of an array.
function permute($array) {
  $orders = array($array);

  $n = count($array);
  $p = range(0, $n);
  for ($i = 1; $i < $n; null) {
    $p[$i]--;
    $j = $i % 2 != 0 ? $p[$i] : 0;

    $temp = $array[$i];
    $array[$i] = $array[$j];
    $array[$j] = $temp;

    for ($i = 1; $p[$i] == 0; $i++)
      $p[$i] = 1;

    $orders[] = $array;
  }

  return $orders;
}

// For old stuff still using doUBBC()...
function doUBBC($message, $enableSmileys = true) {
  return parse_bbc($message, $enableSmileys);
}

// Parse bulletin board code in a string, as well as smileys optionally.
function parse_bbc($message, $smileys = true, $cache_id = '') {
  global $txt, $context, $modSettings, $user_info, $boardurl;
  static $bbc_codes = array(), $itemcodes = array(), $no_autolink_tags = array();
  static $disabled;

  // On the first Parsing i will gernerate all things i need ;) after that it's not needable :)
  if (!isset($modSettings['hide_preparedOption'])) {
    // First there is nothing in it ;)
    $modSettings['hide_preparedOption']['hiddentext'] = '&nbsp;';
    $modSettings['hide_preparedOption']['unhiddentext'] = '&nbsp;';

    if (empty($modSettings['hide_noinfoforguests']) || !$user_info['is_guest']) {

      // Prepare the hidden information :)
      $modSettings['hide_preparedOption']['hiddentext'] = isset($modSettings['hide_hiddentext']) ? $modSettings['hide_hiddentext'] : '';
      $modSettings['hide_preparedOption']['hiddentext'] = str_replace('$language', $user_info['language'], $modSettings['hide_preparedOption']['hiddentext']);
      if (!empty($modSettings['hide_MUIswitch']) && isset($txt['hide_hiddentext']))
        $modSettings['hide_preparedOption']['hiddentext'] = str_replace('$txtvariable', $txt['hide_hiddentext'], $modSettings['hide_preparedOption']['hiddentext']);
      if (!empty($modSettings['hide_enableHTML']))
        $modSettings['hide_preparedOption']['hiddentext'] = un_htmlspecialchars($modSettings['hide_preparedOption']['hiddentext']);

      // Prepare the unhidden infomation?
      if (!empty($modSettings['hide_enableUnhiddenText'])) {
        $modSettings['hide_preparedOption']['unhiddentext'] = isset($modSettings['hide_unhiddentext']) ? $modSettings['hide_unhiddentext'] : '';
        $modSettings['hide_preparedOption']['unhiddentext'] = str_replace('$language', $user_info['language'], $modSettings['hide_preparedOption']['unhiddentext']);
        if (!empty($modSettings['hide_MUIswitch']) && isset($txt['hide_unhiddentext']))
          $modSettings['hide_preparedOption']['unhiddentext'] = str_replace('$txtvariable', $txt['hide_unhiddentext'], $modSettings['hide_preparedOption']['unhiddentext']);
        if (!empty($modSettings['hide_enableHTML']))
          $modSettings['hide_preparedOption']['unhiddentext'] = un_htmlspecialchars($modSettings['hide_preparedOption']['unhiddentext']);
      }
    }

    // So prepare the unhidden Option for the Parser ;)
    if (!empty($modSettings['hide_useSpanTag'])) {
      $modSettings['hide_preparedOption']['before_info'] = '<span class="hiddencontent">';
      $modSettings['hide_preparedOption']['after_info'] = '</span>';
    } else {
      $modSettings['hide_preparedOption']['before_info'] = '<div class="hiddencontent">';
      $modSettings['hide_preparedOption']['after_info'] = '</div>';
    }

    // Okay Let's see where i must put the unhidden Informations :)
    if (!empty($modSettings['hide_enableUnhiddenText']) && empty($modSettings['hide_onlyonetimeinfo'])) {
      if (empty($modSettings['hide_posUnhiddenText']) || $modSettings['hide_posUnhiddenText'] == 4)
        $modSettings['hide_preparedOption']['after_info'] .= $modSettings['hide_preparedOption']['unhiddentext'];
      else if ($modSettings['hide_posUnhiddenText'] == 3)
        $modSettings['hide_preparedOption']['after_info'] = $modSettings['hide_preparedOption']['unhiddentext'].$modSettings['hide_preparedOption']['after_info'];
      else if ($modSettings['hide_posUnhiddenText'] == 2)
        $modSettings['hide_preparedOption']['before_info'] .= $modSettings['hide_preparedOption']['unhiddentext'];
      else if ($modSettings['hide_posUnhiddenText'] == 1)
        $modSettings['hide_preparedOption']['before_info'] = $modSettings['hide_preparedOption']['unhiddentext'].$modSettings['hide_preparedOption']['before_info'];
      else
        $modSettings['hide_preparedOption']['after_info'] .= $modSettings['hide_preparedOption']['unhiddentext'];
    }

    $modSettings['hide_preparedOption']['parse_content'] = !empty($modSettings['hide_enableUnhiddenText']) && empty($modSettings['hide_onlyonetimeinfo']);
  }

  // Hide Mod.. i remove the code before i do anything...
  if ($message !== false && !empty($message)) {
    if ((empty($context['user_post_avaible']) || !isset($context['user_post_avaible'])) && !empty($modSettings['hide_onlyonetimeinfo'])) {
      // Okay some more things to do, i must check if there a hidden content... without i need do nothing ;)
      if (preg_match("/\[hide\](.+?)\[\/hide\]/i", $message) != 0 || preg_match("/\[hide(.+?)\](.+?)\[\/hide\]/i", $message) != 0) {
        // Add the Hidden Text at the end of the post :)
        $message .= '[br]'.$modSettings['hide_preparedOption']['hiddentext'];
      }
    }

    // Here i remove this content :) or show onetime unhide info :)
    if (!isset($context['user_post_avaible']) || empty($context['user_post_avaible'])) {
      // Remove it ;D
      $message = preg_replace("/\[hide\](.+?)\[\/hide\]/i", (empty($modSettings['hide_onlyonetimeinfo']) ? $modSettings['hide_preparedOption']['hiddentext'] : "&nbsp"), $message);
      $message = preg_replace(array('~\n?\[hide.*?\].+?\[/hide\]\n?~is', '~^\n~', '~\[/hide\]~'), (empty($modSettings['hide_onlyonetimeinfo']) ? $modSettings['hide_preparedOption']['hiddentext'] : "&nbsp"), $message);
    } else if (!empty($context['user_post_avaible']) && !empty($modSettings['hide_onlyonetimeinfo']) && !empty($modSettings['hide_enableUnhiddenText'])) {
      // Okay i need to add the unhidden information at the end of the post :)
      if (preg_match("/\[hide\](.+?)\[\/hide\]/i", $message) != 0 || preg_match("/\[hide(.+?)\](.+?)\[\/hide\]/i", $message) != 0) {
        $message .= '[br]'.$modSettings['hide_preparedOption']['unhiddentext'];
      }
    }
  }

  // Never show smileys for wireless clients.  More bytes, can't see it anyway :P.
  if (WIRELESS)
    $smileys = false;
  else if ($smileys !== null && ($smileys == '1' || $smileys == '0'))
    $smileys = (bool) $smileys;

  if (empty($modSettings['enableBBC']) && $message !== false) {
    if ($smileys === true)
      parsesmileys($message);

    return $message;
  }

  // Just in case it wasn't determined yet whether UTF-8 is enabled.
  if (!isset($context['utf8']))
    $context['utf8'] = (empty($modSettings['global_character_set']) ? $txt['lang_character_set'] : $modSettings['global_character_set']) === 'UTF-8';

  // Sift out the bbc for a performance improvement.
  if (empty($bbc_codes) || $message === false) {
    if (!empty($modSettings['disabledBBC'])) {
      $temp = explode(',', strtolower($modSettings['disabledBBC']));

      foreach ($temp as $tag)
        $disabled[trim($tag)] = true;
    }

    if (empty($modSettings['enableEmbeddedFlash']))
      $disabled['flash'] = true;

    $codes = array(
      array(
        'tag' => 'highlight',
        'type' => 'unparsed_equals',
        'test' => '(#[\da-fA-F]{3}|#[\da-fA-F]{6}|[A-Za-z]{1,12})\]',
        'before' => '<span style="background-color: $1;">',
        'after' => '</span>',
      ),
      array(
        'tag' => 'strike',
        'before' => '<strike>',
        'after' => '</strike>',
      ),
      array(
        'tag' => 'abbr',
        'type' => 'unparsed_equals',
        'before' => '<abbr title="$1">',
        'after' => '</abbr>',
        'quoted' => 'optional',
        'disabled_after' => ' ($1)',
      ),
      array(
        'tag' => 'b',
        'before' => '<b>',
        'after' => '</b>',
      ),
      array(
        'tag' => 'black',
        'before' => '<span style="color: black;">',
        'after' => '</span>',
      ),
      array(
        'tag' => 'blue',
        'before' => '<span style="color: blue;">',
        'after' => '</span>',
      ),
      array(
        'tag' => 'code',
        'type' => 'unparsed_content',
        'content' => '<div class="code" id="code">$1</div>',
        // !!! Maybe this can be simplified?
        'validate' => isset($disabled['code']) ? null : create_function('&$tag, &$data, $disabled', '
          global $context;

          if (!isset($disabled[\'code\']))
          {
            $php_parts = preg_split(\'~(&lt;\?php|\?&gt;)~\', $data, -1, PREG_SPLIT_DELIM_CAPTURE);

            for ($php_i = 0, $php_n = count($php_parts); $php_i < $php_n; $php_i++)
            {
              // Do PHP code coloring?
              if ($php_parts[$php_i] != \'&lt;?php\')
                continue;

              $php_string = \'\';
              while ($php_i + 1 < count($php_parts) && $php_parts[$php_i] != \'?&gt;\')
              {
                $php_string .= $php_parts[$php_i];
                $php_parts[$php_i++] = \'\';
              }
              $php_parts[$php_i] = highlight_php_code($php_string . $php_parts[$php_i]);
            }

            // Fix the PHP code stuff...
            $data = str_replace("<pre style=\"display: inline;\">\t</pre>", "\t", implode(\'\', $php_parts));

            // Older browsers are annoying, aren\'t they?
            if ($context[\'browser\'][\'is_ie4\'] || $context[\'browser\'][\'is_ie5\'] || $context[\'browser\'][\'is_ie5.5\'])
              $data = str_replace("\t", "<pre style=\"display: inline;\">\t</pre>", $data);
            else if (!$context[\'browser\'][\'is_gecko\'])
              $data = str_replace("\t", "<span style=\"white-space: pre;\">\t</span>", $data);
          }'),
        'block_level' => true,
      ),
      array(
        'tag' => 'code',
        'type' => 'unparsed_equals_content',
        'content' => '<div class="code" id="code">$1</div>',
        // !!! Maybe this can be simplified?
        'validate' => isset($disabled['code']) ? null : create_function('&$tag, &$data, $disabled', '
          global $context;

          if (!isset($disabled[\'code\']))
          {
            $php_parts = preg_split(\'~(&lt;\?php|\?&gt;)~\', $data[0], -1, PREG_SPLIT_DELIM_CAPTURE);

            for ($php_i = 0, $php_n = count($php_parts); $php_i < $php_n; $php_i++)
            {
              // Do PHP code coloring?
              if ($php_parts[$php_i] != \'&lt;?php\')
                continue;

              $php_string = \'\';
              while ($php_i + 1 < count($php_parts) && $php_parts[$php_i] != \'?&gt;\')
              {
                $php_string .= $php_parts[$php_i];
                $php_parts[$php_i++] = \'\';
              }
              $php_parts[$php_i] = highlight_php_code($php_string . $php_parts[$php_i]);
            }

            // Fix the PHP code stuff...
            $data[0] = str_replace("<pre style=\"display: inline;\">\t</pre>", "\t", implode(\'\', $php_parts));

            // Older browsers are annoying, aren\'t they?
            if ($context[\'browser\'][\'is_ie4\'] || $context[\'browser\'][\'is_ie5\'] || $context[\'browser\'][\'is_ie5.5\'])
              $data = str_replace("\t", "<pre style=\"display: inline;\">\t</pre>", $data);
            else if (!$context[\'browser\'][\'is_gecko\'])
              $data = str_replace("\t", "<span style=\"white-space: pre;\">\t</span>", $data);
          }'),
        'block_level' => true,
      ),
      array(
        'tag' => 'center',
        'before' => '<div align="center">',
        'after' => '</div>',
        'block_level' => true,
      ),
      array(
        'tag' => 'color',
        'type' => 'unparsed_equals',
        'test' => '(#[\da-fA-F]{3}|#[\da-fA-F]{6}|[A-Za-z]{1,12})\]',
        'before' => '<span style="color: $1;">',
        'after' => '</span>',
      ),
      array(
        'tag' => 'email',
        'type' => 'unparsed_content',
        'content' => '<a href="mailto:$1">$1</a>',
        // !!! Should this respect guest_hideContacts?
        'validate' => create_function('&$tag, &$data, $disabled', '$data = strtr($data, array(\'<br />\' => \'\'));'),
      ),
      array(
        'tag' => 'email',
        'type' => 'unparsed_equals',
        'before' => '<a href="mailto:$1">',
        'after' => '</a>',
        // !!! Should this respect guest_hideContacts?
        'disallow_children' => array('email', 'ftp', 'url', 'iurl'),
        'disabled_after' => ' ($1)',
      ),
      array(
        'tag' => 'font',
        'type' => 'unparsed_equals',
        'test' => '[A-Za-z0-9_,\-\s]+?\]',
        'before' => '<span style="font-family: $1;">',
        'after' => '</span>',
      ),
      array(
        'tag' => 'flash',
        'type' => 'unparsed_commas_content',
        'test' => '\d+,\d+\]',
        'content' => ($context['browser']['is_ie'] && !$context['browser']['is_mac_ie'] ? '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="$2" height="$3"><param name="movie" value="$1" /><param name="play" value="true" /><param name="loop" value="true" /><param name="quality" value="high" /><param name="AllowScriptAccess" value="never" /><embed src="$1" width="$2" height="$3" play="true" loop="true" quality="high" AllowScriptAccess="never" /><noembed><a href="$1" target="_blank">$1</a></noembed></object>' : '<embed type="application/x-shockwave-flash" src="$1" width="$2" height="$3" play="true" loop="true" quality="high" AllowScriptAccess="never" /><noembed><a href="$1" target="_blank">$1</a></noembed>'),
        'validate' => create_function('&$tag, &$data, $disabled', '
          if (isset($disabled[\'url\']))
            $tag[\'content\'] = \'$1\';
          else if (strpos($data[0], \'http://\') !== 0 && strpos($data[0], \'https://\') !== 0)
            $data[0] = \'http://\' . $data[0];
        '),
        'disabled_content' => '<a href="$1" target="_blank">$1</a>',
      ),
      array(
        'tag' => 'swf',
        'type' => 'unparsed_content',
        'content' => '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="425" height="350"><param name="movie" value="$1" /><param name="play" value="true" /><param name="loop" value="true" /><param name="quality" value="high" /><param name="AllowScriptAccess" value="never" /><embed src="$1" width="425" height="350" play="true" loop="true" quality="high" AllowScriptAccess="never" /><noembed><a href="$1" target="_blank">[enlace]</a></noembed></object><br><a href="$1" target="_blank">[enlace]</a>',
        'validate' => create_function('&$tag, &$data, $disabled', '$data = strtr($data, array(\'<br />\' => \'\'));'),
        ),

      array(
        'tag' => 'green',
        'before' => '<span style="color: green;">',
        'after' => '</span>',
      ),
      array(
        'tag' => 'hr',
        'type' => 'closed',
        'content' => '<div style="width: 50%;" class="hrs"></div>',
        'block_level' => true,
      ),

      array(
        'tag' => 'googlevid',
        'type' => 'unparsed_commas_content',
        'test' => '\d+,\d+\]',
        'content' => '<object type="application/x-shockwave-flash" data="http://video.google.com/googleplayer.swf?docId=$1" width="$2" height="$3"><param name="movie" value="http://video.google.com/googleplayer.swf?docId=$1" /><param name="wmode" value="transparent" /></object>',
        'validate' => create_function('&$tag, &$data, $disabled', '
                                        if (isset($disabled[\'url\']))
            $tag[\'content\'] = \'$1\';
          $pattern = \'/http:\/\/.+=(.+)(&.+)*/i\';
          if (preg_match($pattern, $data[0], $matches))
            $data[0] = $matches[1];'),
          'disabled_content' => '<a href="$1" target="_blank">$1</a>',
          ),
      array(
        'tag' => 'html',
        'type' => 'unparsed_content',
        'content' => '$1',
        'block_level' => true,
        'disabled_content' => '$1',
      ),
      array(
      'tag' => 'img',
      'type' => 'unparsed_content',
       'parameters' => array( 'alt' => array('optional' => true),
       'width' => array('optional' => true, 'value' => ' width="$1"', 'match' => '(\d+)'),
       'height' => array('optional' => true, 'value' => ' height="$1"', 'match' => '(\d+)'), ),
       'content' => '<img onload="if (this.width >720) {this.width=720}" id="imagen" src="$1" border="0" />',
       'validate' => create_function('&$tag, &$data, $disabled', '$data = strtr($data, array(\'<br />\' => \'\'));'),
       'disabled_content' => '($1)', ),

      array(
      'tag' => 'img', 'type' => 'unparsed_content',
      'content' => '<img onload="if (this.width >720) {this.width=720}" id="imagen" src="$1" border="0">',
      'validate' => create_function('&$tag, &$data, $disabled', '$data = strtr($data, array(\'<br />\' => \'\'));'),
      'disabled_content' => '($1)', ),
      array(
        'tag' => 'i',
        'before' => '<i>',
        'after' => '</i>',
      ),
      array(
        'tag' => 'iurl',
        'type' => 'unparsed_content',
        'content' => '<a href="$1">$1</a>',
        'validate' => create_function('&$tag, &$data, $disabled', '
          $data = strtr($data, array(\'<br />\' => \'\'));
          if (strpos($data, \'http://\') !== 0 && strpos($data, \'https://\') !== 0)
            $data = \'http://\' . $data;
        '),
      ),
      array(
        'tag' => 'iurl',
        'type' => 'unparsed_equals',
        'before' => '<a href="$1">',
        'after' => '</a>',
        'validate' => create_function('&$tag, &$data, $disabled', '
          if (substr($data, 0, 1) == \'#\')
            $data = \'#post_\' . substr($data, 1);
          else if (strpos($data, \'http://\') !== 0 && strpos($data, \'https://\') !== 0)
            $data = \'http://\' . $data;
        '),
        'disallow_children' => array('email', 'ftp', 'url', 'iurl'),
        'disabled_after' => ' ($1)',
      ),
      array(
        'tag' => 'left',
        'before' => '<div style="text-align: left;">',
        'after' => '</div>',
        'block_level' => true,
      ),
      array(
        'tag' => 'right',
        'before' => '<div style="text-align: right;">',
        'after' => '</div>',
        'block_level' => true,
      ),
      array(
        'tag' => 'move',
        'before' => '<marquee>',
        'after' => '</marquee>',
        'block_level' => true,
      ),
      array(
        'tag' => 'nobbc',
        'type' => 'unparsed_content',
        'content' => '$1',
      ),
      array(
        'tag' => 'quote',
        'before' => '<blockquote><div class="cita">' . $txt['smf240'] . ': </div><div class="citacuerpo">',
        'after' => '</div></blockquote>',
        'block_level' => true,
      ),
      array(
        'tag' => 'quote',
        'parameters' => array(
          'author' => array('match' => '(.{1,192}?)', 'quoted' => true, 'validate' => 'parse_bbc'),
        ),
        'before' => '<blockquote><div class="cita">' . $txt['smf240'] . ' {author}: </div><div class="citacuerpo">',
        'after' => '</div></blockquote>',
        'block_level' => true,
      ),
      array(
        'tag' => 'quote',
        'type' => 'parsed_equals',
        'before' => '<blockquote><div class="cita">' . $txt['smf240'] . ' $1: </div><div class="citacuerpo">',
        'after' => '</div></blockquote>',
        'quoted' => 'optional',
        'block_level' => true,
      ),
      array(
        'tag' => 'red',
        'before' => '<span style="color: red;">',
        'after' => '</span>',
      ),
      array(
        'tag' => 'size',
        'type' => 'unparsed_equals',
        'test' => '([1-9][\d]?p[xt]|(?:x-)?small(?:er)?|(?:x-)?large[r]?)\]',
        'before' => '<span style="font-size: $1; line-height: 1.3em;">',
        'after' => '</span>',
      ),
      array(
        'tag' => 'size',
        'type' => 'unparsed_equals',
        'test' => '[1-9]\]',
        'before' => '<font size="$1" style="line-height: 1.3em;">',
        'after' => '</font>',
      ),
      array(
        'tag' => 'url',
        'type' => 'unparsed_content',
        'content' => '<a href="$1" target="_blank" title="$1" alt="$1">$1</a>',
        'validate' => create_function('&$tag, &$data, $disabled', '$data = strtr($data, array(\'<br />\' => \'\'));
          if (strpos($data, \'http://\') !== 0 && strpos($data, \'https://\') !== 0)
            $data = \'http://\' . $data;
        '),
      ),
      array(
        'tag' => 'url',
        'type' => 'unparsed_equals',
        'before' => '<a href="$1" target="_blank" title="$1" alt="$1">',
        'after' => '</a>',
        'validate' => create_function('&$tag, &$data, $disabled', '
          if (strpos($data, \'http://\') !== 0 && strpos($data, \'https://\') !== 0)
            $data = \'http://\' . $data;
        '),
        'disallow_children' => array('email', 'ftp', 'url', 'iurl'),
        'disabled_after' => ' ($1)',
      ),
      array(
        'tag' => 'u',
        'before' => '<span style="text-decoration: underline;">',
        'after' => '</span>',
      ),
      array(
        'tag' => 'white',
        'before' => '<span style="color: white;">',
        'after' => '</span>',
      ),

      array(
        'tag' => 'youtube',
        'type' => 'unparsed_content',
        'content' => '<div style="width:640px;font-size:11px;height:400px;"><embed src="http://www.youtube.com/v/$1&rel=0&hl=es&fs=1&color1=0x006699&color2=0x54abd6&border=1&fmt=22&eurl=', $boardurl, '/" quality="high" type="application/x-shockwave-flash" allownetworking="internal" allowscriptaccess="never" wmode="transparent" width="640px" height="385px"><div style="margin-top:4px;"><a href="http://www.youtube.com/watch?v=$1&fmt=22&eurl=', $boardurl, '/" target="_blank" rel="nofollow">[enlace]</a></div><div style="clear: left;"></div></div>',
        'validate' => create_function('&$tag, &$data, $disabled', '
          // Access globals
          global $txt, $context;
          // Determine which variable is the link
          $link = !is_array($data) ? $data : $data[0] ;
          // Remove linebreaks & trim
          $link = trim(strtr($link, array(\'<br />\' => \'\')));
          // Parse the ID of video or playlist safely
          if  (preg_match(\'~^(?:http://((?:www|au|br|ca|es|fr|de|hk|ie|in|il|it|jp|kr|mx|nl|nz|pl|ru|tw|uk)\.)?youtube\.com/(?:[^"]*?)(?:(?:video_)?id=|(?:v|p)(?:/|=)))?([0-9a-f]{16}|[0-9a-z-_]{11})~i\'.($context[\'utf8\'] ? \'u\' : \'\'), $link, $matches))
          {
            // Localised youtube site?  If not use www.
            $site = !empty($matches[1]) ? strtolower($matches[1]) : \'www.\' ;
            // Video or Playlist ID?
            $type = strlen($matches[2]) == 11 ? 1 : 0 ;
            // Set sizes Or Normalise sizes (If sizes are <100 or > 780)
            if (!is_array($data) || ($data[1] > 780 || $data[1] < 100 || $data[2] > 780 || $data[2] < 100))
              $data = array(0, 425, ($type ? 350 : 355));
            // Set ID in the array
            $data[0] = $matches[2];
            // Tidy up
            unset($matches, $link);

            // Set the Content (With conditions on disabled types of BBCode)
            if (isset($disabled[\'url\']) && isset($disabled[\'youtube\']))
              // Youtube & Url bbc disabled? (eg Printer friendly pages)
              $tag[\'content\'] = "http://". $site ."youtube.com/". ($type ? "watch?v" : "view_play_list?p") ."=". $data[0];
            else if (isset($disabled[\'youtube\']))
              // Only Youtube is disabled, So make an active link
              $tag[\'content\'] = "<a href=\"http://". $site ."youtube.com/". ($type ? "watch?v" : "view_play_list?p") ."=". $data[0]."\" target=\"_blank\">http://". $site ."youtube.com/". ($type ? "watch?v" : "view_play_list?p") ."=". $data[0]."</a>";
            else
            {
              // Empty content
              $tag[\'content\'] = \'\';

              // Build the <object> (Non-Mac IE Only)
                $tag[\'content\'] = \'<div style="width:640px;font-size:11px;height:400px;"><embed src="http://www.youtube.com/v/\'.$data[0].\'&rel=0&hl=es&fs=1&color1=0x006699&color2=0x54abd6&border=1&fmt=22&eurl='.$boardurl.'/" quality="high" type="application/x-shockwave-flash" allownetworking="internal" allowscriptaccess="never" wmode="transparent" width="640px" height="385px"><div style="margin-top:4px;"><a href="http://www.youtube.com/watch?v=\'.$data[0].\'&fmt=22&eurl='.$boardurl.'/" target="_blank" rel="nofollow">[enlace]</a></div><div style="clear: left;"></div></div>\';
            }
          }
          else
            // Invalid link
            $tag[\'content\'] = $txt[\'youtube_invalid\'];
        '),
        'disabled_content' => '$1',
            ),
      array(
        'tag' => 'youtube',
        'type' => 'unparsed_commas_content',
        'test' => '\d+,\d+\]',
        'validate' => create_function('&$tag, &$data, $disabled', '
          // Access globals
          global $txt, $context;
          // Determine which variable is the link
          $link = !is_array($data) ? $data : $data[0] ;
          // Remove linebreaks & trim
          $link = trim(strtr($link, array(\'<br />\' => \'\')));
          // Parse the ID of video or playlist safely
          if  (preg_match(\'~^(?:http://((?:www|au|br|ca|es|fr|de|hk|ie|in|il|it|jp|kr|mx|nl|nz|pl|ru|tw|uk)\.)?youtube\.com/(?:[^"]*?)(?:(?:video_)?id=|(?:v|p)(?:/|=)))?([0-9a-f]{16}|[0-9a-z-_]{11})~i\'.($context[\'utf8\'] ? \'u\' : \'\'), $link, $matches))
          {
            // Localised youtube site?  If not use www.
            $site = !empty($matches[1]) ? strtolower($matches[1]) : \'www.\' ;
            // Video or Playlist ID?
            $type = strlen($matches[2]) == 11 ? 1 : 0 ;
            // Set sizes Or Normalise sizes (If sizes are <100 or > 780)
            if (!is_array($data) || ($data[1] > 780 || $data[1] < 100 || $data[2] > 780 || $data[2] < 100))
              $data = array(0, 425, ($type ? 350 : 355));
            // Set ID in the array
            $data[0] = $matches[2];
            // Tidy up
            unset($matches, $link);

            // Set the Content (With conditions on disabled types of BBCode)
            if (isset($disabled[\'url\']) && isset($disabled[\'youtube\']))
              // Youtube & Url bbc disabled? (eg Printer friendly pages)
              $tag[\'content\'] = "http://". $site ."youtube.com/". ($type ? "watch?v" : "view_play_list?p") ."=". $data[0];
            else if (isset($disabled[\'youtube\']))
              // Only Youtube is disabled, So make an active link
              $tag[\'content\'] = "<a href=\"http://". $site ."youtube.com/". ($type ? "watch?v" : "view_play_list?p") ."=". $data[0]."\" target=\"_blank\">http://". $site ."youtube.com/". ($type ? "watch?v" : "view_play_list?p") ."=". $data[0]."</a>";
            else
            {
              // Empty content
              $tag[\'content\'] = \'\';

              // Build the <object> (Non-Mac IE Only)
              if ($context[\'browser\'][\'is_ie\'] && !$context[\'browser\'][\'is_mac_ie\'])
                $tag[\'content\'] = \'<object width="\'.$data[1].\'px" height="\'.$data[2].\'px">\'
                  .\'<param name="movie" value="http://www.youtube.com/\'.($type ? "v" : "p").\'/\'.$data[0].\'&amp;rel=1&amp;fs=1" />\'
                  .\'<param name="wmode" value="transparent" /><param name="allowFullScreen" value="true" />\'
                  .\'<param name="allowScriptAccess" value="never" />\';
              // Build the <embed>
              $tag[\'content\'] .= \'<embed src="http://www.youtube.com/\'.($type ? "v" : "p").\'/\'.$data[0].\'&amp;rel=1&amp;fs=1" \'
                  .\'type="application/x-shockwave-flash" allowFullScreen="true" allowScriptAccess="never" \'
                  .\'wmode="transparent" width="\'.$data[1].\'px" height="\'.$data[2].\'px">\';
              // Build the <noembed>
              $tag[\'content\'] .= "<noembed><a href=\"http://". $site ."youtube.com/". ($type ? "watch?v" : "view_play_list?p" ) ."=". $data[0]."\" target=\"_blank\">http://". $site ."youtube.com/". ($type ? "watch?v" : "view_play_list?p") ."=". $data[0]."</a></noembed>";
              // Closing <embed>
              $tag[\'content\'] .= \'</embed>\';
              // Close the <object> (Non-Mac IE Only)
              if ($context[\'browser\'][\'is_ie\'] && !$context[\'browser\'][\'is_mac_ie\'])
                $tag[\'content\'] .= \'</object>\';
            }
          }
          else
            // Invalid link
            $tag[\'content\'] = $txt[\'youtube_invalid\'];
        '),
        'disabled_content' => '$1',
      ),
      array(
        'tag' => 'hide',
        'before' => $modSettings['hide_preparedOption']['before_info'],
        'after' => $modSettings['hide_preparedOption']['after_info'],
        'block_level' => true,
      ),
    );

    // This is mainly for the bbc manager, so it's easy to add tags above.  Custom BBC should be added above this line.
    if ($message === false)
      return $codes;
    $itemcodes = array(
      '*' => '',
      '@' => 'disc',
      '+' => 'square',
      'x' => 'square',
      '#' => 'square',
      'o' => 'circle',
      'O' => 'circle',
      '0' => 'circle',
    );

    if (!isset($disabled['li']) && !isset($disabled['list'])) {
      foreach ($itemcodes as $c => $dummy)
        $bbc_codes[$c] = array();
    }

    $no_autolink_tags = array(
      'url',
      'iurl',
      'ftp',
      'email',
    );

    if (!isset($disabled['color'])) {
      $codes[] = array(
        'tag' => 'chrissy',
        'before' => '<span style="color: #CC0099;">',
        'after' => ' :-*</span>',
      );

      $codes[] = array(
        'tag' => 'kissy',
        'before' => '<span style="color: #CC0099;">',
        'after' => ' :-*</span>',
      );
    }

    foreach ($codes as $c)
      $bbc_codes[substr($c['tag'], 0, 1)][] = $c;
    $codes = null;
  }

  // So i parse now the unhidden bbc code one time ;)
  if ($modSettings['hide_preparedOption']['parse_content']) {
    $modSettings['hide_preparedOption']['parse_content'] = false;
    if (isset($bbc_codes['h'])) {
      foreach ($bbc_codes['h'] as $key => $item) {
        if ($item['tag'] == 'hide') {
          $bbc_codes['h'][$key]['before'] = parse_bbc($item['before']);
          $bbc_codes['h'][$key]['after'] = parse_bbc($item['after']);
          break;
        }
      }
    }
  }

  // Shall we take the time to cache this?
  if ($cache_id != '' && !empty($modSettings['cache_enable']) && (($modSettings['cache_enable'] >= 2 && strlen($message) > 1000) || strlen($message) > 2400)) {
    // It's likely this will change if the message is modified.
    $cache_key = 'parse:' . $cache_id . '-' . md5(md5($message) . '-' . $smileys . (empty($disabled) ? '' : implode(',', array_keys($disabled))) . serialize($context['browser']) . $txt['lang_locale'] . $user_info['time_offset'] . $user_info['time_format']);

    if (($temp = cache_get_data($cache_key, 240)) != null)
      return $temp;

    $cache_t = microtime();
  }

  if ($smileys === 'print') {
    // [glow], [shadow], and [move] can't really be printed.
    $disabled['glow'] = true;
    $disabled['shadow'] = true;
    $disabled['move'] = true;

    // Colors can't well be displayed... supposed to be black and white.
    $disabled['color'] = true;
    $disabled['black'] = true;
    $disabled['blue'] = true;
    $disabled['white'] = true;
    $disabled['red'] = true;
    $disabled['green'] = true;
    $disabled['me'] = true;

    // Color coding doesn't make sense.
    $disabled['php'] = true;

    // Links are useless on paper... just show the link.
    $disabled['ftp'] = true;
    $disabled['url'] = true;
    $disabled['iurl'] = true;
    $disabled['email'] = true;
    $disabled['flash'] = true;
    $disabled['youtube'] = true;


    // !!! Change maybe?
    if (!isset($_GET['images']))
      $disabled['img'] = true;

    // !!! Interface/setting to add more?
  }

  $open_tags = array();
  $message = strtr($message, array("\n" => '<br />'));

  // The non-breaking-space looks a bit different each time.
  $non_breaking_space = $context['utf8'] ? ($context['server']['complex_preg_chars'] ? '\x{C2A0}' : chr(0xC2) . chr(0xA0)) : '\xA0';

  $pos = -1;
  while ($pos !== false) {
    $last_pos = isset($last_pos) ? max($pos, $last_pos) : $pos;
    $pos = strpos($message, '[', $pos + 1);

    // Failsafe.
    if ($pos === false || $last_pos > $pos)
      $pos = strlen($message) + 1;

    // Can't have a one letter smiley, URL, or email! (sorry.)
    if ($last_pos < $pos - 1) {
      // We want to eat one less, and one more, character (for smileys.)
      $last_pos = max($last_pos - 1, 0);
      $data = substr($message, $last_pos, $pos - $last_pos + 1);

      // Take care of some HTML!
      if (!empty($modSettings['enablePostHTML']) && strpos($data, '&lt;') !== false) {
        $data = preg_replace('~&lt;a\s+href=((?:&quot;)?)((?:https?://|ftps?://|mailto:)\S+?)\\1&gt;~i', '[url=$2]', $data);
        $data = preg_replace('~&lt;/a&gt;~i', '[/url]', $data);

        // <br /> should be empty.
        $empty_tags = array('br', 'hr');
        foreach ($empty_tags as $tag)
          $data = str_replace(array('&lt;' . $tag . '&gt;', '&lt;' . $tag . '/&gt;', '&lt;' . $tag . ' /&gt;'), '[' . $tag . ' /]', $data);

        // b, u, i, s, pre... basic tags.
        $closable_tags = array('b', 'u', 'i', 's', 'em', 'ins', 'del', 'pre', 'blockquote');
        foreach ($closable_tags as $tag) {
          $diff = substr_count($data, '&lt;' . $tag . '&gt;') - substr_count($data, '&lt;/' . $tag . '&gt;');
          $data = strtr($data, array('&lt;' . $tag . '&gt;' => '<' . $tag . '>', '&lt;/' . $tag . '&gt;' => '</' . $tag . '>'));

          if ($diff > 0)
            $data .= str_repeat('</' . $tag . '>', $diff);
        }

        // Do <img ... /> - with security... action= -> action-.
        preg_match_all('~&lt;img\s+src=((?:&quot;)?)((?:https?://|ftps?://)\S+?)\\1(?:\s+alt=(&quot;.*?&quot;|\S*?))?(?:\s?/)?&gt;~i', $data, $matches, PREG_PATTERN_ORDER);
        if (!empty($matches[0])) {
          $replaces = array();
          foreach ($matches[2] as $match => $imgtag) {
            $alt = empty($matches[3][$match]) ? '' : ' alt=' . preg_replace('~^&quot;|&quot;$~', '', $matches[3][$match]);

            // Remove action= from the URL - no funny business, now.
            if (preg_match('~action(=|%3d)(?!dlattach)~i', $imgtag) != 0)
              $imgtag = preg_replace('~action(=|%3d)(?!dlattach)~i', 'action-', $imgtag);

            // Check if the image is larger than allowed.
            if (!empty($modSettings['max_image_width']) && !empty($modSettings['max_image_height'])) {
              list ($width, $height) = url_image_size($imgtag);

              if (!empty($modSettings['max_image_width']) && $width > $modSettings['max_image_width']) {
                $height = (int) (($modSettings['max_image_width'] * $height) / $width);
                $width = $modSettings['max_image_width'];
              }

              if (!empty($modSettings['max_image_height']) && $height > $modSettings['max_image_height']) {
                $width = (int) (($modSettings['max_image_height'] * $width) / $height);
                $height = $modSettings['max_image_height'];
              }

              // Set the new image tag.
              $replaces[$matches[0][$match]] = '<img src="' . $imgtag . '" width="' . $width . '" height="' . $height . '" alt="' . $matches[2][$match] . '" border="0" />';
            }
            else
              $replaces[$matches[0][$match]] = '<img src="' . $imgtag . '" alt="' . $matches[2][$match] . '" border="0" />';
          }

          $data = strtr($data, $replaces);
        }
      }

      if (!empty($modSettings['autoLinkUrls'])) {
        // Are we inside tags that should be auto linked?
        $no_autolink_area = false;
        if (!empty($open_tags)) {
          foreach ($open_tags as $open_tag)
            if (in_array($open_tag['tag'], $no_autolink_tags))
              $no_autolink_area = true;
        }

        // Don't go backwards.
        //!!! Don't think is the real solution....
        $lastAutoPos = isset($lastAutoPos) ? $lastAutoPos : 0;
        if ($pos < $lastAutoPos)
          $no_autolink_area = true;
        $lastAutoPos = $pos;

        if (!$no_autolink_area) {
          // Parse any URLs.... have to get rid of the @ problems some things cause... stupid email addresses.
          if (!isset($disabled['url']) && (strpos($data, '://') !== false || strpos($data, 'www.') !== false)) {
            // Switch out quotes really quick because they can cause problems.
            $data = strtr($data, array('&#039;' => '\'', '&nbsp;' => $context['utf8'] ? "\xC2\xA0" : "\xA0", '&quot;' => '>">', '"' => '<"<', '&lt;' => '<lt<'));

            // Only do this if the preg survives.
            if (is_string($result = preg_replace(array(
              '~(?<=[\s>\.(;\'"]|^)((?:http|https|ftp|ftps)://[\w\-_%@:|]+(?:\.[\w\-_%]+)*(?::\d+)?(?:/[\w\-_\~%\.@,\?&;=#(){}+:\'\\\\]*)*[/\w\-_\~%@\?;=#}\\\\])~i',
              '~(?<=[\s>(\'<]|^)(www(?:\.[\w\-_]+)+(?::\d+)?(?:/[\w\-_\~%\.@,\?&;=#(){}+:\'\\\\]*)*[/\w\-_\~%@\?;=#}\\\\])~i'
            ), array(
              '[url]$1[/url]',
              '[url=http://$1]$1[/url]'
            ), $data)))
              $data = $result;

            $data = strtr($data, array('\'' => '&#039;', $context['utf8'] ? "\xC2\xA0" : "\xA0" => '&nbsp;', '>">' => '&quot;', '<"<' => '"', '<lt<' => '&lt;'));
          }

          // Next, emails...
          if (!isset($disabled['email']) && strpos($data, '@') !== false) {
            $data = preg_replace('~(?<=[\?\s' . $non_breaking_space . '\[\]()*\\\;>]|^)([\w\-\.]{1,80}@[\w\-]+\.[\w\-\.]+[\w\-])(?=[?,\s' . $non_breaking_space . '\[\]()*\\\]|$|<br />|&nbsp;|&gt;|&lt;|&quot;|&#039;|\.(?:\.|;|&nbsp;|\s|$|<br />))~' . ($context['utf8'] ? 'u' : ''), '[email]$1[/email]', $data);
            $data = preg_replace('~(?<=<br />)([\w\-\.]{1,80}@[\w\-]+\.[\w\-\.]+[\w\-])(?=[?\.,;\s' . $non_breaking_space . '\[\]()*\\\]|$|<br />|&nbsp;|&gt;|&lt;|&quot;|&#039;)~' . ($context['utf8'] ? 'u' : ''), '[email]$1[/email]', $data);
          }
        }
      }

      $data = strtr($data, array("\t" => '&nbsp;&nbsp;&nbsp;'));

      if (!empty($modSettings['fixLongWords']) && $modSettings['fixLongWords'] > 5) {
        // This is SADLY and INCREDIBLY browser dependent.
        if ($context['browser']['is_gecko'] || $context['browser']['is_konqueror'])
          $breaker = '<span style="margin: 0 -0.5ex 0 0;"> </span>';
        // Opera...
        else if ($context['browser']['is_opera'])
          $breaker = '<span style="margin: 0 -0.65ex 0 -1px;"> </span>';
        // Internet Explorer...
        else
          $breaker = '<span style="width: 0; margin: 0 -0.6ex 0 -1px;"> </span>';

        // PCRE will not be happy if we don't give it a short.
        $modSettings['fixLongWords'] = (int) min(65535, $modSettings['fixLongWords']);

        // The idea is, find words xx long, and then replace them with xx + space + more.
        if (strlen($data) > $modSettings['fixLongWords']) {
          // This is done in a roundabout way because $breaker has "long words" :P.
          $data = strtr($data, array($breaker => '< >', '&nbsp;' => $context['utf8'] ? "\xC2\xA0" : "\xA0"));
          $data = preg_replace(
            '~(?<=[>;:!? ' . $non_breaking_space . '\]()]|^)([\w\.]{' . $modSettings['fixLongWords'] . ',})~e' . ($context['utf8'] ? 'u' : ''),
            "preg_replace('/(.{" . ($modSettings['fixLongWords'] - 1) . '})/' . ($context['utf8'] ? 'u' : '') . "', '\\\$1< >', '\$1')",
            $data);
          $data = strtr($data, array('< >' => $breaker, $context['utf8'] ? "\xC2\xA0" : "\xA0" => '&nbsp;'));
        }
      }

      // Do any smileys!
      if ($smileys === true)
        parsesmileys($data);

      // If it wasn't changed, no copying or other boring stuff has to happen!
      if ($data != substr($message, $last_pos, $pos - $last_pos + 1)) {
        $message = substr($message, 0, $last_pos) . $data . substr($message, $pos + 1);

        // Since we changed it, look again incase we added or removed a tag.  But we don't want to skip any.
        $old_pos = strlen($data) + $last_pos - 1;
        $pos = strpos($message, '[', $last_pos);
        $pos = $pos === false ? $old_pos : min($pos, $old_pos);
      }
    }

    // Are we there yet?  Are we there yet?
    if ($pos >= strlen($message) - 1)
      break;

    $tags = strtolower(substr($message, $pos + 1, 1));

    if ($tags == '/' && !empty($open_tags)) {
      $pos2 = strpos($message, ']', $pos + 1);
      if ($pos2 == $pos + 2)
        continue;
      $look_for = strtolower(substr($message, $pos + 2, $pos2 - $pos - 2));

      $to_close = array();
      $block_level = null;
      do {
        $tag = array_pop($open_tags);
        if (!$tag)
          break;

        if (!empty($tag['block_level'])) {
          // Only find out if we need to.
          if ($block_level === false) {
            array_push($open_tags, $tag);
            break;
          }

          // The idea is, if we are LOOKING for a block level tag, we can close them on the way.
          if (strlen($look_for) > 0 && isset($bbc_codes[$look_for{0}])) {
            foreach ($bbc_codes[$look_for{0}] as $temp)
              if ($temp['tag'] == $look_for) {
                $block_level = !empty($temp['block_level']);
                break;
              }
          }

          if ($block_level !== true) {
            $block_level = false;
            array_push($open_tags, $tag);
            break;
          }
        }

        $to_close[] = $tag;
      }
      while ($tag['tag'] != $look_for);

      // Did we just eat through everything and not find it?
      if ((empty($open_tags) && (empty($tag) || $tag['tag'] != $look_for))) {
        $open_tags = $to_close;
        continue;
      } else if (!empty($to_close) && $tag['tag'] != $look_for) {
        if ($block_level === null && isset($look_for{0}, $bbc_codes[$look_for{0}])) {
          foreach ($bbc_codes[$look_for{0}] as $temp)
            if ($temp['tag'] == $look_for) {
              $block_level = !empty($temp['block_level']);
              break;
            }
        }

        // We're not looking for a block level tag (or maybe even a tag that exists...)
        if (!$block_level) {
          foreach ($to_close as $tag)
            array_push($open_tags, $tag);
          continue;
        }
      }

      foreach ($to_close as $tag) {
        $message = substr($message, 0, $pos) . $tag['after'] . substr($message, $pos2 + 1);
        $pos += strlen($tag['after']);
        $pos2 = $pos - 1;

        // See the comment at the end of the big loop - just eating whitespace ;).
        if (!empty($tag['block_level']) && substr($message, $pos, 6) == '<br />')
          $message = substr($message, 0, $pos) . substr($message, $pos + 6);
        if (!empty($tag['trim']) && $tag['trim'] != 'inside' && preg_match('~(<br />|&nbsp;|\s)*~', substr($message, $pos), $matches) != 0)
          $message = substr($message, 0, $pos) . substr($message, $pos + strlen($matches[0]));
      }

      if (!empty($to_close)) {
        $to_close = array();
        $pos--;
      }

      continue;
    }

    // No tags for this character, so just keep going (fastest possible course.)
    if (!isset($bbc_codes[$tags]))
      continue;

    $inside = empty($open_tags) ? null : $open_tags[count($open_tags) - 1];
    $tag = null;

    foreach ($bbc_codes[$tags] as $possible) {
      // Not a match?
      if (strtolower(substr($message, $pos + 1, strlen($possible['tag']))) != $possible['tag'])
        continue;

      $next_c = substr($message, $pos + 1 + strlen($possible['tag']), 1);

      // A test validation?
      if (isset($possible['test']) && preg_match('~^' . $possible['test'] . '~', substr($message, $pos + 1 + strlen($possible['tag']) + 1)) == 0)
        continue;
      // Do we want parameters?
      else if (!empty($possible['parameters'])) {
        if ($next_c != ' ')
          continue;
      } else if (isset($possible['type'])) {
        // Do we need an equal sign?
        if (in_array($possible['type'], array('unparsed_equals', 'unparsed_commas', 'unparsed_commas_content', 'unparsed_equals_content', 'parsed_equals')) && $next_c != '=')
          continue;

        // Maybe we just want a /...
        if ($possible['type'] == 'closed' && $next_c != ']' && substr($message, $pos + 1 + strlen($possible['tag']), 2) != '/]' && substr($message, $pos + 1 + strlen($possible['tag']), 3) != ' /]')
          continue;

        // An immediate ]?
        if ($possible['type'] == 'unparsed_content' && $next_c != ']')
          continue;
      }
      // No type means 'parsed_content', which demands an immediate ] without parameters!
      else if ($next_c != ']')
        continue;

      // Check allowed tree?
      if (isset($possible['require_parents']) && ($inside === null || !in_array($inside['tag'], $possible['require_parents'])))
        continue;
      else if (isset($inside['require_children']) && !in_array($possible['tag'], $inside['require_children']))
        continue;
      else if (isset($inside['disallow_children']) && in_array($possible['tag'], $inside['disallow_children']))
        continue;

      $pos1 = $pos + 1 + strlen($possible['tag']) + 1;
      if (!empty($possible['parameters'])) {
        $preg = array();

        foreach ($possible['parameters'] as $p => $info)
          $preg[] = '(\s+' . $p . '=' . (empty($info['quoted']) ? '' : '&quot;') . (isset($info['match']) ? $info['match'] : '(.+?)') . (empty($info['quoted']) ? '' : '&quot;') . ')' . (empty($info['optional']) ? '' : '?');

        $match = false;
        $orders = permute($preg);

        foreach ($orders as $p)
          if (preg_match('~^' . implode('', $p) . '\]~i', substr($message, $pos1 - 1), $matches) != 0) {
            $match = true;
            break;
          }

        if (!$match)
          continue;

        $params = array();

        for ($i = 1, $n = count($matches); $i < $n; $i += 2) {
          $key = strtok(ltrim($matches[$i]), '=');

          if (isset($possible['parameters'][$key]['value']))
            $params['{' . $key . '}'] = strtr($possible['parameters'][$key]['value'], array('$1' => $matches[$i + 1]));
          else if (isset($possible['parameters'][$key]['validate']))
            $params['{' . $key . '}'] = $possible['parameters'][$key]['validate']($matches[$i + 1]);
          else
            $params['{' . $key . '}'] = $matches[$i + 1];

          // Just to make sure: replace any $ or { so they can't interpolate wrongly.
          $params['{' . $key . '}'] = strtr($params['{' . $key . '}'], array('$' => '&#036;', '{' => '&#123;'));
        }

        foreach ($possible['parameters'] as $p => $info) {
          if (!isset($params['{' . $p . '}']))
            $params['{' . $p . '}'] = '';
        }

        $tag = $possible;

        // Put the parameters into the string.
        if (isset($tag['before']))
          $tag['before'] = strtr($tag['before'], $params);
        if (isset($tag['after']))
          $tag['after'] = strtr($tag['after'], $params);
        if (isset($tag['content']))
          $tag['content'] = strtr($tag['content'], $params);

        $pos1 += strlen($matches[0]) - 1;
      }
      else
        $tag = $possible;
      break;
    }

    // Item codes are complicated buggers... they are implicit [li]s and can make [list]s!
    if ($smileys !== false && $tag === null && isset($itemcodes[substr($message, $pos + 1, 1)]) && substr($message, $pos + 2, 1) == ']' && !isset($disabled['list']) && !isset($disabled['li'])) {
      if (substr($message, $pos + 1, 1) == '0' && !in_array(substr($message, $pos - 1, 1), array(';', ' ', "\t", '>')))
        continue;

      $tag = $itemcodes[substr($message, $pos + 1, 1)];

      // First let's set up the tree: it needs to be in a list, or after an li.
      if ($inside === null || ($inside['tag'] != 'list' && $inside['tag'] != 'li')) {
        $open_tags[] = array(
          'tag' => 'list',
          'after' => '</ul>',
          'block_level' => true,
          'require_children' => array('li'),
          'disallow_children' => isset($inside['disallow_children']) ? $inside['disallow_children'] : null,
        );
        $code = '<ul style="margin-top: 0; margin-bottom: 0;">';
      }
      // We're in a list item already: another itemcode?  Close it first.
      else if ($inside['tag'] == 'li') {
        array_pop($open_tags);
        $code = '</li>';
      }
      else
        $code = '';

      // Now we open a new tag.
      $open_tags[] = array(
        'tag' => 'li',
        'after' => '</li>',
        'trim' => 'outside',
        'block_level' => true,
        'disallow_children' => isset($inside['disallow_children']) ? $inside['disallow_children'] : null,
      );

      // First, open the tag...
      $code .= '<li' . ($tag == '' ? '' : ' type="' . $tag . '"') . '>';
      $message = substr($message, 0, $pos) . $code . substr($message, $pos + 3);
      $pos += strlen($code) - 1;

      // Next, find the next break (if any.)  If there's more itemcode after it, keep it going - otherwise close!
      $pos2 = strpos($message, '<br />', $pos);
      $pos3 = strpos($message, '[/', $pos);

      if ($pos2 !== false && ($pos2 <= $pos3 || $pos3 === false)) {
        preg_match('~^(<br />|&nbsp;|\s|\[)+~', substr($message, $pos2 + 6), $matches);
        $message = substr($message, 0, $pos2) . (!empty($matches[0]) && substr($matches[0], -1) == '[' ? '[/li]' : '[/li][/list]') . substr($message, $pos2);

        $open_tags[count($open_tags) - 2]['after'] = '</ul>';
      }
      // Tell the [list] that it needs to close specially.
      else {
        // Move the li over, because we're not sure what we'll hit.
        $open_tags[count($open_tags) - 1]['after'] = '';
        $open_tags[count($open_tags) - 2]['after'] = '</li></ul>';
      }

      continue;
    }

    // Implicitly close lists and tables if something other than what's required is in them.  This is needed for itemcode.
    if ($tag === null && $inside !== null && !empty($inside['require_children'])) {
      array_pop($open_tags);

      $message = substr($message, 0, $pos) . $inside['after'] . substr($message, $pos);
      $pos += strlen($inside['after']) - 1;
    }

    // No tag?  Keep looking, then.  Silly people using brackets without actual tags.
    if ($tag === null)
      continue;

    // Propagate the list to the child (so wrapping the disallowed tag won't work either.)
    if (isset($inside['disallow_children']))
      $tag['disallow_children'] = isset($tag['disallow_children']) ? array_unique(array_merge($tag['disallow_children'], $inside['disallow_children'])) : $inside['disallow_children'];

    // Is this tag disabled?
    if (isset($disabled[$tag['tag']])) {
      if (!isset($tag['disabled_before']) && !isset($tag['disabled_after']) && !isset($tag['disabled_content'])) {
        $tag['before'] = !empty($tag['block_level']) ? '<div>' : '';
        $tag['after'] = !empty($tag['block_level']) ? '</div>' : '';
        $tag['content'] = isset($tag['type']) && $tag['type'] == 'closed' ? '' : (!empty($tag['block_level']) ? '<div>$1</div>' : '$1');
      } else if (isset($tag['disabled_before']) || isset($tag['disabled_after'])) {
        $tag['before'] = isset($tag['disabled_before']) ? $tag['disabled_before'] : (!empty($tag['block_level']) ? '<div>' : '');
        $tag['after'] = isset($tag['disabled_after']) ? $tag['disabled_after'] : (!empty($tag['block_level']) ? '</div>' : '');
      }
      else
        $tag['content'] = $tag['disabled_content'];
    }

    // The only special case is 'html', which doesn't need to close things.
    if (!empty($tag['block_level']) && $tag['tag'] != 'html' && empty($inside['block_level'])) {
      $n = count($open_tags) - 1;
      while (empty($open_tags[$n]['block_level']) && $n >= 0)
        $n--;

      // Close all the non block level tags so this tag isn't surrounded by them.
      for ($i = count($open_tags) - 1; $i > $n; $i--) {
        $message = substr($message, 0, $pos) . $open_tags[$i]['after'] . substr($message, $pos);
        $pos += strlen($open_tags[$i]['after']);
        $pos1 += strlen($open_tags[$i]['after']);

        // Trim or eat trailing stuff... see comment at the end of the big loop.
        if (!empty($open_tags[$i]['block_level']) && substr($message, $pos, 6) == '<br />')
          $message = substr($message, 0, $pos) . substr($message, $pos + 6);

        if (!empty($open_tags[$i]['trim']) && $tag['trim'] != 'inside' && preg_match('~(<br />|&nbsp;|\s)*~', substr($message, $pos), $matches) != 0)
          $message = substr($message, 0, $pos) . substr($message, $pos + strlen($matches[0]));

        array_pop($open_tags);
      }
    }

    // No type means 'parsed_content'.
    if (!isset($tag['type'])) {
      // !!! Check for end tag first, so people can say "I like that [i] tag"?
      $open_tags[] = $tag;
      $message = substr($message, 0, $pos) . $tag['before'] . substr($message, $pos1);
      $pos += strlen($tag['before']) - 1;
    }
    // Don't parse the content, just skip it.
    else if ($tag['type'] == 'unparsed_content') {
      $pos2 = stripos($message, '[/' . substr($message, $pos + 1, strlen($tag['tag'])) . ']', $pos1);
      if ($pos2 === false)
        continue;

      $data = substr($message, $pos1, $pos2 - $pos1);

      if (!empty($tag['block_level']) && substr($data, 0, 6) == '<br />')
        $data = substr($data, 6);

      if (isset($tag['validate']))
        $tag['validate']($tag, $data, $disabled);

      $code = strtr($tag['content'], array('$1' => $data));
      $message = substr($message, 0, $pos) . $code . substr($message, $pos2 + 3 + strlen($tag['tag']));
      $pos += strlen($code) - 1;
    }
    // Don't parse the content, just skip it.
    else if ($tag['type'] == 'unparsed_equals_content') {
      // The value may be quoted for some tags - check.
      if (isset($tag['quoted'])) {
        $quoted = substr($message, $pos1, 6) == '&quot;';
        if ($tag['quoted'] != 'optional' && !$quoted)
          continue;

        if ($quoted)
          $pos1 += 6;
      }
      else
        $quoted = false;

      $pos2 = strpos($message, $quoted == false ? ']' : '&quot;]', $pos1);

      if ($pos2 === false)
        continue;

      $pos3 = stripos($message, '[/' . substr($message, $pos + 1, strlen($tag['tag'])) . ']', $pos2);

      if ($pos3 === false)
        continue;

      $data = array(
        substr($message, $pos2 + ($quoted == false ? 1 : 7), $pos3 - ($pos2 + ($quoted == false ? 1 : 7))),
        substr($message, $pos1, $pos2 - $pos1)
      );

      if (!empty($tag['block_level']) && substr($data[0], 0, 6) == '<br />')
        $data[0] = substr($data[0], 6);

      // Validation for my parking, please!
      if (isset($tag['validate']))
        $tag['validate']($tag, $data, $disabled);

      $code = strtr($tag['content'], array('$1' => $data[0], '$2' => $data[1]));
      $message = substr($message, 0, $pos) . $code . substr($message, $pos3 + 3 + strlen($tag['tag']));
      $pos += strlen($code) - 1;
    }
    // A closed tag, with no content or value.
    else if ($tag['type'] == 'closed') {
      $pos2 = strpos($message, ']', $pos);
      $message = substr($message, 0, $pos) . $tag['content'] . substr($message, $pos2 + 1);
      $pos += strlen($tag['content']) - 1;
    }
    // This one is sorta ugly... :/.  Unforunately, it's needed for flash.
    else if ($tag['type'] == 'unparsed_commas_content') {
      $pos2 = strpos($message, ']', $pos1);

      if ($pos2 === false)
        continue;

      $pos3 = stripos($message, '[/' . substr($message, $pos + 1, strlen($tag['tag'])) . ']', $pos2);

      if ($pos3 === false)
        continue;

      // We want $1 to be the content, and the rest to be csv.
      $data = explode(',', ',' . substr($message, $pos1, $pos2 - $pos1));
      $data[0] = substr($message, $pos2 + 1, $pos3 - $pos2 - 1);

      if (isset($tag['validate']))
        $tag['validate']($tag, $data, $disabled);

      $code = $tag['content'];

      foreach ($data as $k => $d)
        $code = strtr($code, array('$' . ($k + 1) => trim($d)));

      $message = substr($message, 0, $pos) . $code . substr($message, $pos3 + 3 + strlen($tag['tag']));
      $pos += strlen($code) - 1;
    }
    // This has parsed content, and a csv value which is unparsed.
    else if ($tag['type'] == 'unparsed_commas') {
      $pos2 = strpos($message, ']', $pos1);
      if ($pos2 === false)
        continue;

      $data = explode(',', substr($message, $pos1, $pos2 - $pos1));

      if (isset($tag['validate']))
        $tag['validate']($tag, $data, $disabled);

      // Fix after, for disabled code mainly.
      foreach ($data as $k => $d)
        $tag['after'] = strtr($tag['after'], array('$' . ($k + 1) => trim($d)));

      $open_tags[] = $tag;

      // Replace them out, $1, $2, $3, $4, etc.
      $code = $tag['before'];
      foreach ($data as $k => $d)
        $code = strtr($code, array('$' . ($k + 1) => trim($d)));
      $message = substr($message, 0, $pos) . $code . substr($message, $pos2 + 1);
      $pos += strlen($code) - 1;
    }
    // A tag set to a value, parsed or not.
    else if ($tag['type'] == 'unparsed_equals' || $tag['type'] == 'parsed_equals') {
      // The value may be quoted for some tags - check.
      if (isset($tag['quoted'])) {
        $quoted = substr($message, $pos1, 6) == '&quot;';

        if ($tag['quoted'] != 'optional' && !$quoted)
          continue;

        if ($quoted)
          $pos1 += 6;
      }
      else
        $quoted = false;

      $pos2 = strpos($message, $quoted == false ? ']' : '&quot;]', $pos1);

      if ($pos2 === false)
        continue;

      $data = substr($message, $pos1, $pos2 - $pos1);

      // Validation for my parking, please!
      if (isset($tag['validate']))
        $tag['validate']($tag, $data, $disabled);

      // For parsed content, we must recurse to avoid security problems.
      if ($tag['type'] != 'unparsed_equals')
        $data = parse_bbc($data);

      $tag['after'] = strtr($tag['after'], array('$1' => $data));

      $open_tags[] = $tag;

      $code = strtr($tag['before'], array('$1' => $data));
      $message = substr($message, 0, $pos) . $code . substr($message, $pos2 + ($quoted == false ? 1 : 7));
      $pos += strlen($code) - 1;
    }

    // If this is block level, eat any breaks after it.
    if (!empty($tag['block_level']) && substr($message, $pos + 1, 6) == '<br />')
      $message = substr($message, 0, $pos + 1) . substr($message, $pos + 7);

    // Are we trimming outside this tag?
    if (!empty($tag['trim']) && $tag['trim'] != 'outside' && preg_match('~(<br />|&nbsp;|\s)*~', substr($message, $pos + 1), $matches) != 0)
      $message = substr($message, 0, $pos + 1) . substr($message, $pos + 1 + strlen($matches[0]));
  }

  // Close any remaining tags.
  while ($tag = array_pop($open_tags))
    $message .= $tag['after'];

  if (substr($message, 0, 1) == ' ')
    $message = '&nbsp;' . substr($message, 1);

  // Cleanup whitespace.
  $message = strtr($message, array('  ' => ' &nbsp;', "\r" => '', "\n" => '<br />', '<br /> ' => '<br />&nbsp;', '&#13;' => "\n"));

  //Clean up some missing removed hide close tags...
  if (preg_match("/\[\/hide\]/i", $message) != 0)
    $message = preg_replace("/\[\/hide\]/i", '', $message);

  // Cache the output if it took some time...
  if (isset($cache_key, $cache_t) && array_sum(explode(' ', microtime())) - array_sum(explode(' ', $cache_t)) > 0.05)
    cache_put_data($cache_key, $message, 240);

  return $message;
}

function parse_bbc2($message, $smileys = true, $cache_id = '') {
  global $txt, $context, $modSettings, $user_info;
  static $disabled;

  // Never show smileys for wireless clients.  More bytes, can't see it anyway :P.
  if ($smileys !== null && ($smileys == '1' || $smileys == '0'))
    $smileys = (bool) $smileys;

    if ($smileys === true)
      parsesmileys($message);

    return $message;

  // Just in case it wasn't determined yet whether UTF-8 is enabled.
  if (!isset($context['utf8']))
    $context['utf8'] = (empty($modSettings['global_character_set']) ? $txt['lang_character_set'] : $modSettings['global_character_set']) === 'UTF-8';

  // Shall we take the time to cache this?
  if ($cache_id != '' && !empty($modSettings['cache_enable']) && (($modSettings['cache_enable'] >= 2 && strlen($message) > 1000) || strlen($message) > 2400)) {
    // It's likely this will change if the message is modified.
    $cache_key = 'parse:' . $cache_id . '-' . md5(md5($message) . '-' . $smileys . (empty($disabled) ? '' : implode(',', array_keys($disabled))) . serialize($context['browser']) . $txt['lang_locale'] . $user_info['time_offset'] . $user_info['time_format']);

    if (($temp = cache_get_data($cache_key, 240)) != null)
      return $temp;

    $cache_t = microtime();
  }

  $open_tags = array();
  $message = strtr($message, array("\n" => '<br />'));

  // The non-breaking-space looks a bit different each time.
  $non_breaking_space = $context['utf8'] ? ($context['server']['complex_preg_chars'] ? '\x{C2A0}' : chr(0xC2) . chr(0xA0)) : '\xA0';

  // Close any remaining tags.
  while ($tag = array_pop($open_tags))
    $message .= $tag['after'];

  if (substr($message, 0, 1) == ' ')
    $message = '&nbsp;' . substr($message, 1);

  // Cleanup whitespace.
  $message = strtr($message, array('  ' => ' &nbsp;', "\r" => '', "\n" => '<br />', '<br /> ' => '<br />&nbsp;', '&#13;' => "\n"));


  // Cache the output if it took some time...
  if (isset($cache_key, $cache_t) && array_sum(explode(' ', microtime())) - array_sum(explode(' ', $cache_t)) > 0.05)
    cache_put_data($cache_key, $message, 240);

  return $message;
}

// Parse smileys in the passed message.
function parsesmileys(&$message) {
  global $modSettings, $db_prefix, $txt, $user_info, $context;
  static $smileyfromcache = array(), $smileytocache = array();

  // No smiley set at all?!
  if ($user_info['smiley_set'] == 'none')
    return;

  // If the smiley array hasn't been set, do it now.
  if (empty($smileyfromcache)) {
    // Use the default smileys if it is disabled. (better for "portability" of smileys.)
    if (empty($modSettings['smiley_enable'])) {
      $smileysfrom = array('>:D', ':D', '::)', '>:(', ':)', ';)', ';D', ':(', ':o', '8)', ':P', '???', ':-[', ':-X', ':-*', ':\'(', ':-\\', '^-^', 'O0', 'C:-)', '0:)');
      $smileysto = array('evil.gif', 'cheesy.gif', 'rolleyes.gif', 'angry.gif', 'smiley.gif', 'wink.gif', 'grin.gif', 'sad.gif', 'shocked.gif', 'cool.gif', 'tongue.gif', 'huh.gif', 'embarrassed.gif', 'lipsrsealed.gif', 'kiss.gif', 'cry.gif', 'undecided.gif', 'azn.gif', 'afro.gif', 'police.gif', 'angel.gif');
      $smileysdescs = array('', $txt[289], $txt[450], $txt[288], $txt[287], $txt[292], $txt[293], $txt[291], $txt[294], $txt[295], $txt[451], $txt[296], $txt[526], $txt[527], $txt[529], $txt[530], $txt[528], '', '', '', '');
    } else {
      // Load the smileys in reverse order by length so they don't get parsed wrong.
      if (($temp = cache_get_data('parsing_smileys', 480)) == null) {
        $result = db_query("
          SELECT code, filename, description
          FROM {$db_prefix}smileys", __FILE__, __LINE__);
        $smileysfrom = array();
        $smileysto = array();
        $smileysdescs = array();

        while ($row = mysqli_fetch_assoc($result)) {
          $smileysfrom[] = $row['code'];
          $smileysto[] = $row['filename'];
          $smileysdescs[] = $row['description'];
        }

        mysqli_free_result($result);

        cache_put_data('parsing_smileys', array($smileysfrom, $smileysto, $smileysdescs), 480);
      }
      else
        list($smileysfrom, $smileysto, $smileysdescs) = $temp;
    }

    // The non-breaking-space is a complex thing...
    $non_breaking_space = $context['utf8'] ? ($context['server']['complex_preg_chars'] ? '\x{A0}' : pack('C*', 0xC2, 0xA0)) : '\xA0';

    // This smiley regex makes sure it doesn't parse smileys within code tags (so [url=mailto:David@bla.com] doesn't parse the :D smiley)
    for ($i = 0, $n = count($smileysfrom); $i < $n; $i++) {
      $smileyfromcache[] = '/(?<=[>:\?\.\s' . $non_breaking_space . '[\]()*\\\;]|^)(' . preg_quote($smileysfrom[$i], '/') . '|' . preg_quote(htmlspecialchars($smileysfrom[$i], ENT_QUOTES), '/') . ')(?=[^[:alpha:]0-9]|$)/' . ($context['utf8'] ? 'u' : '');
      // Escape a bunch of smiley-related characters in the description so it doesn't get a double dose :P.
      $smileytocache[] = '<img src="' . htmlspecialchars($modSettings['smileys_url'] . '/' . $user_info['smiley_set'] . '/' . $smileysto[$i]) . '" alt="' . strtr(htmlspecialchars($smileysdescs[$i]), array(':' => '&#58;', '(' => '&#40;', ')' => '&#41;', '$' => '&#36;', '[' => '&#091;')) . '" border="0" />';
    }
  }

  // Replace away!
  // !!! There must be a way to speed this up.
  $message = preg_replace($smileyfromcache, $smileytocache, $message);
}

// Highlight any code...
function highlight_php_code($code) {
  // Remove special characters.
  $code = un_htmlspecialchars(strtr($code, array('<br />' => "\n", "\t" => 'SMF_TAB();', '&#91;' => '[')));

  $oldlevel = error_reporting(0);

  // It's easier in 4.2.x+.
  if (@version_compare(PHP_VERSION, '4.2.0') == -1) {
    ob_start();
    @highlight_string($code);
    $buffer = str_replace(array("\n", "\r"), '', ob_get_contents());
    ob_end_clean();
  }
  else
    $buffer = str_replace(array("\n", "\r"), '', @highlight_string($code, true));

  error_reporting($oldlevel);

  // Yes, I know this is kludging it, but this is the best way to preserve tabs from PHP :P.
  $buffer = preg_replace('~SMF_TAB(</(font|span)><(font color|span style)="[^"]*?">)?\(\);~', "<pre style=\"display: inline;\">\t</pre>", $buffer);

  return strtr($buffer, array('\'' => '&#039;', '<code>' => '', '</code>' => ''));
}

// Put this user in the online log.
function writeLog($force = false) {
  global $db_prefix, $ID_MEMBER, $user_info, $user_settings, $sc, $modSettings, $settings, $topic, $board;

  // If we are showing who is viewing a topic, let's see if we are, and force an update if so - to make it accurate.
  if (!empty($settings['display_who_viewing']) && ($topic || $board)) {
    // Take the opposite approach!
    $force = true;

    // Don't update for every page - this isn't wholly accurate but who cares.
    if ($topic)  {
      if (isset($_SESSION['last_topic_id']) && $_SESSION['last_topic_id'] == $topic)
        $force = false;

      $_SESSION['last_topic_id'] = $topic;
    }
  }

  // Don't mark them as online more than every so often.
  if (!empty($_SESSION['log_time']) && $_SESSION['log_time'] >= (time() - 8) && !$force)
    return;

  if (!empty($modSettings['who_enabled'])) {
    $serialized = $_GET + array('USER_AGENT' => $_SERVER['HTTP_USER_AGENT']);
    unset($serialized['sesc']);
    $serialized = addslashes(serialize($serialized));
  }
  else
    $serialized = '';

  $session_id = $user_info['is_guest'] ? 'ip' . $user_info['ip'] : session_id();
  $do_delete = cache_get_data('log_online-update', 10) < time() - 10;

  if (!empty($_SESSION['log_time']) && $_SESSION['log_time'] >= time() - $modSettings['lastActive'] * 20) {
    if ($do_delete) {
      db_query("
        DELETE FROM {$db_prefix}log_online
        WHERE logTime < NOW() - INTERVAL " . ($modSettings['lastActive'] * 60) . " SECOND
        AND session != '$session_id'", __FILE__, __LINE__);

      cache_put_data('log_online-update', time(), 10);

      db_query("
        UPDATE {$db_prefix}members SET isOnline = 0
        WHERE ID_MEMBER = $ID_MEMBER
        LIMIT 1", __FILE__, __LINE__);
    }

    db_query("
      UPDATE {$db_prefix}log_online
      SET logTime = NOW(), ip = IFNULL(INET_ATON('$user_info[ip]'), 0), url = '$serialized'
      WHERE session = '$session_id'
      LIMIT 1", __FILE__, __LINE__);

    // Guess it got deleted.
    if (db_affected_rows() == 0)
      $_SESSION['log_time'] = 0;
  }
  else
    $_SESSION['log_time'] = 0;

  // Otherwise, we have to delete and insert.
  if (empty($_SESSION['log_time'])) {
    if ($do_delete || !empty($ID_MEMBER))
      db_query("
        DELETE FROM {$db_prefix}log_online
        WHERE " . ($do_delete ? "logTime < NOW() - INTERVAL " . ($modSettings['lastActive'] * 60) . ' SECOND' : '') . ($do_delete && !empty($ID_MEMBER) ? ' OR ' : '') . (empty($ID_MEMBER) ? '' : "ID_MEMBER = $ID_MEMBER"), __FILE__, __LINE__);

      db_query("
        UPDATE {$db_prefix}members SET isOnline = 1
        WHERE ID_MEMBER = $ID_MEMBER
        LIMIT 1", __FILE__, __LINE__);

    db_query("
      " . ($do_delete ? 'INSERT IGNORE' : 'REPLACE') . " INTO {$db_prefix}log_online
        (session, ID_MEMBER, logTime, ip, url)
      VALUES ('$session_id', $ID_MEMBER, NOW(), IFNULL(INET_ATON('$user_info[ip]'), 0), '$serialized')", __FILE__, __LINE__);

      db_query("
        UPDATE {$db_prefix}members SET isOnline = 1
        WHERE ID_MEMBER = $ID_MEMBER
        LIMIT 1", __FILE__, __LINE__);
  }

  // Mark your session as being logged.
  $_SESSION['log_time'] = time();

  // Well, they are online now.
  if (empty($_SESSION['timeOnlineUpdated']))
    $_SESSION['timeOnlineUpdated'] = time();

  if (SMF != 'SSI' && !empty($user_info['last_login']) && $user_info['last_login'] < time() - 60) {
    // Don't count longer than 15 minutes.
    if (time() - $_SESSION['timeOnlineUpdated'] > 60 * 15)
      $_SESSION['timeOnlineUpdated'] = time();

    $user_settings['totalTimeLoggedIn'] += time() - $_SESSION['timeOnlineUpdated'];
    updateMemberData($ID_MEMBER, array('lastLogin' => time(), 'memberIP' => '\'' . $user_info['ip'] . '\'', 'memberIP2' => '\'' . $_SERVER['BAN_CHECK_IP'] . '\'', 'totalTimeLoggedIn' => $user_settings['totalTimeLoggedIn']));

    if (!empty($modSettings['cache_enable']) && $modSettings['cache_enable'] >= 2)
      cache_put_data('user_settings-' . $ID_MEMBER, $user_settings, 60);

    $user_info['total_time_logged_in'] += time() - $_SESSION['timeOnlineUpdated'];
    $_SESSION['timeOnlineUpdated'] = time();
  }
}

// Make sure the browser doesn't come back and repost the form data.  Should be used whenever anything is posted.
function redirectexit($setLocation = '', $refresh = false) {
  global $scripturl, $context, $modSettings, $db_show_debug;

  $add = preg_match('~^(ftp|http)[s]?://~', $setLocation) == 0 && substr($setLocation, 0, 6) != 'about:';

  if (WIRELESS) {
    // Add the scripturl on if needed.
    if ($add)
      $setLocation = $scripturl . '?' . $setLocation;

    $char = strpos($setLocation, '?') === false ? '?' : ';';

    if (strpos($setLocation, '#') ==! false)
      $setLocation = strtr($setLocation, array('#' => $char . WIRELESS_PROTOCOL . '#'));
    else
      $setLocation .= $char . WIRELESS_PROTOCOL;
  }
  else if ($add)
    $setLocation = $scripturl . ($setLocation != '' ? '?' . $setLocation : '');

  // Put the session ID in.
  if (defined('SID') && SID != '')
    $setLocation = preg_replace('/^' . preg_quote($scripturl, '/') . '(?!\?' . preg_quote(SID, '/') . ')(\?)?/', $scripturl . '?' . SID . ';', $setLocation);
  // Keep that debug in their for template debugging!
  else if (isset($_GET['debug']))
    $setLocation = preg_replace('/^' . preg_quote($scripturl, '/') . '(\?)?/', $scripturl . '?debug;', $setLocation);

  if (!empty($modSettings['queryless_urls']) && (empty($context['server']['is_cgi']) || @ini_get('cgi.fix_pathinfo') == 1) && !empty($context['server']['is_apache'])) {
    if (defined('SID') && SID != '')
      $setLocation = preg_replace('/^' . preg_quote($scripturl, '/') . '\?(?:' . SID . ';)((?:board|topic)=[^#]+?)(#[^"]*?)?$/e', "\$scripturl . '/' . strtr('\$1', '&;=', '//,') . '.html\$2?' . SID", $setLocation);
    else
      $setLocation = preg_replace('/^' . preg_quote($scripturl, '/') . '\?((?:board|topic)=[^#"]+?)(#[^"]*?)?$/e', "\$scripturl . '/' . strtr('\$1', '&;=', '//,') . '.html\$2'", $setLocation);
  }

  if (isset($modSettings['integrate_redirect']) && function_exists($modSettings['integrate_redirect']))
    $modSettings['integrate_redirect']($setLocation, $refresh);

  // We send a Refresh header only in special cases because Location looks better. (and is quicker...)
  if ($refresh && !WIRELESS)
    header('Refresh: 0; URL=' . strtr($setLocation, array(' ' => '%20', ';' => '%3b')));
  else
    header('Location: ' . str_replace(' ', '%20', $setLocation));

  // Debugging.
  if (isset($db_show_debug) && $db_show_debug === true)
    $_SESSION['debug_redirect'] = &$GLOBALS['db_cache'];

  obExit(false);
}

// Ends execution.  Takes care of template loading and remembering the previous URL.
function obExit($header = null, $do_footer = null, $from_index = false) {
  global $context, $settings, $modSettings, $txt;
  static $header_done = false, $footer_done = false;

  // Clear out the stat cache.
  trackStats();

  $do_header = $header === null ? !$header_done : $header;
  if ($do_footer === null)
    $do_footer = $do_header;

  // Has the template/header been done yet?
  if ($do_header) {
    // Start up the session URL fixer.
    ob_start('ob_sessrewrite');

    // Just in case we have anything bad already in there...
    if ((isset($_REQUEST['debug']) || isset($_REQUEST['xml']) || (WIRELESS && WIRELESS_PROTOCOL == 'wap')) && in_array($txt['lang_locale'], array('UTF-8', 'ISO-8859-1')))
      ob_start('validate_unicode__recursive');

    if (!empty($settings['output_buffers']) && is_string($settings['output_buffers']))
      $buffers = explode(',', $settings['output_buffers']);
    else if (!empty($settings['output_buffers']))
      $buffers = $settings['output_buffers'];
    else
      $buffers = array();

    if (isset($modSettings['integrate_buffer']))
      $buffers = array_merge(explode(',', $modSettings['integrate_buffer']), $buffers);

    if (!empty($buffers))
      foreach ($buffers as $buffer_function) {
        if (function_exists(trim($buffer_function)))
          ob_start(trim($buffer_function));
      }

    // Display the screen in the logical order.
    template_header();
    $header_done = true;
  }

  if ($do_footer) {
    if (WIRELESS && !isset($context['sub_template']))
      fatal_lang_error('wireless_error_notyet', false);

    // Just show the footer, then.
    loadSubTemplate(isset($context['sub_template']) ? $context['sub_template'] : 'main');

    // Just so we don't get caught in an endless loop of errors from the footer...
    if (!$footer_done) {
      $footer_done = true;
      template_footer();

      // (since this is just debugging... it's okay that it's after </html>.)
      if (!isset($_REQUEST['xml']))
        db_debug_junk();
    }
  }

  // Remember this URL in case someone doesn't like sending HTTP_REFERER.
  if (strpos($_SERVER['REQUEST_URL'], 'action=dlattach') === false)
    $_SESSION['old_url'] = $_SERVER['REQUEST_URL'];

  // For session check verfication.... don't switch browsers...
  $_SESSION['USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];

  // Hand off the output to the portal, etc. we're integrated with.
  if (isset($modSettings['integrate_exit'], $context['template_layers']) && in_array('main', $context['template_layers']) && function_exists($modSettings['integrate_exit']))
    call_user_func($modSettings['integrate_exit'], $do_footer && !WIRELESS);

  // Don't exit if we're coming from index.php; that will pass through normally.
  if (!$from_index || WIRELESS)
    exit;
}

// Set up the administration sections.
function adminIndex($area) {
  global $txt, $context, $scripturl, $sc, $settings, $boardurl;

  // Load the language and templates....
  loadLanguage('Admin');
  loadTemplate('Admin');
  loadLanguage('Shop');

  // Admin area 'Main'.
  $context['admin_areas']['forum'] = array(
    'title' => $txt[427],
    'areas' => array(
      'index' => '<a href="' . $boardurl . '/admin/">' . $txt[208] . '</a>',
    )
  );
  
  if (allowedTo('manage_bans'))
    $context['admin_areas']['forum']['areas']['ban_members'] = '<a href="' . $scripturl . '?action=ban">' . $txt['ban_title'] . '</a>';
    
  if (allowedTo('manage_bans')) {
    $context['admin_areas']['forum']['areas']['denuncias'] = '<a href="' . $boardurl . '/admin-denuncias/">Denuncias</a>';

    if (allowedTo(array('manage_boards', 'admin_forum', 'manage_smileys', 'manage_membergroups', 'manage_permissions'))) {
      $context['admin_areas']['forum']['areas']['publicity'] = '<a href="' . $scripturl . '?action=publicity">' . $txt['publicity'] . '</a>';
      $context['admin_areas']['forum']['areas']['edit_mods_settings'] = '<a href="' . $scripturl . '?action=featuresettings">' . $txt['modSettings_title'] . '</a>';
      $context['admin_areas']['forum']['areas']['news'] = '<a href="' . $boardurl . '/noticias/">' . $txt['news_title'] . '</a>';
      $context['admin_areas']['forum']['areas']['edit_settings'] = '<a href="' . $scripturl . '?action=serversettings;sesc=' . $sc . '">' . $txt[222] . '</a>';
      $context['admin_areas']['forum']['areas']['articles_settings'] = '<a href="' . $scripturl . '?action=articles;sa=admin;sesc=' . $sc . '">' . $txt['smfarticles_admin'] . '</a>';
      $context['admin_areas']['forum']['areas']['tags_settings'] = '<a href="' . $scripturl . '?action=tags;sa=admin;sesc=' . $sc . '">' . $txt['smftags_admin'] . '</a>';
      $context['admin_areas']['forum']['areas']['edit_theme_settings'] = '<a href="' . $scripturl . '?action=theme;sa=settings;th=' . $settings['theme_id'] . ';sesc=' . $sc . '">' . $txt['theme_current_settings'] . '</a>';
      $context['admin_areas']['forum']['areas']['manage_themes'] = '<a href="' . $scripturl . '?action=theme;sa=admin;sesc=' . $sc . '">' . $txt['theme_admin'] . '</a>';
      $context['admin_areas']['forum']['areas']['maintain_forum'] = '<a href="' . $scripturl . '?action=maintain">' . $txt['maintain_title'] . '</a>';
      $context['admin_areas']['forum']['areas']['view_errors'] = '<a href="' . $scripturl . '?action=viewErrorLog;desc">' . $txt['errlog1'] . '</a>';
      $context['admin_areas']['forum']['areas']['shop_inventory'] = '<a href="' . $scripturl . '?action=shop_inventory">' . $txt['shop_admin_inventory'] . '</a>';
      $context['admin_areas']['forum']['areas']['shop_usergroup'] = '<a href="' . $scripturl . '?action=shop_usergroup">' . $txt['shop_admin_usergroup'] . '</a>';
      
      if (allowedTo('manage_boards'))
        $context['admin_areas']['forum']['areas']['manage_boards'] =  '<a href="' . $scripturl . '?action=manageboards">' . $txt[4] . '</a>';

      if (allowedTo(array('admin_forum', 'moderate_forum')))
        $context['admin_areas']['forum']['areas']['posts_and_topics'] = '<a href="' . $scripturl . '?action=postsettings">' . $txt['manageposts'] . '</a>';

      if (allowedTo('admin_forum')) {
        $context['admin_areas']['forum']['areas']['manage_search'] = '<a href="' . $scripturl . '?action=managesearch">' . $txt['manage_search'] . '</a>';
      }

      if (allowedTo('manage_smileys'))
        $context['admin_areas']['forum']['areas']['manage_smileys'] = '<a href="' . $scripturl . '?action=smileys">' . $txt['smileys_manage'] . '</a>';
        $context['allow_view_smfarticles'] = allowedTo('view_articles');
        
      if (allowedTo('moderate_forum'))
        $context['admin_areas']['forum']['areas']['view_members'] = '<a href="' . $scripturl . '?action=viewmembers">' . $txt[5] . '</a>';

      if (allowedTo('manage_membergroups'))
        $context['admin_areas']['forum']['areas']['edit_groups'] = '<a href="' . $scripturl . '?action=membergroups;">' . $txt[8] . '</a>';

      if (allowedTo('manage_permissions'))
        $context['admin_areas']['forum']['areas']['edit_permissions'] = '<a href="' . $scripturl . '?action=permissions">' . $txt['edit_permissions'] . '</a>';

      if (allowedTo(array('admin_forum', 'moderate_forum')))
        $context['admin_areas']['forum']['areas']['registration_center'] = '<a href="' . $scripturl . '?action=regcenter">' . $txt['registration_center'] . '</a>';
    }
  }

  validateSession();

  // Figure out which one we're in now...
  foreach ($context['admin_areas'] as $id => $section)
    if (isset($section[$area]))
      $context['admin_section'] = $id;

  $context['admin_area'] = $area;

  // obExit will know what to do!
  $context['template_layers'][] = 'admin';
}

// Track Statistics.
function trackStats($stats = array()) {
  global $db_prefix, $modSettings;
  static $cache_stats = array();

  if (empty($modSettings['trackStats']))
    return false;

  if (!empty($stats))
    return $cache_stats = array_merge($cache_stats, $stats);
  else if (empty($cache_stats))
    return false;

  $setStringUpdate = '';

  foreach ($cache_stats as $field => $change) {
    $setStringUpdate .= '
      ' . $field . ' = ' . ($change === '+' ? $field . ' + 1' : $change) . ',';

    if ($change === '+')
      $cache_stats[$field] = 1;
  }

  $date = strftime('%Y-%m-%d', forum_time(false));

  db_query("
    UPDATE {$db_prefix}log_activity
    SET" . substr($setStringUpdate, 0, -1) . "
    WHERE date = '$date'
    LIMIT 1", __FILE__, __LINE__);

  if (db_affected_rows() == 0) {
    db_query("
      INSERT IGNORE INTO {$db_prefix}log_activity
        (date, " . implode(', ', array_keys($cache_stats)) . ")
      VALUES ('$date', " . implode(', ', $cache_stats) . ')', __FILE__, __LINE__);
  }

  // Don't do this again.
  $cache_stats = array();

  return true;
}

// Make sure the user isn't posting over and over again.
function spamProtection($error_type) {
  global $modSettings, $db_prefix, $user_info;

  // Delete old entries... if you can moderate this board or this is login, override spamWaitTime with 2.
  if ($error_type == 'spam' && !allowedTo('moderate_board'))
    db_query("
      DELETE FROM {$db_prefix}log_floodcontrol
      WHERE logTime < " . (time() - $modSettings['spamWaitTime']), __FILE__, __LINE__);
  else
    db_query("
      DELETE FROM {$db_prefix}log_floodcontrol
      WHERE (logTime < " . (time() - 2) . " AND ip = '$user_info[ip]')
        OR logTime < " . (time() - $modSettings['spamWaitTime']), __FILE__, __LINE__);

  // Add a new entry, deleting the old if necessary.
  db_query("
    REPLACE INTO {$db_prefix}log_floodcontrol
      (ip, logTime)
    VALUES (SUBSTRING('$user_info[ip]', 1, 16), " . time() . ")", __FILE__, __LINE__);

  // If affected is 0 or 2, it was there already.
  if (db_affected_rows() != 1) {
    // Spammer!  You only have to wait a *few* seconds!
    fatal_lang_error($error_type . 'WaitTime_broken', false, array($modSettings['spamWaitTime']));
    return true;
  }

  // They haven't posted within the limit.
  return false;
}

// Get the size of a specified image with better error handling.
function url_image_size($url) {
  global $sourcedir;

  // Can we pull this from the cache... please please?
  if (($temp = cache_get_data('url_image_size-' . md5($url), 240)) !== null)
    return $temp;
  $t = microtime();

  // Get the host to pester...
  preg_match('~^\w+://(.+?)/(.*)$~', $url, $match);

  // Can't figure it out, just try the image size.
  if ($url == '' || $url == 'http://' || $url == 'https://')
    return false;
  else if (!isset($match[1]))
    $size = @getimagesize($url);
  else {
    $temp = 0;
    $fp = @fsockopen($match[1], 80, $temp, $temp, 0.5);
    if ($fp != false) {
      fwrite($fp, 'HEAD /' . $match[2] . ' HTTP/1.1' . "\r\n" . 'Host: ' . $match[1] . "\r\n" . 'User-Agent: PHP/SMF' . "\r\n" . 'Connection: close' . "\r\n\r\n");

      // Read in the HTTP/1.1 or whatever.
      $test = substr(fgets($fp, 11), -1);
      fclose($fp);

      // See if it returned a 404/403 or something.
      if ($test < 4) {
        $size = @getimagesize($url);

        // This probably means allow_url_fopen is off, let's try GD.
        if ($size === false && function_exists('imagecreatefromstring')) {
          include_once($sourcedir . '/Subs-Package.php');

          // It's going to hate us for doing this, but another request...
          $image = @imagecreatefromstring(fetch_web_data($url));
          if ($image !== false) {
            $size = array(imagesx($image), imagesy($image));
            imagedestroy($image);
          }
        }
      }
    }
  }

  // If we didn't get it, we failed.
  if (!isset($size))
    $size = false;

  // If this took a long time, we may never have to do it again, but then again we might...
  if (array_sum(explode(' ', microtime())) - array_sum(explode(' ', $t)) > 0.8)
    cache_put_data('url_image_size-' . md5($url), $size, 240);

  // Didn't work.
  return $size;
}

function determineTopicClass(&$topic_context) {
  // Set topic class depending on locked status and number of replies.
  if ($topic_context['is_very_hot'])
    $topic_context['class'] = 'veryhot';
  else if ($topic_context['is_hot'])
    $topic_context['class'] = 'hot';
  else
    $topic_context['class'] = 'normal';

  $topic_context['class'] .= $topic_context['is_poll'] ? '_poll' : '_post';

  if ($topic_context['is_locked'])
    $topic_context['class'] .= '_locked';

  if ($topic_context['is_sticky'])
    $topic_context['class'] .= '_sticky';

  // This is so old themes will still work.
  $topic_context['extended_class'] = &$topic_context['class'];
}

// Sets up the basic theme context stuff.
function setupThemeContext() {
  global $modSettings, $user_info, $scripturl, $context, $settings, $options, $txt, $maintenance, $boardurl;

  // Get some news...
  $context['news_lines'] = explode("\n", str_replace("\r", '', trim(addslashes($modSettings['news']))));
  $context['fader_news_lines'] = array();

  for ($i = 0, $n = count($context['news_lines']); $i < $n; $i++) {
    if (trim($context['news_lines'][$i]) == '')
      continue;

    // Clean it up for presentation ;).
    $context['news_lines'][$i] = parse_bbc(stripslashes(trim($context['news_lines'][$i])), true, 'news' . $i);

    // Gotta be special for the javascript.
    $context['fader_news_lines'][$i] = strtr(addslashes($context['news_lines'][$i]), array('/' => '\/', '<a href=' => '<a hre" + "f='));
  }

  $context['random_news_line'] = $context['news_lines'][mt_rand(0, count($context['news_lines']) - 1)];

  if (!$user_info['is_guest']) {
    $context['user']['messages'] = &$user_info['messages'];
    $context['user']['unread_messages'] = &$user_info['unread_messages'];

    // Personal message popup...
    if ($user_info['unread_messages'] > (isset($_SESSION['unread_messages']) ? $_SESSION['unread_messages'] : 0))
      $context['user']['popup_messages'] = true;
    else
      $context['user']['popup_messages'] = false;
    $_SESSION['unread_messages'] = $user_info['unread_messages'];

    if (allowedTo('moderate_forum'))
      $context['unapproved_members'] = !empty($modSettings['registration_method']) && $modSettings['registration_method'] == 2 ? $modSettings['unapprovedMembers'] : 0;

    $context['user']['avatar'] = array();

    // Full URL?
    if (substr($user_info['avatar']['url'], 0, 7) == 'http://') {
      $context['user']['avatar']['href'] = $user_info['avatar']['url'];

      if ($modSettings['avatar_action_too_large'] == 'option_html_resize' || $modSettings['avatar_action_too_large'] == 'option_js_resize') {
        if (!empty($modSettings['avatar_max_width_external']))
          $context['user']['avatar']['width'] = $modSettings['avatar_max_width_external'];

        if (!empty($modSettings['avatar_max_height_external']))
          $context['user']['avatar']['height'] = $modSettings['avatar_max_height_external'];
      }
    }
    // Otherwise we assume it's server stored?
    else if ($user_info['avatar']['url'] != '')
      $context['user']['avatar']['href'] = $modSettings['avatar_url'] . '/' . htmlspecialchars($user_info['avatar']['url']);

    if (!empty($context['user']['avatar']))
      $context['user']['avatar']['image'] = '<img src="' . $context['user']['avatar']['href'] . '"' . (isset($context['user']['avatar']['width']) ? ' width="' . $context['user']['avatar']['width'] . '"' : '') . (isset($context['user']['avatar']['height']) ? ' height="' . $context['user']['avatar']['height'] . '"' : '') . ' alt="" class="avatar" border="0" />';

    // Figure out how long they've been logged in.
    $context['user']['total_time_logged_in'] = array(
      'days' => floor($user_info['total_time_logged_in'] / 86400),
      'hours' => floor(($user_info['total_time_logged_in'] % 86400) / 3600),
      'minutes' => floor(($user_info['total_time_logged_in'] % 3600) / 60)
    );
  } else {
    $context['user']['messages'] = 0;
    $context['user']['unread_messages'] = 0;
    $context['user']['avatar'] = array();
    $context['user']['total_time_logged_in'] = array('days' => 0, 'hours' => 0, 'minutes' => 0);
    $context['user']['popup_messages'] = false;

    if (!empty($modSettings['registration_method']) && $modSettings['registration_method'] == 1)
      $txt['welcome_guest'] .= $txt['welcome_guest_activate'];

    // If we've upgraded recently, go easy on the passwords.
    if (!empty($modSettings['disableHashTime']) && ($modSettings['disableHashTime'] == 1 || time() < $modSettings['disableHashTime']))
      $context['disable_login_hashing'] = true;
    else if ($context['browser']['is_ie5'] || $context['browser']['is_ie5.5'])
      $context['disable_login_hashing'] = true;
  }

  // Set up the menu privileges.
  $context['allow_search'] = allowedTo('search_posts');
  $context['allow_admin'] = allowedTo(array('admin_forum', 'manage_boards', 'manage_permissions', 'moderate_forum', 'manage_membergroups', 'manage_bans', 'send_mail', 'edit_news', 'manage_smileys'));
  $context['allow_edit_profile'] = !$user_info['is_guest'] && allowedTo(array('profile_view_own', 'profile_view_any', 'profile_identity_own', 'profile_identity_any', 'profile_extra_own', 'profile_extra_any', 'profile_remove_own', 'profile_remove_any', 'moderate_forum', 'manage_membergroups'));
  $context['allow_memberlist'] = allowedTo('view_mlist');
  $context['allow_notepad'] = allowedTo('use_notepad') && !empty($modSettings['notepad_enable_use']);
  $context['allow_view_stafflist'] = allowedTo('view_stafflist');
  $context['allow_smfgallery_view'] = allowedTo('smfgallery_view');
  $context['allow_pm'] = allowedTo('pm_read');
  $context['in_maintenance'] = !empty($maintenance);
  $context['current_time'] = timeformat(time(), false);
  $context['current_action'] = isset($_GET['action']) ? $_GET['action'] : '';
  $context['show_quick_login'] = !empty($modSettings['enableVBStyleLogin']) && $user_info['is_guest'];

  if (empty($settings['theme_version']))
    $context['show_vBlogin'] = $context['show_quick_login'];

  // This is here because old index templates might still use it.
  $context['show_news'] = !empty($settings['enable_news']);

  // This is done to make it easier to add to all themes...
  if ($context['user']['popup_messages'] && !empty($options['popup_messages']) && (!isset($_REQUEST['action']) || $_REQUEST['action'] != 'pm')) {
    $context['html_headers'] .= '
  <script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[
    if (confirm("' . $txt['show_personal_messages'] . '"))
      window.open("' . $scripturl . '?action=pm");
  // ]]></script>';
  }

  // Resize avatars the fancy, but non-GD requiring way.
  if ($modSettings['avatar_action_too_large'] == 'option_js_resize' && (!empty($modSettings['avatar_max_width_external']) || !empty($modSettings['avatar_max_height_external']))) {
    $context['html_headers'] .= '
      <script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[
        var smf_avatarMaxWidth = ' . (int) $modSettings['avatar_max_width_external'] . ';
        var smf_avatarMaxHeight = ' . (int) $modSettings['avatar_max_height_external'] . ';';

        if (!$context['browser']['is_ie'] && !$context['browser']['is_mac_ie'])
          $context['html_headers'] .= '
      window.addEventListener("load", smf_avatarResize, false);';
        else
          $context['html_headers'] .= '
      var window_oldAvatarOnload = window.onload;
      window.onload = smf_avatarResize;';

        // !!! Move this over to script.js?
        $context['html_headers'] .= '
      // ]]></script>';
  }

  $context['common_stats']['latest_member'] = array(
    'id' => $modSettings['latestMember'],
    'name' => $modSettings['latestRealName'],
    'href' => $boardurl . '/perfil/' . $modSettings['latestRealName'],
    'link' => '<a href="' . $boardurl . '/perfil/' . $modSettings['latestRealName'] . '">' . $modSettings['latestRealName'] . '</a>',
  );

  $context['common_stats'] = array(
    'total_posts' => comma_format($modSettings['totalMessages']),
    'total_topics' => comma_format($modSettings['totalTopics']),
    'total_members' => comma_format($modSettings['totalMembers']),
    'latest_member' => $context['common_stats']['latest_member'],
  );

  if (empty($settings['theme_version']))
    $context['html_headers'] .= '
      <script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[
        var smf_scripturl = "' . $scripturl . '";
      // ]]></script>';

  if (!isset($context['page_title']))
    $context['page_title'] = '';
}

// This is the only template included in the sources...
function template_rawdata() {
  global $context;

  echo $context['raw_data'];
}

function template_header() {
  global $txt, $context, $settings, $user_info, $boarddir;

  setupThemeContext();

  // Print stuff to prevent caching of pages (except on attachment errors, etc.)
  if (empty($context['no_last_modified'])) {
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');

    // Are we debugging the template/html content?
    if (!isset($_REQUEST['xml']) && isset($_GET['debug']) && !$context['browser']['is_ie'] && !WIRELESS)
      header('Content-Type: application/xhtml+xml');
    else if (!isset($_REQUEST['xml']) && !WIRELESS)
      header('Content-Type: text/html; charset=' . (empty($context['character_set']) ? 'ISO-8859-1' : $context['character_set']));
  }

  header('Content-Type: text/' . (isset($_REQUEST['xml']) ? 'xml' : 'html') . '; charset=' . (empty($context['character_set']) ? 'ISO-8859-1' : $context['character_set']));

  foreach ($context['template_layers'] as $layer) {
    loadSubTemplate($layer . '_above', true);

    // May seem contrived, but this is done in case the main layer isn't there...
    if ($layer == 'main' && allowedTo('admin_forum') && !$user_info['is_guest']) {
      $securityFiles = array('install.php', 'webinstall.php', 'upgrade.php', 'convert.php', 'repair_paths.php', 'repair_settings.php');

      foreach ($securityFiles as $i => $securityFile) {
        if (!file_exists($boarddir . '/' . $securityFile))
          unset($securityFiles[$i]);
      }

      if (!empty($securityFiles)) {
        echo '
          <div style="margin: 2ex; padding: 2ex; border: 2px dashed #cc3344; color: black; background-color: #ffe4e9;">
            <div style="float: left; width: 2ex; font-size: 2em; color: red;">!!</div>
            <b style="text-decoration: underline;">', $txt['smf299'], '</b><br />
            <div style="padding-left: 6ex;">';

        foreach ($securityFiles as $securityFile)
          echo '
            ', $txt['smf300'], '<b>', $securityFile, '</b>!<br />';

        echo '
            </div>
          </div>';
      }
    }
    // If the user is banned from posting inform them of it.
    else if ($layer == 'main' && isset($_SESSION['ban']['cannot_post'])) {
      echo '
        <div class="windowbg" style="margin: 2ex; padding: 2ex; border: 2px dashed red; color: red;">
          ', sprintf($txt['you_are_post_banned'], $user_info['is_guest'] ? $txt[28] : $user_info['name']);

      if (!empty($_SESSION['ban']['cannot_post']['reason']))
        echo '
          <div style="padding-left: 4ex; padding-top: 1ex;">', $_SESSION['ban']['cannot_post']['reason'], '</div>';

      echo '
        </div>';
    }
  }

  if (isset($settings['use_default_images']) && $settings['use_default_images'] == 'defaults' && isset($settings['default_template'])) {
    $settings['theme_url'] = $settings['default_theme_url'];
    $settings['images_url'] = $settings['default_images_url'];
    $settings['theme_dir'] = $settings['default_theme_dir'];
  }
}

function template_footer() {
  global $context, $settings, $modSettings, $time_start, $db_count;

  $context['show_load_time'] = !empty($modSettings['timeLoadPageEnable']);
  $context['load_time'] = round(array_sum(explode(' ', microtime())) - array_sum(explode(' ', $time_start)), 3);
  $context['load_queries'] = $db_count;

  if (isset($settings['use_default_images']) && $settings['use_default_images'] == 'defaults' && isset($settings['default_template'])) {
    $settings['theme_url'] = $settings['actual_theme_url'];
    $settings['images_url'] = $settings['actual_images_url'];
    $settings['theme_dir'] = $settings['actual_theme_dir'];
  }

  foreach (array_reverse($context['template_layers']) as $layer)
    loadSubTemplate($layer . '_below', true);

}

function db_debug_junk() {
  global $context, $scripturl, $boarddir, $modSettings;
  global $db_cache, $db_count, $db_show_debug, $cache_count, $cache_hits;

  if (!isset($db_show_debug) || $db_show_debug !== true || (isset($_GET['action']) && $_GET['action'] == 'viewquery') || WIRELESS)
    return;

  if (empty($_SESSION['view_queries']))
    $_SESSION['view_queries'] = 0;

  if (empty($context['debug']['language_files']))
    $context['debug']['language_files'] = array();

  $files = get_included_files();
  $total_size = 0;

  for ($i = 0, $n = count($files); $i < $n; $i++) {
    $total_size += filesize($files[$i]);
    $files[$i] = strtr($files[$i], array($boarddir => '.'));
  }

  $warnings = 0;

  foreach ($db_cache as $q => $qq) {
    if (!empty($qq['w']))
      $warnings += count($qq['w']);
  }

  $_SESSION['debug'] = &$db_cache;

  // Gotta have valid HTML ;).
  $temp = ob_get_contents();

  if (function_exists('ob_clean'))
    ob_clean();
  else {
    ob_end_clean();
    ob_start('ob_sessrewrite');
  }

  echo preg_replace('~</body>\s*</html>~', '', $temp), '
    <div class="smalltext" style="text-align: left; margin: 1ex;">
      Templates: ', count($context['debug']['templates']), ': <i>', implode('</i>, <i>', $context['debug']['templates']), '</i>.<br />
      Sub templates: ', count($context['debug']['sub_templates']), ': <i>', implode('</i>, <i>', $context['debug']['sub_templates']), '</i>.<br />
      Language files: ', count($context['debug']['language_files']), ': <i>', implode('</i>, <i>', $context['debug']['language_files']), '</i>.<br />
      Files included: ', count($files), ' - ', round($total_size / 1024), 'KB. (<a href="javascript:void(0);" onclick="document.getElementById(\'debug_include_info\').style.display = \'inline\'; this.style.display = \'none\'; return false;">show</a><span id="debug_include_info" style="display: none;"><i>', implode('</i>, <i>', $files), '</i></span>)<br />';

  if (!empty($modSettings['cache_enable']) && !empty($cache_hits)) {
    $entries = array();
    $total_t = 0;
    $total_s = 0;

    foreach ($cache_hits as $h) {
      $entries[] = $h['d'] . ' ' . $h['k'] . ': ' . comma_format($h['t'], 5) . ' - ' . $h['s'] . ' bytes';
      $total_t += $h['t'];
      $total_s += $h['s'];
    }

    echo '
      Cache hits: ', $cache_count, ': ', comma_format($total_t, 5), 's for ', comma_format($total_s), ' bytes (<a href="javascript:void(0);" onclick="document.getElementById(\'debug_cache_info\').style.display = \'inline\'; this.style.display = \'none\'; return false;">show</a><span id="debug_cache_info" style="display: none;"><i>', implode('</i>, <i>', $entries), '</i></span>)<br />';
  }

  echo '
    <a href="', $scripturl, '?action=viewquery" target="_blank">Queries used: ', $db_count, $warnings == 0 ? '' : ', ' . $warnings . ' warning(s)', '</a>.<br />
    <br />';

  if ($_SESSION['view_queries'] == 1)
    foreach ($db_cache as $q => $qq) {
      $is_select = substr(trim($qq['q']), 0, 6) == 'SELECT' || preg_match('~^INSERT(?: IGNORE)? INTO \w+(?:\s+\([^)]+\))?\s+SELECT .+$~s', trim($qq['q'])) != 0;

      echo '
        <b>', $is_select ? '<a href="' . $scripturl . '?action=viewquery;qq=' . ($q + 1) . '#qq' . $q . '" target="_blank" style="text-decoration: none;">' : '', nl2br(str_replace("\t", '&nbsp;&nbsp;&nbsp;', htmlspecialchars(ltrim($qq['q'], "\n\r")))) . ($is_select ? '</a></b>' : '</b>') . '<br />
        &nbsp;&nbsp;&nbsp;';

      if (!empty($qq['f']) && !empty($qq['l']))
        echo 'in <i>' . $qq['f'] . '</i> line <i>' . $qq['l'] . '</i>, ';
        echo 'which took ' . round($qq['t'], 8) . ' seconds.<br />
          <br />';
    }

  echo '
      <a href="' . $scripturl . '?action=viewquery;sa=hide">[' . (empty($_SESSION['view_queries']) ? 'show' : 'hide') . ' queries]</a>
    </div></body></html>';
}

// Lookup an IP; try shell_exec first because we can do a timeout on it.
function host_from_ip($ip) {
  global $modSettings;

  if (($host = cache_get_data('hostlookup-' . $ip, 600)) !== null)
    return $host;

  $t = microtime();

  // If we can't access nslookup/host, PHP 4.1.x might just crash.
  if (@version_compare(PHP_VERSION, '4.2.0') == -1)
    $host = false;

  // Try the Linux host command, perhaps?
  if (!isset($host) && (strpos(strtolower(PHP_OS), 'win') === false || strpos(strtolower(PHP_OS), 'darwin') !== false) && mt_rand(0, 1) == 1) {
    if (!isset($modSettings['host_to_dis']))
      $test = @shell_exec('host -W 1 ' . @escapeshellarg($ip));
    else
      $test = @shell_exec('host ' . @escapeshellarg($ip));

    // Did host say it didn't find anything?
    if (strpos($test, 'not found') !== false)
      $host = '';
    // Invalid server option?
    else if ((strpos($test, 'invalid option') || strpos($test, 'Invalid query name 1')) && !isset($modSettings['host_to_dis']))
      updateSettings(array('host_to_dis' => 1));
    // Maybe it found something, after all?
    else if (preg_match('~\s([^\s]+?)\.\s~', $test, $match) == 1)
      $host = $match[1];
  }

  // This is nslookup; usually only Windows, but possibly some Unix?
  if (!isset($host) && strpos(strtolower(PHP_OS), 'win') !== false && strpos(strtolower(PHP_OS), 'darwin') === false && mt_rand(0, 1) == 1) {
    $test = @shell_exec('nslookup -timeout=1 ' . @escapeshellarg($ip));
    if (strpos($test, 'Non-existent domain') !== false)
      $host = '';
    else if (preg_match('~Name:\s+([^\s]+)~', $test, $match) == 1)
      $host = $match[1];
  }

  // This is the last try :/.
  if (!isset($host) || $host === false)
    $host = @gethostbyaddr($ip);

  // It took a long time, so let's cache it!
  if (array_sum(explode(' ', microtime())) - array_sum(explode(' ', $t)) > 0.5)
    cache_put_data('hostlookup-' . $ip, $host, 600);

  return $host;
}

// Chops a string into words and prepares them to be inserted into (or searched from) the database.
function text2words($text, $max_chars = 20, $encrypt = false) {
  global $func, $context;

  // Step 1: Remove entities/things we don't consider words:
  $words = preg_replace('~([\x0B\0' . ($context['utf8'] ? ($context['server']['complex_preg_chars'] ? '\x{A0}' : pack('C*', 0xC2, 0xA0)) : '\xA0') . '\t\r\s\n(){}\\[\\]<>!@$%^*.,:+=`\~\?/\\\\]|&(amp|lt|gt|quot);)+~' . ($context['utf8'] ? 'u' : ''), ' ', strtr($text, array('<br />' => ' ')));

  // Step 2: Entities we left to letters, where applicable, lowercase.
  $words = un_htmlspecialchars($func['strtolower']($words));

  // Step 3: Ready to split apart and index!
  $words = explode(' ', $words);

  if ($encrypt) {
    $possible_chars = array_flip(array_merge(range(46, 57), range(65, 90), range(97, 122)));
    $returned_ints = array();

    foreach ($words as $word) {
      if (($word = trim($word, '-_\'')) !== '') {
        $encrypted = substr(crypt($word, 'uk'), 2, $max_chars);
        $total = 0;

        for ($i = 0; $i < $max_chars; $i++)
          $total += $possible_chars[ord($encrypted{$i})] * pow(63, $i);

        $returned_ints[] = $max_chars == 4 ? min($total, 16777215) : $total;
      }
    }

    return array_unique($returned_ints);
  } else {
    // Trim characters before and after and add slashes for database insertion.
    $returned_words = array();

    foreach ($words as $word)
      if (($word = trim($word, '-_\'')) !== '')
        $returned_words[] = addslashes($max_chars === null ? $word : substr($word, 0, $max_chars));

    // Filter out all words that occur more than once.
    return array_unique($returned_words);
  }
}

// Generate a random seed and ensure it's stored in settings.
function smf_seed_generator() {
  global $modSettings;

  // Never existed?
  if (empty($modSettings['rand_seed'])) {
    $modSettings['rand_seed'] = microtime() * 1000000;
    updateSettings(array('rand_seed' => $modSettings['rand_seed']));
  }

  if (@version_compare(PHP_VERSION, '4.2.0') == -1) {
    $seed = ($modSettings['rand_seed'] + ((double) microtime() * 1000003)) & 0x7fffffff;
    mt_srand($seed);
  }

  // Change the seed.
  updateSettings(array('rand_seed' => mt_rand()));
}

function ssi_amigable($url) {
  $url = strtolower($url);
  $find = array('&#225;', '&#233;', '&#237;', '&#243;', '&#250;', '&#241;');
  $repl = array('a', 'e', 'i', 'o', 'u', 'n');
  $url = str_replace($find, $repl, $url);

  // Añadir guiones
  $find = array(' ', '&', '\r\n', '\n', '+');
  $url = str_replace($find, '-', $url);

  $find = array('/[^a-z0-9\-<>]/', '/[\-]+/', '/<[^>]*>/');
  $repl = array('', '-', '');
  $url = preg_replace($find, $repl, $url);

  return $url;
}

function ssi_reducir($tipo) {
  censorText($tipo);

  if (strlen($tipo) > 45) {
    $tipo = substr($tipo, 0, 45) . '...';
  }

  return $tipo;
}

function ssi_reducir2($tipo) {
  censorText($tipo);

  if (strlen($tipo) > 15) {
    $tipo = substr($tipo, 0, 15) . '...';
  }

  return $tipo;
}

function reducir30($tipo) {
  censorText($tipo);

  if (strlen($tipo) > 30) {
    $tipo = substr($tipo, 0, 30) . '...';
  }

  return $tipo;
}

function reducir22($tipo) {
  censorText($tipo);

  if (strlen($tipo) > 22) {
    $tipo = substr($tipo, 0, 22) . '...';
  }

  return $tipo;
}

function ssi_destacados() {
  global $db_prefix;

  $Array = mt_rand(1, 2);
  $request = db_query("
    SELECT value
    FROM {$db_prefix}settings
    WHERE variable = 'Highlights$Array'", __FILE__, __LINE__);

  $row = mysqli_fetch_assoc($request);

  echo $row['value'];
}

function ssi_sexo1($valor) {
  $valor = str_replace('1', 'Masculino', $valor);
  $valor = str_replace('2', 'Femenino', $valor);

  return $valor;
}

function ssi_sexo2($valor) {
  global $settings;

  $valor = str_replace('*', ' <img alt="Hombre" title="Hombre" src="' . $settings['images_url']  . '/Male.gif"> ', $valor);
  $valor = str_replace('^', ' <img alt="Mujer" title="Mujer" src="' . $settings['images_url']  . '/Female.gif"> ', $valor);

  return $valor;
}

function ssi_sexo3($valor) {
  $valor = str_replace('1', '*', $valor);
  $valor = str_replace('2', '^', $valor);

  return $valor;
}

function ssi_pais($valor) {
  $valor = str_replace('ar', 'Argentina', $valor);
  $valor = str_replace('bo', 'Bolivia', $valor);
  $valor = str_replace('br', 'Brasil', $valor);
  $valor = str_replace('cl', 'Chile', $valor);
  $valor = str_replace('co', 'Colombia', $valor);
  $valor = str_replace('cr', 'Costa Rica', $valor);
  $valor = str_replace('cu', 'Cuba', $valor);
  $valor = str_replace('ec', 'Ecuador', $valor);
  $valor = str_replace('es', 'Espa&ntilde;a', $valor);
  $valor = str_replace('gt', 'Guatemala', $valor);
  $valor = str_replace('it', 'Italia', $valor);
  $valor = str_replace('mx', 'Mexico', $valor);
  $valor = str_replace('py', 'Paraguay', $valor);
  $valor = str_replace('pe', 'Per&uacute;', $valor);
  $valor = str_replace('pt', 'Portugal', $valor);
  $valor = str_replace('pr', 'Puerto Rico', $valor);
  $valor = str_replace('uy', 'Uruguay', $valor);
  $valor = str_replace('ve', 'Venezuela', $valor);
  $valor = str_replace('ot', 'Otro', $valor);

  return $valor;
}

function ssi_estado_icon($valor) {
  $valor = str_replace('mcontento', 'Muy&nbsp;contento', $valor);
  $valor = str_replace('contento', 'Contento/a', $valor);
  $valor = str_replace('sueno', 'Con&nbsp;sue&ntilde;o', $valor);
  $valor = str_replace('descansar', 'Descansando', $valor);
  $valor = str_replace('triste', 'Triste', $valor);
  $valor = str_replace('enferm', 'Enfermo', $valor);
  $valor = str_replace('emusic', 'Escuchando&nbsp;m&uacute;sica', $valor);

  return $valor;
}

function ssi_vistaprevia() {
  global $db_prefix, $context, $modSettings, $boardurl, $settings;

  $subject = htmlentities(addslashes($_POST['subject']), ENT_QUOTES, 'UTF-8');
  $message = htmlentities(addslashes($_POST['message']), ENT_QUOTES, 'UTF-8');
  $message = parse_bbc($message);
  $user_id = $context['user']['id'];
  $reason = htmlentities(addslashes($_POST['causa']), ENT_QUOTES, 'UTF-8');

  if ($context['user']['is_guest']) {
    echo '<div class="noesta" style="width: 922px; margin-bottom: 4px;">Debes estar logueado para estar ac&aacute;.-</div>';
  } else if (strlen($subject) < $modSettings['title_post_preview']) {
    echo '
      <div class="noesta" style="width: 922px; margin-bottom: 4px;">El t&iacute;tulo no puede tener menos de ' . $modSettings['title_post_preview'] . ' letras.-</div>
      <div style="clear: both;"></div>';
  } else if (strlen($_REQUEST['message']) < $modSettings['body_post_preview']) {
    echo '<div class="noesta" style="width: 922px; margin-bottom: 4px;">El post no puede tener menos de ' . $modSettings['body_post_preview'] . ' letras.-</div>';
  } else {
    echo '
      <div class="box_140" style="float: left; margin-right: 8px;">
        <div class="box_title" style="width: 138px;">
          <div class="box_txt box_140-34">Publicado por:</div>
          <div class="box_rss">
            <div class="icon_img">
              <a href="' . $boardurl . '/rss/post-user/' . $context['user']['name'] . '">
                <img alt="" src="' . $settings['images_url'] . '/icons/tpbig-v1-iconos.gif?v3.2.3" style="cursor: pointer; margin-top: -352px; display: inline;">
              </a>
            </div>
          </div>
        </div>
        <div class="windowbg" style="padding: 4px; width: 130px;">';

    $userse = db_query("
      SELECT *
      FROM {$db_prefix}members
      WHERE ID_MEMBER = {$user_id}", __FILE__, __LINE__);

    while ($row = mysqli_fetch_assoc($userse)) {
      $context['ID_MEMBER'] = $row['ID_MEMBER'];
      $context['memberName'] = $row['memberName'];
      $context['avatar'] = $row['avatar'];
      $context['personalText'] = $row['personalText'];
      $context['ID_POST_GROUP'] = $row['ID_POST_GROUP'];
      $context['ID_GROUP'] = $row['ID_GROUP'];
      $context['realName'] = $row['realName'];
      $context['usertitle'] = $row['usertitle'];
      $context['estado_icon'] = $row['estado_icon'];
      $context['gender'] = $row['gender'];
      $context['topics'] = $row['topics'];
      $context['money'] = $row['money'];
      $context['moneyBank'] = $row['moneyBank'];
    }

    $idgrup = $context['ID_POST_GROUP'];
    $idgrup2 = $context['ID_GROUP'];

    $userse2 = db_query("
      SELECT *
      FROM {$db_prefix}membergroups
      WHERE ID_GROUP = {$idgrup}", __FILE__, __LINE__);

    while ($row2 = mysqli_fetch_assoc($userse2)) {
      $membergropu = $row2['groupName'];
    }

    $userse3 = db_query("
      SELECT *
      FROM {$db_prefix}membergroups
      WHERE ID_GROUP = {$idgrup2}", __FILE__, __LINE__);

    while ($row2 = mysqli_fetch_assoc($userse3)) {
      $membergropu2 = $row2['groupName'];
    }

    $medallasa = db_query("
      SELECT *
      FROM {$db_prefix}membergroups
      WHERE ID_GROUP = " . (!empty($idgrup2) ? $idgrup2 : $idgrup), __FILE__, __LINE__);

    while ($rows = mysqli_fetch_assoc($medallasa)) {
      $medalla = $rows['stars'];
    }

    if ($modSettings['avatar_action_too_large'] == 'option_html_resize' || $modSettings['avatar_action_too_large'] == 'option_js_resize') {
      if (!empty($modSettings['avatar_max_width_external']))
        $context['user']['avatar']['width'] = $modSettings['avatar_max_width_external'];

      if (!empty($modSettings['avatar_max_height_external']))
        $context['user']['avatar']['height'] = $modSettings['avatar_max_height_external'];
    }

    echo '
      <center>
        <div class="fondoavatar" style="overflow: hidden; width: 130px;" align="center">
          <a href="' . $boardurl . '/perfil/' . $context['memberName'] . '" title="Ver perfil">';

    if (!empty($context['avatar']))
      $context['user']['avatar']['image'] = '<img src="'  .$context['avatar'] . '" width="105" alt="" class="avatar" border="0" />';

    if ($context['avatar']) {
      echo $context['user']['avatar']['image'];
    } else {
      echo '<img src="' . $boardurl . '/avatar.gif" alt="Sin avatar" border="0">';
    }

    echo '
          </a>
          <br />
          <span class="mp">' . $context['personalText'] . '</span>
        </div>
      </center>
      <br/>';

    echo '
      <b>
        <a href="' . $boardurl . '/perfil/' . $context['memberName'] . '" style="font-size: 14px; color: rgb(255, 102, 0);">' . $context['realName'] . '</a>
      </b>
      <br />';

    echo '
      <b style="font-size: 12px; color: rgb(116, 116, 116); text-shadow: 0px 1px 1px rgb(106, 86, 69);">' . (!empty($membergropu2) ? $membergropu2 : $membergropu) . '</b>
      <br/>';
    echo '
      <span title="', (!empty($membergropu2) ? $membergropu2 : $membergropu), '">
        <img alt="" src="' . str_replace('1#rangos', $settings['images_url'] . '/rangos', $medalla) . '">
      </span>';
    echo '<span title="' . ssi_sexo1($context['gender']) . '">' . ssi_sexo2(ssi_sexo3($context['gender'])) . '</span>&nbsp;';

    if ($context['usertitle']) {
      echo '<img alt="" title="'. ssi_pais($context['usertitle'])  . '" src="' . $settings['images_url'] . '/icons/banderas/' . $context['usertitle'] . '.gif"> ';
    } else {
      echo '<img alt="" title="" src="' . $settings['images_url'] . '/icons/banderas/ot.gif"> ';
    }

    echo '
      <img title="Estado: ' . ssi_estado_icon($context['estado_icon']) . '" src="' . $settings['images_url'] . '/icons/estado/' . $context['estado_icon'] . '.gif" alt="">
      <br />';

    echo '
      <br />
      <div style="margin-bottom: 2px;">
        <span style="font-size: 12px;">
          <img alt="" src="' . $settings['images_url'] . '/icons/mensaje_para.gif" border="0">
          &nbsp;
          <a href="' . $boardurl . '/mensajes/a/' . $context['memberName'] . '" title="Enviar mensaje">Enviar mensaje</a>
        </span>
      </div>
      <div style="margin-bottom: 4px;">
        <span class="icons fot2" style="font-size: 12px;">
          <a href="' . $boardurl . '/imagenes/' . $context['memberName'] . '" title="Sus im&aacute;genes">
            &nbsp;
            Sus im&aacute;genes
          </a>
        </span>
      </div>
      <br />
      <hr class="divider">';

    // Contar los comentarios del usuario
    $request = db_query("
      SELECT *
      FROM {$db_prefix}comments
      WHERE ID_MEMBER = {$user_id}", __FILE__, __LINE__);

    $context['comentuser'] = mysqli_num_rows($request);

    echo '
          <br />
          <div class="fondoavatar" style="overflow: hidden; width: 130px;">
            <b style="color: rgb(254, 143, 71); text-shadow: 0px 1px 1px rgb(106, 86, 69);">PUNTOS:</b>
            &nbsp;
            <b>
              <span id="cant_pts_post">' . $context['moneyBank'] . '</span>
            </b>
            <br />
            <b style="color: rgb(254, 143, 71); text-shadow: 0px 1px 1px rgb(106, 86, 69);">POST:</b>
            &nbsp;
            <b>
              <a href="' . $boardurl . '/user-post/' . $context['memberName'] . '">' . $context['topics'] . '</a>
            </b>
            <br />
            <b style="color: rgb(254, 143, 71); text-shadow: 0px 1px 1px rgb(106, 86, 69);">COMENTARIOS:</b>
            &nbsp;
            <b>
              <a href="' . $boardurl . '/user-comment/' . $context['memberName'] . '">' . $context['comentuser'] . '</a>
            </b>
          </div>
          <br />
          <span class="size11"></span>
        </div>
      </div>
      <div>
        <div class="box_780" style="float: left;">
          <div class="box_title" style="width: 772px;">
            <div class="box_txt box_780-34">
              <center>' . $subject . '</center>
            </div>
            <div class="box_rss">
              <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 16px; height: 16px;" border="0">
            </div>
          </div>
          <div class="windowbg" style="padding: 4px; width: 764px;" id="vista_previa">' . $message . '</div>
          <div style="margin-top: 4px; margin-bottom: 4px;" align="center">
            <input onclick="cerrar_vprevia()" class="button" style="font-size: 13px;" value="Cerrar la previsualizaci&oacute;n" title="Cerrar la previsualizaci&oacute;n" type="button" />
            &nbsp;
            <input onclick="return oblig(this.form.subject.value, this.form.message.value, this.form.tags.value, this.form);" class="button" style="font-size: 13px;" value="Ok, est&aacute; perfecto!" title="Ok, est&aacute; perfecto!" type="submit" />
          </div>
        </div>
      </div>
      <div style="clear: both;"></div>
      <br />';
  }
}

function ssi_grupos() {
  global $context, $db_prefix;

  $request = db_query("
    SELECT *
    FROM {$db_prefix}members AS m
    WHERE {$context['user']['id']} = m.ID_MEMBER", __FILE__, __LINE__);

  while ($grup = mysqli_fetch_assoc($request)) {
    $context['ID_GROUP'] = $grup['ID_POST_GROUP'];
    $context['Turista'] = $grup['ID_POST_GROUP'] == '4';
    $context['Conocido'] = $grup['ID_POST_GROUP'] == '5';
    $context['Vecino'] = $grup['ID_POST_GROUP'] == '6';
    $context['Amigo'] = $grup['ID_POST_GROUP'] == '7';
    $context['Familiar'] = $grup['ID_POST_GROUP'] == '8';
    $context['Casero'] = $grup['ID_POST_GROUP'] == '9';

    /* No son por mensajes */
    $context['Abastecedor'] = $grup['ID_GROUP'] == '10';
    $context['Heredero'] = $grup['ID_GROUP'] == '11';
    $context['Hermano Mayor'] = $grup['ID_GROUP'] == '2';
    $context['Padre'] = $grup['ID_GROUP'] == '1';
  }

  mysqli_free_result($request);
}

function votos($valor) {
  if (is_numeric($valor) && $valor >= 0) {
    echo '<td style="color:green;">+' . $valor . '</td>';
  } else if (is_numeric($valor) && $valor < 0) {
    echo '<td style="color:red;">' . $valor . '</td>';
  }
}

function create_button() {}
?>