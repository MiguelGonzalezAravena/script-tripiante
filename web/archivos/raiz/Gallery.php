<?php
if (!defined('SMF'))
	die('Hacking attempt...');

function GalleryMain()
{
	global $modSettings, $boardurl, $boarddir;

	loadtemplate('Gallery');
	if (loadlanguage('Gallery') == false)
		loadLanguage('Gallery','english');
	$subActions = array(
		'main' => 'main',
		'view' => 'ViewPicture',
		'adminset'=> 'AdminSettings',
		'adminset2'=> 'AdminSettings2',
		'delete' => 'DeletePicture',
		'delete2' => 'DeletePicture2',
		'edit' => 'EditPicture',
		'edit2' => 'EditPicture2',
		'comment' => 'AddComment',
		'comment2' => 'AddComment2',
		'delcomment' => 'DeleteComment',
		'viewc' => 'ViewC',
		'add' => 'AddPicture',
		'add2' => 'AddPicture2',
	);


	@$sa = $_GET['sa'];
	if (!empty($subActions[$sa]))
		$subActions[$sa]();
	else
		main();

}

function main()
{
	global $context, $scripturl, $mbname, $txt, $db_prefix, $modSettings, $user_info;

	$context['page_title'] = $txt[18];

	$context['sub_template']  = 'main';
}

function ViewPicture()
{
	global $context, $mbname, $db_prefix,$modSettings,$user_info, $scripturl,$txt, $ID_MEMBER;

	loadlanguage('Post');

	$id = (int) $_REQUEST['id'];
	if($id == '')
		fatal_error($txt['gallery_error_no_pic_selected']);

	$context['gallery_pic_id'] = $id;

	//Comments allowed check
    $dbresult = db_query("SELECT p.allowcomments FROM {$db_prefix}gallery_pic as p WHERE ID_PICTURE = $id LIMIT 1", __FILE__, __LINE__);
	$row = mysql_fetch_assoc($dbresult);
	mysql_free_result($dbresult);


	$context['sub_template']  = 'add_comment';
	$context['page_title'] = $txt[18];


	$context['smileys'] = array(
		'postform' => array(),
		'popup' => array(),
	);

	if(function_exists('parse_bbc'))
		$esmile = 'embarrassed.gif';
	else
		$esmile = 'embarassed.gif';

	if (empty($modSettings['smiley_enable']) && $user_info['smiley_set'] != 'none')
		$context['smileys']['postform'][] = array(
			'smileys' => array(
				array('code' => ':)', 'filename' => 'smiley.gif', 'description' => $txt[287]),
				array('code' => ';)', 'filename' => 'wink.gif', 'description' => $txt[292]),
				array('code' => ':D', 'filename' => 'cheesy.gif', 'description' => $txt[289]),
				array('code' => ';D', 'filename' => 'grin.gif', 'description' => $txt[293]),
				array('code' => '>:(', 'filename' => 'angry.gif', 'description' => $txt[288]),
				array('code' => ':(', 'filename' => 'sad.gif', 'description' => $txt[291]),
				array('code' => ':o', 'filename' => 'shocked.gif', 'description' => $txt[294]),
				array('code' => '8)', 'filename' => 'cool.gif', 'description' => $txt[295]),
				array('code' => '???', 'filename' => 'huh.gif', 'description' => $txt[296]),
				array('code' => '::)', 'filename' => 'rolleyes.gif', 'description' => $txt[450]),
				array('code' => ':P', 'filename' => 'tongue.gif', 'description' => $txt[451]),
				array('code' => ':-[', 'filename' => $esmile, 'description' => $txt[526]),
				array('code' => ':-X', 'filename' => 'lipsrsealed.gif', 'description' => $txt[527]),
				array('code' => ':-\\', 'filename' => 'undecided.gif', 'description' => $txt[528]),
				array('code' => ':-*', 'filename' => 'kiss.gif', 'description' => $txt[529]),
				array('code' => ':\'(', 'filename' => 'cry.gif', 'description' => $txt[530])
			),
			'last' => true,
		);
	elseif ($user_info['smiley_set'] != 'none')
	{
		$request = db_query("
			SELECT code, filename, description, smileyRow, hidden
			FROM {$db_prefix}smileys
			WHERE hidden IN (0, 2)
			ORDER BY smileyRow, smileyOrder", __FILE__, __LINE__);
		while ($row = mysql_fetch_assoc($request))
			$context['smileys'][empty($row['hidden']) ? 'postform' : 'popup'][$row['smileyRow']]['smileys'][] = $row;
		mysql_free_result($request);
	}

	// Clean house... add slashes to the code for javascript.
	foreach (array_keys($context['smileys']) as $location)
	{
		foreach ($context['smileys'][$location] as $j => $row)
		{
			$n = count($context['smileys'][$location][$j]['smileys']);
			for ($i = 0; $i < $n; $i++)
			{
				$context['smileys'][$location][$j]['smileys'][$i]['code'] = addslashes($context['smileys'][$location][$j]['smileys'][$i]['code']);
				$context['smileys'][$location][$j]['smileys'][$i]['js_description'] = addslashes($context['smileys'][$location][$j]['smileys'][$i]['description']);
			}

			$context['smileys'][$location][$j]['smileys'][$n - 1]['last'] = true;
		}
		if (!empty($context['smileys'][$location]))
			$context['smileys'][$location][count($context['smileys'][$location]) - 1]['last'] = true;
	}
	$settings['smileys_url'] = $modSettings['smileys_url'] . '/' . $user_info['smiley_set'];

	// Allow for things to be overridden.
	if (!isset($context['post_box_columns']))
		$context['post_box_columns'] = 60;
	if (!isset($context['post_box_rows']))
		$context['post_box_rows'] = 12;
	if (!isset($context['post_box_name']))
		$context['post_box_name'] = 'comment';
	if (!isset($context['post_form']))
		$context['post_form'] = 'cprofile';

	$context['show_bbc'] = !empty($modSettings['enableBBC']) && !empty($settings['show_bbc']);

	if (!empty($modSettings['disabledBBC']))
	{
		$disabled_tags = explode(',', $modSettings['disabledBBC']);
		foreach ($disabled_tags as $tag)
			$context['disabled_tags'][trim($tag)] = true;
	}

	isAllowedTo('smfgallery_view');

	//Get the picture ID
	$id = (int) $_REQUEST['id'];
	if($id == '')
		fatal_error($txt['gallery_error_no_pic_selected']);

    $dbresult = db_query("SELECT p.ID_PICTURE, p.width, p.height, p.allowcomments, p.ID_CAT, p.keywords, p.commenttotal, p.filesize, p.filename, p.views, p.points, p.title, p.ID_MEMBER, m.memberName, m.realName, m.estado_icon, p.date, p.description FROM {$db_prefix}gallery_pic as p
    LEFT JOIN {$db_prefix}members AS m ON (p.ID_MEMBER = m.ID_MEMBER) WHERE p.ID_PICTURE = $id   LIMIT 1", __FILE__, __LINE__);
	$row = mysql_fetch_assoc($dbresult);
	$context['gallery_pic'] = array(
		'ID_PICTURE' => $row['ID_PICTURE'],
		'ID_MEMBER' => $row['ID_MEMBER'],
		'commenttotal' => $row['commenttotal'],
		'points' => $row['points'],
		'views' => $row['views'],
		'title' => $row['title'],
		'filename' => $row['filename'],
		'date' => $row['date'],
		'memberName' => $row['memberName'],
		'realName' => $row['realName'],
		'estado_icon' => $row['estado_icon'],
	);
	mysql_free_result($dbresult);
	  $dbresult = db_query("UPDATE {$db_prefix}gallery_pic
		SET views = views + 1 WHERE ID_PICTURE = $id LIMIT 1", __FILE__, __LINE__);
	$context['sub_template']  = 'view_picture';
	$context['page_title'] = $txt[18];
	if (!empty($modSettings['gallery_who_viewing']))
	{
		$context['can_moderate_forum'] = allowedTo('moderate_forum');
			if(function_exists('parse_bbc'))
			{
				//SMF 1.1
				//Taken from Display.php
				// Start out with no one at all viewing it.
				$context['view_members'] = array();
				$context['view_members_list'] = array();
				$context['view_num_hidden'] = 0;

				// Search for members who have this picture id set in their GET data.
				$request = db_query("
					SELECT
						lo.ID_MEMBER, lo.logTime, mem.realName, mem.memberName, mem.showOnline,
						mg.onlineColor, mg.ID_GROUP, mg.groupName
					FROM {$db_prefix}log_online AS lo
						LEFT JOIN {$db_prefix}members AS mem ON (mem.ID_MEMBER = lo.ID_MEMBER)
						LEFT JOIN {$db_prefix}membergroups AS mg ON (mg.ID_GROUP = IF(mem.ID_GROUP = 0, mem.ID_POST_GROUP, mem.ID_GROUP))
					WHERE INSTR(lo.url, 's:7:\"gallery\";s:2:\"sa\";s:4:\"view\";s:2:\"id\";s:1:\"$id\";') OR lo.session = '" . ($user_info['is_guest'] ? 'ip' . $user_info['ip'] : session_id()) . "'", __FILE__, __LINE__);
				while ($row = mysql_fetch_assoc($request))
				{
					if (empty($row['ID_MEMBER']))
						continue;

					if (!empty($row['onlineColor']))
						$link = '<a href="' . $scripturl . '?action=profile;u=' . $row['ID_MEMBER'] . '" style="color: ' . $row['onlineColor'] . ';">' . $row['realName'] . '</a>';
					else
						$link = '<a href="' . $scripturl . '?action=profile;u=' . $row['ID_MEMBER'] . '">' . $row['realName'] . '</a>';

					$is_buddy = in_array($row['ID_MEMBER'], $user_info['buddies']);
					if ($is_buddy)
						$link = '<b>' . $link . '</b>';
					if (!empty($row['showOnline']) || allowedTo('moderate_forum'))
						$context['view_members_list'][$row['logTime'] . $row['memberName']] = empty($row['showOnline']) ? '<i>' . $link . '</i>' : $link;
					$context['view_members'][$row['logTime'] . $row['memberName']] = array(
						'id' => $row['ID_MEMBER'],
						'username' => $row['memberName'],
						'name' => $row['realName'],
						'group' => $row['ID_GROUP'],
						'href' => $scripturl . '?action=profile;u=' . $row['ID_MEMBER'],
						'link' => $link,
						'is_buddy' => $is_buddy,
						'hidden' => empty($row['showOnline']),
					);

					if (empty($row['showOnline']))
						$context['view_num_hidden']++;
				}
				$context['view_num_guests'] = mysql_num_rows($request) - count($context['view_members']);
				mysql_free_result($request);
				krsort($context['view_members']);
				krsort($context['view_members_list']);
			}
			else
			{
				$context['view_members'] = array();
				$context['view_members_list'] = array();
				$context['view_num_hidden'] = 0;
				$request = db_query("
					SELECT mem.ID_MEMBER, IFNULL(mem.realName, 0) AS realName, mem.showOnline
					FROM {$db_prefix}log_online AS lo
						LEFT JOIN {$db_prefix}members AS mem ON (mem.ID_MEMBER = lo.ID_MEMBER)
					WHERE INSTR(lo.url, 's:7:\"gallery\";s:2:\"sa\";s:4:\"view\";s:2:\"id\";s:1:\"$id\";')", __FILE__, __LINE__);
				while ($row = mysql_fetch_assoc($request))
					if (!empty($row['ID_MEMBER']))
					{
						if (!empty($row['showOnline']) || allowedTo('moderate_forum'))
							$context['view_members_list'][] = empty($row['showOnline']) ? '<i><a href="' . $scripturl . '?action=profile;u=' . $row['ID_MEMBER'] . '">' . $row['realName'] . '</a></i>' : '<a href="' . $scripturl . '?action=profile;u=' . $row['ID_MEMBER'] . '">' . $row['realName'] . '</a>';
						$context['view_members'][] = array(
							'id' => $row['ID_MEMBER'],
							'name' => $row['realName'],
							'href' => $scripturl . '?action=profile;u=' . $row['ID_MEMBER'],
							'link' => '<a href="' . $scripturl . '?action=profile;u=' . $row['ID_MEMBER'] . '">' . $row['realName'] . '</a>',
							'hidden' => empty($row['showOnline']),
						);

						if (empty($row['showOnline']))
							$context['view_num_hidden']++;
					}

			$context['view_num_guests'] = mysql_num_rows($request) - count($context['view_members']);
				mysql_free_result($request);
			}

	}
}

function AddPicture()
{
	global $context, $mbname, $txt, $modSettings, $db_prefix;

	isAllowedTo('smfgallery_add');
	$context['sub_template']  = 'add_picture';
	$context['page_title'] = $txt[18];
	require_once('SSI.php');
	ssi_grupos();
	if($context['Turista'])
	fatal_error('Los usuarios de rango Turistas no pueden agregar im&aacute;genes.-', true);
}

function AddPicture2()
{
	global $ID_MEMBER, $txt, $db_prefix, $modSettings, $sourcedir, $context, $boardurl, $gd2;

	isAllowedTo('smfgallery_add');

	$title = htmlspecialchars($_REQUEST['title'],ENT_QUOTES);

	$filename = $_POST['filename'];
	$title = $_POST['title'];
	$ID_MEMBER = $context['user']['id'];
	$t = time();

	if (trim($title) == '')
		fatal_error($txt['gallery_error_no_title'],false);
@require_once('SSI.php');
ssi_grupos();

if($context['Turista']) {
fatal_error('Los usuarios de rango Turistas no pueden agregar im&aacute;genes.-', true);
} elseif(empty($title)) {
echo '<div align="center"><div class="box_errors"><div class="box_title" style="width: 388px"><div class="box_txt box_error" align="left">&iexcl;Atenci&oacute;n!</div><div class="box_rss"><img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width:14px;height:12px;" border="0" /></div></div><div class="windowbg" style="width:380px;padding:4px;"><br />Debes agregar un t&iacute;tulo.-<br /><br /><input class="login" style="font-size: 11px;" type="button" title="Volver atras" value="Volver atras" onclick="history.back()" /> <input class="login" style="font-size: 11px;" type="submit" title="Ir a la P&aacute;gina principal" value="Ir a la P&aacute;gina principal" onclick="location.href=\'/\'" /><br /><br /></div></div><br /></div><div style="clear:both"></div>';
} elseif(empty($filename)) {
echo '<div align="center"><div class="box_errors"><div class="box_title" style="width: 388px"><div class="box_txt box_error" align="left">&iexcl;Atenci&oacute;n!</div><div class="box_rss"><img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width:14px;height:12px;" border="0" /></div></div><div class="windowbg" style="width:380px;padding:4px;"><br />Debes agregar una imagen.-<br /><br /><input class="login" style="font-size: 11px;" type="button" title="Volver atras" value="Volver atras" onclick="history.back()" /> <input class="login" style="font-size: 11px;" type="submit" title="Ir a la P&aacute;gina principal" value="Ir a la P&aacute;gina principal" onclick="location.href=\'/\'" /><br /><br /></div></div><br /></div><div style="clear:both"></div>';
} else {
db_query("INSERT INTO {$db_prefix}gallery_pic (filename, title, ID_MEMBER, date) VALUES ('$filename', '$title', '$ID_MEMBER', '$t')", __FILE__, __LINE__);
if (isset($modSettings['shopVersion']))
db_query("UPDATE {$db_prefix}members SET money = money + " . $modSettings['gallery_shop_picadd'] . ", moneyBank = moneyBank + " . $modSettings['gallery_shop_picadd'] . "  WHERE ID_MEMBER = '$ID_MEMBER' LIMIT 1", __FILE__, __LINE__);
Header("Location: {$boardurl}/imagenes/".$context['user']['name']."");
}
}

function EditPicture()
{
	global $context, $mbname, $txt, $ID_MEMBER, $db_prefix, $modSettings;

	is_not_guest();
	ssi_grupos();
	if($context['Turista'])
	fatal_error('Los usuarios de rango Turistas no pueden agregar im&aacute;genes.-', true);
	$id = (int) $_REQUEST['id'];
	if (empty($id))
		fatal_error($txt['gallery_error_no_pic_selected']);
    $dbresult = db_query("SELECT p.ID_PICTURE, p.thumbfilename, p.allowcomments, p.commenttotal, p.filesize, p.filename, p.approved, p.views, p.title, p.ID_MEMBER, m.memberName, m.realName, p.date, p.description
    FROM {$db_prefix}gallery_pic as p
    LEFT JOIN {$db_prefix}members AS m ON (m.ID_MEMBER = p.ID_MEMBER) WHERE ID_PICTURE = $id LIMIT 1", __FILE__, __LINE__);
	$row = mysql_fetch_assoc($dbresult);

	//Gallery picture information
	$context['gallery_pic'] = array(
		'ID_PICTURE' => $row['ID_PICTURE'],
		'ID_MEMBER' => $row['ID_MEMBER'],
		'commenttotal' => $row['commenttotal'],
		'views' => $row['views'],
		'title' => $row['title'],
		'filename' => $row['filename'],
		'date' => timeformat($row['date']),
		'memberName' => $row['memberName'],
		'realName' => $row['realName'],
	);
	mysql_free_result($dbresult);

	if(AllowedTo('smfgallery_manage') || (AllowedTo('smfgallery_edit') && $ID_MEMBER == $context['gallery_pic']['ID_MEMBER']))
	{


		$context['page_title'] = $mbname . ' - ' . $txt['gallery_text_title'] . ' - ' . $txt['gallery_form_editpicture'];
		$context['sub_template']  = 'edit_picture';

	}
	else
	{
		fatal_error($txt['gallery_error_noedit_permission']);
	}


}

function EditPicture2()
{
	global $ID_MEMBER, $txt, $db_prefix, $modSettings, $sourcedir, $gd2, $context, $boardurl;
	global $context;

	is_not_guest();
	$id = (int) $_REQUEST['id'];
	if (empty($id))
		fatal_error($txt['gallery_error_no_pic_selected']);

	// Check the user permissions
    $dbresult = db_query("SELECT ID_MEMBER, title, filename FROM {$db_prefix}gallery_pic WHERE ID_PICTURE = $id LIMIT 1", __FILE__, __LINE__);
	$row = mysql_fetch_assoc($dbresult);
	$memID = $row['ID_MEMBER'];
	$filename2 = $row['filename'];
	$oldthumbfilename  = $row['thumbfilename'];
	$request	=	db_query("SELECT * FROM {$db_prefix}members WHERE ID_MEMBER = " . $memID, __FILE__, __LINE__);
	$row2		=	mysql_fetch_assoc($request);
	if (AllowedTo('smfgallery_manage') || (AllowedTo('smfgallery_edit') && $ID_MEMBER == $memID))
	{

		$title = htmlentities($_REQUEST['title'], ENT_QUOTES, "UTF-8");
		$filename = $_REQUEST['filename'];
		if (trim($title) == '')
			fatal_error($txt['gallery_error_no_title'],false);
		db_query("UPDATE {$db_prefix}gallery_pic SET title = '$title', filename = '$filename' WHERE ID_PICTURE = $id LIMIT 1", __FILE__, __LINE__);

$ID_MODERATOR	=	$context['user']['id'];
$ID_MEMBER		=	$memID;
$ID_TOPIC		=	$id;
$TYPE			=	'Imagen';
$ACTION			=	'modify';
$subject		=	$title;
$reason			=	htmlentities($_POST['causa'], ENT_QUOTES, "UTF-8");
if (!empty($modSettings['modlog_enabled']) && allowedTo('modify_any')) {
db_query("INSERT INTO {$db_prefix}mod_history (ID_MODERATOR, ID_MEMBER, ID_TOPIC, TYPE, ACTION, subject, reason) VALUES ('" . $ID_MODERATOR . "', '" . $ID_MEMBER . "', '" . $ID_TOPIC . "', '" . $TYPE . "', '" . $ACTION . "', '" . $subject . "', '" . $reason . "')", __FILE__, __LINE__);
}

			redirectexit($boardurl . '/imagenes/' . $row2['memberName']);
	}
	else
		fatal_error($txt['gallery_error_noedit_permission']);

}

function DeletePicture()
{
	global $context, $mbname, $txt, $ID_MEMBER, $db_prefix;

	is_not_guest();

	$id = (int) $_REQUEST['id'];
	if (empty($id))
		fatal_error($txt['gallery_error_no_pic_selected']);

	//Check if the user owns the picture or is admin
    $dbresult = db_query("SELECT p.ID_PICTURE, p.thumbfilename, p.width, p.height, p.allowcomments, p.ID_CAT, p.keywords, p.commenttotal, p.filesize, p.filename, p.approved, p.views, p.title, p.ID_MEMBER, m.memberName, m.realName, p.date, p.description
    FROM {$db_prefix}gallery_pic as p
    LEFT JOIN {$db_prefix}members AS m ON (m.ID_MEMBER = p.ID_MEMBER) WHERE ID_PICTURE = $id  LIMIT 1", __FILE__, __LINE__);
	$row = mysql_fetch_assoc($dbresult);

	//Gallery picture information
	$context['gallery_pic'] = array(
		'ID_PICTURE' => $row['ID_PICTURE'],
		'ID_MEMBER' => $row['ID_MEMBER'],
		'commenttotal' => $row['commenttotal'],
		'views' => $row['views'],
		'title' => $row['title'],
		'description' => $row['description'],
		'filesize' => round($row['filesize']  / 1024, 2),
		'filename' => $row['filename'],
		'thumbfilename' => $row['thumbfilename'],
		'width' => $row['width'],
		'height' => $row['height'],
		'allowcomments' => $row['allowcomments'],
		'ID_CAT' => $row['ID_CAT'],
		'date' => timeformat($row['date']),
		'keywords' => $row['keywords'],
		'memberName' => $row['memberName'],
		'realName' => $row['realName'],
	);
	mysql_free_result($dbresult);

	if (AllowedTo('smfgallery_manage') || (AllowedTo('smfgallery_delete') && $ID_MEMBER == $context['gallery_pic']['ID_MEMBER']))
	{
		$context['page_title'] = $txt[18];
		$context['sub_template']  = 'delete_picture';

	}
	else
	{
		fatal_error($txt['gallery_error_nodelete_permission']);
	}


}

function DeletePicture2()
{
	global $txt, $ID_MEMBER, $db_prefix, $modSettings, $context;

	$id = (int) $_REQUEST['id'];
	if (empty($id))
		fatal_error($txt['gallery_error_no_pic_selected']);

	//Check if the user owns the picture or is admin
    $dbresult = db_query("SELECT p.ID_PICTURE, p.filename, p.ID_MEMBER, p.title FROM {$db_prefix}gallery_pic as p WHERE ID_PICTURE = $id LIMIT 1 ", __FILE__, __LINE__);
	$row = mysql_fetch_assoc($dbresult);
	$memID = $row['ID_MEMBER'];

	if(AllowedTo('smfgallery_manage') || (AllowedTo('smfgallery_delete') && $ID_MEMBER == $memID))
	{
		//Delete Large image
		@unlink($modSettings['gallery_path'] . $row['filename']);
		//Delete Thumbnail
		@unlink($modSettings['gallery_path'] . $row['thumbfilename']);

		//Delete all the picture related db entries
		db_query("DELETE FROM {$db_prefix}gallery_comment WHERE ID_PICTURE = $id", __FILE__, __LINE__);
		db_query("DELETE FROM {$db_prefix}gallery_pic WHERE ID_PICTURE = $id", __FILE__, __LINE__);
		db_query("DELETE FROM {$db_prefix}points WHERE ID_TOPIC = $id AND TYPE = 'imagenes'", __FILE__, __LINE__);
		db_query("DELETE FROM {$db_prefix}bookmarks WHERE ID_TOPIC = $id AND TYPE = 'imagen'", __FILE__, __LINE__);

$ID_MODERATOR	=	$context['user']['id'];
$ID_MEMBER		=	$memID;
$ID_TOPIC		=	$id;
$TYPE			=	'Imagen';
$ACTION			=	'remove';
$subject		=	$row['title'];
$reason			=	htmlentities($_POST['causa'], ENT_QUOTES, "UTF-8");
if (!empty($modSettings['modlog_enabled']) && allowedTo('modify_any')) {
db_query("INSERT INTO {$db_prefix}mod_history (ID_MODERATOR, ID_MEMBER, ID_TOPIC, TYPE, ACTION, subject, reason) VALUES ('" . $ID_MODERATOR . "', '" . $ID_MEMBER . "', '" . $ID_TOPIC . "', '" . $TYPE . "', '" . $ACTION . "', '" . $subject . "', '" . $reason . "')", __FILE__, __LINE__);
}
		// Update the SMF Shop Points
			if (isset($modSettings['shopVersion']))
 				{
				db_query("
					UPDATE {$db_prefix}members
				 	SET money = money - " . $modSettings['gallery_shop_picadd'] . "
				 	WHERE ID_MEMBER = {$memID}
				 	LIMIT 1 ", __FILE__, __LINE__);
					}

		$requesti = db_query("
		SELECT g.ID_MEMBER, g.title
		FROM ({$db_prefix}gallery_pic AS g)
		WHERE g.ID_PICTURE = $id
		LIMIT 1 ", __FILE__, __LINE__);
	list ($starter, $subject) = mysql_fetch_row($requesti);
	mysql_free_result($requesti);

	if ($starter == $ID_MEMBER && !allowedTo('remove_any'))
		isAllowedTo('remove_own');
	else
		isAllowedTo('remove_any');


				// Redirect to the users image page.
		redirectexit('action=gallery;sa=myimages;u=' . $ID_MEMBER);
	}
	else
	{
		fatal_error($txt['gallery_error_nodelete_permission']);
	}


}

function dpuntos()
{
	global $scripturl, $context, $txt, $db_prefix, $ID_MEMBER, $modSettings, $boardurl, $memberContext, $themeUser, $mbname, $boardurl;

	$context['sub_template']  = 'dpuntos';
	$context['page_title'] = $txt[18];

	$cantidad = (float) $_GET['cantidad'];
	$db = mysql_query("SELECT *
	FROM {$db_prefix}members AS m WHERE ".$context['user']['id']." = m.ID_MEMBER");
while ($grup = mysql_fetch_assoc($db))
{
$context['money'] = $grup['money'];
}
mysql_free_result($db);
    if ($context['money'] < $cantidad)
		fatal_error('No tienes esa cantidad de puntos.', false);
	elseif ($cantidad < 0)
		fatal_error('Los puntos solamente son positivos.', false);
	elseif ($cantidad == 0)
		fatal_error('Debes ingresar una cantidad valida.', false);


	$id = (int) $_REQUEST['id'];
	$user = $_GET['user'];
	$userincr = $context['user']['id'];
	if($id == '')
	fatal_error($txt['gallery_error_no_pic_selected'], false);
	if($cantidad == '')
	fatal_error('Debes especificar una candidad.', v);
	if($user == '')
	fatal_error('Debes especificar un usuario.', false);
	if($user == $context['user']['id'])
	fatal_error('No puedes dar puntos a tus imagenes.');
	$errorr = mysql_query("SELECT *	FROM {$db_prefix}gallery_cat WHERE id_user = $userincr AND id_img = {$id} LIMIT 1");
	$yadio = mysql_num_rows($errorr) != 0 ? true : false;
	mysql_free_result($errorr);
	 	if ($yadio)
    	fatal_error('Ya has dado puntos a esta imagen.', false);
      	if($cantidad > 10)
    	fatal_error('No puedes dar m&aacute;s de 10 puntos.', false);

            mysql_query("
				UPDATE {$db_prefix}members
				SET money = money - {$cantidad}
				WHERE ID_MEMBER = $userincr
				LIMIT 1");
			mysql_query("
				UPDATE {$db_prefix}members
				SET money = money + {$cantidad}
				WHERE ID_MEMBER = {$user}
				LIMIT 1");
		   mysql_query("
				UPDATE {$db_prefix}gallery_pic
				SET puntos = puntos + {$cantidad}
				WHERE ID_PICTURE = {$id}
				LIMIT 1");
			mysql_query("INSERT INTO {$db_prefix}gallery_cat (id_img,id_user)
values('$id', '$userincr')");
	Header("Location: {$boardurl}/?action=gallery;sa=dpuntos2;id=$id;cant=$cantidad");
}

function dpuntos2()
{	global $context, $mbname, $txt;

	$context['sub_template']  = 'dpuntos2';
	$context['page_title'] = $txt[18];
}

function AddComment2()
{
	global $db_prefix, $ID_MEMBER, $txt, $modSettings;
	isAllowedTo('smfgallery_comment');
	$comment = htmlspecialchars($_REQUEST['comment'],ENT_QUOTES);
	$id = (int) $_REQUEST['id'];
	if (empty($id))
		fatal_error($txt['gallery_error_no_pic_selected']);
    $dbresult = db_query("SELECT p.allowcomments FROM {$db_prefix}gallery_pic as p WHERE ID_PICTURE = $id LIMIT 1", __FILE__, __LINE__);
	$row = mysql_fetch_assoc($dbresult);
	mysql_free_result($dbresult);
	if (trim($comment) == '')
	fatal_error($txt['gallery_error_no_comment'],false);
	$commentdate = time();
	db_query("INSERT INTO {$db_prefix}gallery_comment
	(ID_MEMBER, comment, date, ID_PICTURE)
	VALUES ($ID_MEMBER,'$comment', $commentdate,$id)", __FILE__, __LINE__);
	if (isset($modSettings['shopVersion']))
	db_query("UPDATE {$db_prefix}gallery_pic
	SET commenttotal = commenttotal + 1 WHERE ID_PICTURE = $id LIMIT 1", __FILE__, __LINE__);

	redirectexit($boardurl . '/imagenes/ver/' . $id);

}

function DeleteComment()
{
	global $context, $db_prefix, $txt, $scripturl, $modSettings, $boardurl;
	is_not_guest();
	isAllowedTo('smfgallery_manage');
	if($_POST['campos'] == '')
	fatal_error($txt['gallery_error_no_com_selected']);
    $idimg=$_POST['idimg'];
	if(!empty($_POST['campos'])) {
	$aLista=array_keys($_POST['campos']);
	$total=count($aLista);
	db_query("DELETE FROM {$db_prefix}gallery_comment WHERE ID_COMMENT IN (".implode(',',$aLista).")", __FILE__, __LINE__);


	$dbresult = db_query("UPDATE {$db_prefix}gallery_pic
	SET commenttotal = commenttotal - $total WHERE ID_PICTURE = $idimg LIMIT 1", __FILE__, __LINE__);
		}
	redirectexit($boardurl . '/imagenes/ver/' . $idimg);
}

function AdminSettings()
{
	global $context, $mbname, $txt;
	isAllowedTo('smfgallery_manage');

	adminIndex('gallery_settings');
	$context['page_title'] = $txt[18];


	$context['sub_template']  = 'settings';

}
function AdminSettings2()
{
	global $boardurl;
	isAllowedTo('smfgallery_manage');

	// Get the settings

	$gallery_max_height = (int) $_REQUEST['gallery_max_height'];
	$gallery_max_width =  (int) $_REQUEST['gallery_max_width'];
	$gallery_max_filesize =  (int) $_REQUEST['gallery_max_filesize'];
	$gallery_commentchoice =  isset($_REQUEST['gallery_commentchoice']) ? 1 : 0;

	// Shop settings
	$gallery_shop_picadd = (int) $_REQUEST['gallery_shop_picadd'];
	$gallery_shop_commentadd = (int) $_REQUEST['gallery_shop_commentadd'];

	$gallery_path = $_REQUEST['gallery_path'];
	$gallery_url = $_REQUEST['gallery_url'];
	$gallery_who_viewing = isset($_REQUEST['gallery_who_viewing']) ? 1 : 0;


	// Image Linking codes
	$gallery_set_showcode_bbc_image = isset($_REQUEST['gallery_set_showcode_bbc_image']) ? 1 : 0;
	$gallery_set_showcode_directlink = isset($_REQUEST['gallery_set_showcode_directlink']) ? 1 : 0;
	$gallery_set_showcode_htmllink = isset($_REQUEST['gallery_set_showcode_htmllink']) ? 1 : 0;


    if($gallery_commentchoice)
    	$gallery_commentchoice = 1;
    else
    	$gallery_commentchoice = 0;

     if($gallery_who_viewing)
     	$gallery_who_viewing = 1;
     else
    	$gallery_who_viewing = 0;

	updateSettings(
	array(
	'gallery_max_height' => $gallery_max_height,
	'gallery_max_width' => $gallery_max_width,
	'gallery_max_filesize' => $gallery_max_filesize,
	'gallery_path' => $gallery_path,
	'gallery_url' => $gallery_url,
	'gallery_commentchoice' => $gallery_commentchoice,
	'gallery_who_viewing' => $gallery_who_viewing,
	'gallery_shop_commentadd' => $gallery_shop_commentadd,
	'gallery_shop_picadd' => $gallery_shop_picadd,

	'gallery_set_showcode_bbc_image' => $gallery_set_showcode_bbc_image,
	'gallery_set_showcode_directlink' => $gallery_set_showcode_directlink,
	'gallery_set_showcode_htmllink' => $gallery_set_showcode_htmllink,

	));

	redirectexit('action=gallery;sa=adminset');

}

function ReportList()
{
	global $context, $mbname, $txt, $db_prefix;

	isAllowedTo('smfgallery_manage');

	$context['page_title'] = $txt[18];

	adminIndex('gallery_settings');

	$context['sub_template']  = 'reportlist';
}

function DeleteReport()
{
	global $db_prefix, $txt;

	// Check the permission
	isAllowedTo('smfgallery_manage');

	$id = (int) $_REQUEST['id'];
	if (empty($id))
		fatal_error($txt['gallery_error_no_report_selected']);

	db_query("DELETE FROM {$db_prefix}gallery_report WHERE ID = $id LIMIT 1", __FILE__, __LINE__);

	// Redirect to redirect list
	redirectexit('action=gallery;sa=reportlist');
}
function ApprovePicture(){}
function UnApprovePicture(){}
function ReOrderCats(){}
function Search(){}
function Search2(){}
function AdminCats(){}
function CatUp(){}
function CatDown(){}
function AddCategory(){}
function AddCategory2(){}
function ViewC(){}
function EditCategory(){}
function EditCategory2(){}
function DeleteCategory(){}
function DeleteCategory2(){}
function AddComment(){}
?>