<?php

function template_init() {
  global $context, $settings, $options, $txt;

  $settings['use_default_images'] = 'never';
  $settings['doctype'] = 'xhtml';
  $settings['theme_version'] = '1.1';
  $settings['use_tabs'] = true;
  $settings['use_buttons'] = true;
  $settings['seperate_sticky_lock'] = true;
}

function template_main_above() {
  global $context, $settings, $options, $scripturl, $txt, $modSettings, $slogan, $boardurl;

  $action = $_REQUEST['action'];

  echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
  <html xmlns="http://www.w3.org/1999/xhtml" lang="es" xml:lang="es" >
    <!--' . date("Y", time()) . ' ' . $boardurl . '/por Miguelithox-->
    <head profile="http://purl.org/NET/erdf/profile">
      <link rel="schema.dc" href="http://purl.org/dc/elements/1.1/" />
      <link rel="schema.foaf" href="http://xmlns.com/foaf/0.1/" />
      <meta name="google-site-verification" content="mum2lysef2BJ5eFJzD3uPj2ZQUwcgpYCqZvpo6a-WJc" />
      <meta http-equiv="Content-Type" content="text/html; charset=' . $context['character_set'] . '" />
      <title>' . $context['forum_name'] . ' - ' . $context['page_title'] . '</title>
      <link rel="stylesheet" type="text/css" href="' . $settings['images_url'] . '/estilo-tp1.css" />
      <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
      <script type="text/javascript" src="' . $settings['images_url'] . '/acciones-tp.js"></script>
      <link rel="icon" href="' . $settings['images_url'] . '/favicon.png" type="image/x-icon" />
      <link rel="shortcut icon" href="' . $settings['images_url'] . '/favicon.png" type="image/x-icon" />
      <link rel="apple-touch-icon" href="' . $settings['images_url'] . '/apple-touch-icon.png" />
      <meta name="keywords" content="' . $context['page_title'] . ',rapidshare,megaupload,mediafire,-1,-2,descarga,directa 2010" />
      <meta name="robots" content="All" />
      <link rel="alternate" type="application/atom+xml" title="&Uacute;ltimos posts" href="' . $boardurl . '/rss/ultimos-post" />
      <link rel="alternate" type="application/atom+xml" title="&Uacute;ltimos comentarios" href="' . $boardurl . '/rss/ultimos-comment" />
      <meta name="revisit-after" content="1 days" />
      <meta name="title" content="' . $context['forum_name'] . ' - ' . $context['page_title'] . '" />
      <meta name="keywords" content="rapidshare,megaupload,mediafire,descarga-directa,bajar,mp3,casitaweb,rigo,caladj,elblogderigo,lawebderigo,elforoderigo,linksharing,enlaces,juegos,musica,links,noticias,imagenes,videos,animaciones,arte,tecnologia,celulares,argentina,comunidad,cw,infornes,2008,2009,warez,linksharing,web 2.0, 2010" />
      <meta name="description" content="Un sitio de distracci&oacute;n, para descargar M&uacute;sica, Juegos, Programas, Peliculas, enterarte las &Uacute;ltimas noticias, Animate viendo animaciones, peliculas online, chatea, conoce amigos, aporta, Todo eso y mucho m&aacute;s!!!.-" />
      <meta name="generator" content="' . $context['page_title'] . ' Para descargar / bajar / instalar gratis / Gratuito / rigo / casitaweb / 2.0 / linksaring / rapidshare / descargas / directas / megaupload / mediafire / software / freeware / serial / gratis / programas / musica / juegos / peliculas" />
      <link rel="search" type="application/opensearchdescription+xml" title="' . $context['forum_name'] . '" href="' . $settings['images_url'] . '/buscador-cw.xml" />
      <link rel="up" href="#top" title="Volver al principio de esta pagina" />
    </head>
    <body id="top" >
      <b class="rtop">
        <b class="rtop1">
          <b></b>
        </b>
        <b class="rtop2">
          <b></b>
        </b>
        <b class="rtop3"></b>
        <b class="rtop4"></b>
        <b class="rtop5"></b>
      </b>
      <div id="maincontainer">
        <div id="head">
          <div id="logo" style="margin-bottom: 10px;">
            <a href="' . $boardurl . '/" title="' . $context['forum_name'] . ' - ' . $slogan . '" id="logoi">
              <img src="' . $settings['images_url'] . '/espacio.gif" alt="' . $context['forum_name'] . ' - ' . $slogan . '" title="' . $context['forum_name'] . ' - ' . $slogan . '" align="top" border="0" />
            </a>
          </div>
          <div align="right" id="banner" style="position: relative;">';

  if ($context['user']['is_logged']) {
    // Is the forum in maintenance mode?
    if ($context['in_maintenance'] && $context['user']['is_admin'])
      echo '
        <div align="center" style="font-family: Verdana; font-weight: bold;">
          <p>
            <img src="' . $settings['images_url'] . '/construction.png" alt="Mantenci&oacute;n" title="Mantenci&oacute;n">&nbsp;' . $txt[616] . '&nbsp;<img src="' . $settings['images_url'] . '/construction.png" alt="Mantenci&oacute;n" title="Mantenci&oacute;n">
          </p>
        </div>';
  }

  echo '
          <div style="margin: 0px; padding: 0px; position: absolute; bottom: 0px; right: 0px;">
            <ul id="nav_header_ul">
              <li class="' . ($action == 'comunidades' ? 'active' : '') . '">
                <a href="' . $boardurl . '/comunidades/">Comunidades</a>
              </li>
            </ul>
            <div class="clearBoth" style="margin: 0px; padding:0px;">
          </div>
        </div>
      </div>
    </div>';

  template_menu();

  echo '
    <table id="bodyarea" style="text-align: left; background: #FFFFFF url(\'' . $settings['images_url'] . '/bg-cuerpo.png\') repeat-x top left;">
      <tr>
        <td valign="top" style="padding-left: 12px; padding-top: 15px;">
          <div id="cuerpocontainer">';
}

function template_main_below() {
  global $context, $settings, $options, $scripturl, $txt;

  echo '
                </div>
              </td>
            </tr>
          </table>
          <div style="margin: 0px; padding: 0px; background: #FFFFFF; height: 6px!important; vertical-align: bottom;">
            <div style="margin: 0px; padding: 0px; width: 50%; float: left; height: 6px !important; vertical-align: bottom; background: #FFFFFF url(' . $settings['images_url'] . '/abajo-left.png) no-repeat top left;"></div>
            <div style="margin: 0px; padding: 0px; width:50%; float: right; height: 6px !important; vertical-align: bottom; background: #FFFFFF url(' . $settings['images_url'] . '/abajo-right.png) no-repeat top right;"></div>
          </div>
          <div id="pie" style="margin:0px;">
            &copy; ' . date("Y", time()) . ' 
            <a href="' . $boardurl . '/" title="' . $context['forum_name'] . '">' . $context['forum_name'] . '</a>
            &nbsp;|&nbsp;
            <a href="' . $boardurl . '/protocolo/" title="Protocolo">Protocolo</a>
            &nbsp;|&nbsp;
            <a href="' . $boardurl . '/enlazanos/" title="Enl&aacute;zanos">Enl&aacute;zanos</a>
            &nbsp;|&nbsp;
            <a href="' . $boardurl . '/widget/" title="Widget">Widget</a>
            &nbsp;|&nbsp;
            <a href="' . $boardurl . '/contactanos/" title="Contacto">Contacto</a>
            &nbsp;|&nbsp;
            <a href="' . $boardurl . '/recomendar/" title="Recomendar ' . $context['forum_name'] . '">Recomendar ' . $context['forum_name'] . '</a>
            &nbsp;|&nbsp;
            <a href="' . $boardurl . '/mapa-del-sitio/" title="Mapa del sitio">Mapa del sitio</a>
            <br />
            <a href=http://www.ademails.com/estadisticas1059994843.htm>
              <script type="text/javascript" language="JavaScript">
                <!--
                document.write("<img src=\"http://www.ademails.com/cgi-bin/contador.cgi?ID=1059994843");
                document.write("&referer=");
                document.write(escape(document.referrer));
                document.write("\" border=0 alt=\"Estadisticas\">");
                // -->
              </script>
              <noscript>
                <img src=http://www.ademails.com/cgi-bin/contador.cgi?ID=1059994843 border=0 alt="Estadisticas">
              </noscript>
            </a>
          </div>
        </div>
      </div>
      <b class="rbott">
        <b class="rbott5"></b>
        <b class="rbott4"></b>
        <b class="rbott3"></b>
        <b class="rbott2">
          <b></b>
        </b>
        <b class="rbott1">
          <b></b>
        </b>
      </b>
      </body>
    </html>';

  if ($context['show_load_time'])
    echo '
    <span class="smalltext">' . $txt['smf301'] . $context['load_time'] . $txt['smf302'] . $context['load_queries'] . $txt['smf302b'] . '</span>';
}

function template_menu() {
  global $context, $settings, $options, $scripturl, $txt, $boardurl;

  echo '
    <div style="margin: 0px; padding: 0px;">
      <div id="menu-top" style="margin: 0px; padding: 0px;">
        <span class="menu_izq">
          <a href="' . $boardurl . '/" title="Inicio">Inicio</a>
          &nbsp;
          <font color="#FFFFFF">-</font>
          &nbsp;
          <a href="' . $boardurl . '/buscar/" title="Buscador">Buscador</a>
          &nbsp;
          <font color="#FFFFFF">-</font>
          &nbsp;
          <a href="' . $boardurl . '/ayuda/" title="Ayuda">Ayuda</a>
          &nbsp;
          <font color="#FFFFFF">-</font>
          &nbsp;
          <a href="' . $boardurl . '/chat/" title="Chat">Chat</a>
          &nbsp;
          <font color="#FFFFFF">-</font>
          &nbsp;';

  if ($context['user']['is_guest']) {
    echo '
      <a href="' . $boardurl . '/ingresar/" title="Ingresar">Ingresar</a>
      &nbsp;
      <font color="#FFFFFF">-</font>
      &nbsp;
      <a href="' . $boardurl . '/registrarse/" title="Registrate!">
        <b>Registrate!</b>
      </a>';
  } else {
    echo '
      <a href="' . $boardurl . '/tops/" title="TOPs">TOPs</a>
      &nbsp;
      <font color="#FFFFFF">-</font>
      &nbsp;
      <a valign="middle" href="' . $boardurl . '/agregar/" title="Publicar">
        <b>Publicar</b>
      </a>';

    if ($context['allow_admin']) {
      echo '
      &nbsp;
      <font color="#FFFFFF">-</font>
      &nbsp;
      <a valign="middle" href="' . $boardurl . '/admin/" title="ADM">
        <b>ADM</b>
      </a>';
    }
  }

  echo '</span>';

  if ($context['user']['is_guest']) {
    echo '
      <span class="menu_centro">
        <a href="' . $boardurl . '/ingresar/">Iniciar Sesi&oacute;n</a>';
  } else {
    echo '<span class="menu_centro" style="#margin-top: 4px; _margin-top: 4px;">';

    if ($context['user']['unread_messages']) {
      $unread_messages = ($context['user']['unread_messages'] > 0 ? $context['user']['unread_messages'] : '');
      echo '
        <a valign="middle" href="' . $boardurl . '/mensajes/" title="' . $unread_messages ' MP Nuevo">
          <img alt="" src="' . $settings['images_url'] . '/icons/mensaje_nuevo.gif" style="padding-top: 6px; #padding-top: 0px; _padding-top: 0px;" align="top" border="0" />
        </a>
        <a href="' . $boardurl . '/mensajes/" title="' . $unread_messages  . ' MP Nuevo">
          <font class="size9" color="#FFFFFF">
            <b>(' . $unread_messages . ')</b>
          </font>
        </a>';
    } else {
      echo '
        <a valign="middle" href="' . $boardurl . '/mensajes" title="Mensajes Privados">
          <img alt="" src="' . $settings['images_url'] . '/icons/mensaje.gif" style="padding-top: 6px; #padding-top: 0px; _padding-top: 0px;" align="top" border="0" />
        </a>';
    }

    echo '
      <font color="#FFFFFF">|</font>
      <a class="icons monitor" href="' . $boardurl . '/monitoreo-user/" title="Monitoreo de usuario">
        <img alt="" src="' . $settings['images_url'] . '/espacio.gif" align="top" border="0" />
      </a>
      <font color="#FFFFFF">|</font>
      <a class="icons fav2" href="' . $boardurl . '/favoritos/" title="Mis Favoritos">
        <img alt="" src="' . $settings['images_url'] . '/espacio.gif" align="top" border="0" />
      </a>
      <font color="#FFFFFF">|</font>
      <a class="icons cuenta" href="' . $boardurl . '/editar-perfil/" title="Editar mi perfil">
        <img alt="" src="' . $settings['images_url'] . '/espacio.gif" align="top" border="0" />
      </a>
      <font color="#FFFFFF">|</font>
      <a href="' . $boardurl . '/perfil/' . $context['user']['name'] . '" title="Mi Perfil">' . $context['user']['name'] . '</a>
      &nbsp;
      <font color="#FFFFFF">[<a href="' . $boardurl . '/salir/" onclick="if (!confirm(\'\xbfEstas seguro que desea salir de su cuenta?\')) return false;" title="Salir">X</a>]</font>
      &nbsp;
      <a class="icons his-mod" href="' . $boardurl . '/hist-mod/" title="Historial de moderaci&oacute;n">
        <img alt="" src="' . $settings['images_url'] . '/espacio.gif" align="top" border="0" /
      </a>';
  }

  echo '
    </span>
    <span class="menu_der">';

  categorias();

  echo '
      </span>
    </div>';

  if (!empty($settings['enable_news']) && !empty($context['random_news_line']) && $context['user']['is_logged'])
    echo '<div id="mensaje-top">' . $context['random_news_line'] . '</div>';

  echo '</div>';
}

function categorias($output_method = 'echo') {
  global $db_prefix, $txt, $scripturl, $modSettings;

  $action = $_REQUEST['action'];

  if($action == 'comunidades') {
    $request = db_query("
      SELECT ID_CATEGORY, name, friendly_url
      FROM {$db_prefix}community_categories
      ORDER BY name ASC", __FILE__, __LINE__);

    $context['categories'] = array();

    while ($row = mysqli_fetch_assoc($request)) {
      $context['categories'][] = array(
        'ID_CATEGORY' => $row['ID_CATEGORY'],
        'name' => $row['name'],
        'friendly_url' => $row['friendly_url'],
      );
    }

    mysqli_free_result($request);

    echo '
      <select style="width: 207px; background: #C0CDD4;" onchange="ir_a_categoria_com()" name="categoria" id="categoria">
        <option value="" selected="selected">Ver categor&iacute;as</option>';

    foreach ($context['categories'] as $categories) {
      echo '<option value="' . $categories['friendly_url'] . '"' . ($categories['selected'] ? ' selected="selected"' : '') . '>' . $categories['name'] . '</option>';
    }

    echo '</select>';
  } else {
    $request = db_query("
      SELECT b.ID_BOARD, b.name, b.childLevel, c.name AS catName, b.description
      FROM {$db_prefix}boards AS b
      LEFT JOIN {$db_prefix}categories AS c ON (c.ID_CAT = b.ID_CAT)
      ORDER BY name ASC", __FILE__, __LINE__);

    $context['boards'] = array();

    while ($row = mysqli_fetch_assoc($request)) {
      $context['boards'][] = array(
        'id' => $row['ID_BOARD'],
        'name' => $row['name'],
        'description' => $row['description'],
      );
    }

    mysqli_free_result($request);

    echo '
      <select style="width: 202px;" onchange="ir_a_categoria()" name="categoria" id="categoria" class="select">
        <option value="" selected="selected">Ver categor&iacute;as</option>';

    foreach ($context['boards'] as $board) {
      echo '<option value="' . $board['description'] . '"' . ($board['selected'] ? ' selected="selected"' : '') . '>' . $board['name'] . '</option>';
    }

    echo '</select>';
  }
}

function template_button_strip() {}
function theme_linktree() {}

?>