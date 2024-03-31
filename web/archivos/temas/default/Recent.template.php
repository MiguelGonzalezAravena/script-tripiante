<?php
// Version: 1.1.9; Recent
function template_main() {
  global $context, $settings, $modSettings, $db_prefix, $etiquetas, $boardurl;

  echo '
    <div style="text-align: left;">
      <div style="float: left; height: auto; margin-right: 6px;">
        <div class="ultimos_postsa" style="margin-bottom: 4px;">
          <div class="box_title" style="width: 378px;">
            <div class="box_txt ultimos_posts">&Uacute;ltimos posts</div>
            <div class="box_rss">
              <div class="icon_img">
                <a href="' . $boardurl . '/rss/ultimos-post/">
                  <img alt="" src="' . $settings['images_url'] . '/icons/tpbig-v1-iconos.gif?v3.2.3" style="cursor: pointer; margin-top: -352px; display: inline;" />
                </a>
              </div>
            </div>
          </div>
          <!-- empiezan los post -->';

  $end = $modSettings['number_posts'];
  $page = (int) $_GET['pag'];

  if (isset($page)) {
    $start = ($page - 1) * $end;
    $actualPage = $page;
  } else {
    $start = 0;
    $actualPage = 1;
  }

  $categoria	=	htmlentities(addslashes($_REQUEST['categoria']), ENT_QUOTES, 'UTF-8');

  echo '<div class="windowbg" style="width: 370px; padding: 4px;">';

  if (!empty($categoria)) {
    $add = "AND b.description = '$categoria' ";
  }

  $request	= db_query("
    SELECT m.ID_TOPIC, m.ID_BOARD, m.hiddenOption, m.subject, b.name, b.description, b.ID_BOARD, t.isSticky, t.ID_TOPIC, t.ID_BOARD
    FROM ({$db_prefix}messages AS m, {$db_prefix}boards AS b, {$db_prefix}topics AS t)
    WHERE m.ID_TOPIC = t.ID_TOPIC
    AND b.ID_BOARD = m.ID_BOARD
    AND t.ID_BOARD = m.ID_BOARD
    {$add}
    AND t.isSticky = 1
    GROUP BY t.ID_TOPIC
    ORDER BY t.ID_TOPIC ASC
    LIMIT {$start}, {$end}", __FILE__, __LINE__);

  $request2	= db_query("
    SELECT m.ID_TOPIC, m.ID_BOARD, m.hiddenOption, m.subject, b.name, b.description, b.ID_BOARD, t.isSticky, t.ID_TOPIC, t.ID_BOARD
    FROM ({$db_prefix}messages AS m, {$db_prefix}boards AS b, {$db_prefix}topics AS t)
    WHERE m.ID_TOPIC = t.ID_TOPIC
    AND b.ID_BOARD = m.ID_BOARD
    AND t.ID_BOARD = m.ID_BOARD
    {$add}
    AND t.isSticky = 0
    GROUP BY t.ID_TOPIC
    ORDER BY t.ID_TOPIC DESC
    LIMIT {$start}, {$end}", __FILE__, __LINE__);

  $colors = array(
    0 => '#F4F4FF',
    1 => '#F4F4FF',
    2 => '#FAE78F',
    3 => '#FAE78F',
    4 => '#FF7A3D'
  );

  $index_color = 0;

  while ($row = mysqli_fetch_assoc($request)) {
    if ($index_color > 4) {
      $index_color = 0;
    }

    echo '
      <div class="entry_sticky" style="background-color: ' . $colors[$index_color] . ';">
        <div class="icon_img" style="float: left; margin-right: 2px;">
          <img alt="Sticky" src="' . $settings['images_url'] . '/icons/tpbig-v1-iconos.gif?v3.2.3" style="margin-top: -559px; display: inline;" />
        </div>
        <div class="icon" style="float: left;">
          <img alt="' . $row['name'] . '" title="' . $row['name'] . '" src="' . $settings['images_url'] . '/post/icono_' . $row['ID_BOARD'] . '.gif" />
        </div>
        <div class="text_container">';

    if($context['user']['is_guest']) {
      if($row['hiddenOption']) {
        echo '
          <div class="icon_img" style="float: left; margin-right: 0px;">
            <img alt="" src="' . $settings['images_url'] . '/icons/tpbig-v1-iconos.gif?v3.2.3" style="margin-top: -578px; display: inline;" />
          </div>';
      }
    }

    echo '
          <a href="' . $boardurl . '/post/' . $row['ID_TOPIC'] . '/' . $row['description'] . '/' . ssi_amigable($row['subject']) . '.html" target="_self" title="' . htmlentities($row['subject'], ENT_QUOTES, 'UTF-8') . '" alt="' . htmlentities($row['subject'], ENT_QUOTES, "UTF-8") . '">
            ' . ssi_reducir(htmlentities($row['subject'], ENT_QUOTES, 'UTF-8')) . '
          </a>
        </div>
      </div>
      <div class="hrs"></div>';

    $index_color	=	1	+	$index_color;
  }

  mysqli_free_result($request);

  if (mysqli_num_rows($request2) == '') {
    echo '<div class="noesta"><br/><br/><br/><br/>No hay post en esta categor&iacute;a.<br/><br/><br/><br/><br/></div>';
  } else {
    while ($row = mysqli_fetch_assoc($request2)) {
      echo '
        <div class="entry_item">
          <div class="icon">
            <img alt="' . $row['name'] . '" title="' . $row['name'] . '" src="' . $settings['images_url'] . '/post/icono_' . $row['ID_BOARD'] . '.gif" />
          </div>
          <div class="text_container">';

      if ($context['user']['is_guest']) {
        if($row['hiddenOption']) {
          echo '
            <div class="icon_img" style="float: left; margin-right: 0px;">
              <img alt="" src="' . $settings['images_url'] . '/icons/tpbig-v1-iconos.gif?v3.2.3" style="margin-top: -578px; display: inline;" />
            </div>';
        }
      }

      echo '
            <a href="' . $boardurl . '/post/' . $row['ID_TOPIC'] . '/' . $row['description'] . '/' . ssi_amigable($row['subject']) . '.html" target="_self" title="' . htmlentities($row['subject'], ENT_QUOTES, 'UTF-8') . '" alt="' . htmlentities($row['subject'], ENT_QUOTES, 'UTF-8') . '">
              ' . ssi_reducir(htmlentities($row['subject'], ENT_QUOTES, "UTF-8")) . '
            </a>
          </div>
        </div>
        <div style="clear: left;"></div>';
    }
  }

  mysqli_free_result($request);

  if (empty($categoria)) {
    $request = db_query("
      SELECT *
      FROM {$db_prefix}messages", __FILE__, __LINE__);

    $records = mysqli_num_rows($request);
  } else {
    $request = db_query("
      SELECT *
      FROM ({$db_prefix}messages AS m, {$db_prefix}boards AS b)
      WHERE m.ID_BOARD = b.ID_BOARD
      AND b.description = '$categoria'", __FILE__, __LINE__);

    $records = mysqli_num_rows($request);
  }

  mysqli_free_result($request);

  $previousPage = $actualPage - 1;
  $nextPage = $actualPage + 1;
  $lastPage = $records / $end;
  $residue = $records % $end;

  if ($residue > 0)
    $lastPage = floor($lastPage) + 1;

  echo '
      </div>
    </div>
    <div class="windowbgpag" style="width: 378px;">';

  if (mysqli_num_rows($request2) == '') {
    echo '<a href="' . $boardurl . '/">Inicio</a>';
  } else {
    if ($categoria == '') {
      if ($actualPage > 1)
        echo ' <a href="' . $boardurl . '/pag-' . $previousPage   . '">&#171; anterior</a>';

      if ($actualPage < $lastPage)
        echo ' <a href="' . $boardurl . '/pag-' . $nextPage   . '">siguiente &#187;</a>';
    } else {
      if ($actualPage > 1)
        echo ' <a href="' . $boardurl . '/categoria/' . $categoria . '/pag-' . $previousPage   . '">&#171; anterior</a>';

      if ($actualPage < $lastPage)
        echo ' <a href="' . $boardurl . '/categoria/' . $categoria . '/pag-' . $nextPage   . '">siguiente &#187;</a>';
    }
  }

  echo '
      </div>
    </div>';

  // Chat
  echo '
    <div style="float: left; margin: 0px; padding: 0px; height: 90px; margin-bottom: 8px;" align="center">
      <a href="' . $boardurl . '/chat/" target="_blank">
        <img alt="" src="' . $settings['images_url'] . '/sala-chat.png" />
      </a>
    </div>';

  // Buscador
  echo '
    <div style="float: left; margin-right: 8px;">
    <script type="text/javascript">
      function errorrojos (search) {
        if (search == \'\') {
          alert(\'Es necesario escribir una palabra para buscar.\');
          return false;
        }
      }
    </script>
    <div class="act_comments">
      <div class="box_title" style="width: 361px;">
        <div class="box_txt ultimos_comments">Buscador</div>
        <div class="box_rss">
          <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 16px; height: 16px;" border="0" />
        </div>
      </div>
      <div class="windowbg" style="padding: 4px; width: 353px; margin-bottom: 8px;">
        <form style="margin: 0px; padding: 0px;" action="' . $boardurl . '/buscar.php" method="get" accept-charset="' . $context['character_set'] . '">
          <center>
            <div class="fondo-buscador-sombra">
              <input type="text" name="search" id="search" class="ibuscador" />
              <input onclick="return errorrojos(this.form.search.value);" alt="" class="bbuscador" title="Buscar" value=" " align="top" type="submit">
            </div>
          </center>
          <div style="clear: both;"></div>
          <div class="buscadorPlus">
            <span style="float: left">Buscar con:</span>
            <div style="float: right">
              <input class="radio" id="google" name="buscador_tipo" value="g" checked="checked" type="radio" />
              <label for="google">
                <img src="' . $settings['images_url'] . '/google.gif" style="vertical-align: middle;" alt="" />
              </label>
              <input class="radio" id="tripiante" name="buscador_tipo" value="t" type="radio" />
              <label for="tripiante">
                <img src="' . $settings['images_url'] . '/tripiante.png" style="vertical-align: middle;" alt="" />
              </label>
            </div>
            <div style="clear: both;"></div>
          </div>
        </form>
      </div>
    </div>';

  if ($modSettings['radio'] == 1) {
    echo '
      <div class="act_comments">
        <div class="box_title" style="width: 361px;">
          <div class="box_txt ultimos_comments">' . $context['forum_name'] . ' - Radio</div>
          <div class="box_rss">
            <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 16px; height: 16px;" border="0">
          </div>
        </div>
        <div class="windowbg" style="padding: 4px; width: 353px; margin-bottom: 8px;">
          <center>
            ' . $modSettings['codigoradio'] . '
            <br />
            <img alt="" src="' . $settings['images_url'] . '/icons/radio-tp.gif">
            <b class="size11">Radio oficial de ' . $context['forum_name'] . '</b>
          </center>
        </div>
      </div>';
  }

  // Últimos comentarios
  echo '
    <div class="act_comments">
      <div class="box_title" style="width: 361px;">
        <div class="box_txt ultimos_comments">&Uacute;ltimos comentarios</div>
        <div class="box_rss">
          <div class="icon_img">
            <img alt="Actualizar" onclick="actualizar_comentarios(); return false;" src="' . $settings['images_url'] . '/icons/tpbig-v1-iconos2.gif?v3.2.3" style="cursor: pointer; margin-top: -96px; display: inline;" title="Actualizar" />
          </div>
        </div>
      </div>
      <div class="windowbg" style="width: 353px; padding: 4px; margin-bottom: 8px;">
        <span id="ult_comm">';

  comentarios();

  echo '
        </span>
      </div>
    </div>';

  // Tops posts de la semana
  echo '
    <div class="act_comments">
      <div class="box_title" style="width: 361px;">
        <div class="box_txt ultimos_comments">Tops posts de la semana</div>
        <div class="box_rss">
          <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 16px; height: 16px;" border="0" />
        </div>
      </div>
      <div class="windowbg" style="width: 353px; padding: 4px; margin-bottom: 8px; font-size: 11px;">';

  $starttime = mktime(0, 0, 0, date("n"), date("j"), date("Y")) - (date("N") * 3600 * 24);
  $starttime = forum_time(false, $starttime);
  $tops_posts_de_la_semana = 0;

  $request = db_query("
    SELECT t.ID_TOPIC, m.ID_TOPIC, m.subject, m.ID_BOARD, b.ID_BOARD, b.description, p.ID_TOPIC, p.TYPE, SUM(p.POINTS) as POINTS, p.time
    FROM {$db_prefix}topics AS t, {$db_prefix}messages AS m, {$db_prefix}boards AS b, {$db_prefix}points AS p
    WHERE p.time > " . $starttime . "
    AND t.ID_TOPIC = m.ID_TOPIC
    AND p.ID_TOPIC = t.ID_TOPIC
    AND m.ID_BOARD = b.ID_BOARD
    AND p.TYPE = 'post'
    GROUP BY t.ID_TOPIC
    ORDER BY POINTS DESC
    LIMIT " . $modSettings['number_tops'], __FILE__, __LINE__);

  while ($row = mysqli_fetch_assoc($request)) {
    echo '<b>' . $tops_posts_de_la_semana++ . ' -</b> <a href="' . $boardurl . '/post/' . $row['ID_TOPIC'] . '/' . $row['description'] . '/' . ssi_amigable($row['subject']) . '.html" title="' . $row['subject'] . '">' . ssi_reducir($row['subject']) . '</a> (<span title="' . $row['points'] . ' pts">' . $row['POINTS'] . ' pts</span>)
    <br />';
  }

  mysqli_free_result($request);

  echo '
      </div>
    </div>';

  // TOPs Tags (Nube de Tags)
  echo '
    <div class="act_comments">
      <div class="box_title" style="width: 361px;">
        <div class="box_txt ultimos_comments">
          TOPs Tags <span style="font-size: 9px;">(<a href="' . $boardurl . '/tags/" title="Nube de Tags">Nube de Tags</a>)</span>
        </div>
        <div class="box_rss">
          <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 16px; height: 16px;" border="0" />
        </div>
      </div>
      <div class="windowbg" style="width: 353px; padding: 4px; margin-bottom: 8px; font-size: 11px;">
      <center>';

  nube_etiquetas($etiquetas);

  echo '
        </center>
      </div>
    </div>';

  // Destacados
  echo '
    <div class="act_comments">
      <div class="box_title" style="width: 361px;">
        <div class="box_txt ultimos_comments">Destacados</div>
        <div class="box_rss">
          <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 16px; height: 16px;" border="0" />
        </div>
      </div>
      <div align="center" class="windowbg" style="width: 353px; padding: 4px; margin-bottom: 8px;">';

  ssi_destacados();

  echo '
        </div>
      </div>
    </div>
    <div style="float: left;">';

  // Imagen al azar
  echo '
    <div class="img_aletat">
      <div class="box_title" style="width: 160px;">
        <div class="box_txt img_aletat">Imagen al azar</div>
        <div class="box_rss">
          <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 16px; height: 16px;" border="0" />
        </div>
      </div>
      <div class="windowbg" style="padding: 4px; width: 152px; margin-bottom: 8px;">';

  $request = db_query("
    SELECT m.ID_MEMBER, m.realName, m.memberName, i.ID_MEMBER, i.ID_PICTURE, i.filename, i.title, i.commenttotal
    FROM ({$db_prefix}gallery_pic AS i, {$db_prefix}members AS m)
    WHERE i.ID_MEMBER = m.ID_MEMBER
    ORDER BY RAND()
    LIMIT 1", __FILE__, __LINE__);

  while ($row = mysqli_fetch_assoc($request)) {
    echo '
        <a href="' . $boardurl . '/imagenes/ver/' . $row['ID_PICTURE'] . '" title="' . $row['title'] . '" alt="' . $row['title'] . '">
          <img src="' . $row['filename'] . '" title="' . $row['title'] . '" alt="' . $row['title'] . '" width="151px" height="151px" />
        </a>
      </div>';
  }

  mysqli_free_result($request);

  echo '
    </div>';

  // Últimas imágenes
  echo '
    <div class="img_aletat">
      <div class="box_title" style="width: 161px;">
        <div class="box_txt img_aletat">&Uacute;ltimas im&aacute;genes</div>
        <div class="box_rss">
          <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 16px; height: 16px;" border="0" />
        </div>
      </div>
      <div class="windowbg" style="padding: 4px; width: 153px; margin-bottom: 8px; font-size: 11px;">';

  $request = db_query("
    SELECT *
    FROM {$db_prefix}gallery_pic
    ORDER BY ID_PICTURE DESC
    LIMIT " . $modSettings['number_images'], __FILE__, __LINE__);

  $context['ultimas_imagenes'] = array();

  while ($row = mysqli_fetch_assoc($request)) {
    echo '
      <div style="-moz-border-radius: 5px; -webkit-border-radius: 5px; padding: 2px; margin-bottom: 1px; background-color: #F4F4FF; border: 1px solid #C2D2E4;">
        <img src="' . $settings['images_url'] . '/icons/foto.gif" alt="' . $row['title'] . '" title="' . $row['title'] . '"/>&nbsp;
        <a href="' . $boardurl . '/imagenes/ver/' . $row['ID_PICTURE'] . '" title="' . $row['title'] . '" alt="' . $row['title'] . '">' . ssi_reducir2($row['title']) . '</a>
      </div>';
  }

  mysqli_free_result($request);

  if ($context['user']['is_logged']) {
    echo '
      <br />
      <center>
        <a class="icons aimg" href="' . $boardurl . '/imagenes/agregar/" title="Agrega tu imagen">Agrega tu imagen</a>
      </center>';
  }

  echo '
      </div>
    </div>';

  // User de la semana
  echo '
    <div class="img_aletat">
      <div class="box_title" style="width: 161px;">
        <div class="box_txt img_aletat">User de la semana</div>
          <div class="box_rss">
            <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 16px; height: 16px;" border="0" />
          </div>
        </div>
        <div class="windowbg" style="padding: 4px; width: 153px; margin-bottom: 8px;">';

  $starttime = mktime(0, 0, 0, date("n"), date("j"), date("Y")) - (date("N") * 3600 * 24);
  $starttime = forum_time(false, $starttime);
  $user_de_la_semana = 0;
    
  $request = db_query("
    SELECT me.ID_MEMBER, me.memberName, me.realName, COUNT(*) as count_posts
    FROM {$db_prefix}messages AS m
    LEFT JOIN {$db_prefix}members AS me ON (me.ID_MEMBER = m.ID_MEMBER)
    WHERE m.posterTime > " . $starttime . "
    AND m.ID_MEMBER != 0
    GROUP BY me.ID_MEMBER
    ORDER BY count_posts DESC
    LIMIT 10", __FILE__, __LINE__);

  $max_num_posts = 1;
  $user_de_la_semana++;

  while ($row_members = mysqli_fetch_assoc($request)) {
    echo '
      <font style="font-size: 11px">
        <b>' . $user_de_la_semana++ . ' - </b>
        <a href="' . $boardurl . '/perfil/' . $row_members['realName'] . '">' . $row_members['realName'] . '</a> (' . $row_members['count_posts']. ')
      </font>
      <br />';

    if ($max_num_posts < $row_members['count_posts']) {
      $max_num_posts = $row_members['count_posts'];
    }

    foreach ($context['user_de_la_semana'] as $i => $j) {
      $context['user_de_la_semana'][$i]['post_percent'] = round(($j['num_posts'] * 100) / $max_num_posts);
    }

    unset($max_num_posts, $row_members, $j, $i);
  }

  mysqli_free_result($request);

  echo '
      </div>
    </div>';

  // User con más post
  echo '
    <div class="img_aletat">
      <div class="box_title" style="width: 161px;">
        <div class="box_txt img_aletat">User con m&aacute;s post</div>
        <div class="box_rss">
          <div class="icon_img">
            <a href="' . $boardurl . '/rss/poster/">
              <img alt="" src="' . $settings['images_url'] . '/icons/tpbig-v1-iconos.gif?v3.2.3" style="cursor: pointer; margin-top: -352px; display: inline;" />
            </a>
          </div>
        </div>
      </div>
      <div class="windowbg" style="padding: 4px; width: 153px; margin-bottom: 8px;">';

  $members_result = db_query("
    SELECT ID_MEMBER, realName, memberName, topics
    FROM {$db_prefix}members
    WHERE topics > 0
    ORDER BY topics DESC
    LIMIT 10", __FILE__, __LINE__);

  $max_num_topics = 1;
  $user_con_mas_post = 0;

  while ($row_members = mysqli_fetch_assoc($members_result)) {
    echo '
      <font style="font-size: 11px"><b>' . $user_con_mas_post++ . ' - </b><a href="' . $boardurl . '/perfil/' . $row_members['realName'] . '" title="' . $row_members['realName'] . '">' . $row_members['realName'] . '</a> (' . $row_members['topics'] . ')</font>
      <br/>';

    if ($max_num_topics < $row_members['topics']) {
      $max_num_topics = $row_members['topics'];
    }
  }

  mysqli_free_result($request);

  echo '
      </div>
    </div>';

  // Enlaces
  echo '
    <div class="img_aletat">
      <div class="box_title" style="width: 161px;">
        <div class="box_txt img_aletat">Enlaces</div>
        <div class="box_rss">
          <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 16px; height: 16px;" border="0" />
        </div>
      </div>
      <div class="windowbg" style="padding: 4px; width: 153px; margin-bottom: 8px;">';

  // Enlace 4
  echo '
    <div align="left" style="margin-bottom: 4px;">
      <span class="iconse anuncio">
        <a title="Anunciate aca" href="' . $boardurl . '/contactanos/" target="_blank" rel="nofollow">Anunciate ac&aacute;</a>
      </span>
    </div>';

  echo '
        <div class="hrs"></div>
        <center>
          <a class="size10" href="' . $boardurl . '/enlazanos/" target="_blank" rel="nofollow">Enl&aacute;zanos en tu web</a>
        </center>
      </div>
    </div>';

  // Estadísticas
  echo '
          <div class="img_aletat">
            <div class="box_title" style="width: 161px;"><div class="box_txt img_aletat">Estad&iacute;sticas</div>
            <div class="box_rss">
              <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 16px; height: 16px;" border="0" />
            </div>
          </div>
          <div class="windowbg" align="center" style="padding: 4px; font-size: 11px; width: 153px; margin-bottom: 8px;">
              ' . $context['common_stats']['total_topics'] . ' posts<br />
              ' . $context['total_comments'] . ' comentarios<br />
              ' . $context['common_stats']['total_members'] . ' usuarios<br />
              ' . $context['common_stats']['latest_member']['link'] . ' &uacute;ltimo usuario
          </div>
        </div>
      </div>
      <div style="clear:left;"></div>
    </div>';
}

function comentarios() {
  global $modSettings, $db_prefix, $boardurl;

  $request = db_query("
    SELECT
      c.ID_COMMENT, c.ID_TOPIC AS ID_TOPIC2, c.ID_MEMBER AS ID_MEMBER2, m.ID_MEMBER, m.realName,
      t.ID_TOPIC, b.ID_BOARD, t.ID_BOARD, b.description, m2.subject, m2.ID_TOPIC, m2.subject AS subject2
    FROM ({$db_prefix}comments AS c, {$db_prefix}members AS m, {$db_prefix}topics AS t, {$db_prefix}boards as b, {$db_prefix}messages AS m2)
    WHERE c.ID_TOPIC = t.ID_TOPIC
    AND c.ID_MEMBER = m.ID_MEMBER
    AND b.ID_BOARD = t.ID_BOARD
    AND m2.ID_TOPIC = t.ID_TOPIC
    ORDER BY c.ID_COMMENT DESC
    LIMIT " . $modSettings['number_comments'], __FILE__, __LINE__);

  while ($row = mysqli_fetch_assoc($request)) {
    $ID_COMMENT = $row['ID_COMMENT'];
    $ID_TOPIC = $row['ID_TOPIC2'];
    $ID_MEMBER = $row['ID_MEMBER2'];
    $realName = $row['realName'];
    $description = $row['description'];
    $subject = ssi_reducir($row['subject']);
    echo '
      <font class="size11">
        <b>
          <a title="" href="' . $boardurl . '/perfil/' . $realName . '">' . $realName . '</a>
        </b>
        &nbsp;-&nbsp;
        <a title="' . $subject . '"  href="' . $boardurl . '/post/' . $ID_TOPIC . '/' . $description . '/' . ssi_amigable($subject) . '.html#cmt_' . $ID_COMMENT . '">' . $subject . '</a>
      </font>
      <br style="margin: 0px; padding: 0px;" />';
  }

  mysqli_free_result($request);
}

function nube_etiquetas($etiquetas) {
  global $db_prefix, $boardurl;

  $etiquetas = array(
    "windows" => 10,
    "video" => 11,
    "rock" => 10,
    "rapidshare" => 14,
    "programas" => 11,
    "programa" => 10,
    "post" => 12,
    "portable" => 10,
    "peliculas" => 10,
    "pelicula" => 10,
    "pc" => 14,
    "online" => 10,
    "musica" => 18,
    "mp3" => 12,
    "metal" => 12,
    "megaupload" => 13,
    "juegos" => 13,
    "juego" => 12,
    "imagenes" => 12,
    "humor" => 10,
    "full" => 12,
    "espaol" => 11,
    "dvdrip" => 12,
    "descargas" => 11,
    "descargar" => 12,
    "descarga" => 15,
    "de" => 13,
    "tp" => 10,
    "tripiante" => 11,
    "2010" => 20
  );

  $count = 0;

  foreach ($etiquetas as $nombreetiqueta => $apariciones) {
    $request = db_query("
      SELECT * 
      FROM ({$db_prefix}tags as t, {$db_prefix}tags_log as l)
      WHERE t.ID_TAG = l.ID_TAG
      AND t.tag = '$nombreetiqueta'", __FILE__, __LINE__);

    $row = mysqli_num_rows($request);

    echo '
      <a href="' . $boardurl . '/tags/' . $nombreetiqueta . '" style="font-size: ' . $apariciones . 'pt; margin-right: 2px; margin-bottom: 2px;" title="' . $row . ' post con el tag ' . $nombreetiqueta . '">' . $nombreetiqueta . '</a>';

    $count++;

    if($count == 9 || $count == 19 || $count == 29) {
      echo '<br />';
    }
  }
}

?>