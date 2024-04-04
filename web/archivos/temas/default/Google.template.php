<?php

function template_manual_above() { }
function template_manual_below() { }

function template_manual_intro() {
  global $context, $settings, $boardurl;

  echo '
        <script type="text/javascript">
          function errorr(q) {
            if (q == \'\') {
              document.getElementById(\'errorss\').innerHTML = \'<br /><font class="size10" style="color: red;">Es necesario escribir una palabra para buscar.</font>\';
              return false;
            }
          }
        </script>
        <div>
          <div class="box_buscador">
            <div class="box_title" style="width: 920px;">
              <div class="box_txt box_buscadort">
                <center>Buscador</center>
              </div>
              <div class="box_rss">
                <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 14px; height: 12px;" border="0" />
              </div>
            </div>
            <div style="width: 912px; padding: 4px;" class="windowbg">
              <center>
                <br />
                <form action="' . $boardurl . '/google-search/" id="cse-search-box">
                  <b>Buscar:</b>
                  <input type="hidden" name="cof" value="FORID:10" />
                  <input type="hidden" name="cx" value="008407595988527806565:gpsb5aspgg8" />
                  <input type="hidden" name="ie" value="' . $context['character_set'] . '" />
                  <input type="text" name="q" size="50" />
                  <input type="submit"  onclick="return errorr(this.form.q.value);" style="font-size: 15px; width: 200px;" class="login" value="Buscar" name="sa"/>
                  <label id="errorss"></label>
                  <br />
                  <br />
                  <div class="cse-branding-logo">
                    <img align="absmiddle" alt="Google" src="http://www.google.com/images/poweredby_transparent/poweredby_EEEEEE.gif"/>
                    &nbsp;
                    B&uacute;squeda personalizada
                  </div>
                </form>
              </center>
            </div>
          </div>
        </div>
        <div>
          <div class="box_buscador">
            <div class="box_title" style="width: 920px;">
              <div class="box_txt box_buscadort">
                <center>Resultados de Google</center>
              </div>
            <div class="box_rss">
              <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 14px; height: 12px;" border="0" />
            </div>
          </div>
          <div style="width: 912px; padding: 4px;" class="windowbg">
            <center>
              <div id="resultados"></div>
              <script type="text/javascript">
                var googleSearchIframeName = "resultados";
                var googleSearchFormName = "cse-search-box";
                var googleSearchFrameWidth = 911;
                var googleSearchDomain = "www.google.com";
                var googleSearchPath = "/cse";
              </script>
              <script type="text/javascript" src="http://www.google.com/afsonline/show_afs_search.js"></script>
            </center>
          </div>
        </div>
      </div>
      <div style="clear:both"></div>
    </div>'; 
}

?>