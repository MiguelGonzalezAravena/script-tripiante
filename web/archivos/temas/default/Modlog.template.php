<?php
@require_once('SSI.php');

// Version: 1.1; Modlog
function traduccion($valor) {
  $valor = str_replace('remove', '<font style="color: #FF0000;">Eliminado</font>', $valor);
  $valor = str_replace('modify', '<font style="color: #00BA00;">Editado</font>', $valor);

  return $valor;
}

function template_main() {
  global $context, $settings, $db_prefix, $boardurl;

  if ($context['allow_admin']) {
    echo '<form action="' . $boardurl . '/hist-mod/" method="post" accept-charset="' . $context['character_set'] . '">';
  }

  $request = db_query("
    SELECT *
    FROM {$db_prefix}mod_history", __FILE__, __LINE__);

  $query = mysqli_fetch_row($request);

  if ($query <= 0) {
    echo '
      <div style="width: 922px;">
        <div class="noesta">
          No hay nada en el historial de moderaci&oacute;n.-
        </div>
      </div>';
  } else {
    echo '
      <table class="linksList">
        <thead align="center">
          <tr>
            <th>&#191;Qu&eacute;?</th>
            <th>Por</th>
            <th>Causa</th>
            <th>Acci&oacute;n</th>
            <th>Moderador</th>
          </tr>
        </thead>
        <tbody>';

    foreach ($context['historial'] as $historial) {
      switch($historial['TYPE']) {
        case 'Post':
          $request = db_query("
            SELECT *
            FROM {$db_prefix}topics AS t, {$db_prefix}boards AS b
            WHERE t.ID_BOARD = b.ID_BOARD
            AND t.ID_TOPIC = " . $historial['ID_TOPIC'], __FILE__, __LINE__);

          $row = mysqli_fetch_assoc($request);

          echo '
            <tr>
              <td>
                <span style="color: #B97CFF;">' . $historial['TYPE'] . ':</span>
                ';

          if ($historial['ACTION'] == 'modify') {
            echo '<a href="' . $boardurl . '/post/' . $historial['ID_TOPIC'] . '/' . $row['description'] . '/' . ssi_amigable($historial['subject']) . '.html">' . $historial['subject'] . '</a>';
          } else if ($historial['ACTION'] == 'remove') {
            echo $historial['subject'];
          }

          echo '
                (ID: ' . $historial['ID_TOPIC'] . ')
              </td>
              <td>
                <a href="' . $boardurl . '/perfil/' . $historial['realName2'] . '" title="' . $historial['realName2'] . '" alt="' . $historial['realName2'] . '">' . $historial['realName2'] . '</a>
              </td>
              <td>
                ' . (empty($historial['reason']) ? '&nbsp;-&nbsp;' : $historial['reason']) . '
              </td>
              <td>
                ' . traduccion($historial['ACTION']) . '
              </td>
              <td>
                <a href="' . $boardurl . '/perfil/' . $historial['realName'] . '" title="' . $historial['realName'] . '" alt="' . $historial['realName'] . '">' . $historial['realName'] . '</a>
              <td>
            </tr>';
          break;
        case 'Imagen':
          echo '
            <tr>
              <td>
                <span style="color: #B97CFF;">' . $historial['TYPE'] . ':</span>
                ';

          if ($historial['ACTION'] == 'modify') {
            echo '&nbsp;<a href="' . $boardurl . '/imagenes/ver/' . $historial['ID_TOPIC'] . '">' . $historial['subject'] . '</a>';
          } else if ($historial['ACTION'] == 'remove') {
            echo $historial['subject'];
          }

          echo '
                (ID: ' . $historial['ID_TOPIC'] . ')
              </td>
              <td>
                <a href="' . $boardurl . '/perfil/' . $historial['realName2'] . '" title="' . $historial['realName2'] . '" alt="' . $historial['realName2'] . '">' . $historial['realName2'] . '</a>
              </td>
              <td>
                ' . (empty($historial['reason']) ? '&nbsp;-&nbsp;' : $historial['reason']) . '
              </td>
              <td>
                ' . traduccion($historial['ACTION']) . '
              </td>
              <td>
                <a href="' . $boardurl . '/perfil/' . $historial['realName'] . '" title="' . $historial['realName'] . '" alt="' . $historial['realName'] . '">' . $historial['realName'] . '</a>
              <td>
            </tr>';
          break;
      }
    }

    echo '
        </tbody>
      </table>';

    if ($context['user']['is_admin']) {
      echo '
        <p align="right">
          <input class="login" type="submit" name="removeall" value="Limpiar Historial" />
        </p>
        <input type="hidden" name="sc" value="' . $context['session_id'] . '" /></form>';
    }

    echo '<div style="clear:both"></div>';
  }
}

?>