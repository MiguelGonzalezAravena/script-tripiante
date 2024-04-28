<?php
if (!defined('SMF'))
  die('Hacking attempt...');

function DisplayStats() {
  global $txt, $scripturl, $db_prefix, $modSettings, $user_info, $context, $boardurl;

  if (!empty($_REQUEST['expand'])) {
    $month = (int) substr($_REQUEST['expand'], 4);
    $year = (int) substr($_REQUEST['expand'], 0, 4);

    if ($year > 1900 && $year < 2200 && $month >= 1 && $month <= 12) {
      $_SESSION['expanded_stats'][$year][] = $month;
    }
  } else if (!empty($_REQUEST['collapse'])) {
    $month = (int) substr($_REQUEST['collapse'], 4);
    $year = (int) substr($_REQUEST['collapse'], 0, 4);

    if (!empty($_SESSION['expanded_stats'][$year])) {
      $_SESSION['expanded_stats'][$year] = array_diff($_SESSION['expanded_stats'][$year], array($month));
    }
  }

  // Handle the XMLHttpRequest.
  if (isset($_REQUEST['xml'])) {
    // Collapsing stats only needs adjustments of the session variables.
    if (!empty($_REQUEST['collapse'])) {
      obExit(false);
    }

    $context['sub_template'] = 'stats';
    getDailyStats("YEAR(date) = $year AND MONTH(date) = $month");
    $context['monthly'][$year . sprintf('%02d', $month)]['date'] = array(
      'month' => sprintf('%02d', $month),
      'year' => $year,
    );

    return;
  }

  loadLanguage('Stats');
  loadTemplate('Stats');

  isAllowedTo('view_stats');

  $context['page_title'] = 'TOPs';

  $context['show_member_list'] = allowedTo('view_mlist');

  // Get averages...
  $result = db_query("
    SELECT
      SUM(posts) AS posts, SUM(topics) AS topics, SUM(registers) AS registers,
      SUM(mostOn) AS mostOn, MIN(date) AS date, SUM(hits) AS hits
    FROM {$db_prefix}log_activity", __FILE__, __LINE__);
  $row = mysqli_fetch_assoc($result);

  mysqli_free_result($result);

  // This would be the amount of time the forum has been up... in days...
  $total_days_up = ceil((time() - strtotime($row['date'])) / (60 * 60 * 24));
  $context['average_posts'] = round($row['posts'] / $total_days_up, 2);
  $context['average_topics'] = round($row['topics'] / $total_days_up, 2);
  $context['average_members'] = round($row['registers'] / $total_days_up, 2);
  $context['average_online'] = round($row['mostOn'] / $total_days_up, 2);
  $context['average_hits'] = round($row['hits'] / $total_days_up, 2);
  $context['num_hits'] = $row['hits'];

  // How many users are online now.
  $result = db_query("
    SELECT COUNT(*)
    FROM {$db_prefix}log_online", __FILE__, __LINE__);
  list ($context['users_online']) = mysqli_fetch_row($result);

  mysqli_free_result($result);

  // Statistics such as number of boards, categories, etc.
  $result = db_query("
    SELECT COUNT(*)
    FROM {$db_prefix}boards AS b", __FILE__, __LINE__);
  list ($context['num_boards']) = mysqli_fetch_row($result);

  mysqli_free_result($result);

  $result = db_query("
    SELECT COUNT(*)
    FROM {$db_prefix}categories AS c", __FILE__, __LINE__);
  list ($context['num_categories']) = mysqli_fetch_row($result);

  mysqli_free_result($result);

  $context['num_members'] = &$modSettings['totalMembers'];
  $context['num_posts'] = &$modSettings['totalMessages'];
  $context['num_topics'] = &$modSettings['totalTopics'];
  $context['most_members_online'] = array(
    'number' => &$modSettings['mostOnline'],
    'date' => timeformat($modSettings['mostDate'])
  );
  $context['latest_member'] = &$context['common_stats']['latest_member'];

  // Male vs. female ratio - let's calculate this only every four minutes.
  if (($context['gender'] = cache_get_data('stats_gender', 240)) == null) {
    $result = db_query("
      SELECT COUNT(*) AS totalMembers, gender
      FROM {$db_prefix}members
      GROUP BY gender", __FILE__, __LINE__);
    $context['gender'] = array();
    while ($row = mysqli_fetch_assoc($result)) {
      // Assuming we're telling... male or female?
      if (!empty($row['gender'])) {
        $context['gender'][$row['gender'] == 2 ? 'females' : 'males'] = $row['totalMembers'];
      }
    }

    mysqli_free_result($result);

    // Set these two zero if the didn't get set at all.
    if (empty($context['gender']['males'])) {
      $context['gender']['males'] = 0;
    }

    if (empty($context['gender']['females'])) {
      $context['gender']['females'] = 0;
    }

    // Try and come up with some "sensible" default states in case of a non-mixed board.
    if ($context['gender']['males'] == $context['gender']['females']) {
      $context['gender']['ratio'] = '1:1';
    } else if ($context['gender']['males'] == 0) {
      $context['gender']['ratio'] = '0:1';
    } else if ($context['gender']['females'] == 0) {
      $context['gender']['ratio'] = '1:0';
    } else if ($context['gender']['males'] > $context['gender']['females']) {
      $context['gender']['ratio'] = round($context['gender']['males'] / $context['gender']['females'], 1) . ':1';
    } else if ($context['gender']['females'] > $context['gender']['males']) {
      $context['gender']['ratio'] = '1:' . round($context['gender']['females'] / $context['gender']['males'], 1);
    }

    cache_put_data('stats_gender', $context['gender'], 240);
  }

  $date = strftime('%Y%m%d', forum_time(false));

  // Members online so far today.
  $result = db_query("
    SELECT mostOn
    FROM {$db_prefix}log_activity
    WHERE date = $date
    LIMIT 1", __FILE__, __LINE__);
  list ($context['online_today']) = mysqli_fetch_row($result);

  mysqli_free_result($result);

  $context['online_today'] = (int) $context['online_today'];
  $boards_result = db_query("
    SELECT ID_BOARD, name, numPosts, description
    FROM {$db_prefix}boards AS b
    WHERE $user_info[query_see_board]" . (!empty($modSettings['recycle_enable']) && $modSettings['recycle_board'] > 0 ? "
      AND b.ID_BOARD != $modSettings[recycle_board]" : '') . "
    ORDER BY numPosts DESC
    LIMIT 10", __FILE__, __LINE__);
  $context['top_boards'] = array();
  $max_num_posts = 1;

  while ($row_board = mysqli_fetch_assoc($boards_result)) {
    $context['top_boards'][] = array(
      'id' => $row_board['ID_BOARD'],
      'name' => $row_board['name'],
      'description' => $row_board['description'],
      'num_posts' => $row_board['numPosts'],
      'href' => $boardurl . '/categoria/' . $row_board['description'] . '/',
      'link' => '<a href="' . $boardurl . '/categoria/' . $row_board['description'] . '/">' . $row_board['name'] . '</a>'
    );

    if ($max_num_posts < $row_board['numPosts'])
      $max_num_posts = $row_board['numPosts'];
  }
  mysqli_free_result($boards_result);

  foreach ($context['top_boards'] as $i => $board)
    $context['top_boards'][$i]['post_percent'] = round(($board['num_posts'] * 100) / $max_num_posts);

  // Are you on a larger forum?  If so, let's try to limit the number of topics we search through.
  if ($modSettings['totalMessages'] > 100000) {
    $request = db_query("
      SELECT ID_TOPIC
      FROM {$db_prefix}topics
      WHERE numReplies != 0
      ORDER BY numReplies DESC
      LIMIT 100", __FILE__, __LINE__);
    $topic_ids = array();
    while ($row = mysqli_fetch_assoc($request))
      $topic_ids[] = $row['ID_TOPIC'];
    mysqli_free_result($request);
  }
  else {
    $topic_ids = array();
  }

  // Inicio de consultas

  // 10 Posts más comentados
  $request = db_query("
    SELECT t.ID_TOPIC, COUNT(c.ID_TOPIC) as Cuenta, t.subject, t.ID_BOARD, b.name AS bname, b.description
    FROM ({$db_prefix}comments as c, {$db_prefix}messages as t, {$db_prefix}boards AS b)
    WHERE t.ID_TOPIC = c.ID_TOPIC
    AND t.ID_BOARD = b.ID_BOARD
    GROUP BY c.ID_TOPIC
    ORDER BY Cuenta DESC
    LIMIT 10", __FILE__, __LINE__);

  $context['tcomentados'] = array();

  while ($row = mysqli_fetch_assoc($request)) {
    $context['tcomentados'][] = array(
      'subject' => $row['subject'],
      'short_title' => htmlentities(ssi_reducir($row['subject']), ENT_QUOTES, 'ISO-8859-1'),
      'full_title' => htmlentities($row['subject'], ENT_QUOTES, 'ISO-8859-1'),
      'cuenta' => $row['Cuenta'],
      'ID_TOPIC' => $row['ID_TOPIC'],
      'description' => $row['description'],
      'bname' => $row['bname'],
    );
  }

  mysqli_free_result($request);

  // 10 Posts más vistos
  $topic_view_result = db_query("
    SELECT m.subject, t.numViews, t.ID_BOARD, t.ID_TOPIC, b.name, b.description
    FROM ({$db_prefix}topics AS t, {$db_prefix}messages AS m, {$db_prefix}boards AS b)
    WHERE m.ID_MSG = t.ID_FIRST_MSG
      AND $user_info[query_see_board]" . (!empty($modSettings['recycle_enable']) && $modSettings['recycle_board'] > 0 ? "
      AND b.ID_BOARD != $modSettings[recycle_board]" : '') . "
      AND t.ID_BOARD = b.ID_BOARD" . (!empty($topic_ids) ? "
      AND t.ID_TOPIC IN (" . implode(', ', $topic_ids) . ")" : '') . "
    ORDER BY t.numViews DESC
    LIMIT 10", __FILE__, __LINE__);
  $context['top_topics_views'] = array();
  $max_num_views = 1;

  while ($row_topic_views = mysqli_fetch_assoc($topic_view_result)) {
    $row_topic_views['subject'] = censorText($row_topic_views['subject']);

    $context['top_topics_views'][] = array(
      'id' => $row_topic_views['ID_TOPIC'],
      'board' => array(
        'id' => $row_topic_views['ID_BOARD'],
        'name' => $row_topic_views['name'],
        'description' => $row_topic_views['description'],
        'href' => $boardurl . '/categoria/' . $row_topic_views['description'] . '/',
        'link' => '<a href="' . $boardurl . '/categoria/' . $row_topic_views['description'] . '/">' . $row_topic_views['name'] . '</a>'
      ),
      'subject' => $row_topic_views['subject'],
      'short_title' => htmlentities(ssi_reducir($row_topic_views['subject']), ENT_QUOTES, 'ISO-8859-1'),
      'full_title' => htmlentities($row_topic_views['subject'], ENT_QUOTES, 'ISO-8859-1'),
      'num_views' => $row_topic_views['numViews'],
      'href' => $scripturl . '?topic=' . $row_topic_views['ID_TOPIC'] . '.0',
      'link' => '<a href="' . $scripturl . '?topic=' . $row_topic_views['ID_TOPIC'] . '.0">' . htmlentities($row_topic_views['subject'], ENT_QUOTES, 'ISO-8859-1') . '</a>'
    );

    if ($max_num_views < $row_topic_views['numViews']) {
      $max_num_views = $row_topic_views['numViews'];
    }
  }

  mysqli_free_result($topic_view_result);

  foreach ($context['top_topics_views'] as $i => $topic) {
    $context['top_topics_views'][$i]['post_percent'] = round(($topic['num_views'] * 100) / $max_num_views);
  }

  // 10 Posts con más puntos
  $request = db_query("
    SELECT m.subject, m.ID_TOPIC AS id, t.ID_TOPIC, t.ID_BOARD, t.points, b.name AS bname, b.description
    FROM ({$db_prefix}topics AS t, {$db_prefix}messages AS m, {$db_prefix}boards AS b)
    WHERE t.ID_TOPIC = m.ID_TOPIC
    AND t.ID_BOARD = b.ID_BOARD
    ORDER BY t.points DESC
    LIMIT 10 ", __FILE__, __LINE__);

  $context['postpuntos'] = array();
  while ($row = mysqli_fetch_assoc($request)) {
    $row['subject'] = censorText($row['subject']);

    $context['postpuntos'][] = array(
      'short_title' => htmlentities(ssi_reducir($row['subject']), ENT_QUOTES, 'ISO-8859-1'),
      'full_title' => htmlentities($row['subject'], ENT_QUOTES, 'ISO-8859-1'),
      'id' => $row['ID_TOPIC'],
      'cat' => $row['description'],
      'points' => $row['points']
    );
  }

  mysqli_free_result($request);

  // 10 Principales posteadores
  $members_result = db_query("
    SELECT ID_MEMBER, realName, memberName, topics
    FROM {$db_prefix}members
    WHERE topics > 0
    ORDER BY topics DESC
    LIMIT 10", __FILE__, __LINE__);

  $context['top_starters'] = array();
  $max_num_topics = 1;

  while ($row_members = mysqli_fetch_assoc($members_result)) {
    $row_members['memberName'] = censorText($row_members['memberName']);
    $row_members['realName'] = censorText($row_members['realName']);
    $context['top_starters'][] = array(
      'name' => $row_members['realName'],
      'id' => $row_members['ID_MEMBER'],
      'num_topics' => $row_members['topics'],
      'href' => $boardurl . '/perfil/' . $row_members['memberName'],
      'link' => '<a href="' . $boardurl . '/perfil/' . $row_members['memberName'] . '">' . $row_members['realName'] . '</a>'
    );

    if ($max_num_topics < $row_members['topics']) {
      $max_num_topics = $row_members['topics'];
    }
  }

  mysqli_free_result($members_result);

  foreach ($context['top_starters'] as $i => $topic) {
    $context['top_starters'][$i]['post_percent'] = round(($topic['num_topics'] * 100) / $max_num_topics);
  }

  // 10 Usuarios con más puntos
  // Large forums may need a bit more prodding...
  $context['shop_richest'] = array();

  // Get the richest people
  $result = db_query("
    SELECT ID_MEMBER, realName, moneyBank
    FROM {$db_prefix}members
    ORDER BY moneyBank DESC
    LIMIT 10", __FILE__, __LINE__);

  while ($row = mysqli_fetch_assoc($result)) {
    $context['shop_richest'][] = array(
      'ID_MEMBER' => $row['ID_MEMBER'],
      'realName' => censorText($row['realName']),
      'money' => $row['moneyBank']
    );
  }

  mysqli_free_result($result);

  // 10 Usuarios que más comentan
  $request = db_query("
    SELECT COUNT(g.ID_MEMBER + c.ID_MEMBER) AS total, mem.ID_MEMBER, mem.realName, mem.memberName
    FROM ({$db_prefix}members AS mem, {$db_prefix}comments AS c, {$db_prefix}gallery_comment AS g)
    WHERE mem.ID_MEMBER = g.ID_MEMBER
    AND mem.ID_MEMBER = c.ID_MEMBER
    AND c.ID_MEMBER = g.ID_MEMBER
    GROUP BY g.ID_MEMBER
    ORDER BY total DESC
    LIMIT 10", __FILE__, __LINE__);

  $context['mascomentan'] = array();

  while ($row = mysqli_fetch_assoc($request)) {
    $context['mascomentan'][] = array(
      'realName' => censorText($row['realName']),
      'memberName' => censorText($row['memberName']),
      'total' => $row['total'],
    );
  }

  mysqli_free_result($request);

  // 10 Imágenes más comentadas
  $request = db_query("
    SELECT COUNT(c.ID_PICTURE) AS cuenta, p.ID_PICTURE, c.ID_PICTURE, p.title
    FROM ({$db_prefix}gallery_pic AS p, {$db_prefix}gallery_comment AS c)
    WHERE c.ID_PICTURE = p.ID_PICTURE
    GROUP BY p.ID_PICTURE DESC
    ORDER BY cuenta DESC
    LIMIT 10", __FILE__, __LINE__);

  $context['topimagenescom'] = array();

  while ($row = mysqli_fetch_assoc($request)) {
    $context['topimagenescom'][] = array(
      'title' => $row['title'],
      'short_title' => htmlentities(ssi_reducir($row['title']), ENT_QUOTES, 'ISO-8859-1'),
      'full_title' => htmlentities($row['title'], ENT_QUOTES, 'ISO-8859-1'),
      'cuenta' => $row['cuenta'],
      'ID_PICTURE' => $row['ID_PICTURE'],
    );
  }

  mysqli_free_result($request);

  // 10 Imágenes más vistas
  $request = db_query("
    SELECT m.ID_MEMBER, i.ID_MEMBER, i.ID_PICTURE, i.title, m.memberName, m.realName, i.views
    FROM ({$db_prefix}gallery_pic AS i, {$db_prefix}members AS m)
    WHERE i.ID_MEMBER = m.ID_MEMBER
    ORDER BY i.views DESC
    LIMIT 0 , 10", __FILE__, __LINE__);

  $context['imgv'] = array();
 
  while ($row = mysqli_fetch_assoc($request)) {
    $context['imgv'][] = array(
      'id' => $row['ID_PICTURE'],
      'titulo' => $row['title'],
      'short_title' => htmlentities(ssi_reducir($row['title']), ENT_QUOTES, 'ISO-8859-1'),
      'full_title' => htmlentities($row['title'], ENT_QUOTES, 'ISO-8859-1'),
      'idm' => $row['ID_MEMBER'],
      'v' => $row['views'],
    );
  }

  mysqli_free_result($request);

  // 10 Imágenes con más puntos
  $request = db_query("
    SELECT points, ID_PICTURE, title
    FROM {$db_prefix}gallery_pic
    GROUP BY ID_PICTURE, title DESC
    ORDER BY points DESC
    LIMIT 10", __FILE__, __LINE__);

  $context['comment-img3'] = array();

  while ($row = mysqli_fetch_assoc($request)) {
    $context['comment-img3'][] = array(
      'title' => $row['title'],
      'short_title' => htmlentities(ssi_reducir($row['title']), ENT_QUOTES, 'ISO-8859-1'),
      'full_title' => htmlentities($row['title'], ENT_QUOTES, 'ISO-8859-1'),
      'points' => $row['points'],
      'ID_PICTURE' => $row['ID_PICTURE']
    );
  }

  mysqli_free_result($request);

  // 10 Muros más comentados
  $request = db_query("
    SELECT COUNT(c.COMMENT_MEMBER_ID) AS cuenta, c.COMMENT_MEMBER_ID, mem.ID_MEMBER, mem.memberName, mem.realName
    FROM ({$db_prefix}members AS mem, {$db_prefix}profile_comments AS c)
    WHERE c.COMMENT_MEMBER_ID = mem.ID_MEMBER
    GROUP BY mem.ID_MEMBER DESC
    ORDER BY cuenta DESC
    LIMIT 10", __FILE__, __LINE__);

  $context['muroscomentados'] = array();

  while ($row = mysqli_fetch_assoc($request)) {
    $context['muroscomentados'][] = array(
      'realName' => censorText($row['realName']),
      'memberName' => censorText($row['memberName']),
      'cuenta' => $row['cuenta'],
    );
  }

  mysqli_free_result($request);

  // 10 Usuarios con más imágenes
  $request = db_query("
    SELECT COUNT(p.ID_MEMBER) AS cuenta, p.ID_PICTURE, mem.ID_MEMBER, mem.memberName, mem.realName, p.ID_MEMBER
    FROM ({$db_prefix}gallery_pic AS p, {$db_prefix}members AS mem)
    WHERE p.ID_MEMBER = mem.ID_MEMBER
    GROUP BY mem.ID_MEMBER DESC
    ORDER BY cuenta DESC
    LIMIT 10", __FILE__, __LINE__);

  $context['imagenuser'] = array();

  while ($row = mysqli_fetch_assoc($request)) {
    $context['imagenuser'][] = array(
      'realName' => censorText($row['realName']),
      'memberName' => censorText($row['memberName']),
      'cuenta' => $row['cuenta'],
    );
  }

  mysqli_free_result($request);

  // TO-DO: ¿Se está usando esto?
  $request = db_query("
    SELECT m.ID_MEMBER, i.ID_MEMBER, i.ID_PICTURE, i.title, m.memberName, m.realName
    FROM ({$db_prefix}gallery_pic AS i, {$db_prefix}members AS m)
    WHERE i.ID_MEMBER = m.ID_MEMBER
    ORDER BY i.ID_PICTURE DESC
    LIMIT 10", __FILE__, __LINE__);

  $context['imagenestop'] = array();
  while ($row = mysqli_fetch_assoc($request)) {
    $context['imagenestop'][] = array(
    'id' => $row['ID_PICTURE'],
    'titulo' => $row['title'],
    'idm' => $row['ID_MEMBER'],
    'nombrem' => $row['memberName'],
    'nombrem2' => $row['realName'],
    );
  }

  mysqli_free_result($request);

  $topic_reply_result = db_query("
    SELECT *
    FROM ({$db_prefix}messages AS m, {$db_prefix}comments AS c)
    WHERE c.ID_TOPIC = m.ID_TOPIC
    LIMIT 10", __FILE__, __LINE__);

  $context['top_topics_replies'] = array();
  $max_num_replies = 1;

  while ($row_topic_reply = mysqli_fetch_assoc($topic_reply_result)) {
    censorText($row_topic_reply['subject']);

    $context['top_topics_replies'][] = array(
      'id' => $row_topic_reply['ID_TOPIC'],
      'subject' => $row_topic_reply['subject'],
      'num_replies' => $row_topic_reply['ID_TOPIC'],
    );

    if ($max_num_replies < $row_topic_reply['numReplies'])
      $max_num_replies = $row_topic_reply['numReplies'];
  }
  mysqli_free_result($topic_reply_result);

  foreach ($context['top_topics_replies'] as $i => $topic)
    $context['top_topics_replies'][$i]['post_percent'] = round(($topic['num_replies'] * 100) / $max_num_replies);

  if ($modSettings['totalMessages'] > 100000) {
    $request = db_query("
      SELECT ID_TOPIC
      FROM {$db_prefix}topics
      WHERE numViews != 0
      ORDER BY numViews DESC
      LIMIT 100", __FILE__, __LINE__);
    $topic_ids = array();
    while ($row = mysqli_fetch_assoc($request))
      $topic_ids[] = $row['ID_TOPIC'];
    mysqli_free_result($request);
  } else {
    $topic_ids = array();
  }

  // Try to cache this when possible, because it's a little unavoidably slow.
  if (($members = cache_get_data('stats_top_starters', 360)) == null) {
    $request = db_query("
      SELECT ID_MEMBER_STARTED, COUNT(*) AS hits
      FROM {$db_prefix}topics" . (!empty($modSettings['recycle_enable']) && $modSettings['recycle_board'] > 0 ? "
      WHERE ID_BOARD != $modSettings[recycle_board]" : '') . "
      GROUP BY ID_MEMBER_STARTED
      ORDER BY hits DESC
      LIMIT 20", __FILE__, __LINE__);
    $members = array();
    while ($row = mysqli_fetch_assoc($request))
      $members[$row['ID_MEMBER_STARTED']] = $row['hits'];
    mysqli_free_result($request);

    cache_put_data('stats_top_starters', $members, 360);
  }

  if (empty($members)) {
    $members = array(0 => 0);
  }



  // Time online top 10.
  // !!!SLOW This query is sorta slow.  Should we just add a key? (or would that be bad in the long run?)
  $temp = cache_get_data('stats_total_time_members', 600);
  $members_result = db_query("
    SELECT ID_MEMBER, realName, totalTimeLoggedIn
    FROM {$db_prefix}members" . (!empty($temp) ? "
    WHERE ID_MEMBER IN (" . implode(', ', $temp) . ")" : '') . "
    ORDER BY totalTimeLoggedIn DESC
    LIMIT 20", __FILE__, __LINE__);
  $context['top_time_online'] = array();
  $temp2 = array();
  $max_time_online = 1;
  while ($row_members = mysqli_fetch_assoc($members_result))
  {
    $temp2[] = (int) $row_members['ID_MEMBER'];
    if (count($context['top_time_online']) >= 10)
      continue;

    // Figure out the days, hours and minutes.
    $timeDays = floor($row_members['totalTimeLoggedIn'] / 86400);
    $timeHours = floor(($row_members['totalTimeLoggedIn'] % 86400) / 3600);

    // Figure out which things to show... (days, hours, minutes, etc.)
    $timelogged = '';
    if ($timeDays > 0)
      $timelogged .= $timeDays . $txt['totalTimeLogged5'];
    if ($timeHours > 0)
      $timelogged .= $timeHours . $txt['totalTimeLogged6'];
    $timelogged .= floor(($row_members['totalTimeLoggedIn'] % 3600) / 60) . $txt['totalTimeLogged7'];

    $context['top_time_online'][] = array(
      'id' => $row_members['ID_MEMBER'],
      'name' => $row_members['realName'],
      'time_online' => $timelogged,
      'seconds_online' => $row_members['totalTimeLoggedIn'],
      'href' => $boardurl . '/perfil/' . $row_members['memberName'],
      'link' => '<a href="' . $boardurl . '/perfil/' . $row_members['realName'] . '">' . $row_members['realName'] . '</a>'
    );

    if ($max_time_online < $row_members['totalTimeLoggedIn'])
      $max_time_online = $row_members['totalTimeLoggedIn'];
  }
  mysqli_free_result($members_result);

  foreach ($context['top_time_online'] as $i => $member)
    $context['top_time_online'][$i]['time_percent'] = round(($member['seconds_online'] * 100) / $max_time_online);

  // Cache the ones we found for a bit, just so we don't have to look again.
  if ($temp !== $temp2)
    cache_put_data('stats_total_time_members', $temp2, 480);

  // Activity by month.
  $months_result = db_query("
    SELECT
      YEAR(date) AS stats_year, MONTH(date) AS stats_month, SUM(hits) AS hits, SUM(registers) AS registers, SUM(topics) AS topics, SUM(posts) AS posts, MAX(mostOn) AS mostOn, COUNT(*) AS numDays
    FROM {$db_prefix}log_activity
    GROUP BY stats_year, stats_month", __FILE__, __LINE__);
  $context['monthly'] = array();
  while ($row_months = mysqli_fetch_assoc($months_result))
  {
    $ID_MONTH = $row_months['stats_year'] . sprintf('%02d', $row_months['stats_month']);
    $expanded = !empty($_SESSION['expanded_stats'][$row_months['stats_year']]) && in_array($row_months['stats_month'], $_SESSION['expanded_stats'][$row_months['stats_year']]);

    $context['monthly'][$ID_MONTH] = array(
      'id' => $ID_MONTH,
      'date' => array(
        'month' => sprintf('%02d', $row_months['stats_month']),
        'year' => $row_months['stats_year']
      ),
      'href' => $scripturl . '?action=stats;' . ($expanded ? 'collapse' : 'expand') . '=' . $ID_MONTH . '#' . $ID_MONTH,
      'link' => '<a href="' . $scripturl . '?action=stats;' . ($expanded ? 'collapse' : 'expand') . '=' . $ID_MONTH . '#' . $ID_MONTH . '">' . $txt['months'][$row_months['stats_month']] . ' ' . $row_months['stats_year'] . '</a>',
      'month' => $txt['months'][$row_months['stats_month']],
      'year' => $row_months['stats_year'],
      'new_topics' => $row_months['topics'],
      'new_posts' => $row_months['posts'],
      'new_members' => $row_months['registers'],
      'most_members_online' => $row_months['mostOn'],
      'hits' => $row_months['hits'],
      'num_days' => $row_months['numDays'],
      'days' => array(),
      'expanded' => $expanded
    );
  }

  // This gets rid of the filesort on the query ;).
  krsort($context['monthly']);

  if (empty($_SESSION['expanded_stats']))
    return;

  $condition = array();
  foreach ($_SESSION['expanded_stats'] as $year => $months)
    if (!empty($months))
      $condition[] = "YEAR(date) = $year AND MONTH(date) IN (" . implode(', ', $months) . ')';

  // No daily stats to even look at?
  if (empty($condition))
    return;

  getDailyStats(implode(' OR ', $condition));
}

function getDailyStats($condition)
{
  global $context, $db_prefix;

  // Activity by day.
  $days_result = db_query("
    SELECT YEAR(date) AS stats_year, MONTH(date) AS stats_month, DAYOFMONTH(date) AS stats_day, topics, posts, registers, mostOn, hits
    FROM {$db_prefix}log_activity
    WHERE $condition
    ORDER BY stats_day ASC", __FILE__, __LINE__);
  while ($row_days = mysqli_fetch_assoc($days_result))
    $context['monthly'][$row_days['stats_year'] . sprintf('%02d', $row_days['stats_month'])]['days'][] = array(
      'day' => sprintf('%02d', $row_days['stats_day']),
      'month' => sprintf('%02d', $row_days['stats_month']),
      'year' => $row_days['stats_year'],
      'new_topics' => $row_days['topics'],
      'new_posts' => $row_days['posts'],
      'new_members' => $row_days['registers'],
      'most_members_online' => $row_days['mostOn'],
      'hits' => $row_days['hits']
    );
  mysqli_free_result($days_result);
}

// This is the function which returns stats to simple machines.org IF enabled!
// See http://www.simplemachines.org/about/stats.php for more info.
function SMStats()
{
  global $modSettings, $user_info, $forum_version;

  // First, is it disabled?
  if (empty($modSettings['allow_sm_stats']))
    die();

  // Are we saying who we are, and are we right? (OR an admin)
  if (!$user_info['is_admin'] && (!isset($_GET['sid']) || $_GET['sid'] != $modSettings['allow_sm_stats']))
    die();

  // Verify the referer...
  if (!$user_info['is_admin'] && (!isset($_SERVER['HTTP_REFERER']) || md5($_SERVER['HTTP_REFERER']) != '746cb59a1a0d5cf4bd240e5a67c73085'))
    die();

  // Get the actual stats.
  $stats_to_send = array(
    'UID' => $modSettings['allow_sm_stats'],
    'time_added' => time(),
    'members' => $modSettings['totalMembers'],
    'messages' => $modSettings['totalMessages'],
    'topics' => $modSettings['totalTopics'],
    'boards' => 0,
    'php_version' => PHP_VERSION,
    'mysql_version' => '',
    'smf_version' => $forum_version,
    'smfd_version' => $modSettings['smfVersion'],
  );

  $request = db_query("
    SELECT VERSION()", __FILE__, __LINE__);
  list ($stats_to_send['mysql_version']) = mysqli_fetch_row($request);
  mysqli_free_result($request);

  // Encode all the data, for security.
  foreach ($stats_to_send as $k => $v)
    $stats_to_send[$k] = urlencode($k) . '=' . urlencode($v);

  // Turn this into the query string!
  $stats_to_send = implode('&', $stats_to_send);

  // If we're an admin, just plonk them out.
  if ($user_info['is_admin'])
    echo $stats_to_send;
  else
  {
    // Connect to the collection script.
    $fp = @fsockopen("www.simplemachines.org", 80, $errno, $errstr);
    if ($fp)
    {
      $length = strlen($stats_to_send);

      $out = "POST /smf/stats/collect_stats.php HTTP/1.1\r\n";
      $out .= "Host: www.simplemachines.org\r\n";
      $out .= "Content-Type: application/x-www-form-urlencoded\r\n";
      $out .= "Content-Length: $length\r\n\r\n";
      $out .= "$stats_to_send\r\n";
      $out .= "Connection: Close\r\n\r\n";
      fwrite($fp, $out);
      fclose($fp);
    }
  }

  // Die.
  die('OK');
}

?>