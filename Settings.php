<?php
/**********************************************************************************
* Settings.php                                                                    *
***********************************************************************************
* SMF: Simple Machines Forum                                                      *
* Open-Source Project Inspired by Zef Hemel (zef@zefhemel.com)                    *
* =============================================================================== *
* Software Version:           SMF 1.1                                             *
* Software by:                Simple Machines (http://www.simplemachines.org)     *
* Copyright 2006 by:          Simple Machines LLC (http://www.simplemachines.org) *
*           2001-2006 by:     Lewis Media (http://www.lewismedia.com)             *
* Support, News, Updates at:  http://www.simplemachines.org                       *
***********************************************************************************
* This program is free software; you may redistribute it and/or modify it under   *
* the terms of the provided license as published by Simple Machines LLC.          *
*                                                                                 *
* This program is distributed in the hope that it is and will be useful, but      *
* WITHOUT ANY WARRANTIES; without even any implied warranty of MERCHANTABILITY    *
* or FITNESS FOR A PARTICULAR PURPOSE.                                            *
*                                                                                 *
* See the "license.txt" file for details of the Simple Machines license.          *
* The latest version can always be found at http://www.simplemachines.org.        *
**********************************************************************************/

########## Forum Info ##########
$mbname = 'Tripiante'; # The name of your forum.
$slogan = 'Compartir es gratuito'; # The name of your forum.
$language = 'english'; # The default language file set for the forum.
$boardurl = 'http://tripiante.net'; # URL to your forum's folder. (without the trailing /!)
$webmaster_email = 'staff.tripiante@gmail.com'; # Email address to send emails from. (like noreply@yourdomain.com.)
$cookiename = 'Tripiante2010'; # Name of the cookie to set for authentication.
$chatid = '80701498'; # Id of your Chat

########## Maintenance ##########
# Note: If $maintenance is set to 2, the forum will be unusable!  Change it to 0 to fix it.
$maintenance = 0; # Set to 1 to enable Maintenance Mode, 2 to make the forum untouchable. (you'll have to make it 0 again manually!)
$mtitle = '<b style="color:Red;font-size:13px;font-family:Verdana;">Tripiante se encuentra en mantenimiento.</b><br/>'; # Title for the Maintenance Mode message.
$mmessage = '<b style="color:Green;font-size:11px;font-family:Verdana;">Sepa disculpar las molestias causadas, si desea contactarse con nosotros: <a href="mailto:staff.tripiante@gmail.com" title="e-mail">staff.tripiante@gmail.com</a></b>'; # Description of why the forum is in maintenance mode.

########## Database Info ##########
$db_server = 'localhost';
$db_name = 'db_name';
$db_user = 'db_user';
$db_passwd = 'db_passwd';
$db_prefix = '';
$db_persist = 0;
$db_error_send = 0;

########## Directories/Files ##########
# Note: These directories do not have to be changed unless you move things.
$boarddir = '/home/tripiant/public_html'; # The absolute path to the forum's folder. (not just '.'!)
$sourcedir = '/home/tripiant/public_html/web/archivos/raiz'; # Path to the Sources directory.

########## Error-Catching ##########
# Note: You shouldn't touch these settings.
$db_last_error = 1267855563;

# Make sure the paths are correct... at least try to fix them.
if (!file_exists($sourcedir) && file_exists($boarddir . '/web/archivos/raiz')) {
  $sourcedir = $boarddir . '/web/archivos/raiz';
}

$db_character_set = 'UTF8';

?>