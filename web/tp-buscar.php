<?php
@require_once($_SERVER['DOCUMENT_ROOT'].'/Settings.php');

$search			=	$_GET['search'];
$buscador_tipo	=	$_GET['buscador_tipo'];

if($buscador_tipo	==	'g')
{
Header("Location: {$boardurl}/google-search/?cof=FORID%3A9&cx=008407595988527806565:gpsb5aspgg8&ie=UTF-8&sa=Buscar&q={$search}");
}
elseif($buscador_tipo	==	't')
{
Header("Location: {$boardurl}/buscar/&search={$search}&userspec=0&sort=ID_MSG|desc&brd=0");
}

?>