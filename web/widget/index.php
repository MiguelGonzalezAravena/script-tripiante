<?php
@require_once($_SERVER['DOCUMENT_ROOT'] . '/Settings.php');
@require_once($_SERVER['DOCUMENT_ROOT'] . '/SSI.php');

// TO-DO: ¿Está bien esa conexión?
$conexion = @mysqli_connect($db_server, $db_user, $db_passwd);
@mysqli_select_db($conexion, $db_name);
  
$cat = (int) $_REQUEST['cat'];
$tamano = (int) $_REQUEST['tamano'];
$end = (int) $_REQUEST['cantidad'];
$page = (int) $_GET['pag'];

if (isset($page)) {
  $start = ($page - 1) * $end;
  $actualPage = $page;
} else {
  $start = 0;
  $actualPage = 1;
}

echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml" lang="es" xml:lang="es" ><head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>' . $mbname . ' - Widget</title>
<style type="text/css">
body {
  font-family: Arial, Helvetica, sans-serif;
  font-size: 12px;
  margin: 0px;
  padding: 0px;
  background: #AAC0D8 url(' . $boardurl . '/wget/fondo-blanco.gif) repeat-x;
}

a {
  color: #000;
  text-decoration: none
}

a:hover {
  color:#936D14;
}

*:focus {
  outline:0px;
}

.nsfw{
  color: #FFbbBB
}

.item {
  width: ' . ($tamano != '' ? $tamano - 17 : 183) . 'px;
  overflow: hidden;
  height: 16px;
  margin: 2px 0px 0px 0px;
  padding: 0px;
  border-bottom: 1px solid #F4F4F4;
}

.exterior {
  width: ' . ($tamano != '' ? $tamano - 17 : 183) . 'px;
}
</style>
</head>
<body><div class="exterior">
';

if (!empty($cat)) {
  $add = "AND t.ID_BOARD = $cat";
}

$request = db_query("
  SELECT m.ID_TOPIC, m.ID_BOARD AS ID_BOARD2, m.hiddenOption, m.subject, b.name, b.description, b.ID_BOARD, t.isSticky, t.ID_TOPIC
  FROM ({$db_prefix}messages AS m, {$db_prefix}boards AS b, {$db_prefix}topics AS t)
  WHERE m.ID_TOPIC = t.ID_TOPIC
  AND b.ID_BOARD = t.ID_BOARD
  $add
  GROUP BY t.ID_TOPIC
  ORDER BY t.ID_TOPIC DESC
  LIMIT $end", __FILE__, __LINE__);

$count = mysqli_num_rows($request);

if ($count <= 0) {
  echo '
    <div class="noesta">
      <br /><br/ ><br /><br />
      Est&aacute; p&aacute;gina no existe.
      <br /><br /><br /><br /><br />
    </div>';
} else {
  while ($row = mysqli_fetch_assoc($request)) {
    echo '
      <div class="item">
        <div class="icon_img" style="float: left; margin: 0px 5px 0px 0px; width: 17px; height: 17px">
          <img alt="" title="' . $row['name'] . '" src="' . $boardurl . '/wget/icono-cat-' . $row['ID_BOARD2'] . '.gif" style="margin-top:-0px;" />
        </div>
        <a target="_blank" title="' . $row['subject'] . '" href="' . $boardurl . '/post/' . $row['ID_TOPIC'] . '/' . $row['description'] . '/' . ssi_amigable($row['subject']) . '.html">' . htmlentities(ssi_reducir2($row['subject'])) . '</a>
      </div>';
  }
}

if (empty($cat))  {
  $request = db_query("
    SELECT *
    FROM {$db_prefix}messages", __FILE__, __LINE__);
} else {
  $request = db_query("
    SELECT *
    FROM {$db_prefix}messages WHERE ID_BOARD = $cat", __FILE__, __LINE__);
}

$records = mysqli_num_rows($request);

echo '
      <center>
        <a href="' . $boardurl . '/" target="_parent">[ Ver m&aacute;s posts ]</a>
      </center>
    </body>
  </html>';

?>