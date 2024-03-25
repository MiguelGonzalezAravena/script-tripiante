<?php
/*if (isset($_SERVER["HTTP_HOST"]))
	die('Hacking attempt...');
*/

@require_once($_SERVER['DOCUMENT_ROOT'].'/SSI.php');
@require_once($_SERVER['DOCUMENT_ROOT'].'/Settings.php');

ssi_actualizar_puntos();
?>