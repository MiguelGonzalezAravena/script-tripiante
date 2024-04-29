<?php
define('SMF', 1);

require_once(dirname(__FILE__) . '/Settings.php');
require_once($sourcedir . '/Errors.php');
require_once($sourcedir . '/Load.php');
require_once($sourcedir . '/QueryString.php');

$forum_version = 'SMF 1.1.11';
$script_version = '1.0';

error_reporting(E_ALL);
$time_start = microtime();

foreach (array('db_character_set') as $variable) {
  if (isset($GLOBALS[$variable])) {
    unset($GLOBALS[$variable]);
  }
}

if (@version_compare(PHP_VERSION, '4.2.3') != 1) {
  require_once($sourcedir . '/Subs-Compat.php');
}

if (!empty($maintenance) && $maintenance == 2) {
  db_fatal_error();
}

if (empty($db_persist)) {
  $db_connection = @mysqli_connect($db_server, $db_user, $db_passwd);
} else {
  $db_connection = @mysqli_connect($db_server, $db_user, $db_passwd, null, null, null, MYSQLI_CLIENT_PERSISTENT);
}

if (!$db_connection || !@mysqli_select_db($db_connection, $db_name)) {
  db_fatal_error();
}

reloadSettings();
cleanRequest();

$context = array();

if (empty($modSettings['rand_seed']) || mt_rand(1, 250) == 69) {
  smf_seed_generator();
}

if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/vnd.wap.xhtml+xml') !== false) {
  $_REQUEST['wap2'] = 1;
} else if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'text/vnd.wap.wml') !== false) {
  if (strpos($_SERVER['HTTP_USER_AGENT'], 'DoCoMo/') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'portalmmm/') !== false) {
    $_REQUEST['imode'] = 1;
  } else {
    $_REQUEST['wap'] = 1;
  }
}

if (!defined('WIRELESS')) {
  define('WIRELESS', isset($_REQUEST['wap']) || isset($_REQUEST['wap2']) || isset($_REQUEST['imode']));
}

if (WIRELESS) {
  define('WIRELESS_PROTOCOL', isset($_REQUEST['wap']) ? 'wap' : (isset($_REQUEST['wap2']) ? 'wap2' : (isset($_REQUEST['imode']) ? 'imode' : '')));

  $modSettings['enableCompressedOutput'] = '0';
  $modSettings['defaultMaxMessages'] = 5;
  $modSettings['defaultMaxTopics'] = 9;
  if (WIRELESS_PROTOCOL == 'wap') {
    header('Content-Type: text/vnd.wap.wml');
  }
}

if (!empty($modSettings['enableCompressedOutput']) && !headers_sent() && ob_get_length() == 0) {
  if (@ini_get('zlib.output_compression') == '1' || @ini_get('output_handler') == 'ob_gzhandler' || @version_compare(PHP_VERSION, '4.2.0') == -1) {
    $modSettings['enableCompressedOutput'] = '0';
  } else {
    ob_start('ob_gzhandler');
  }
}

if (empty($modSettings['enableCompressedOutput'])) {
  ob_start();
}

set_error_handler('error_handler');
loadSession();
call_user_func(smf_main());
obExit(null, null, true);

function smf_main() {
  global $modSettings, $settings, $user_info, $topic, $maintenance, $sourcedir, $kill_proxyblocker;

  if (isset($_GET['action']) && $_GET['action'] == 'keepalive')
    die;

  loadUserSettings();
  loadBoard();
  loadTheme();

  $hostaddr = gethostbyaddr($_SERVER['REMOTE_ADDR']);

  if (
    !empty($modSettings['proxyblock_index'])
    && empty($kill_proxyblocker)
    && (!$hostaddr
    || $hostaddr == '.'
    || empty($_SERVER['HTTP_ACCEPT_ENCODING'])
    || !empty($_SERVER['HTTP_X_FORWARDED_FOR'])
    || !empty($_SERVER['HTTP_X_FORWARDED'])
    || !empty($_SERVER['HTTP_FORWARDED_FOR'])
    || !empty($_SERVER['HTTP_VIA'])
    || in_array($_SERVER['REMOTE_PORT'], array(8080, 80, 6588, 8000, 3128, 553, 554))
    || empty($_SERVER['HTTP_CONNECTION'])
    || stripos($hostaddr, "tor-exit")
    || IsTorExitPoint())
  ) {
    fatal_lang_error('on_proxy');
  }

  is_not_banned();
  loadPermissions();

  if (empty($_REQUEST['action']) || !in_array($_REQUEST['action'], array('dlattach', 'jsoption', '.xml'))) {
    writeLog();

    if (!empty($modSettings['hitStats']))
      trackStats(array('hits' => '+'));
  }

  if (!empty($maintenance) && !allowedTo('admin_forum')) {
    if (isset($_REQUEST['action']) && ($_REQUEST['action'] == 'login2' || $_REQUEST['action'] == 'logout')) {
      require_once($sourcedir . '/LogInOut.php');
      return $_REQUEST['action'] == 'login2' ? 'Login2' : 'Logout';
    } else {
      require_once($sourcedir . '/Subs-Auth.php');
      return 'InMaintenance';
    }
  }
  else if (empty($modSettings['allow_guestAccess']) && $user_info['is_guest'] && (!isset($_REQUEST['action']) || !in_array($_REQUEST['action'], array('coppa', 'login', 'login2', 'register', 'register2', 'reminder', 'activate', 'smstats', 'help', 'verificationcode'))))
  {
    require_once($sourcedir . '/Subs-Auth.php');
    return 'KickGuest';
  } else if (empty($_REQUEST['action'])) {
    if (empty($topic)) {
      require_once($sourcedir . '/Recent.php');
      return 'RecentPosts';
    } else {
      require_once($sourcedir . '/Display.php');
      return 'Display';
    }
  }

  $actionArray = array(
    'activate' => array('Register.php', 'Activate'),
    'admin' => array('Admin.php', 'Admin'),
    'articles' => array('Articles.php', 'ArticlesMain'),
    'announce' => array('Post.php', 'AnnounceTopic'),
    'ban' => array('ManageBans.php', 'Ban'),
    'boardrecount' => array('Admin.php', 'AdminBoardRecount'),
    'bookmarks' => array('Bookmarks.php', 'Bookmarks'),
    'bookmarks2' => array('Bookmarks2.php', 'Bookmarks'),
    'buddies' => array('Buddies.php', 'BuddiesMain'),
    'sitemap' => array('Sitemap.php', 'ShowSiteMap'),
    'enlazanos' => array('Enlazanos.php', 'ShowHelp'),
    'tos' => array('Tos.php', 'Tos'),
    'buddy' => array('Subs-Members.php', 'BuddyListToggle'),
    'chat' => array('Chat.php', 'Chat'),
    'contact' => array('Contact.php', 'Contact'),
    'cleanperms' => array('Admin.php', 'CleanupPermissions'),
    'convertentities' => array('Admin.php', 'ConvertEntities'),
    'denuncias' => array('Denuncias.php', 'Denuncias'),
    'convertutf8' => array('Admin.php', 'ConvertUtf8'),
    'coppa' => array('Register.php', 'CoppaForm'),
    'comunidades' => array('Comunidades.php', 'ComunidadesMain'),
    'deletemsg' => array('RemoveTopic.php', 'DeleteMessage'),
    'denunciar' => array('Denunciar.php', 'ShowHelp'),
    'denunciar2' => array('Denunciar2.php', 'ShowHelp'),
    'denunciar3' => array('Denunciar3.php', 'ShowHelp'),
    'detailedversion' => array('Admin.php', 'VersionDetail'),
    'display' => array('Display.php', 'Display'),
    'do' => array('Do.php', 'Hacer'),
    'dumpdb' => array('DumpDatabase.php', 'DumpDatabase2'),
    'featuresettings' => array('ModSettings.php', 'ModifyFeatureSettings'),
    'featuresettings2' => array('ModSettings.php', 'ModifyFeatureSettings2'),
    'findmember' => array('Subs-Auth.php', 'JSMembers'),
    'gallery' => array('Gallery.php', 'GalleryMain'),
    'google' => array('Google.php', 'Google'),
    'im' => array('PersonalMessage.php', 'MessageMain'),
    'jsoption' => array('Themes.php', 'SetJavaScript'),
    'jsmodify' => array('Post.php', 'JavaScriptModify'),
    'lock' => array('LockTopic.php', 'LockTopic'),
    'login' => array('LogInOut.php', 'Login'),
    'login2' => array('LogInOut.php', 'Login2'),
    'logout' => array('LogInOut.php', 'Logout'),
    'maintain' => array('Admin.php', 'Maintenance'),
    'manageattachments' => array('ManageAttachments.php', 'ManageAttachments'),
    'manageboards' => array('ManageBoards.php', 'ManageBoards'),
    'managesearch' => array('ManageSearch.php', 'ManageSearch'),
    'markasread' => array('Subs-Boards.php', 'MarkRead'),
    'membergroups' => array('ManageMembergroups.php', 'ModifyMembergroups'),
    'mergetopics' => array('SplitTopics.php', 'MergeTopics'),
    'mlist' => array('Memberlist.php', 'Memberlist'),
    'modifycat' => array('ManageBoards.php', 'ModifyCat'),
    'modifykarma' => array('Karma.php', 'ModifyKarma'),
    'modlog' => array('Modlog.php', 'ViewModlog'),
    'monitor' => array('Monitor.php', 'Monitor'),
    'movetopic' => array('MoveTopic.php', 'MoveTopic'),
    'movetopic2' => array('MoveTopic.php', 'MoveTopic2'),
    'news' => array('ManageNews.php', 'ManageNews'),
    'notepad' => array('Profile.php', 'misnotas'),
    'optimizetables' => array('Admin.php', 'OptimizeTables'),
    'packageget' => array('PackageGet.php', 'PackageGet'),
    'packages' => array('Packages.php', 'Packages'),
    'permissions' => array('ManagePermissions.php', 'ModifyPermissions'),
    'pm' => array('PersonalMessage.php', 'MessageMain'),
    'post' => array('Post.php', 'Post'),
    'post2' => array('Post.php', 'Post2'),
    'postsettings' => array('ManagePosts.php', 'ManagePostSettings'),
    'printpage' => array('Printpage.php', 'PrintTopic'),
    'printpage2' => array('Printpage2.php', 'PrintImg'),
    'profile' => array('Profile.php', 'ModifyProfile'),
    'profile2' => array('Profile.php', 'ModifyProfile2'),
    'protocolo' => array('Protocolo.php', 'Protocolo'),
    'publicity' => array('Publicity.php', 'Publicity'),
    'quotefast' => array('Post.php', 'QuoteFast'),
    'quickmod' => array('Subs-Boards.php', 'QuickModeration'),
    'quickmod2' => array('Subs-Boards.php', 'QuickModeration2'),
    'recent' => array('Recent.php', 'RecentPosts'),
    'recomendar' => array('Recomendar.php', 'Recomendar'),
    'regcenter' => array('ManageRegistration.php', 'RegCenter'),
    'register' => array('Register.php', 'Register'),
    'register2' => array('Register.php', 'Register2'),
    'reminder' => array('Reminder.php', 'RemindMe'),
    'removetopic2' => array('RemoveTopic.php', 'RemoveTopic2'),
    'removeoldtopics2' => array('RemoveTopic.php', 'RemoveOldTopics2'),
    'repairboards' => array('RepairBoards.php', 'RepairBoards'),
    'requestmembers' => array('Subs-Auth.php', 'RequestMembers'),
    /*
    'search' => array('Search.php', 'PlushSearch1'),
    'search2' => array('Search.php', 'PlushSearch2'),
    'searchtag' => array('SearchTags.php', 'PlushSearch1'),
    'searchtag2' => array('SearchTags.php', 'PlushSearch2'),
    */
    'buscar' => array('Buscar.php', 'Buscar'),
    'buscar2' => array('Buscar.php', 'Buscar2'),
    'sendtopic' => array('SendTopic.php', 'SendTopic'),
    'sendtopic2' => array('SendTopic2.php', 'SendTopic2'),
    'serversettings' => array('ManageServer.php', 'ModifySettings'),
    'serversettings2' => array('ManageServer.php', 'ModifySettings2'),
    'smileys' => array('ManageSmileys.php', 'ManageSmileys'),
    'spellcheck' => array('Subs-Post.php', 'SpellCheck'),
    'splittopics' => array('SplitTopics.php', 'SplitTopics'),
    'stats' => array('Stats.php', 'DisplayStats'),
    'sticky' => array('LockTopic.php', 'Sticky'),
    'tags' => array('Tags.php', 'TagsMain'),
    'theme' => array('Themes.php', 'ThemesMain'),
    'trackip' => array('Profile.php', 'trackIP'),
    'viewErrorLog' => array('ManageErrors.php', 'ViewErrorLog'),
    'viewmembers' => array('ManageMembers.php', 'ViewMembers'),
    'viewprofile' => array('Profile.php', 'ModifyProfile'),
    'verificationcode' => array('Register.php', 'VerificationCode'),
    'viewquery' => array('ViewQuery.php', 'ViewQuery'),
    'who' => array('Who.php', 'Who'),
    'widget' => array('Widget.php', 'ShowHelp'),
    '.xml' => array('News.php', 'ShowXmlFeed'),
    'shop_inventory' => array('shop/ShopAdmin.php', 'ShopInventory'),
    'shop_usergroup' => array('shop/ShopAdmin.php', 'ShopUserGroup'),
  );

  if (!isset($_REQUEST['action']) || !isset($actionArray[$_REQUEST['action']])) {
    if (!empty($settings['catch_action'])) {
      require_once($sourcedir . '/Themes.php');
      return 'WrapAction';
    }

    require_once($sourcedir . '/Recent.php');
    return 'RecentPosts';
  }

  require_once($sourcedir . '/' . $actionArray[$_REQUEST['action']][0]);
  return $actionArray[$_REQUEST['action']][1];
}

function IsTorExitPoint() {
  return (
    gethostbyname(ReverseIPOctets($_SERVER['REMOTE_ADDR']) . "." .
      $_SERVER['SERVER_PORT'] . "." .
      ReverseIPOctets($_SERVER['SERVER_ADDR']) .
      ".ip-port.exitlist.torproject.org")
    == "127.0.0.2"
  );
}

function ReverseIPOctets($inputip) {
  $ipoc = explode('.', $inputip);
  return $ipoc[3] . '.' . $ipoc[2] . '.' . $ipoc[1] . '.' . $ipoc[0];
}

?>