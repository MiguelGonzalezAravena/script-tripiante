<?php
if (!defined('SMF'))
  die('Hacking attempt...');

function ViewQuery() {
  global $scripturl, $settings, $context, $db_connection, $modSettings;

  // Don't allow except for administrators.
  isAllowedTo('admin_forum');

  // If we're just hiding/showing, do it now.
  if (isset($_REQUEST['sa']) && $_REQUEST['sa'] == 'hide') {
    $_SESSION['view_queries'] = $_SESSION['view_queries'] == 1 ? 0 : 1;

    if (strpos($_SESSION['old_url'], 'action=viewquery') !== false)
      redirectexit();
    else
      redirectexit($_SESSION['old_url']);
  }

  if (isset($modSettings['integrate_egg_nog']) && function_exists($modSettings['integrate_egg_nog']))
    call_user_func($modSettings['integrate_egg_nog']);

  $query_id = isset($_REQUEST['qq']) ? (int) $_REQUEST['qq'] - 1 : -1;

  echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html>
      <head>
        <title>' . $context['forum_name'] . '</title>
        <link rel="stylesheet" type="text/css" href="' . $settings['theme_url'] . '/style.css" />
        <style type="text/css">
          body
          {
            margin: 1ex;
          }
          body, td, th, .normaltext
          {
            font-size: x-small;
          }
          .smalltext
          {
            font-size: xx-small;
          }
        </style>
      </head>
      <body>';

  foreach ($_SESSION['debug'] as $q => $query_data) {
    // Fix the indentation....
    $query_data['q'] = ltrim(str_replace("\r", '', $query_data['q']), "\n");
    $query = explode("\n", $query_data['q']);
    $min_indent = 0;

    foreach ($query as $line) {
      preg_match('/^(\t*)/', $line, $temp);
      if (strlen($temp[0]) < $min_indent || $min_indent == 0)
        $min_indent = strlen($temp[0]);
    }

    foreach ($query as $l => $dummy)
      $query[$l] = substr($dummy, $min_indent);

    $query_data['q'] = implode("\n", $query);

    $is_select_query = substr(trim($query_data['q']), 0, 6) == 'SELECT';

    if ($is_select_query)
      $select = $query_data['q'];
    else if (preg_match('~^INSERT(?: IGNORE)? INTO \w+(?:\s+\([^)]+\))?\s+(SELECT .+)$~s', trim($query_data['q']), $matches) != 0) {
      $is_select_query = true;
      $select = $matches[1];
    } else if (preg_match('~^CREATE TEMPORARY TABLE .+?(SELECT .+)$~s', trim($query_data['q']), $matches) != 0) {
      $is_select_query = true;
      $select = $matches[1];
    }

    echo '
      <div id="qq' . $q . '" style="margin-bottom: 2ex;">
        <a' . $is_select_query ? ' href="' . $scripturl . '?action=viewquery;qq=' . ($q + 1) . '#qq' . $q . '"' : '' . ' style="font-weight: bold; color: black; text-decoration: none;">
          ' . nl2br(str_replace("\t", '&nbsp;&nbsp;&nbsp;', htmlspecialchars($query_data['q']))) . '
        </a><br />';

    if (!empty($query_data['f']) && !empty($query_data['l']))
      echo '
      in <i>', $query_data['f'], '</i> line <i>', $query_data['l'], '</i>, ';

    echo '
      which took ' . round($query_data['t'], 8) . ' seconds.
    </div>';

    // Explain the query.
    if ($query_id == $q && $is_select_query) {
      $result = db_query("
        EXPLAIN " . $select, false, false);

      if ($result === false) {
        echo '
          <table border="1" cellpadding="4" cellspacing="0" style="empty-cells: show; font-family: serif; margin-bottom: 2ex;">
            <tr><td>' . mysqli_error($db_connection) . '</td></tr>
          </table>';
        continue;
      }

      echo '<table border="1" rules="all" cellpadding="4" cellspacing="0" style="empty-cells: show; font-family: serif; margin-bottom: 2ex;">';

      $row = mysqli_fetch_assoc($result);

      echo '
        <tr>
          <th>' . implode('</th>
          <th>', array_keys($row)) . '</th>
        </tr>';

      mysqli_data_seek($result, 0);

      while ($row = mysqli_fetch_assoc($result)) {
        echo '
          <tr>
            <td>' . implode('</td>
            <td>', $row) . '</td>
          </tr>';
      }

      mysqli_free_result($result);

      echo '</table>';
    }
  }

  echo '
      </body>
    </html>';

  obExit(false);
}

?>