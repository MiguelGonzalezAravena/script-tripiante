<?php
require_once(dirname(dirname(__FILE__)) . '/Settings.php');

$search = $_GET['q'];

header("Location: {$boardurl}/google-search/?q={$search}");
?>