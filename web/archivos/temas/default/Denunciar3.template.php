<?php

function template_manual_above() {}

function template_manual_below() {}

function template_manual_intro() {
  global $context, $settings, $db_prefix, $boardurl;

  $context['NOMBRE_USER'] = htmlentities(addslashes($_GET['id']), ENT_QUOTES, 'UTF-8');
  $request = db_query("
    SELECT mem.ID_MEMBER, mem.memberName, mem.realName
    FROM ({$db_prefix}members AS mem)
    WHERE mem.memberName = '" . $context['NOMBRE_USER'] . "'
    LIMIT 1 ", __FILE__, __LINE__);

  while ($row = mysqli_fetch_assoc($request)) {
    $memberName = $row['memberName'];
    $ID_MEMBER = $row['ID_MEMBER'];
  }

  mysqli_free_result($request);

  if ($context['user']['is_guest']) {
    fatal_error('Para denunciar a un usuario debes autentificarte.-', false);
  } else if (empty($_GET['id'])) {
    fatal_error('Debes ingresar el usuario a denunciar.-', false);
  } else if ($context['NOMBRE_USER'] != $memberName) {
    fatal_error('El usuario que deseas denunciar no existe.-', false);
  } else if ($context['NOMBRE_USER'] == $context['user']['name']) {
    fatal_error('No puedes denunciarte a ti mismo.-', false);
  } else {
    echo '
      <script type="text/javascript">
        function errorrojos(comentario) {
          if(comentario == \'\') {
            document.getElementById(\'errorss\').innerHTML = \'<br /><font class="size10" style="color: red;">Es necesario escribir un comentario sobre la denuncia.</font>\';
            return false;
          }
        }
      </script>
      <form action="' . $boardurl . '/denuncia/enviar/" method="post" accept-charset="' . $context['character_set'] . '">
        <div>
          <div class="box_buscador">
            <div class="box_title" style="width: 921px;">
              <div class="box_txt box_buscadort">
                <center>Denunciar Usuario</center>
              </div>
              <div class="box_rss">
                <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 14px; height: 12px;" border="0" />
              </div>
            </div>
            <div class="windowbg" style="width: 919px;">
              <center>
                <p align="center" style="margin: 0px; padding: 0px;" class="size11">
                  <br />
                  <b>Denunciar al usuario:</b>
                  <br />
                  ' . $memberName . '
                  <br /><br />
                  <font class="size11">
                    <b>Raz&oacute;n de la denuncia:</b>
                  </font>
                  <br />
                  <select name="razon" style="color: black; background-color: rgb(250, 250, 250); font-size: 12px;">
                    <option value="Hace Spam">Hace Spam</option>
                    <option value="Es Racista o irrespetuoso">Es Racista o irrespetuoso</option>
                    <option value="Publica informacion personal">Publica informaci&oacute;n personal</option>
                    <option value="Publica Pornografia">Publica Pornograf&iacute;a</option>
                    <option value="No cumple con el protocolo">No cumple con el protocolo</option>
                    <option value="Otra razon (especificar)">Otra raz&oacute;n (especificar)</option>
                  </select>
                  <br /><br />
                  <font class="size11">
                    <b>Aclaraci&oacute;n y comentarios:</b>
                  </font>
                  <br />
                  <textarea name="comentario" cols="40" rows="5" wrap="hard" tabindex="6"></textarea>
                  <label id="errorss"></label>
                  <br /><br />
                  <input onclick="return errorrojos(this.form.comentario.value);" class="login" type="submit" value="Denunciar Usuario" />
                  <br />
                  <input type="hidden" name="ID_TOPIC" value="' . $ID_MEMBER . '" />
                  <input type="hidden" name="tipo" value="user" />
                  <br />
                </p>
              </center>
            </div>
          </div>
        </div>
      </form>
      <div style="clear:both"></div>'; 
  }
}

function template_manual_login() {
  global $settings, $boardurl;

  echo '
    <div align="center">
      <div class="box_errors">
        <div class="box_title" style="width: 388px">
          <div class="box_txt box_error" align="left">Denuncia enviada</div>
          <div class="box_rss">
            <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 14px; height: 12px;" border="0" />
          </div>
        </div>
        <div class="windowbg" style="width: 380px; padding: 4px;">
          <br />
          Tu denuncia ha sido enviada correctamente.-
          <br /><br />
          <input class="login" style="font-size: 11px;" type="submit" title="Ir a la P&aacute;gina principal" value="Ir a la P&aacute;gina principal" onclick="location.href=\'' . $boardurl . '/\'" />
          <br /><br />
        </div>
      </div>
      <br /></div>
      <div style="clear:both"></div>
    </div>';
}

?>