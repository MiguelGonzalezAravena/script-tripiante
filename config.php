<?php
$mbname = 'Tripiante';
$slogan = 'Compartir es gratuito';
$language = 'english';
$boardurl = 'http://tripiante.net';
$webmaster_email = 'staff.tripiante@gmail.com';
$cookiename = 'Tripiante2010';
$chatid	=	'80701498';
$maintenance = 0;
$mtitle = '<b style="color:Red;font-size:13px;font-family:Verdana;">Tripiante se encuentra en mantenimiento.</b><br/>';
$mmessage = '<b style="color:Green;font-size:11px;font-family:Verdana;">Sepa disculpar las molestias causadas, si desea contactarse con nosotros: <a href="mailto:staff.tripiante@gmail.com" title="e-mail">staff.tripiante@gmail.com</a></b>';
$db_server = 'localhost';
$db_name = 'db_name';
$db_user = 'db_user';
$db_passwd = 'db_passwd';
$db_prefix = '';
$db_persist = 0;
$db_error_send = 0;
$boarddir = '/home/tripiant/public_html';
$sourcedir = '/home/tripiant/public_html/web/archivos/raiz';
$db_last_error = 0;
if (!file_exists($sourcedir) && file_exists($boarddir . '/web/archivos/raiz'))
	$sourcedir = $boarddir . '/web/archivos/raiz';
$db_character_set = 'UTF8';
$db_connection = @mysql_connect($db_server, $db_user, $db_passwd);
@mysql_select_db($db_name, $db_connection);
define('SMF', 1);
@require_once($sourcedir . '/QueryString.php');
@require_once($sourcedir . '/Subs.php');
@require_once($sourcedir . '/Errors.php');
@require_once($sourcedir . '/Load.php');
@require_once($sourcedir . '/Security.php');
?>