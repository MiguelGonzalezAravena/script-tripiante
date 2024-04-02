<?php
@require_once($_SERVER['DOCUMENT_ROOT'].'/Settings.php');

$search = $_GET['search'];
$buscador_tipo = $_GET['buscador_tipo'];

if ($buscador_tipo	==	'g') {
  header("Location: {$boardurl}/google-search/?cof=FORID%3A9&cx=008407595988527806565:gpsb5aspgg8&ie=UTF-8&sa=Buscar&q={$search}");
} else if ($buscador_tipo	==	't') {
  header("Location: {$boardurl}/buscar/&search={$search}&userspec=0&sort=ID_MSG|desc&brd=0");
}

?>