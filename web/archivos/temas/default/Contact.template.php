<?php
function template_main() {
  global $context, $modSettings, $txt, $settings, $boardurl;

  echo '
    <script>
      function error(nombre, email, comentario, code) {
        if (nombre == \'\') {
          document.getElementById(\'nombre\').innerHTML = \'<br /><font class="size10" style="color: red;">Debes agregar tu nombre y apellido.</font>\';
          return false;
        }

        if (email == \'\') {
          document.getElementById(\'errorr\').innerHTML = \'<br /><font class="size10" style="color: red;">Debes agregar tu e-mail.</font>\';
          return false;
        }

        if (comentario == \'\') {
          document.getElementById(\'comentario\').innerHTML = \'<font class="size10" style="color: red;"><br />Debes agregar el comentario.</font>\';
          return false;
        }

        if (code == \'\') {
          document.getElementById(\'visual_verification_code\').innerHTML = \'<font class="size10" style="color: red;"><br />Debes insertar el codigo.</font>\';
          return false;
        }
      }
    </script>
    <div>
      <form action="' . $boardurl . '/contactanos/enviando/" method="post" accept-charset="' . $context['character_set'] . '">
        <div class="box_buscador">
          <div class="box_title" style="width: 920px;">
            <div class="box_txt box_buscadort">
              <center>Contacto</center>
            </div>
            <div class="box_rss">
              <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 14px; height: 12px;" border="0" />
            </div>
          </div>
          <div class="windowbg" style="width: 912px; padding: 4px;">
            <center>
               <b class="size11">* Su nombre y apellido:</b><br />
              <input style="width:184px;" name="nombre" tabindex="1" type="text" onfocus="foco(this);" onblur="no_foco(this);" />
              <label id="nombre"></label><br />
              <b class="size11">* E-mail:</b><br />
              <input style="width: 184px;" name="email" tabindex="2" type="text" onfocus="foco(this);" onblur="no_foco(this);" />
              <label id="errorr"></label><br />
              <b class="size11">Empresa:</b><br />
              <input style="width: 184px;" name="empresa" tabindex="3" type="text" onfocus="foco(this);" onblur="no_foco(this);" />
              <br />			
              <b class="size11">Tel&eacute;fono:</b><br />
              <input style="width:184px;" name="tel" value="" tabindex="4" type="text" onfocus="foco(this);" onblur="no_foco(this);" />
              <br />
              <b class="size11">Motivo:</b><br />
              <select tabindex="5" style="width: 85px;" class="select" name="motivo">
                <option value="Publicidad">Publicidad</option>
                <option value="Sugerencias">Sugerencias</option>

                <option value="Peticiones">Peticiones</option>
                <option value="Errores">Errores</option>
                <option value="Otros">Otros</option>
              </select>
              <br />
              <b class="size11">Horarios de contacto:</b><br />
              <input tabindex="6" style="width: 134px;" name="hc" type="text" onfocus="foco(this);" onblur="no_foco(this);" />
              <br />
              <b class="size11">* Comentarios:</b><br />
              <textarea onfocus="foco(this);" onblur="no_foco(this);" name="comentario" style="width: 249px;" cols="40" rows="5" tabindex="7"></textarea>
              <label id="comentario"></label><br />
              <b class="size11">* C&oacute;digo de la im&aacute;gen</b><br />
              <script type="text/javascript">
                var RecaptchaOptions = {
                  theme : \'' . (empty($modSettings['recaptcha_theme']) ? 'clean' : $modSettings['recaptcha_theme']) . '\',
                };
              </script>
              <script type="text/javascript" src="http://api.recaptcha.net/challenge?k=' . $modSettings['recaptcha_public_key'] . '"></script>
              <noscript>
                <iframe src="http://api.recaptcha.net/noscript?k=' . $modSettings['recaptcha_public_key'] . '" frameborder="0"></iframe><br />
                <textarea name="recaptcha_challenge_field" rows="2" cols="10"></textarea>
                <input type="hidden" name="recaptcha_response_field" value="manual_challenge" />
              </noscript>
              <label id="visual_verification_code"></label><br />
              <font class="size11" style="color: red;">* Campos obligatorios</font><br />
              <input class="login" onclick="return error(this.form.nombre.value, this.form.email.value, this.form.comentario.value, this.form.code);" name="enviar" value="Enviar" type="submit" />
              <br />
              <span class="size9">- Su IP (' . $_SERVER['REMOTE_ADDR'] . ') Ser&aacute; almacenada en nuestra base de datos por razones de seguridad.</span>
            </center>
          </div>
        </div>
      </form>
    </div>
    <div style="clear:both"></div>';

  //--------------------------------------------------------------------------------------------------
  //		Visual verification
  //--------------------------------------------------------------------------------------------------
  if ($context['visual_verification'] || isset($context['contact_form_error_visual_verification'])) {
    echo '
      <table class="normaltext">
        <tr>
          <td';

    if (isset($context['contact_form_error_visual_verification']))
      echo ' style="border-style: solid; border-color: red; border-width: 2px;"';

    echo '>
        <span class="normaltext">
          <br />
          ' . $txt['contact_form_label_verification'] . '
          <span class="smalltext" style="color: red;"> ' . $txt['contact_form_field_required'] . '</span>
        </span><br />' . template_control_verification($context['visual_verification_id']) . '
      </td>
      <td>';

    if (isset($context['contact_form_error_visual_verification']))
      echo '<span class="smalltext" style="color: red;">' . $context['visual_verification_error_dscr'] . '</span>';

    echo '</td>
        </tr>
      </table>';
  }
}

?>