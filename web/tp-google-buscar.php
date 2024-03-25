<?php
@require_once($_SERVER['DOCUMENT_ROOT'].'/Settings.php');

$search	=	$_GET['q'];

Header("Location: {$boardurl}/google-search/?q={$search}");
?>