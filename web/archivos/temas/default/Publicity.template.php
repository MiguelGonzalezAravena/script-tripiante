<?php
@require_once('SSI.php');

function template_main() {
  global $scripturl, $context, $settings, $modSettings;

  echo '
    <form action="' . $scripturl . '?action=publicity;m=guardar" method="post" accept-charset="' . $context['character_set'] . '" name="guardar" id="guardar">
      <div class="box_745" style="float: left;">
        <div class="box_title" style="width: 745px;">
          <div style="text-align: center;" class="box_txt">
            <center>' . $context['page_title'] . '</center>
          </div>
          <div class="box_rss">
            <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 16px; height: 16px;" border="0" />
          </div>
        </div>
        <div class="windowbg" style="width: 737px; padding: 4px;">
          <table width="100%" style="padding: 4px; border: none;">
            <tr>
              <td>Horizontal (728x90)</td>
              <td>
                <textarea name="horizontal" style="width: 100%; height: 200px;">' . $modSettings['horizontal'] . '</textarea>
              </td>
            </tr>
            <tr>
              <td>Horizontal 2 (468x60)</td>
              <td>
                <textarea name="horizontal2" style="width: 100%; height: 200px;">' . $modSettings['horizontal2'] . '</textarea>
              </td>
            </tr>
            <tr>
              <td>Vertical (120x600)</td>
              <td>
                <textarea name="vertical" style="width: 100%; height: 200px;">' . $modSettings['vertical'] . '</textarea>
              </td>
            </tr>
            <tr>
              <td>Vertical 2 (160x600)</td>
              <td>
                <textarea name="vertical2" style="width: 100%; height: 200px;">' . $modSettings['vertical2'] . '</textarea>
              </td>
            </tr>
            <tr>
              <td>Destacados 1 (300x250)</td>
              <td>
                <textarea name="Highlights1" style="width:100%; height: 200px;">' . $modSettings['Highlights1'] . '</textarea>
              </td>
            </tr>
            <tr>
              <td>Destacados 2 (300x250)</td>
              <td>
                <textarea name="Highlights2" style="width: 100%; height: 200px;">' . $modSettings['Highlights2'] . '</textarea>
              </td>
            </tr>
            <tr>
              <td>
                <input type="submit" name="Guardar publicidad" value="Guardar publicidad" class="login" />
              </td>
            </tr>
          </table>
        </div>
      </div>
    </form>
    <div style="clear:both"></div>';
}

function template_guardar() {
  global $db_prefix;

  $horizontal = htmlentities(addslashes($_POST['horizontal']), ENT_QUOTES, 'UTF-8');
  $horizontal2 = htmlentities(addslashes($_POST['horizontal2']), ENT_QUOTES, 'UTF-8');
  $vertical = htmlentities(addslashes($_POST['vertical']), ENT_QUOTES, 'UTF-8');
  $vertical2 = htmlentities(addslashes($_POST['vertical']), ENT_QUOTES, 'UTF-8');
  $highlights1 = htmlentities(addslashes($_POST['Highlights1']), ENT_QUOTES, 'UTF-8');
  $highlights2 = htmlentities(addslashes($_POST['Highlights2']), ENT_QUOTES, 'UTF-8');

  db_query("UPDATE {$db_prefix}settings SET value = '$horizontal' WHERE variable = 'horizontal'", __FILE__, __LINE__);
  db_query("UPDATE {$db_prefix}settings SET value = '$horizontal2' WHERE variable = 'horizontal2'", __FILE__, __LINE__);
  db_query("UPDATE {$db_prefix}settings SET value = '$vertical' WHERE variable = 'vertical'", __FILE__, __LINE__);
  db_query("UPDATE {$db_prefix}settings SET value = '$vertical2' WHERE variable = 'vertical2'", __FILE__, __LINE__);
  db_query("UPDATE {$db_prefix}settings SET value = '$highlights1' WHERE variable = 'Highlights1'", __FILE__, __LINE__);
  db_query("UPDATE {$db_prefix}settings SET value = '$highlights2' WHERE variable = 'Highlights2'", __FILE__, __LINE__);

  redirectexit('action=publicity');
}

?>