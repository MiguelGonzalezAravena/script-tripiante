<?php
// Version: 1.1.5; Post

// The main template for the post page.
function template_main() {
  global $context, $settings, $scripturl, $boardurl;

  ssi_grupos();

  echo '
    <script type="text/javascript">
      function scrollUp() {
        var cs = (document.documentElement && document.documentElement.scrollTop)? document.documentElement : document.body;
        var step = Math.ceil(cs.scrollTop / 10);
        scrollBy(0, (step-(step*2)));
        if (cs.scrollTop>0)
          setTimeout(\'scrollUp()\', 40);
      }

      function cerrar_vprevia() {
        $(\'#preview\').fadeOut("slow");
      }

      function vprevia(subject, message, tags, f) {
        if (subject == \'\') {
          document.getElementById(\'error1\').innerHTML = \'<font class="size10" style="color: red;"><br />Es necesario que pongas el titulo del post.</font>\';
          return false;
        }

        if (message == \'\') {
          document.getElementById(\'error2\').innerHTML = \'<font class="size10" style="color: red;"><br />Es necesario que escribas el post.</font>\';
          return false;
        }

        if (message.length > 63206) {
          document.getElementById(\'error3\').innerHTML = \'<font class="size10" style="color: red;"><br />El contenido del post no se puede pasar de los 63206 caracteres.</font>\';
          return false;
        }

        if (tags == \'\') {
          document.getElementById(\'tagserror\').innerHTML = \'<br />Es necesario que pongas los tags del post.\';
          return false;
        }

        if (f.categorias.options.selectedIndex == -1 || f.categorias.options[f.categorias.options.selectedIndex].value == -1) {
          document.getElementById(\'error5\').innerHTML = \'<font class="size10" style="color: red;"><br />Debes agregar la categr&iacute;a del post.</font>\';
          return false;
        }

        var separar_tags = tags.split(",");
        if (separar_tags.length -1 >= 2) {
        for (x in separar_tags) {
          if (separar_tags[x].length <3) {
            alert("El Tag \"" + separar_tags[x] + "\" tiene menos de 3 caracteres");
            return;
          } else {
            for (y in separar_tags) {
              if (separar_tags[x]==separar_tags[y] & x != y) {
                alert("El Tag \"" + separar_tags[x] + "\" se encuentra repetido");
                return;
              }
            }
          }
        }
      }

      if (separar_tags.length < 4) {
        document.getElementById(\'tagserrordos\').innerHTML = \'<br />Es necesario que ingreses por lo menos 4 tags separados por coma.\';
        return false;
      }

      var params = \'subject=\' + encodeURIComponent(subject) + \'&message=\' + encodeURIComponent(message) + \'&accion=\' + encodeURIComponent(2);

      $.ajax({
        type: "POST",
        url: \'/vista-previa/\',
        data: params,
        success: function(h) {
          scrollUp();
          $(\'#preview\').html(h);
          $(\'#preview\').css(\'display\', \'inline\');
        }
      });
    </script>';

  echo '
    <form action="' . $scripturl . '?action=' . $context['destination'] . ';board=1" method="post" accept-charset="' . $context['character_set'] . '" name="postmodify" id="postmodify" enctype="multipart/form-data" style="margin: 0;">';

  echo '
    <div id="preview" style="display: none; width: 922px;"></div>
    <div style="margin-bottom: 8px;">
      <div class="box_235" style="float: left; margin-right: 8px;">
        <div class="box_title" style="width: 233px;">
          <div class="box_txt box_235-34"><center>&#161;Aclaraci&oacute;n!</center></div>
          <div class="box_rss">
            <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 16px; height: 16px;" border="0" />
          </div>
        </div>
        <div class="windowbg" border="0" style="width: 225px; padding: 4px; font-family: arial;">
          <center class="size12">
            En esta secci&oacute;n puedes agregar una publicacion para compartirla con nuestra comunidad.
            <hr class="divider" />
            Para que esta publicaci&oacute;n no sea borrada por el staff de la web, debe estar de acuerdo con
            <a href="' . $boardurl . '/protocolo/" target="_blank" title="Protocolo"><b>las normas</b></a> establecidas en la web.
            <hr class="divider" />
            Tambi&eacute;n debe tener en cuenta los siguientes puntos:
          </center>
          <br />
          <img src="' . $settings['images_url'] . '/icons/bullet-verde.gif" alt="" />
          Contenido descriptivo.
          <br />
          <img src="' . $settings['images_url'] . '/icons/bullet-verde.gif" alt="" />
          T&iacute;tulo descriptivo.
          <br />
          <img src="' . $settings['images_url'] . '/icons/bullet-verde.gif" alt="" />
          Agregar im&aacute;genes sobre el post.
          <br />
          <img src="' . $settings['images_url'] . '/icons/bullet-verde.gif" alt="" />
          Noticias con fuente.
          <br />
          <img src="' . $settings['images_url'] . '/icons/bullet-verde.gif" alt="" />
          No excederse en mayusculas.
          <br />
          <img src="' . $settings['images_url'] . '/icons/bullet-verde.gif" alt="" />
          No t&iacute;tulo llamativo.
          <br />
          <img src="' . $settings['images_url'] . '/icons/bullet-verde.gif" atl="" />
          No spam.
          <br />
          <img src="' . $settings['images_url'] . '/icons/bullet-verde.gif" atl="" />
          No gore o asqueros.
          <br />
          <img src="' . $settings['images_url'] . '/icons/bullet-verde.gif" atl="" />
          No insultos o malos tratos.
          <br />
          <img src="' . $settings['images_url'] . '/icons/bullet-verde.gif" atl="" />
          No pornograf&iacute;a.<br />
          <br />
          <center style="font-size:11px;">
            <img src="' . $settings['images_url'] . '/icons/bullet-rojo.gif" atl="" />
            Cumpliendo estos puntos m&aacute;s teniendo en cuenta el <a href="' . $boardurl . '/protocolo/" target="_blank" title="Protocolo">protocolo</a>,
            es probable que su post no sea eliminado ni editado.
          </center>
          <p align="right" style="margin: 0px; padding: 0px; font-size: 11px;">Muchas gracias.</p>
        </div>
      </div>';

  echo '
    <div class="ed-ag-post" style="float: left; margin-bottom: 8px;">
      <div class="box_title" style="width: 677px;">
        <div class="box_txt ed-ag-posts">
          <center>';

  if ((!$context['is_new_post'])) {
    echo $context['submit_label'];
  } else {
    echo 'Agregar nuevo post';
  }

  echo '
        </center>
      </div>
      <div class="box_rss">
        <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 16px; height: 16px;" border="0" />
      </div>
    </div>
    <div class="windowbg" border="0" style="width: 669px; padding: 4px;">';

  if ($context['allow_admin'] && !empty($_REQUEST['topic'])) {
    echo '
      <b class="size11">Causa de edici&oacute;n:</b><br />
      <input type="text" onfocus="foco(this);" onblur="no_foco(this);" name="causa" value="' . $context['causa'] . '" tabindex="' . $context['tabindex']++ . '" style="width: 415px;" maxlength="60" />
      <br />';
  }

  echo '
    <b class="size11">T&iacute;tulo:</b><br />
    <input type="text" onfocus="foco(this);" onblur="no_foco(this);" name="subject"' . ($context['subject'] == '' ? '' : ' value="' . $context['subject']) . '" tabindex="' . $context['tabindex']++ . '" style="width: 415px;" maxlength="60" />
    <label id="error1"></label>
    <br />
    <br />';

  theme_postbox($context['message']);

  echo '
      </div>
    </div>';

  echo '
    <input type="hidden" name="topic" value="' . $context['current_topic'] . '" />
    <input type="hidden" name="sc" value="' . $context['session_id'] . '" />
    <input type="hidden" name="seqnum" value="' . $context['form_sequence_number'] . '" />';

  echo '
        </div>
      </form>
      <div style="clear:both"></div>
    </div>';

  // A hidden form to post data to the spell checking window.
  if ($context['show_spellchecking'])
    echo '
      <form action="' . $scripturl . '?action=spellcheck" method="post" accept-charset="' . $context['character_set'] . '" name="spell_form" id="spell_form" target="spellWindow">
        <input type="hidden" name="spellstring" value="" />
      </form>';
}

// This function displays all the stuff you'd expect to see with a message box, the box, BBC buttons and of course smileys.
function template_postbox(&$message) {
  global $context, $settings, $txt, $db_prefix, $boardurl;

  $topic = (int) $_REQUEST['topic'];

  if (isset($topic)) {
    $request = db_query("
      SELECT *
      FROM ({$db_prefix}messages AS men, {$db_prefix}topics AS t)
      WHERE men.ID_TOPIC = {$topic}
      AND men.ID_TOPIC = t.ID_TOPIC", __FILE__, __LINE__);

    while ($row = mysqli_fetch_assoc($request)) {
      $context['ID_BOARD'] = $row['ID_BOARD'];
      $context['hiddenOption'] = $row['hiddenOption'];
      $context['isSticky'] = $row['isSticky'];
      $context['locked'] = $row['locked'];
    }

    mysqli_free_result($request);
  }

  echo '<b class="size11">Mensaje del post:</b><br />';

  echo '
    <textarea style="height: 300px; width: 615px;" onfocus="foco(this);" onblur="no_foco(this);" id="markItUp" name="' . $context['post_box_name'] . '" class="markItUpEditor" tabindex="3">' . $message . '</textarea>
    <label id="error2"></label>
    <label id="error3"></label>';

  if (!empty($context['smileys']['postform'])) {
    foreach ($context['smileys']['postform'] as $smiley_row) {
      foreach ($smiley_row['smileys'] as $smiley) {
        echo '
          <a href="javascript:void(0);" onclick="replaceText(\' ' . $smiley['code'] . '\', document.forms.' . $context['post_form'] . '.' . $context['post_box_name'] . '); return false;">
            <img src="' . $settings['smileys_url'] . '/' . $smiley['filename'] . '" align="bottom" alt="" title="' . $smiley['description'] . '" />
          </a> ';
      }
    }
  }

  if (!empty($context['smileys']['popup']))
    echo '
      <script type="text/javascript">
        function openpopup() {
          var winpops = window.open("' . $boardurl . '/emoticones/", "", "width=255px,height=500px,scrollbars");
        }
      </script>
      <a href="javascript:openpopup()">[' . $txt['more_smileys'] . ']</a>
      <br />';

  echo '<b class="size11">' . $txt['smftags_topic'] . '</b><br />';

  if ($context['is_new_post']) {
    echo '
      <input style="width: 415px;" maxlength="128" value="" name="tags" tabindex="' . $context['tabindex']++ . '" type="text" onfocus="foco(this);" onblur="no_foco(this);" />
      <br />
      <span class="size9">' . $txt['smftags_seperate'] . '</b></span>
      <label id="tagserror" class="size10" style="color: red;"></label>
      <label id="tagserrordos" class="size10" style="color: red;"></label>
      <br />';
  } else {
    echo '<input style="width:415px;" maxlength="128" value="';

    $request = db_query("
      SELECT *
      FROM ({$db_prefix}tags_log as l, {$db_prefix}tags as t)
      WHERE t.ID_TAG = l.ID_TAG
      AND l.ID_TOPIC = " . $topic, __FILE__, __LINE__);

    $count = mysqli_num_rows($request);
    $contar = 0;

    while ($row = mysqli_fetch_assoc($request)) {
      echo trim(htmlentities($row['tag'], ENT_QUOTES, 'ISO-8859-1'));

      $contar++;

      if ($contar < $count) {
        echo ', ';
      }
    }

    mysqli_free_result($request);

    echo '" name="tags" tabindex="' . $context['tabindex']++ . '" type="text" onfocus="foco(this);" onblur="no_foco(this);" />
      <br />
      <span class="size9">' . $txt['smftags_seperate'] . '</b></span>
      <label id="tagserror" class="size10" style="color: red;"></label>
      <label id="tagserrordos" class="size10" style="color: red;"></label>
      <br />';
  }

  echo '
    <b class="size11">Categor&iacute;a:</b><br />
    <select style="width: 202px;" tabindex="' . $context['tabindex']++ . '" name="categorias" class="select">
      <option value="-1"' . (empty($context['ID_BOARD']) ? ' selected="selected"' : '' ) . '>Elegir categor&iacute;a</option>';

  foreach ($context['boards'] as $board) {
    echo '
      <option value="' . $board['id'] . '"' . ($context['ID_BOARD'] == $board['id'] ? ' selected="selected"' : '') . '>' . $board['name'] . '</option>';
  }

  echo '
    </select>
    <label id="error5"></label><br />
    <font class="size11"><b>Opciones:</b></font>';

  if ($context['can_sticky']) {
    if ($context['sticky']) {
      echo '
        <br />
        <label for="check_sticky">
          <input type="checkbox" name="sticky" id="check_sticky" value="0" />&nbsp;Quitarle sticky
        </label>';
    } else {
      echo '
        <br />
          <label for="check_sticky">
            <input type="checkbox" name="sticky" id="check_sticky" value="1" />&nbsp;Agregarle sticky
          </label>';
    }
  }

  if ($context['hiddenOption']) {
    echo '<br /><label for="hiddenOption"><input type="checkbox" name="hiddenOption" id="hiddenOption" value="0" /> Todos p&uacute;blico</label>';
  } else {
    echo '<br /><label for="hiddenOption"><input type="checkbox" name="hiddenOption" id="hiddenOption" value="1" /> S&oacute;lo usuarios registrados</label>';
  }

  if ($context['can_lock']) {
    if ($context['locked']) {
        echo '
          <br />
          <label for="check_lock">
            <input type="checkbox" name="lock" id="check_lock" value="0" />&nbsp;Si permitir comentarios.
          </label>';
      } else {
        echo '
          <br />
          <label for="check_lock">
            <input type="checkbox" name="lock" id="check_lock" value="1" />&nbsp;No permitir comentarios.
          </label>';
    }
  }

  if ($context['show_spellchecking']) {
    echo '<input type="button" value="' . $txt['spell_check'] . '" tabindex="' . $context['tabindex']++ . '" onclick="spellCheck(\'postmodify\', \'message\');" />';
  }


  if ((!$context['is_new_post'])) {
    echo '
      <center>
        <input onclick="vprevia(this.form.subject.value, this.form.message.value, this.form.tags.value, this.form);" class="button" style="font-size: 15px;" value="Previsualizar" title="Previsualizar" type="button" tabindex="' . $context['tabindex']++ . '" />
      </center>';
  } else {
    echo '
      <center>
        <input onclick="vprevia(this.form.subject.value, this.form.message.value, this.form.tags.value, this.form);" class="button" style="font-size: 15px;" value="Previsualizar" title="Previsualizar" type="button" tabindex="' . $context['tabindex']++ . '" />
      </center>';
  }

  echo '<br />';
}

function template_announce() {
  global $context, $settings, $options, $txt, $scripturl;

  echo '
    <form action="' . $scripturl . '?action=announce;sa=send" method="post" accept-charset="' . $context['character_set'] . '">
      <table width="600" cellpadding="5" cellspacing="0" border="0" align="center" class="tborder">
        <tr class="titlebg">
          <td>' . $txt['announce_title'] . '</td>
        </tr>
        <tr class="windowbg">
          <td class="smalltext" style="padding: 2ex;">' . $txt['announce_desc'] . '</td>
        </tr>
        <tr>
          <td class="windowbg2">
            ' . $txt['announce_this_topic'] . '&nbsp;
            <a href="' . $scripturl . '?topic=' . $context['current_topic'] . '.0">' . $context['topic_subject'] . '</a>
            <br />
          </td>
        </tr>
        <tr>
          <td class="windowbg2">';

  foreach ($context['groups'] as $group)
    echo '
      <label for="who_' . $group['id'] . '">
        <input type="checkbox" name="who[' . $group['id'] . ']" id="who_' . $group['id'] . '" value="' . $group['id'] . '" checked="checked" class="check" />&nbsp;' . $group['name'] . '
      </label>&nbsp;
      <i>(' . $group['member_count'] . ')</i>
      <br />';

  echo '
            <br />
            <label for="checkall">
              <input type="checkbox" id="checkall" class="check" onclick="invertAll(this, this.form);" checked="checked" />&nbsp;<i>' . $txt[737] . '</i>
            </label>
          </td>
        </tr>
        <tr>
          <td class="windowbg2" style="padding-bottom: 1ex;" align="center">
            <input type="submit" value="' . $txt[105] . '" />
          </td>
        </tr>
      </table>
      <input type="hidden" name="sc" value="' . $context['session_id'] . '" />
      <input type="hidden" name="topic" value="' . $context['current_topic'] . '" />
      <input type="hidden" name="move" value="' . $context['move'] . '" />
      <input type="hidden" name="goback" value="' . $context['go_back'] . '" />
    </form>';
}

function template_announcement_send() {
  global $context, $settings, $options, $txt, $scripturl;

  echo '
    <form action="' . $scripturl . '?action=announce;sa=send" method="post" accept-charset="' . $context['character_set'] . '" name="autoSubmit" id="autoSubmit">
      <table width="600" cellpadding="5" cellspacing="0" border="0" align="center" class="tborder">
        <tr class="titlebg">
          <td>
            ' . $txt['announce_sending'] . '&nbsp;
            <a href="' . $scripturl . '?topic=' . $context['current_topic'] . '.0" target="_blank">' . $context['topic_subject'] . '</a>
          </td>
        </tr>
        <tr>
          <td class="windowbg2"><b>' . $context['percentage_done'] . '% ' . $txt['announce_done'] . '</b></td>
        </tr>
        <tr>
          <td class="windowbg2" style="padding-bottom: 1ex;" align="center">
            <input type="submit" name="b" value="' . $txt['announce_continue'] . '" />
          </td>
        </tr>
      </table>
      <input type="hidden" name="sc" value="' . $context['session_id'] . '" />
      <input type="hidden" name="topic" value="' . $context['current_topic'] . '" />
      <input type="hidden" name="move" value="' . $context['move'] . '" />
      <input type="hidden" name="goback" value="' . $context['go_back'] . '" />
      <input type="hidden" name="start" value="' . $context['start'] . '" />
      <input type="hidden" name="membergroups" value="' . $context['membergroups'] . '" />
    </form>
    <script language="JavaScript" type="text/javascript">
      <!-- // -->
      <![CDATA[
        var countdown = 2;
        doAutoSubmit();

        function doAutoSubmit() {
          if (countdown == 0)
            document.forms.autoSubmit.submit();
          else if (countdown == -1)
            return;

          document.forms.autoSubmit.b.value = "' . $txt['announce_continue'] . ' (" + countdown + ")";
          countdown--;

          setTimeout("doAutoSubmit();", 1000);
        }
      // ]]>
    </script>';
}

function template_quickreply_box() {}

?>