<?php
function template_manual_intro() {
  global $context, $settings, $db_prefix, $boardurl;

  $context['ID_DEL_POST'] = (int) $_GET['id'];
  $request = db_query("
    SELECT m.subject, m.ID_TOPIC, m.ID_MEMBER, ma.ID_MEMBER, ma.memberName, t.ID_MEMBER_STARTED
    FROM ({$db_prefix}messages AS m, {$db_prefix}members AS ma, {$db_prefix}topics AS t)
    WHERE m.ID_TOPIC = {$context['ID_DEL_POST']}
    AND m.ID_MEMBER = ma.ID_MEMBER
    AND t.ID_TOPIC  = m.ID_TOPIC
    AND t.ID_TOPIC = " . $context['ID_DEL_POST'], __FILE__, __LINE__);

  while ($row = mysqli_fetch_assoc($request)) {
    $titulo = $row['subject'];
    $id = $row['ID_TOPIC'];
    $usuario = $row['memberName'];
    $started = $row['ID_MEMBER_STARTED'];		
  }

  mysqli_free_result($request);

  if ($context['user']['is_guest']) {
    fatal_error('Disculpe, para denunciar un post debe autentificarte.', false);
  } else if (empty($context['ID_DEL_POST'])) {
    fatal_error('Debes ingresar la identificador del post a denunciar.', false);
  } else if ($context['ID_DEL_POST'] != $id)  {
    fatal_error('El post que deseas denunciar no existe', false);
  } else if($started == $context['user']['id']) {
    fatal_error('Disculpe, pero no puedes denunciar tus post. Si tienes alg&uacute;n problema, b&oacute;rralo o ed&iacute;talo tu mismo.', false);
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
        <div>
          <div class="box_buscador">
            <div class="box_title" style="width: 921px;">
              <div class="box_txt box_buscadort"><center>Denunciar Post</center></div>
              <div class="box_rss">
                <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 14px; height: 12px;" border="0" />
              </div>
            </div>
            <table border="0px" class="windowbg" width="921px">
              <tr align="center">
                <td align="center">
                  <form action="' . $boardurl . '/denuncia/enviar/" method="post">
                    <p align="center" class="size11">
                      <b>Denunciar el post:</b>&nbsp;<br />
                      ' . $id  . ' / ' . $titulo . '
                      <b>Creado por:</b>&nbsp;<br />
                      ' . $usuario . '<br /><br />
                      <font class="size11"><b>Raz&oacute;n de la denuncia:</b></font><br />
                      <select name="razon" style="color: black; background-color: rgb(250, 250, 250); font-size: 12px;">
                        <option value="Re-post">Re-post</option>
                        <option value="Se hace spam">Se hace spam</option>
                        <option value="Tiene enlaces muertos">Tiene enlaces muertos</option>
                        <option value="Es racista o irrespetuoso">Es racista o irrespetuoso</option>

                        <option value="Contiene informacion personal">Contiene informaci&oacute;n personal</option>
                        <option value="El titulo esta en mayuscula">El t&iacute;tulo est&aacute; en may&uacute;scula</option>
                        <option value="Contiene pornografia">Contiene pornograf&iacute;a</option>
                        <option value="Es gore o asqueroso">Es gore o asqueroso</option>
                        <option value="Esta mal la fuente">Est&aacute; mal la fuente</option>

                        <option value="Post demasiado pobre">Post demasiado pobre</option>
                        <option value="Pide contrasena y no esta">Pide contrase&ntilde;a y no est&aacute;</option>
                        <option value="No cumple con el protocolo">No cumple con el protocolo</option>
                        <option value="Otra razon (especificar)">Otra raz&oacute;n (especificar)</option>
                      </select>
                      <br /><br />
                      <font class="size11"><b>Aclaraci&oacute;n y comentarios:</b></font>
                      <br />
                      <textarea name="comentario" cols="40" rows="5" wrap="hard" tabindex="6"></textarea>
                      <label id="errorss"></label>
                      <br />
                      <font size="1">En el caso de ser Re-post se debe indicar el enlace del post original.</font>
                      <br /><br />
                      <input onclick="return errorrojos(this.form.comentario.value);" class="login" type="submit" value="Denunciar Post" />
                      <br />
                      <input type="hidden" name="ID_TOPIC" value="' . $id  . '">
                      <input type="hidden" name="tipo" value="post" />
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
            <input class="login" style="font-size: 11px;" type="submit" title="Ir a la P&aacute;gina principal" value="Ir a la P&aacute;gina principal" onclick="location.href=\'' . $boardurl . '/\'" /><br /><br />
          </div>
        </div>
        <br />
      </div>
      <div style="clear:both"></div>
    </div>';
}

function template_manual_above() {}
function template_manual_below() {}

?>