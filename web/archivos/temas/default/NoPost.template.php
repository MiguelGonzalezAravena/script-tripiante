<?php

function template_main() {
  global $context, $settings;
  
  @require_once('SSI.php');

  $request = db_query("
    SELECT m.subject, m.ID_TOPIC, m.ID_BOARD, b.ID_BOARD, b.description, t.ID_TOPIC, t.ID_BOARD
    FROM ({$db_prefix}messages AS m, {$db_prefix}boards AS b, {$db_prefix}topics AS t)
    WHERE m.ID_BOARD = b.ID_BOARD
    AND m.ID_TOPIC = t.ID_TOPIC
    AND t.ID_BOARD = m.ID_BOARD
    ORDER BY m.ID_TOPIC DESC
    LIMIT 10", __FILE__, __LINE__);

  $context['nopost'] = array();

  while ($row = mysqli_fetch_assoc($request)) {
    $context['nopost'][] = array(
      'ID_TOPIC' => $row['ID_TOPIC'],
      'ID_BOARD' => $row['ID_BOARD'],
      'name' => $row['name'],
      'description' => $row['description'],
      'subject' => $row['subject'],
      'subject_html' => ssi_amigable($row['subject'])
    );
  }

  mysqli_free_result($request);

  echo '
    <div align="center">
      <div style="width: 390px; text-align: left;">
        <div class="post-deleted">
          <h3>Este post no existe o fue eliminado!</h3>
          Pero OJO no es el unico post en ' . $context['forum_name'] . '.
          <h4>Otros Posts</h4>
          <span style="display: none; font-size: 0.5px;">
            rapidshare megaupload mediafire casitaweb calamaro actualidad 2008 2007 2009 2010 2011 2012 1999 1992 1998 msn musica peliculas descarga directa ya si vuelve polvora mojada temporal millones litros lagrimas remolino de semillas tierras floreser autos ofrender lleves mar pido 1 2 3 4 5 6 7 8 9 0 parlantes computadora descargas programas softwares www zip js php web casita web rel nofollow alive_link serenata guitarra bateria ofertas
          </span>
          <ul>';

  foreach($context['nopost'] as $nopost) {
    echo '
      <li>
        <img style="float: left; margin-right: 4px;" src="' . $settings['images_url'] . '/post/icono_' . $nopost['ID_BOARD'] . '.gif" alt="" title="' . $nopost['name'] . '" />&nbsp;
        <a href="' . $boardurl . '/post/' . $nopost['ID_TOPIC'] . '/' . $nopost['description'] . '/' . $nopost['subject_html'] . '.html" title="' . $nopost['subject'] . '">' . $nopost['subject'] . '</a>
      </li>';
  }

  echo '
            </ul>
          </div>
        </div>
      </div>
      <div style="clear: both;">
    </div>
    <div style="clear:both"></div>';
}

?>