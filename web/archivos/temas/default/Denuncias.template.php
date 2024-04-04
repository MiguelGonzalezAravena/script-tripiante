<?php
@require_once('SSI.php');

function template_main() {
  global $context, $settings, $modSettings, $db_prefix, $boardurl;

  $request = db_query("
    SELECT *
    FROM {$db_prefix}denunciations
    WHERE TYPE = 'post'", __FILE__, __LINE__);

  $context['denunciasss'] = mysqli_num_rows($request);

  $end = $modSettings['denunciations'];
  $page = (int) $_GET['pag'];

  if (isset($page)) {
    $start = ($page - 1) * $end;
    $actualPage = $page;
  } else {
    $start = 0;
    $actualPage = 1;
  }

  $query = "
    SELECT den.ID_DENUNCIATIONS, den.ID_TOPIC, den.ID_MEMBER, den.comment, den.reason, den.TYPE, m.ID_MEMBER,
    m.realName AS realName1, m.memberName AS memberName1, b.ID_BOARD, b.description, m2.ID_TOPIC, m2.ID_BOARD, 
    m2.subject, m2.ID_MEMBER, m3.ID_MEMBER, m3.recibir, m3.realName AS realName2, m3.memberName AS memberName2
    FROM ({$db_prefix}denunciations AS den, {$db_prefix}members AS m, {$db_prefix}boards AS b, {$db_prefix}messages as m2, {$db_prefix}members as m3)
    WHERE den.ID_TOPIC = m2.ID_TOPIC
    AND den.ID_MEMBER = m.ID_MEMBER
    AND m2.ID_BOARD = b.ID_BOARD
    AND den.TYPE = 'post'
    AND m3.ID_MEMBER = m2.ID_MEMBER
    ORDER BY den.ID_DENUNCIATIONS DESC";

  $request2 = db_query("
    {$query}
    LIMIT {$start}, {$end}", __FILE__, __LINE__);

  $count = mysqli_num_rows($request2);

  echo '
    <div style="float: left; width: 737px; margin-right: 8px;">
      <div class="mennes">
        <div class="botnes">
          <ul>
            <li>
              <a href="' . $boardurl . '/admin-denuncias/post/" title="Posts">Posts</a>
            </li>
            <li>
              <a href="' . $boardurl . '/admin-denuncias/imagen/" title="Im&aacute;genes">Im&aacute;genes</a>
            </li>
            <li>
              <a href="' . $boardurl . '/admin-denuncias/user/" title="Usuarios">Usuarios</a>
            </li>
            <li>
              <a href="' . $boardurl . '/admin-denuncias/comunidades/" title="Comunidades">Comunidades</a>
            </li>
          </ul>
          <div style="clear: both;"></div>
        </div>
      </div>
      <div class="clearBoth"></div>
      <form action="' . $boardurl . '/admin-denuncias/eliminar/" method="post" accept-charset="' . $context['character_set'] . '" name="eliminar" id="eliminar">
        <div class="box_745" style="float: left;">
          <div class="box_title" style="width: 745px;">
            <div style="text-align: center;" class="box_txt">
              <center>' . $context['page_title'] . '</center>
            </div>
            <div class="box_rss">
              <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 16px; height: 16px;" border="0" />
            </div>
          </div>
          <div class="windowbg" style="width: 737px; padding: 4px;">
            <table width="100%" style="padding: 4px; border: none;">';

  if ($count <= 0) {
    echo '<div class="noesta">No hay denuncias de posts realizados.</div>';
  } else {
    while ($row = mysqli_fetch_assoc($request2)) {
      $comentario = htmlspecialchars(censorText($row['comment']));

      echo '
        <td>
          <input type="checkbox" name="campos[' . $row['ID_DENUNCIATIONS'] . ']" />
        </td>
        <tr>
          <td width="20%">
            <b class="size11">Post Denunciado:&nbsp;</b>
          </td>
          <td>
            <a href="' . $boardurl . '/post/' . $row['ID_TOPIC'] . '/' . $row['description'] . '/' . ssi_amigable($row['subject']) . '.html" title="' . $row['subject'] . '">' . $row['subject'] . '</a>
          </td>
        </tr>
        <tr>
          <td width="20%">
            <b class="size11">Autor del post:&nbsp;</b>
          </td>
          <td>
            <div style="margin-bottom: 2px;">
              <span style="font-size: 12px;">
                <a href="' . $boardurl . '/perfil/' . $row['memberName2'] . '" title="' . $row['realName2'] . '">' . $row['realName2'] . '</a>';

      if ($row['recibir'] == 'si') {
        echo '
          &nbsp;
          <a href="' . $boardurl . '/mensajes/a/' . $row['memberName2'] . '" title="Enviar mensaje">
            <img alt="" src="' . $settings['images_url'] . '/icons/mensaje_para.gif" border="0" />
          </a>';
      }

      echo '
              </span>
            </div>
          </td>
        </tr>
        <tr>
          <td width="20%">
            <b class="size11">Informar del post:&nbsp;</b>
          </td>
          <td>';

      if ($row['recibir'] == 'si') {
        echo '<img src="' . $settings['images_url'] . '/icons/bullet-verde.gif" atl="" />';
      } else if ($row['recibir'] == 'no') {
        echo '<img src="' . $settings['images_url'] . '/icons/bullet-rojo.gif" atl="" />';
      }

      echo '
          </td>
        </tr>
        <tr>
          <td width="20%">
            <b class="size11">Usuario que denunci&oacute;:&oacute;</b>
          </td>
          <td>
            <a href="' . $boardurl . '/perfil/' . $row['memberName1'] . '" title="' . $row['realName1'] . '">' . $row['realName1'] . '</a>
          </td>
        </tr>
        <tr>
          <td width="20%">
            <b class="size11">Raz&oacute;n: </b>
          </td>
          <td>
            ' . $row['reason'] . '
          </td>
        </tr>
        <tr>
          <td width="20%">
            <b class="size11">Comentario:&nbsp;</b>
          </td>
          <td>' . $comentario . '</td>
        </tr>';
    }

    $request = db_query($query, __FILE__, __LINE__);
    $records = mysqli_num_rows($request);
  }

  $previousPage = $actualPage - 1;
  $nextPage = $actualPage + 1;
  $lastPage = $records / $end;
  $residue = $records % $end;

  if ($residue > 0) {
    $lastPage = floor($lastPage) + 1;
  }

  echo '
      </table>
    </div>
    <div class="windowbgpag" style="width: 757px;">';

  if ($actualPage > 1) {
    echo '<a href="' . $boardurl . '/admin-denuncias/post/pag-' . $previousPage . '">&#171; anterior</a>';
  }

  if ($actualPage < $lastPage) {
    echo '<a href="' . $boardurl . '/admin-denuncias/post/pag-' . $nextPage . '">siguiente &#187;</a>';
  }

  if (!$count <= 0) {
    echo '
      </div>
      <p align="right">
        <span class="size10">Denuncia/s Seleccionada/s:</span>
        &nbsp;
        <input class="login" style="font-size: 9px;" type="submit" value="Eliminar" />
      </p>';
  }

  echo '
      </div>
    </form>
    <div style="clear:both"></div>';
}

function template_imagen() {
  global $context, $settings, $modSettings, $db_prefix, $boardurl;

  $request = db_query("
    SELECT *
    FROM {$db_prefix}denunciations
    WHERE TYPE = 'imagen'", __FILE__, __LINE__);

  $context['denunciasss'] = mysqli_num_rows($request);

  $end = $modSettings['denunciations'];
  $page = (int) $_GET['pag'];

  if (isset($page)) {
    $start = ($page - 1) * $end;
    $actualPage = (int) $page;
  } else {
    $start = 0;
    $actualPage = 1;
  }

  $query = "
    SELECT den.ID_DENUNCIATIONS, den.ID_TOPIC, den.ID_MEMBER, den.comment, den.reason, den.TYPE, m.ID_MEMBER, m.realName, m.memberName, g.ID_PICTURE, g.title, g.ID_MEMBER
    FROM ({$db_prefix}denunciations AS den, {$db_prefix}members AS m, {$db_prefix}gallery_pic AS g)
    WHERE den.ID_TOPIC = g.ID_PICTURE
    AND den.ID_MEMBER = m.ID_MEMBER
    AND den.TYPE = 'imagen'
    ORDER BY den.ID_DENUNCIATIONS DESC";

  $request2 = db_query("
    {$query}
    LIMIT {$start}, {$end}", __FILE__, __LINE__);

  $count = mysqli_num_rows($request2);

  echo '
    <div style="float: left; width: 737px; margin-right: 8px;">
      <div class="mennes">
        <div class="botnes">
          <ul>
            <li>
              <a href="' . $boardurl . '/admin-denuncias/post/" title="Posts">Posts</a>
            </li>
            <li>
              <a href="' . $boardurl . '/admin-denuncias/imagen/" title="Im&aacute;genes">Im&aacute;genes</a>
            </li>
            <li>
              <a href="' . $boardurl . '/admin-denuncias/user/" title="Usuarios">Usuarios</a>
            </li>
            <li>
              <a href="' . $boardurl . '/admin-denuncias/comunidades/" title="Comunidades">Comunidades</a>
            </li>
          </ul>
          <div style="clear: both;"></div>
        </div>
      </div>
      <div class="clearBoth"></div>
      <form action="' . $boardurl . '/admin-denuncias/eliminar/" method="post" accept-charset="' . $context['character_set'] . '" name="eliminar" id="eliminar">
        <div class="box_745" style="float: left;">
          <div class="box_title" style="width: 745px;">
            <div style="text-align: center;" class="box_txt">
              <center>' . $context['page_title'] . '</center>
            </div>
            <div class="box_rss">
              <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 16px; height :16px;" border="0" />
            </div>
          </div>
          <div class="windowbg" style="width: 737px; padding: 4px;">
            <table width="100%" style="padding: 4px; border: none;">';

  if ($count <= 0) {
    echo '<div class="noesta">No hay denuncias de im&aacute;genes hechas.</div>';
  } else {
    while ($row = mysqli_fetch_assoc($request2)) {
      $comentario = htmlspecialchars(censorText($row['comment']));

      echo '
        <tr>
          <td>
            <input type="checkbox" name="campos[' . $row['ID_DENUNCIATIONS'] . ']" />
          </td>
        </tr>
        <tr>
          <td width="20%">
            <b class="size11">Post Denunciado:&nbsp;</b>
          </td>
          <td>
            <a href="' . $boardurl . '/imagenes/ver/' . $row['ID_TOPIC'] . '" title="' . $row['title'] . '">' . $row['title'] . '</a>
          </td>
        </tr>
        <tr>
          <td width="20%">
            <b class="size11">Usuario que denunci&oacute;:&nbsp;</b>
          </td>
          <td>
            <a href="' . $boardurl . '/perfil/' . $row['memberName'] . '" title="' . $row['realName'] . '">' . $row['realName'] . '</a>
          </td>
        </tr>
        <tr>
          <td width="20%">
            <b class="size11">Raz&oacute;n:&nbsp;</b>
          </td>
          <td>' . $row['reason'] . '</td>
        </tr>
        <tr>
          <td width="20%">
            <b class="size11">Comentario:&nbsp;</b>
          </td>
          <td>' . $comentario . '</td>
        </tr>';
    }

    $request = db_query($query, __FILE__, __LINE__);
    $records = mysqli_num_rows($request);
  }

  $previousPage = $actualPage - 1;
  $nextPage = $actualPage + 1;
  $lastPage = $records / $end;
  $residue = $records % $end;

  if ($residue > 0) {
    $lastPage = floor($lastPage) + 1;
  }

  echo '
      </table>
    </div>
    <div class="windowbgpag" style="width: 757px;">';

  if ($actualPage > 1) {
    echo '<a href="' . $boardurl . '/admin-denuncias/imagen/pag-' . $previousPage . '">&#171; anterior</a>';
  }

  if ($actualPage < $lastPage) {
    echo '<a href="' . $boardurl . '/admin-denuncias/imagen/pag-' . $nextPage . '">siguiente &#187;</a>';
  }

  if (!$count <= 0) {
    echo '
      </div>
      <p align="right">
        <span class="size10">Denuncia/s Seleccionada/s:</span>
        &nbsp;
        <input class="login" style="font-size: 9px;" type="submit" value="Eliminar" />
      </p>';
  }

  echo '
      </div>
    </form>
    <div style="clear:both"></div>';
}

function template_user() {
  global $context, $settings, $modSettings, $db_prefix, $boardurl;

  $request = db_query("
    SELECT *
    FROM {$db_prefix}denunciations
    WHERE TYPE = 'user'", __FILE__, __LINE__);

  $context['denunciasss'] = mysqli_num_rows($request);

  $end = $modSettings['denunciations'];
  $page = (int) $_GET['pag'];

  if (isset($page)) {
    $start = ($page - 1) * $end;
    $actualPage = (int) $page;
  } else {
    $start = 0;
    $actualPage = 1;
  }

  $query = "
    SELECT mem.ID_MEMBER, mem.memberName AS memberName1, mem.realName AS realName1, mem2.ID_MEMBER, mem2.memberName AS memberName2, mem2.realName AS realName2, den.ID_DENUNCIATIONS, den.ID_TOPIC, den.ID_MEMBER, den.TYPE, den.comment, den.reason
    FROM ({$db_prefix}members AS mem, {$db_prefix}members AS mem2, {$db_prefix}denunciations AS den)
    WHERE mem.ID_MEMBER = den.ID_MEMBER
    AND den.ID_TOPIC = mem2.ID_MEMBER
    AND den.TYPE = 'user'
    ORDER BY den.ID_DENUNCIATIONS DESC";

  $request2 = db_query("
    {$query}
    LIMIT {$start}, {$end}", __FILE__, __LINE__);

  $count = mysqli_num_rows($request2);

  echo '
    <div style="float: left; width: 737px; margin-right: 8px;">
      <div class="mennes">
        <div class="botnes">
          <ul>
            <li>
              <a href="' . $boardurl . '/admin-denuncias/post/" title="Posts">Posts</a>
            </li>
            <li>
              <a href="' . $boardurl . '/admin-denuncias/imagen/" title="Im&aacute;genes">Im&aacute;genes</a>
            </li>
            <li>
              <a href="' . $boardurl . '/admin-denuncias/user/" title="Usuarios">Usuarios</a>
            </li>
            <li>
              <a href="' . $boardurl . '/admin-denuncias/comunidades/" title="Comunidades">Comunidades</a>
            </li>
          </ul>
          <div style="clear: both;">
        </div>
      </div>
    </div>
    <div class="clearBoth"></div>
    <form action="' . $boardurl . '/admin-denuncias/eliminar/" method="post" accept-charset="' . $context['character_set'] . '" name="eliminar" id="eliminar">
      <div class="box_745" style="float: left;">
        <div class="box_title" style="width: 745px;">
          <div style="text-align: center;" class="box_txt">
            <center>' . $context['page_title'] . '</center>
          </div>
          <div class="box_rss">
            <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 16px; height: 16px;" border="0" />
          </div>
        </div>
        <div class="windowbg" style="width: 737px; padding: 4px;">
          <table width="100%" style="padding: 4px; border: none;">';

  if ($count <= 0) {
    echo '<div class="noesta">No hay denuncias de usuarios realizadas.</div>';
  } else {
    while ($row = mysqli_fetch_assoc($request2)) {
      $comentario = htmlspecialchars(censorText($row['comment']));

      echo '
        <tr>
          <td>
            <input type="checkbox" name="campos[' . $row['ID_DENUNCIATIONS'] . ']" />
          </td>
        </tr>
        <tr>
          <td width="20%">
            <b class="size11">Usuario Denunciado:&nbsp;</b>
          </td>
          <td>
            <a href="' . $boardurl . '/perfil/' . $row['memberName2'] . '" title="' . $row['realName2'] . '">' . $row['realName2'] . '</a>
          </td>
        </tr>
        <tr>
          <td width="20%">
            <b class="size11">Usuario que denunci&oacute;:&nbsp;</b>
          </td>
          <td>
            <a href="' . $boardurl . '/perfil/' . $row['memberName1'] . '" title="' . $row['realName1'] . '">' . $row['realName1'] . '</a>
          </td>
        </tr>
        <tr>
          <td width="20%">
            <b class="size11">Raz&oacute;n:&nbsp;</b>
          </td>
          <td>' . $row['reason'] . '</td>
        </tr>
        <tr>
          <td width="20%">
            <b class="size11">Comentario:&nbsp;</b>
          </td>
          <td>' . $comentario . '</td>
        </tr>';
    }

    $request = db_query($query, __FILE__, __LINE__);
    $records = mysqli_num_rows($request);
  }

  $previousPage = $actualPage - 1;
  $nextPage = $actualPage + 1;
  $lastPage = $records / $end;
  $residue = $records % $end;

  if ($residue > 0) {
    $lastPage = floor($lastPage) + 1;
  }

  echo '
      </table>
    </div>
    <div class="windowbgpag" style="width: 757px;">';

  if ($actualPage > 1) {
    echo '<a href="' . $boardurl . '/admin-denuncias/user/pag-' . $previousPage . '">&#171; anterior</a>';
  }

  if ($actualPage < $lastPage) {
    echo '<a href="' . $boardurl . '/admin-denuncias/user/pag-' . $nextPage . '">siguiente &#187;</a>';
  }

  if (!$count <= 0) {
    echo '
      <p align="right">
        <span class="size10">Denuncia/s Seleccionada/s:</span>
        &nbsp;<input class="login" style="font-size: 9px;" type="submit" value="Eliminar" />
      </p>';
  }

  echo '
      </div>
    </form>
    <div style="clear:both"></div>';
}

function template_comunidades() {
  global $context, $settings, $modSettings, $db_prefix, $boardurl;

  $request = db_query("
    SELECT *
    FROM {$db_prefix}denunciations
    WHERE TYPE = 'comunidad'", __FILE__, __LINE__);

  $context['denunciasss'] = mysqli_num_rows($request);

  $end = $modSettings['denunciations'];
  $page = (int) $_GET['pag'];

  if (isset($page)) {
    $start = ($page-1)*$end;
    $actualPage = $page;
  } else {
    $start = 0;
    $actualPage = 1;
  }

  $query = "
    SELECT *
    FROM ({$db_prefix}members AS mem, {$db_prefix}denunciations AS den, {$db_prefix}communities AS c)
    WHERE mem.ID_MEMBER = den.ID_MEMBER
    AND den.ID_TOPIC = c.ID_COMMUNITY
    AND den.TYPE = 'comunidad'
    ORDER BY den.ID_DENUNCIATIONS DESC";

  // Registros paginados
  $request2 = db_query("
    {$query}
    LIMIT {$start}, {$end}", __FILE__, __LINE__);

  $count = mysqli_num_rows($request2);

  echo '
    <div style="float: left; width: 737px; margin-right: 8px;">
      <div class="mennes">
        <div class="botnes">
          <ul>
            <li>
              <a href="' . $boardurl . '/admin-denuncias/post/" title="Posts">Posts</a>
            </li>
            <li>
              <a href="' . $boardurl . '/admin-denuncias/imagen/" title="Im&aacute;genes">Im&aacute;genes</a>
            </li>
            <li>
              <a href="' . $boardurl . '/admin-denuncias/user/" title="Usuarios">Usuarios</a>
            </li>
            <li>
              <a href="' . $boardurl . '/admin-denuncias/comunidades/" title="Comunidades">Comunidades</a>
            </li>
          </ul>
          <div style="clear: both;"></div>
        </div>
      </div>
      <div class="clearBoth"></div>
      <form action="' . $boardurl . '/admin-denuncias/eliminar/" method="post" accept-charset="' . $context['character_set'] . '" name="eliminar" id="eliminar">
        <div class="box_745" style="float: left;">
          <div class="box_title" style="width: 745px;">
            <div style="text-align: center;" class="box_txt">
              <center>' . $context['page_title'] . '</center>
            </div>
            <div class="box_rss">
              <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 16px; height: 16px;" border="0" />
            </div>
          </div>
          <div class="windowbg" style="width: 737px; padding: 4px;">
            <table width="100%" style="padding: 4px; border: none;">';

  if ($count <= 0) {
    echo '<div class="noesta">No hay denuncias de comunidades realizadas.</div>';
  } else {
    while ($row = mysqli_fetch_assoc($request2)) {
      $comentario = htmlspecialchars(censorText($row['comment']));

      echo '
        <tr>
          <td>
            <input type="checkbox" name="campos['.$row['ID_DENUNCIATIONS'].']" />
          </td>
        </tr>
        <tr>
          <td width="20%">
            <b class="size11">Comunidad Denunciada:&nbsp;</b>
          </td>
          <td>
            <a href="' . $boardurl . '/comunidades/' . $row['friendly_url'] . '" title="' . $row['friendly_url'] . '">' . $row['title'] . '</a>
          </td>
        </tr>
        <tr>
          <td width="20%">
            <b class="size11">Usuario que denunci&oacute;:&nbsp;</b>
          </td>
          <td>
            <a href="' . $boardurl . '/perfil/' . $row['memberName'] . '" title="' . $row['realName'] . '">' . $row['realName'] . '</a>
          </td>
        </tr>
        <tr>
          <td width="20%">
            <b class="size11">Raz&oacute;n:&nbsp;</b>
          </td>
          <td>' . $row['reason'] . '</td>
        </tr>
        <tr>
          <td width="20%">
            <b class="size11">Comentario:&nbsp;</b>
          </td>
          <td>' . $comentario . '</td>
        </tr>';
    }

    // Registros totales
    $request = db_query($query, __FILE__, __LINE__);
    $records = mysqli_num_rows($request);
  }

  $previousPage = $actualPage - 1;
  $nextPage = $actualPage + 1;
  $lastPage = $records / $end;
  $residue = $records % $end;

  if ($residue > 0) {
    $lastPage = floor($lastPage) + 1;
  }

  echo '
      </table>
    </div>
    <div class="windowbgpag" style="width: 757px;">';

  if ($actualPage > 1) {
    echo '<a href="' . $boardurl . '/admin-denuncias/comunidades/pag-' . $previousPage . '">&#171; anterior</a>';
  }

  if ($actualPage < $lastPage) {
    echo '<a href="' . $boardurl . '/admin-denuncias/comunidades/pag-' . $nextPage . '">siguiente &#187;</a>';
  }

  if (!$count <= 0) {
    echo '
      <p align="right">
        <span class="size10">Denuncia/s Seleccionada/s:</span>
        &nbsp;
        <input class="login" style="font-size: 9px;" type="submit" value="Eliminar" />
      </p>';
  }

  echo '
      </div>
    </form>
    <div style="clear:both"></div>';
}

function template_eliminar() {
  global $context, $db_prefix, $boardurl;

  if (!empty($_POST['campos']) || $context['allow_admin']) {
    $aLista = array_keys($_POST['campos']);

    db_query("
      DELETE FROM {$db_prefix}denunciations
      WHERE ID_DENUNCIATIONS IN (" . implode(',', $aLista) . ")", __FILE__, __LINE__);

    header('Location: ' . $boardurl . '/admin-denuncias/');
  }
}

?>