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

function template_main() {
  global $context, $settings, $txt, $modSettings, $boardurl;
  
  echo '
    <script language="JavaScript" type="text/javascript">
      function showr_email(comment) {
        if(comment == \'\') {
          alert(\'No has escrito ningun mensaje.\');
          return false;
        }
      }
    </script>
    <div>
      <div class="box_buscador">
        <div class="box_title" style="width: 921px;">
          <div class="box_txt box_buscadort">
            <center>Recomendar a tus amigos</center>
          </div>
          <div class="box_rss">
            <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 14px; height:12px;" border="0" />
          </div>
        </div>
        <div style="width: 911px; padding: 4px;" class="windowbg">
          <center>
            <form action="' . $boardurl . '/enviar-a-amigo/enviando-post/' . $_GET['topic'] . '" method="post" accept-charset="' . $context['character_set'] . '">
              <br />
              <font class="size11">
                <b>Recomendarle este post hasta a seis amigos:</b>
              </font>
              <br />
              <b class="size11">1 - </b>
              <input type="text" onfocus="foco(this);" onblur="no_foco(this);" name="r_email" size="28" maxlength="60" />
              <b class="size11">2 - </b>
              <input type="text" onfocus="foco(this);" onblur="no_foco(this);" name="r_email1" size="28" maxlength="60" />
              <br />
              <b class="size11">3 - </b>
              <input type="text" onfocus="foco(this);" onblur="no_foco(this);" name="r_email2" size="28" maxlength="60" />
              <b class="size11">4 - </b>
              <input type="text" onfocus="foco(this);" onblur="no_foco(this);" name="r_email3" size="28" maxlength="60" />
              <br />
              <b class="size11">5 - </b>
              <input type="text" onfocus="foco(this);" onblur="no_foco(this);" name="r_email4" size="28" maxlength="60" />
              <b class="size11">6 - </b>
              <input type="text" onfocus="foco(this);" onblur="no_foco(this);" name="r_email5" size="28" maxlength="60" />
              <br /><br />
              <font class="size11">
                <b>Asunto:</b>
              </font>
              <br />
              <input size="40" name="titulo" value="' . $context['page_title'] . '" type="text" onfocus="foco(this);" onblur="no_foco(this);" />
              <br /><br />
              <font class="size11">
                <b>Mensaje:</b>
              </font>
              <br />
              <textarea onfocus="foco(this);" onblur="no_foco(this);" cols="70" rows="8" wrap="hard" tabindex="6" name="comment">' . $txt['sendtopic_comment'] . '</textarea>
              <br /><br />
              <font class="size11">
                <b>C&oacute;digo de la im&aacute;gen:</b>
              </font>
              <br />
              <script type="text/javascript">
                var RecaptchaOptions = {
                  theme : \'' . empty($modSettings['recaptcha_theme']) ? 'clean' : $modSettings['recaptcha_theme']  . '\',
                };
              </script>
              <script type="text/javascript" src="http://api.recaptcha.net/challenge?k=' . $modSettings['recaptcha_public_key'] . '"></script>
              <noscript>
                <iframe src="http://api.recaptcha.net/noscript?k=' . $modSettings['recaptcha_public_key'] . '" frameborder="0"></iframe>
                <br />
                <textarea name="recaptcha_challenge_field" rows="2" cols="10"></textarea>
                <input type="hidden" name="recaptcha_response_field" value="manual_challenge" />
              </noscript>
              <br />
              <input onclick="return showr_email(this.form.comment.value);" type="submit" class="login" name="send" value="Recomendar post" />
              <br />
              <a href="', $_SERVER['HTTP_REFERER'], '" title="&#171; Volver al post" target="_self">&#171; Volver al post</a>
            </form>
          </center>
        </div>
      </div>
    </div>
    <div style="clear:both"></div>';
}

?>