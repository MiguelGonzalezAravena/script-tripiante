<?php
function template_manual_above() {}

function template_manual_below() {}

function template_manual_intro() {
   global $context, $settings, $options, $txt, $scripturl, $modSettings, $boardurl;

   echo '
      <div id="cuerpocontainer">
        <div class="box_buscador">
          <div class="box_title" style="width: 920px;">
            <div class="box_txt box_buscadort">
              <center>' . $txt['protocol']  .'</center>
            </div>
            <div class="box_rss">
              <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 14px; height: 12px;" border="0" />
            </div>
          </div>
          <div class="windowbg" style="width: 912px; padding: 4px;">
            <div class="codePro">
              <b>' . $txt['introduction']  .':</b>
              <div class="codePro1">
                <b>' . $context['forum_name'] . '</b>
                &nbsp;
                ' . $txt['intro2'] . '
                <b>' . $context['forum_name'] . '</b>
                &nbsp;
                ' . $txt['intro2-2'] . '.
              </div>
            </div>
            <div class="codePro">
              <b>' . $txt['protocol'] . ':</b>
              <div class="codePro1">
                <span class="size12" align="left">
                  <p style="margin: 0px; padding: 0px; padding-left: 10px;">
                    <img src="' . $settings['images_url'] . '/icons/bullet-verde.gif" alt="" />
                    &nbsp;
                    <b>' . $txt['features_to_post'] . ':</b>
                  </p>
                  <p style="margin: 0px; padding: 0px;">
                    <img src="' . $settings['images_url'] . '/icons/bullet-rojo.gif" alt="" />
                    &nbsp;
                    ' . $txt['rule1'] . '.
                  </p>

                  <p style="margin: 0px; padding: 0px;">
                    <img src="' . $settings['images_url'] . '/icons/bullet-rojo.gif" alt="" />
                    &nbsp;
                    ' . $txt['rule3'] . '.
                  </p>
                  <p style="margin: 0px; padding: 0px;">
                    <img src="' . $settings['images_url'] . '/icons/bullet-rojo.gif" alt="" />
                    &nbsp;
                    ' . $txt['rule4'] . '.
                  </p>
                  <p style="margin: 0px; padding: 0px;">
                    <img src="' . $settings['images_url'] . '/icons/bullet-rojo.gif" alt="" />
                    &nbsp;
                    ' . $txt['rule5'] . '.
                  </p>
                  <p style="margin: 0px; padding: 0px;">
                    <img src="' . $settings['images_url'] . '/icons/bullet-rojo.gif" alt="" />
                    &nbsp;
                    ' . $txt['rule6'] . ' 
                    (' . $context['forum_name'] . ' ' . $txt['rule6-2'] . '.)
                  </p>
                  <p style="margin: 0px; padding: 0px;">' . $txt['rule6-3'] . '</p>
                  <br />

                  <p style="margin: 0px; padding: 0px; padding-left: 10px;">
                    <img src="', $settings['images_url'], '/icons/bullet-verde.gif" alt="" />
                    &nbsp;
                    <b>' . $txt['eliminating_post'] . ':</b>
                  </p>
                  <p style="margin: 0px; padding: 0px;">
                    <img src="', $settings['images_url'], '/icons/bullet-rojo.gif" alt="" />
                    &nbsp;
                    ', $txt['rule7'] ,'.
                  </p>
                  <p style="margin: 0px; padding: 0px;">
                    <img src="', $settings['images_url'], '/icons/bullet-rojo.gif" alt="" />
                    &nbsp;
                    ', $txt['rule8'] ,'.
                  </p>
                  <p style="margin: 0px; padding: 0px;">
                    <img src="', $settings['images_url'], '/icons/bullet-rojo.gif" alt="" />
                    &nbsp;
                    ', $txt['rule9'] ,'.
                  </p>
                  <p style="margin: 0px; padding: 0px;">
                    <img src="', $settings['images_url'], '/icons/bullet-rojo.gif" alt="" />
                    &nbsp;
                    ', $txt['rule10'] ,'.
                  </p>
                  <p style="margin: 0px; padding: 0px;">
                    <img src="', $settings['images_url'], '/icons/bullet-rojo.gif" alt="" />
                    &nbsp;
                    ', $txt['rule11'] ,'.
                  </p>
                  <p style="margin: 0px; padding: 0px;">
                    <img src="', $settings['images_url'], '/icons/bullet-rojo.gif" alt="" />
                    &nbsp;
                    ', $txt['rule12'] ,'.
                  </p>
                  <p style="margin: 0px; padding: 0px;">
                    <img src="', $settings['images_url'], '/icons/bullet-rojo.gif" alt="" />
                    &nbsp;
                    ', $txt['rule14'] ,'.
                  </p>
                  <br />
                  <p style="margin: 0px; padding: 0px; padding-left: 10px;">
                    <img src="', $settings['images_url'], '/icons/bullet-verde.gif" alt="" />
                    &nbsp;
                    <b> ', $txt['amending_post'] ,':</b>
                  </p>
                  <p style="margin: 0px; padding: 0px;">
                    <img src="', $settings['images_url'], '/icons/bullet-rojo.gif" alt="" />
                    &nbsp;
                    ', $txt['rule15'] ,'.
                  </p>
                  <p style="margin: 0px; padding: 0px;">
                    <img src="', $settings['images_url'], '/icons/bullet-rojo.gif" alt="" />
                    &nbsp;
                    ', $txt['rule16'] ,'.
                  </p>
                  <p style="margin: 0px; padding: 0px;">
                    <img src="', $settings['images_url'], '/icons/bullet-rojo.gif" alt="" />
                    &nbsp;
                    ', $txt['rule17'] ,'.
                  </p>
                  <br />
                  <p style="margin: 0px; padding: 0px; padding-left: 10px;">
                    <img src="', $settings['images_url'], '/icons/bullet-verde.gif" alt="" />
                    &nbsp;
                    <b> ', $txt['comments_are_eliminated'] ,':</b>
                  </p>
                  <p style="margin: 0px; padding: 0px;">
                    <img src="', $settings['images_url'], '/icons/bullet-rojo.gif" alt="" />
                    &nbsp;
                    ', $txt['rule18'] ,'.
                  </p>
                  <p style="margin: 0px; padding: 0px;">
                    <img src="', $settings['images_url'], '/icons/bullet-rojo.gif" alt="" />
                    &nbsp;
                    ', $txt['rule19'] ,'.
                  </p>
                  <p style="margin: 0px; padding: 0px;">
                    <img src="', $settings['images_url'], '/icons/bullet-rojo.gif" alt="" />
                    &nbsp;
                    ', $txt['rule20'] ,'.
                  </p>
                  <p style="margin: 0px; padding: 0px;">
                    <img src="', $settings['images_url'], '/icons/bullet-rojo.gif" alt="" />
                    &nbsp;
                    ', $txt['rule21'] ,'.
                  </p>
                  <p style="margin: 0px; padding: 0px;">
                    <img src="', $settings['images_url'], '/icons/bullet-rojo.gif" alt="" />
                    &nbsp;
                    ', $txt['rule22'] ,'.
                  </p>
                  <br />
                  <p style="margin: 0px; padding: 0px; padding-left: 10px;">
                    <img src="', $settings['images_url'], '/icons/bullet-verde.gif" alt="" />
                    &nbsp;
                    <b> ', $txt['was_suspended_users'] ,':</b>
                  </p>
                  <p style="margin: 0px; padding: 0px;">
                    <img src="', $settings['images_url'], '/icons/bullet-rojo.gif" alt="" />
                    &nbsp;
                    ', $txt['rule23'] ,'.
                  </p>
                  <p style="margin: 0px; padding: 0px;">
                    <img src="', $settings['images_url'], '/icons/bullet-rojo.gif" alt="" />
                    &nbsp;
                    ', $txt['rule24'] ,'
                  </p>
                  <p style="margin: 0px; padding: 0px;">
                    <img src="', $settings['images_url'], '/icons/bullet-rojo.gif" alt="" />
                    &nbsp;
                    ', $txt['rule25'] ,'
                    &nbsp;
                    <a href="', $boardurl ,'/contactanos/" target="_blank" title="Contactar">', $txt['rule25-2'] ,'</a>
                    &nbsp;
                    ', $txt['rule25-3'] ,'.
                  </p>
                  <p style="margin: 0px; padding: 0px;">
                    <img src="', $settings['images_url'], '/icons/bullet-rojo.gif" alt="" />
                    &nbsp;
                    ', $txt['rule26'] ,'.
                  </p>
                  <br />
                  <p style="margin: 0px; padding: 0px; padding-left: 10px;">
                    <img src="', $settings['images_url'], '/icons/bullet-verde.gif" alt="" />
                    &nbsp;
                    <b> ', $txt['eliminate_or_modify_images'] ,':</b>
                  </p>
                  <p style="margin: 0px; padding: 0px;">
                    <img src="', $settings['images_url'], '/icons/bullet-rojo.gif" alt="" />
                    &nbsp;
                    ', $txt['rule27'] ,'.
                  </p>
                  <p style="margin: 0px; padding: 0px;">
                    <img src="', $settings['images_url'], '/icons/bullet-rojo.gif" alt="" />
                    &nbsp;
                    ', $txt['rule28'] ,'.
                  </p>

                  <p style="margin: 0px; padding: 0px;">
                    <img src="', $settings['images_url'], '/icons/bullet-rojo.gif" alt="" />
                    &nbsp;
                    ', $txt['rule29'] ,'.
                  </p>
                  <p style="margin: 0px; padding: 0px;">
                    <img src="', $settings['images_url'], '/icons/bullet-rojo.gif" alt="" />
                    &nbsp;
                    ', $txt['rule30'] ,'.
                  </p>
                  <br />
                  <p style="margin: 0px; padding: 0px; padding-left: 10px;">
                    <img src="', $settings['images_url'], '/icons/bullet-verde.gif" alt="" />
                    &nbsp;
                    <b>', $txt['to_create_c'] ,':</b>
                  </p>
                  <p style="margin: 0px; padding: 0px;">
                    <img src="', $settings['images_url'], '/icons/bullet-rojo.gif" alt="" />
                    &nbsp;
                    ', $txt['rule31'] ,'.
                  </p>
                  <p style="margin: 0px; padding: 0px;">
                    <img src="', $settings['images_url'], '/icons/bullet-rojo.gif" alt="" />
                    &nbsp;
                    ', $txt['rule32'] ,'.
                  </p>
                  <p style="margin: 0px; padding: 0px;">
                    <img src="', $settings['images_url'], '/icons/bullet-rojo.gif" alt="" />
                    &nbsp;
                    ', $txt['rule33'] ,'.
                  </p>
                  <p style="margin: 0px; padding: 0px;">
                    <img src="', $settings['images_url'], '/icons/bullet-rojo.gif" alt="" />
                    &nbsp;
                    ', $txt['rule34'] ,'.
                  </p>
                  <p style="margin: 0px; padding: 0px;">', $txt['rule34-2'] ,'</p>
                  <br />
                  <p style="margin: 0px; padding: 0px; padding-left: 10px;">
                    <img src="', $settings['images_url'], '/icons/bullet-verde.gif" alt="" />
                    &nbsp;
                    <b> ', $txt['are_delette_c'] ,':</b>
                  </p>
                  <p style="margin: 0px; padding: 0px;">
                    <img src="', $settings['images_url'], '/icons/bullet-rojo.gif" alt="" />
                    &nbsp;
                    ', $txt['rule35'] ,'.
                  </p>
                  <p style="margin: 0px; padding: 0px;">
                    <img src="', $settings['images_url'], '/icons/bullet-rojo.gif" alt="" />
                    &nbsp;
                    ', $txt['rule36'] ,'.
                  </p>
                  <p style="margin: 0px; padding: 0px;">
                    <img src="', $settings['images_url'], '/icons/bullet-rojo.gif" alt="" />
                    &nbsp;
                    ', $txt['rule37'] ,'.
                  </p>
                  <p style="margin: 0px; padding: 0px;">
                    <img src="', $settings['images_url'], '/icons/bullet-rojo.gif" alt="" />
                    &nbsp;
                    ', $txt['rule38'] ,'
                  </p>
                  <p style="margin: 0px; padding: 0px;">
                    <img src="', $settings['images_url'], '/icons/bullet-rojo.gif" alt="" />
                    &nbsp;
                    ', $txt['rule39'] ,'.
                  </p>
                  <div style="clear: both;"></div>
                </span>
              </div>
            </div>';
}

?>