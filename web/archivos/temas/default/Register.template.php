<?php

function codigoalazar() {
  global $id;

  $dato = array(1, 2, 3, 4, 5, 6, 7, 8, 9);
  $datoAleatorio = array_rand($dato, 4);

  for ($i = 0; $i <= count($datoAleatorio); $i++) {
    echo $dato[$datoAleatorio[$i]];
    $captchaCode = $dato[$datoAleatorio[$i]];
  }
}

function template_before() {
  global $context, $settings, $txt, $modSettings, $boardurl;

  // TO-DO: Guardar en alguna variable de languages
  // Paises
  $countries = [
    'ar' => 'Argentina',
    'bo' => 'Bolivia',
    'br' => 'Brasil',
    'cl' => 'Chile',
    'co' => 'Colombia',
    'cr' => 'Costa Rica',
    'cu' => 'Cuba',
    'ec' => 'Ecuador',
    'es' => 'Espa&ntilde;a',
    'gt' => 'Guatemala',
    'it' => 'Italia',
    'mx' => 'M&eacute;xico',
    'py' => 'Paraguay',
    'pe' => 'Per&uacute;',
    'pt' => 'Portugal',
    'pr' => 'Puerto Rico',
    'uy' => 'Uruguay',
    've' => 'Venezuela',
    'ot' => 'Otro'
  ];

  echo '
    <script language="JavaScript" type="text/javascript">
      function nuevoAjax() {
        var xmlhttp = false;
        try {
          xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
        } catch(e) {
          try {
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
          } catch(E) {
            xmlhttp = false;
          }
        }
      
        if (!xmlhttp && typeof XMLHttpRequest != "undefined") {
          xmlhttp = new XMLHttpRequest();
        }
      
        return xmlhttp;
      }
      
      function nuevoEvento(evento) {
        var divMensaje = document.getElementById("error");
        sconderuno = document.getElementById("esconderuno");
        sconderdos = document.getElementById("esconderdos");
        scondertres = document.getElementById("escondertres");
        var image=document.getElementById("img");

        if (evento == "verificacion") {
          var input = document.getElementById("verificacion");
          var valor = input.value;
        }
        
        input.disabled = true;
        image.style.display = "inline";
        sconderuno.style.display = "none";
        sconderdos.style.display = "none";
        scondertres.style.display = "none";
        var ajax = nuevoAjax();
        
        ajax.open("POST", "' . $boardurl . '/nick-verificar/", true);
        ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        ajax.send(evento + "=" + valor);

        ajax.onreadystatechange = function() {
          if (ajax.readyState == 4) {
            input.disabled = false;
            image.style.display = "none";
            sconderuno.style.display = "";
            sconderdos.style.display = "";
            scondertres.style.display = "";
            divMensaje.innerHTML=ajax.responseText;
          }
        }
      }

      function mail(evento) {
        var divMensaje = document.getElementById("errord");
        sconderuno = document.getElementById("esconderunod");
        sconderdos = document.getElementById("esconderdosd");
        scondertres = document.getElementById("escondertresd");
        var image = document.getElementById("imgd");
        
        if (evento == "emailverificar") {
          var input = document.getElementById("emailverificar");
          var valor = input.value;
        }
        
        input.disabled = true;
        image.style.display = "inline";
        sconderuno.style.display = "none";
        sconderdos.style.display = "none";
        scondertres.style.display = "none";
        var ajax = nuevoAjax();

        ajax.open("POST", "' . $boardurl . '/email-verificar/", true);
        ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        ajax.send(evento + "=" + valor);
        ajax.onreadystatechange = function() {
          if (ajax.readyState == 4) {
            input.disabled = false;
            image.style.display = "none";
            sconderuno.style.display = "";
            sconderdos.style.display = "";
            scondertres.style.display = "";
            divMensaje.innerHTML = ajax.responseText;
          }
        }
      }

      function showtags(name, user, passwrd1, passwrd2, email, f, location, bday2, bday1, bday3) {
        if (name==\'\') {
          alert(\'Debes ingresar tu nombre y apellido.\');
          return false;
        }

        if (user == \'\') {
          alert(\'Debes ingresar un nick.\');
          return false;
        }

        if (passwrd1 == \'\') {
          alert(\'Debes ingresar una contrase\xfaa.\');
          return false;
        }

        if (passwrd2 == \'\') {
          alert(\'Debes conirmar tu contrase\xfaa.\');
          return false;
        }

        if (email == \'\') {
          alert(\'Debes ingresar un e-mail.\');
          return false;
        }

        if (f.usertitle.options.selectedIndex == -1 || f.usertitle.options[f.usertitle.options.selectedIndex].value == -1) {
          alert(\'Debes ingresar el pa\xeds donde tu vives.\');
          return false;
        }

        if (location == \'\') {
          alert(\'Debes ingresar la ciudad donde tu vives.\');
          return false;
        }

        if (bday2 == \'\') {
          alert(\'Debes ingresar el d\xeda de tu nacimiento.\');
          return false;
        }

        if (bday1 == \'\') {
          alert(\'Debes ingresar el mes de tu nacimiento.\');
          return false;
        }

        if (bday3 == \'\') {
          alert(\'Debes ingresar el a\xf1o de tu nacimiento.\');
          return false;
        }
      }

      function verifyAgree() {
        if (document.forms.creator.passwrd1.value != document.forms.creator.passwrd2.value) {
          alert("No coinciden las contrase\xf1as.");
          return false;
        }

        return true;
      }
    </script>
    <form action="' . $boardurl . '/registrandose/" method="post" accept-charset="' . $context['character_set'] . '" name="creator" id="creator">
    <div style="width: 354px; float: left; margin-right: 8px;">
      <div class="box_354" style="margin-bottom:8px;">
        <div class="box_title" style="width: 352px;">
          <div class="box_txt box_354-34">&#161;Aclaraci&oacute;n del registro&#33;</div>
          <div class="box_rss">
            <div class="icon_img">
              <img src="' . $settings['images_url'] . '/blank.gif" style="width: 16px; height: 16px;" border="0" alt="" />
            </div>
          </div>
        </div>
      <div style="width: 344px; padding: 4px;" class="windowbg">
        <font class="size10">
          El registro de usuarios en ' . $context['forum_name'] . ' es limitado.
          &nbsp;
          Al registrarte, tendr&aacute;s acceso a la totalidad de los posts.
          &nbsp;
          Podr&aacute;s tambi&eacute;n crear tus propios posts, los cuales ser&aacute;n publicados y los podr&aacute;n ver todos los usuarios.
          <br /><br />
          Al tener tu propia cuenta, prodr&aacute;s gozar de rangos, en lo cual al ir ascendiendo se te suman los permisos en la web.
          &nbsp;
          Para llegar al rango m&aacute;ximo, debes llegar a los 1500 puntos, y adem&aacute;s, la web entrega a los usuarios m&aacute;s destacados un rango especial que es el rango "Heredero" o "Abastecedor".
          &nbsp;
          Estos rangos tienen m&aacute;s permisos que todos los otros usuarios.
          <br /><br />
          Muchas gracias.
          <br /><br />
        </font>
        <font class="size9">IMPORTANTE: todos los casilleros con el asterisco (*) son obligatorios</font>
      </div>
    </div>
    <div class="box_354">
      <div class="box_title" style="width: 352px;">
        <div class="box_txt box_354-34">Destacados</div>
        <div class="box_rss">
          <div class="icon_img">
            <img src="' . $settings['images_url'] . '/blank.gif" style="width: 16px; height: 16px;" border="0" alt="" />
          </div>
        </div>
      </div>
      <div style="width: 344px; padding: 4px;" class="windowbg">
        <center>';

  ssi_destacados();

  echo '
          </center>
        </div>
      </div>
    </div>
    <div style="width: 560px; float: left;">
      <div class="box_560">
        <div class="box_title" style="width: 558px;">
          <div class="box_txt box_560-34">' . $txt[517] . '</div>
          <div class="box_rss">
            <div class="icon_img">
              <img src="' . $settings['images_url'] . '/blank.gif" style="width: 16px; height: 16px;" border="0" alt="" />
            </div>
          </div>
        </div>
      </div>
      <table border="0" width="100%" cellpadding="3" cellspacing="0" class="windowbg">
        <tr class="windowbg">
          <td width="100%">
            <table align="center" cellpadding="3" cellspacing="0" border="0" width="100%">
              <tr>
              <td align="right" width="40%">
                <font class="size11">
                  *
                  &nbsp;
                  <b>Nombre y Apellido:</b>
                </font>
              </td>
              <td>
                <input type="text" onfocus="foco(this);" onblur="no_foco(this);" name="name" size="20" tabindex="' . $context['tabindex']++ . '" maxlength="50" />
              </td>
            </tr>
            <tr>
              <td align="right" width="40%">
                <font class="size11">
                  *
                  &nbsp;
                  <b>' . $txt[98] . ':</b>
                </font>
              </td>
              <td>
                <input type="text" onfocus="foco(this);" onblur="no_foco(this);nuevoEvento(\'verificacion\');" name="user" size="20" tabindex="' . $context['tabindex']++ . '" maxlength="20" id="verificacion" />
                &nbsp;
                <img alt="" src="' . $settings['images_url'] . '/icons/cargando.gif" style="display: none;" id="img"/>
              </td>
            </tr>
            <tr id="esconderuno" style="display: none;">
              <td id="esconderdos" style="display: none;" align="right" width="40%">
              </td>
              <td id="escondertres" style="display: none;">
                <div id="error"></div>
              </td>
            </tr>
            <tr>
              <td align="right" width="40%">
                <font class="size11">
                  *
                  &nbsp;
                  <b>' . $txt[81] . ':</b>
                </font>
              </td>
              <td>
                <input maxlength="30" type="password" onfocus="foco(this);" onblur="no_foco(this);" name="passwrd1" size="30" tabindex="' . $context['tabindex']++ . '" />
              </td>
            </tr>
            <tr>
              <td align="right" width="40%">
                <font class="size11">
                  *
                  &nbsp;
                  <b>' . $txt[82] . ':</b>
                </font>
              </td>
              <td>
                <input type="password" onfocus="foco(this);" onblur="no_foco(this);" maxlength="30" name="passwrd2" size="30" tabindex="' . $context['tabindex']++ . '" />
              </td>
            </tr>
            <tr>
              <td align="right" width="40%">
                <font class="size11">
                  *
                  &nbsp;
                  <b>' . $txt[69] . ':</b>
                </font>
              </td>
              <td>
                <input type="text" onfocus="foco(this);" onblur="no_foco(this);mail(\'emailverificar\')" name="email" id="emailverificar" size="30" tabindex="' . $context['tabindex']++ . '" />
                &nbsp;&nbsp;
                <img alt="" src="' . $settings['images_url'] . '/icons/cargando.gif" style="display: none;" id="imgd"/>
              </td>
            </tr>
            <tr id="esconderunod" style="display: none;">
              <td id="esconderdosd" style="display: none;" align="right" width="40%"></td>
              <td id="escondertresd" style="display: none;">
                <div id="errord"></div>
              </td>
            </tr>
            <tr>
              <td align="right" width="40%">
                <font class="size11">
                  *
                  &nbsp;
                  <b>Pa&iacute;s:&nbsp;</b>
                </font>
              </td>
              <td>
                <select tabindex="' . $context['tabindex']++ . '" name="usertitle" id="usertitle">
                  <option value="-1">Seleccionar Pa&iacute;s</option>';

  $countries_keys = array_keys($countries);

  for ($i = 0; $i < count($countries_keys); $i++) {
    $value = $countries_keys[$i];
    echo '<option value="' . $value . '"' . ($context['member']['title'] == $value ? ' selected="selected"' : '') . '>' . $countries[ $value ] . '</option>';
  }

  echo '
          </select>
        </td>
      </tr>
      <tr>
        <td align="right" width="40%">
          <font class="size11">
            *
            &nbsp;
            <b>Ciudad:&nbsp;</b>
          </font>
        </td>
        <td>
          <input tabindex="' . $context['tabindex']++ . '" type="text" onfocus="foco(this);" onblur="no_foco(this);" name="location" size="30" value="" />
        </td>
      </tr>
      <tr>
        <td align="right" width="40%">
          <font class="size11">
            *
            &nbsp;
            <b>Sexo:&nbsp;</b>
          </font>
        </td>
        <td>
          <select name="gender" tabindex="' . $context['tabindex']++ . '" class="select" size="1">
            <option value="1">Masculino</option>
            <option value="2">Femenino</option>
          </select>
        </td>
      </tr>
      <tr>
        <td align="right" width="40%">
          <font class="size11">
            *
            &nbsp;
            <b>Fecha de nacimiento:</b>
          </font>
          <div class="smalltext">&#40;d&iacute;a&#47;mes&#47;a&ntilde;o&#41;</div>
        </td>
        <td>
          <select tabindex="' . $context['tabindex']++ . '" name="bday2" id="bday2" autocomplete="off">
            <option value="">D&iacute;a:</option>';

  for ($i = 1; $i < 32; $i++) {
    echo '<option value="' . $i . '">' . $i . '</option>';
  }

  echo '
    </select>
    <select tabindex="' . $context['tabindex']++ . '" name="bday1" id="bday1" autocomplete="off">
      <option value="">Mes:</option>';

  // Obtener primera key del arreglo de meses
  $key = key($txt['months']);

  // Generar el arreglo de meses con posición corrida - 1 valores;
  $months = $txt['months'][$key];

  for ($i = 1; $i < 13; $i++) {
    echo '<option value="' . $i . '">' . strtolower($months[$i - 1]) . '</option>';
  }

  echo '
      </select>
      <select tabindex="' . $context['tabindex']++ . '" name="bday3" id="bday3" autocomplete="off">
        <option value="">A&ntilde;o:</option>';

  for ($i = date("Y", time()) - 18; $i > 1899; $i--) {
    echo '<option value="' . $i . '">' . $i . '</option>';
  }

  echo '
            </select>
          </td>
        </tr>
        <tr>
          <td align="right" width="40%">
            <font class="size11">
              <b>Avatar:&nbsp;</b>
            </font>
          </td>
          <td>
            <input tabindex="' . $context['tabindex']++ . '" type="text" onfocus="foco(this);" onblur="no_foco(this);" name="avatar" size="30" value="' . $boardurl . '/avatar.gif" />
          </td>
        </tr>
        <tr>
          <td align="right" width="40%">
            <font class="size11">
              <b>Sitio Web&nbsp;/&nbsp;Blog:&nbsp;</b>
            </font>
          </td>
          <td>
            <input tabindex="' . $context['tabindex']++ . '" type="text" onfocus="foco(this);" onblur="no_foco(this);" name="websiteTitle" size="30" value="http://" />
          </td>
        </tr>
        <tr>
          <td align="right" width="40%">
            <font class="size11">
              <b>Mensaje personal:&nbsp;</b>
            </font>
          </td>
          <td>
            <input tabindex="' . $context['tabindex']++ . '" type="text" onfocus="foco(this);" onblur="no_foco(this);" name="personalText" size="30" maxlength="21" value="" />
          </td>
        </tr>
      </td>
    </tr>';

  if ($context['visual_verification'] || $context['use_recaptcha']) {
    echo '
                        <tr valign="top">
                          <td width="40%" align="right" valign="top">
                            <font class="size11">
                              *
                              &nbsp;
                              <strong>' . $txt['visual_verification_label'] . ':</strong>
                            </font>
                          </td>
                          <td>
                            <script type="text/javascript">
                              var RecaptchaOptions = {
                                theme : \''. empty($modSettings['recaptcha_theme']) ? 'clean' : $modSettings['recaptcha_theme'] . '\',
                              };
                            </script>
                            <script type="text/javascript" src="http://api.recaptcha.net/challenge?k=' . $modSettings['recaptcha_public_key'] . '"></script>
                            <noscript>
                              <iframe src="http://api.recaptcha.net/noscript?k=', $modSettings['recaptcha_public_key'], '" frameborder="0"></iframe>
                              <br />
                              <textarea name="recaptcha_challenge_field" rows="2" cols="10"></textarea>
                              <input type="hidden" name="recaptcha_response_field" value="manual_challenge" />
                            </noscript>
                          </td>
                        </tr>
                      </div>
                    </td>
                  </tr>
                  <tr valign="top">
                    <td align="right" width="40%" align="top">&nbsp;</td>
                    <td>
                      <label for="regagree">
                        <input tabindex="' . $context['tabindex']++ . '" type="checkbox" name="regagree" onclick="checkAgree();" id="regagree" class="check" />
                        &nbsp;
                        ' . $txt[585] . '
                      </label>
                      &nbsp;
                      <a href="' . $boardurl . '/terminos-y-condiciones/" target="_blank">T&eacute;rminos de uso</a>
                    </td>
                  </tr>
                </table>
                <br />
                <div align="center">
                  <font class="size11" style="color: red;">*&nbsp;Campos obligatorios</font>
                  <br /><br />
                  <input onclick="return showtags(this.form.name.value,this.form.user.value, this.form.passwrd1.value, this.form.passwrd2.value, this.form.email.value, this.form, this.form.location.value, this.form.bday2.value, this.form.bday1.value, this.form.bday3.value);" class="login" type="submit" name="regSubmit" value="' . $txt[97] . '" />
                </div>
              </form>
            </td>
          </tr>
        </table>
      </div>
      <div style="clear:both"></div>';
  }
}

// After registration... all done ;).
function template_after() {
  fatal_error('Su cuenta ha sido creada satifactoriamente.-<br /> Ya puedes ingresar al sitio.-', false);
}

// Template for giving instructions about COPPA activation.
function template_coppa() {
  global $context, $txt, $scripturl;

  // Formulate a nice complicated message!
  echo '
    <br />
    <table width="60%" cellpadding="4" cellspacing="0" border="0" class="tborder" align="center">
      <tr class="titlebg">
        <td>', $context['page_title'], '</td>
      </tr><tr class="windowbg">
        <td align="left">', $context['coppa']['body'], '<br /></td>
      </tr><tr class="windowbg">
        <td align="center">
          <a href="', $scripturl, '?action=coppa;form;member=', $context['coppa']['id'], '" target="_blank">', $txt['coppa_form_link_popup'], '</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a href="', $scripturl, '?action=coppa;form;dl;member=', $context['coppa']['id'], '">', $txt['coppa_form_link_download'], '</a><br /><br />
        </td>
      </tr><tr class="windowbg">
        <td align="left">', $context['coppa']['many_options'] ? $txt['coppa_send_to_two_options'] : $txt['coppa_send_to_one_option'], '</td>
      </tr>';

  // Can they send by post?
  if (!empty($context['coppa']['post'])) {
    echo '
      <tr class="windowbg">
        <td align="left"><b>1) ', $txt['coppa_send_by_post'], '</b></td>
      </tr><tr class="windowbg">
        <td align="left" style="padding-bottom: 1ex;">
          <div style="padding: 4px; width: 32ex; background-color: white; color: black; margin-left: 5ex; border: 1px solid black;">
            ', $context['coppa']['post'], '
          </div>
        </td>
      </tr>';
  }

  // Can they send by fax??
  if (!empty($context['coppa']['fax'])) {
    echo '
      <tr class="windowbg">
        <td align="left"><b>', !empty($context['coppa']['post']) ? '2' : '1', ') ', $txt['coppa_send_by_fax'], '</b></td>
      </tr><tr class="windowbg">
        <td align="left" style="padding-bottom: 1ex;">
          <div style="padding: 4px; width: 32ex; background-color: white; color: black; margin-left: 5ex; border: 1px solid black;">
            ', $context['coppa']['fax'], '
          </div>
        </td>
      </tr>';
  }

  // Offer an alternative Phone Number?
  if ($context['coppa']['phone']) {
    echo '
      <tr class="windowbg" style="padding-bottom: 1ex;">
        <td align="left">', $context['coppa']['phone'], '</td>
      </tr>';
  }

  echo '
    </table>
    <br />';
}

// An easily printable form for giving permission to access the forum for a minor.
function template_coppa_form() {
  global $context, $txt;

  // Show the form (As best we can)
  echo '
    <table border="0" width="100%" cellpadding="3" cellspacing="0" class="tborder" align="center">
      <tr>
        <td align="left">', $context['forum_contacts'], '</td>
      </tr>
      <tr>
        <td align="right">
          <i>', $txt['coppa_form_address'], '</i>: ', $context['ul'], '<br />
          ', $context['ul'], '<br />
          ', $context['ul'], '<br />
          ', $context['ul'], '
        </td>
      </tr>
      <tr>
        <td align="right">
          <i>', $txt['coppa_form_date'], '</i>: ', $context['ul'], '
          <br /><br />
        </td>
      </tr>
      <tr>
        <td align="left">
          ', $context['coppa_body'], '
        </td>
      </tr>
    </table>
    <br />';
}

// Show a window containing the spoken verification code.
function template_verification_sound() {
  global $context, $settings, $txt;

  echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"', $context['right_to_left'] ? ' dir="rtl"' : '', '>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=', $context['character_set'], '" />
    <title>', $context['page_title'], '</title>
    <link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/style.css" />
    <style type="text/css">';

  // Internet Explorer 4/5 and Opera 6 just don't do font sizes properly. (they are bigger...)
  if ($context['browser']['needs_size_fix'])
    echo '
      @import(', $settings['default_theme_url'], '/fonts-compat.css);';

  // Just show the help text and a "close window" link.
  echo '
    </style>
  </head>
  <body style="margin: 1ex;">
    <div class="popuptext">';
  if ($context['browser']['is_ie'])
    echo '
      <object classid="clsid:22D6F312-B0F6-11D0-94AB-0080C74C7E95" type="audio/x-wav">
        <param name="AutoStart" value="1" />
        <param name="FileName" value="', $context['verificiation_sound_href'], ';format=.wav" />
      </object>';
  else
    echo '
      <object type="audio/x-wav" data="', $context['verificiation_sound_href'], ';format=.wav">
        <a href="', $context['verificiation_sound_href'], ';format=.wav">', $context['verificiation_sound_href'], ';format=.wav</a>
      </object>';
  echo '
      <br />
      <a href="', $context['verificiation_sound_href'], ';sound">', $txt['visual_verification_sound_again'], '</a><br />
      <a href="javascript:self.close();">', $txt['visual_verification_sound_close'], '</a><br />
      <a href="', $context['verificiation_sound_href'], ';format=.wav">', $txt['visual_verification_sound_direct'], '</a>
    </div>
  </body>
</html>';
}

function template_admin_register() {
  global $context, $scripturl, $txt, $modSettings;

  echo '
  <form action="', $scripturl, '?action=regcenter" method="post" accept-charset="', $context['character_set'], '" name="postForm" id="postForm">
    <script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[
      function onCheckChange()
      {
        if (document.forms.postForm.emailActivate.checked || document.forms.postForm.password.value == \'\')
        {
          document.forms.postForm.emailPassword.disabled = true;
          document.forms.postForm.emailPassword.checked = true;
        }
        else
          document.forms.postForm.emailPassword.disabled = false;
      }
    // ]]></script>
    <table border="0" cellspacing="0" cellpadding="4" align="center" width="70%" class="tborder">
      <tr class="titlebg">
        <td colspan="2" align="center">', $txt['admin_browse_register_new'], '</td>
      </tr>';
  if (!empty($context['registration_done']))
    echo '
      <tr class="windowbg2">
        <td colspan="2" align="center"><br />
          ', $context['registration_done'], '
        </td>
      </tr><tr class="windowbg2">
        <td colspan="2" align="center"><hr /></td>
      </tr>';
  echo '
      <tr class="windowbg2">
        <th width="50%" align="right">
          <label for="user_input">', $txt['admin_register_username'], ':</label>
          <div class="smalltext" style="font-weight: normal;">', $txt['admin_register_username_desc'], '</div>
        </th>
        <td width="50%" align="left">
          <input type="text" name="user" id="user_input" size="30" maxlength="25" />
        </td>
      </tr><tr class="windowbg2">
        <th width="50%" align="right">
          <label for="email_input">', $txt['admin_register_email'], ':</label>
          <div class="smalltext" style="font-weight: normal;">', $txt['admin_register_email_desc'], '</div>
        </th>
        <td width="50%" align="left">
          <input type="text" name="email" id="email_input" size="30" />
        </td>
      </tr><tr class="windowbg2">
        <th width="50%" align="right">
          <label for="password_input">', $txt['admin_register_password'], ':</label>
          <div class="smalltext" style="font-weight: normal;">', $txt['admin_register_password_desc'], '</div>
        </th>
        <td width="50%" align="left">
          <input type="password" name="password" id="password_input" size="30" onchange="onCheckChange();" /><br />
        </td>
      </tr><tr class="windowbg2">
        <th width="50%" align="right">
          <label for="group_select">', $txt['admin_register_group'], ':</label>
          <div class="smalltext" style="font-weight: normal;">', $txt['admin_register_group_desc'], '</div>
        </th>
        <td width="50%" align="left">
          <select name="group" id="group_select">';

  foreach ($context['member_groups'] as $id => $name)
    echo '
            <option value="', $id, '">', $name, '</option>';
  echo '
          </select><br />
        </td>
      </tr><tr class="windowbg2">
        <th width="50%" align="right">
          <label for="emailPassword_check">', $txt['admin_register_email_detail'], ':</label>
          <div class="smalltext" style="font-weight: normal;">', $txt['admin_register_email_detail_desc'], '</div>
        </th>
        <td width="50%" align="left">
          <input type="checkbox" name="emailPassword" id="emailPassword_check" checked="checked" disabled="disabled" class="check" /><br />
        </td>
      </tr><tr class="windowbg2">
        <th width="50%" align="right">
          <label for="emailActivate_check">', $txt['admin_register_email_activate'], ':</label>
        </th>
        <td width="50%" align="left">
          <input type="checkbox" name="emailActivate" id="emailActivate_check"', !empty($modSettings['registration_method']) && $modSettings['registration_method'] == 1 ? ' checked="checked"' : '', ' onclick="onCheckChange();" class="check" /><br />
        </td>
      </tr><tr class="windowbg2">
        <td width="100%" colspan="2" align="right">
          <input type="submit" name="regSubmit" value="', $txt[97], '" />
          <input type="hidden" name="sa" value="register" />
        </td>
      </tr>
    </table>
    <input type="hidden" name="sc" value="', $context['session_id'], '" />
  </form>';
}

// Form for editing the agreement shown for people registering to the forum.
function template_edit_agreement() {
  global $context, $scripturl, $txt;

  // Just a big box to edit the text file ;).
  echo '
  <form action="', $scripturl, '?action=regcenter" method="post" accept-charset="', $context['character_set'], '">
    <table border="0" cellspacing="0" cellpadding="4" align="center" width="80%" class="tborder">
      <tr class="titlebg">
        <td align="center">', $txt['smf11'], '</td>
      </tr>';

  // Warning for if the file isn't writable.
  if (!empty($context['warning']))
    echo '
      <tr class="windowbg2">
        <td style="color: red; font-weight: bold;" align="center">
          ', $context['warning'], '
        </td>
      </tr>';
  echo '
      <tr class="windowbg2">
        <td align="center" style="padding-bottom: 1ex; padding-top: 2ex;">';

  // Show the actual agreement in an oversized text box.
  echo '
          <textarea cols="70" rows="20" name="agreement" style="width: 94%; margin-bottom: 1ex;">', $context['agreement'], '</textarea><br />
          <label for="requireAgreement"><input type="checkbox" name="requireAgreement" id="requireAgreement"', $context['require_agreement'] ? ' checked="checked"' : '', ' value="1" /> ', $txt[584], '.</label><br />
          <br />
          <input type="submit" value="', $txt[10], '" />
          <input type="hidden" name="sa" value="agreement" />
        </td>
      </tr>
    </table>
    <input type="hidden" name="sc" value="', $context['session_id'], '" />
  </form>';
}

function template_edit_reserved_words() {
  global $context, $scripturl, $txt;

  echo '
    <form action="', $scripturl, '?action=regcenter" method="post" accept-charset="', $context['character_set'], '">
      <table border="0" cellspacing="1" class="bordercolor" align="center" cellpadding="4" width="80%">
        <tr class="titlebg">
          <td align="center">
            ', $txt[341], '
          </td>
        </tr><tr>
          <td class="windowbg2" align="center">
            <div style="width: 80%;">
              <div style="margin-bottom: 2ex;">', $txt[342], '</div>
              <textarea cols="30" rows="6" name="reserved" style="width: 98%;">', implode("\n", $context['reserved_words']), '</textarea><br />

              <div align="left" style="margin-top: 2ex;">
                <label for="matchword"><input type="checkbox" name="matchword" id="matchword" ', $context['reserved_word_options']['match_word'] ? 'checked="checked"' : '', ' class="check" /> ', $txt[726], '</label><br />
                <label for="matchcase"><input type="checkbox" name="matchcase" id="matchcase" ', $context['reserved_word_options']['match_case'] ? 'checked="checked"' : '', ' class="check" /> ', $txt[727], '</label><br />
                <label for="matchuser"><input type="checkbox" name="matchuser" id="matchuser" ', $context['reserved_word_options']['match_user'] ? 'checked="checked"' : '', ' class="check" /> ', $txt[728], '</label><br />
                <label for="matchname"><input type="checkbox" name="matchname" id="matchname" ', $context['reserved_word_options']['match_name'] ? 'checked="checked"' : '', ' class="check" /> ', $txt[729], '</label><br />
              </div>

              <input type="submit" value="', $txt[10], '" name="save_reserved_names" style="margin: 1ex;" />
            </div>
          </td>
        </tr>
      </table>
      <input type="hidden" name="sa" value="reservednames" />
      <input type="hidden" name="sc" value="', $context['session_id'], '" />
    </form>';
}

function template_admin_settings() {
  global $context, $scripturl, $txt, $modSettings;

  // Javascript for the verification image.
  if ($context['use_graphic_library'])
  {
  echo '
  <script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[
    function refreshImages()
    {
      var imageType = document.getElementById(\'visual_verification_type_select\').value;
      document.getElementById(\'verificiation_image\').src = \'', $context['verificiation_image_href'], ';type=\' + imageType;
    }
  // ]]></script>';
  }

  echo '
  <form action="', $scripturl, '?action=regcenter" method="post" accept-charset="', $context['character_set'], '">
    <table border="0" cellspacing="1" cellpadding="4" align="center" width="100%" class="tborder">
      <tr class="titlebg">
        <td align="center">', $txt['settings'], '</td>
      </tr>
      <tr class="windowbg2">
        <td align="center">';

  // Functions to do some nice box disabling dependant on age restrictions.
  echo '
          <script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[
            function checkCoppa()
            {
              var coppaDisabled = document.getElementById(\'coppaAge_input\').value == 0;
              document.getElementById(\'coppaType_select\').disabled = coppaDisabled;

              var disableContacts = coppaDisabled || document.getElementById(\'coppaType_select\').options[document.getElementById(\'coppaType_select\').selectedIndex].value != 1;
              document.getElementById(\'coppaPost_input\').disabled = disableContacts;
              document.getElementById(\'coppaFax_input\').disabled = disableContacts;
              document.getElementById(\'coppaPhone_input\').disabled = disableContacts;
            }
          // ]]></script>';
  echo '
          <table border="0" cellspacing="0" cellpadding="4" align="center" width="100%">
            <tr class="windowbg2">
              <th width="50%" align="right">
                <label for="registration_method_select">', $txt['admin_setting_registration_method'], '</label> <span style="font-weight: normal;">(<a href="', $scripturl, '?action=helpadmin;help=registration_method" onclick="return reqWin(this.href);">?</a>)</span>:
              </th>
              <td width="50%" align="left">
                <select name="registration_method" id="registration_method_select">
                  <option value="0"', empty($modSettings['registration_method']) ? ' selected="selected"' : '', '>', $txt['admin_setting_registration_standard'], '</option>
                  <option value="1"', !empty($modSettings['registration_method']) && $modSettings['registration_method'] == 1 ? ' selected="selected"' : '', '>', $txt['admin_setting_registration_activate'], '</option>
                  <option value="2"', !empty($modSettings['registration_method']) && $modSettings['registration_method'] == 2 ? ' selected="selected"' : '', '>', $txt['admin_setting_registration_approval'], '</option>
                  <option value="3"', !empty($modSettings['registration_method']) && $modSettings['registration_method'] == 3 ? ' selected="selected"' : '', '>', $txt['admin_setting_registration_disabled'], '</option>
                </select>
              </td>
            </tr>
            <tr class="windowbg2">
              <th width="50%" align="right">
                <label for="notify_new_registration_check">', $txt['admin_setting_notify_new_registration'], '</label>:
              </th>
              <td width="50%" align="left">
                <input type="checkbox" name="notify_new_registration" id="notify_new_registration_check" ', !empty($modSettings['notify_new_registration']) ? 'checked="checked"' : '', ' class="check" />
              </td>
            </tr><tr class="windowbg2">
              <th width="50%" align="right">
                <label for="send_welcomeEmail_check">', $txt['admin_setting_send_welcomeEmail'], '</label> <span style="font-weight: normal;">(<a href="', $scripturl, '?action=helpadmin;help=send_welcomeEmail" onclick="return reqWin(this.href);">?</a>)</span>:
              </th>
              <td width="50%" align="left">
                <input type="checkbox" name="send_welcomeEmail" id="send_welcomeEmail_check"', !empty($modSettings['send_welcomeEmail']) ? ' checked="checked"' : '', ' class="check" />
              </td>
            </tr><tr class="windowbg2">
              <th width="50%" align="right">
                <label for="password_strength_select">', $txt['admin_setting_password_strength'], '</label> <span style="font-weight: normal;">(<a href="', $scripturl, '?action=helpadmin;help=password_strength" onclick="return reqWin(this.href);">?</a>)</span>:
              </th>
              <td width="50%" align="left">
                <select name="password_strength" id="password_strength_select">
                  <option value="0"', empty($modSettings['password_strength']) ? ' selected="selected"' : '', '>', $txt['admin_setting_password_strength_low'], '</option>
                  <option value="1"', !empty($modSettings['password_strength']) && $modSettings['password_strength'] == 1 ? ' selected="selected"' : '', '>', $txt['admin_setting_password_strength_medium'], '</option>
                  <option value="2"', !empty($modSettings['password_strength']) && $modSettings['password_strength'] == 2 ? ' selected="selected"' : '', '>', $txt['admin_setting_password_strength_high'], '</option>
                </select>
              </td>
            </tr><tr class="windowbg2" valign="top">
              <th width="50%" align="right">
                <label for="visual_verification_type_select">
                  ', $txt['admin_setting_image_verification_type'], ':<br />
                  <span class="smalltext" style="font-weight: normal;">
                    ', $txt['admin_setting_image_verification_type_desc'], '
                  </span>
                </label>
              </th>
              <td width="50%" align="left">
                <select name="visual_verification_type" id="visual_verification_type_select" ', $context['use_graphic_library'] ? 'onchange="refreshImages();"' : '', '>
                  <option value="1" ', !empty($modSettings['disable_visual_verification']) && $modSettings['disable_visual_verification'] == 1 ? 'selected="selected"' : '', '>', $txt['admin_setting_image_verification_off'], '</option>
                  <option value="2" ', !empty($modSettings['disable_visual_verification']) && $modSettings['disable_visual_verification'] == 2 ? 'selected="selected"' : '', '>', $txt['admin_setting_image_verification_vsimple'], '</option>
                  <option value="3" ', !empty($modSettings['disable_visual_verification']) && $modSettings['disable_visual_verification'] == 3 ? 'selected="selected"' : '', '>', $txt['admin_setting_image_verification_simple'], '</option>
                  <option value="0" ', empty($modSettings['disable_visual_verification']) ? 'selected="selected"' : '', '>', $txt['admin_setting_image_verification_medium'], '</option>
                  <option value="4" ', !empty($modSettings['disable_visual_verification']) && $modSettings['disable_visual_verification'] == 4 ? 'selected="selected"' : '', '>', $txt['admin_setting_image_verification_high'], '</option>
                </select><br />';
  if ($context['use_graphic_library'])
    echo '
                <img src="', $context['verificiation_image_href'], ';type=', empty($modSettings['disable_visual_verification']) ? 0 : $modSettings['disable_visual_verification'], '" alt="', $txt['admin_setting_image_verification_sample'], '" id="verificiation_image" /><br />';
  else
  {
    echo '
                <span class="smalltext">', $txt['admin_setting_image_verification_nogd'], '</span>';
  }
  echo '
              </td>
            </tr><tr class="windowbg2">
              <td width="100%" colspan="2" align="center">
                <hr />
              </td>

            <tr class="windowbg2">
              <th width="50%" align="right">
                <label for="recaptchaEnable_check">', $txt['recaptcha_enabled'], '</label>:
              </th>
              <td width="50%" align="left">
                <input type="checkbox" name="recaptchaEnable" id="recaptchaEnable_check" ', !empty($modSettings['recaptcha_enabled']) ? 'checked="checked"' : '', ' class="check" />
              </td>
            </tr><tr class="windowbg2">
              <th width="50%" align="right">
                <label for="recaptchaTheme">', $txt['recaptcha_theme'], '</label>:
              </th>
              <td width="50%" align="left">
                <select name="recaptchaTheme" id="recaptcha_theme_select">
                  <option value="clean"', empty($modSettings['recaptcha_theme']) ? ' selected="selected"' : '', '>', $txt['recaptcha_theme_clean'], '</option>
                  <option value="blackglass"', !empty($modSettings['recaptcha_theme']) && $modSettings['recaptcha_theme'] == "blackglass" ? ' selected="selected"' : '', '>', $txt['recaptcha_theme_blackglass'], '</option>
                  <option value="red"', !empty($modSettings['recaptcha_theme']) && $modSettings['recaptcha_theme'] == "red" ? ' selected="selected"' : '', '>', $txt['recaptcha_theme_red'], '</option>
                  <option value="white"', !empty($modSettings['recaptcha_theme']) && $modSettings['recaptcha_theme'] == "white" ? ' selected="selected"' : '', '>', $txt['recaptcha_theme_white'], '</option>
                </select>
              </td>
            </tr><tr class="windowbg2" valign="top">
              <th width="50%" align="right">
                <label for="recaptchaPublicKey_input">', $txt['recaptcha_public_key'], '</label>:
              </th>
              <td width="50%" align="left">
                <input type="text" name="recaptchaPublicKey" id="recaptchaPublicKey_input" value="', !empty($modSettings['recaptcha_public_key']) ? $modSettings['recaptcha_public_key'] : '', '" size="40" maxlength="40" />
              </td>
            </tr><tr class="windowbg2" valign="top">
              <th width="50%" align="right">
                <label for="recaptchaPrivateKey_input">', $txt['recaptcha_private_key'], '</label>:
              </th>
              <td width="50%" align="left">
                <input type="text" name="recaptchaPrivateKey" id="recaptchaPrivateKey_input" value="', !empty($modSettings['recaptcha_private_key']) ? $modSettings['recaptcha_private_key'] : '', '" size="40" maxlength="40" />
              </td>
            </tr><tr class="windowbg2" valign="top">
              <td colspan = "2" align="center">',
                $txt['recaptcha_no_key_question'],' <a href="http://recaptcha.net/api/getkey?app=recaptcha_for_smf&amp;domain=', $_SERVER['SERVER_NAME'], '">', $txt['recaptcha_get_key'], '</a>
              </td>
            </tr><tr class="windowbg2">
              <td width="100%" colspan="2" align="center">
                <hr />
              </td>
            </tr><tr class="windowbg2" valign="top">
              <th width="50%" align="right">
                <label for="coppaAge_input">', $txt['admin_setting_coppaAge'], '</label> <span style="font-weight: normal;">(<a href="', $scripturl, '?action=helpadmin;help=coppaAge" onclick="return reqWin(this.href);">?</a>)</span>:
                <div class="smalltext" style="font-weight: normal;">', $txt['admin_setting_coppaAge_desc'], '</div>
              </th>
              <td width="50%" align="left">
                <input type="text" name="coppaAge" id="coppaAge_input" value="', !empty($modSettings['coppaAge']) ? $modSettings['coppaAge'] : '', '" size="3" maxlength="3" onkeyup="checkCoppa();" />
              </td>
            </tr><tr class="windowbg2" valign="top">
              <th width="50%" align="right">
                <label for="coppaType_select">', $txt['admin_setting_coppaType'], '</label> <span style="font-weight: normal;">(<a href="', $scripturl, '?action=helpadmin;help=coppaType" onclick="return reqWin(this.href);">?</a>)</span>:
              </th>
              <td width="50%" align="left">
                <select name="coppaType" id="coppaType_select" onchange="checkCoppa();">
                  <option value="0"', empty($modSettings['coppaType']) ? ' selected="selected"' : '', '>', $txt['admin_setting_coppaType_reject'], '</option>
                  <option value="1"', !empty($modSettings['coppaType']) && $modSettings['coppaType'] == 1 ? ' selected="selected"' : '', '>', $txt['admin_setting_coppaType_approval'], '</option>
                </select>
              </td>
            </tr><tr class="windowbg2" valign="top">
              <th width="50%" align="right">
                <label for="coppaPost_input">', $txt['admin_setting_coppaPost'], '</label> <span style="font-weight: normal;">(<a href="', $scripturl, '?action=helpadmin;help=coppaPost" onclick="return reqWin(this.href);">?</a>)</span>:
                <div class="smalltext" style="font-weight: normal;">', $txt['admin_setting_coppaPost_desc'], '</div>
              </th>
              <td width="50%" align="left">
                <textarea name="coppaPost" id="coppaPost_input" rows="4" cols="35">', $context['coppaPost'], '</textarea>
              </td>
            </tr><tr class="windowbg2" valign="top">
              <th width="50%" align="right">
                <label for="coppaFax_input">', $txt['admin_setting_coppaFax'], '</label> <span style="font-weight: normal;">(<a href="', $scripturl, '?action=helpadmin;help=coppaPost" onclick="return reqWin(this.href);">?</a>)</span>:
                <div class="smalltext" style="font-weight: normal;">', $txt['admin_setting_coppaPost_desc'], '</div>
              </th>
              <td width="50%" align="left">
                <input type="text" name="coppaFax" id="coppaFax_input" value="', !empty($modSettings['coppaFax']) ? $modSettings['coppaFax'] : '', '" size="22" maxlength="35" />
              </td>
            </tr><tr class="windowbg2" valign="top">
              <th width="50%" align="right">
                <label for="coppaPhone_input">', $txt['admin_setting_coppaPhone'], '</label> <span style="font-weight: normal;">(<a href="', $scripturl, '?action=helpadmin;help=coppaPost" onclick="return reqWin(this.href);">?</a>)</span>:
                <div class="smalltext" style="font-weight: normal;">', $txt['admin_setting_coppaPost_desc'], '</div>
              </th>
              <td width="50%" align="left">
                <input type="text" name="coppaPhone" id="coppaPhone_input" value="', !empty($modSettings['coppaPhone']) ? $modSettings['coppaPhone'] : '', '" size="22" maxlength="35" />
              </td>
            </tr><tr class="windowbg2">
              <td width="100%" colspan="3" align="right">
                <input type="submit" name="save" value="', $txt[10], '" />
                <input type="hidden" name="sa" value="settings" />
              </td>
            </tr>
          </table>';

  // Handle disabling of some of the input boxes.
  echo '
          <script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[';

  if (empty($modSettings['coppaAge']) || empty($modSettings['coppaType']))
    echo '
            document.getElementById(\'coppaPost_input\').disabled = true;
            document.getElementById(\'coppaFax_input\').disabled = true;
            document.getElementById(\'coppaPhone_input\').disabled = true;';
  if (empty($modSettings['coppaAge']))
    echo '
            document.getElementById(\'coppaType_select\').disabled = true;';

  echo '
          // ]]></script>
        </td>
      </tr>
    </table>
    <input type="hidden" name="sc" value="' . $context['session_id'] . '" />
  </form>';
}

?>