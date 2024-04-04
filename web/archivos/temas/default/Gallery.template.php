<?php

function template_main() {
  global $db_prefix, $txt, $context, $us;

  // Permissions
  isAllowedTo('smfgallery_view');

  $g_manage = allowedTo('smfgallery_manage');

  $us = htmlentities(addslashes($_REQUEST['user']), ENT_QUOTES, 'UTF-8');

  if (empty($us)) {
    fatal_error($txt['gallery_error_no_user_selected']);
  }

  $resp = db_query("
    SELECT *
    FROM {$db_prefix}members
    WHERE memberName = '$us'", __FILE__, __LINE__);

  $datos = mysqli_fetch_assoc($resp);

  if ($us != $datos['memberName']) {
    fatal_error('Este usuario no existe.-', false);
  }

  // TO-DO: ¿Esto está bien?
  @require_once($_SERVER['DOCUMENT_ROOT'] . '/web/archivos/temas/default/Profile.template.php');

  if($us == $context['user']['name']) {
    echo '
      <style type="text/css">
        .photo_small {
          width: 90px;
          margin: 6px;
          padding: 2px;
          text-align: left;
          background: #FFFFFF none repeat scroll 0%;
          border: 1px solid #000000;
        }
      </style>';

    menu();

    $end = 9;
    $page = (int) $_GET['pag'];

    if (isset($page)) {
      $start = ($page - 1) * $end;
      $actualPage = (int) $page;
    } else {
      $start = 0;
      $actualPage = 1;
    }

    $query = "
      SELECT g.ID_PICTURE, g.ID_MEMBER, g.title, g.filename, g.commenttotal, m.ID_MEMBER, m.realName, m.memberName
      FROM ({$db_prefix}gallery_pic AS g, {$db_prefix}members AS m)
      WHERE g.ID_MEMBER = m.ID_MEMBER
      AND m.memberName = '$us'
      ORDER BY g.ID_PICTURE DESC";

    // Registros paginados
    $request2 = db_query("
      {$query}      
      LIMIT {$start}, {$end}", __FILE__, __LINE__);

    echo '
      <div class="box_780" style="float: left;">
        <div class="box_title" style="width: 772px;">
          <div class="box_txt box_780-34">
            <center>Mis im&aacute;genes</center>
          </div>
          <div class="box_rss">
            <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 16px; height: 16px;" border="0" />
          </div>
        </div>
        <div class="windowbg" style="width: 764px; padding: 4px;">
          <table border="0" width="100%">';

    while ($row = mysqli_fetch_assoc($request2)) {
      $total = db_query("
        SELECT COUNT(ID_PICTURE) AS total
        FROM {$db_prefix}gallery_comment
        WHERE ID_PICTURE = " . $row['ID_PICTURE'], __FILE__, __LINE__);

      $total2 = mysqli_fetch_assoc($total);

      echo '
        <td width="70px">
          <div style="width: 90px;">
            <div class="photo_small">
              <a href="' . $boardurl . '/imagenes/ver/' . $row['ID_PICTURE'] . '">
                <img src="' . $row['filename'] . '" title="' . $row['title'] . '" onload="if (this.height > 68) { this.height = 68 }" style="width: 90px;" border="0"/>
              </a>
            </div>
            <div class="smalltext">
              <center>
                Comentarios:
                &nbsp;
                (<a href="' . $boardurl . '/imagenes/ver/' . $row['ID_PICTURE'] . '#comentarios">' . $total2['total'] . '</a>)
              </center>
            </div>
          </div>
        </td>';
    }

    // Registros totales
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
        </tr>
      </table>';

    $count = mysqli_num_rows($request2);

    if ($count <= 0) {
      echo '<div class="noesta">' . $datos['memberName'] . ' no tiene ninguna imagen.</div>';
    }

    echo '
      </div>
      <div class="windowbgpag" style="width: 780px;">';

    if ($actualPage > 1) {
      echo '<a href="' . $boardurl . '/imagenes/' . $datos['memberName'] . '/pag-' . $previousPage . '">&#171; anterior</a>';
    }

    if ($actualPage < $lastPage) {
      echo '<a href="' . $boardurl . '/imagenes/' . $datos['memberName'] . '/pag-' . $nextPage . '">siguiente &#187;</a>';
    }

    echo '
        </div>
        <div class="clearBoth"></div>
        <div style="clear: both;"></div>
      </div>
      <div style="clear:both"></div>';
  } else {
    $end = 9;
    $page = (int) $_GET['pag'];

    if (isset($page)) {
      $start = ($page - 1) * $end;
      $actualPage = $page;
    } else {
      $start = 0;
      $actualPage = 1;
    }

    $query = "
      SELECT g.ID_PICTURE, g.ID_MEMBER, g.title, g.filename, g.commenttotal, m.ID_MEMBER, m.realName, m.memberName
      FROM ({$db_prefix}gallery_pic AS g, {$db_prefix}members AS m)
      WHERE g.ID_MEMBER = m.ID_MEMBER
      AND m.memberName = '$us'
      ORDER BY g.ID_PICTURE DESC";

    $request2 = db_query("
      {$query}      
      LIMIT {$start}, {$end}", __FILE__, __LINE__);

    echo '
      <style type="text/css">
        .photo_small {
          width: 90px;
          margin: 6px;
          padding: 2px;
          text-align: left;
          background: #FFFFFF none repeat scroll 0%;
          border: 1px solid #000000;
        }
      </style>
      <div class="box_buscador">
        <div class="box_title" style="width: 915px;">
          <div class="box_txt box_buscadort">
            <center>
              Im&aacute;genes de ' . $datos['memberName'] . '
            </center>
          </div>
          <div class="box_rss">
            <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 14px; height: 12px;" border="0" />
          </div>
        </div>
        <div style="width: 907px; padding: 4px;" class="windowbg" border="0">
          <table border="0" width="100%">';

    while ($row = mysqli_fetch_assoc($request2)) {
      $total = db_query("
        SELECT COUNT(ID_PICTURE) AS total
        FROM {$db_prefix}gallery_comment
        WHERE ID_PICTURE = " . $row['ID_PICTURE'], __FILE__, __LINE__);

      $total2 = mysqli_fetch_assoc($total);

      echo '
        <td width="70px">
          <div style="width: 90px;">
            <div class="photo_small">
              <a href="' . $boardurl . '/imagenes/ver/' . $row['ID_PICTURE'] . '">
                <img src="' . $row['filename'] . '" title="' . $row['title'] . '" onload="if (this.height > 68) { this.height = 68 }" style="width: 90px;" border="0"/>
              </a>
            </div>
            <div class="smalltext">
              <center>
                Comentarios:
                &nbsp;(<a href="' . $boardurl . '/imagenes/ver/' . $row['ID_PICTURE'] . '#comentarios">' . $total2['total'] . '</a>)
              </center>
            </div>
          </div>
        </td>';
    }

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
        </tr>
      </table>';

    $count = mysqli_num_rows($request2);

    if ($count <= 0) {
      echo '<div class="noesta">' . $datos['memberName'] . ' no tiene ninguna imagen.</div>';
    }

    echo '
      </div>
      <div class="windowbgpag" style="width: 780px;">';

    if ($actualPage > 1) {
      echo '<a href="' . $boardurl . '/imagenes/' . $datos['memberName'] . '/pag-' . $previousPage . '">&#171; anterior</a>';
    }

    if ($actualPage < $lastPage) {
      echo '<a href="' . $boardurl . '/imagenes/' . $datos['memberName'] . '/pag-' . $nextPage . '">siguiente &#187;</a>';
    }
  }

  echo '
      </div>
      <div class="clearBoth"></div>
      <div style="clear: both;"></div>
    </div>
    <div style="clear:both"></div>';
}

function template_image_listing() {}

function template_add_picture() {}

function template_edit_picture() {
  global $scripturl, $modSettings, $txt, $context, $settings;

  // Load the spell checker?
  if ($context['show_spellchecking']) {
    echo '<script language="JavaScript" type="text/javascript" src="' . $settings['default_theme_url'] . '/spellcheck.js"></script>';
  }

  echo '
    <form method="POST" enctype="multipart/form-data" name="picform" id="picform" action="' . $scripturl . '?action=gallery&sa=edit2" accept-charset="' . $context['character_set'] . '">
      <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr class="catbg">
          <td width="50%" colspan="2"  align="center">
            <b>' . $txt['gallery_form_editpicture'] . '</b>
          </td>
        </tr>
        <tr class="windowbg2">
          <td align="right">
            <b>' . $txt['gallery_form_title'] . '</b>
            &nbsp;
          </td>
          <td>
            <input type="text" name="title" size="50" value="' . $context['gallery_pic']['title'] . '" />
          </td>
        </tr>
        <tr class="windowbg2">
          <td align="right">
            <b>' . $txt['gallery_form_category'] . '</b>
            &nbsp;
          </td>
          <td>
            <select name="cat">';

  foreach($context['gallery_cat_list'] as  $row) {
    echo '<option value="' . $row['ID_CAT']  . '" ' . (($context['gallery_pic']['ID_CAT'] == $row['ID_CAT']) ? ' selected="selected"' : '') . '>' . $row['title'] . '</option>';
  }

  echo '
        </select>
      </td>
    </tr>
    <tr class="windowbg2">
      <td align="right">
        <b>' . $txt['gallery_form_description'] . '</b>
        &nbsp;
      </td>
      <td>
        <textarea name="description" rows="6" cols="55">' . $context['gallery_pic']['description'] . '</textarea>';

  if ($context['show_spellchecking']) {
    echo '
      <br />
      <input type="button" value="' . $txt['spell_check'] . '" onclick="spellCheck(\'picform\', \'description\');" />';
  }

  echo '
      </td>
    </tr>
    <tr class="windowbg2">
      <td align="right">
        <b>' . $txt['gallery_form_keywords'] . '</b>
        &nbsp;
      </td>
      <td>
        <input type="text" name="keywords" maxlength="100" value="' . $context['gallery_pic']['keywords'] . '" />
      </td>
    </tr>
    <tr class="windowbg2">
      <td align="right" valign="top">
        <b>' . $txt['gallery_form_uploadpic'] . '</b>
        &nbsp;
      </td>
      <td>
        <input type="file" size="48" name="picture" />';

  if (!empty($modSettings['gallery_max_width'])) {
   echo '<br />' . $txt['gallery_form_maxwidth'] . $modSettings['gallery_max_width'] . $txt['gallery_form_pixels'];
  }

  if (!empty($modSettings['gallery_max_height'])) {
    echo '<br />' . $txt['gallery_form_maxheight'] . $modSettings['gallery_max_height'] . $txt['gallery_form_pixels'];
  }

  echo '
      </td>
    </tr>';

  if ($modSettings['gallery_commentchoice']) {
    echo '
      <tr class="windowbg2">
        <td align="right">
          <b>' . $txt['gallery_form_additionaloptions'] . '</b>
          &nbsp;
        </td>
        <td>
          <input type="checkbox" name="allowcomments" ' . ($context['gallery_pic']['allowcomments'] ? 'checked="checked"' : '') . ' />
          <b>' . $txt['gallery_form_allowcomments'] . '</b>
        </td>
      </tr>';
  }

  echo '
    <tr class="windowbg2">
      <td width="28%" colspan="2" height="26" align="center" class="windowbg2">
        <input type="hidden" name="id" value="' . $context['gallery_pic']['ID_PICTURE'] . '" />
        <input type="submit" value="' . $txt['gallery_form_editpicture'] . '" name="submit" />
        <br />';

  if (!allowedTo('smfgallery_autoapprove')) {
    echo $txt['gallery_form_notapproved'];
  }

  echo '
          <div align="center">
            <br />
            <b>' . $txt['gallery_text_oldpicture'] . '</b>
            <br />
            <a href="' . $scripturl . '?action=gallery;sa=view;id=' . $context['gallery_pic']['ID_PICTURE'] . '" target="blank">
              <img src="' . $modSettings['gallery_url'] . $context['gallery_pic']['thumbfilename']  . '" border="0" />
            </a>
            <br />
            <span class="smalltext">' . $txt['gallery_text_views'] . $context['gallery_pic']['views'] . '
            <br />
            ' . $txt['gallery_text_filesize'] . $context['gallery_pic']['filesize'] . 'kb<br />
            ' . $txt['gallery_text_date'] . $context['gallery_pic']['date'] . '<br />
          </div>
        </td>
        </tr>
      </table>
    </form>';

  if ($context['show_spellchecking']) {
    echo '
      <form action="' . $scripturl . '?action=spellcheck" method="post" accept-charset="' . $context['character_set'] . '" name="spell_form" id="spell_form" target="spellWindow">
        <input type="hidden" name="spellstring" value="" />
      </form>';
  }
}

function template_view_picture() {
  global $context, $modSettings, $db_prefix, $settings, $boardurl, $options;

  // Cargar permisos
  $id = (int) $_GET['id'];
  $iduser = $context['gallery_pic']['ID_MEMBER'];
  $g_manage = allowedTo('smfgallery_manage');
  $g_edit_own = allowedTo('smfgallery_edit');
  $g_delete_own = allowedTo('smfgallery_delete');
  $g_add = allowedTo('smfgallery_add');
  $bbc_check = function_exists('parse_bbc');

  @require_once('SSI.php');

  $request = db_query("
    SELECT *
    FROM {$db_prefix}gallery_pic
    WHERE ID_PICTURE = " . $context['gallery_pic']['ID_PICTURE'], __FILE__, __LINE__);

  while ($row = mysqli_fetch_assoc($request)) {
    $fecha = timeformat($row['date']);
    $context['numcom'] = $row['commenttotal'];
    $context['puntos'] = $row['puntos'];
    $context['num_views'] = $row['views'];
  }

  mysqli_free_result($request);

  $request = db_query("
    SELECT DISTINCT g.ID_PICTURE, g.points, g.title, g.ID_MEMBER, mem.ID_MEMBER
    FROM ({$db_prefix}gallery_pic AS g, {$db_prefix}members AS mem)
    WHERE g.ID_MEMBER = mem.ID_MEMBER
    ORDER BY RAND()
    LIMIT 10", __FILE__, __LINE__);

  $context['al-azar'] = array();

  while ($row = mysqli_fetch_assoc($request)) {
    $context['al-azar'][] = array(
      'title' => $row['title'],
      'points' => $row['points'],
      'ID_PICTURE' => $row['ID_PICTURE'],
    );
  }

  mysqli_free_result($request);

  ssi_grupos();

  $request = db_query("
    SELECT den.ID_TOPIC, den.TYPE
    FROM {$db_user}denunciations AS den
    WHERE den.ID_TOPIC = {$id}
    AND den.TYPE = 'imagen'", __FILE__, __LINE__);

  $context['contando'] = mysqli_num_rows($request);

  if ($context['contando'] > 5 && empty($context['user']['is_admin'])) {
    fatal_error('Imagen eliminada por acumulaci&oacute;n de denuncias, se encuentra en proceso de revisi&oacute;n.', false);
  }

  if ($context['contando'] > 5 && $context['user']['is_admin']) {
    echo '<p align="center" style="color: #FF0000;">Verificar Imagen - Tiene ' . $context['contando'] . ' denuncias</p>';
  }

  echo '
    <script type="text/javascript">
      function errorrojo2(causa) {
        if(causa == \'\') {
          document.getElementById(\'errors\').innerHTML = \'<font class="size10" style="color: red;">Es necesaria la causa de la eliminaci&oacute;n.</font>\';
          return false;
        }
      }

      function errorrojo(cuerpo_comment) {
        if(cuerpo_comment == \'\') {
          document.getElementById(\'error\').innerHTML=\'<br /><font class="size10" style="color: red;">No has escrito ning&uacute;n comentario.</font>\';
          return false;
        }
      }
    </script>
    <a name="inicio"></a>';

  // Diseño de la imagen
  echo '
    <div style="margin-bottom: 8px;">
      <div class="box_140" style="float:  left; margin-right: 8px;">
        <div class="box_title" style="width: 138px;">
          <div class="box_txt box_140-34">Publicado por:</div>';

  $request = db_query("
    SELECT *
    FROM {$db_prefix}members
    WHERE ID_MEMBER = " . $iduser, __FILE__, __LINE__);

  while ($row = mysqli_fetch_assoc($request)) {
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
    $context['firma'] = parse_bbc($row['signature']);
    $context['money'] = $row['money'];
    $context['ID_MEMBER'] = $row['ID_MEMBER'];
  }

  mysqli_free_result($request);

  $idgrup = $context['ID_POST_GROUP'];
  $idgrup2 = $context['ID_GROUP'];

  $request = db_query("
    SELECT *
    FROM {$db_prefix}membergroups
    WHERE ID_GROUP = " . $idgrup, __FILE__, __LINE__);

  while ($row2 = mysqli_fetch_assoc($request)) {
    $membergropu = $row2['groupName'];
  }

  mysqli_free_result($request);

  $request = db_query("
    SELECT *
    FROM {$db_prefix}membergroups
    WHERE ID_GROUP = " . $idgrup2, __FILE__, __LINE__);

  while ($row = mysqli_fetch_assoc($request)) {
    $membergropu2 = $row['groupName'];
  }

  $medalla = db_query("
    SELECT *
    FROM {$db_prefix}membergroups
    WHERE ID_GROUP = " . (!empty($idgrup2) ? $idgrup2 : $idgrup), __FILE__, __LINE__);

  while ($row = mysqli_fetch_assoc($medalla)) {
    $medalla = $row['stars'];
  }

  mysqli_free_result($request);

  $firma = str_replace('if(this.width >720) {this.width=720}','if(this.width > 375) {this.width=375}', $context['firma']);

  echo '
      <div class="box_rss">
        <div class="icon_img">
          <a href="' . $boardurl . '/rss/post-user/' . $context['memberName'] . '">
            <img alt="" src="' . $settings['images_url'] . '/icons/tpbig-v1-iconos.gif?v3.2.3" style="cursor: pointer; margin-top: -352px; display: inline;" />
          </a>
        </div>
      </div>
    </div>
    <div class="windowbg" style="width: 130px; padding: 4px;">
      <center>';

  if ($context['avatar']) {
    echo '
        <div class="fondoavatar" style="overflow: hidden; width: 130px;" align="center">
          <a href="' . $boardurl . '/perfil/' . $context['memberName'] . '" title="Ver Perfil">
            <img src="' . $context['avatar'] . '" width="105" alt="" class="avatar" border="0" onerror="error_avatar(this)" />
          </a>
          <br />
          <span class="mp">' . $context['personalText'] . '</span>
        </div>
      </center>
      <br />';
  } else {
    echo '
        <div class="fondoavatar" style="overflow: auto; width: 130px;" align="center">
          <a href="' . $boardurl . '/perfil/' . $context['memberName'] . '" title="Ver Perfil">
            <img src="' . $boardurl . '/avatar.gif" alt="Sin Avatar" border="0">
          </a>
          <br />
          <span class="mp">' . $context['personalText'] . '</span>
        </div>
      </center>
      <br />';
  }

  echo '
    <b>
      <a href="' . $boardurl . '/perfil/' . $context['memberName'] . '" style="font-size: 14px; color: #FF6600;">' . $context['realName'] . '</a>
    </b>
    <br />
    <b style="font-size: 12px; color: #747474; text-shadow: #6A5645 0px 1px 1px;">' . (!empty($membergropu2) ? $membergropu2 : $membergropu) . '</b>
    <br />
    <span title="' . (!empty($membergropu2) ? $membergropu2 : $membergropu) . '">
      <img  alt="" src="' . str_replace('1#rangos', $settings['images_url'] . '/rangos', $medalla) . '" />
    </span>';

  if (!empty($settings['show_gender']) && $context['gender']['image'] != '') {
    echo '
      &nbsp;
      <span title="' . ssi_sexo1($context['gender']) . '">' . ssi_sexo2(ssi_sexo3($context['gender'])) . '</span>';
  }

  if ($context['usertitle']) {
    echo ' <img alt="" title="' . ssi_pais($context['usertitle']) . '" src="' . $settings['images_url'] . '/icons/banderas/' . $context['usertitle'] . '.gif" />';
  } else {
    echo ' <img alt="" title="' . ssi_pais($context['usertitle']) . '" src="' . $settings['images_url'] . '/icons/banderas/ot.gif" />';
  }

  if (!empty($context['estado_icon'])) {
    echo ' <img title="Estado: ' . ssi_estado_icon($context['estado_icon']) . '" src="' . $settings['images_url'] . '/icons/estado/' . $context['estado_icon'] . '.gif" alt="" />';
  }

  echo '
    <br /><br />';

  if ($settings['show_profile_buttons']) {
    if ($context['user']['is_logged']) {
      echo '
        <div style="margin-bottom: 2px;">
          <span style="font-size: 12px;">
            <img alt="" src="' . $settings['images_url'] . '/icons/mensaje_para.gif" border="0" />
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
        </div>';
    }
  }

  echo '
    <br />
    <div class="hrs"></div>';

  $iduser = $context['ID_MEMBER'];

  $request = db_query("
    SELECT * FROM {$db_prefix}comments
    WHERE ID_MEMBER = " . $iduser, __FILE__, __LINE__);

  $context['comentuser'] = mysqli_num_rows($request);

  echo '
    <br />
    <div class="fondoavatar" style="overflow: hidden; width: 130px;">
      <b style="color: #FE8F47; text-shadow: #6A5645 0px 1px 1px;">PUNTOS:</b>
      &nbsp;
      <b>
        <span id="cant_pts_post">' . $context['money'] . '</span>
      </b>
      <br />
      <b style="color: #FE8F47; text-shadow: #6A5645 0px 1px 1px;">POST:</b>
      &nbsp;
      <b>
        <a href="' . $boardurl . '/user-post/' . $context['memberName'] . '">' . $context['topics'] . '</a>
      </b>
      <br />
      <b style="color: #FE8F47; text-shadow: #6A5645 0px 1px 1px;">COMENTARIOS:</b>
      &nbsp;
      <b>
        <a href="' . $boardurl . '/user-comment/' . $context['memberName'] . '">' . $context['comentuser'] . '</a>
      </b>
    </div>
    <br />';

  if ($context['user']['is_guest']) {
    echo '
      <div class="hrs"></div>
      <div class="size11">
        <br />
        <a href="' . $boardurl . '/registrarse/" target="_blank" rel="nofollow">&iexcl;REG&Iacute;STRATE!</a> es <b>GRATIS</b>
      </div>';
  }

  echo '
        <span class="size11"></span>
      </div>
    </div>
    <div class="box_780" style="float: left;">
      <div class="box_title" style="width: 772px;">
        <div class="box_txt box_780-34">
          <center>' . $context['gallery_pic']['title'] . '</center>
        </div>
        <div class="box_rss">
          <div class="icon_img">
            <a href="' . $boardurl . '/imprimir/imagen/' . $context['gallery_pic']['ID_PICTURE'] . '">
              <img alt="" src="' . $settings['images_url'] . '/icons/tpbig-v1-iconos.gif?v3.2.3" style="cursor: pointer; margin-top: -640px; display: inline;" />
            </a>
          </div>
        </div>
      </div>
      <div class="windowbg" style="width: 772px;" id="img_' . $context['gallery_pic']['ID_PICTURE'] . '">
        <div class="post-contenido" property="dc:content">';

  if ($context['user']['is_guest']) {
    echo '
      <div align="center" style="-moz-border-radius: 5px; -webkit-border-radius: 5px; display: block; margin-bottom: 25px; margin-top: 10px; padding: 2px; border: solid 1px #D5CCC3; background: #FFF;">
        ' . $modSettings['horizontal'] . '
        <br />
        <a href="' . $boardurl . '/registrarse/" style="font-size: 12px; color: #FFB600; margin-bottom: 3px;">
          <b>REG&Iacute;STRATE GRATIS Y ELIMINA ESTA PUBLICIDAD, ADEM&Aacute;S TENDR&Aacute;S ACCESO A TODOS LOS POSTS Y FUNCIONES</b>
        </a>
      </div>';
  }

  echo '
    <center>
      <img onload="if(this.width > 750) {this.width=750}" alt="" title="' . $context['gallery_pic']['title'] . '" src="' . $context['gallery_pic']['filename']  . '" />
    </center>';

  if ($context['user']['is_guest']) {
    echo '
      <div align="center" style="-moz-border-radius: 5px; -webkit-border-radius: 5px; display: block; margin-bottom: 10px; margin-top: 25px; padding: 2px; border: solid 1px #D5CCC3; background: #FFF;">
        ' . $modSettings['horizontal'] . '
        <br />
        <a href="' . $boardurl . '/registrarse/" style="font-size: 12px; color: #FFB600; margin-bottom: 3px;">
          <b>REG&Iacute;STRATE GRATIS Y ELIMINA ESTA PUBLICIDAD, ADEM&Aacute;S TENDR&Aacute;S ACCESO A TODOS LOS POSTS Y FUNCIONES</b>
        </a>
        <br />
      </div>';
  }

  echo '
      </div>
    </div>
    <!-- info de la imagen -->
    <div style="width: 780px; margin-top: 8px;">
      <div style="width: 380px; float: left; margin-right: 8px ; #margin-right: 8px; _margin-right:6px;">
        <div class="box_390" style="width: 380px;">
          <div class="box_title" style="width: 378px;">
            <div class="box_txt box_390-34">Opciones</div>
            <div class="box_rss">
              <span id="cargando_opciones" style="display: none;">
                <img alt="" src="' . $settings['images_url'] . '/icons/cargando.gif" style="width: 16px; height: 16px;" border="0" />
              </span>
              <span id="cargando_opciones2">
                <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 16px; height: 16px;" border="0" />
              </span>
            </div>
          </div>
          <div class="windowbg size11" style="width: 370px; padding: 4px;">';

  if ($context['user']['is_logged']) {
    echo '<form action="' . $boardurl . '/eliminar-imagen/' . $context['gallery_pic']['ID_PICTURE'] . '/" method="post" accept-charset="' . $context['character_set'] . '" style="margin: 0px; padding: 0px;" name="causa" id="causa">';
  }

  if ($context['allow_admin']) {
    echo '
      <input class="login" style="font-size: 11px;" value="Editar img" title="Editar img" onclick="location.href=\'' . $boardurl . '/editar-imagen/id-' . $context['gallery_pic']['ID_PICTURE']  . '\'; return errorrojo2(this.form.causa.value);" type="button" />
      &nbsp;
      <input class="login" style="font-size: 11px;" value="Eliminar img" title="Eliminar img" onclick="if (!confirm(\'\xbfEstas seguro que desea eliminar esta imagen?\')) return false; return errorrojo2(this.form.causa.value);" type="submit" />
      &nbsp;
      <input type="text" id="causa" name="causa" maxlength="50" size="30" />
      <center>
        <label id="errors"></label>
      </center>
      <div class="hrs"></div>';
  } else if ($iduser==$context['user']['id'] || $context['allow_admin']) {
    echo '
      <input class="login" style="font-size: 11px;" value="Editar img" title="Editar img" onclick="location.href=\'' . $boardurl . '/editar-imagen/id-' . $context['gallery_pic']['ID_PICTURE']  . '\'" type="button" />
      &nbsp;
      <input class="login" style="font-size: 11px;" value="Eliminar img" title="Eliminar img" onclick="if (!confirm(\'\xbfEstas seguro que desea eliminar esta imagen?\')) return false; location.href=\'' . $boardurl . '/eliminar-img/' . $context['current_topic'] . '/\'" type="button" />
      <div class="hrs"></div>';
  }

  if ($iduser == $context['user']['id']) {
    echo '</form>';
  }

  if ($context['Conocido'] || $context['Vecino'] || $context['Amigo'] || $context['Familiar'] || $context['Casero'] || $context['allow_admin']) {
    echo '
      <span id="span_opciones1" class="size10">
        <b class="size11">Dar puntos:</b>';

    $puntos = $context['user']['money'];

    for ($o = 1; $o <= $puntos; $o++) {
      echo '<a href="#" onclick="votar_img(\'' . $context['gallery_pic']['ID_PICTURE'] . '\', \'' . $o . '\'); return false;" title="Dar ' . $o . ' puntos">' . $o . '</a>';
      
      $contarpuntos++;

      if ($contarpuntos < $puntos) {
        echo '&nbsp;-&nbsp;';
      }
    }

    echo '
        &nbsp;
        <i>
          <strong>de ' . $context['user']['money'] . ' disp.</strong>
        </i>
      </span>';
  } else {
    echo '<b class="size11"><center>Usuarios no registrados y <span title="Primer rango">turistas</span> no puede calificar.</center></b>';
  }

  echo '<div class="hrs"></div>';

  if ($context['user']['is_logged']) {
    echo '
      <center>
        <span id="span_opciones2" style="text-align: center; display: block;">
          <a class="icons agregar_favoritos" href="#" onclick="add_favoritos_img(\'' . $context['gallery_pic']['ID_PICTURE'] . '\'); return false;">Agregar a Favoritos</a>
          &nbsp;|&nbsp;
          <a class="icons denunciar_post" title="Denunciar Imagen" href="' . $boardurl . '/denuncia/imagen-' . $context['gallery_pic']['ID_PICTURE'] . '">Denunciar Imagen</a>
          &nbsp;|&nbsp;
          <a class="icons recomendar_post" href="' . $boardurl . '/enviar-a-amigo/imagen-' . $context['gallery_pic']['ID_PICTURE'] . '">Enviar a un amigo</a>
        </span>
      </center>';
  } else {
    echo '<a class="icons recomendar_post" href="' . $boardurl . '/enviar-a-amigo/imagen-' . $context['gallery_pic']['ID_PICTURE'] . '">Enviar a un amigo</a>';
  }

  echo '
    <div class="hrs"></div>
    <b class="size13">Otras im&aacute;genes:</b>
    <br />';

  foreach($context['al-azar'] as $alzar) {
    echo '
      <div class="entry_item">
        <div class="icon">
          <img alt="" title="' . $alzar['title'] . '" src="' . $settings['images_url'] . '/icons/foto.gif" />
        </div>
        <div class="text_container">
          <a rel="dc:relation" href="' . $boardurl . '/imagenes/ver/' . $alzar['ID_PICTURE'] . '" title="' . $alzar['title'] . '" target="_self" >' . $alzar['title'] . '</a>
        </div>
      </div>';
  }

  $link = $boardurl . $_SERVER['REQUEST_URI'];

  $request = db_query("
    SELECT o.ID_TOPIC
    FROM ({$db_prefix}bookmarks AS o)
    WHERE o.ID_TOPIC = {$context['gallery_pic']['ID_PICTURE']}
    AND o.TYPE = 'imagen'", __FILE__, __LINE__);

  $context['fav1'] = mysqli_num_rows($request);

  echo '
            <div style="clear: left;"></div>
          </div>
        </div>
      </div>
      <div style="width: 386px; float: left;">
        <div class="box_390">
          <div class="box_title" style="width: 384px;">
            <div class="box_txt box_390-34">Informaci&oacute;n de la imagen</div>
            <div class="box_rss">
              <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 16px; height: 16px;" border="0" />
            </div>
          </div>
          <div class="windowbg size11" style="width: 376px; padding: 4px;">
            <center>
              <span class="icons visitas">
                &nbsp;
                ' . $context['gallery_pic']['views'] . '
                &nbsp;
                visitas
              </span>
              <span class="icons fav">
                <span id="cant_favs_post">' . $context['fav1'] . '</span>
                &nbsp;
                favoritos
              </span>
              <span class="icons puntos">
                <span id="cant_pts_post_dos">' . $context['gallery_pic']['points'] . '</span>
                &nbsp;
                puntos
              </span>
            </center>
            <div class="hrs"></div>
            <b>Creado el:</b>
            &nbsp;
            <span property="dc:date" content="' . $fecha . '">' . $fecha . '</span>
            <div class="hrs"></div>
            <div style="float: left; margin-right: 4px;">
              <b>Agregar a:</b>
            </div>
        <div class="icon_img" style="float:left; margin-right: 4px;">
          <a href="http://technorati.com/faves/?add=' . $link . '" rel="nofollow" target="_blank" title="Agregar a: Technorati">
            <img alt="" src="' . $settings['images_url'] . '/icons/tpbig-v1-iconos.gif?v3.2.3" style="cursor: pointer; margin-top: -391px; display: inline;" />
          </a>
        </div>
        <div class="icon_img" style="float: left; margin-right: 4px;">
          <a href="http://www.facebook.com/share.php?u=' . $link . '" rel="nofollow" target="_blank" title="Agregar a: Facebook">
            <img alt="" src="' . $settings['images_url'] . '/icons/tpbig-v1-iconos.gif?v3.2.3" style="cursor: pointer; margin-top: -411px; display: inline;" />
          </a>
        </div>
        <div class="icon_img" style="float: left; margin-right: 4px;">
          <a href="http://twitter.com/home?status=Les recomiendo que vean esta imagen: ' . $link . '" rel="nofollow" target="_blank" title="Agregar a: Twitter">
            <img alt="" src="' . $settings['images_url'] . '/icons/tpbig-v1-iconos.gif?v3.2.3" style="cursor: pointer; margin-top: -472px; display: inline;" />
          </a>
        </div>
        <div class="icon_img" style="float: left; margin-right: 4px;">
          <a href="http://del.icio.us/post?url=' . $link . '" rel="nofollow" target="_blank" title="Agregar a: Del.icio">
            <img alt="" src="' . $settings['images_url'] . '/icons/tpbig-v1-iconos.gif?v3.2.3" style="cursor: pointer; margin-top: -432px; display: inline;" />
          </a>
        </div>
        <div class="icon_img">
          <a href="http://digg.com/submit?phase=2&#38;url=' . $link . '" rel="nofollow" target="_blank" title="Agregar a: Digg">
            <img alt="" src="' . $settings['images_url'] . '/icons/tpbig-v1-iconos.gif?v3.2.3" style="cursor: pointer; margin-top: -453px; display: inline;" />
          </a>
        </div>
        <hr />
        <table>
          <tr>
            <td width="50px">
              <b>Enlace:</b>
            </td>
            <td width="290px">
              <input id="enlace" name="enlace" type="text" onfocus="foco(this);" onblur="no_foco(this);" value="' . $context['gallery_pic']['filename']  . '" onclick="selectycopy(getElementById(\'enlace\')); APITrack(\'copy_details_url\');" style="width: 290px;" />
            </td>
          </tr>
          <tr>
            <td width="50px">
              <b>Embed:</b>
            </td>
            <td width="290px">
              <input id="embed" name="embed" type="text" onfocus="foco(this);" onblur="no_foco(this);" value="&lt;a title=&quot;' . $context['gallery_pic']['title']  . ' - ' . $boardurl . '&quot; href=&quot;' . $link . '&quot; target=&quot;_blank&quot;&gt;' . $context['gallery_pic']['title']  . ' - ' . $boardurl . '&lt;/a&gt;" onclick="selectycopy(getElementById(\'embed\')); APITrack(\'copy_details_url\');" style="width: 290px;" />
            </td>
          </tr>
          <tr>
            <td width="50px">
              <b>BBCode:</b>
            </td>
            <td width="290px">
              <input id="bbcode" name="bbcode" type="text" onfocus="foco(this);" onblur="no_foco(this);" value="[IMG]' . $context['gallery_pic']['filename']  . '[/IMG]" onclick="selectycopy(getElementById(\'bbcode\')); APITrack(\'copy_details_url\');" style="width: 290px;">
            </td>
          </tr>
        </table>
      </div>
    </div>';

  if (!empty($context['firma']) && empty($options['show_no_signatures'])) {
    echo '
      <div class="box_390" style="margin-top: 8px;">
        <div class="box_title" style="width: 384px;">
          <div class="box_txt box_390-34">Firma</div>
          <div class="box_rss">
            <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 16px; height: 16px;" border="0" />
          </div>
        </div>
        <div class="windowbg" style="width: 376px; padding: 4px;">' . censorText($firma) . '</div>
      </div>';
  }

  echo '
      </div>
    </div>
    <!-- fin info del post -->

    <!-- comentarios -->
    <div style="margin-bottom: 8px;">
      <div class="box_780" style="float: left; margin-bottom: 8px; margin-top: 8px;">';

  if ($context['allow_admin'] || $iduser == $context['user']['id']) {
    echo '<form action="' . $boardurl . '/comentario-img/eliminar/" method="post" accept-charset="' . $context['character_set'] . '" id="eliminar-comments">';
  }

  echo '
    <div class="box_title" style="width: 772px;">
      <div class="box_txt box_780-34">Comentarios</div>
      <div class="box_rss">
        <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 16px; height: 16px;" border="0" />
      </div>
    </div>
    <div class="windowbg" style="width: 764px; padding: 4px;">';

  $request = db_query("
    SELECT c.ID_PICTURE, c.ID_COMMENT, c.date, c.comment, c.ID_MEMBER, m.posts, m.memberName, m.realName
    FROM {$db_prefix}gallery_comment as c, {$db_prefix}members AS m
    WHERE c.ID_PICTURE = " . $context['gallery_pic']['ID_PICTURE'] . "
    AND c.ID_MEMBER = m.ID_MEMBER
    ORDER BY c.ID_COMMENT ASC", __FILE__, __LINE__);

  $context['sin_coment'] = mysqli_num_rows($request);
  $context['pic_comment'] = array();

  while ($row = mysqli_fetch_assoc($request)) {
    $context['pic_comment'][] = array(
      'ID_COMMENT' => $row['ID_COMMENT'],
      'memberName' => censorText($row['memberName']),
      'realName' => censorText($row['realName']),
      'ID_MEMBER' => $row['ID_MEMBER'],
      'comment' => parse_bbc($row['comment']),
      'comment2' => censorText($row['comment']),
      'date' => $row['date'],
      );

    $context['ID_PICTURE'] = $row['ID_PICTURE'];
  }

  mysqli_free_result($request);

  $cantidad++;
  $memCommID = $pic_comment['ID_MEMBER'];

  loadMemberData($memCommID);
  loadMemberContext($memCommID);

  if ($context['sin_coment']) {
    foreach ($context['pic_comment'] as $pic_comment) {
      echo '
        <div id="cmt_' . $pic_comment['ID_COMMENT'] . '">
          <span class="size12">';

      if ($context['allow_admin'] || $iduser == $context['user']['id']) {
        echo '<input type="checkbox" name="campos[' . $pic_comment['ID_COMMENT'] . ']" />';
      }

      echo '
        &nbsp;
        <a onclick="citar_comment(' . $pic_comment['ID_COMMENT'] . ')" href="javascript:void(0)">#' . $cantidad++ . '</a>
        &nbsp;
        <b id="autor_cmnt_' . $pic_comment['ID_COMMENT'] . '" user_comment="' . $pic_comment['memberName'] . '" text_comment="' . $pic_comment['comment2'] . '">
          <a href="' . $boardurl . '/perfil/' . $pic_comment['memberName'] . '">' . $pic_comment['realName'] . '</a>
        </b>
        &nbsp;|&nbsp;
        <span class="size10">' . date("d.n.y H:i:s", $pic_comment['date']) . '</span>';

      if ($context['user']['is_logged']) {
        echo '
          &nbsp;
          <a href="' . $boardurl . '/mensajes/a/' . $pic_comment['memberName'] . '" title="Enviar MP a: ' . $pic_comment['realName'] . '">
            <img alt="" src="' . $settings['images_url'] . '/icons/mensaje_para.gif" style="margin-top: 2px; margin-right: 2px;" align="top" border="0" />
          </a>
          <a class="icons citar" onclick="citar_comment(' . $pic_comment['ID_COMMENT'] . ')" href="javascript:void(0)" title="Citar Comentario">
            <img alt="" src="' . $settings['images_url'] . '/espacio.gif" align="top" border="0" />
          </a>';
      }

      echo '
            dijo:
            <br />
            <div style="overflow: hidden;">' . $pic_comment['comment'] . '</div>
          </span>
        </div>
        <div class="hrs"></div>';
    }
  } else {
    echo '<div id="no_comentarios" class="noesta">Esta imagen no tiene comentarios.-</div>';
  }

  echo '
      <div id="return_agregar_comentario" style="display: none;"></div>
    </div>';

  if ($context['sin_coment']) {
    if($context['allow_admin'] || $iduser == $context['user']['id']) {
      echo '
        <span class="size10">Comentarios Seleccionados:</span>
        &nbsp;
        <input class="login" style="font-size: 9px;" type="submit" value="Eliminar" />';
    }
  }

  echo '
          <input value="' . $context['id_img'] . '" name="idimg" id="idimg" type="hidden" />
        </form>
      </div>
    </div>
    <!-- fin comentarios -->';

  if (!empty($options['display_quick_reply']) && $context['user']['is_logged']) {
    echo '
      <!-- comentar -->
      <a name="comentar"></a>
      <div style="margin-bottom: 8px;" class="agregar_comentario">
        <div class="box_780" style="float: left; margin-bottom: 8px;">
          <div class="box_title" style="width: 772px;">
            <div class="box_txt box_780-34">Agregar un nuevo comentario</div>
            <div class="box_rss">
              <span id="gif_cargando_add_comment" style="display: none;">
                <img alt="" src="' . $settings['images_url'] . '/icons/cargando.gif" style="width: 16px; height: 16px;" border="0" />
              </span>
              <span id="gif_cargando_add_comment2">
                <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 16px; height: 16px;" border="0" />
              </span>
            </div>
          </div>
          <div class="windowbg" style="width: 764px; padding: 4px; font-size: 11px;">
            <form name="nuevocoment">
              <table>
                <tr>
                  <td width="400px" valign="top">
                    <div style="width: 400px;">
                      <center>
                        <div class="msg_add_comment"></div>
                      </center>
                      <textarea onfocus="foco(this);" onblur="no_foco(this);" style="height: 90px; width: 390px;" id="cuerpo_comment" name="cuerpo_comment" class="markItUpEditor" tabindex="1"></textarea>';
    loadSmileys();
    printSmileys();

    echo '
                    </div>
                  </td>
                  <td valign="top">
                    <img src="' . $settings['images_url'] . '/icons/bullet-verde.gif" alt=""><b>&nbsp;Se eliminan los comentarios:</b><br />
                    <img src="' . $settings['images_url'] . '/icons/bullet-rojo.gif" alt="" />&nbsp;Que contenga tipograf&iacute;as muy grandes, abuso de may&uacute;sculas o con el claro efecto de llamar la atenci&oacute;n.<br />
                    <img src="' . $settings['images_url'] . '/icons/bullet-rojo.gif" alt="" />&nbsp;Que el usuario haya comentado primero su imagen.<br />
                    <img src="' . $settings['images_url'] . '/icons/bullet-rojo.gif" alt="" />&nbsp;Que contengan insultos, ofensas, etc. (hacia otro usuario o de forma general).<br />
                    <img src="' . $settings['images_url'] . '/icons/bullet-rojo.gif" alt="" />&nbsp;Que sea un comentario racistas.<br />
                    <img src="' . $settings['images_url'] . '/icons/bullet-rojo.gif" alt="" />&nbsp;Que contenga SPAM.
                    <p style="padding: 0px; margin: 0px;" align="right">[<a href="' . $boardurl . '/protocolo/" title="Protocolo completo">Protocolo completo</a>]</p>
                    <br />
                    <input class="login" type="button" id="button_add_comment" value="Enviar Comentario" onclick="add_comment_img(\'' . $context['gallery_pic']['ID_PICTURE'] . '\'); return false;" tabindex="2" />
                  </td>
                </tr>
                </table>
                <div style="clear: left;"></div>
              </form>
            </div>
          </div>
        </div>
        <!-- fin comentar -->
      </div>';
  }

  if ($context['user']['is_guest']) {
    echo '
      <div style="clear: left;"></div>
      <div class="noesta-am" style="width:774px;">
        Para poder comentar necesitas estar <a href="' . $boardurl . '/registrarse/" style="color: #FFB600;" title="Reg&iacute;strarse">Registrado</a>. Si ya tienes usuario <a href="' . $boardurl . '/ingresar/" style="color: #FFB600;" title="Conectarse">&iexcl;Con&eacute;ctate!</a>
      </div>';
  }

  echo '
      </div>
    </div>
    <div style="clear:both"></div>';
}

function loadSmileys() {

  global $context, $settings, $user_info, $modSettings, $db_prefix;

  $context['smileys'] = array(
    'postform' => array(),
    'popup' => array(),
  );

  if (empty($modSettings['smiley_enable']) && $user_info['smiley_set'] != 'none') {
    $context['smileys']['postform'][] = array(
      'smileys' => array(),
      'last' => true,
    );
  } else if ($user_info['smiley_set'] != 'none') {
    if (($temp = cache_get_data('posting_smileys', 480)) == null) {
      $request = db_query("
        SELECT code, filename, description, smileyRow, hidden
        FROM {$db_prefix}smileys
        WHERE hidden IN (0, 2)
        ORDER BY smileyRow, smileyOrder", __FILE__, __LINE__);

      while ($row = mysqli_fetch_assoc($request)) {
        $row['code'] = htmlspecialchars($row['code']);
        $row['filename'] = htmlspecialchars($row['filename']);
        $row['description'] = htmlspecialchars($row['description']);
        $context['smileys'][empty($row['hidden']) ? 'postform' : 'popup'][$row['smileyRow']]['smileys'][] = $row;
      }

      mysqli_free_result($request);
      cache_put_data('posting_smileys', $context['smileys'], 480);
    }
    else
      $context['smileys'] = $temp;
  }

  foreach (array_keys($context['smileys']) as $location) {
    foreach ($context['smileys'][$location] as $j => $row) {
      $n = count($context['smileys'][$location][$j]['smileys']);

      for ($i = 0; $i < $n; $i++) {
        $context['smileys'][$location][$j]['smileys'][$i]['code'] = addslashes($context['smileys'][$location][$j]['smileys'][$i]['code']);
        $context['smileys'][$location][$j]['smileys'][$i]['js_description'] = addslashes($context['smileys'][$location][$j]['smileys'][$i]['description']);
      }

      $context['smileys'][$location][$j]['smileys'][$n - 1]['last'] = true;
    }

    if (!empty($context['smileys'][$location]))
      $context['smileys'][$location][count($context['smileys'][$location]) - 1]['last'] = true;
  }

  $settings['smileys_url'] = $modSettings['smileys_url'] . '/' . $user_info['smiley_set'];
}

function printSmileys() {
  global $context, $txt, $settings, $boardurl;

  loadLanguage('Post');

  if (!empty($context['smileys']['postform'])) {
    foreach ($context['smileys']['postform'] as $smiley_row) {
      foreach ($smiley_row['smileys'] as $smiley)
        echo '
        <a href="javascript:void(0);" onclick="replaceText(\' ' . $smiley['code'] . '\', document.forms.nuevocoment.cuerpo_comment); return false;">
          <img src="' . $settings['smileys_url'] . '/' . $smiley['filename'] . '" align="bottom" alt="' . $smiley['description'] . '" title="' . $smiley['description'] . '" />
        </a>
        &nbsp;';
    }

    if (!empty($context['smileys']['popup'])) {
      echo '
        <script type="text/javascript">
          function openpopup() {
            var winpops = window.open("' . $boardurl . '/emoticones/", "", "width=255px,height=500px,scrollbars");
          }
        </script>
        <a href="javascript:openpopup()">[' . $txt['more_smileys'] . ']</a>';
    }
  }
}

function template_delete_picture() { 
  global $scripturl, $modSettings, $txt, $context;

  echo '
    <form method="POST" action="' . $scripturl . '?action=gallery&sa=delete2" accept-charset="' . $context['character_set'] . '">
      <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr class="catbg">
          <td width="50%" colspan="2" align="center">
            <b>' . $txt['gallery_form_delpicture'] . '</b>
          </td>
        </tr>
        <tr class="windowbg2">
          <td width="28%" colspan="2" align="center" class="windowbg2">
            ' . $txt['gallery_warn_deletepicture'] . '
            <br />
            <div align="center">
              <br />
              <b>' . $txt['gallery_form_delpicture'] . '</b>
              <br />
              <a href="' . $scripturl . '?action=gallery;sa=view;id=' . $context['gallery_pic']['ID_PICTURE'] . '" target="blank">
                <img src="' . $modSettings['gallery_url'] . $context['gallery_pic']['thumbfilename'] . '" border="0" />
              </a>
              <br />
              <span class="smalltext">
                Views:&nbsp;' . $context['gallery_pic']['views'] . '<br />
                ' . $txt['gallery_text_filesize']  . $context['gallery_pic']['filesize'] . 'kb<br />
                ' . $txt['gallery_text_date'] . $context['gallery_pic']['date'] . '<br />
                ' . $txt['gallery_text_comments'] . '
                &nbsp;
                (<a href="' . $scripturl . '?action=gallery;sa=view;id=' .  $context['gallery_pic']['ID_PICTURE'] . '" target="blank">' .  $context['gallery_pic']['commenttotal'] . '</a>)
                <br />
              </span>
            </div>
            <br />
            <input type="hidden" name="id" value="' . $context['gallery_pic']['ID_PICTURE'] . '" />
            <input type="submit" value="' . $txt['gallery_form_delpicture'] . '" name="submit" />
            <br />
          </td>
        </tr>
      </table>
    </form>';

  GalleryCopyright();
}

function template_add_comment() {}
function template_report_picture() {}

function template_settings() {
  global $scripturl, $modSettings, $txt, $context;

  echo '
    <table border="0" width="80%" cellspacing="0" align="center" cellpadding="4" class="tborder">
      <tr class="titlebg">
        <td>' . $txt['gallery_text_settings'] . '</td>
      </tr>
      <tr class="windowbg">
        <td>
          <b>' . $txt['gallery_text_settings'] . '</b>
          &nbsp;-&nbsp;
          <span class="smalltext">' . $txt['gallery_set_description'] . '</span>
          <br />
          <form method="POST" action="' . $scripturl . '?action=gallery;sa=adminset2" accept-charset="' . $context['character_set'] . '">
            <table>
              <tr>
                <td>' . $txt['gallery_set_maxheight'] . '</td>
                <td>
                  <input type="text" name="gallery_max_height" value="' .  $modSettings['gallery_max_height'] . '" />
                </td>
              </tr>
              <tr>
                <td>' . $txt['gallery_set_maxwidth'] . '</td>
                <td>
                  <input type="text" name="gallery_max_width" value="' .  $modSettings['gallery_max_width'] . '" />
                </td>
              </tr>
              <tr>
                <td>' . $txt['gallery_set_filesize'] . '</td>
                <td>
                  <input type="text" name="gallery_max_filesize" value="' .  $modSettings['gallery_max_filesize'] . '" /> (bytes)
                </td>
              </tr>
              <tr>
                <td>' . $txt['gallery_set_path'] . '</td>
                <td>
                  <input type="text" name="gallery_path" value="' .  $modSettings['gallery_path'] . '" size="50" />
                </td>
              </tr>
              <tr>
                <td>' . $txt['gallery_set_url'] . '</td>
                <td>
                  <input type="text" name="gallery_url" value="' .  $modSettings['gallery_url'] . '" size="50" />
                </td>
              </tr>
            </table>
            <input type="checkbox" name="gallery_who_viewing" ' . ($modSettings['gallery_who_viewing'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_whoonline'] . '<br />';

          if (!is_writable($modSettings['gallery_path'])) {
            echo '<font color="#FF0000"><b>' . $txt['gallery_write_error'] . $modSettings['gallery_path'] . '</b></font>';
          }

          echo '
            <input type="checkbox" name="gallery_commentchoice" ' . (!empty($modSettings['gallery_commentchoice']) ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_commentschoice'] . '<br />
            <br />' . $txt['gallery_shop_settings'] . '<br />
            ' . $txt['gallery_shop_picadd'] . '<input type="text" name="gallery_shop_picadd" value="' .  $modSettings['gallery_shop_picadd'] . '" /><br />
            ' . $txt['gallery_shop_commentadd'] . '<input type="text" name="gallery_shop_commentadd" value="' .  $modSettings['gallery_shop_commentadd'] . '" /><br />
            <br />
            <b>' . $txt['gallery_txt_image_linking'] . '</b><br />
            <input type="checkbox" name="gallery_set_showcode_bbc_image" ' . ($modSettings['gallery_set_showcode_bbc_image'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_showcode_bbc_image'] . '<br />
            <input type="checkbox" name="gallery_set_showcode_directlink" ' . ($modSettings['gallery_set_showcode_directlink'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_showcode_directlink'] . '<br />
            <input type="checkbox" name="gallery_set_showcode_htmllink" ' . ($modSettings['gallery_set_showcode_htmllink'] ? ' checked="checked" ' : '') . ' />' . $txt['gallery_set_showcode_htmllink'] . '<br />
            <br />
            <input type="submit" name="savesettings" value="' . $txt['gallery_save_settings'] . '" />
          </form>
          <br />
          <b>' . $txt['gallery_text_permissions'] . '</b>
          <br />
          <span class="smalltext">' . $txt['gallery_set_permissionnotice'] . '</span>
          <br />
          <a href="' . $scripturl . '?action=permissions">' . $txt['gallery_set_editpermissions']  . '</a>
        </td>
      </tr>
      <tr class="windowbg">
        <td>
          <b>Has SMF Gallery helped you?</b>
          Then support the developers:<br />
          <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
            <input type="hidden" name="cmd" value="_xclick">
            <input type="hidden" name="business" value="sales@visualbasiczone.com">
            <input type="hidden" name="item_name" value="SMF Gallery">
            <input type="hidden" name="no_shipping" value="1">
            <input type="hidden" name="no_note" value="1">
            <input type="hidden" name="currency_code" value="USD">
            <input type="hidden" name="tax" value="0">
            <input type="hidden" name="bn" value="PP-DonationsBF">
            <input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-butcc-donate.gif" border="0" name="submit" alt="Make payments with PayPal - it is fast, free and secure!" />
            <img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" />
          </form>
          <br />
          You can also upgrade to the SMF Gallery Pro edition at <a href="http://www.smfhacks.com/smf-gallery-pro.php" target="blank">http://www.smfhacks.com/smf-gallery-pro.php</a>
          <br />
          <table>
            <tr>
              <td>
                <a href="http://www.adbrite.com/mb/?spid=11444&afb=120x60-1-blue">
                <img src="http://files.adbrite.com/mb/images/120x60-1-blue.gif" border="0">
              </td>
              <td>
                <a href="http://www.kqzyfj.com/click-3289266-10408495" target="_top">
                  <img src="http://www.tqlkg.com/image-3289266-10408495" width="120" height="60" alt="" border="0"/>
                </a>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>';

  GalleryCopyright();
}

function template_reportlist() {}

function template_myimages() {}

function template_approvelist() {}

function template_search() {}

function template_search_results() {}

function GalleryCopyright() {}

function template_manage_cats() {}

function template_add_category() {}

function template_edit_category() {}

function template_delete_category() {}

?>