<?php

function reducir_amigos($tipo) {
  $tipo = censorText($tipo);

  if (strlen($tipo) > 6) {
    $tipo = substr($tipo, 0, 6) . '...';
  }

  return $tipo;
}

function pelo_color($valor) {
  $valor = str_replace('negro', 'Negro', $valor);
  $valor = str_replace('castano_oscuro', 'Casta&ntilde;o oscuro', $valor);
  $valor = str_replace('castano_claro', 'Casta&ntilde;o claro', $valor);
  $valor = str_replace('rubio', 'Rubio', $valor);
  $valor = str_replace('pelirrojo', 'Pelirrojo', $valor);
  $valor = str_replace('gris', 'Gris', $valor);
  $valor = str_replace('canoso', 'Canoso', $valor);
  $valor = str_replace('tenido', 'Te&ntilde;ido', $valor);
  $valor = str_replace('rapado', 'Rapado', $valor);
  $valor = str_replace('calvo', 'Calvo', $valor);

  return $valor;
}

function ojos_color($valor) {
  $valor = str_replace('negros', 'Negros', $valor);
  $valor = str_replace('marrones', 'Marrones', $valor);
  $valor = str_replace('celestes', 'Celestes', $valor);
  $valor = str_replace('verdes', 'Verdes', $valor);
  $valor = str_replace('grises', 'Grises', $valor);

  return $valor;
}

function fisico($valor) {
  $valor = str_replace('delgado', 'Delgado/a', $valor);
  $valor = str_replace('atletico', 'Atl&eacute;tico', $valor);
  $valor = str_replace('normal', 'Normal', $valor);
  $valor = str_replace('kilos_de_mas', 'Algunos kilos de m&aacute;s', $valor);
  $valor = str_replace('corpulento', 'Corpulento/a', $valor);

  return $valor;
}

function dieta($valor) {
  $valor = str_replace('vegetariana', 'Vegetariana', $valor);
  $valor = str_replace('lacto_vegetariana', 'Lacto Vegetariana', $valor);
  $valor = str_replace('organica', 'Org&aacute;nica', $valor);
  $valor = str_replace('de_todo', 'De todo', $valor);
  $valor = str_replace('comida_basura', 'Comida basura', $valor);

  return $valor;
}

function fumo($valor) {
  $valor = str_replace('no', 'No', $valor);
  $valor = str_replace('casualmente', 'Casualmente', $valor);
  $valor = str_replace('socialmente', 'Socialmente', $valor);
  $valor = str_replace('regularmente', 'Regularmente', $valor);
  $valor = str_replace('mucho', 'Mucho', $valor);

  return $valor;
}

function me_gustaria($valor) {
  $valor = str_replace('hacer_amigos', 'Hacer Amigos', $valor);
  $valor = str_replace('conocer_gente_con_mis_intereses', 'Conocer gente con mis intereses', $valor);
  $valor = str_replace('conocer_gente_para_hacer_negocios', 'Conocer gente para hacer negocios', $valor);
  $valor = str_replace('encontrar_pareja', 'Encontrar pareja', $valor);
  $valor = str_replace('de_todo', 'De todo', $valor);

  return $valor;
}

function estado($valor) {
  $valor = str_replace('soltero', 'Soltero/a', $valor);
  $valor = str_replace('novio', 'De novio/a', $valor);
  $valor = str_replace('casado', 'Casado/a', $valor);
  $valor = str_replace('divorciado', 'Divorciado/a', $valor);
  $valor = str_replace('viudo', 'Viudo/a', $valor);
  $valor = str_replace('algo', 'En algo...', $valor);

  return $valor;
}

function hijos($valor) {
  $valor = str_replace('no', 'No tengo', $valor);
  $valor = str_replace('algun_dia', 'Alg&uacute;n d&iacute;a', $valor);
  $valor = str_replace('no_quiero', 'No son lo m&iacute;o', $valor);
  $valor = str_replace('viven_conmigo', 'Tengo, vivo con ellos', $valor);
  $valor = str_replace('no_viven_conmigo', 'Tengo, no vivo con ellos', $valor);

  return $valor;
}

function estudios($valor) {
  $valor = str_replace('sin', 'Sin Estudios', $valor);
  $valor = str_replace('pri', 'Primario completo', $valor);
  $valor = str_replace('sec_curso', 'Secundario en curso', $valor);
  $valor = str_replace('sec_completo', 'Secundario completo', $valor);
  $valor = str_replace('ter_curso', 'Terciario en curso', $valor);
  $valor = str_replace('ter_completo', 'Terciario completo', $valor);
  $valor = str_replace('univ_curso', 'Universitario en curso', $valor);
  $valor = str_replace('univ_completo', 'Universitario completo', $valor);
  $valor = str_replace('post_curso', 'Post-grado en curso', $valor);
  $valor = str_replace('post_completo', 'Post-grado completo', $valor);

  return $valor;
}

function ingresos($valor) {
  $valor = str_replace('sin', 'Sin ingresos', $valor);
  $valor = str_replace('bajos', 'Bajos', $valor);
  $valor = str_replace('intermedios', 'Intermedios', $valor);
  $valor = str_replace('altos', 'Altos', $valor);

  return $valor;
}

function menu() {
  global $boardurl, $settings;

  echo '
    <div class="box_140" style="float: left; margin-right: 8px; margin-bottom: 8px;">
      <div class="box_title" style="width: 138px;">
        <div class="box_txt box_140-34">Mis opciones</div>
        <div class="box_rss">
          <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 16px; height: 16px;" border="0" />
        </div>
      </div>
      <div class="smalltext windowbg" style="width: 130px; padding: 4px;">
        <div align="left" style="margin-bottom: 4px;">
          <span style="margin-bottom: 5px;" class="icons cuenta2">
            <a href="' . $boardurl . '/editar-perfil/">Editar mi perfil</a>
          </span>
        </div>
        <div class="hrs"></div>
        <span class="size10" style="font-family: arial;">
          <img src="' . $settings['images_url'] . '/user.gif" alt="" />
          &nbsp;
          <b>Editar mi apariencia:</b>
          <br />
          <ul style="margin: 0px; padding-left: 15px;">
            <li style="margin: 0px; padding-left: 0px;">
              <a href="' . $boardurl . '/editar-apariencia/paso1/">Formaci&oacute;n y trabajo</a>
            </li>
            <li style="margin: 0px; padding-left: 0px;">
              <a href="' . $boardurl . '/editar-apariencia/paso2/">M&aacute;s sobre mi</a>
            </li>
            <li style="margin: 0px; padding-left: 0px;">
              <a href="' . $boardurl . '/editar-apariencia/paso3/">Como soy</a>
            </li>
            <li style="margin: 0px; padding-left: 0px;">
              <a href="' . $boardurl . '/editar-apariencia/paso4/">Intereses y preferencias</a>
            </li>
          </ul>
        </span>
        <div class="hrs"></div>
        <div align="left" style="margin-bottom: 4px;">
          <span class="icons mavatar">
            <a href="' . $boardurl . '/editar-perfil/avatar/">Modificar mi avatar</a>
          </span>
        </div>
        <div align="left" style="margin-bottom: 4px;">
          <span class="icons aimg">
            <a href="' . $boardurl . '/imagenes/agregar/">Agregar imagen</a>
          </span>
        </div>
        <div align="left" style="margin-bottom: 4px;">
          <span style="padding-left: 2px;">
            <img alt="" src="' . $settings['images_url'] . '/icons/notas.gif" />
            &nbsp;
            <a href="' . $boardurl . '/mis-notas/">Mis notas</a>
          </span>
        </div>
      </div>
    </div>';
}

function menu2() {
  global $context, $settings, $db_prefix, $boardurl;

  $memberName = censorText($context['member']['name']);

  echo '
    <div style="text-align: left; width: 100%; padding: 0px; margin: 0px;">
      <div style="float: left; margin-bottom: 10px; margin-right: 8px;">
        <div style="background-color: #E3F0FF; border: 1px solid #B3D4F8; width: 152px; padding: 4px;">
          <center>';

  if (!empty($context['member']['avatar']['name'])) {
    echo '<img src="' . $context['member']['avatar']['name'] . '" width="105" alt="" class="avatar" border="0" />';
  } else {
    echo '<img alt="" src="' . $boardurl . '/avatar.gif" border="0" alt="Sin Avatar" onerror="error_avatar(this)" />';
  }

  echo '
      </center>
    </div>';

  if ($context['user']['is_logged']) {
    echo '
      <div class="userOption">
        <ul>';

    if ($context['user']['name'] == $memberName) {
      echo '
        <li>
          <a href="' . $boardurl . '/editar-perfil/" title="Editar mi perfil">Editar mi perfil</a>
        </li>';
    } else {
      echo '
        <li>
          <a href="' . $boardurl . '/mensajes/a/' . $memberName . '" title="Enviarle mensaje privado">Enviarle mensaje privado</a>
        </li>
        <li>
          <a class="profile_actions" href="' . $boardurl . '/imagenes/' . $memberName . '" title="Ver sus im&aacute;genes">Ver sus im&aacute;genes</a>
        </li>';

      $request = db_query("
        SELECT *
        FROM {$db_prefix}buddies
        WHERE ID_MEMBER = " . $context['user']['id'] . "
        AND BUDDY_ID = " . $context['member']['id'] . "
        AND requested = " . $context['user']['id'], __FILE__, __LINE__);

      $row = mysqli_num_rows($request);

      if ($row <= 0) {
        echo '
          <li>
            <a href="' . $boardurl . '/amigos-agregar/' . $memberName . '" title="Agregar a mis amistades">Agregar a mis amistades</a>
          </li>';
      } else {
        echo '
          <li>
            <a href="' . $boardurl . '/amigos-eliminar/' . $memberName . '" title="Quitar amistad">Quitar amistad</a>
          </li>';
      }

      echo '
        <li>
          <a class="profile_actions" href="' . $boardurl . '/denunciar-usuario/' . $memberName . '" title="Denunciar usuario">Denunciar usuario</a>
        </li>';

      $request = db_query("
        SELECT *
        FROM {$db_prefix}ignored
        WHERE ID_MEMBER = " . $context['user']['id'] . "
        AND ID_IGNORED = " . $context['member']['id'], __FILE__, __LINE__);

      $ignore = mysqli_num_rows($request);

      if ($ignore <= 0) {
        echo '
          <li id="ac_no">
            <a href="#" onclick="ignorar2(\'' . $memberName . '\'); return false;" title="Ignorar usuario">Ignorar usuario</a>
          </li>
          <li id="ac_no3" style="display: none;">
            <a href="#" onclick="ignorar(\'' . $memberName . '\'); return false;" title="No ignorar usuario">No ignorar usuario</a>
          </li>
          <li id="ac_no2" style="display: none;">
            <a href="#" onclick="ignorar2(\'' . $memberName . '\'); return false;" title="Ignorar usuario">Ignorar usuario</a>
          </li>';
      } else {
        echo '
          <li id="ac_no">
            <a href="#" onclick="ignorar(\'' . $memberName . '\'); return false;" title="No ignorar usuario">No ignorar usuario</a>
          </li>
          <li id="ac_no2" style="display: none;">
            <a href="#" onclick="ignorar2(\'' . $memberName . '\'); return false;" title="Ignorar usuario">Ignorar usuario</a>
          </li>
          <li id="ac_no3" style="display: none;">
            <a href="#" onclick="ignorar(\'' . $memberName . '\'); return false;" title="No ignorar usuario">No ignorar usuario</a>
          </li>';
      }
    }

    echo '
      </ul>
      <div style="clear: both;"></div>';
  }

  if ($context['user']['is_logged']) {
    echo '
      </div>
      <div id="gif_cargando_ign" style="display: none;">
        <p align="right" style="padding: 0px; margin: 0px;">
          <img alt="" src="' . $settings['images_url'] . '/icons/cargando.gif" style="width: 16px; height: 16px;" border="0"  />
        </p>
        <div style="clear: both;"></div>
      </div>';
  }

  echo '
    <div style="margin-bottom: 10px; margin-top: 8px;">
      <div class="box_title" style="width: 160px;">
        <div class="box_txt box_perfil-36">Datos</div>
        <div class="box_rss">
          <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 16px; height: 16px;" border="0" />
        </div>
      </div>
      <div class="windowbg" style="width: 152px; padding: 4px;">
        <p class="datosp">Nick:</p>
        <a href="' . $boardurl . '/perfil/' . $memberName . '" title="' . $memberName . '">' . $memberName . '</a>
        <br /><br />
        <p class="datosp">Es usuario desde:</p>
        ' . $context['member']['registered'] . '
        <br /><br />
        <p class="datosp">Edad:</p>
        ' . $context['member']['age'] . '
        <br /><br />
        <p class="datosp">Ciudad:</p>
        ' . $context['member']['location'] . '
        <br /><br />
        <p class="datosp">Sexo:</p>
        ' . $context['member']['gender']['name'] . '
        &nbsp;-&nbsp;
        ' . $context['member']['gender']['image'] . '
        <br /><br />
        <p class="datosp">Pa&iacute;s:</p>
        ' . ssi_pais($context['member']['title']) . '
        &nbsp;-&nbsp;
        <img alt="" title="' . ssi_pais($context['member']['title']) . '" src="' . $settings['images_url'] . '/icons/banderas/' . $context['member']['title'] . '.gif" />';

  if (!empty($context['member']['msn']['name'])) {
    echo '
      <br /><br />
      <p class="datosp">Mensajero:</p>' . $context['member']['msn']['name'];
  }

  if (!empty($context['member']['website']['title'])) {
    echo '
      <br /><br />
      <p class="datosp">Sitio web:</p>
      <a style="text-transform:lowercase;" href="' . $context['member']['website']['title'] . '" target="_blank">' . $context['member']['website']['title'] . '</a>';
  }

  if (!empty($context['member']['blurb'])) {
    echo '
      <br /><br />
      <p class="datosp">Mensaje personal:</p>' . $context['member']['blurb'];
  }

  echo '
    <br /><br />
    <p class="datosp">Rango:</p>
    ' . (!empty($context['member']['group']) ? $context['member']['group'] : $context['member']['post_group']) . '
    &nbsp;-&nbsp;
    ' . $context['member']['group_stars'];

  if (!empty($context['member']['estado_icon'])) {
    echo '
      <br /><br />
      <p class="datosp">Estado:</p>
      ' . ssi_estado_icon($context['member']['estado_icon']) . '
      &nbsp;-&nbsp;
      <img title="' . ssi_estado_icon($context['member']['estado_icon']) . '" src="' . $settings['images_url'] . '/icons/estado/'. $context['member']['estado_icon'], '.gif" alt="" />';
  }

  echo '
      </div>
    </div>';
}

function sidebar() {
  global $context, $settings, $db_prefix, $boardurl;

  if ($context['member']['sidebar'] == 'si') {
    if ($context['member']['name'] != $context['user']['name']) {
      // Amigos en común
      // TO-DO: Revisar esta consulta
      $request = db_query("
        SELECT *
        FROM ({$db_prefix}members AS mem, {$db_prefix}buddies AS b, {$db_prefix}members AS mem2, {$db_prefix}buddies AS b2)
        WHERE b.ID_MEMBER = " . $context['member']['id'] . "
        AND b.BUDDY_ID = b2.BUDDY_ID
        AND mem.ID_MEMBER = b2.BUDDY_ID
        AND b2.ID_MEMBER = mem2.ID_MEMBER
        AND mem2.ID_MEMBER = " . $context['user']['id'] . "
        ORDER BY RAND()
        LIMIT 6", __FILE__, __LINE__);

      $count1 = mysqli_num_rows($request);

      if (isset($context['member']['buddies_data2'])) {
        $iq = 1;

        if ($iq == 1) {
          echo '
            <div style="margin-top: 8px;">
              <div class="box_title" style="width: 160px;">
                <div class="box_txt box_perfil-36">Amigos en com&uacute;n</div>
                <div class="box_rss">
                  <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 16px; height: 16px;" border="0" />
                </div>
              </div>
              <div class="windowbg" style="width: 152px; padding: 4px;">
                <div style="float: left; font-size: 10px;">
                  <p style="margin: 0px; padding: 0px;">
                    <a href="' . $boardurl . '/perfil/', $context['member']['name'], '/amigos-en-comun/" title="' . $count1 . ' amigos en com&uacute;n">' . $count1 . ' amigos en com&uacute;n</a>
                  </p>
                </div>
                <div style="font-size: 10px;">
                  <p align="right" style="margin: 0px; padding: 0px;">
                    <a href="' . $boardurl . '/perfil/', $context['member']['name'], '/lista-de-amigos/" title="Ver todos">Ver todos</a>
                  </p>
                </div>
                <hr />
                <center>
                  <table>
                    <tr>';
        }


        $count2 = 1;
        foreach ($context['member']['buddies_data2'] as $buddy_id => $data) {
          echo '
            <td align="center" style="font-size: 11px; font-family: arial; margin: 0px; padding: 0px;">
              <a href="' . $boardurl . '/perfil/', $data['memberName'], '" title="', $data['realName'], '" style="text-decoration: none;">
                <img style="width: 40px; height: 40px;" alt="" src="' . (!empty($data['avatar']) ? $data['avatar'] : $boardurl . '/avatar.gif') . '" onerror="error_avatar(this)" />
                <br />
                ' . reducir_amigos($data['realName']) . '
              </a>
            </td>';

          $count2++;

          if ($count2 == 3) {
            echo '
              </tr>
              <tr>';
          }

          $iq++;
        }

        echo '
                  </tr>
                </table>
              </center>
            </div>
          </div>';
      }
    }

    // Amigos 
    $request = db_query("
      SELECT *
      FROM ({$db_prefix}members AS mem, {$db_prefix}buddies AS b)
      WHERE b.ID_MEMBER = " . $context['member']['id'] . "
      AND b.BUDDY_ID = mem.ID_MEMBER
      ORDER BY RAND()
      LIMIT 6", __FILE__, __LINE__);

    $count3 = mysqli_num_rows($request);

    if (isset($context['member']['buddies_data'])) {
      $i = 1;

      if ($i == 1) {
        echo '
          <div style="margin-top: 8px;">
            <div class="box_title" style="width: 160px;">
              <div class="box_txt box_perfil-36">Amigos</div>
              <div class="box_rss">
                <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 16px; height: 16px;" border="0" />
              </div>
            </div>
            <div class="windowbg" style="width: 152px; padding: 4px;">
              <div style="float: left; font-size: 10px;">
                <p style="margin: 0px; padding: 0px;">
                  <a href="' . $boardurl . '/perfil/', $context['member']['name'], '/lista-de-amigos/" title="' . $count3 . ' amigos">' . $count3 . ' amigos</a>
                </p>
              </div>
              <div style="font-size: 10px;">
                <p align="right" style="margin: 0px; padding: 0px;">
                  <a href="' . $boardurl . '/perfil/', $context['member']['name'], '/lista-de-amigos/" title="Ver todos">Ver todos</a>
                </p>
              </div>
              <hr />
              <center>
                <table>
                  <tr>';
      }

      $count4 = 1;

      foreach ($context['member']['buddies_data'] as $buddy_id => $data) {
        echo '
          <td align="center" style="font-size: 11px; font-family: arial; margin: 0px; padding: 0px;">
            <a href="' . $boardurl . '/perfil/', $data['memberName'], '" title="', $data['realName'], '" style="text-decoration: none;">
              <img style="width: 40px; height: 40px;" alt="" src="' . (!empty($data['avatar']) ? $data['avatar'] : $boardurl . '/avatar.gif') . '" onerror="error_avatar(this)" />
              <br />
              ' . reducir_amigos($data['realName']) . '
            </a>
          </td>';

          $count4++;

          if ($count4 == 3) {
            echo '
              </tr>
              <tr>';
          }

          $i++;
      }

      echo '
                </tr>
              </table>
            </center>
          </div>
        </div>';
    }
  }
}

function menu3() {
  global $context, $settings, $db_prefix, $boardurl;

  // Conteo de comentarios
  $iduser = $context['member']['id'];

  $request = db_query("
    SELECT *
    FROM ({$db_prefix}comments AS c, {$db_prefix}topics AS t)
    WHERE c.ID_MEMBER = {$iduser}
    AND c.ID_TOPIC = t.ID_TOPIC", __FILE__, __LINE__);

  $request2 = db_query("
    SELECT c.ID_COMMENT, c.ID_MEMBER, m.ID_MEMBER, m.memberName
    FROM {$db_prefix}gallery_comment AS c, {$db_prefix}members AS m
    WHERE c.ID_MEMBER = m.ID_MEMBER
    AND m.memberName = '{$context['member']['name']}'", __FILE__, __LINE__);

  $context['comentuser'] = mysqli_num_rows($request);
  $context['comentimguser'] = mysqli_num_rows($request2);

  // Conteo de imágenes
  $request3 = db_query("
    SELECT *
    FROM {$db_prefix}gallery_pic AS c, {$db_prefix}members AS mem
    WHERE c.ID_MEMBER = mem.ID_MEMBER
    AND mem.ID_MEMBER = " . $iduser, __FILE__, __LINE__);

  $context['imguser'] = mysqli_num_rows($request3);


  // Conteo de mensajes del Muro 
  $request4 = db_query("
    SELECT *
    FROM ({$db_prefix}members AS m, {$db_prefix}profile_comments AS p)
    WHERE p.ID_MEMBER = m.ID_MEMBER
    AND p.COMMENT_MEMBER_ID = " . $context['member']['id'], __FILE__, __LINE__);

  $context['muromsg'] = mysqli_num_rows($request4);
    
  echo '
      <div style="margin-top: 8px;">
        <div class="box_title" style="width: 160px;">
          <div class="box_txt box_perfil-36">Estad&iacute;sticas</div>
          <div class="box_rss">
            <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 16px; height: 16px;" border="0" />
          </div>
        </div>
        <div class="windowbg" style="width: 152px; padding: 4px;">
          <p class="datosp">Post:</p>
          <a href="' . $boardurl . '/user-post/' . $context['member']['name'] . '">' . $context['member']['posts'] . '</a>
          <br /><br />
          <p class="datosp">Mensajes en su muro:</p>
          <span id="cantmuro">' . $context['muromsg'] . '</span>
          <br /><br />
          <p class="datosp">Comentarios:</p>
          <a href="' . $boardurl . '/user-comment/' . $context['member']['name'] . '">' . ($context['comentuser'] + $context['comentimguser']) . '</a>
          <br /><br />
          <p class="datosp">Im&aacute;genes:</p>
          <a href="' . $boardurl . '/imagenes/' . $context['member']['name'] . '">' . $context['imguser'] . '</a>
          <br /><br />
          <p class="datosp">Puntos:</p>
          ' . $context['member']['moneyBank'] . '
        </div>
      </div>
    </div>';
}

function menu4() {
  global $context, $settings, $db_prefix, $boardurl, $modSettings;

  echo '
    <div style="float: left; margin-bottom: 8px; margin-left: 8px;">
      <div style="margin-bottom: 10px;">
        <div class="box_title" style="width: 201px;">
          <div class="box_txt box_perfil2-36">&Uacute;ltimos post</div>
          <div class="box_rss">
            <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 14px; height: 12px;" border="0" />
          </div>
        </div>
        <div class="windowbg" style="width: 193px; padding: 4px;">';

  $request = db_query("
    SELECT *
    FROM ({$db_prefix}messages AS m, {$db_prefix}topics AS t, {$db_prefix}boards AS b, {$db_prefix}members AS mem)
    WHERE m.ID_TOPIC = t.ID_TOPIC
    AND m.ID_BOARD = t.ID_BOARD
    AND m.ID_BOARD = b.ID_BOARD
    AND m.ID_MEMBER = mem.ID_MEMBER
    AND mem.ID_MEMBER = {$context['member']['id']}
    ORDER BY m.ID_TOPIC DESC
    LIMIT " . $modSettings['profile_posts_limit'], __FILE__, __LINE__);

  $count = mysqli_num_rows($request);

  if ($count == 0) {
    echo '<div class="noesta">' . $context['member']['name'] . ' no tiene ning&uacute;n post hecho.-</div>';
  } else {
    $context['posts'] = array();

    while ($row = mysqli_fetch_assoc($request)) {
      $context['posts'][] = array(
        'name' => $row['name'],
        'ID_BOARD' => $row['ID_BOARD'],
        'ID_TOPIC' => $row['ID_TOPIC'],
        'description' => $row['description'],
        'subject' => $row['subject'],
      );
    }

    foreach ($context['posts'] as $post) {
      echo '
        <table width="100%">
          <tr>
            <td width="100%">
              <div class="box_icono4">
                <img alt="" title="' . $post['name'] . '" src="' . $settings['images_url'] . '/post/icono_', $post['ID_BOARD'], '.gif" />
              </div>
              <a href="' . $boardurl . '/post/' . $post['ID_TOPIC'] . '/' . $post['description'] . '/' . ssi_amigable($post['subject']) . '.html">' . ssi_reducir2(htmlentities($post['subject'], ENT_QUOTES, 'UTF-8')) . '</a>
            </td>
          </tr>
        </table>';
    }

    echo '
      <br />
      <span style="font-size: 9px;">
        <center>
          <a href="' . $boardurl . '/user-post/' . $context['member']['name'] . '">ver m&aacute;s</a>
        </center>
      </span>';
  }

  echo '
      </div>
    </div>';
}

function menu5() {
  global $context, $settings, $db_prefix, $boardurl, $modSettings;

  $memberName = censorText($context['member']['name']);

  echo '
    <div style="margin-bottom: 10px;">
      <div class="box_title" style="width: 201px;">
        <div class="box_txt box_perfil2-36">&Uacute;ltimas im&aacute;genes</div>
        <div class="box_rss">
          <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 14px; height: 12px;" border="0" />
        </div>
      </div>
      <div class="windowbg" style="width: 193px; padding: 4px;">';

  $request = db_query("
    SELECT *
    FROM ({$db_prefix}gallery_pic AS g, {$db_prefix}members AS m)
    WHERE g.ID_MEMBER = m.ID_MEMBER
    AND m.ID_MEMBER = {$context['member']['id']}
    ORDER BY g.ID_PICTURE DESC
    LIMIT " . $modSettings['profile_images_limit'], __FILE__, __LINE__);

  $count = mysqli_num_rows($request);

  if ($count <= 0) {
    echo '<div class="noesta">' . $memberName . ' no tiene ninguna imagen.-</div>';
  } else {
    $context['imagenes'] = array();

    while ($row = mysqli_fetch_assoc($request)) {
      $context['imagenes'][] = array(
        'ID_PICTURE' => $row['ID_PICTURE'],
        'filename' => $row['filename'],
        'commenttotal' => $row['commenttotal'],
      );
    }

    foreach ($context['imagenes'] as $img) {
      $total = db_query("
        SELECT COUNT(ID_COMMENT) AS total
        FROM {$db_prefix}gallery_comment
        WHERE ID_PICTURE = " . $img['ID_PICTURE'], __FILE__, __LINE__);

      $total2 = mysqli_fetch_assoc($total);

      echo '
        <div class="photo_small1">
          <center>
            <a href="' . $boardurl . '/imagenes/ver/' . $img['ID_PICTURE'] . '">
              <img alt="" style="width: 150px;" src="' . $img['filename'] . '" border="6" />
            </a>
          </center>
        </div>
        <div class="smalltext">
          <center>
            Comentarios:
            &nbsp;
            (<a href="' . $boardurl . '/imagenes/ver/' . $img['ID_PICTURE'] . '#comentarios">' . $total2['total'] . '</a>)
          </center>
        </div>';
    }

    echo '
      <br />
      <span style="font-size: 9px;">
        <center>
          <a href="' . $boardurl . '/imagenes/' . $memberName . '">Ir a sus im&aacute;genes</a>
        </center>
      </span>';
  }

  echo '
      </div>
    </div>';

  if ($context['allow_admin']) {
    echo '
        <div style="margin-bottom: 10px;">
        <div class="box_title" style="width: 201px;">
          <div class="box_txt box_perfil2-36">Panel de moderaci&oacute;n</div>
          <div class="box_rss">
            <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 14px; height: 12px;" border="0" />
          </div>
        </div>
        <div class="windowbg" style="width: 193px; padding: 4px;">
          <center>
            <a href="' . $boardurl . '/ban-user/' . $context['member']['id'] . '">Banear a ' . $memberName . '</a>
          </center>
          <center>
            <a href="' . $boardurl . '/ver-ip/' . $context['member']['ip'] . '">Rastrear IP de ' . $memberName . '</a>
          </center>
          <center>
            <a href="' . $boardurl . '/editar-usuario/' . $memberName . '">Editar perfil de ' . $memberName . '</a>
          </center>
          <center>
            <a href="' . $boardurl . '/mensajes/a/' . $memberName . '">Enviar MP a ' . $memberName . '</a>
          </center>
          <center>
            <a href="' . $boardurl . '/rastrear-user/' . $memberName . '">Rastrear a ' . $memberName . '</a>
          </center>
        </div>
      </div>';
  }

  echo '
      </div>
    </div>
    <div style="clear:both"></div>';
}

function template_perfil() {
  global $txt, $context, $settings, $boardurl;

  menu();

  echo '
    <form action="' . $boardurl . '/perfil-editando/" method="post" accept-charset="' . $context['character_set'] . '" name="creator" id="creator" enctype="multipart/form-data">
      <div class="box_780" style="float: left;">
        <div class="box_title" style="width: 772px;">
          <div class="box_txt box_780-34">
            <center>Editar mi perfil</center>
          </div>
          <div class="box_rss">
            <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 16px; height: 16px;" border="0" />
          </div>
        </div>
        <div class="windowbg" style="width: 764px; padding: 4px;">
          <table width="100%" style="padding: 4px; border: none;">
            <tr>
              <td width="20%">
                <b class="size11">Nombre y Apellido </b>
              </td>
              <td>
                <input type="text" onfocus="foco(this);" onblur="no_foco(this);" name="name" size="30" value="' . $context['member']['nombre'] . '" />
              </td>
            </tr>';

  if ($context['allow_edit_membergroups']) {
    echo '
      <tr>
        <td width="40%">
          <b class="size11">' . $txt['primary_membergroup'] . '</b>
        </td>
        <td>
          <select name="ID_GROUP">';

    // Fill the select box with all primary member groups that can be assigned to a member.
    foreach ($context['member_groups'] as $member_group) {
      echo '<option value="' . $member_group['id'] . '"' . ($member_group['is_primary'] ? ' selected="selected"' : '') . '>' . $member_group['name'] . '</option>';
    }

    echo '
        </td>
      </tr>';
  }

  echo '
    <tr>
      <td width="40%">
        <b class="size11">Nick:</b>
      </td>
      <td>' . $context['member']['name'] . '</td>
    </tr>
    <tr>
      <td width="40%">
        <b class="size11">Fecha de nacimiento:</b>
        <div class="smalltext">&#40;d&iacute;a&#47;mes&#47;a&ntilde;o&#41;</div>
      </td>
      <td>
        <select tabindex="1" name="bday2" id="bday2" autocomplete="off">
          <option value="' . $context['member']['birth_date']['day'] . '">D&iacute;a:</option>';
  for ($i = 1; $i < 32; $i++) {
    echo '<option value="' . $i . '"' . ($context['member']['birth_date']['day'] == $i ? ' selected="selected"' : '') . '>' . $i . '</option>';
  }

  // Obtener primera key del arreglo de meses
  $key = key($txt['months']);

  // Generar el arreglo de meses con posición corrida - 1 valores;
  $months = $txt['months'][$key];

  echo '
    </select>
    <select tabindex="2" name="bday1" id="bday1" autocomplete="off">
      <option value="' . $context['member']['birth_date']['month'] . '">Mes:</option>';
      
  for ($i = 1; $i < 13; $i++) {
    echo '<option value="' . $i . '"' . ($context['member']['birth_date']['month'] == $i ? ' selected="selected"' : '') . '>' . strtolower($months[$i - 1]) . '</option>';
  }

  echo '
    </select>
    <select tabindex="3" name="bday3" id="bday3" autocomplete="off">
      <option value="' . $context['member']['birth_date']['year'] . '">A&ntilde;o:</option>';

  for ($i = date("Y", time()) - 18; $i > 1899; $i--) {
    echo '<option value="' . $i . '"' . ($context['member']['birth_date']['year'] == $i ? ' selected="selected"' : '') . '>' . $i . '</option>';
  }

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

  // Privacidad
  $privacy = [
    0 => 'A todos',
    1 => 'Nadie',
    2 => 'Amigos',
    3 => 'Registrados'
  ];
 
  echo '
        </select>
      </td>
    </tr>
    <tr>
      <td width="40%">
        <b class="size11">Pa&iacute;s:&nbsp;</b>
      </td>
      <td>
        <select name="usertitle" id="usertitle">
          <option value="' . $context['member']['title'] . '">Pa&iacute;s</option>';

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
              <td width="40%">
                <b class="size11">Ciudad:&nbsp;</b>
              </td>
              <td>
                <input type="text" onfocus="foco(this);" onblur="no_foco(this);" name="location" size="30" value="' . $context['member']['location'] . '" />
              </td>
            </tr>
            <tr>
              <td width="40%">
                <b class="size11">Sexo:&nbsp;</b>
              </td>
              <td>
                <select name="gender" size="1">
                  <option value="1"' . ($context['member']['gender']['name'] == 'm' ? ' selected="selected"' : '') . '>Masculino</option>
                  <option value="2"' . ($context['member']['gender']['name'] == 'f' ? ' selected="selected"' : '') . '>Femenino</option>
                </select>
              </td>
            </tr>
            <tr>
              <td width="40%">
                <b class="size11">Mostrar apariencia a:</b>
              </td>
              <td>
                <select name="quienve" id="quienve" size="1">';

  $privacy_keys = array_keys($privacy);
  for ($i = 0; $i < count($privacy_keys); $i++) {
    $value = $privacy_keys[$i];
    echo '<option value="' . $value . '"' . ($context['member']['quienve'] == $value ? ' selected="selected"' : '') . '>' . $privacy[ $value ] . '</option>';
  }

  echo '
                </select>
              </td>
            </tr>
            <tr>
              <td width="40%">
                <b class="size11">Sidebar:</b>
              </td>
              <td>
                <select name="sidebar" id="sidebar" size="1">
                  <option value="no"' . ($context['member']['sidebar'] == 'no' ? ' selected="selected"' : '') . '>No</option>
                  <option value="si"' . ($context['member']['sidebar'] == 'si' ? ' selected="selected"' : '') . '>Si</option>
                </select>
              </td>
            </tr>
            <tr>
              <td width="40%">
                <b class="size11">Avisar si me borran posts e im&aacute;genes:</b>
              </td>
              <td>
                <select name="recibir" id="recibir" size="1">
                  <option value="no"' . ($context['member']['recibir'] == 'no' ? ' selected="selected"' : '') . '>No</option>
                  <option value="si"' . ($context['member']['recibir'] == 'si' ? ' selected="selected"' : '') . '>Si</option>
                </select>
              </td>
            </tr>
            <tr>
              <td width="20%">
                <b class="size11">Texto personal:</b>
                <div class="smalltext">(aparecer&aacute; debajo del avatar)</div>
              </td>
              <td>
                <input type="text" onfocus="foco(this);" onblur="no_foco(this);" name="personalText" size="30" maxlength="21" value="' . $context['member']['blurb'] . '" />
              </td>
            </tr>
            <tr>
              <td width="20%">
                <b class="size11">Firma:</b>
                <div class="smalltext">(aparecer&aacute; debajo tu post)</div>
              </td>
              <td>
                <textarea class="editor" onkeyup="calcCharLeft();" name="signature" rows="5" cols="50">' . $context['member']['signature'] . '</textarea>
                <br />
                <span class="smalltext">
                  M&aacute;x 400; caracteres restantes:&nbsp;&nbsp;
                  <span id="signatureLeft">400</span>
                </span>
                <script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[
                  function tick() {
                    if (typeof(document.forms.creator) != "undefined") {
                      calcCharLeft();
                      setTimeout("tick()", 1000);
                    }
                    else
                      setTimeout("tick()", 800);
                  }

                  function calcCharLeft() {
                    var maxLength = 400;
                    var oldSignature = "", currentSignature = document.forms.creator.signature.value;

                    if (!document.getElementById("signatureLeft"))
                      return;

                    if (oldSignature != currentSignature) {
                      oldSignature = currentSignature;

                      if (currentSignature.replace(/\r/, "").length > maxLength)
                        document.forms.creator.signature.value = currentSignature.replace(/\r/, "").substring(0, maxLength);
                      currentSignature = document.forms.creator.signature.value.replace(/\r/, "");
                    }

                    setInnerHTML(document.getElementById("signatureLeft"), maxLength - currentSignature.length);
                  }

                  setTimeout("tick()", 800);
                // ]]></script>
              </td>
            </tr>
            <tr>
              <td width="20%">
                <b class="size11">Mensajero:&nbsp;</b>
                <div class="smalltext">(msn, gtalk, yahoo)</div>
              </td>
              <td>
                <input type="text" onfocus="foco(this);" onblur="no_foco(this);" name="MSN" value="', $context['member']['MSN'], '" size="30" />
              </td>
            </tr>
            <tr>
              <td width="20%">
                <b class="size11">Sitio Web&nbsp;/&nbsp;Blog:&nbsp;</b>
                <div class="smalltext">(debe ser una URL completa)</div>
              </td>
              <td>
                <input type="text" onfocus="foco(this);" onblur="no_foco(this);" name="websiteTitle" size="30" value="', $context['member']['websiteTitle'], '" />
              </td>
            </tr>
            <tr>
              <td width="40%">
                <b class="size11">Email:&nbsp;</b>
                <div class="smalltext">Debe ser una direcci&oacute;n v&aacute;lida de email.</div>
              </td>
              <td>
                <input type="text" onfocus="foco(this);" onblur="no_foco(this);" name="emailAddress" size="30" value="', $context['member']['emailAddress'], '" />
              </td>
            </tr>
            <tr>
              <td width="40%">
                <b class="size11"', (isset($context['modify_error']['bad_new_password']) ? ' style="color: red;"' : ''), '>Escoge contrase&ntilde;a:&nbsp;</b>
                <div class="smalltext">Te sugerimos que utilices 6 o m&aacute;s caracteres combinando n&uacute;meros y letras.</div>
              </td>
              <td>
                <input type="password" onfocus="foco(this);" onblur="no_foco(this);" name="passwrd1" size="20" autocomplete="off" />
              </td>
            </tr>
            <tr>
              <td width="40%">
                <b class="size11">Verifica contrase&ntilde;a:&nbsp;</b>
              </td>
              <td>
                <input type="password" onfocus="foco(this);" onblur="no_foco(this);" name="passwrd2" size="20" autocomplete="off" />
              </td>
            </tr>
            <tr>
              <td align="center" colspan="2">
                <br /><br />
                <input class="login" type="submit" value="Modificar mi perfil" />
                <input type="hidden" name="sc" value="', $context['session_id'], '" />
                <input type="hidden" name="userID" value="', $context['member']['id'], '" />
                <input type="hidden" name="sa" value="', $context['menu_item_selected'], '" />
              </td>
            </tr>
          </table>
        </div>
      </div>
    </form>
    <div style="clear:both"></div>';
}

function template_avatar() {
  global $context, $boardurl, $settings;

  echo '
    <script type="text/javascript">
      function load_new_avatar() {
        var f = document.forms.per;

        if (f.avatar.value.substring(0, 7)!=\'http://\') {
          f.avatar.focus();
          alert(\'La direccion debe comenzar con http://\');
          return;
        }

        window.newAvatar = new Image();
        window.newAvatar.src = f.avatar.value;
        newAvatar.loadBeginTime = (new Date()).getTime();
        newAvatar.onerror = show_error;
        newAvatar.onload = show_new_avatar;
        avatar_check_timeout();
      }

      function avatar_check_timeout() {
        if (((new Date()).getTime() - newAvatar.loadBeginTime) > 15) {
          alert(\'Avatar no recomendable. Razon: Muy lento\');
          document.forms.per.avatar.focus();
        }
      }

      function show_error() {
        alert(\'Hubo un error al leer la imagen. Por favor, verifica que la direccion sea correcta.\');
        document.forms.per.avatar.focus();
      }

      function show_new_avatar() {
        document.getElementById(\'miAvatar\').src = newAvatar.src;
      }

      function errorrojos(avatar) {
        if (avatar == \'\') {
          document.getElementById(\'errorss\').innerHTML = \'<font class="size10" style="color: red;">Falta agregar el avatar.</font><br />\';
          return false;
        }
      }
    </script>';

  menu();

  echo '
      <form name="per" method="post" onsubmit="return load_new_avatar();" action="' . $boardurl . '/enviar-avatar/">
        <div class="box_780" style="float: left;">
          <div class="box_title" style="width: 772px;">
            <div class="box_txt box_780-34">
              <center>Modificar mi avatar</center>
            </div>
            <div class="box_rss">
              <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 16px; height: 16px;" border="0" />
            </div>
          </div>
          <div class="windowbg" border="0" style="width: 764px; padding: 4px; margin-bottom: 8px;">
            <table width="100%" cellpadding="4">
              <tr valign="top">
                <td width="130px" valign="top">
                  <div class="fondoavatar" style="overflow: auto; width: 130px;" align="right">
                    <img alt="" src="', $context['member']['avatar']['name'], '" width="120" weight="120" align="left" vspace="4" hspace="4" id="miAvatar" onerror="error_avatar(this)" />
                  </div>
                </td>
                <td width="640px" valign="top">
                  <br /><br />
                  <center>
                    Escribe la direcci&oacute;n de tu <i>avatar</i>.
                    <br />
                    Ejemplo:&nbsp;
                    <b>' . $boardurl . '/avatar.gif</b>
                    <br /><br />
                    <input type="text" onfocus="foco(this);" onblur="no_foco(this);" size="64" maxlength="255" name="avatar" id="avatar" value="', $context['member']['avatar']['name'], '" />
                    <input type="button" class="login" value="Previsualizar" onclick="load_new_avatar()" />
                    <br />
                    <label id="errorss"></label>
                    <label id="sinavatar">
                      <input name="sinavatar" id="sinavatar" value="1" type="checkbox" ' . ($context['member']['avatar']['external'] == $boardurl . '/avatar.gif' ? 'checked="checked"' : '') . '>
                      &nbsp;
                      Sin avatar
                      &nbsp;
                      <span style="font-size:10px;">(avatar default)</span>.
                    </label>
                  </center>
                </td>
              </tr>
              <tr>
                <td colspan="3" align="center">
                  <hr />
                  <b class="size11" style="color: red;">* Si el avatar contiene pornograf&iacute;a, es morboso. Se borrar&aacute;.</b>
                  <br />
                  <input onclick="return errorrojos(this.form.avatar.value); this.form.submit()" type="submit" class="button" style="font-size: 15px" value="Modificar mi perfil" title="Modificar mi perfil" />
                  <input type="hidden" name="sc" value="', $context['session_id'], '" />
                  <input type="hidden" name="userID" value="', $context['member']['id'], '" />
                  <input type="hidden" name="sa" value="', $context['menu_item_selected'], '" />
                </td>
              </tr>
            </table>
          </div>
        </div>
      </div>
    </form>
    <div style="clear:both"></div>';
      
  if ($_POST['sinavatar'] == '1') {
    $context['member']['avatar']['name'] = $boardurl . '/avatar.gif';
  }
}

function template_paso1() {
  global $context, $boardurl, $settings;

  menu();

  $schooling = [
    '' => 'Sin respuesta',
    'sin' => 'Sin estudios',
    'pri' => 'Primario completo',
    'sec_curso' => 'Secundario en curso',
    'sec_completo' => 'Secundario completo',
    'ter_curso' => 'Terciario en curso',
    'ter_completo' => 'Terciario completo',
    'univ_curso' => 'Universitario en curso',
    'univ_completo' => 'Universitario completo',
    'post_curso' => 'Postgrado en curso',
    'post_completo' => 'Postgrado completo'
  ];

  $incomes = [
    '' => 'Sin respuesta',
    'sin' => 'Sin ingresos',
    'bajos' => 'Bajos',
    'intermedios' => 'Intermedios',
    'altos' => 'Altos '
  ];

  echo '
      <div class="box_780" style="float: left; margin-bottom: 8px;">
        <div class="box_title" style="width: 778px;">
          <div class="box_txt box_780-34">
            <center>Formaci&oacute;n y trabajo</center>
          </div>
          <div class="box_rss">
            <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 16px; height: 16px;" border="0" />
          </div>
        </div>
        <div class="windowbg" border="0" style="width: 770px; padding: 4px;">
          <form action="' . $boardurl . '/accion-apariencia/paso1/" method="post" accept-charset="' . $context['character_set'] . '" enctype="multipart/form-data" style="margin: 0px; padding: 0px;">
            <table cellpadding="4" width="100%">
              <tbody>
                <tr>
                  <td align="right" valign="top" width="23%">
                    <b>Estudios:</b>
                  </td>
                  <td width="40%">
                    <select id="estudios" name="estudios">';

  $schooling_keys = array_keys($schooling);

  for ($i = 0; $i < count($schooling_keys); $i++) {
    $value = $schooling_keys[$i];
    echo '<option value="' . $value . '"' . ($context['member']['estudios'] == $value ? ' selected="selected"' : '') . '>' . $schooling[$value] . '</option>';
  }

  echo '
                  </select>
                </td>
              </tr>
              <tr>
                <td align="right" valign="top" width="23%">
                  <b>Profesi&oacute;n:</b>
                </td>
                <td width="40%">
                  <input size="30" maxlength="32" name="profesion" id="profesion" value="' . $context['member']['profesion'] . '" type="text" onfocus="foco(this);" onblur="no_foco(this);" />
                </td>
              </tr>
              <tr>
                <td align="right" valign="top">
                  <b>Empresa:</b>
                </td>
                <td>
                  <input size="30" maxlength="32" name="empresa" id="empresa" value="' . $context['member']['empresa'] . '" type="text" onfocus="foco(this);" onblur="no_foco(this);" />
                </td>
              </tr>
              <tr>
                <td align="right" valign="top">
                  <b>Nivel de ingresos:</b>
                </td>
                <td>
                  <select id="ingresos" name="ingresos">';

  $incomes_keys = array_keys($incomes);

  for ($i = 0; $i < count($incomes_keys); $i++) {
    $value = $incomes_keys[$i];
    echo '<option value="' . $value . '"' . ($context['member']['ingresos'] == $value ? ' selected="selected"' : '') . '>' . $incomes[$value] . '</option>';
  }

  echo '
                  </select>
                </td>
              </tr>
              <tr>
                <td align="right" valign="top">
                  <b>Intereses Profesionales:</b>
                </td>
                <td>
                  <textarea name="intereses_profesionales" cols="30" rows="5" id="intereses_profesionales">' . $context['member']['intereses_profesionales'] . '</textarea>
                </td>
              </tr>
              <tr>
                <td align="right" valign="top">
                  <b>Habilidades Profesionales:</b>
                </td>
                <td>
                  <textarea name="habilidades_profesionales" cols="30" rows="5" id="habilidades_profesionales">' . $context['member']['habilidades_profesionales'] . '</textarea>
                </td>
              </tr>
              <tr>
                <td colspan="3" align="center">
                  <hr />
                  Al modificar mi apariencia tambi&eacute;n acepto los <a href="' . $boardurl . '/terminos-y-condiciones/" target="_blank">T&eacute;rminos de uso</a>.
                </td>
              </tr>
              <tr>
                <td colspan="3" align="center">
                  <input class="button" style="font-size: 15px;" value="Modificar mi apariencia" title="Modificar mi apariencia" type="submit" />
                  <input type="hidden" name="sc" value="' . $context['session_id'] . '" />
                  <input type="hidden" name="userID" value="' . $context['member']['id'] . '" />
                  <input type="hidden" name="sa" value="' . $context['menu_item_selected'] . '" />
                </td>
              </tr>
            </tbody>
          </table>
        </form>
      </div>
    </div>
    <div style="clear:both"></div>';
}

// Mejorar
function template_paso2() {
  global $context, $boardurl, $settings;

  menu();

  echo '
    <div class="box_780" style="float: left; margin-bottom: 8px;">
      <div class="box_title" style="width: 778px;">
        <div class="box_txt box_780-34">
          <center>M&aacute;s sobre mi</center>
        </div>
        <div class="box_rss">
          <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 16px; height: 16px;" border="0" />
        </div>
      </div>
      <div class="windowbg" border="0" style="width: 770px; padding: 4px;">
        <form action="' . $boardurl . '/accion-apariencia/paso2/" method="post" accept-charset="' . $context['character_set'] . '" enctype="multipart/form-data" style="margin: 0px; padding: 0px;">
          <table width="100%" cellpadding="4">
            <tbody>
              <tr>
                <td valign="top" width="23%" align="right">
                  <b>Me gustar&iacute;a:</b>
                </td>
                <td width="40%">
                  <table width="100%" border="0">
                    <tbody>
                      <tr>
                        <td>
                          <input '; if(empty($context['member']['me_gustaria'])) { echo 'checked="checked"'; } echo 'name="me_gustaria" id="me_gustaria" value="" type="radio" />
                          Sin Respuesta
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <input '; if($context['member']['me_gustaria'] == 'me_gustaria') { echo 'checked="checked"'; } echo 'name="me_gustaria" id="me_gustaria" value="hacer_amigos" type="radio" />
                          Hacer Amigos
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <input '; if($context['member']['me_gustaria'] == 'conocer_gente_con_mis_intereses') { echo 'checked="checked"'; } echo 'name="me_gustaria" id="me_gustaria" value="conocer_gente_con_mis_intereses" type="radio" />
                          Conocer gente con mis intereses
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <input '; if($context['member']['me_gustaria'] == 'conocer_gente_para_hacer_negocios') { echo 'checked="checked"'; } echo 'name="me_gustaria" id="me_gustaria" value="conocer_gente_para_hacer_negocios" type="radio" />
                          Conocer gente para hacer negocios
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <input '; if($context['member']['me_gustaria'] == 'encontrar_pareja') { echo 'checked="checked"'; } echo 'name="me_gustaria" id="me_gustaria" value="encontrar_pareja" type="radio" />
                          Encontrar pareja
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <input '; if($context['member']['me_gustaria'] == 'de_todo') { echo 'checked="checked"'; } echo 'name="me_gustaria" id="me_gustaria" value="de_todo" type="radio" />
                          De todo
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </td>
              </tr>
              <tr>
                <td valign="top" align="right">
                  <b>En el amor estoy:</b>
                </td>
                <td>
                  <table width="100%" border="0">
                    <tbody>
                      <tr>
                        <td>
                          <input '; if(empty($context['member']['estado'])) { echo 'checked="checked"'; } echo 'name="estado" id="estado" value="" type="radio" />
                          Sin Respuesta
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <input '; if($context['member']['estado'] == 'soltero') { echo 'checked="checked"'; } echo 'name="estado" id="estado" value="soltero" type="radio" />
                          Soltero/a
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <input '; if($context['member']['estado'] == 'novio') { echo 'checked="checked"'; } echo 'name="estado" id="estado" value="novio" type="radio" />
                          De novio/a
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <input '; if($context['member']['estado'] == 'casado') { echo 'checked="checked"'; } echo 'name="estado" id="estado" value="casado" type="radio" />
                          Casado/a
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <input '; if($context['member']['estado'] == 'divorciado') { echo 'checked="checked"'; } echo 'name="estado" id="estado" value="divorciado" type="radio" />
                          Divorciado/a
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <input '; if($context['member']['estado'] == 'viudo') { echo 'checked="checked"'; } echo 'name="estado" id="estado" value="viudo" type="radio" />
                          Viudo/a
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <input '; if($context['member']['estado'] == 'algo') { echo 'checked="checked"'; } echo 'name="estado" id="estado" value="algo" type="radio" />
                          En algo...
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </td>
              </tr>
              <tr>
                <td valign="top" width="23%" align="right">
                  <b>Hijos:</b>
                </td>
                <td width="40%">
                  <table width="100%" border="0">
                    <tbody>
                      <tr>
                        <td>
                          <input '; if(empty($context['member']['hijos'])) { echo 'checked="checked"'; } echo 'name="hijos" id="hijos" value="" type="radio" />
                          Sin Respuesta
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <input '; if($context['member']['hijos'] == 'no') { echo 'checked="checked"'; } echo 'name="hijos" id="hijos" value="no" type="radio" />
                          No tengo
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <input '; if($context['member']['hijos'] == 'algun_dia') { echo 'checked="checked"'; } echo 'name="hijos" id="hijos" value="algun_dia" type="radio" />
                          Alg&uacute;n d&iacute;a
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <input '; if($context['member']['hijos'] == 'no_quiero') { echo 'checked="checked"'; } echo 'name="hijos" id="hijos" value="no_quiero" type="radio" />
                          No son lo m&iacute;o
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <input '; if($context['member']['hijos'] == 'viven_conmigo') { echo 'checked="checked"'; } echo 'name="hijos" id="hijos" value="viven_conmigo" type="radio" />
                          Tengo, vivo con ellos
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <input '; if($context['member']['hijos'] == 'no_viven_conmigo') { echo 'checked="checked"'; } echo 'name="hijos" id="hijos" value="no_viven_conmigo" type="radio" />
                          Tengo, no vivo con ellos
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </td>
              </tr>
              <tr>
                <td colspan="3" align="center">
                  <hr />
                  Al modificar mi apariencia tambi&eacute;n acepto los <a href="' . $boardurl . '/terminos-y-condiciones/" target="_blank">T&eacute;rminos de uso</a>.
                </td>
              </tr>
              <tr>
                <td colspan="3" align="center">
                  <input class="button" style="font-size: 15px;" value="Modificar mi apariencia" title="Modificar mi apariencia" type="submit" />
                  <input type="hidden" name="sc" value="', $context['session_id'], '" />
                  <input type="hidden" name="userID" value="', $context['member']['id'], '" />
                  <input type="hidden" name="sa" value="', $context['menu_item_selected'], '" />
                </td>
              </tr>
            </tbody>
          </table>
        </form>
      </div>
    </div>
    <div style="clear:both"></div>';
}

function template_paso3() {
  global $context, $boardurl, $settings;

  menu();

  echo '
    <div class="box_780" style="float: left; margin-bottom: 8px;">
      <div class="box_title" style="width: 778px;">
        <div class="box_txt box_780-34">
          <center>Como soy</center>
        </div>
        <div class="box_rss">
          <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 16px; height: 16px;" border="0" />
        </div>
      </div>
      <div class="windowbg" border="0" style="width: 770px; padding: 4px;">
        <form action="' . $boardurl . '/accion-apariencia/paso3/" method="post" accept-charset="' . $context['character_set'] . '" enctype="multipart/form-data" style="margin: 0px; padding: 0px;">
          <table width="100%" cellpadding="4">
            <tbody>
              <tr>
                <td align="right" width="23%">
                  <b>Mi altura:</b>
                </td>
                <td width="40%">
                  <input name="altura" id="altura" size="3" maxlength="3" type="text" onfocus="foco(this);" onblur="no_foco(this);" value="' . $context['member']['altura'] . '" />
                  &nbsp;
                  centimetros
                </td>
              </tr>
              <tr>
                <td align="right">
                  <b>Mi peso:</b>
                </td>
                <td>
                  <input name="peso" id="peso" size="3" maxlength="3" type="text" onfocus="foco(this);" onblur="no_foco(this);" value="' . $context['member']['peso'] . '" />
                  &nbsp;
                  kilos
                </td>
              </tr>
              <tr>
                <td align="right" width="23%">
                  <b>Color de pelo:</b>
                </td>
                <td width="40%">
                  <select id="pelo_color" name="pelo_color">
                    <option '; if(empty($context['member']['pelo_color'])) { echo 'selected="selected" '; } echo 'value="">Sin Respuesta</option>
                    <option '; if($context['member']['pelo_color'] == 'negro') { echo 'selected="selected" '; } echo 'value="negro">Negro</option>
                    <option '; if($context['member']['pelo_color'] == 'castano_oscuro') { echo 'selected="selected" '; } echo 'value="castano_oscuro">Casta&ntilde;o oscuro</option>
                    <option '; if($context['member']['pelo_color'] == 'castano_claro') { echo 'selected="selected" '; } echo 'value="castano_claro">Casta&ntilde;o claro</option>
                    <option '; if($context['member']['pelo_color'] == 'rubio') { echo 'selected="selected" '; } echo 'value="rubio">Rubio</option>
                    <option '; if($context['member']['pelo_color'] == 'pelirrojo') { echo 'selected="selected" '; } echo 'value="pelirrojo">Pelirrojo</option>
                    <option '; if($context['member']['pelo_color'] == 'gris') { echo 'selected="selected" '; } echo 'value="gris">Gris</option>
                    <option '; if($context['member']['pelo_color'] == 'canoso') { echo 'selected="selected" '; } echo 'value="canoso">Canoso</option>
                    <option '; if($context['member']['pelo_color'] == 'tenido') { echo 'selected="selected" '; } echo 'value="tenido">Te&ntilde;ido</option>
                    <option '; if($context['member']['pelo_color'] == 'rapado') { echo 'selected="selected" '; } echo 'value="rapado">Rapado</option>
                    <option '; if($context['member']['pelo_color'] == 'calvo') { echo 'selected="selected" '; } echo 'value="calvo">Calvo</option>
                  </select>
                </td>
              </tr>
              <tr>
                <td align="right">
                  <b>Color de ojos:</b>
                </td>
                <td>
                  <select id="ojos_color" name="ojos_color">
                    <option '; if(empty($context['user']['ojos_color'])) { echo 'selected="selected" '; } echo 'value="">Sin Respuesta</option>
                    <option '; if($context['member']['ojos_color'] == 'negros') { echo 'selected="selected" '; } echo 'value="negros">Negros</option>
                    <option '; if($context['member']['ojos_color'] == 'marrones') { echo 'selected="selected" '; } echo 'value="marrones">Marrones</option>
                    <option '; if($context['member']['ojos_color'] == 'celestes') { echo 'selected="selected" '; } echo 'value="celestes">Celestes</option>
                    <option '; if($context['member']['ojos_color'] == 'verdes') { echo 'selected="selected" '; } echo 'value="verdes">Verdes</option>
                    <option '; if($context['member']['ojos_color'] == 'grises') { echo 'selected="selected" '; } echo 'value="grises">Grises</option>
                  </select>
                </td>
              </tr>
              <tr>
                <td align="right">
                  <b>Complexi&oacute;n:</b>
                </td>
                <td>
                  <select id="fisico" name="fisico">
                    <option '; if(empty($context['member']['fisico'])) { echo 'selected="selected" '; } echo 'value="">Sin Respuesta</option>
                    <option '; if($context['member']['fisico'] == 'delgado') { echo 'selected="selected" '; } echo 'value="delgado">Delgado/a</option>
                    <option '; if($context['member']['fisico'] == 'atletico') { echo 'selected="selected" '; } echo 'value="atletico">Atl&eacute;tico</option>
                    <option '; if($context['member']['fisico'] == 'normal') { echo 'selected="selected" '; } echo 'value="normal">Normal</option>
                    <option '; if($context['member']['fisico'] == 'kilos_de_mas') { echo 'selected="selected" '; } echo 'value="kilos_de_mas">Algunos kilos de m&aacute;s</option>
                    <option '; if($context['member']['fisico'] == 'corpulento') { echo 'selected="selected" '; } echo 'value="corpulento">Corpulento/a</option>
                  </select>
                </td>
              </tr>
              <tr>
                <td align="right" valign="top">
                  <b>Mi dieta es:</b>
                </td>
                <td>
                  <select id="dieta" name="dieta">
                    <option '; if(empty($context['member']['dieta'])) { echo 'selected="selected" '; } echo 'value="">Sin Respuesta</option>
                    <option '; if($context['member']['dieta'] == 'vegetariana') { echo 'selected="selected" '; } echo 'value="vegetariana">Vegetariana</option>
                    <option '; if($context['member']['dieta'] == 'lacto_vegetariana') { echo 'selected="selected" '; } echo 'value="lacto_vegetariana">Lacto Vegetariana</option>
                    <option '; if($context['member']['dieta'] == 'organica') { echo 'selected="selected" '; } echo 'value="organica">Org&aacute;nica</option>
                    <option '; if($context['member']['dieta'] == 'de_todo') { echo 'selected="selected" '; } echo 'value="de_todo">De todo</option>
                    <option '; if($context['member']['dieta'] == 'comida_basura') { echo 'selected="selected" '; } echo 'value="comida_basura">Comida basura</option>
                  </select>
                </td> 
              </tr>
              <tr>
                <td align="right" valign="top">
                  <b>Fumo:</b>
                </td>
                <td>
                  <table border="0" width="100%">
                    <tbody>
                      <tr>
                        <td>
                          <input '; if(empty($context['member']['fumo'])) { echo 'checked="checked" '; } echo 'name="fumo" id="fumo" value="" type="radio" />
                          Sin Respuesta
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <input '; if($context['member']['fumo'] == 'no') { echo 'checked="checked" '; } echo 'name="fumo" id="fumo" value="no" type="radio" />
                          No
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <input '; if($context['member']['fumo'] == 'casualmente') { echo 'checked="checked" '; } echo 'name="fumo" id="fumo" value="casualmente" type="radio" />
                          Casualmente
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <input '; if($context['member']['fumo'] == 'socialmente') { echo 'checked="checked" '; } echo 'name="fumo" id="fumo" value="socialmente" type="radio" />
                          Socialmente
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <input '; if($context['member']['fumo'] == 'regularmente') { echo 'checked="checked" '; } echo 'name="fumo" id="fumo" value="regularmente" type="radio" />
                          Regularmente
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <input '; if($context['member']['fumo'] == 'mucho') { echo 'checked="checked" '; } echo 'name="fumo" id="fumo" value="mucho" type="radio">
                          Mucho
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </td>
              </tr>
              <tr>
                <td align="right" valign="top">
                  <b>Tomo alcohol:</b>
                </td>
                <td>
                  <table border="0" width="100%">
                    <tbody>
                      <tr>
                        <td>
                          <input '; if(empty($context['member']['tomo_alcohol'])) { echo 'checked="checked" '; } echo 'name="tomo_alcohol" id="tomo_alcohol" value="" type="radio" />
                          Sin Respuesta
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <input '; if($context['member']['tomo_alcohol'] == 'no') { echo 'checked="checked" '; } echo 'name="tomo_alcohol" id="tomo_alcohol" value="no" type="radio" />
                          No
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <input '; if($context['member']['tomo_alcohol'] == 'casualmente') { echo 'checked="checked" '; } echo 'name="tomo_alcohol" id="tomo_alcohol" value="casualmente" type="radio" />
                          Casualmente
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <input '; if($context['member']['tomo_alcohol'] == 'socialmente') { echo 'checked="checked" '; } echo 'name="tomo_alcohol" id="tomo_alcohol" value="socialmente" type="radio" />
                          Socialmente
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <input '; if($context['member']['tomo_alcohol'] == 'regularmente') { echo 'checked="checked" '; } echo 'name="tomo_alcohol" id="tomo_alcohol" value="regularmente" type="radio" />
                          Regularmente
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <input '; if($context['member']['tomo_alcohol'] == 'mucho') { echo 'checked="checked" '; } echo 'name="tomo_alcohol" id="tomo_alcohol" value="mucho" type="radio" />
                          Mucho
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </td>
              </tr>
              <tr>
                <td colspan="3" align="center">
                  <hr />
                  Al modificar mi apariencia tambi&eacute;n acepto los <a href="' . $boardurl . '/terminos-y-condiciones/" target="_blank">T&eacute;rminos de uso</a>.
                </td>
              </tr>
              <tr>
                <td colspan="3" align="center">
                  <input class="button" style="font-size: 15px;" value="Modificar mi apariencia" title="Modificar mi apariencia" type="submit" />
                  <input type="hidden" name="sc" value="', $context['session_id'], '" />
                  <input type="hidden" name="userID" value="', $context['member']['id'], '" />
                  <input type="hidden" name="sa" value="', $context['menu_item_selected'], '" />
                </td>
              </tr>
            </tbody>
          </table>
        </form>
      </div>
    </div>
    <div style="clear:both"></div>';
}

function template_paso4() {
  global $context, $boardurl, $settings;
  
  menu();

  echo '
    <div class="box_780" style="float: left; margin-bottom: 8px;">
      <div class="box_title" style="width: 778px;">
        <div class="box_txt box_780-34">
          <center>Intereses y preferencias</center>
        </div>
        <div class="box_rss">
          <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 16px; height: 16px;" border="0" />
        </div>
      </div>
      <div class="windowbg" border="0" style="width: 770px; padding: 4px;">
        <form action="' . $boardurl . '/accion-apariencia/paso4/" method="post" accept-charset="' . $context['character_set'] . '" enctype="multipart/form-data" style="margin: 0px; padding: 0px;">
          <table width="100%" cellpadding="4">
            <tbody>
              <tr>
                <td align="right" valign="top" width="23%">
                  <b>Mis intereses:</b>
                </td>
                <td width="40%">
                  <textarea style="width:235px;height:102px;" name="mis_intereses" cols="30" rows="5" id="mis_intereses">', $context['member']['mis_intereses'], '</textarea>
                </td>
              </tr>
              <tr>
                <td align="right" valign="top">
                  <b>Hobbies:</b>
                </td>
                <td>
                  <textarea style="width: 235px; height: 102px;" name="hobbies" cols="30" rows="5" id="hobbies">', $context['member']['hobbies'], '</textarea>
                </td>
              </tr>
              <tr>
                <td align="right" valign="top">
                  <b>Series de Tv favoritas:</b>
                </td>
                <td>
                  <textarea style="width: 235px; height: 102px;" name="series_tv_favoritas" cols="30" rows="5" id="series_tv_favoritas">', $context['member']['series_tv_favoritas'], '</textarea>
                </td>
              </tr>
              <tr>
                <td align="right" valign="top" width="23%">
                  <b>M&uacute;sica favorita:</b>
                </td>
                <td width="40%">
                  <textarea style="width: 235px; height: 102px;" name="musica_favorita" cols="30" rows="5" id="musica_favorita">', $context['member']['musica_favorita'], '</textarea>
                </td>
              </tr>
              <tr>
                <td align="right" valign="top">
                  <b>Deportes y equipos favoritos:</b>
                </td>
                <td>
                  <textarea style="width: 235px; height: 102px;" name="deportes_y_equipos_favoritos" cols="30" rows="5" id="deportes_y_equipos_favoritos">', $context['member']['deportes_y_equipos_favoritos'], '</textarea>
                </td>
              </tr>
              <tr>
                <td align="right" valign="top">
                  <b>Libros Favoritos:</b>
                </td>
                <td>
                  <textarea style="width: 235px; height: 102px;" name="libros_favoritos" cols="30" rows="5" id="libros_favoritos">', $context['member']['libros_favoritos'], '</textarea>
                </td>
              </tr>
              <tr>
                <td align="right" valign="top" width="23%">
                  <b>Pel&iacute;culas favoritas:</b>
                </td>
                <td width="40%">
                  <textarea style="width: 235px; height: 102px;" name="peliculas_favoritas" cols="30" rows="5" id="peliculas_favoritas">', $context['member']['peliculas_favoritas'], '</textarea>
                </td>
              </tr>
              <tr>
                <td align="right" valign="top">
                  <b>Comida favor&iacute;ta:</b>
                </td>
                <td>
                  <textarea style="width: 235px; height: 102px;" name="comida_favorita" cols="30" rows="5" id="comida_favorita">', $context['member']['comida_favorita'], '</textarea>
                </td>
              </tr>
              <tr>
                <td align="right" valign="top">
                  <b>Mis h&eacute;roes son:</b>
                </td>
                <td>
                  <textarea style="width: 235px; height: 102px;" name="mis_heroes_son" cols="30" rows="5" id="mis_heroes_son">', $context['member']['mis_heroes_son'], '</textarea>
                </td>
              </tr>
              <tr>
                <td colspan="3" align="center">
                  <hr />
                  Al modificar mi apariencia tambi&eacute;n acepto los <a href="' . $boardurl . '/terminos-y-condiciones/" target="_blank">T&eacute;rminos de uso</a>.
                </td>
              </tr>
              <tr>
                <td colspan="3" align="center">
                  <input class="button" style="font-size: 15px;" value="Modificar mi apariencia" title="Modificar mi apariencia" type="submit" />
                  <input type="hidden" name="sc" value="', $context['session_id'], '" />
                  <input type="hidden" name="userID" value="', $context['member']['id'], '" />
                  <input type="hidden" name="sa" value="', $context['menu_item_selected'], '" />
                </td>
              </tr>
            </tbody>
          </table>
        </form>
      </div>
    </div>
    <div style="clear:both"></div>';
}

function template_agregarimagen() {
  global $settings, $boardurl;

  echo '
    <script language="JavaScript" type="text/javascript">
      function requerido(title, filename) {
        if (title == \'\') {
          alert(\'No has escrito el titulo de la imagen.\');
          return false;
        }

        if(filename == \'\') {
          alert(\'No has agregado ning\xfan enlace de imagen.\');
          return false;
        }
      }
      </script>';

  menu();

  echo '
    <form method="POST" enctype="multipart/form-data" name="forma" id="forma" action="' . $boardurl . '/imagenes/agregar/enviar/">
      <div class="box_780" style="float: left;">
        <div class="box_title" style="width: 778px;">
          <div class="box_txt box_780-34">
            <center>Agregar imagen</center>
          </div>
          <div class="box_rss">
            <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 16px; height: 16px;" border="0" />
          </div>
        </div>
        <div class="windowbg" border="0" style="width: 770px; padding: 4px;">
          <center>
            <b class="size11">Titulo:</b>
            <br />
            <input  tabindex="1" size="60" maxlength="54" type="text" onfocus="foco(this);" onblur="no_foco(this);" name="title" id="title" value="" />
            <br /><br />
            <b class="size11">URL de la imagen:</b>
            &nbsp;
            <br />
            <input type="text" onfocus="foco(this);" onblur="no_foco(this);" tabindex="2" size="60" name="filename" value="" />
            <hr />
            <b class="size11" style="color: red;">* Si la imagen contiene pornografia, es morboso. Se borrar&aacute;.</b>
            <br />
            <input type="submit" class="button" style="font-size: 15px;" onclick="return requerido(this.form.title.value, this.form.filename.value);" tabindex="3" value="Agregar imagen" name="submit" />
          </center>
        </div>
      </div>
    </form>
    <div style="clear:both"></div>';
}

function template_editarimagen() {
  global $context, $settings, $db_prefix, $boardurl;

  $id = (int) $_REQUEST['id'];

  $request = db_query("
    SELECT ID_PICTURE, title, filename
    FROM {$db_prefix}gallery_pic
    WHERE ID_PICTURE = " . $id, __FILE__, __LINE__);

  $row = mysqli_fetch_assoc($request);
  $causa = htmlentities(addslashes($_POST['causa']), ENT_QUOTES, 'UTF-8');

  echo '
    <script language="JavaScript" type="text/javascript">
      function requerido(title, filename) {
        if(title == \'\') {
          alert(\'No has escrito el titulo de la imagen.\');
          return false;
        }

        if(filename == \'\') {
          alert(\'No has agregado ning\xfan enlace de imagen.\');
          return false;
        }

      }
    </script>';

  menu();

  echo '
    <form method="POST" enctype="multipart/form-data" name="forma2" id="forma2" action="' . $boardurl . '/imagenes/editar/enviar/">
      <div class="box_780" style="float: left; margin-bottom: 8px;">
        <div class="box_title" style="width: 778px;">
          <div class="box_txt box_780-34">
            <center>Editar imagen</center>
          </div>
          <div class="box_rss">
            <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 16px; height: 16px;" border="0" />
          </div>
        </div>
        <div class="windowbg" border="0" style="width: 770px; padding: 4px;">
          <center>';

  if ($context['allow_admin']) {
    echo '
      <b class="size11">Causa de la edici&oacute;n:</b>
      <br />
      <input  tabindex="1" size="60" maxlength="54" type="text" onfocus="foco(this);" onblur="no_foco(this);" name="causa" />
      <br /><br />';
  }

  echo '
            <b class="size11">Titulo:</b>
            <br />
            <input  tabindex="1" size="60" maxlength="54" type="text" onfocus="foco(this);" onblur="no_foco(this);" name="title" id="title" value="' . $row['title'] . '"/>
            <br /><br />
            <b class="size11">URL de la imagen:</b>
            &nbsp;
            <br />
            <input type="text" onfocus="foco(this);" onblur="no_foco(this);" tabindex="2" size="60" name="filename" value="' . $row['filename'] . '" />
            <hr />
            <b class="size11" style="color: red;">* Si la imagen contiene pornograf&iacute;a, es morboso. Se borrar&aacute;.</b>
            <br />
            <input type="submit" tabindex="3" class="button" style="font-size: 15px;" onclick="return requerido(this.form.title.value, this.form.filename.value);" value="Editar imagen" name="submit" />
          </center>
        </div>
      </div>
      <input type="hidden" name="id" value="' . $row['ID_PICTURE'] . '" />
    </form>
    <div style="clear:both"></div>';
}

function template_misnotas() {
  global $context, $settings, $txt, $ID_MEMBER, $modSettings, $boardurl, $db_prefix;

  menu();

  if (!empty($context['nojs']['id'])) {
    echo '
      <div style="float: left;">
        <div class="box_780">
          <div class="box_title" style="width: 772px;">
            <div class="box_txt box_780-34">
              <center>Mi nota</center>
            </div>
            <div class="box_rss">
              <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 16px; height: 16px;" border="0" />
            </div>
          </div>
          <div class="windowbg" border="0" style="width: 764px; padding: 4px;">
            <form action="' . $boardurl . '/mis-notas/editando/" method="post" accept-charset="' . $context['character_set'] . '" enctype="multipart/form-data">
              <input type="text" title="' . $txt['notes_add_new_title'] . '" onfocus="if(this.value==\'' . $txt['notes_add_new_title'] . '\') this.value=\'\'; foco(this);" onblur="if(this.value==\'\') this.value=\'' . $txt['notes_add_new_title'] . '\'; no_foco(this);" value="' . strip_tags($context['nojs']['subject']) . '" style="width: 758px; font-family: arial; font-size: 12px;" name="titulo" id="titulo" maxlength="60" />
              <br />
              <textarea name="contenido" id="contenido" style="width: 758px; height: 185px; font-family: arial; font-size: 12px;" title="' . $txt['notes_add_new_text'] . '" onfocus="if(this.value==\'' . $txt['notes_add_new_text'] . '\') this.value=\'\'; foco(this);" onblur="if(this.value==\'\') this.value=\'' . $txt['notes_add_new_text'] . '\'; no_foco(this);">' . $context['nojs']['body'] . '</textarea>
              <br />
              <p align="right" style="margin: 0px; padding: 0px;">
                <input type="button" value="Salir sin guardar" onclick="location.href=\'' . $boardurl . '/mis-notas/\'" class="login" />
                &nbsp;
                <input type="submit" value="Salir y guardar" name="editar" class="login" />
                &nbsp;
                <input type="button" value="Eliminar nota" onclick="if (!confirm(\'\xbfEstas seguro que desea eliminar esta nota?\')) return false;location.href=\'' . $boardurl . '/mis-notas/eliminar-' . $context['nojs']['id'] . '\'" class="login" />
                <input type="hidden" name="id" value="' . $context['nojs']['id'] . '" />
                <input type="hidden" name="sa" value="edit" />
              </p>
            </form>
          </div>
        </div>
      </div>
      <div style="clear:both"></div>';
  } else {
    $end = $modSettings['notes'];
    $page = (int) $_GET['pag'];

    if (isset($page)) {
      $start = ($page - 1) * $end;
      $actualPage = $page;
    } else {
      $start = 0;
      $actualPage = 1;
    }

    $query = "
      SELECT id_note, subject, body, posterTime
      FROM {$db_prefix}member_notes
      WHERE ID_MEMBER = $ID_MEMBER
      ORDER BY id_note DESC";
  
    // Registros paginados
    $request2 = db_query("
      {$query}
      LIMIT {$start}, {$end}", __FILE__, __LINE__);

    $count = mysqli_num_rows($request2);

    if ($count <= 0) {
      echo '
        <div style="float: left;">
          <div class="noesta" style="width: 774px;">No tienes notas agregadas.</div>';
    } else {
      echo '
        <div style="float: left;">
          <table class="linksList" style="width: 774px;">
            <thead align="center">
              <tr>
                <th style="text-align: left;">Nota</th>
                <th>Fecha</th>
                <th>Eliminar</th>
              </tr>
            </thead>
            <tbody>';

      while ($row = mysqli_fetch_assoc($request2)) {
        echo '
          <tr>
            <td style="text-align: left;">
              <a title="', parse_bbc(strip_tags($row['subject'])), '" href="' . $boardurl . '/mis-notas/ver-', $row['id_note'], '/">', parse_bbc(strip_tags($row['subject'])), '</a>
            </td>
            <td title="', timeformat($row['posterTime']), '">', timeformat($row['posterTime']), '</td>
            <td>
              <a title="Eliminar nota" onclick="if (!confirm(\'\xbfEstas seguro que desea eliminar esta nota?\')) return false;" href="' . $boardurl . '/mis-notas/eliminar-', $row['id_note'], '">
                <img alt="Eliminar nota" title="Eliminar nota" style="width:16px;height:16px;" src="', $settings['images_url'], '/icons/eliminar-notas.gif" />
              </a>
            </td>
          </tr>';
      }

      // Registros totales
      $request = db_query($query, __FILE__, __LINE__);
      $records = mysqli_num_rows($request);

      echo '
          </tbody>
        </table>';

      $previousPage = $actualPage - 1;
      $nextPage = $actualPage + 1;
      $lastPage = $records / $end;
      $residue = $records % $end;

      if ($residue > 0) {
        $lastPage = floor($lastPage) + 1;
      }

      echo '<div class="windowbgpag" style="width: 774px;">';

      if ($actualPage > 1) {
        echo '<a href="' . $boardurl . '/mis-notas/pag-' . $previousPage . '">&#171; anterior</a>';
      }

      if ($actualPage < $lastPage) {
        echo '<a href="' . $boardurl . '/mis-notas/pag-' . $nextPage . '">siguiente &#187;</a></div>';
      }
    }

    echo '
      <div style="width: 774px; margin-top: 4px;">
        <p align="right" style="margin: 0px; padding: 0px;">
          <input type="button" value="Agregar nota" onclick="location.href=\'' . $boardurl . '/mis-notas/agregar/\'" class="login" />
        </p>
      </div>';
  }

  echo '
      <div class="clearBoth"></div>
    </div>
    <div style="clear:both"></div>';
}

function template_agregarnota() {
  global $context, $settings, $txt, $boardurl;
  
  menu();

  echo '
    <div class="box_780" style="float: left;">
      <div class="box_title" style="width: 772px;">
        <div class="box_txt box_780-34">
          <center>Agregar nota</center>
        </div>
        <div class="box_rss">
          <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 16px; height: 16px;" border="0" />
        </div>
      </div>
      <div class="windowbg" border="0" style="width: 764px; padding: 4px;">
        <form action="' . $boardurl . '/mis-notas/enviar/" method="post" accept-charset="' . $context['character_set'] . '" enctype="multipart/form-data">
          <input type="text" title="' . $txt['notes_add_new_title'] . '" onfocus="if(this.value==\'' . $txt['notes_add_new_title'] . '\') this.value=\'\'; foco(this);" onblur="if(this.value==\'\') this.value=\'' . $txt['notes_add_new_title'] . '\'; no_foco(this);" value="' . $txt['notes_add_new_title'] . '" style="width: 758px; font-family: arial; font-size: 12px;" name="titulo" maxlength="60" id="titulo" />
          <br/>
          <textarea name="contenido" id="contenido" style="width: 758px; height: 185px; font-family: arial; font-size: 12px;" title="' . $txt['notes_add_new_text'] . '" onfocus="if(this.value==\'' . $txt['notes_add_new_text'] . '\') this.value=\'\'; foco(this);" onblur="if(this.value==\'\') this.value=\'' . $txt['notes_add_new_text'] . '\'; no_foco(this);">' . $txt['notes_add_new_text'] . '</textarea>
          <br />
          <p align="right" style="margin: 0px; padding: 0px;">
            <input type="submit" value="Crear nota" name="agregar" class="login" />
          </p>
          <input type="hidden" name="sa" value="add" />
          <input type="hidden" name="nojs" value="" />
          <input type="hidden" name="sc" value="' . $context['session_id'] . '" />
        </form>
      </div>
    </div>
    <div style="clear:both"></div>';
}

function template_summary() {
  global $context, $settings, $boardurl, $sourcedir;

  menu2();
  sidebar();
  menu3();

  $memberName = censorText($context['member']['name']);

  echo '
    <div style="float: left; margin-bottom: 8px;">
      <div class="mennes">
        <div class="botnes">
          <ul>
            <li>
              <a href="' . $boardurl . '/perfil/', $memberName, '/muro/" title="Muro" alt="Muro">
                <img src="' . $settings['images_url'] . '/icons/muro.gif" alt="Muro" title="Muro" />
                &nbsp;
                Muro
              </a>
            </li>
            <li>
              <a href="' . $boardurl . '/perfil/', $memberName, '/apariencia/" title="Apariencia" alt="Apariencia">
                <img src="' . $settings['images_url'] . '/user.gif" alt="Apariencia" title="Apariencia"/>
                &nbsp;
                Apariencia
              </a>
            </li>
            <li>
              <a href="' . $boardurl . '/perfil/', $memberName, '/comunidades/" title="Comunidades" alt="Comunidades">
                <img src="' . $settings['images_url'] . '/comunidades/comunidad.png" alt="Comunidades" title="Comunidades" />
                &nbsp;
                Comunidades
              </a>
            </li>
          </ul>
          <div style="clear: both;">
        </div>
      </div>
    </div>
    <div class="clearBoth"></div>';

  if ($memberName == $context['user']['name']) {
    echo '
      <div style="margin-bottom: 8px;">
        <div class="box_title" style="width: 539px;">
          <div class="box_txt">En este momento estoy</div>
          <div class="box_rss">
            <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 14px; height: 12px;" border="0" />
          </div>
        </div>
        <div class="windowbg" style="font-size: 11px; width: 523px; padding: 8px;">
          <center>';

    // TO-DO: Cambiar forma de pintar estados
    if($context['member']['estado_icon'] == 'mcontento') { echo '<u><b>'; }
    echo '<a href="' . $boardurl . '/estado/mcontento/" title="Muy contento/a">Muy contento/a</a>';
    if($context['member']['estado_icon'] == 'mcontento') { echo '</u></b>';}

    echo ' - ';

    if($context['member']['estado_icon'] == 'contento') { echo '<u><b>'; }
    echo '<a href="' . $boardurl . '/estado/contento/" title="Contento/a">Contento/a</a>';
    if($context['member']['estado_icon'] == 'contento') { echo '</u></b>'; }

    echo ' - ';

    if($context['member']['estado_icon'] == 'sueno') { echo '<u><b>'; }
    echo '<a href="' . $boardurl . '/estado/cons/" title="Con sue&ntilde;o">Con sue&ntilde;o</a>';
    if($context['member']['estado_icon'] == 'sueno') { echo '</u></b>'; }
    
    echo ' - ';
    
    if($context['member']['estado_icon'] == 'descansar') { echo '<u><b>'; }
    echo '<a href="' . $boardurl . '/estado/desc/" title="Descansando">Descansando</a>';
    if($context['member']['estado_icon'] == 'descansar') { echo '</u></b>'; }
    
    echo ' - ';
    
    if($context['member']['estado_icon'] == 'triste') { echo '<u><b>'; }
    echo '<a href="' . $boardurl . '/estado/triste/" title="Triste">Triste</a>';
    if($context['member']['estado_icon'] == 'triste') { echo '</u></b>'; }
    
    echo ' - ';
    
    if($context['member']['estado_icon'] == 'enferm') { echo '<u><b>'; }
    echo '<a href="' . $boardurl . '/estado/enferm/" title="Enfermo/a">Enfermo/a</a>';
    if($context['member']['estado_icon'] == 'enferm') { echo '</u></b>'; }
    
    echo ' - ';
    
    if($context['member']['estado_icon'] == 'emusic') { echo '<u><b>'; }
    echo '<a href="' . $boardurl . '/estado/emusic/" title="Escuchando m&uacute;sica">Escuchando m&uacute;sica</a>';
    if($context['member']['estado_icon'] == 'emusic') { echo '</u></b>'; }
    
    echo '<br />';
    
    if($context['member']['estado_icon'] == '') { echo '<u><b>'; }
    echo '<a href="' . $boardurl . '/estado/qestado/" title="Sin estado">Sin estado</a>';
    if($context['member']['estado_icon'] == '') { echo '</u></b>'; }
    
    echo '
          </center>
        </div>
      </div>
      <div style="background: url(\'' . $settings['images_url'] . '/quehago.png\') no-repeat; width: 525px; height: 39px; padding: 8px 8px 0px 8px;">
        <div style="float: left; margin-right: 3px;">
          <input title="&#191;Qu&eacute; est&aacute;s haciendo ahora&#63;" onfocus="if(this.value==\'&#191;Qu&eacute; est&aacute;s haciendo ahora&#63;\') this.value=\'\';foco(this);" onblur="if(this.value==\'\') this.value=\'&#191;Qu&eacute; est&aacute;s haciendo ahora&#63;\';no_foco(this);" style="width: 463px; font-size: 11px; font-family: Arial, FreeSans;" name="quehago" id="quehago" value="&#191;Qu&eacute; est&aacute;s haciendo ahora&#63;" type="text" />
        </div>
        <div style="padding-top: 1px;">
          <input class="login" style="padding: 0px; margin: 0px; font-size: 11px; width: 53px;" value="Publicar" onclick="add_quehago(); return false;" type="button" id="button_add_quehago" />
        </div>
      </div>';
  }

  if ($context['user']['is_logged']) {
    echo '
      <div style="background:url(\'' . $settings['images_url'] . '/muro.png\') no-repeat; width: 525px; height: 55px; padding: 8px 8px 0px 8px;">
        <div style="float: left; margin-right: 3px;">
          <textarea title="Escribe algo..." onfocus="if(this.value==\'Escribe algo...\') this.value=\'\';foco(this);" onblur="if(this.value==\'\') this.value=\'Escribe algo...\';no_foco(this);" style="height: 30px; overflow: visible; width: 463px; font-size: 11px; font-family: Arial, FreeSans;" name="muro" id="muro">Escribe algo...</textarea>
        </div>
        <div style="padding-top: 1px;">
          <input class="login" style="padding: 0px; margin: 0px; font-size: 11px; width: 53px;" value="Publicar" onclick="add_muro(\'', $context['member']['id'], '\'); return false;" type="button" id="button_add_muro" />
          <img alt="" src="' . $settings['images_url'] . '/icons/cargando.gif" style="width: 16px; height: 16px; display: none;" id="gif_cargando_add_muro" border="0">
        </div>
      </div>
      <div style="width: 541px; margin-bottom: 4px;">
        <div class="msg_add_muro"></div>
      </div>';
  } else {
    echo '
      <div style="clear: left;">
        <div class="noesta-am" style="width: 541px; margin-bottom: 8px;">
          Para poder comentar en este muro es necesario estar <a href="' . $boardurl . '/registrarse/" style="color: #FFB600;" title="Reg&iacute;strarse">Registrado</a>.
          <br />
          Si ya tienes usuario <a href="' . $boardurl . '/ingresar/" style="color:#FFB600;" title="Conectarse">&iexcl;Con&eacute;ctate!</a>
        </div>
      </div>';
  }

  echo '
    <div class="windowbg" style="border-top: #D7CFC6 solid 1px; width: 523px; padding: 8px; font-size: 11px;">
      <div id="return_agregar_muro"></div>';

  require_once($sourcedir . '/ProfileComments.php');

  ShowUserBox($memCommID);

  menu4();
  menu5();
}

function ver_apariencia() {
  global $context;

  echo '
    <p class="datosp">Mide:</p>
    &nbsp;
    ' . (empty($context['member']['altura']) ? 'Sin datos' : $context['member']['altura']) . '
    &nbsp;
    cent&iacute;metros
    <br /><br />
    <p class="datosp">Pesa:</p>
    &nbsp;
    ' . (empty($context['member']['peso']) ? 'Sin datos' : $context['member']['peso']) . '
    &nbsp;
    kilos
    <br /><br />
    <p class="datosp">Su color de pelo:</p>
    &nbsp;
    ' . (empty($context['member']['pelo_color']) ? 'Sin datos' : pelo_color($context['member']['pelo_color'])) . '
    <br /><br />
    <p class="datosp">Su color de ojos:</p>
    &nbsp;
    ' . (empty($context['member']['ojos_color']) ? 'Sin datos' : ojos_color($context['member']['ojos_color'])) . '
    <br /><br />
    <p class="datosp">Su f&iacute;sico:</p>
    &nbsp;
    ' . (empty($context['member']['fisico']) ? 'Sin datos' : fisico($context['member']['fisico'])) . '
    <br /><br />
    <p class="datosp">Su dieta es:</p>
    &nbsp;
    ' . (empty($context['member']['dieta']) ? 'Sin datos' : dieta($context['member']['dieta'])) . '
    <br /><br />
    <p class="datosp">Fuma:</p>
    &nbsp;
    ' . (empty($context['member']['fumo']) ? 'Sin datos' : fumo($context['member']['fumo'])) . '
    <br /><br />
    <p class="datosp">Toma alcohol:</p>
    &nbsp;
    ' . (empty($context['member']['tomo_alcohol']) ? 'Sin datos' : fumo($context['member']['tomo_alcohol'])) . '
    <br /><br />
    <p class="datosp">Le gustar&iacute;a:</p>
    &nbsp;
    ' . (empty($context['member']['me_gustaria']) ? 'Sin datos' : me_gustaria($context['member']['me_gustaria'])) . '
    <br /><br />
    <p class="datosp">En el amor est&aacute;:</p>
    &nbsp;
    ' . (empty($context['member']['estado']) ? 'Sin datos' : estado($context['member']['estado'])) . '
    <br /><br />
    <p class="datosp">Hijos:</p>
    &nbsp;
    ' . (empty($context['member']['hijos']) ? 'Sin datos' : hijos($context['member']['hijos'])) . '
    <br /><br />
    <p class="datosp">Sus estudios:</p>
    &nbsp;
    ' . (empty($context['member']['estudios']) ? 'Sin datos' : estudios($context['member']['estudios'])) . '
    <br /><br />
    <p class="datosp">Profesi&oacute;n:</p>
    &nbsp;
    ' . (empty($context['member']['profesion']) ? 'Sin datos' : $context['member']['profesion']) . '
    <br /><br />
    <p class="datosp">Empresa:</p>
    &nbsp;
    ' . (empty($context['member']['empresa']) ? 'Sin datos' : $context['member']['empresa']) . '
    <br /><br />
    <p class="datosp">Su nivel de ingresos:</p>
    &nbsp;
    ' . (empty($context['member']['ingresos']) ? 'Sin datos' : ingresos($context['member']['ingresos'])) . '
    <br /><br />
    <p class="datosp">Intereses profesionales:</p>
    &nbsp;
    ' . (empty($context['member']['intereses_profesionales']) ? 'Sin datos' : $context['member']['intereses_profesionales']) . '
    <br /><br />
    <p class="datosp">Habilidades profesionales:</p>
    &nbsp;
    ' . (empty($context['member']['habilidades_profesionales']) ? 'Sin datos' : $context['member']['habilidades_profesionales']) . '
    <br /><br />
    <p class="datosp">Intereses:</p>
    &nbsp;
    ' . (empty($context['member']['mis_intereses']) ? 'Sin datos' : $context['member']['mis_intereses']) . '
    <br /><br />
    <p class="datosp">Hobbies:</p>
    &nbsp;
    ' . (empty($context['member']['hobbies']) ? 'Sin datos' : $context['member']['hobbies']) . '
    <br /><br />
    <p class="datosp">Series de Tv favoritas:</p>
    &nbsp;
    ' . (empty($context['member']['series_tv_favoritas']) ? 'Sin datos' : $context['member']['series_tv_favoritas']) . '
    <br /><br />
    <p class="datosp">M&uacute;sica favorita:</p>
    &nbsp;
    ' . (empty($context['member']['musica_favorita']) ? 'Sin datos' : $context['member']['musica_favorita']) . '
    <br /><br />
    <p class="datosp">Deportes y equipos:</p>
    &nbsp;
    ' . (empty($context['member']['deportes_y_equipos_favoritos']) ? 'Sin datos' : $context['member']['deportes_y_equipos_favoritos']) . '
    <br /><br />
    <p class="datosp">Libros favoritos:</p>
    &nbsp;
    ' . (empty($context['member']['libros_favoritos']) ? 'Sin datos' : $context['member']['libros_favoritos']) . '
    <br /><br />
    <p class="datosp">Pel&iacute;culas favoritas:</p>
    &nbsp;
    ' . (empty($context['member']['peliculas_favoritas']) ? 'Sin datos' : $context['member']['peliculas_favoritas']) . '
    <br /><br />
    <p class="datosp">Comida favorita:</p>
    &nbsp;
    ' . (empty($context['member']['comida_favorita']) ? 'Sin datos' : $context['member']['comida_favorita']) . '
    <br /><br />
    <p class="datosp">Sus h&eacute;roes son:</p>
    &nbsp;
    ' . (empty($context['member']['mis_heroes_son']) ? 'Sin datos' : $context['member']['mis_heroes_son']) . '
    <br /><br />';
}

function template_apariencia() {
  global $context, $settings, $boardurl, $db_prefix;

  menu2();
  sidebar();
  menu3();

  $memberName = censorText($context['member']['name']);

  echo '
    <div style="float: left; margin-bottom: 8px;">
      <div class="mennes">
        <div class="botnes">
          <ul>
            <li>
              <a href="' . $boardurl . '/perfil/' . $memberName . '/muro/" title="Muro" alt="Muro">
                <img src="' . $settings['images_url'] . '/icons/muro.gif" alt="Muro" title="Muro" />
                &nbsp;
                Muro
              </a>
            </li>
            <li>
              <a href="' . $boardurl . '/perfil/' . $memberName . '/apariencia/" title="Apariencia" alt="Apariencia">
                <img src="' . $settings['images_url'] . '/user.gif" alt="Apariencia" title="Apariencia"/>
                &nbsp;
                Apariencia
              </a>
            </li>
            <li>
              <a href="' . $boardurl . '/perfil/' . $memberName . '/comunidades/" title="Comunidades" alt="Comunidades">
                <img src="' . $settings['images_url'] . '/comunidades/comunidad.png" alt="Comunidades" title="Comunidades" />
                &nbsp;
                Comunidades
              </a>
            </li>
          </ul>
          <div style="clear: both;"></div>
        </div>
      </div>
      <div class="clearBoth"></div>
      <div class="windowbg" style="border-top: 1px solid #D7CFC6; width: 523px; padding: 8px; font-size: 12px;">';

  if ($memberName == $context['user']['name'] || $context['allow_admin']) {
    ver_apariencia();
  } else if ($context['member']['quienve'] == '0') {
    ver_apariencia();
  } else if ($context['member']['quienve'] == '1') {
    echo '<div class="noesta">No puedes ver la apariencia de ' . $memberName . '.</div>';
  } else if ($context['member']['quienve'] == '2') {
    $request = db_query("
      SELECT *
      FROM {$db_prefix}buddies
      WHERE BUDDY_ID = " . $context['user']['id'], __FILE__, __LINE__);

    $row = mysqli_num_rows($request);

    if ($row > 0) {
      ver_apariencia();
    } else if ($row <= 0) {
      echo '<div class="noesta">S&oacute;lo amigos de ' . $memberName . ' pueden ver la apariencia.</div>';
    }
  } else if ($context['member']['quienve'] == '3') {
    if($context['user']['is_logged']) {
      ver_apariencia();
    } else {
      echo '<div class="noesta">S&oacute;lo usuarios registrados pueden ver la apariencia de ' . $memberName . '.</div>';
    }
  }

  echo '
        </div>
      </div>
    </div>';

  menu4();
  menu5();
}

function template_comunidades() {
  global $context, $settings, $boardurl;

  menu2();
  sidebar();
  menu3();

  $memberName = censorText($context['member']['name']);

  echo '
    <div style="float: left; margin-bottom: 8px;">
      <div class="mennes">
        <div class="botnes">
          <ul>
            <li>
              <a href="' . $boardurl . '/perfil/' . $memberName . '/muro/" title="Muro" alt="Muro">
                <img src="' . $settings['images_url'] . '/icons/muro.gif" alt="Muro" title="Muro" />
                &nbsp;
                Muro</a>
              </li>
            <li>
              <a href="' . $boardurl . '/perfil/' . $memberName . '/apariencia/" title="Apariencia" alt="Apariencia">
                <img src="' . $settings['images_url'] . '/user.gif" alt="Apariencia" title="Apariencia"/>
                &nbsp;
                Apariencia
              </a>
            </li>
            <li>
              <a href="' . $boardurl . '/perfil/' . $memberName . '/comunidades/" title="Comunidades" alt="Comunidades">
                <img src="' . $settings['images_url'] . '/comunidades/comunidad.png" alt="Comunidades" title="Comunidades" />
                &nbsp;
                Comunidades
              </a>
            </li>
          </ul>
          <div style="clear: both;"></div>
        </div>
      </div>
      <div class="clearBoth"></div>';

  // Últimos temas creados
  @require_once($_SERVER['DOCUMENT_ROOT'] . '/web/tp-ComuTemPerfil.php');
  echo '<div style="border: 1px solid #517BA1; background: #517BA1; height: 2px; margin-top: 10px; margin-bottom: 10px;" class="hrs"></div>';

  // Comunidades creadas
  @require_once($_SERVER['DOCUMENT_ROOT'] . '/web/tp-ComuCrePerfil.php');
  echo '<div style="border: 1px solid #517BA1; background: #517BA1; height: 2px; margin-top: 10px; margin-bottom: 10px;" class="hrs"></div>';

  // Es miembro de las comunidades
  @require_once($_SERVER['DOCUMENT_ROOT'] . '/web/tp-ComuMemPerfil.php');

  echo '
      <div class="clearBoth"></div>
    </div>';

  menu4();
  menu5();
}

// Template for showing all the buddies of the current user.
function template_editBuddies() {
  global $context, $settings, $scripturl, $txt;

  echo '
    <table border="0" width="85%" cellspacing="1" cellpadding="4" class="bordercolor" align="center">
      <tr class="titlebg">
        <td colspan="8" height="26">
          &nbsp;<img src="', $settings['images_url'], '/icons/profile_sm.gif" alt="" align="top" />&nbsp;', $txt['editBuddies'], '
        </td>
      </tr>
      <tr class="catbg3">
        <td width="20%">', $txt[68], '</td>
        <td>', $txt['online8'], '</td>
        <td>', $txt[69], '</td>
        <td align="center">', $txt[513], '</td>
        <td align="center">', $txt[603], '</td>
        <td align="center">', $txt[604], '</td>
        <td align="center">', $txt['MSN'], '</td>
        <td></td>
      </tr>';

  // If they don't have any buddies don't list them!
  if (empty($context['buddies']))
    echo '
      <tr class="windowbg">
        <td colspan="8" align="center"><b>', $txt['no_buddies'], '</b></td>
      </tr>';

  // Now loop through each buddy showing info on each.
  $alternate = false;
  foreach ($context['buddies'] as $buddy)
  {
    echo '
      <tr class="', $alternate ? 'windowbg' : 'windowbg2', '">
        <td>', $buddy['link'], '</td>
        <td align="center"><a href="', $buddy['online']['href'], '"><img src="', $buddy['online']['image_href'], '" alt="', $buddybuddy['online']['label'], '" title="', $buddy['online']['label'], '" /></a></td>
        <td align="center">', ($buddy['hide_email'] ? '' : '<a href="mailto:' . $buddy['email'] . '"><img src="' . $settings['images_url'] . '/email_sm.gif" alt="' . $txt[69] . '" title="' . $txt[69] . ' ' . $buddy['name'] . '" /></a>'), '</td>
        <td align="center">', $buddy['icq']['link'], '</td>
        <td align="center">', $buddy['aim']['link'], '</td>
        <td align="center">', $buddy['yim']['link'], '</td>
        <td align="center">', $buddy['msn']['link'], '</td>
        <td align="center"><a href="', $scripturl, '?action=profile;u=', $context['member']['id'], ';sa=editBuddies;remove=', $buddy['id'], '"><img src="', $settings['images_url'], '/icons/delete.gif" alt="', $txt['buddy_remove'], '" title="', $txt['buddy_remove'], '" /></a></td>
      </tr>';

    $alternate = !$alternate;
  }

  echo '
    </table>';

  // Add a new buddy?
  echo '
  <br />
  <form action="', $scripturl, '?action=profile;u=', $context['member']['id'], ';sa=editBuddies" method="post" accept-charset="', $context['character_set'], '">
    <table width="65%" cellpadding="4" cellspacing="0" class="tborder" align="center">
      <tr class="titlebg">
        <td colspan="2">', $txt['buddy_add'], '</td>
      </tr>
      <tr class="windowbg">
        <td width="45%">
          <b>', $txt['who_member'], ':</b>
        </td>
        <td width="55%">
          <input type="text" name="new_buddy" id="new_buddy" size="25" />
          <a href="', $scripturl, '?action=findmember;input=new_buddy;quote=1;sesc=', $context['session_id'], '" onclick="return reqWin(this.href, 350, 400);"><img src="', $settings['images_url'], '/icons/assist.gif" alt="', $txt['find_members'], '" align="top" /></a>
        </td>
      </tr>
      <tr class="windowbg">
        <td colspan="2" align="right">
          <input type="submit" value="', $txt['buddy_add_button'], '" />
        </td>
      </tr>
    </table>
  </form>';
}

// This template shows an admin information on a users IP addresses used and errors attributed to them.
function template_trackUser() {
  global $context, $scripturl, $txt;

  // The first table shows IP information about the user.
  echo '
    <table cellpadding="0" cellspacing="0" border="0" class="bordercolor" align="center" width="90%"><tr><td>
      <table border="0" cellspacing="1" cellpadding="4" align="left" width="100%">
        <tr class="titlebg">
          <td colspan="2">
            <b>', $txt['view_ips_by'], ' ', $context['member']['name'], '</b>
          </td>
        </tr>';

  // The last IP the user used.
  echo '
        <tr>
          <td class="windowbg2" align="left" width="200">', $txt['most_recent_ip'], ':</td>
          <td class="windowbg2" align="left">
            <a href="', $scripturl, '?action=trackip;searchip=', $context['last_ip'], ';">', $context['last_ip'], '</a>
          </td>
        </tr>';

  // Lists of IP addresses used in messages / error messages.
  echo '
        <tr>
          <td class="windowbg2" align="left">', $txt['ips_in_messages'], ':</td>
          <td class="windowbg2" align="left">
            ', (count($context['ips']) > 0 ? implode(', ', $context['ips']) : '(' . $txt['none'] . ')'), '
          </td>
        </tr><tr>
          <td class="windowbg2" align="left">', $txt['ips_in_errors'], ':</td>
          <td class="windowbg2" align="left">
            ', (count($context['error_ips']) > 0 ? implode(', ', $context['error_ips']) : '(' . $txt['none'] . ')'), '
          </td>
        </tr>';

  // List any members that have used the same IP addresses as the current member.
  echo '
        <tr>
          <td class="windowbg2" align="left">', $txt['members_in_range'], ':</td>
          <td class="windowbg2" align="left">
            ', (count($context['members_in_range']) > 0 ? implode(', ', $context['members_in_range']) : '(' . $txt['none'] . ')'), '
          </td>
        </tr>
      </table>
    </td></tr></table>
    <br />';

  // The second table lists all the error messages the user has caused/received.
  echo '
    <table cellpadding="0" cellspacing="0" border="0" class="bordercolor" align="center" width="90%"><tr><td>
      <table border="0" cellspacing="1" cellpadding="4" align="center" width="100%">
        <tr class="titlebg">
          <td colspan="4">
            ', $txt['errors_by'], ' ', $context['member']['name'], '
          </td>
        </tr><tr class="windowbg">
          <td class="smalltext" colspan="4" style="padding: 2ex;">
            ', $txt['errors_desc'], '
          </td>
        </tr><tr class="titlebg">
          <td colspan="4">
            ', $txt[139], ': ', $context['page_index'], '
          </td>
        </tr><tr class="catbg3">
          <td>', $txt['ip_address'], '</td>
          <td>', $txt[72], '</td>
          <td>', $txt[317], '</td>
        </tr>';

  // If there arn't any messages just give a message stating this.
  if (empty($context['error_messages']))
    echo '
        <tr><td class="windowbg2" colspan="4"><i>', $txt['no_errors_from_user'], '</i></td></tr>';

  // Otherwise print every error message out.
  else
    // For every error message print the IP address that caused it, the message displayed and the date it occurred.
    foreach ($context['error_messages'] as $error)
      echo '
        <tr>
          <td class="windowbg2">
            <a href="', $scripturl, '?action=trackip;searchip=', $error['ip'], ';">', $error['ip'], '</a>
          </td>
          <td class="windowbg2">
            ', $error['message'], '<br />
            <a href="', $error['url'], '">', $error['url'], '</a>
          </td>
          <td class="windowbg2">', $error['time'], '</td>
        </tr>';
  echo '
      </table>
    </td></tr></table>';
}

// The template for trackIP, allowing the admin to see where/who a certain IP has been used.
function template_trackIP() {
  global $context, $scripturl, $txt;

  // This function always defaults to the last IP used by a member but can be set to track any IP.
  echo '
    <form action="', $scripturl, '?action=trackip" method="post" accept-charset="', $context['character_set'], '">';

  // The first table in the template gives an input box to allow the admin to enter another IP to track.
  echo '
      <table cellpadding="0" cellspacing="0" border="0" class="bordercolor" align="center" width="90%"><tr><td>
        <table border="0" cellspacing="1" cellpadding="4" align="center" width="100%">
          <tr class="titlebg">
            <td>', $txt['trackIP'], '</td>
          </tr><tr>
            <td class="windowbg2">
              ', $txt['enter_ip'], ':&nbsp;&nbsp;<input type="text" name="searchip" value="', $context['ip'], '" />&nbsp;&nbsp;<input type="submit" value="', $txt['trackIP'], '" />
            </td>
          </tr>
        </table>
      </td></tr></table>
    </form>
    <br />';

  // The table inbetween the first and second table shows links to the whois server for every region.
  if ($context['single_ip'])
  {
    echo '
    <table cellpadding="0" cellspacing="0" border="0" class="bordercolor" align="center" width="90%"><tr><td>
      <table border="0" cellspacing="1" cellpadding="4" align="center" width="100%">
        <tr class="titlebg">
          <td colspan="2">
            ', $txt['whois_title'], ' ', $context['ip'], '
          </td>
        </tr><tr>
          <td class="windowbg2">';
    foreach ($context['whois_servers'] as $server)
      echo '
            <a href="', $server['url'], '" target="_blank"', isset($context['auto_whois_server']) && $context['auto_whois_server']['name'] == $server['name'] ? ' style="font-weight: bold;"' : '', '>', $server['name'], '</a><br />';
    echo '
          </td>
        </tr>
      </table>
    </td></tr></table>
    <br />';
  }

  // The second table lists all the members who have been logged as using this IP address.
  echo '
    <table cellpadding="0" cellspacing="0" border="0" class="bordercolor" align="center" width="90%"><tr><td>
      <table border="0" cellspacing="1" cellpadding="4" align="center" width="100%">
        <tr class="titlebg">
          <td colspan="2">
            ', $txt['members_from_ip'], ' ', $context['ip'], '
          </td>
        </tr><tr class="catbg3">
          <td>', $txt['ip_address'], '</td>
          <td>', $txt['display_name'], '</td>
        </tr>';
  if (empty($context['ips']))
    echo '
        <tr><td class="windowbg2" colspan="2"><i>', $txt['no_members_from_ip'], '</i></td></tr>';
  else
    // Loop through each of the members and display them.
    foreach ($context['ips'] as $ip => $memberlist)
      echo '
        <tr>
          <td class="windowbg2"><a href="', $scripturl, '?action=trackip;searchip=', $ip, ';">', $ip, '</a></td>
          <td class="windowbg2">', implode(', ', $memberlist), '</td>
        </tr>';
  echo '
      </table>
    </td></tr></table>
    <br />';

  // The third table in the template displays a list of all the messages sent using this IP (can be quite long).
  echo '
    <table cellpadding="0" cellspacing="0" border="0" class="bordercolor" align="center" width="90%"><tr><td>
      <table border="0" cellspacing="1" cellpadding="4" align="center" width="100%">
        <tr class="titlebg">
          <td colspan="4">
            ', $txt['messages_from_ip'], ' ', $context['ip'], '
          </td>
        </tr><tr class="windowbg">
          <td class="smalltext" colspan="4" style="padding: 2ex;">
            ', $txt['messages_from_ip_desc'], '
          </td>
        </tr><tr class="titlebg">
          <td colspan="4">
            <b>', $txt[139], ':</b> ', $context['message_page_index'], '
          </td>
        </tr><tr class="catbg3">
          <td>', $txt['ip_address'], '</td>
          <td>', $txt['rtm8'], '</td>
          <td>', $txt[319], '</td>
          <td>', $txt[317], '</td>
        </tr>';

  // No message means nothing to do!
  if (empty($context['messages']))
    echo '
        <tr><td class="windowbg2" colspan="4"><i>', $txt['no_messages_from_ip'], '</i></td></tr>';
  else
    // For every message print the IP, member who posts it, subject (with link) and date posted.
    foreach ($context['messages'] as $message)
      echo '
        <tr>
          <td class="windowbg2">
            <a href="', $scripturl, '?action=trackip;searchip=', $message['ip'], '">', $message['ip'], '</a>
          </td>
          <td class="windowbg2">
            ', $message['member']['link'], '
          </td>
          <td class="windowbg2">
            <a href="', $scripturl, '?topic=', $message['topic'], '.msg', $message['id'], '#msg', $message['id'], '">
              ', $message['subject'], '
            </a>
          </td>
          <td class="windowbg2">', $message['time'], '</td>
        </tr>';
  echo '
      </table>
    </td></tr></table>
    <br />';

  // The final table in the template lists all the error messages caused/received by anyone using this IP address.
  echo '
    <table cellpadding="0" cellspacing="0" border="0" class="bordercolor" align="center" width="90%"><tr><td>
      <table border="0" cellspacing="1" cellpadding="4" align="center" width="100%">
        <tr class="titlebg">
          <td colspan="4">
            ', $txt['errors_from_ip'], ' ', $context['ip'], '
          </td>
        </tr><tr class="windowbg">
          <td class="smalltext" colspan="4" style="padding: 2ex;">
            ', $txt['errors_from_ip_desc'], '
          </td>
        </tr><tr class="titlebg">
          <td colspan="4">
            ', $txt[139], ': ', $context['error_page_index'], '
          </td>
        </tr><tr class="catbg3">
          <td>', $txt['ip_address'], '</td>
          <td>', $txt['display_name'], '</td>
          <td>', $txt[72], '</td>
          <td>', $txt[317], '</td>
        </tr>';
  if (empty($context['error_messages']))
    echo '
        <tr><td class="windowbg2" colspan="4"><i>', $txt['no_errors_from_ip'], '</i></td></tr>';
  else
    // For each error print IP address, member, message received and date caused.
    foreach ($context['error_messages'] as $error)
      echo '
        <tr>
          <td class="windowbg2">
            <a href="', $scripturl, '?action=trackip;searchip=', $error['ip'], '">', $error['ip'], '</a>
          </td>
          <td class="windowbg2">
            ', $error['member']['link'], '
          </td>
          <td class="windowbg2">
            ', $error['message'], '<br />
            <a href="', $error['url'], '">', $error['url'], '</a>
          </td>
          <td class="windowbg2">', $error['error_time'], '</td>
        </tr>';
  echo '
      </table>
    </td></tr></table>';
}

function template_account() {
  global $context, $settings, $scripturl, $modSettings, $txt;

  // Javascript for checking if password has been entered / taking admin powers away from themselves.
  echo '
    <script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[
      function checkProfileSubmit()
      {';

  // If this part requires a password, make sure to give a warning.
  if ($context['user']['is_owner'] && $context['require_password'])
    echo '
        // Did you forget to type your password?
        if (document.forms.creator.oldpasswrd.value == "")
        {
          alert("', $txt['smf244'], '");
          return false;
        }';

  // This part checks if they are removing themselves from administrative power on accident.
  if ($context['allow_edit_membergroups'] && $context['user']['is_owner'] && $context['member']['group'] == 1)
    echo '
        if (typeof(document.forms.creator.ID_GROUP) != "undefined" && document.forms.creator.ID_GROUP.value != "1")
          return confirm("', $txt['deadmin_confirm'], '");';

  echo '
        return true;
      }
    // ]]></script>';

  // The main containing header.
  echo '
    <form action="', $scripturl, '?action=profile2" method="post" accept-charset="', $context['character_set'], '" name="creator" id="creator" onsubmit="return checkProfileSubmit();">
      <table border="0" width="85%" cellspacing="1" cellpadding="4" align="center" class="bordercolor">
        <tr class="titlebg">
          <td height="26">
            &nbsp;<img src="', $settings['images_url'], '/icons/profile_sm.gif" alt="" align="top" />&nbsp;
            ', $txt[79], '
          </td>
        </tr>';

  // Display Name, language and date user registered.
  echo '
        <tr class="windowbg">
          <td class="smalltext" height="25" style="padding: 2ex;">
            ', $txt['account_info'], '
          </td>
        </tr>
        <tr>
          <td class="windowbg2" style="padding-bottom: 2ex;">
            <table width="100%" cellpadding="3" cellspacing="0" border="0">';

  // Only show these settings if you're allowed to edit the account itself (not just the membergroups).
  if ($context['allow_edit_account'])
  {
    if ($context['user']['is_admin'] && !empty($context['allow_edit_username']))
      echo '
              <tr>
                <td colspan="2" align="center" style="color: red">', $txt['username_warning'], '</td>
              </tr>
              <tr>
                <td width="40%">
                  <b>', $txt[35], ': </b>
                </td>
                <td>
                  <input type="text" name="memberName" size="30" value="', $context['member']['username'], '" />
                </td>
              </tr>';
    else
      echo '
              <tr>
                <td width="40%">
                  <b>', $txt[35], ': </b>', $context['user']['is_admin'] ? '
                  <div class="smalltext">(<a href="' . $scripturl . '?action=profile;u=' . $context['member']['id'] . ';sa=account;changeusername" style="font-style: italic;">' . $txt['username_change'] . '</a>)</div>' : '', '
                </td>
                <td>
                  ', $context['member']['username'], '
                </td>
              </tr>';

    echo '
              <tr>
                <td>
                  <b', (isset($context['modify_error']['no_name']) || isset($context['modify_error']['name_taken']) ? ' style="color: red;"' : ''), '>', $txt[68], ': </b>
                  <div class="smalltext">', $txt[518], '</div>
                </td>
                <td>', ($context['allow_edit_name'] ? '<input type="text" name="realName" size="30" value="' . $context['member']['name'] . '" maxlength="60" />' : $context['member']['name']), '</td>
              </tr>';

    // Allow the administrator to change the date they registered on and their post count.
    if ($context['user']['is_admin'])
      echo '
              <tr>
                <td><b>', $txt[233], ':</b></td>
                <td><input type="text" name="dateRegistered" size="30" value="', $context['member']['registered'], '" /></td>
              </tr>
              <tr>
                <td><b>', $txt[86], ': </b></td>
                <td><input type="text" name="posts" size="4" value="', $context['member']['posts'], '" /></td>
              </tr>';

    // Only display if admin has enabled "user selectable language".
    if (!empty($modSettings['userLanguage']) && count($context['languages']) > 1)
    {
      echo '
              <tr>
                <td width="40%"><b>', $txt[349], ':</b></td>
                <td>
                  <select name="lngfile">';

      // Fill a select box with all the languages installed.
      foreach ($context['languages'] as $language)
        echo '
                    <option value="', $language['filename'], '"', $language['selected'] ? ' selected="selected"' : '', '>', $language['name'], '</option>';
      echo '
                  </select>
                </td>
              </tr>';
    }
  }

  // Only display member group information/editing with the proper permissions.
  if ($context['allow_edit_membergroups'])
  {
    echo '
              <tr>
                <td colspan="2"><hr width="100%" size="1" class="hrcolor" /></td>
              </tr><tr>
                <td valign="top">
                  <b>', $txt['primary_membergroup'], ': </b>
                  <div class="smalltext">(<a href="', $scripturl, '?action=helpadmin;help=moderator_why_missing" onclick="return reqWin(this.href);">', $txt['moderator_why_missing'], '</a>)</div>
                </td>
                <td>
                  <select name="ID_GROUP">';
    // Fill the select box with all primary member groups that can be assigned to a member.
    foreach ($context['member_groups'] as $member_group)
      echo '
                    <option value="', $member_group['id'], '"', $member_group['is_primary'] ? ' selected="selected"' : '', '>
                      ', $member_group['name'], '
                    </option>';
    echo '
                  </select>
                </td>
              </tr><tr>
                <td valign="top"><b>', $txt['additional_membergroups'], ':</b></td>
                <td>
                  <div id="additionalGroupsList">
                    <input type="hidden" name="additionalGroups[]" value="0" />';
    // For each membergroup show a checkbox so members can be assigned to more than one group.
    foreach ($context['member_groups'] as $member_group)
      if ($member_group['can_be_additional'])
        echo '
                    <label for="additionalGroups-', $member_group['id'], '"><input type="checkbox" name="additionalGroups[]" value="', $member_group['id'], '" id="additionalGroups-', $member_group['id'], '"', $member_group['is_additional'] ? ' checked="checked"' : '', ' class="check" /> ', $member_group['name'], '</label><br />';
    echo '
                  </div>
                  <a href="javascript:void(0);" onclick="document.getElementById(\'additionalGroupsList\').style.display = \'block\'; document.getElementById(\'additionalGroupsLink\').style.display = \'none\'; return false;" id="additionalGroupsLink" style="display: none;">', $txt['additional_membergroups_show'], '</a>
                  <script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[
                    document.getElementById("additionalGroupsList").style.display = "none";
                    document.getElementById("additionalGroupsLink").style.display = "";
                  // ]]></script>
                </td>
              </tr>';
  }

  // Show this part if you're not only here for assigning membergroups.
  if ($context['allow_edit_account'])
  {
    // Show email address box.
    echo '
              <tr>
                <td colspan="2"><hr width="100%" size="1" class="hrcolor" /></td>
              </tr><tr>
                <td width="40%"><b', (isset($context['modify_error']['bad_email']) || isset($context['modify_error']['no_email']) || isset($context['modify_error']['email_taken']) ? ' style="color: red;"' : ''), '>', $txt[69], ': </b><div class="smalltext">', $txt[679], '</div></td>
                <td><input type="text" name="emailAddress" size="30" value="', $context['member']['email'], '" /></td>
              </tr>';

    // If the user is allowed to hide their email address from the public give them the option to here.
    if ($context['allow_hide_email'])
    {
      echo '
              <tr>
                <td width="40%"><b>', $txt[721], '</b></td>
                <td><input type="hidden" name="hideEmail" value="0" /><input type="checkbox" name="hideEmail"', $context['member']['hide_email'] ? ' checked="checked"' : '', ' value="1" class="check" /></td>
              </tr>';
  }

    // Option to show online status - if they are allowed to.
    if ($context['allow_hide_online'])
    {
      echo '
              <tr>
                <td width="40%"><b>', $txt['show_online'], '</b></td>
                <td><input type="hidden" name="showOnline" value="0" /><input type="checkbox" name="showOnline"', $context['member']['show_online'] ? ' checked="checked"' : '', ' value="1" class="check" /></td>
              </tr>';
    }

    // Show boxes so that the user may change his or her password.
    echo '
              <tr>
                <td colspan="2"><hr width="100%" size="1" class="hrcolor" /></td>
              </tr><tr>
                <td width="40%"><b', (isset($context['modify_error']['bad_new_password']) ? ' style="color: red;"' : ''), '>', $txt[81], ': </b><div class="smalltext">', $txt[596], '</div></td>
                <td><input type="password" name="passwrd1" size="20" /></td>
              </tr><tr>
                <td width="40%"><b>', $txt[82], ': </b></td>
                <td><input type="password" name="passwrd2" size="20" /></td>
              </tr>';

    // This section allows the user to enter secret question/answer so they can reset a forgotten password.
    echo '
              <tr>
                <td colspan="2"><hr width="100%" size="1" class="hrcolor" /></td>
              </tr><tr>
                <td width="40%"><b>', $txt['pswd1'], ':</b><div class="smalltext">', $txt['secret_desc'], '</div></td>
                <td><input type="text" name="secretQuestion" size="50" value="', $context['member']['secret_question'], '" /></td>
              </tr><tr>
                <td width="40%"><b>', $txt['pswd2'], ':</b><div class="smalltext">', $txt['secret_desc2'], '</div></td>
                <td><input type="text" name="secretAnswer" size="20" /><span class="smalltext" style="margin-left: 4ex;"><a href="', $scripturl, '?action=helpadmin;help=secret_why_blank" onclick="return reqWin(this.href);">', $txt['secret_why_blank'], '</a></span></td>
              </tr>';
  }
  // Show the standard "Save Settings" profile button.
  template_profile_save();

  echo '
            </table>
          </td>
        </tr>
      </table>
    </form>';
}

// Template for forum specific options - avatar, signature etc.
function template_forumProfile() {
  global $context, $settings, $scripturl, $modSettings, $txt;

  // The main containing header.
  echo '
    <form action="', $scripturl, '?action=profile2" method="post" accept-charset="', $context['character_set'], '" name="creator" id="creator" enctype="multipart/form-data">
      <table border="0" width="85%" cellspacing="1" cellpadding="4" align="center" class="bordercolor">
        <tr class="titlebg">
          <td height="26">
            &nbsp;<img src="', $settings['images_url'], '/icons/profile_sm.gif" alt="" align="top" />&nbsp;
            ', $txt[79], '
          </td>
        </tr><tr class="windowbg">
          <td class="smalltext" height="25" style="padding: 2ex;">
            ', $txt['forumProfile_info'], '
          </td>
        </tr><tr>
          <td class="windowbg2" style="padding-bottom: 2ex;">
            <table border="0" width="100%" cellpadding="5" cellspacing="0">';

  // This is the avatar selection table that is only displayed if avatars are enabled!
  if (!empty($context['member']['avatar']['allow_server_stored']) || !empty($context['member']['avatar']['allow_upload']) || !empty($context['member']['avatar']['allow_external']))
  {
    // If users are allowed to choose avatars stored on the server show selection boxes to choice them from.
    if (!empty($context['member']['avatar']['allow_server_stored']))
    {
      echo '
              <tr>
                <td width="40%" valign="top" style="padding: 0 2px;">
                  <table width="100%" cellpadding="5" cellspacing="0" border="0" style="height: 25ex;"><tr>
                    <td valign="top" width="20" class="windowbg"><input type="radio" name="avatar_choice" id="avatar_choice_server_stored" value="server_stored"', ($context['member']['avatar']['choice'] == 'server_stored' ? ' checked="checked"' : ''), ' class="check" /></td>
                    <td valign="top" style="padding-left: 1ex;">
                      <b', (isset($context['modify_error']['bad_avatar']) ? ' style="color: red;"' : ''), '><label for="avatar_choice_server_stored">', $txt[229], ':</label></b>
                      <div style="margin: 2ex;"><img name="avatar" id="avatar" src="', !empty($context['member']['avatar']['allow_external']) && $context['member']['avatar']['choice'] == 'external' ? $context['member']['avatar']['external'] : $modSettings['avatar_url'] . '/blank.gif', '" alt="Do Nothing" /></div>
                    </td>
                  </tr></table>
                </td>
                <td>
                  <table width="100%" cellpadding="0" cellspacing="0" border="0"><tr>
                    <td style="width: 20ex;">
                      <select name="cat" id="cat" size="10" onchange="changeSel(\'\');" onfocus="selectRadioByName(document.forms.creator.avatar_choice, \'server_stored\');">';
      // This lists all the file catergories.
      foreach ($context['avatars'] as $avatar)
        echo '
                        <option value="', $avatar['filename'] . ($avatar['is_dir'] ? '/' : ''), '"', ($avatar['checked'] ? ' selected="selected"' : ''), '>', $avatar['name'], '</option>';
      echo '
                      </select>
                    </td>
                    <td>
                      <select name="file" id="file" size="10" style="display: none;" onchange="showAvatar()" onfocus="selectRadioByName(document.forms.creator.avatar_choice, \'server_stored\');" disabled="disabled"><option></option></select>
                    </td>
                  </tr></table>
                </td>
              </tr>';
    }

    // If the user can link to an off server avatar, show them a box to input the address.
    if (!empty($context['member']['avatar']['allow_external']))
    {
      echo '
              <tr>
                <td valign="top" style="padding: 0 2px;">
                  <table width="100%" cellpadding="5" cellspacing="0" border="0"><tr>
                    <td valign="top" width="20" class="windowbg"><input type="radio" name="avatar_choice" id="avatar_choice_external" value="external"', ($context['member']['avatar']['choice'] == 'external' ? ' checked="checked"' : ''), ' class="check" /></td>
                    <td valign="top" style="padding-left: 1ex;"><b><label for="avatar_choice_external">', $txt[475], ':</label></b><div class="smalltext">', $txt[474], '</div></td>
                  </tr></table>
                </td>
                <td valign="top">
                  <input type="text" name="userpicpersonal" size="45" value="', $context['member']['avatar']['external'], '" onfocus="selectRadioByName(document.forms.creator.avatar_choice, \'external\');" onchange="if (typeof(previewExternalAvatar) != \'undefined\') previewExternalAvatar(this.value);" />
                </td>
              </tr>';
    }

    // If the user is able to upload avatars to the server show them an upload box.
    if (!empty($context['member']['avatar']['allow_upload']))
      echo '
              <tr>
                <td valign="top" style="padding: 0 2px;">
                  <table width="100%" cellpadding="5" cellspacing="0" border="0"><tr>
                    <td valign="top" width="20" class="windowbg"><input type="radio" name="avatar_choice" id="avatar_choice_upload" value="upload"', ($context['member']['avatar']['choice'] == 'upload' ? ' checked="checked"' : ''), ' class="check" /></td>
                    <td valign="top" style="padding-left: 1ex;"><b><label for="avatar_choice_upload">', $txt['avatar_will_upload'], ':</label></b></td>
                  </tr></table>
                </td>
                <td valign="top">
                  ', ($context['member']['avatar']['ID_ATTACH'] > 0 ? '<img src="' . $context['member']['avatar']['href'] . '" /><input type="hidden" name="ID_ATTACH" value="' . $context['member']['avatar']['ID_ATTACH'] . '" /><br /><br />' : ''), '
                  <input type="file" size="48" name="attachment" value="" onfocus="selectRadioByName(document.forms.creator.avatar_choice, \'upload\');" />
                </td>
              </tr>';
  }

  // Personal text...
  echo '
              <tr>
                <td width="40%"><b>', $txt[228], ': </b></td>
                <td><input type="text" name="personalText" size="50" maxlength="50" value="', $context['member']['blurb'], '" /></td>
              </tr>
              <tr>
                <td colspan="2"><hr width="100%" size="1" class="hrcolor" /></td>
              </tr>';

  // Gender, birthdate and location.
  echo '
              <tr>
                <td width="40%">
                  <b>', $txt[563], ':</b>
                  <div class="smalltext">', $txt[566], ' - ', $txt[564], ' - ', $txt[565], '</div>
                </td>
                <td class="smalltext">
                  <input type="text" name="bday3" size="4" maxlength="4" value="', $context['member']['birth_date']['year'], '" /> -
                  <input type="text" name="bday1" size="2" maxlength="2" value="', $context['member']['birth_date']['month'], '" /> -
                  <input type="text" name="bday2" size="2" maxlength="2" value="', $context['member']['birth_date']['day'], '" />
                </td>
              </tr><tr>
                <td width="40%"><b>', $txt[227], ': </b></td>
                <td><input type="text" name="location" size="50" value="', $context['member']['location'], '" /></td>
              </tr>
              <tr>
                <td width="40%"><b>', $txt[231], ': </b></td>
                <td>
                  <select name="gender" size="1">
                    <option value="0"></option>
                    <option value="1"', ($context['member']['gender']['name'] == 'm' ? ' selected="selected"' : ''), '>', $txt[238], '</option>
                    <option value="2"', ($context['member']['gender']['name'] == 'f' ? ' selected="selected"' : ''), '>', $txt[239], '</option>
                  </select>
                </td>
              </tr><tr>
                <td colspan="2"><hr width="100%" size="1" class="hrcolor" /></td>
              </tr>';

  // All the messenger type contact info.
  echo '
              <tr>
                <td width="40%"><b>', $txt[513], ': </b><div class="smalltext">', $txt[600], '</div></td>
                <td><input type="text" name="ICQ" size="24" value="', $context['member']['icq']['name'], '" /></td>
              </tr><tr>
                <td width="40%"><b>', $txt[603], ': </b><div class="smalltext">', $txt[601], '</div></td>
                <td><input type="text" name="AIM" maxlength="16" size="24" value="', $context['member']['aim']['name'], '" /></td>
              </tr><tr>
                <td width="40%"><b>', $txt['MSN'], ': </b><div class="smalltext">', $txt['smf237'], '.</div></td>
                <td><input type="text" name="MSN" size="24" value="', $context['member']['msn']['name'], '" /></td>
              </tr><tr>
                <td width="40%"><b>', $txt[604], ': </b><div class="smalltext">', $txt[602], '</div></td>
                <td><input type="text" name="YIM" maxlength="32" size="24" value="', $context['member']['yim']['name'], '" /></td>
              </tr><tr>
                <td colspan="2"><hr width="100%" size="1" class="hrcolor" /></td>
              </tr>';

  // Input box for custom titles, if they can edit it...
  if (!empty($modSettings['titlesEnable']) && $context['allow_edit_title'])
    echo '
              <tr>
                <td width="40%"><b>' . $txt['title1'] . ': </b></td>
                <td><input type="text" name="usertitle" size="50" value="' . $context['member']['title'] . '" /></td>
              </tr>';

  // Show the signature box.
  echo '
              <tr>
                <td width="40%" valign="top">
                  <b>', $txt[85], ':</b>
                  <div class="smalltext">', $txt[606], '</div><br />
                  <br />';

  if ($context['show_spellchecking'])
    echo '
                  <input type="button" value="', $txt['spell_check'], '" onclick="spellCheck(\'creator\', \'signature\');" />';

  echo '
                </td>
                <td>
                  <textarea class="editor" onkeyup="calcCharLeft();" name="signature" rows="5" cols="50">', $context['member']['signature'], '</textarea><br />';

  // If there is a limit at all!
  if (!empty($context['max_signature_length']))
    echo '
                  <span class="smalltext">', $txt[664], ' <span id="signatureLeft">', $context['max_signature_length'], '</span></span>';

  // Load the spell checker?
  if ($context['show_spellchecking'])
    echo '
                  <script language="JavaScript" type="text/javascript" src="', $settings['default_theme_url'], '/spellcheck.js"></script>';

  // Some javascript used to count how many characters have been used so far in the signature.
  echo '
                  <script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[
                    function tick()
                    {
                      if (typeof(document.forms.creator) != "undefined")
                      {
                        calcCharLeft();
                        setTimeout("tick()", 1000);
                      }
                      else
                        setTimeout("tick()", 800);
                    }

                    function calcCharLeft()
                    {
                      var maxLength = ', $context['max_signature_length'], ';
                      var oldSignature = "", currentSignature = document.forms.creator.signature.value;

                      if (!document.getElementById("signatureLeft"))
                        return;

                      if (oldSignature != currentSignature)
                      {
                        oldSignature = currentSignature;

                        if (currentSignature.replace(/\r/, "").length > maxLength)
                          document.forms.creator.signature.value = currentSignature.replace(/\r/, "").substring(0, maxLength);
                        currentSignature = document.forms.creator.signature.value.replace(/\r/, "");
                      }

                      setInnerHTML(document.getElementById("signatureLeft"), maxLength - currentSignature.length);
                    }

                    setTimeout("tick()", 800);
                  // ]]></script>
                </td>
              </tr>';

  // Website details.
  echo '
              <tr>
                <td colspan="2"><hr width="100%" size="1" class="hrcolor" /></td>
              </tr>
              <tr>
                <td width="40%"><b>', $txt[83], ': </b><div class="smalltext">', $txt[598], '</div></td>
                <td><input type="text" name="websiteTitle" size="50" value="', $context['member']['website']['title'], '" /></td>
              </tr><tr>
                <td width="40%"><b>', $txt[84], ': </b><div class="smalltext">', $txt[599], '</div></td>
                <td><input type="text" name="websiteUrl" size="50" value="', $context['member']['website']['url'], '" /></td>
              </tr>';

  // If karma is enabled let the admin edit it...
  if ($context['user']['is_admin'] && !empty($modSettings['karmaMode']))
  {
    echo '
              <tr>
                <td colspan="2"><hr width="100%" size="1" class="hrcolor" /></td>
              </tr><tr>
                <td valign="top"><b>', $modSettings['karmaLabel'], '</b></td>
                <td>
                  ', $modSettings['karmaApplaudLabel'], ' <input type="text" name="karmaGood" size="4" value="', $context['member']['karma']['good'], '" onchange="setInnerHTML(document.getElementById(\'karmaTotal\'), this.value - this.form.karmaBad.value);" style="margin-right: 2ex;" /> ', $modSettings['karmaSmiteLabel'], ' <input type="text" name="karmaBad" size="4" value="', $context['member']['karma']['bad'], '" onchange="this.form.karmaGood.onchange();" /><br />
                  (', $txt[94], ': <span id="karmaTotal">', ($context['member']['karma']['good'] - $context['member']['karma']['bad']), '</span>)
                </td>
              </tr>';
  }

  // Show the standard "Save Settings" profile button.
  template_profile_save();

  echo '
            </table>
          </td>
        </tr>
      </table>';

  /* If the user is allowed to choose avatars stored on the server, the below javascript is used to update the
    file listing of avatars as the user changes catergory. It also updates the preview image as they choose
    different files on the select box. */
  if (!empty($context['member']['avatar']['allow_server_stored']))
    echo '
      <script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[
        var files = ["' . implode('", "', $context['avatar_list']) . '"];
        var avatar = document.getElementById("avatar");
        var cat = document.getElementById("cat");
        var selavatar = "' . $context['avatar_selected'] . '";
        var avatardir = "' . $modSettings['avatar_url'] . '/";
        var size = avatar.alt.substr(3, 2) + " " + avatar.alt.substr(0, 2) + String.fromCharCode(117, 98, 116);
        var file = document.getElementById("file");

        if (avatar.src.indexOf("blank.gif") > -1)
          changeSel(selavatar);
        else
          previewExternalAvatar(avatar.src)

        function changeSel(selected)
        {
          if (cat.selectedIndex == -1)
            return;

          if (cat.options[cat.selectedIndex].value.indexOf("/") > 0)
          {
            var i;
            var count = 0;

            file.style.display = "inline";
            file.disabled = false;

            for (i = file.length; i >= 0; i = i - 1)
              file.options[i] = null;

            for (i = 0; i < files.length; i++)
              if (files[i].indexOf(cat.options[cat.selectedIndex].value) == 0)
              {
                var filename = files[i].substr(files[i].indexOf("/") + 1);
                var showFilename = filename.substr(0, filename.lastIndexOf("."));
                showFilename = showFilename.replace(/[_]/g, " ");

                file.options[count] = new Option(showFilename, files[i]);

                if (filename == selected)
                {
                  if (file.options.defaultSelected)
                    file.options[count].defaultSelected = true;
                  else
                    file.options[count].selected = true;
                }

                count++;
              }

            if (file.selectedIndex == -1 && file.options[0])
              file.options[0].selected = true;

            showAvatar();
          }
          else
          {
            file.style.display = "none";
            file.disabled = true;
            document.getElementById("avatar").src = avatardir + cat.options[cat.selectedIndex].value;
            document.getElementById("avatar").style.width = "";
            document.getElementById("avatar").style.height = "";
          }
        }

        function showAvatar()
        {
          if (file.selectedIndex == -1)
            return;

          document.getElementById("avatar").src = avatardir + file.options[file.selectedIndex].value;
          document.getElementById("avatar").alt = file.options[file.selectedIndex].text;
          document.getElementById("avatar").alt += file.options[file.selectedIndex].text == size ? "!" : "";
          document.getElementById("avatar").style.width = "";
          document.getElementById("avatar").style.height = "";
        }

        function previewExternalAvatar(src)
        {
          if (!document.getElementById("avatar"))
            return;

          var maxHeight = ', !empty($modSettings['avatar_max_height_external']) ? $modSettings['avatar_max_height_external'] : 0, ';
          var maxWidth = ', !empty($modSettings['avatar_max_width_external']) ? $modSettings['avatar_max_width_external'] : 0, ';
          var tempImage = new Image();

          tempImage.src = src;
          if (maxWidth != 0 && tempImage.width > maxWidth)
          {
            document.getElementById("avatar").style.height = parseInt((maxWidth * tempImage.height) / tempImage.width) + "px";
            document.getElementById("avatar").style.width = maxWidth + "px";
          }
          else if (maxHeight != 0 && tempImage.height > maxHeight)
          {
            document.getElementById("avatar").style.width = parseInt((maxHeight * tempImage.width) / tempImage.height) + "px";
            document.getElementById("avatar").style.height = maxHeight + "px";
          }
          document.getElementById("avatar").src = src;
        }
      // ]]></script>';
  echo '
    </form>';

  if ($context['show_spellchecking'])
    echo '
    <form action="', $scripturl, '?action=spellcheck" method="post" accept-charset="', $context['character_set'], '" name="spell_form" id="spell_form" target="spellWindow"><input type="hidden" name="spellstring" value="" /></form>';
}

function template_deleteAccount() {
  global $context, $settings, $scripturl, $txt, $scripturl;

  // The main containing header.
  echo '
    <form action="', $scripturl, '?action=profile2" method="post" accept-charset="', $context['character_set'], '" name="creator" id="creator">
      <table border="0" width="85%" cellspacing="1" cellpadding="4" align="center" class="bordercolor">
        <tr class="titlebg">
          <td height="26">
            &nbsp;<img src="', $settings['images_url'], '/icons/profile_sm.gif" alt="" align="top" />&nbsp;
            ', $txt['deleteAccount'], '
          </td>
        </tr>';
  // If deleting another account give them a lovely info box.
  if (!$context['user']['is_owner'])
  echo '
          <tr class="windowbg">
            <td class="smalltext" colspan="2" style="padding-top: 2ex; padding-bottom: 2ex;">
              ', $txt['deleteAccount_desc'], '
            </td>
          </tr>';
  echo '
        <tr>
          <td class="windowbg2">
            <table width="100%" cellspacing="0" cellpadding="3"><tr>
              <td align="center" colspan="2">';

  // If they are deleting their account AND the admin needs to approve it - give them another piece of info ;)
  if ($context['needs_approval'])
    echo '
                <div style="color: red; border: 2px dashed red; padding: 4px;">', $txt['deleteAccount_approval'], '</div><br />
              </td>
            </tr><tr>
              <td align="center" colspan="2">';

  // If the user is deleting their own account warn them first - and require a password!
  if ($context['user']['is_owner'])
  {
    echo '
                <span style="color: red;">', $txt['own_profile_confirm'], '</span><br /><br />
              </td>
            </tr><tr>
              <td class="windowbg2" align="', !$context['right_to_left'] ? 'right' : 'left', '">
                <b', (isset($context['modify_error']['bad_password']) || isset($context['modify_error']['no_password']) ? ' style="color: red;"' : ''), '>', $txt['smf241'], ': </b>
              </td>
              <td class="windowbg2" align="', !$context['right_to_left'] ? 'left' : 'right', '">
                <input type="password" name="oldpasswrd" size="20" />&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="submit" value="', $txt[163], '" />
                <input type="hidden" name="sc" value="', $context['session_id'], '" />
                <input type="hidden" name="userID" value="', $context['member']['id'], '" />
                <input type="hidden" name="sa" value="', $context['menu_item_selected'], '" />
              </td>';
  }
  // Otherwise an admin doesn't need to enter a password - but they still get a warning - plus the option to delete lovely posts!
  else
  {
    echo '
                <div style="color: red; margin-bottom: 2ex;">', $txt['deleteAccount_warning'], '</div>
              </td>
            </tr>';

    // Only actually give these options if they are kind of important.
    if ($context['can_delete_posts'])
      echo '
            <tr>
              <td colspan="2" align="center">
                ', $txt['deleteAccount_posts'], ': <select name="remove_type">
                  <option value="none">', $txt['deleteAccount_none'], '</option>
                  <option value="posts">', $txt['deleteAccount_all_posts'], '</option>
                  <option value="topics">', $txt['deleteAccount_topics'], '</option>
                </select>
              </td>
            </tr>';

    echo '
            <tr>
              <td colspan="2" align="center">
                <label for="deleteAccount"><input type="checkbox" name="deleteAccount" id="deleteAccount" value="1" class="check" onclick="if (this.checked) return confirm(\'', $txt['deleteAccount_confirm'], '\');" /> ', $txt['deleteAccount_member'], '.</label>
              </td>
            </tr>
            <tr>
              <td colspan="2" class="windowbg2" align="center" style="padding-top: 2ex;">
                <input type="submit" value="', $txt['smf138'], '" />
                <input type="hidden" name="sc" value="', $context['session_id'], '" />
                <input type="hidden" name="userID" value="', $context['member']['id'], '" />
                <input type="hidden" name="sa" value="', $context['menu_item_selected'], '" />
              </td>';
  }
  echo '
            </tr></table>
          </td>
        </tr>
      </table>
    </form>';
}

// Template for the password box/save button stuck at the bottom of every profile page.
function template_profile_save() {
  global $context, $txt;

  echo '
              <tr>
                <td colspan="2"><hr width="100%" size="1" class="hrcolor" /></td>
              </tr><tr>';

  // Only show the password box if it's actually needed.
  if ($context['user']['is_owner'] && $context['require_password'])
    echo '
                <td width="40%">
                  <b', isset($context['modify_error']['bad_password']) || isset($context['modify_error']['no_password']) ? ' style="color: red;"' : '', '>', $txt['smf241'], ': </b>
                  <div class="smalltext">', $txt['smf244'], '</div>
                </td>
                <td>
                  <input type="password" name="oldpasswrd" size="20" style="margin-right: 4ex;" />';
  else
    echo '
                <td align="right" colspan="2">';

  echo '
                  <input type="submit" value="', $txt[88], '" />
                  <input type="hidden" name="sc" value="', $context['session_id'], '" />
                  <input type="hidden" name="userID" value="', $context['member']['id'], '" />
                  <input type="hidden" name="sa" value="', $context['menu_item_selected'], '" />
                </td>
              </tr>';
}

// Small template for showing an error message upon a save problem in the profile.
function template_error_message() {
  global $context, $txt;

  echo '
    <div class="windowbg" style="margin: 1ex; padding: 1ex 2ex; border: 1px dashed red; color: red;">
      <span style="text-decoration: underline;">', $txt['profile_errors_occurred'], ':</span>
      <ul>';

    // Cycle through each error and display an error message.
    foreach ($context['post_errors'] as $error)
      //if (isset($txt['profile_error_' . $error]))
        echo '
        <li>', $txt['profile_error_' . $error], '.</li>';

    echo '
      </ul>
    </div>';
}

function template_post() {
  global $settings, $db_prefix, $context, $modSettings, $boardurl;

  $memberName = censorText($context['member']['name']);
  $end = $modSettings['user_posts'];
  $page = (int) $_GET['pag'];

  if (isset($page)) {
    $start = ($page - 1) * $end;
    $actualPage = (int) $page;
  } else {
    $start = 0;
    $actualPage = 1;
  }

  $query = "
    SELECT
      m.ID_TOPIC, m.ID_BOARD, m.hiddenOption, m.subject, m.ID_MEMBER, b.name, b.description,
      b.ID_BOARD, t.isSticky, t.ID_TOPIC, t.ID_BOARD, m.posterTime, t.points, m2.ID_MEMBER,
      m.posterName
    FROM ({$db_prefix}messages AS m, {$db_prefix}boards AS b, {$db_prefix}topics AS t, {$db_prefix}members AS m2)
    WHERE m.ID_TOPIC = t.ID_TOPIC
    AND b.ID_BOARD = m.ID_BOARD
    AND t.ID_BOARD = m.ID_BOARD
    AND m.ID_MEMBER = m2.ID_MEMBER
    AND m.posterName = '" . $memberName . "'
    GROUP BY t.ID_TOPIC
    ORDER BY m.posterTime DESC";

  $request2 = db_query("
    {$query}
    LIMIT {$start}, {$end}", __FILE__, __LINE__);

  $count = mysqli_num_rows($request2);

  if ($count <= 0) {
    echo '<div class="noesta" style="width: 922px;">', $memberName, ' no tiene posts hechos.</div>';
  } else {
    echo '
      <div style="float: left; width: 757px;">
        <table class="linksList" style="width: 757px;">
          <thead align="center">
            <th>&nbsp;</th>
            <th style="text-align:left;">Posts de <i>', $memberName, '</th>
            <th>Fecha</th>
            <th>Puntos</th>
            <th>Enviar</th>
          </tr>
        </thead>
        <tbody>';

    while ($row = mysqli_fetch_assoc($request2)) {
      echo '
        <tr>
          <td>
            <img alt="" title="' . $row['name'] . '" src="' . $settings['images_url'] . '/post/icono_' . $row['ID_BOARD'] . '.gif" />
          </td>
          <td style="text-align: left;">
            <a href="' . $boardurl . '/post/' . $row['ID_TOPIC'] . '/' . $row['description'] . '/' . ssi_amigable($row['subject']) . '.html" alt="" title="' . $row['subject'] . '">' . $row['subject'] . '</a>
          </td>
          <td title="' . timeformat($row['posterTime']) . '">' . timeformat($row['posterTime']) . '</td>
          <td style="color: green;" title="' . $row['points'] . '">' . $row['points'] . '</td>
          <td>
            <a title="Enviar a amigo" href="' . $boardurl . '/enviar-a-amigo/' . $row['ID_TOPIC'] . '">
              <img alt="" src="' . $settings['images_url'] . '/icons/icono-enviar-mensaje.gif" />
            </a>
          </td>
        </tr>';
    }

    // Registros totales
    $request = db_query($query, __FILE__, __LINE__);
    $records = mysqli_num_rows($request);

    $previousPage = $actualPage - 1;
    $nextPage = $actualPage + 1;
    $lastPage = $records / $end;
    $residue = $records % $end;

    if ($residue > 0) {
      $lastPage = floor($lastPage) + 1;
    }

    echo '
        </tbdoy>
      </table>
      <div class="windowbgpag" style="width: 757px;">';

    if ($actualPage > 1) {
      echo '<a href="' . $boardurl . '/user-post/', $memberName, '/pag-' . $previousPage . '">&#171; anterior</a>';
    }

    if ($actualPage < $lastPage) {
      echo '<a href="' . $boardurl . '/user-post/', $memberName, '/pag-' . $nextPage . '">siguiente &#187;</a>';
    }

    echo '
            </div>
            <div class="clearBoth"></div>
          </div>
          <div style="float: left; width: 160px; margin-left: 8px;">
            <div class="img_aletat">
              <div class="box_title" style="width: 155px;">
                <div class="box_txt img_aletat">Publicidad</div>
                <div class="box_rss">
                  <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 16px; height: 16px;" border="0" />
                </div>
              </div>
              <div class="windowbg" style="width: 147px; padding: 4px;">
                <center>' . $modSettings['vertical'] . '</center>
              </div>
            </div>
          </div>
          <div style="clear:both"></div>
        </div>
      </div>';
  }
}

function template_comentarios() {
  global $settings, $db_prefix, $context, $modSettings, $boardurl;

  $memberName = $context['member']['name'];

  $request = db_query("
    SELECT c.ID_COMMENT, c.ID_MEMBER, c.ID_TOPIC, mem.ID_MEMBER, m.ID_TOPIC, m.posterName, mem.memberName, t.ID_TOPIC, t.ID_MEMBER_STARTED, mem.realName
    FROM ({$db_prefix}comments AS c, {$db_prefix}members AS mem, {$db_prefix}topics AS t, {$db_prefix}messages AS m)
    WHERE m.ID_TOPIC = t.ID_TOPIC
    AND c.ID_TOPIC = t.ID_TOPIC
    AND c.ID_TOPIC = m.ID_TOPIC
    AND c.ID_MEMBER = mem.ID_MEMBER
    AND mem.memberName = '" . $memberName . "' ", __FILE__, __LINE__);

  $request2 = db_query("
    SELECT c.ID_COMMENT, c.ID_PICTURE, c.ID_MEMBER, c.comment, c.date, g.title, g.ID_PICTURE, g.ID_MEMBER
    FROM ({$db_prefix}gallery_comment AS c, {$db_prefix}gallery_pic AS g)
    WHERE c.ID_PICTURE = g.ID_PICTURE
    AND c.ID_MEMBER = " . $context['member']['id'] . "
    AND g.ID_PICTURE = c.ID_PICTURE", __FILE__, __LINE__);

  $count_c = mysqli_num_rows($request);
  $count_c_img = mysqli_num_rows($request2);

  echo '
    <div style="float:left;width:757px;">
      <div class="mennes">
        <div class="botnes">
          <ul>
            <li>
              <a href="' . $boardurl . '/user-comment/' . $memberName . '" title="Comentarios en posts">Comentarios en posts (' . $count_c . ')</a>
            </li>
            <li>
              <a href="' . $boardurl . '/user-comment-img/' . $memberName . '" title="Comentarios en im&aacute;genes">Comentarios en im&aacute;genes (' . $count_c_img . ')</a>
            </li>
          </ul>
          <div style="clear: both;">
        </div>
      </div>
    </div>
    <div class="clearBoth"></div>
    <div class="box_757" style="margin-bottom: 8px;">
      <div class="box_title" style="width: 755px;">
        <div class="box_txt box_757-34">
          <center>Comentarios de <i>' . $memberName . '</i> en posts</center>
        </div>
        <div class="box_rss">
          <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 16px; height: 16px;" border="0" />
        </div>
      </div>
      <div class="windowbg" style="width: 747px; padding: 4px;">';

  if ($count_c <= 0) {
    echo '<div class="noesta">' . $memberName . ' no tiene comentarios en posts hechos.</div>';
  } else {
    $end = $modSettings['user_comments_posts'];
    $page = (int) $_GET['pag'];

    if (isset($page)) {
      $start = ($page-1)*$end;
      $actualPage = $page;
    } else {
      $start = 0;
      $actualPage = 1;
    }

    $query = "
      SELECT
        m.ID_TOPIC, m.ID_BOARD, m.hiddenOption, m.subject, m.ID_MEMBER, b.name, b.description, b.ID_BOARD, t.isSticky,
        t.ID_TOPIC, t.ID_BOARD, c.posterTime, t.points, c.ID_MEMBER, c.ID_TOPIC, c.comment, c.ID_COMMENT, m2.ID_MEMBER,
        m2.memberName
      FROM ({$db_prefix}messages AS m, {$db_prefix}boards AS b, {$db_prefix}topics AS t, {$db_prefix}comments AS c, {$db_prefix}members AS m2)
      WHERE m.ID_TOPIC = t.ID_TOPIC
      AND b.ID_BOARD = m.ID_BOARD
      AND t.ID_BOARD = m.ID_BOARD
      AND c.ID_TOPIC = t.ID_TOPIC
      AND m.ID_TOPIC = c.ID_TOPIC
      AND c.ID_MEMBER = m2.ID_MEMBER 
      AND m2.memberName = '" . $memberName . "'
      ORDER BY c.ID_COMMENT DESC";

    $request3 = db_query("
      {$query}
      LIMIT {$start}, {$end}", __FILE__, __LINE__);

    while ($row = mysqli_fetch_assoc($request3)) {
      echo '
        <table width="100%">
          <tr>
            <td valign="top" style="width: 16px;">
              <img alt="" src="' . $settings['images_url'] . '/post/icono_' . $row['ID_BOARD'] . '.gif" title="' . $row['name'] . '" />
            </td>
            <td>
              <b class="size11">
                <a title="' . $row['subject'] . '" href="' . $boardurl . '/post/' . $row['ID_TOPIC'] . '/' . $row['description'] . '/' . ssi_amigable($row['subject']) . '.html" >' . $row['subject'] . '</a>
              </b>
              <div class="size11">
                ' . timeformat($row['posterTime']) . ':
                &nbsp;
                <a href="' . $boardurl . '/post/' . $row['ID_TOPIC'] . '/' . $row['description'] . '/' . ssi_amigable($row['subject']) . '.html#cmt_' . $row['ID_COMMENT'] . '" >' . $row['comment'] . '</a>
              </div>
            </td>
          </tr>
        </table>';
    }

    $request = db_query($query, __FILE__, __LINE__);
    $records = mysqli_num_rows($request, __FILE__, __LINE__));
  }

  $previousPage = $actualPage - 1;
  $nextPage = $actualPage + 1;
  $lastPage = $records / $end;
  $residue = $records % $end;

  if ($residue > 0) {
    $lastPage = floor($lastPage) + 1;
  }

  echo '
    </div>
    <div class="windowbgpag" style="width: 757px;">';

  if ($actualPage > 1) {
    echo '<a href="' . $boardurl . '/user-comment/', $context['member']['name'], '/pag-' . $previousPage . '">&#171; anterior</a>';
  }

  if ($actualPage < $lastPage) {
    echo '<a href="' . $boardurl . '/user-comment/', $context['member']['name'], '/pag-' . $nextPage . '">siguiente &#187;</a>';
  }

  echo '
        </div>
        <div class="clearBoth"></div>
        <div style="clear: both;"></div>
      </div>
    </div>
    <div style="float: left; width: 155px; margin-left: 8px;">
      <div class="img_aletat">
        <div class="box_title" style="width: 155px;">
          <div class="box_txt img_aletat">Publicidad</div>
          <div class="box_rss">
            <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 16px; height: 16px;" border="0" />
          </div>
        </div>
        <div class="windowbg" style="width: 147px; padding: 4px;">
          <center>' . $modSettings['vertical'] . '</center>
        </div>
      </div>
    </div>
    <div style="clear:both"></div>';
}

function template_comentariosimg() {
  global $settings, $db_prefix, $context, $modSettings, $boardurl;

  $memberName = $context['member']['name'];

  $request = db_query("
    SELECT c.ID_COMMENT, c.ID_MEMBER, c.ID_TOPIC, mem.ID_MEMBER, m.ID_TOPIC, m.posterName, mem.memberName, t.ID_TOPIC, t.ID_MEMBER_STARTED
    FROM ({$db_prefix}comments AS c, {$db_prefix}members AS mem, {$db_prefix}topics AS t, {$db_prefix}messages AS m)
    WHERE m.ID_TOPIC = t.ID_TOPIC
    AND c.ID_TOPIC = t.ID_TOPIC
    AND c.ID_TOPIC = m.ID_TOPIC
    AND c.ID_MEMBER = mem.ID_MEMBER
    AND mem.memberName = '" . $memberName . "'", __FILE__, __LINE__);

  $count_c = mysqli_num_rows($request);

  $request2 = db_query("
    SELECT c.ID_COMMENT, c.ID_PICTURE, c.ID_MEMBER, c.comment, c.date, g.title, g.ID_PICTURE, g.ID_MEMBER
    FROM ({$db_prefix}gallery_comment AS c, {$db_prefix}gallery_pic AS g)
    WHERE c.ID_PICTURE = g.ID_PICTURE
    AND c.ID_MEMBER = " . $context['member']['id'] . "
    AND g.ID_PICTURE = c.ID_PICTURE", __FILE__, __LINE__);

  $count_c_img = mysqli_num_rows($request2);

  echo '
    <div style="float: left; width: 757px;">
      <div class="mennes">
        <div class="botnes">
          <ul>
            <li>
              <a href="' . $boardurl . '/user-comment/' . $memberName . '" title="Comentarios en posts">Comentarios en posts (' . $count_c . ')</a>
            </li>
            <li>
              <a href="' . $boardurl . '/user-comment-img/' . $memberName . '" title="Comentarios en im&aacute;genes">Comentarios en im&aacute;genes (' . $count_c_img . ')</a>
            </li>
          </ul>
          <div style="clear: both;"></div>
        </div>
      </div>
      <div class="clearBoth"></div>
      <div class="box_757" style="margin-bottom: 8px;">
        <div class="box_title" style="width: 755px;">
          <div class="box_txt box_757-34">
            <center>Comentarios de <i>' . $memberName . '</i> en im&aacute;genes</center>
          </div>
          <div class="box_rss">
            <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 16px; height: 16px;" border="0" />
          </div>
        </div>
        <div class="windowbg" style="width: 747px; padding: 4px;">';

  if ($count_c_img <= 0) {
    echo '<div class="noesta">' . $memberName . ' no tiene comentarios en im&aacute;genes hechas.</div>';
  } else {
    $end = $modSettings['user_comments_images'];
    $page = (int) $_GET['pag'];
  
    if (isset($page)) {
      $start = ($page-1)*$end;
      $actualPage = (int) $page;
    } else {
      $start = 0;
      $actualPage = 1;
    }

    $query = "
      SELECT c.ID_COMMENT, c.ID_PICTURE, c.ID_MEMBER, c.comment, c.date, g.title, g.ID_PICTURE, g.ID_MEMBER
      FROM ({$db_prefix}gallery_comment AS c, {$db_prefix}gallery_pic AS g)
      WHERE c.ID_PICTURE = g.ID_PICTURE
      AND c.ID_MEMBER = " . $context['member']['id'] . "
      AND g.ID_PICTURE = c.ID_PICTURE
      ORDER BY c.ID_COMMENT DESC";

    $request3 = db_query("
      {$query}
      LIMIT {$start}, {$end}", __FILE__, __LINE__);

    while ($row = mysqli_fetch_assoc($request3)) {
      echo '
        <table width="100%">
          <tr>
            <td valign="top" style="width: 16px;">
              <span class="icons fot2">&nbsp;</span>
            </td>
            <td>
              <b class="size11">
                <a title="' . $row['title'] . '" href="' . $boardurl . '/imagenes/ver/' . $row['ID_PICTURE'] . '" >' . $row['title'] . '</a>
              </b>
              <div class="size11">
                ' . timeformat($row['date']) . ':
                &nbsp;
                <a href="' . $boardurl . '/imagenes/ver/' . $row['ID_PICTURE'] . '#cmt_' . $row['ID_COMMENT'] . '" >' . $row['comment'] . '</a>
              </div>
            </td>
          </tr>
        </table>';
    }

    $request = db_query($query, __FILE__, __LINE__);
    $records = mysqli_num_rows($request);

    $previousPage = $actualPage - 1;
    $nextPage = $actualPage + 1;
    $lastPage = $records / $end;
    $residue = $records % $end;

    if ($residue > 0) {
      $lastPage = floor($lastPage) + 1;
    }

    echo '
      </div>
      <div class="windowbgpag" style="width: 757px;">';

    if ($actualPage > 1) {
      echo '<a href="' . $boardurl . '/user-comment-img/' . $memberName . '/pag-' . $previousPage . '">&#171; anterior</a>';
    }

    if ($actualPage < $lastPage) {
      echo '<a href="' . $boardurl . '/user-comment-img/' . $memberName . '/pag-' . $nextPage . '">siguiente &#187;</a>';
    }
  }

  echo '
        </div>
        <div class="clearBoth"></div>
        <div style="clear: both;"></div>
      </div>
    </div>
    <div style="float: left; width: 155px; margin-left: 8px;">
      <div class="img_aletat">
        <div class="box_title" style="width: 155px;">
          <div class="box_txt img_aletat">Publicidad</div>
          <div class="box_rss">
            <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 16px; height: 16px;" border="0" />
          </div>
        </div>
        <div class="windowbg" style="width: 147px; padding: 4px;">
          <center>' . $modSettings['vertical'] . '</center>
        </div>
      </div>
    </div>
    <div style="clear:both"></div>';
}

function template_buddies() {
  global $db_prefix, $context, $settings, $modSettings, $boardurl;

  menu2();
  sidebar();
  menu3();

  echo '
    <div style="float: left; margin-bottom: 8px;">
      <div class="windowbg" style="border: 1px solid #D7CFC6; width: 523px; padding: 8px; font-size: 11px;">';

  $memberName = $context['member']['name'];
  $end = $modSettings['user_friends'];
  $page = (int) $_GET['pag'];

  if (isset($page)) {
    $start = ($page-1)*$end;
    $actualPage = (int) $page;
  } else {
    $start = 0;
    $actualPage = 1;
  }

  $query = "
    SELECT mem.ID_MEMBER AS ID_MIEMBRO, mem.realName, mem.memberName, mem.showOnline, mem.avatar, mem.personalText, b.ID_MEMBER, b.time_updated, b.BUDDY_ID, lo.ID_MEMBER AS MONLINE, lo.logTime 
    FROM ({$db_prefix}members AS mem, {$db_prefix}buddies AS b, {$db_prefix}log_online AS lo)
    WHERE b.ID_MEMBER = " . $context['member']['id'] . "
    AND b.BUDDY_ID = mem.ID_MEMBER
    GROUP BY b.BUDDY_ID DESC
    ORDER BY b.time_updated DESC";

  $request2 = db_query("
    {$query}
    LIMIT {$start}, {$end}", __FILE__, __LINE__);

  $count = db_query("
    SELECT mem.ID_MEMBER AS ID_MIEMBRO, mem.realName, mem.memberName, mem.showOnline, mem.avatar, mem.personalText, b.ID_MEMBER, b.time_updated, b.BUDDY_ID, o.ID_MEMBER AS MONLINE
    FROM ({$db_prefix}members AS mem, {$db_prefix}buddies AS b, {$db_prefix}log_online AS o)
    WHERE IFNULL(mem.showOnline, 1) = 1
    AND b.ID_MEMBER = " . $context['member']['id'] . "
    AND b.BUDDY_ID = mem.ID_MEMBER
    GROUP BY b.BUDDY_ID DESC
    ORDER BY b.time_updated DESC", __FILE__, __LINE__);

  if ($count == 0) {
    echo '<div class="noesta">' . $memberName . ' no tiene ning&uacute;n amigo a&ntilde;adido.</div>';
  } else {
    echo '
      <p align="right" style="margin: 0px; padding: 0px;">
        <a href="' . $boardurl . '/perfil/' . $memberName . '/lista-de-amigos/">' . mysqli_num_rows($count) . ' amigos</a>
      </p>
      <hr />';

    while ($row = mysqli_fetch_assoc($request2)) {
      echo '
        <table>
          <tbody>
            <tr>
              <td valign="top">
                <img style="width: 50px; height: 50px;" alt="" src="' . (!empty($row['avatar']) ? $row['avatar'] : $boardurl . '/avatar.gif') . '" onerror="error_avatar(this)">
              </td>
              <td style="margin: 0px; padding: 4px;" valign="top">';

      if ($memberName == $context['user']['name'] || $context['allow_admin']) {
        echo '
          <a onclick="if (!confirm(\'\xbfEstas seguro que deseas eliminar a este usuario de tus amigos?\')) return false;" href="' . $boardurl . '/amigos-eliminar/' . $row['memberName'] . '/" title="Eliminar usuario de mi lista de amigos">
            <img alt="Eliminar usuario de mi lista de amigos" src="' . $settings['images_url'] . '/eliminar.gif" width="8px" height="8px" />
          </a>
          &#32;-&#32;';
      }

      echo '
        <b>
          <span style="font-size: 12px;">
            <a href="' . $boardurl . '/perfil/' . $row['memberName'] . '" title="' . $row['realName'] . '">' . $row['realName'] . '</a>
          </span>
        </b>';

      if (!empty($row['personalText'])) {
        echo '&#32;-&#32;' . $row['personalText'];
      }

      if ($memberName == $context['user']['name'] || $context['allow_admin']) {
        if ($row['MONLINE'] == $row['BUDDY_ID'] && $row['MONLINE'] == $row['ID_MIEMBRO']) {
          echo '&#32;-&#32;<img src="' . $settings['images_url'] . '/icons/bullet-verde.gif" alt="Conectado/a" title="Conectado/a">';
        } else if ($row['MONLINE'] != $row['BUDDY_ID'] && $row['MONLINE'] != $row['ID_MIEMBRO']) {
          echo '&#32;-&#32;<img src="' . $settings['images_url'] . '/icons/bullet-rojo.gif" alt="Desconectado/a" title="Desconectado/a">';
        }
      }

      echo '
                </span>
                <br />
                <span style="color: green; font-size: 10px;">
                  <b>Es amigo desde:</b>
                  &nbsp;
                  ' . timeformat($row['time_updated']) . '
                </span>
              </td>
            </tr>
          </tbody>
        </table>
        <hr />';
    }

    $request = db_query($query, __FILE__, __LINE__);
    $records = mysqli_num_rows($request);
  }

  $previousPage = $actualPage - 1;
  $nextPage = $actualPage + 1;
  $lastPage = $records / $end;
  $residue = $records % $end;

  if ($residue > 0) {
    $lastPage = floor($lastPage) + 1;
  }

  echo '
    </div>
    <div class="windowbgpag" style="width: 539px;">';

  if ($actualPage > 1) {
    echo '<a href="' . $boardurl . '/perfil/', $memberName, '/lista-de-amigos-pag-' . $previousPage . '">&#171; anterior</a>';
  }

  if ($actualPage < $lastPage) {
    echo '<a href="' . $boardurl . '/perfil/', $memberName, '/lista-de-amigos-pag-' . $nextPage . '">siguiente &#187;</a>';
  }

    echo '
      <div class="clearBoth"></div>
    </div>
  </div>';

  menu4();
  menu5();
}

// Pendiente
function template_buddies2() {
  global $db_prefix, $context, $settings, $modSettings, $boardurl;

  menu2();
  sidebar();
  menu3();

  echo '
    <div style="float: left; margin-bottom: 8px;">
      <div class="windowbg" style="border: 1px solid #D7CFC6; width: 523px; padding: 8px; font-size: 11px;">';

  $memberName = censorText($context['member']['name']);
  $end = $modSettings['user_friends2'];
  $page = (int) $_GET['pag'];

  if (isset($page)) {
    $start = ($page-1)*$end;
    $actualPage = (int) $page;
  } else {
    $start = 0;
    $actualPage = 1;
  }

  $query = "
    SELECT 
      mem.ID_MEMBER AS ID_MIEMBRO, mem.realName, mem.memberName, mem.showOnline, mem.avatar, mem.personalText,
      b.ID_MEMBER, b.time_updated, b.BUDDY_ID, o.ID_MEMBER AS MONLINE, b2.BUDDY_ID, b2.ID_MEMBER, mem2.ID_MEMBER
    FROM ({$db_prefix}members AS mem, {$db_prefix}buddies AS b, {$db_prefix}members AS mem2, {$db_prefix}buddies AS b2, {$db_prefix}log_online AS o)
    WHERE b.ID_MEMBER = " . $context['member']['id'] . "
    AND b.BUDDY_ID = b2.BUDDY_ID
    AND mem.ID_MEMBER = b2.BUDDY_ID
    AND b2.ID_MEMBER = " . $context['user']['id'] . "
    AND b2.ID_MEMBER = mem2.ID_MEMBER
    AND mem2.ID_MEMBER = " . $context['user']['id'] . "
    GROUP BY b.BUDDY_ID DESC
    ORDER BY b.time_updated DESC";

  $request2 = db_query("
    {$query}
    LIMIT {$start}, {$end}", __FILE__, __LINE__);

  $total = db_query("
    SELECT * FROM ({$db_prefix}members AS mem, {$db_prefix}buddies AS b)
    WHERE b.ID_MEMBER = " . $context['member']['id'] . "
    AND b.BUDDY_ID = mem.ID_MEMBER
    GROUP BY b.BUDDY_ID DESC
    ORDER BY b.time_updated DESC", __FILE__, __LINE__);

  $count = mysqli_num_rows($request2);
  $contartotal = mysqli_num_rows($total);

  if ($count <= 0) {
    echo '<div class="noesta">' . $memberName . ' no tiene ning&uacute;n amigo a&ntilde;adido.</div>';
  } else if ($memberName == $context['user']['name']) {
    echo '<b class="size11">Acci&oacute;n no reconocida.-</b><hr />';
  } else {
    echo '
      <p style="margin: 0px; padding: 0px; float: left; width: 250px;">
        <a href="' . $boardurl . '/perfil/' . $memberName . '/amigos-en-comun/">' . $count . ' amigos en com&uacute;n</a>
      </p>
      <p align="right" style="margin: 0px; padding: 0px;">
        <a href="' . $boardurl . '/perfil/' . $memberName . '/lista-de-amigos/">' . $contartotal . ' amigos</a>
      </p>
      <hr />';

    while($row = mysqli_fetch_assoc($request2)) {
      echo '
        <table>
          <tbody>
            <tr>
              <td valign="top">
                <img style="width: 50px; height: 50px;" alt="" src="' . (!empty($row['avatar']) ? $row['avatar'] : $boardurl . '/avatar.gif') . '" onerror="error_avatar(this)" />
              </td>
              <td style="margin: 0px; padding: 4px;" valign="top">';

      if ($memberName == $context['user']['name'] || $context['allow_admin']) {
        echo '
          <a onclick="if (!confirm(\'\xbfEstas seguro que deseas eliminar a este usuario de tus amigos?\')) return false;" href="' . $boardurl . '/amigos-eliminar/' . $row['memberName'] . '/" title="Eliminar usuario de mi lista de amigos">
            <img alt="Eliminar usuario de mi lista de amigos" src="' . $settings['images_url'] . '/eliminar.gif" width="8px" height="8px">
          </a>&#32;-&#32;';
      }

      echo '
        <b>
          <span style="font-size: 12px;">
            <a href="' . $boardurl . '/perfil/' . $row['memberName'] . '" title="' . $row['realName'] . '">' . $row['realName'] . '</a>
          </span>
        </b>';

      if (!empty($row['personalText'])) {
        echo '&#32;-&#32;' . $row['personalText'];
      }

      if ($memberName == $context['user']['name'] || $context['allow_admin']) {
        if ($row['MONLINE'] == $row['ID_MIEMBRO']) {
          echo '&#32;-&#32;<img src="' . $settings['images_url'] . '/icons/bullet-verde.gif" alt="Conectado/a" title="Conectado/a">';
        } else {
          echo '&#32;-&#32;<img src="' . $settings['images_url'] . '/icons/bullet-rojo.gif" alt="Desconectado/a" title="Desconectado/a">';
        }
      }

      echo '
                </span>
                <br />
                <span style="color: green; font-size: 10px;">
                  <b>Es amigo desde:</b>
                  &nbsp;
                  ' . timeformat($row['time_updated']) . '
                </span>
              </td>
            </tr>
          </tbody>
        </table>
        <hr />';
    }

    $request = db_query($query, __FILE__, __LINE__);
    $records = mysqli_num_rows($query);
  }

  $previousPage = $actualPage - 1;
  $nextPage = $actualPage + 1;
  $lastPage = $records / $end;
  $residue = $records % $end;

  if ($residue > 0) {
    $lastPage = floor($lastPage) + 1;
  }

  echo '
    </div>
    <div class="windowbgpag" style="width: 539px;">';

  if ($actualPage > 1) {
    echo '<a href="' . $boardurl . '/perfil/', $memberName, '/lista-de-amigos-pag-' . $previousPage . '">&#171; anterior</a>';
  }

  if ($actualPage < $lastPage) {
    echo '<a href="' . $boardurl . '/perfil/', $memberName, '/lista-de-amigos-pag-' . $nextPage . '">siguiente &#187;</a>';
  }

  echo '
        <div class="clearBoth"></div>
      </div>
    </div>';

  menu4();
  menu5();
}

function template_profile_above() {}

function template_profile_below() {}

function template_theme() {}

function template_notification() {}

function template_pmprefs() {}

function template_showPermissions() {}

function template_statPanel() {}

?>