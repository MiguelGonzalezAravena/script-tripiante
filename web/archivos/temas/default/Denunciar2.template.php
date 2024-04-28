<?php

function template_manual_above() {}

function template_manual_below() {}

function template_manual_intro() {
  global $context, $settings, $db_prefix, $boardurl;

  $context['ID_PICTURE'] = (int) $_GET['id'];

  $request = db_query("
    SELECT g.title, g.ID_PICTURE, g.ID_MEMBER AS ID_MEMBER2, ma.ID_MEMBER, ma.memberName
    FROM ({$db_prefix}gallery_pic AS g, {$db_prefix}members AS ma)
    WHERE g.ID_MEMBER = ma.ID_MEMBER
    AND g.ID_PICTURE = " . $context['ID_PICTURE'], __FILE__, __LINE__);

  while ($row = mysqli_fetch_assoc($request)) {
    $titulo = $row['title'];
    $id = $row['ID_PICTURE'];
    $usuario = $row['memberName'];
    $started = $row['ID_MEMBER2'];
  }

  mysqli_free_result($request);

  
  if ($context['user']['is_guest']) {
    fatal_error('Para denunciar una imagen debes autentificarte.', false);
  } else if (empty($_GET['id'])) {
    fatal_error('Debes ingresar el ID de la imagen a denunciar.', false);
  } else if ($context['ID_PICTURE'] != $id) {
    fatal_error('La imagen que deseas denunciar no existe.', false);
  } else if ($started == $context['user']['id']) {
    fatal_error('No puedes denunciar tus im&aacute;genes. Si tienes alg&uacute;n problema, b&oacute;rrala o ed&iacute;tala t&uacute;.', false);
  } else {
    echo '
        <script type="text/javascript">
          function errorrojos(comentario) {
            if (comentario == \'\') {
              document.getElementById(\'errorss\').innerHTML = \'<br /><font class="size10" style="color: red;">Es necesario escribir un comentario sobre la denuncia.</font>\';
              return false;
            }
          }
        </script>
        <div>
          <div class="box_buscador">
            <div class="box_title" style="width: 921px;">
              <div class="box_txt box_buscadort">
                <center>Denunciar Imagen</center>
              </div>
              <div class="box_rss">
                <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 14px; height: 12px;" border="0" />
              </div>
            </div>
            <table class="windowbg" width="921px">
              <tr align="center">
                <td align="center">
                  <form action="' . $boardurl . '/denuncia/enviar/" method="post">
                    <p align="center" class="size11">
                      <b>Denunciar la imagen:</b>
                      <br />
                      ' . $id  . ' / ' . $titulo . '
                      <br /><br />
                      <b>Creada por:</b>
                      <br />
                      ' . $usuario . '
                      <br /><br />
                      <font class="size11">
                        <b>Raz&oacute;n de la denuncia:</b>
                      </font>
                      <br />
                      <select name="razon" style="color: black; background-color: rgb(250, 250, 250); font-size: 12px;">
                        <option value="Imagen ya agregada">Imagen ya agregada</option>
                        <option value="Se hace spam">Se hace spam</option>
                        <option value="Contiene pornografia">Contiene pornograf&iacute;a</option>
                        <option value="Es gore o asqueroso">Es gore o asqueroso</option>
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
                      <br />
                      <font size="1">En el caso de ser una imagen ya agregada, se debe indicar el enlace de la imagen original.</font>
                      <br /><br />
                      <input onclick="return errorrojos(this.form.comentario.value);" class="login" type="submit" value="Denunciar Imagen" />
                      <br />
                      <input type="hidden" name="ID_TOPIC" value="' . $id  . '" />
                      <input type="hidden" name="tipo" value="imagen" />
                    </p>
                  </form>
                </td>
              </tr>
            </table>
          </div>
        </div>
        <div style="clear:both"></div>
      </div>'; 
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
        <br />
      </div>
      <div style="clear:both"></div>
    </div>';
}

?>