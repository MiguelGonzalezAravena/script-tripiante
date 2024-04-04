<?php
@require_once($_SERVER['DOCUMENT_ROOT'] . '/Settings.php');
@require_once($_SERVER['DOCUMENT_ROOT'] . '/SSI.php');

$user = htmlentities(addslashes($_REQUEST['user']));

$end = 10;
$page = (int) $_GET['pag'];

if (isset($page)) {
  $start = ($page - 1) * $end;
  $actualPage = $page;
} else {
  $start = 0;
  $actualPage = 1;
}

$query = "
  SELECT *
  FROM {$db_prefix}community_topic AS ct, {$db_prefix}communities AS c, {$db_prefix}members AS m
  WHERE ct.ID_MEMBER = m.ID_MEMBER
  AND m.memberName = '$user'
  AND c.ID_COMMUNITY = ct.ID_COMMUNITY
  ORDER BY ID_TOPIC DESC";

// Registros paginados
$request = db_query("
  {$query}
  LIMIT {$start}, {$end}", __FILE__, __LINE__);

$rows = mysqli_num_rows($request);

if ($rows == 0) {
  echo '<div class="noesta" style="width: 541px;">Este usuario no tiene temas creados.</div>';	
} else if ($rows > 0) {
  echo '
    <div id="ComuTemPerfil">
      <div class="clearBoth"></div>
      <table class="linksList" style="width: 541px;">
        <thead>
          <tr>
            <th>&nbsp;</th>
            <th style="text-align: left;">&Uacute;ltimos temas creados</th>
            <th>Comunidad</th>
            <th>Calificaci&oacute;n</th>
          </tr>
        </thead>
      <tbody>';

  while ($row = mysqli_fetch_assoc($request)) {
    echo '
      <tr>
        <td title="Comunidades">
          <img title="Comunidades" src="' . $settings['images_url'] . '/comunidades/temas.png" alt="" />
        </td>
        <td style="text-align: left;">
          <a href="' . $boardurl . '/comunidades/' . $row['friendly_url'] . '/' . $row['ID_TOPIC'] . '/' . ssi_amigable($row['subject']) . '.html"  class="titlePost" alt="' . $row['subject'] . '" title="' . $row['subject'] . '">' . reducir30($row['subject']) . '</a>
        </td>
        <td>
          <a title="' . $row['title'] . '" alt="' . $row['title'] . '" href="' . $boardurl . '/comunidades/' . $row['friendly_url'] . '">' . reducir22($row['title']) . '</a>
        </td>
        ' . votos($row['points']) . '
      </tr>';
  }

  echo '
        </tbody>
      </table>
    <div class="clearBoth"></div>
    <div class="windowbgpag" style="width: 537px;">';

  mysqli_free_result($request);

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

  if ($actualPage > 1) {
    echo '<a style="cursor: pointer;" onclick="ComuTemPerfil(\''  . $user . '\', \'' . $previousPage . '\');">&#171; anterior</a>';
  }

  if ($actualPage < $lastPage) {
    echo '<a style="cursor: pointer;" onclick="ComuTemPerfil(\''  . $user . '\', \'' . $nextPage . '\');">siguiente &#187;</a>';
  }

  echo '
      </div>
      <div class="clearBoth"></div>
    </div>';
}

?>