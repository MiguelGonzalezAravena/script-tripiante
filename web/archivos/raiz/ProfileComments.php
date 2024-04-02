<?php
if (!defined('SMF'))
  die('Hacking attempt...');

function CommentsMain() {
  loadtemplate('ProfileComments');
  loadlanguage('Post');

  // Load the language files
  if (loadlanguage('ProfileComments') == false)
    loadLanguage('ProfileComments','english');

  // Profile Comments actions
  $subActions = array(
    'view' => 'view',
    'admin' => 'CommentsAdmin',
    'delete' => 'Delete',
  );

  $sa = $_REQUEST['sa'];

  // Follow the sa or just go to administration.
  if (!empty($subActions[$sa]))
    $subActions[$sa]();
  else
    view();
}

function view() {
  die(base64_decode('UG93ZXJlZCBieSBQcm9maWxlIENvbW1lbnRzIG1hZGUgYnkgdmJnYW1lcjQ1IGh0dHA6Ly93d3cuc21maGFja3MuY29t'));
}

function Delete() {
  global $db_prefix, $ID_MEMBER, $txt;

  is_not_guest();

  $id = (int) @$_REQUEST['id'];

  if (empty($id))
    fatal_error($txt['pcomments_err_nocom']);

  $dbresult = db_query("
    SELECT p.ID_COMMENT, p.ID_MEMBER, p.COMMENT_MEMBER_ID
    FROM {$db_prefix}profile_comments as p
    WHERE p.ID_COMMENT = $id", __FILE__, __LINE__);

  $row = mysqli_fetch_assoc($dbresult);

  mysqli_free_result($dbresult);

  if (allowedTo('pcomments_delete_any') || allowedTo('pcomments_delete_own') && $row['ID_MEMBER'] == $ID_MEMBER) {
    db_query("
      DELETE FROM {$db_prefix}profile_comments
      WHERE ID_COMMENT = $id LIMIT 1", __FILE__, __LINE__);

    header("Location: {$_SERVER['HTTP_REFERRER']}");
  } else {
    fatal_error($txt['pcomments_nodel'], false);
  }
}

function ShowUserBox($memCommID, $onlineColor = '') {
  global $settings, $db_prefix, $modSettings, $context, $boardurl;

  $end = $modSettings['profile_comments_limit'];
  $page = (int) $_GET['pag'];

  if (isset($_GET['pag'])) {
    $start = ($page - 1) * $end;
    $actualPage = $page;
  } else {
    $start = 0;
    $actualPage = 1;
  }

  $request = db_query("
    SELECT m.ID_MEMBER, m.realName, m.memberName, p.ID_MEMBER, p.ID_COMMENT, p.COMMENT_MEMBER_ID, p.date
    FROM ({$db_prefix}members AS m, {$db_prefix}profile_comments AS p)
    WHERE p.ID_MEMBER = m.ID_MEMBER
    AND p.COMMENT_MEMBER_ID = " . $context['member']['id'] . "
    ORDER BY p.ID_COMMENT DESC", __FILE__, __LINE__);

  $count = mysqli_num_rows($request);

  if ($count <= 0) {
    echo '<div id="no_muro" class="noesta">' . $context['member']['name'] . ' no tiene ning&uacute;n mensaje en su muro.</div>';
  } else {
    $request	=	db_query("
      SELECT m.ID_MEMBER, m.realName, m.memberName, m.avatar, p.ID_MEMBER, p.ID_COMMENT, p.subject, p.COMMENT_MEMBER_ID, p.date, p.comment
      FROM ({$db_prefix}members AS m, {$db_prefix}profile_comments AS p)
      WHERE p.ID_MEMBER = m.ID_MEMBER
      AND p.COMMENT_MEMBER_ID = " . $context['member']['id'] . "
      ORDER BY p.ID_COMMENT DESC
      LIMIT {$start}, {$end}", __FILE__, __LINE__);

    $context['murocomentarios'] = array();

    while ($row = mysqli_fetch_assoc($request))
      $context['murocomentarios'][] = array(
        'ID_COMMENT' => $row['ID_COMMENT'],
        'subject' => $row['subject'],
        'comment' => parse_bbc2($row['comment']),
        'date' => $row['date'],
        'ID_MEMBER' => $row['ID_MEMBER'],
        'memberName' => $row['memberName'],
        'realName' => $row['realName'],
        'avatar' => $row['avatar'],
      );

    foreach ($context['murocomentarios'] as $muro) {
      if (!empty($muro['subject'])) {
        if ($context['member']['name'] == $context['user']['name'] || $context['allow_admin']) {
        echo '<a onclick="if (!confirm(\'\xbfEstas seguro que deseas borrar este mensaje?\')) return false;" href="/eliminar-muro/', $muro['ID_COMMENT'], '/" title="Eliminar Mensaje"><img alt="Eliminar Mensaje" src="' . $settings['images_url'] . '/eliminar.gif" width="8px" height="8px" /></a>&#32;-&#32;';
        } 
        echo '<span style="margin-left:8px;"><img alt="" src="' . $settings['images_url'] . '/user.gif" /><b class="size13">' . parse_bbc2($muro['subject']) . '</b><center><span style="color:grey;font-size:11px;">(' . timeformat($muro['date']) . ')</span></center></span><hr />';
      } else if (!empty($muro['comment'])) {
        if ($context['member']['name'] == $context['user']['name'] || $context['allow_admin']) {
          echo '<a onclick="if (!confirm(\'\xbfEstas seguro que deseas borrar este mensaje?\')) return false;" href="/eliminar-muro/', $muro['ID_COMMENT'], '/" title="Eliminar Mensaje"><img alt="Eliminar Mensaje" src="' . $settings['images_url'] . '/eliminar.gif" width="8px" height="8px" /></a>&#32;-&#32;';
        }

        echo '<b><span style="font-size:12px;"><a href="/perfil/', $muro['memberName'], '" title="', $muro['realName'], '">', $muro['realName'], '</a></span> escribi&oacute; </b><span style="color:grey;font-size:10px;">(', timeformat($muro['date']), ')</span><table><tr><td valign="top"><img style="width:50px;height:50px;" alt="" src="', $muro['avatar'], '" onerror="error_avatar(this)" /></td><td valign="top" style="margin:0px;padding:4px;"><div style="overflow: hidden;">', $muro['comment'], '</div>';

        if($context['user']['is_logged']) {
          echo '<br /><br /><a href="/perfil/', $muro['memberName'], '/muro/" title="Escribe en el Muro de ', $muro['realName'], '">Escribe en el Muro de ', $muro['realName'], '</a>';
        }

        echo '</td></tr></table><hr />';
      }
    }

    $request = db_query("
      SELECT *
      FROM ({$db_prefix}members AS m, {$db_prefix}profile_comments AS p)
      WHERE p.ID_MEMBER = m.ID_MEMBER
      AND p.COMMENT_MEMBER_ID = " . $context['member']['id'], __FILE__, __LINE__);

    $records = mysqli_num_rows($request);
  }

  $previousPage = $actualPage - 1;
  $nextPage = $actualPage + 1;
  $lastPage = $records / $end;
  $residue = $records % $end;

  if ($residue > 0)
    $lastPage = floor($lastPage) + 1;

  echo '</div><div class="windowbgpag" style="width: 524px;">';

  if ($nextPage < $lastPage)
    echo '';

  if ($actualPage > 1)
    echo '<a href="' . $boardurl . '/perfil/' . $context['member']['name'] . '/muro-pag-' . $previousPage . '">&#171; anterior</a>';

  if ($actualPage < $lastPage)
    echo '<a href="' . $boardurl . '/perfil/' . $context['member']['name'] . '/muro-pag-' . $nextPage . '">siguiente &#187;</a>';

  echo '</div><div class="clearBoth"></div></div></div>';
}

?>