<?php

function template_main() {
  global $context, $settings, $txt, $scripturl, $modSettings, $db_prefix, $boardurl;

  if ($context['user']['is_guest']) {
    header('Location: /ingresar/');
  } else {
    // Contadores
    $contador1 = 0;
    $contador2 = 0;
    $contador3 = 0;
    $contador4 = 0;
    $contador5 = 0;
    $contador6 = 0;
    $contador7 = 0;
    $contador8 = 0;
    $contador9 = 0;
    $contador10 = 0;
    $contador11 = 0;

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
        'subject' => ssi_reducir($row['subject']),
        'cuenta' => $row['Cuenta'],
        'ID_TOPIC' => $row['ID_TOPIC'],
        'description' => $row['description'],
        'bname' => $row['bname'],
      );
    }

    mysqli_free_result($request);

    echo '
      <table align="center">
        <tr align="center">
          <td align="center">
        <div>
          <div class="box_300" align="left" style="float: left; margin-right: 8px;">
            <div class="box_title" style="width: 300px;">
              <div class="box_txt box_300-34">10 Posts m&aacute;s comentados</div>
              <div class="box_rss">
                <div class="icon_img">
                  <a href="' . $boardurl . '/rss/post-comments/">
                    <img alt="" src="' . $settings['images_url'] . '/icons/tpbig-v1-iconos.gif?v3.2.3" style="cursor: pointer; margin-top: -352px; display: inline;" />
                  </a>
                </div>
              </div>
            </div>
            <div class="windowbg" style="width: 292px; padding: 4px;">';

    foreach ($context['tcomentados'] as $total) {
      echo '
        <span class="size11">
          <b>' . $contador1++ . '&nbsp;-&nbsp;</b>
          &nbsp;
          <a title="' . censorText($total['subject']) . '" href="' . $boardurl . '/post/' . $total['ID_TOPIC'] . '/' . $total['description'] . '/' . ssi_amigable($total['subject']) . '.html">' . $total['subject'] . '</a>
          &nbsp;
          (' . $total['cuenta'] . ' com)
        </span>
        <br />';
    }

    echo '
        </div>
      </div>';

    // 10 Posts más vistos
    echo '
      <div class="box_300" align="left" style="float: left; margin-right: 8px;">
        <div class="box_title" style="width: 300px;">
          <div class="box_txt box_300-34">10 Posts m&aacute;s vistos</div>
          <div class="box_rss">
            <div class="icon_img">
              <a href="' . $boardurl . '/rss/post-visitas/">
                <img alt="" src="' . $settings['images_url'] . '/icons/tpbig-v1-iconos.gif?v3.2.3" style="cursor: pointer; margin-top: -352px; display: inline;" />
              </a>
            </div>
          </div>
        </div>
        <div class="windowbg" style="width: 292px; padding: 4px;">';

    foreach ($context['top_topics_views'] as $topic) {
      echo '
        <span class="size11">
          <b>'. $contador2++ .'&nbsp;-&nbsp;</b>
          <a title="' . censorText($topic['subject']) . '" href="' . $boardurl . '/post/' . $topic['id'] . '/' . $topic['board']['description'] . '/' . ssi_amigable($topic['subject']).'.html">' . ssi_reducir($topic['subject']) . '</a>
          &nbsp;
          (' . $topic['num_views'] . ' vis)
        </span>
        <br />';
    }

    echo '
        </div>
      </div>';

    // 10 Post con más puntos
    $request = db_query("
      SELECT m.subject, m.ID_TOPIC, t.ID_TOPIC, t.ID_BOARD, t.points, b.name AS bname
      FROM ({$db_prefix}topics AS t, {$db_prefix}messages AS m, {$db_prefix}boards AS b)
      WHERE t.ID_TOPIC = m.ID_TOPIC
      AND t.ID_BOARD = b.ID_BOARD
      ORDER BY t.points DESC
      LIMIT 10 ", __FILE__, __LINE__);

    $context['postpuntos'] = mysqli_num_rows($request);

    echo '
      <div class="box_300" align="left" style="float: left;">
        <div class="box_title" style="width: 300px;">
          <div class="box_txt box_300-34">10 Post con m&aacute;s puntos</div>
          <div class="box_rss">
            <div class="icon_img">
              <a href="' . $boardurl . '/rss/post-puntos/">
                <img alt="" src="' . $settings['images_url'] . '/icons/tpbig-v1-iconos.gif?v3.2.3" style="cursor: pointer; margin-top: -352px; display: inline;" />
              </a>
            </div>
          </div>
        </div>
        <div class="windowbg" style="width: 292px; padding: 4px;">';

    while ($row = mysqli_fetch_array($request)) {
      echo '
        <span class="size11">
          <b>' . $contador3++ . '&nbsp;-&nbsp;</b>
          <a title="' . censorText($row['titulo']) . '" href="' . $boardurl . '/post/' . $row['bname'] . '/' . $row['id'] . '/' . $row['subject'] . '.html">' . ssi_reducir($row['subject']) . '</a>
          &nbsp;
          (' . $row['points'] . ' pts)
        </span>
        <br />';
    }

    echo '
              </div>
            </div>
          </td>
        </tr>
      </table>';

    // 10 Usuarios que más postean
    echo '
      <table align="center">
        <tr align="center">
        <td align="center">
          <div style="margin-top: 8px;">
            <div class="box_300" align="left" style="float: left; margin-right: 8px;">
              <div class="box_title" style="width: 300px;">
                <div class="box_txt box_300-34">10 Principales Posteadores</div>
                <div class="box_rss">
                  <div class="icon_img">
                    <a href="' . $boardurl . '/rss/poster/">
                      <img alt="" src="' . $settings['images_url'] . '/icons/tpbig-v1-iconos.gif?v3.2.3" style="cursor: pointer; margin-top: -352px; display: inline;" />
                    </a>
                  </div>
                </div>
              </div>
              <div class="windowbg" style="width: 292px; padding: 4px;">';

    foreach ($context['top_starters'] as $poster) {
      echo '
        <span class="size11">
          <b>' . $contador4++ . '&nbsp;-&nbsp;</b>
          <a title="' . censorText($poster['name']) . '" href="' . $boardurl . '/perfil/' . $poster['name'] . '">' . censorText($poster['name']) . '</a>
          &nbsp;
          (' . $poster['num_topics'] . ' posts)
        </span>
        <br />';
    }

    echo '
        </div>
      </div>
      <div class="box_300" align="left" style="float: left; margin-right: 8px;">
        <div class="box_title" style="width: 300px;">
          <div class="box_txt box_300-34">10 Usuarios con m&aacute;s puntos</div>
          <div class="box_rss">
            <div class="icon_img">
              <a href="' . $boardurl . '/rss/user-pts/">
                <img alt="" src="' . $settings['images_url'] . '/icons/tpbig-v1-iconos.gif?v3.2.3" style="cursor: pointer; margin-top: -352px; display: inline;" />
              </a>
            </div>
          </div>
        </div>
        <div class="windowbg" style="width: 292px; padding: 4px;">';

    foreach ($context['shop_richest'] as $row) {
      echo '
        <span class="size11">
          <b>' . $contador5++ . '&nbsp;-&nbsp;</b>
          &nbsp;
          <a title="' . censorText($row['realName']) . '" href="' . $boardurl . '/perfil/' . censorText($row['realName']) . '">' . censorText($row['realName']) . '</a>
          &nbsp;
          (' . $row['money'] . ' pts)
        </span>
        <br />';
    }

    echo  '
        </div>
      </div>';

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
        'realName' => $row['realName'],
        'memberName' => $row['memberName'],
        'total' => $row['total'],
      );
    }

    mysqli_free_result($request);

    echo '
      <div class="box_300" align="left" style="float:left;">
        <div class="box_title" style="width: 300px;">
          <div class="box_txt box_300-34">10 Usuarios que m&aacute;s comentan</div>
          <div class="box_rss">
            <div class="icon_img">
              <a href="' . $boardurl . '/rss/user-cmt/">
                <img alt="" src="' . $settings['images_url'] . '/icons/tpbig-v1-iconos.gif?v3.2.3" style="cursor: pointer; margin-top: -352px; display: inline;" />
              </a>
            </div>
          </div>
        </div>
        <div class="windowbg" style="width: 292px; padding: 4px;">';

    foreach ($context['mascomentan'] as $row) {
      echo '
      <span class="size11">
        <b>' . $contador6++ . '</b>&nbsp;-&nbsp;
        <a href="' . $boardurl . '/perfil/' . censorText($row['memberName']) . '" title="' . censorText($row['realName']) . '">' . censorText($row['realName']) . '</a>
        &nbsp;
        (' . $row['total'] . ' com)
      </span>
      <br />';
    }

    echo '
        </div>
      </div>';

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
        'title' => ssi_reducir($row['title']),
        'cuenta' => $row['cuenta'],
        'ID_PICTURE' => $row['ID_PICTURE'],
      );
    }

    mysqli_free_result($request);

    echo '
      <div>
        <div style="clear: left;"></div>
        <div style="margin-top: 8px;">
          <div class="box_300" align="left" style="float: left; margin-right: 8px;">
            <div class="box_title" style="width: 300px;">
              <div class="box_txt box_300-34">10 Im&aacute;genes m&aacute;s comentadas</div>
              <div class="box_rss">
                <div class="icon_img">
                  <a href="' . $boardurl . '/rss/img-comentadas/">
                    <img alt="" src="' . $settings['images_url'] . '/icons/tpbig-v1-iconos.gif?v3.2.3" style="cursor: pointer; margin-top: -352px; display: inline;" />
                  </a>
                </div>
              </div>
            </div>
            <div class="windowbg" style="width: 292px; padding: 4px;">';

    foreach ($context['topimagenescom'] as $row) {
      echo '
        <span class="size11">
          <b>' . $contador7++ . '&nbsp;-&nbsp;</b>
          <a title="' . $row['title'] . '" href="' . $boardurl . '/imagenes/ver/' . $row['ID_PICTURE'] . '">' . $row['title'] . '</a>
          &nbsp;
          (' . $row['cuenta'] . ' com)
        </span>
        <br />';
    }

    echo '
        </div>
      </div>
      <div class="box_300" align="left" style="float: left; margin-right: 8px;">
        <div class="box_title" style="width: 300px;">
          <div class="box_txt box_300-34">10 Im&aacute;genes m&aacute;s vistas</div>
          <div class="box_rss">
            <div class="icon_img">
              <a href="' . $boardurl . '/rss/img-visitas/">
                <img alt="" src="' . $settings['images_url'] . '/icons/tpbig-v1-iconos.gif?v3.2.3" style="cursor: pointer; margin-top: -352px; display: inline;" />
              </a>
            </div>
          </div>
        </div>
        <div class="windowbg" style="width: 292px; padding: 4px;">';

    foreach ($context['imgv'] as $imgv) {
      echo '
        <span class="size11">
          <b>' . $contador8++ . '&nbsp;-&nbsp;</b>
          <a title="' . censorText($imgv['titulo']) . '" href="' . $boardurl . '/imagenes/ver/' . $imgv['id'] . '">' . ssi_reducir($imgv['titulo']) . '</a>
          &nbsp;
          (' . $imgv['v'] . ' vis)
        </span>
        <br />';
    }

    echo '
        </div>
      </div>
      <div class="box_300" align="left" style="float: left;">
        <div class="box_title" style="width: 300px;">
          <div class="box_txt box_300-34">10 Im&aacute;genes con m&aacute;s puntos</div>
          <div class="box_rss">
            <div class="icon_img">
              <a href="' . $boardurl . '/rss/img-puntos/">
                <img alt="" src="' . $settings['images_url'] . '/icons/tpbig-v1-iconos.gif?v3.2.3" style="cursor: pointer; margin-top: -352px; display: inline;" />
              </a>
            </div>
          </div>
        </div>
        <div class="windowbg" style="width: 292px; padding: 4px;">';

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
        'points' => $row['points'],
        'ID_PICTURE' => $row['ID_PICTURE']
      );
    }

    mysqli_free_result($request);

    foreach ($context['comment-img3'] as $row) {
      echo '
        <span class="size11">
          <b>' . $contador9++ . '&nbsp;-&nbsp;</b>
          <a title="' . censorText($row['title']) . '" href="' . $boardurl . '/imagenes/ver/' . $row['ID_PICTURE'] . '">' . ssi_reducir($row['title']) . '</a>
          &nbsp;
          (' . $row['points'] . ' pts)
        </span>
        <br />';
    }

    echo '
        </div>
      </div>';

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
        'realName' => $row['realName'],
        'memberName' => $row['memberName'],
        'cuenta' => $row['cuenta'],
      );
    }

    mysqli_free_result($request);

    echo '
      </div>
      <div style="clear: left;"></div>
      <div style="margin-top: 8px;">
        <div class="box_300" align="left" style="float: left; margin-right: 8px;">
          <div class="box_title" style="width: 300px;">
            <div class="box_txt box_300-34">10 Muros m&aacute;s comentados</div>
            <div class="box_rss">
              <div class="icon_img">
                <a href="' . $boardurl . '/rss/muro-cmt/">
                  <img alt="" src="' . $settings['images_url'] . '/icons/tpbig-v1-iconos.gif?v3.2.3" style="cursor: pointer; margin-top: -352px; display: inline;" />
                </a>
              </div>
            </div>
          </div>
          <div class="windowbg" style="width: 292px; padding: 4px;">';

    foreach ($context['muroscomentados'] as $row) {
      echo '
        <span class="size11">
          <b>' . $contador10++ . '&nbsp;-&nbsp;</b>
          <a title="' . $row['realName'] . '" href="' . $boardurl . '/perfil/' . $row['memberName'] . '">' . $row['realName'] . '</a>
          &nbsp;
          (' . $row['cuenta'] . ' msj)
        </span>
        <br />';
    }

    echo '
        </div>
      </div>';

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
        'realName' => $row['realName'],
        'memberName' => $row['memberName'],
        'cuenta' => $row['cuenta'],
      );
    }

    mysqli_free_result($request);

    echo '
      <div class="box_300" align="left" style="float: left; margin-right: 8px;">
        <div class="box_title" style="width: 300px;">
          <div class="box_txt box_300-34">10 Usuarios con m&aacute;s im&aacute;genes</div>
            <div class="box_rss">
              <div class="icon_img">
                <a href="' . $boardurl . '/rss/user-img/">
                  <img alt="" src="' . $settings['images_url'] . '/icons/tpbig-v1-iconos.gif?v3.2.3" style="cursor: pointer; margin-top: -352px; display: inline;" />
                </a>
              </div>
            </div>
          </div>
          <div class="windowbg" style="width: 292px; padding: 4px;">';

    foreach ($context['imagenuser'] as $imagenuser) {
      echo '
        <span class="size11">
          <b>'. $contador11++ .'&nbsp;-&nbsp;</b>
          <a href="' . $boardurl . '/perfil/', $imagenuser['memberName'], '" title="', $imagenuser['realName'], '">', $imagenuser['realName'], '</a>
          &nbsp;
          (', $imagenuser['cuenta'], ' img)
        </span>
        <br />';
    }

    echo '
        </div>
      </div>';

    // Publicidad
    echo '
      <div class="box_300" align="left" style="float: left;">
        <div class="box_title" style="x">
          <div class="box_txt box_300-34">Publicidad</div>
          <div class="box_rss">
            <div class="icon_img">
              <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 14px; height: 12px;" border="0" />
            </div>
          </div>
        </div>
        <div class="windowbg" style="width: 292px; padding: 4px;">';

    ssi_destacados();

    echo '
          </div>
        </div>
      </div>
      <div style="clear: left;"></div>';

    if ($context['allow_admin']) {
      echo '
        <table border="0" width="100%" cellspacing="1" cellpadding="4" class="bordercolor">
          <tr class="titlebg">
            <td align="center" colspan="4">', $context['page_title'], '</td>
          </tr>
          <tr>
            <td class="catbg" colspan="4"><b>', $txt['smf_stats_2'], '</b></td>
          </tr>
          <tr>
            <td class="windowbg" width="20" valign="middle" align="center">
              <img src="', $settings['images_url'], '/stats_info.gif" width="20" height="20" alt="" />
            </td>
            <td class="windowbg2" valign="top">
              <table border="0" cellpadding="1" cellspacing="0" width="100%">
                <tr>
                  <td nowrap="nowrap">', $txt[488], ':</td>
                  <td align="right">', $context['show_member_list'] ? '<a href="' . $scripturl . '?action=mlist">' . $context['num_members'] . '</a>' : $context['num_members'], '</td>
                </tr>
                <tr>
                  <td nowrap="nowrap">', $txt[489], ':</td>
                  <td align="right">', $context['num_posts'], '</td>
                </tr>
                <tr>
                  <td nowrap="nowrap">', $txt[490], ':</td>
                  <td align="right">', $context['num_topics'], '</td>
                </tr>
                <tr>
                  <td nowrap="nowrap">', $txt[658], ':</td>
                  <td align="right">', $context['num_categories'], '</td>
                </tr>
                <tr>
                  <td nowrap="nowrap">', $txt['users_online'], ':</td>
                  <td align="right">', $context['users_online'], '</td>
                </tr>
                <tr>
                  <td nowrap="nowrap" valign="top">', $txt[888], ':</td>
                  <td align="right">', $context['most_members_online']['number'], ' - ', $context['most_members_online']['date'], '</td>
                </tr>
                <tr>
                  <td nowrap="nowrap">', $txt['users_online_today'], ':</td>
                  <td align="right">', $context['online_today'], '</td>';
      if (!empty($modSettings['hitStats'])) {
        echo '
                </tr>
                <tr>
                  <td nowrap="nowrap">', $txt['num_hits'], ':</td>
                  <td align="right">', $context['num_hits'], '</td>';
      }

      echo '
                </tr>
              </table>
            </td>
            <td class="windowbg" width="20" valign="middle" align="center">
              <img src="', $settings['images_url'], '/stats_info.gif" width="20" height="20" alt="" />
            </td>
            <td class="windowbg2" valign="top">
              <table border="0" cellpadding="1" cellspacing="0" width="100%">
                <tr>
                  <td nowrap="nowrap">', $txt['average_members'], ':</td>
                  <td align="right">', $context['average_members'], '</td>
                </tr>
                <tr>
                  <td nowrap="nowrap">', $txt['average_posts'], ':</td>
                  <td align="right">', $context['average_posts'], '</td>
                </tr>
                <tr>
                  <td nowrap="nowrap">', $txt['average_topics'], ':</td>
                  <td align="right">', $context['average_topics'], '</td>
                </tr>
                <tr>
                  <td nowrap="nowrap">', $txt[665], ':</td>
                  <td align="right">', $context['num_boards'], '</td>
                </tr>
                <tr>
                  <td nowrap="nowrap">', $txt[656], ':</td>
                  <td align="right">', $context['common_stats']['latest_member']['link'], '</td>
                </tr>
                <tr>
                  <td nowrap="nowrap">', $txt['average_online'], ':</td>
                  <td align="right">', $context['average_online'], '</td>
                </tr>
                <tr>
                  <td nowrap="nowrap">', $txt['gender_ratio'], ':</td>
                  <td align="right">', $context['gender']['ratio'], '</td>';
      if (!empty($modSettings['hitStats'])) {
        echo '
                </tr>
                <tr>
                  <td nowrap="nowrap">', $txt['average_hits'], ':</td>
                  <td align="right">', $context['average_hits'], '</td>';
      }

      echo '
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td class="catbg" colspan="2" width="50%"><b>', $txt['smf_stats_4'], '</b></td>
          </tr>';

      echo '
            <td class="windowbg" width="20" valign="middle" align="center">
              <img src="', $settings['images_url'], '/stats_board.gif" width="20" height="20" alt="" />
            </td>
            <td class="windowbg2" width="50%" valign="top">
              <table border="0" cellpadding="1" cellspacing="0" width="100%">';

      foreach ($context['top_boards'] as $board) {
        echo '
                <tr>
                  <td width="60%" valign="top">', $board['link'], '</td>
                  <td width="20%" align="left" valign="top">', $board['num_posts'] > 0 ? '<img src="' . $settings['images_url'] . '/bar.gif" width="' . $board['post_percent'] . '" height="15" alt="" />' : '&nbsp;', '</td>
                  <td width="20%" align="right" valign="top">', $board['num_posts'], '</td>
                </tr>';
      }

      echo '
              </table>
            </td>
          </tr>
          <tr>
            <td class="catbg" colspan="2" width="50%"><b>', $txt['smf_stats_12'], '</b></td>
          </tr>';

      echo '
            <td class="windowbg" width="20" valign="middle" align="center">
              <img src="', $settings['images_url'], '/stats_views.gif" width="20" height="20" alt="" />
            </td>
            <td class="windowbg2" width="50%" valign="top">
              <table border="0" cellpadding="1" cellspacing="0" width="100%">';

      foreach ($context['top_topics_views'] as $topic) {
        echo '
                <tr>
                  <td width="60%" valign="top">', $topic['link'], '</td>
                  <td width="20%" align="left" valign="top">', $topic['num_views'] > 0 ? '<img src="' . $settings['images_url'] . '/bar.gif" width="' . $topic['post_percent'] . '" height="15" alt="" />' : '&nbsp;', '</td>
                  <td width="20%" align="right" valign="top">', $topic['num_views'], '</td>
                </tr>';
      }

      echo '
              </table>
            </td>
          </tr>
          <tr>
            <td class="catbg" colspan="2" width="50%"><b>', $txt['smf_stats_15'], '</b></td>
            <td class="catbg" colspan="2" width="50%"><b>', $txt['smf_stats_16'], '</b></td>
          </tr>
          <tr>
            <td class="windowbg" width="20" valign="middle" align="center">
              <img src="', $settings['images_url'], '/stats_replies.gif" width="20" height="20" alt="" />
            </td>
            <td class="windowbg2" width="50%" valign="top">
              <table border="0" cellpadding="1" cellspacing="0" width="100%">';

      foreach ($context['top_starters'] as $poster) {
        echo '
                <tr>
                  <td width="60%" valign="top">', $poster['link'], '</td>
                  <td width="20%" align="left" valign="top">', $poster['num_topics'] > 0 ? '<img src="' . $settings['images_url'] . '/bar.gif" width="' . $poster['post_percent'] . '" height="15" alt="" />' : '&nbsp;', '</td>
                  <td width="20%" align="right" valign="top">', $poster['num_topics'], '</td>
                </tr>';
      }

      echo '
              </table>
            </td>
            <td class="windowbg" width="20" valign="middle" align="center" nowrap="nowrap"><img src="', $settings['images_url'], '/stats_views.gif" width="20" height="20" alt="" /></td>
            <td class="windowbg2" width="50%" valign="top">
              <table border="0" cellpadding="1" cellspacing="0" width="100%">';

      foreach ($context['top_time_online'] as $poster) {
        echo '
                <tr>
                  <td width="60%" valign="top">', $poster['link'], '</td>
                  <td width="20%" align="left" valign="top">', $poster['time_online'] > 0 ? '<img src="' . $settings['images_url'] . '/bar.gif" width="' . $poster['time_percent'] . '" height="15" alt="" />' : '&nbsp;', '</td>
                  <td width="20%" align="right" valign="top" nowrap="nowrap">', $poster['time_online'], '</td>
                </tr>';
      }

      echo '
              </table>
            </td>
          </tr><tr>
            <td class="catbg" colspan="4"><b>', $txt['smf_stats_5'], '</b></td>
          </tr><tr>
            <td class="windowbg" width="20" valign="middle" align="center">
              <img src="', $settings['images_url'], '/stats_history.gif" width="20" height="20" alt="" />
            </td>
            <td class="windowbg2" colspan="4">';

      if (!empty($context['monthly'])) {
        echo '
              <table border="0" width="100%" cellspacing="1" cellpadding="4" class="tborder" style="margin-bottom: 1ex;" id="stats">
                <tr class="titlebg" valign="middle" align="center">
                  <td width="25%">', $txt['smf_stats_13'], '</td>
                  <td width="15%">', $txt['smf_stats_7'], '</td>
                  <td width="15%">', $txt['smf_stats_8'], '</td>
                  <td width="15%">', $txt['smf_stats_9'], '</td>
                  <td width="15%">', $txt['smf_stats_14'], '</td>';
        if (!empty($modSettings['hitStats'])) {
          echo '
                  <td>', $txt['smf_stats_10'], '</td>';
        }

        echo '
                </tr>';

        foreach ($context['monthly'] as $month) {
          echo '
                <tr class="windowbg2" valign="middle" id="tr_', $month['id'], '">
                  <th align="left" width="25%">
                    <a name="', $month['id'], '" id="link_', $month['id'], '" href="', $month['href'], '" onclick="return doingExpandCollapse || expand_collapse(\'', $month['id'], '\', ', $month['num_days'], ');"><img src="', $settings['images_url'], '/', $month['expanded'] ? 'collapse.gif' : 'expand.gif', '" alt="" id="img_', $month['id'], '" /> ', $month['month'], ' ', $month['year'], '</a>
                  </th>
                  <th align="center" width="15%">', $month['new_topics'], '</th>
                  <th align="center" width="15%">', $month['new_posts'], '</th>
                  <th align="center" width="15%">', $month['new_members'], '</th>
                  <th align="center" width="15%">', $month['most_members_online'], '</th>';

          if (!empty($modSettings['hitStats'])) {
            echo '
                  <th align="center">', $month['hits'], '</th>';
          }

          echo '
                </tr>';

          if ($month['expanded']) {
            foreach ($month['days'] as $day) {
              echo '
                <tr class="windowbg2" valign="middle" align="left">
                  <td align="left" style="padding-left: 3ex;">', $day['year'], '-', $day['month'], '-', $day['day'], '</td>
                  <td align="center">', $day['new_topics'], '</td>
                  <td align="center">', $day['new_posts'], '</td>
                  <td align="center">', $day['new_members'], '</td>
                  <td align="center">', $day['most_members_online'], '</td>';

              if (!empty($modSettings['hitStats'])) {
                echo '
                  <td align="center">', $day['hits'], '</td>';
              }

              echo '
                </tr>';
            }
          }
        }

        echo '
              </table>';
      }

      echo '
            </td>
          </tr>
        </table>
        <script language="javascript" type="text/javascript">
          <!-- // -->
          <![CDATA[
          var doingExpandCollapse = false;

          function expand_collapse(curId, numDays) {
            if (window.XMLHttpRequest) {
              if (document.getElementById("img_" + curId).src.indexOf("expand") > 0) {
                if (typeof window.ajax_indicator == "function")
                  ajax_indicator(true);

                getXMLDocument(smf_scripturl + "?action=stats;expand=" + curId + ";xml", onDocReceived);
                doingExpandCollapse = true;
              } else {
                var myTable = document.getElementById("stats"), i;
                var start = document.getElementById("tr_" + curId).rowIndex + 1;
                for (i = 0; i < numDays; i++)
                  myTable.deleteRow(start);

                // Adjust the image and link.
                document.getElementById("img_" + curId).src = smf_images_url + "/expand.gif";
                document.getElementById("link_" + curId).href = smf_scripturl + "?action=stats;expand=" + curId + "#" + curId;

                // Modify the session variables.
                getXMLDocument(smf_scripturl + "?action=stats;collapse=" + curId + ";xml");
              }

              return false;
            }
            else
              return true;
          }

          function onDocReceived(XMLDoc) {
            var numMonths = XMLDoc.getElementsByTagName("month").length, i, j, k, numDays, curDay, start;
            var myTable = document.getElementById("stats"), curId, myRow, myCell, myData;
            var dataCells = [
              "date",
              "new_topics",
              "new_posts",
              "new_members",
              "most_members_online"
            ];

            if (numMonths > 0 && XMLDoc.getElementsByTagName("month")[0].getElementsByTagName("day").length > 0 && XMLDoc.getElementsByTagName("month")[0].getElementsByTagName("day")[0].getAttribute("hits") != null)
              dataCells[5] = "hits";

            for (i = 0; i < numMonths; i++) {
              numDays = XMLDoc.getElementsByTagName("month")[i].getElementsByTagName("day").length;
              curId = XMLDoc.getElementsByTagName("month")[i].getAttribute("id");
              start = document.getElementById("tr_" + curId).rowIndex + 1;

              for (j = 0; j < numDays; j++) {
                curDay = XMLDoc.getElementsByTagName("month")[i].getElementsByTagName("day")[j];
                myRow = myTable.insertRow(start + j);
                myRow.className = "windowbg2";

                for (k in dataCells) {
                  myCell = myRow.insertCell(-1);
                  if (dataCells[k] == "date")
                    myCell.style.paddingLeft = "3ex";
                  else
                    myCell.style.textAlign = "center";
                  myData = document.createTextNode(curDay.getAttribute(dataCells[k]));
                  myCell.appendChild(myData);
                }
              }

              // Adjust the arrow to point downwards.
              document.getElementById("img_" + curId).src = smf_images_url + "/collapse.gif";

              // Adjust the link to collapse instead of expand
              document.getElementById("link_" + curId).href = smf_scripturl + "?action=stats;collapse=" + curId + "#" + curId;
            }

            doingExpandCollapse = false;

            if (typeof window.ajax_indicator == "function")
              ajax_indicator(false);
          }
          // ]]>
        </script>';
    }

    echo '
            <div style="clear:both"></div>
            </div>
          </td>
        </tr>
      </table>';
  }
}

?>