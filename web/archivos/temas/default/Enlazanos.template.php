<?php
function template_manual_above() {}
function template_manual_below() {}

function template_manual_intro() {
  global $settings, $txt, $mbname, $boardurl;

  echo '
    <div id="cuerpocontainer">
      <div class="box_buscador"
        <div class="box_title" style="width: 921px;">
          <div class="box_txt box_buscadort">
            <center>' . $txt['enlazanos'] . '</center>
          </div>
          <div class="box_rss">
            <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 14px; height: 12px;" border="0">
          </div>
        </div>
        <div class="windowbg" style="width: 911px; padding: 4px;">
          <table style="border-bottom:1px solid #B3A496;">
            <tr>
              <td style="width: 125px; height: 62px; margin-top: 25px;">
                <center>
                  <a title="' . $mbname . '" href="' . $boardurl . '/">
                    <img src="' . $boardurl . '/web/enlazanos/16x16.gif" alt="' . $mbname . '" width="16" border="0" height="16" />
                  </a>
                </center>
              </td>
              <td style="width: 772px; height: 62px;">
                <textarea readonly="readonly" onmouseup="this.focus(); this.select()" wrap="off" style="border: 1px dashed rgb(192, 192, 192); background-color: rgb(249, 249, 249); width: 772px; height: 50px; font-family: arial; font-size: 11px;">
                  &lt;a title="' . $mbname . '" href="' . $boardurl . '/"&gt;
                  &lt;img src="' . $boardurl . '/web/enlazanos/16x16.gif" alt="' . $mbname . '" width="16" border="0" height="16" /&gt;
                  &lt;/a&gt;
                </textarea>
              </td>
            </tr>
          </table>
          <table style="border-bottom: 1px solid #B3A496;">
            <tr>
              <td style="width: 125px; height: 62px; margin-top: 25px;">
                <center>
                  <a title="' . $mbname . '" href="' . $boardurl . '/">
                    <img src="' . $boardurl . '/web/enlazanos/88x31.gif" alt="' . $mbname . '" width="88" border="0" height="31" />
                  </a>
                </center>
              </td>
              <td style="width: 772px; height: 62px;">
                <textarea readonly="readonly" onmouseup="this.focus(); this.select()" wrap="off" style="border: 1px dashed rgb(192, 192, 192); background-color: rgb(249, 249, 249); width: 772px; height: 50px; font-family: arial; font-size: 11px;">
                  &lt;a title="' . $mbname . '" href="' . $boardurl . '/"&gt;
                  &lt;img src="' . $boardurl . '/web/enlazanos/88x31.gif" alt="' . $mbname . '" width="88" border="0" height="31" /&gt;
                  &lt;/a&gt;
                </textarea>
              </td>
            </tr>
          </table>
          <table style="border-bottom: 1px solid #B3A496;">
            <tr>
              <td style="width: 125px; height: 62px; margin-top: 25px;">
                <center>
                  <a title="' . $mbname . '" href="' . $boardurl . '/"><img src="/web/enlazanos/100x20.gif" alt="' . $mbname . '" width="100" border="0" height="20"/></a>
                </center>
              </td>
              <td style="width: 772px; height: 62px;">
                <textarea readonly="readonly" onmouseup="this.focus(); this.select()" wrap="off" style="border: 1px dashed rgb(192, 192, 192); background-color: rgb(249, 249, 249); width: 772px; height: 50px; font-family: arial; font-size: 11px;">
                  &lt;a title="' . $mbname . '" href="' . $boardurl . '/"&gt;
                  &lt;img src="' . $boardurl . '/web/enlazanos/100x20.gif" alt="' . $mbname . '" width="100" border="0" height="20" /&gt;
                  &lt;/a&gt;
                </textarea>
              </td>
            </tr>
          </table>
          <table>
            <tr>
              <td style="width: 125px; height: 62px; margin-top: 25px;">
                <center>
                  <a title="' . $mbname . '" href="' . $boardurl . '/">
                    <img src="' . $boardurl . '/web/enlazanos/125x125.gif" alt="' . $mbname . '" width="125" border="0" height="125" />
                  </a>
                </center>
              </td>
              <td style="width: 772px; height: 62px;">
                <textarea readonly="readonly" onmouseup="this.focus(); this.select()" wrap="off" style="border: 1px dashed rgb(192, 192, 192); background-color: rgb(249, 249, 249); width: 772px; height: 50px; font-family: arial; font-size: 11px;">
                  &lt;a title="' . $mbname . '" href="' . $boardurl . '/"&gt;
                  &lt;img src="' . $boardurl . '/web/enlazanos/125x125.gif" alt="' . $mbname . '" width="125" border="0" height="125" /&gt;
                  &lt;/a&gt;
                </textarea>
              </td>
            </tr>
          </table>'; 
}

?>