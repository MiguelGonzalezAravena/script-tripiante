<?php
@require_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');

$user = htmlentities(addslashes($_REQUEST['user']), ENT_QUOTES, 'UTF-8');

$end = 10;
$page = (int) $_GET['pag'];

if (isset($page)) {
  $start = ($page - 1) * $end;
  $actualPage = $page;
} else {
  $start = 0;
  $actualPage = 1;
}

$request = db_query("
  SELECT c.ID_COMMUNITY, c.title, c.friendly_url, cm.ID_COMMUNITY, cm.ID_MEMBER, cm.ID, mem.ID_MEMBER, mem.memberName
  FROM ({$db_prefix}communities AS c, {$db_prefix}members AS mem, {$db_prefix}community_members AS cm)
  WHERE cm.ID_MEMBER = mem.ID_MEMBER
  AND mem.memberName = '$user'
  AND c.ID_COMMUNITY = cm.ID_COMMUNITY
  ORDER BY cm.ID DESC
  LIMIT $start, $end", __FILE__, __LINE__);

$rows = mysqli_num_rows($request);

if ($rows == 0) {
  echo '<div class="noesta" style="width: 541px;">Este usuario no est&aacute; unido a comunidades.</div>';
} else if ($rows > 0) {
  echo '
    <div id="ComuMemPerfil">
      <div class="clearBoth"></div>
      <table class="linksList" style="width: 541px;">
        <thead>
          <tr>
            <th>&nbsp;</th>
            <th style="text-align: left;">Es miembro de las comunidades</th>
            <th>Por</th>
            <th>Miembros</th>
            <th>Temas</th>
          </tr>
        </thead>
        <tbody>';

  while ($row = mysqli_fetch_assoc($request)) {
    $request = db_query("
      SELECT COUNT(ID_COMMUNITY) AS temas
      FROM community_topic
      WHERE ID_COMMUNITY = " . $row['ID_COMMUNITY'], __FILE__, __LINE__);

    $row1 = mysqli_fetch_assoc($request);

    $request = db_query("
      SELECT COUNT(ID_COMMUNITY) AS miembros
      FROM community_members
      WHERE ID_COMMUNITY = " . $row['ID_COMMUNITY'], __FILE__, __LINE__);

    $row2 = mysqli_fetch_assoc($request);

    $request = db_query("
      SELECT *
      FROM ({$db_prefix}members AS m, {$db_prefix}communities AS c)
      WHERE m.ID_MEMBER = c.ID_MEMBER
      AND c.ID_COMMUNITY = " . $row['ID_COMMUNITY'], __FILE__, __LINE__);

    $row3 = mysqli_fetch_assoc($request);

    echo '
      <tr>
        <td title="Comunidades">
          <img title="Comunidades" src="' . $settings['images_url'] . '/comunidades/comunidad.png" alt="Comunidades" />
        </td>
        <td style="text-align: left;">
          <a title="' . $row['title'] . '" alt="' . $row['title'] . '" href="' . $boardurl . '/comunidades/' . $row['friendly_url'] . '" class="titlePost">' . $row['title'] . '</a>
        </td>
        <td>
          <a href="' . $boardurl . '/perfil/' . $row3['memberName'] . '" alt="' . $row3['realName'] . '" title="' . $row3['realName'] . '">' . $row3['realName'] . '</a>
        </td>
        <td>' . $row2['miembros'] . '</td>
        <td>' . $row1['temas'] . '</td>
      </tr>
      ';
  }

  echo '
      </tbody>
    </table>
    <div class="clearBoth"></div>
    <div class="windowbgpag" style="width: 537px;">';

  $request = db_query("
    SELECT *
    FROM ({$db_prefix}communities AS c, {$db_prefix}members AS mem, {$db_prefix}community_members AS cm)
    WHERE c.ID_COMMUNITY = cm.ID_COMMUNITY
    AND cm.ID_MEMBER = mem.ID_MEMBER
    AND mem.memberName = '$user'", __FILE__, __LINE__);

  $records = mysqli_num_rows($request);
  $previousPage = $actualPage - 1;
  $nextPage = $actualPage + 1;
  $lastPage = $records / $end;
  $residue = $records % $end;

  if ($residue > 0) {
    $lastPage = floor($lastPage) + 1;
  }

  if ($actualPage > 1) {
    echo '<a style="cursor: pointer;" onclick="ComuMemPerfil(\''  . $user . '\', \'' . $previousPage . '\');">&#171; anterior</a>';
  }

  if ($actualPage < $lastPage) {
    echo '<a style="cursor: pointer;" onclick="ComuMemPerfil(\''  . $user . '\', \'' . $nextPage . '\');">siguiente &#187;</a>';
  }

  echo '
      </div>
      <div class="clearBoth"></div>
    </div>';
}

?>