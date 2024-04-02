<?php
@require_once($_SERVER['DOCUMENT_ROOT'] . '/Settings.php');

$search	=	$_GET['q'];

header("Location: {$boardurl}/google-search/?q={$search}");
?>