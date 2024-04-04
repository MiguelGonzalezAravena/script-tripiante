<?php
@require_once($_SERVER['DOCUMENT_ROOT'] . '/Settings.php');

$search = htmlentities(addslashes($_GET['search']), ENT_QUOTES, 'UTF-8');
$buscador_tipo = htmlentities(addslashes($_GET['buscador_tipo']), ENT_QUOTES, 'UTF-8');

if ($buscador_tipo == 'g') {
  header('Location: ' . $boardurl . '/google-search/?cof=FORID%3A9&cx=008407595988527806565:gpsb5aspgg8&ie=UTF-8&sa=Buscar&q=' . $search);
} else if ($buscador_tipo == 't') {
  // TO-DO: Verificar si se puede cambiar &search= por ?search=
  header('Location: ' . $boardurl . '/buscar/&search=' . $search . '&userspec=0&sort=ID_MSG|desc&brd=0');
}

?>