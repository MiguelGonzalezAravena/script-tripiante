<?php
function template_main() {
  global $txt, $context, $settings, $modSettings, $ID_MEMBER, $boardurl, $db_prefix;

  echo '
    <div style="float: left; width: 757px; margin-right: 8px;">
      <div class="mennes">
        <div class="botnes">
          <ul>
            <li>
              <a href="' . $boardurl . '/favoritos/post/" title="Posts">Posts</a>
            </li>
            <li>
              <a href="' . $boardurl . '/favoritos/imagen/" title="Im&aacute;genes">Im&aacute;genes</a>
            </li>
          </ul>
          <div style="clear: both;"></div>
        </div>
      </div>
      <div class="clearBoth"></div>
      <div class="box_757">
        <div class="box_title" style="width: 757px;">
          <div class="box_txt box_757-34">
            <center>' . $txt['bookmark_list'] . '</center>
          </div>
          <div class="box_rss">
            <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 16px; height: 16px;" border="0"/>
          </div>
        </div>
        <div style="width: 747px; padding: 4px;" class="windowbg">
          <form action="' . $boardurl . '/favoritos/imagen/eliminar/" method="post">';

  $end = $modSettings['bookmarks_images'];
  $page = (int) $_GET['pag'];

  if (isset($page)) {
    $calc = ($page - 1) * $end;
    $start = $calc > 0 ? $calc : 0;
    $actualPage = $page;
  } else {
    $start = 0;
    $actualPage = 1;
  }

  $query = "
    SELECT ms.date, ms.points, ms.ID_PICTURE, ms.title, ms.ID_MEMBER, mem.realName, mem.ID_MEMBER, mem.memberName, bm.ID_MEMBER, bm.TYPE, bm.ID_TOPIC
    FROM ({$db_prefix}bookmarks AS bm, {$db_prefix}gallery_pic AS ms, {$db_prefix}members AS mem)
    WHERE bm.ID_MEMBER = $ID_MEMBER
    AND ms.ID_PICTURE = bm.ID_TOPIC
    AND mem.ID_MEMBER = ms.ID_MEMBER
    AND bm.TYPE = 'imagen'
    ORDER BY bm.ID_TOPIC DESC";

  $request = db_query("
    {$query}
    LIMIT {$start}, {$end}", __FILE__, __LINE__);

  $context['bookmarks'] = mysqli_num_rows($request);
  $records = $context['bookmarks'];

  if (!empty($context['bookmarks'])) {
    while ($row = mysqli_fetch_assoc($request)) {
      echo '
        <div class="entryf">
          <div class="icon">
            <img alt="Imagen" title="Imagen" src="' . $settings['images_url'] . '/icons/foto.gif" />
          </div>
          <div class="text_container">
            <div class="title">
              <a href="' . $boardurl . '/imagenes/ver/' . $row['ID_PICTURE'] . '/">' . $row['title'] . '</a>
            </div>
            <div style="margin: 0pt; float: left;" class="data">
              <p style="margin: 0px; padding: 0px;" align="right">
                ' . $txt['was_created_by'] . '
                <a style="color: #717171;" href="' . $boardurl . '/perfil/' . $row['memberName'] . '">' . $row['realName'] . '</a>
                |
                pts: ' . $row['points'] . '
                |
                <a title="' . $txt['send_to_friend'] . '" href="' . $boardurl . '/enviar-a-amigo/imagen-' . $row['ID_PICTURE'] . '">
                  <img alt="" src="' . $settings['images_url'] . '/icons/icono-enviar-mensaje.gif" />
                </a>
                |
                <input name="remove_bookmarks[]" type="checkbox" value="' . $row['ID_PICTURE'] . '" />
              </p>
            </div>
          </div>
        </div>';
    }

    mysqli_free_result($request);

    $request = db_query($query, __FILE__, __LINE__);
    $records = mysqli_num_rows($request);

    $previousPage = $actualPage - 1;
    $nextPage = $actualPage + 1;
    $lastPage = $records / $end;
    $residue = $records % $end;

    if ($residue > 0) {
      $lastPage = floor($lastPage) + 1;
    }

    echo '
          <div style="clear: left;"></div>
          <p style="margin-top: 5px; margin-left: 0px; margin-right: 0px; margin-bottom: 0px; padding: 0px;" align="right">
            <input class="login" style="font-size: 12px;" value="' . $txt['bookmark_delete'] . '" type="submit" name="send" />
          </p>
        </form>
      </div>';
  } else {
    echo '<div class="noesta">' . $txt['bookmark_list_empty'] . '</div>';
  }

  echo '
    </div>
    <div class="windowbgpag" style="width: 757px;">';

  if ($actualPage > 1) {
    echo '<a href="' . $boardurl . '/favoritos/imagen/pag-' . $previousPage . '">&#171; anterior</a>';
  }

  if ($actualPage < $lastPage) {
    echo '<a href="' . $boardurl . '/favoritos/imagen/pag-' . $nextPage . '">siguiente &#187;</a>';
  }

  echo '
            </div>
            <div class="clearBoth"></div>
            <div style="clear: both;">
          </div>
        </div>
      </div>
      <div width="160px" style="float: left; width: 160px;">
        <div style="float: left; margin-bottom:8px;" class="img_aletat">
          <div class="box_title" style="width: 160px;">
            <div class="box_txt img_aletat">Publicidad</div>
            <div class="box_rss">
              <img src="' . $settings['images_url'] . '/blank.gif" style="width: 16px; height: 16px;" border="0" alt="" />
            </div>
          </div>
          <div class="windowbg" style="width: 150px; padding: 4px;">
            <center>' . $modSettings['vertical'] . '</center>
          </div>
        </div>
      </div>
      <div style="clear:both"></div>
    </div>';
}

?>