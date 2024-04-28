<?php
include_once('SSI.php');

function template_main() {
  global $context, $settings, $boardurl;

  // Comentarios en mis posts
  echo '
    <div>
      <div style="float:left;width:708px;">
        <div class="box_r_buscador" style="margin-right: 8px; margin-botton: 8px;">
          <div class="box_title" style="width: 698px;">
            <div class="box_txt box_r_buscadort">
              <center>Comentarios en mis posts</center>
            </div>
            <div class="box_rss">
              <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 14px; height: 12px;" border="0" />
            </div>
          </div>
          <div class="windowbg" style="width: 690px; padding: 4px;">
            <table>';

  if (!empty($context['monitorcom'])) {
    foreach ($context['monitorcom'] as $row) {
      echo '
        <tr>
          <td valign="top" width="16">
            <img alt="" src="' . $settings['images_url'] . '/post/icono_' . $row['ID_BOARD'] . '.gif" title="' . $row['bname'] . '" />
          </td>
          <td>
            <b class="size11">
              <a title="' . $row['full_title'] . '" alt="' . $row['full_title'] . '" href="' . $boardurl . '/post/' . $row['id'] . '/' . $row['description'] . '/' . ssi_amigable($row['full_title']) . '.html">' . $row['short_title'] . '</a>
            </b>
            <div class="size11">
              ' . timeformat($row['posterTime']) . ':
              <a href="' . $boardurl . '/post/' . $row['id'] . '/' . $row['description'] . '/' . ssi_amigable($row['titulo']) . '.html#cmt_' . $row['ID_COMMENT'] . '">' . $row['comment'] . '</a>
            </div>
          </td>
        </tr>';
    }
  } else {
    echo '<div class="noesta">Nada por ac&aacute;...</div>';
  }

  echo '
        </table>
      </div>
    </div>';

  // Comentarios en mis imágenes
  echo '
    <div class="box_r_buscador" style="margin-right: 8px;">
      <div class="box_title" style="width: 698px;">
        <div class="box_txt box_r_buscadort">
          <center>Comentarios en mis im&aacute;genes</center>
        </div>
        <div class="box_rss">
          <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 14px; height: 12px;" border="0" />
        </div>
      </div>
      <div class="windowbg" style="width: 690px; padding: 4px;">
        <table>';

  if (!empty($context['monitorimg'])) {
    foreach ($context['monitorimg'] as $row) {
      echo '
        <tr>
          <td valign="top">
            <span class="icons fot2">
              <b class="size11">
                <a title="' . $row['full_title'] . '" alt="' . $row['full_title'] . '" href="' . $boardurl . '/imagenes/ver/' . $row['ID_PICTURE'] . '">' . $row['short_title'] . '</a>
              </b>
              <div class="size11">
                ' . timeformat($row['date']) . ':
                <a href="' . $boardurl . '/imagenes/ver/' . $row['ID_PICTURE'] . '#cmt_' . $row['ID_COMMENT'] . '">' . censorText($row['comment']) . '</a>
              </div>
            </span>
          </td>
        </tr>';
    }
  } else {
    echo '<div class="noesta">Nada por ac&aacute;...</div>';
  }

  echo '
        </table>
      </div>
    </div>';

  echo '
    <div class="box_timos" style="margin-right: 8px; float: left;">
      <div class="box_title" style="width: 340px;">
        <div class="box_txt box_timos">
          <center>Mis im&aacute;genes en favoritos</center>
        </div>
        <div class="box_rss">
          <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 14px; height: 12px;" border="0" />
        </div>
      </div>
      <div class="windowbg" style="width: 332px; padding: 4px;">';

  if (!empty($context['monitorfavimagenes'])) {
    foreach ($context['monitorfavimagenes'] as $row) {
      echo '
        <img alt="Imagen" title="Imagen" src="' . $settings['images_url'] . '/icons/foto.gif" />
        <b>
          <a href="' . $boardurl . '/imagenes/ver/' . $row['ID_PICTURE'] . '/" title="' . $row['full_title'] . '" alt="' . $row['full_title'] . '">' . $row['short_title'] . '</a>
        </b>
        <br />
        <p align="right" class="size11" style="margin: 0px; padding: 0px;">
          <b>
            Lo agreg&oacute;:
            <a href="' . $boardurl . '/perfil/' . $row['realName'] . '" title="' . $row['realName'] . '" alt="' . $row['realName'] . '">
              <span style="color: orange;">' . $row['realName'] . '</span>
            </a>
          </b>
        </p>
        <div class="hrs"></div>';
    }
  } else {
    echo '<div class="noesta">Nada por ac&aacute;...</div>';
  }

  echo '
      </div>
    </div>
    <div class="box_timoh" style="margin-right: 8px; float: left;">
      <div class="box_title" style="width: 348px;">
        <div class="box_txt box_timoh">
          <center>Puntos obtenidos (im&aacute;genes)</center>
        </div>
        <div class="box_rss">
          <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 14px; height: 12px;" border="0" />
        </div>
      </div>
      <div class="windowbg" style="width: 340px; padding: 4px;">';

  if (!empty($context['monitorpunimagenes'])) {
    foreach ($context['monitorpunimagenes'] as $row) {
      echo '
        <img alt="Imagen" title="Imagen" src="' . $settings['images_url'] . '/icons/foto.gif" />
        <b>
          <a href="' . $boardurl . '/imagenes/ver/' . $row['ID_PICTURE'] . '/" title="' . $row['full_title'] . '" alt="' . $row['full_title'] . '">' . $row['short_title'] . '</a>
        </b>
        <br />
        <p align="right" class="size11" style="margin: 0px; padding: 0px;">
          <b>
            <span style="color: green;">+' . $row['amount'] . '</span>
            -
            <a href="' . $boardurl . '/perfil/' . $row['realName'] . '" title="' . $row['realName'] . '">
              <span style="color: orange;">' . $row['realName'] . '</span>
            </a>
          </b>
        </p>
        <div class="hrs"></div>';
    }
  } else {
    echo '<div class="noesta">Nada por ac&aacute;...</div>';
  }

  echo '
        </div>
      </div>
    </div>';

  // Puntos obtenidos (posts)
  echo '
    <div style="float: left; width: 212px; margin-bottom: 8px;">
      <div class="publicidad" style="margin-bottom: 8px;">
        <div class="box_title" style="width: 212px;">
          <div class="box_txt publicidad_r">
            <center>Puntos obtenidos (posts)</center>
          </div>
          <div class="box_rss">
            <img src="' . $settings['images_url'] . '/blank.gif" style="width: 14px; height: 12px;" border="0" />
          </div>
        </div>
        <div class="windowbg" style="width: 204px; padding: 4px;">';

  if (!empty($context['monitorpun'])) {
    foreach ($context['monitorpun'] as $row) {
      echo '
        <img alt="' . $row['bname'] . '" title="' . $row['bname'] . '" src="' . $settings['images_url'] . '/post/icono_' . $row['ID_BOARD'] . '.gif" />
        <b>
          <a href="' . $boardurl . '/post/' . $row['ID_TOPIC'] . '/' . $row['description'] . '/' . ssi_amigable($row['full_title']) . '.html" title="' . $row['full_title'] . '" alt="' . $row['full_title'] . '">' . $row['short_title'] . '</a>
        </b>
        <br />
        <p align="right" class="size11" style="margin: 0px; padding: 0px;">
          <b>
            <span style="color: green;">+' . $row['amount'] . '</span>
            -
            <a href="' . $boardurl . '/perfil/' . $row['realName'] . '" title="' . $row['realName'] . '">
              <span style="color: orange;">' . $row['realName'] . '</span>
            </a>
          </b>
        </p>
        <div class="hrs"></div>';
    }
  } else {
    echo '
          <div class="noesta">Nada por ac&aacute;...</div>
        </div>
      </div>';
  }

  echo '
      </div>
    </div>';

  // Mis posts en favoritos
  echo '
    <div class="publicidad" style="margin-bottom: 8px;">
      <div class="box_title" style="width: 212px;">
        <div class="box_txt publicidad_r">
          <center>Mis posts en favoritos</center>
        </div>
        <div class="box_rss">
          <img  src="' . $settings['images_url'] . '/blank.gif" style="width: 14px; height: 12px;" border="0"/>
        </div>
      </div>
      <div class="windowbg" style="width: 204px; padding: 4px;">';

  if (!empty($context['monitorfav'])) {
    foreach ($context['monitorfav'] as $row) {
      echo '
        <img alt="' . $row['bname'] . '" title="' . $row['bname'] . '" src="' . $settings['images_url'] . '/post/icono_' . $row['ID_BOARD'] . '.gif" />
        <b>
          <a href="' . $boardurl . '/post/' . $row['ID_TOPIC'] . '/' . $row['description'] . '/' . ssi_amigable($row['full_title']) . '.html" title="' . $row['full_title'] . '" alt="' . $row['full_title'] . '">' . $row['short_title'] . '</a>
        </b>
        <br />
        <p align="right" class="size11" style="margin: 0px; padding: 0px;">
          <b>
            Lo agreg&oacute;:
            <a href="' . $boardurl . '/perfil/' . $row['realName'] . '" title="' . $row['realName'] . '">
              <span style="color: orange;">' . $row['realName'] . '</span>
            </a>
          </b>
        </p>
        <div class="hrs"></div>';
    }
  } else {
    echo '<div class="noesta">Nada por ac&aacute;...</div>';
  }

  echo '
      </div>
    </div>
    <div class="publicidad" style="margin-bottom: 8px;">
      <div class="box_title" style="width: 212px;">
        <div class="box_txt publicidad_r"><center>Yo en amigos</center>
      </div>
      <div class="box_rss">
        <img src="' . $settings['images_url'] . '/blank.gif" style="width: 14px; height: 12px;" border="0"/>
      </div>
    </div>
    <div class="windowbg" style="width: 204px; padding: 4px; -align: left">';

  if (!empty($context['yoamigos'])) {
    foreach ($context['yoamigos'] as $row) {
      echo '
        <b>Qui&eacute;n:</b>
        <a href="' . $boardurl . '/perfil/' . $row['memberName'] . '" title="' . $row['realName'] . '" alt="' . $row['realName'] . '">' . $row['realName'] . '</a>
        <br />
        <p align="right" class="size11" style="margin: 0px; padding: 0px;">
          <b>
            Cu&aacute;ndo:
            <span style="color: orange;">' . $row['time_updated'] . '</span>
          </b>
        </p>
        <div class="hrs"></div>';
    }
  } else {
    echo '<div class="noesta">Nada por ac&aacute;...</div>';
  }

  echo '
          </div>
        </div>
      </div>
    </div>
    <div style="clear:both"></div>';
}

?>