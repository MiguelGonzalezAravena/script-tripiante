<?php
if (!defined('SMF'))
	die('Hacking attempt...');

function ModifyFeatureSettings()
{
	global $context, $txt, $scripturl, $modSettings, $sourcedir;

	// You need to be an admin to edit settings!
	isAllowedTo('admin_forum');

	// All the admin bar, to make it right.
	adminIndex('edit_mods_settings');
	loadLanguage('Help');
	loadLanguage('HidePost');
	loadLanguage('ModSettings');

	// Will need the utility functions from here.
	require_once($sourcedir . '/ManageServer.php');

	$context['page_title'] = $txt['modSettings_title'];
	$context['sub_template'] = 'show_settings';

	$subActions = array(
		'basic' => 'ModifyBasicSettings',
		'layout' => 'ModifyLayoutSettings',
		'karma' => 'ModifyKarmaSettings',
		'others' => 'ModifyOthers',
	);

	$subActions['contact'] = 'ModifyContactSettings';

	// By default do the basic settings.
	$_REQUEST['sa'] = isset($_REQUEST['sa']) && isset($subActions[$_REQUEST['sa']]) ? $_REQUEST['sa'] : 'basic';
	$context['sub_action'] = $_REQUEST['sa'];

	// Load up all the tabs...
	$context['admin_tabs'] = array(
		'title' => &$txt['modSettings_title'],
		'help' => 'modsettings',
		'description' => $txt['smf3'],
		'tabs' => array(
			'basic' => array(
				'title' => $txt['mods_cat_features'],
				'href' => $scripturl . '?action=featuresettings;sa=basic;sesc=' . $context['session_id'],
			),
			'layout' => array(
				'title' => $txt['mods_cat_layout'],
				'href' => $scripturl . '?action=featuresettings;sa=layout;sesc=' . $context['session_id'],
			),
			'karma' => array(
				'title' => $txt['smf293'],
				'href' => $scripturl . '?action=featuresettings;sa=karma;sesc=' . $context['session_id'],
			),
			'others' => array(
				'title' => $txt['other_options'],
				'href' => $scripturl . '?action=featuresettings;sa=others;sesc=' . $context['session_id'],
				'is_last' => true,
			),
		),
	);


	$context['admin_tabs']['tabs']['contact'] = array(
		'title' => $txt['contact_form_mods_cat'],
		'href' => $scripturl . '?action=featuresettings;sa=contact;sesc=' . $context['session_id'],
		);
	// Select the right tab based on the sub action.
	if (isset($context['admin_tabs']['tabs'][$context['sub_action']]))
		$context['admin_tabs']['tabs'][$context['sub_action']]['is_selected'] = true;

	// Call the right function for this sub-acton.
	$subActions[$_REQUEST['sa']]();
}

// This function basically just redirects to the right save function.
function ModifyFeatureSettings2()
{
	global $context, $txt, $scripturl, $modSettings, $sourcedir;

	isAllowedTo('admin_forum');
	loadLanguage('ModSettings');

	// Quick session check...
	checkSession();

	require_once($sourcedir . '/ManageServer.php');

	$subActions = array(
		'basic' => 'ModifyBasicSettings',
		'layout' => 'ModifyLayoutSettings',
		'karma' => 'ModifyKarmaSettings',
		'others' => 'ModifyOthers',
	);


	$subActions['contact'] = 'ModifyContactSettings';
	// Default to core (I assume)
	$_REQUEST['sa'] = isset($_REQUEST['sa']) && isset($subActions[$_REQUEST['sa']]) ? $_REQUEST['sa'] : 'basic';

	// Actually call the saving function.
	$subActions[$_REQUEST['sa']]();
}

function ModifyBasicSettings()
{
	global $txt, $scripturl, $context, $settings, $sc, $modSettings;

	$config_vars = array(
		'',
			// Basic stuff, user languages, titles, flash, permissions...
			array('check', 'allow_guestAccess'),
			array('check', 'userLanguage'),
			array('check', 'allow_editDisplayName'),
			array('check', 'allow_hideOnline'),
			array('check', 'allow_hideEmail'),
			array('check', 'guest_hideContacts'),
			array('check', 'titlesEnable'),
			array('check', 'enable_buddylist'),
			array('text', 'default_personalText'),
			array('int', 'max_signatureLength'),
		'',
			array('text', 'time_format'),
			array('select', 'number_format', array('1234.00' => '1234.00', '1,234.00' => '1,234.00', '1.234,00' => '1.234,00', '1 234,00' => '1 234,00', '1234,00' => '1234,00')),
			array('float', 'time_offset'),
			array('int', 'failed_login_threshold'),
			array('int', 'lastActive'),
			array('check', 'trackStats'),
			array('check', 'hitStats'),
			array('check', 'enableErrorLogging'),
			array('check', 'securityDisable'),
		'',
			// Added for member notepad mod
			array('check', 'notepad_enable_use'),
			array('int', 'notepad_max_notes'),
		'',
			// Reactive on email, and approve on delete
			array('check', 'send_validation_onChange'),
			array('check', 'approveAccountDeletion'),
		'',
			// Option-ish things... miscellaneous sorta.
			array('check', 'allow_disableAnnounce'),
			array('check', 'disallow_sendBody'),
			array('check', 'modlog_enabled'),
			array('check', 'queryless_urls'),
		'',
			// Width/Height image reduction.
			array('int', 'max_image_width'),
			array('int', 'max_image_height'),
		'',
			// Reporting of personal messages?

		'',
			array('check', 'proxyblock_reg'),
			array('check', 'proxyblock_index'),
			array('check', 'enableReportPM'),
	);

	// Saving?
	if (isset($_GET['save']))
	{
		// Fix PM settings.
		$_POST['pm_spam_settings'] = (int) $_POST['max_pm_recipients'] . ',' . (int) $_POST['pm_posts_verification'] . ',' . (int) $_POST['pm_posts_per_hour'];
		$save_vars = $config_vars;
		$save_vars[] = array('text', 'pm_spam_settings');

		saveDBSettings($save_vars);

		writeLog();
		redirectexit('action=featuresettings;sa=basic');
	}

	// Hack for PM spam settings.
	list ($modSettings['max_pm_recipients'], $modSettings['pm_posts_verification'], $modSettings['pm_posts_per_hour']) = explode(',', $modSettings['pm_spam_settings']);
	$config_vars[] = array('int', 'max_pm_recipients');
	$config_vars[] = array('int', 'pm_posts_verification');
	$config_vars[] = array('int', 'pm_posts_per_hour');

	$context['post_url'] = $scripturl . '?action=featuresettings2;save;sa=basic';
	$context['settings_title'] = $txt['mods_cat_features'];

	prepareDBSettingContext($config_vars);
}

function ModifyLayoutSettings()
{
	global $txt, $scripturl, $context, $settings, $sc;

	$config_vars = array(
			// Compact pages?
			array('check', 'compactTopicPagesEnable'),
			array('int', 'compactTopicPagesContiguous', null, $txt['smf235'] . '<div class="smalltext">' . str_replace(' ', '&nbsp;', '"3" ' . $txt['smf236'] . ': <b>1 ... 4 [5] 6 ... 9</b>') . '<br />' . str_replace(' ', '&nbsp;', '"5" ' . $txt['smf236'] . ': <b>1 ... 3 4 [5] 6 7 ... 9</b>') . '</div>'),
		'',
			// Stuff that just is everywhere - today, search, online, etc.
			array('select', 'todayMod', array(&$txt['smf290'], &$txt['smf291'], &$txt['smf292'])),
			array('check', 'topbottomEnable'),
			array('check', 'onlineEnable'),
			array('check', 'enableVBStyleLogin'),
		'',
			// Pagination stuff.
			array('int', 'defaultMaxMembers'),
		'',
			// This is like debugging sorta.
			array('check', 'timeLoadPageEnable'),
			array('check', 'disableHostnameLookup'),
		'',
			// Who's online.
			array('check', 'who_enabled'),
	);

	// Saving?
	if (isset($_GET['save']))
	{
		saveDBSettings($config_vars);
		redirectexit('action=featuresettings;sa=layout');

		loadUserSettings();
		writeLog();
	}

	$context['post_url'] = $scripturl . '?action=featuresettings2;save;sa=layout';
	$context['settings_title'] = $txt['mods_cat_layout'];

	prepareDBSettingContext($config_vars);
}

function ModifyKarmaSettings()
{
	global $txt, $scripturl, $context, $settings, $sc;

	$config_vars = array(
			// Karma - On or off?
			array('select', 'karmaMode', explode('|', $txt['smf64'])),
		'',
			// Who can do it.... and who is restricted by time limits?
			array('int', 'karmaMinPosts'),
			array('float', 'karmaWaitTime'),
			array('check', 'karmaTimeRestrictAdmins'),
		'',
			// What does it look like?  [smite]?
			array('text', 'karmaLabel'),
			array('text', 'karmaApplaudLabel'),
			array('text', 'karmaSmiteLabel'),
	);

	// Saving?
	if (isset($_GET['save']))
	{
		saveDBSettings($config_vars);
		redirectexit('action=featuresettings;sa=karma');
	}

	$context['post_url'] = $scripturl . '?action=featuresettings2;save;sa=karma';
	$context['settings_title'] = $txt['smf293'];

	prepareDBSettingContext($config_vars);
}

function ModifyOthers()
{
	global $txt, $scripturl, $context, $settings, $sc, $modSettings;

	$config_vars = array(
	$txt['index_options'],
			array('int', 'number_posts'),
			array('int', 'number_comments'),
			array('int', 'number_tops'),
			array('int', 'number_images'),
			array('check', 'radio'),
		'',
	$txt['monitor_options'],
			array('int', 'monitor_post_comments'),
			array('int', 'monitor_image_comments'),
			array('int', 'monitor_image_bookmarks'),
			array('int', 'monitor_image_points'),
			array('int', 'monitor_post_points'),
			array('int', 'monitor_post_bookmarks'),
			array('int', 'monitor_friends'),
		'',
	$txt['bookmarks_options'],
			array('int', 'bookmarks_posts'),
			array('int', 'bookmarks_images'),
		'',
	$txt['options_profile_comments'],
			array('int', 'notes'),
			array('int', 'characters_limit_profile_comment'),
			array('int', 'time_profile_comment'),
			array('int', 'characters_limit_quehago'),
			array('int', 'profile_comments_limit'),
			array('int', 'profile_posts_limit'),
			array('int', 'profile_images_limit'),
			array('int', 'user_posts'),
			array('int', 'user_comments_posts'),
			array('int', 'user_comments_images'),
			array('int', 'user_images'),
			array('int', 'user_friends'),
			array('int', 'user_friends2'),
		'',			
			$txt['preview'],
			array('int', 'title_post_preview'),
			array('int', 'body_post_preview'),
		'',
			$txt['post_options'],
			array('int', 'characters_limit_comments'),
		'',	
			$txt['community_options'],
			array('int', 'community_topics_general'),
			array('int', 'community_comments_general'),
			array('int', 'community_tops'),
			array('int', 'community_latest'),
			array('int', 'community_topics'),
			array('int', 'community_comments'),
			array('int', 'community_members'),
		'',
			$txt['general_options'],
			array('int', 'mod_history'),
			array('int', 'denunciations'),
		'',
);

	// Saving?
	if (isset($_GET['save']))
	{
		$save_vars = $config_vars;
		$save_vars[] = array('text', 'pm_spam_settings');

		saveDBSettings($save_vars);

		writeLog();
		redirectexit('action=featuresettings;sa=others');
	}

	$context['post_url'] = $scripturl . '?action=featuresettings2;save;sa=others';
	$context['settings_title'] = $txt['mods_cat_features'];

	prepareDBSettingContext($config_vars);
}


function ModifyContactSettings()
{
	global $txt, $scripturl, $context, $settings, $sc, $webmaster_email;

	$config_vars = array(

		array('check', 'contact_form_enable_guest_access', 'postinput' => '<span style="color: #444444;" class="middletext">' . $txt['contact_form_label_unchecked'] . $txt['contact_form_default_guest_access'] . '</span>'),
		array('check', 'contact_form_enable_member_access', 'postinput' => '<span style="color: #444444;" class="middletext">' . $txt['contact_form_label_unchecked'] . $txt['contact_form_default_member_access'] . '</span>'),
		array('text', 'contact_form_dest_email_addr', '24', 'postinput' => '<span style="color: #444444;" class="middletext"><br /><br />' . $txt['contact_form_label_default'] . $webmaster_email . ' <a href="' . $scripturl . '?action=admin;area=serversettings;sa=core;sesc=' . $context['session_id'] . '">' . $txt['contact_form_label_edit'] . '</a></span>'),
		'',
		array('check', 'contact_form_require_name', 'postinput' => '<span style="color: #444444;" class="middletext">' . $txt['contact_form_label_unchecked'] . $txt['contact_form_default_require_name'] . '</span>'),
		array('text', 'contact_form_default_name', '24', 'postinput' => '<span style="color: #444444;" class="middletext">' . $txt['contact_form_label_default'] . $txt['contact_form_default_name_default'] . '</span>'),
		array('check', 'contact_form_sender_name_dont_use_form', 'postinput' => '<span style="color: #444444;" class="middletext">' . $txt['contact_form_label_unchecked'] . $txt['contact_form_sender_name_dont_use_form_default'] . '</span>'),
		'',
		array('check', 'contact_form_require_email', 'postinput' => '<span style="color: #444444;" class="middletext">' . $txt['contact_form_label_unchecked'] . $txt['contact_form_default_require_email'] . '</span>'),
		array('text', 'contact_form_default_email_sender', '24', 'postinput' => '<span style="color: #444444;" class="middletext"><br /><br />' . $txt['contact_form_label_default'] . $webmaster_email . ' <a href="' . $scripturl . '?action=admin;area=serversettings;sa=core;sesc=' . $context['session_id'] . '">' . $txt['contact_form_label_edit'] . '</a></span>'),
		array('check', 'contact_form_sender_email_dont_use_form', 'postinput' => '<span style="color: #444444;" class="middletext">' . $txt['contact_form_label_unchecked'] . $txt['contact_form_sender_email_dont_use_form_default'] . '</span>'),
		array('check', 'contact_form_skip_dns_check', 'postinput' => '<span style="color: #444444;" class="middletext">' . $txt['contact_form_label_unchecked'] . $txt['contact_form_default_dns_check'] . '</span>'),
		'',
		array('check', 'contact_form_require_subject', 'postinput' => '<span style="color: #444444;" class="middletext">' . $txt['contact_form_label_unchecked'] . $txt['contact_form_default_require_subject'] . '</span>'),
		array('text', 'contact_form_default_subject', '24', 'postinput' => '<span style="color: #444444;" class="middletext">' . $txt['contact_form_label_default'] . $txt['contact_form_default_subject_default'] . '</span>'),
		array('check', 'contact_form_sender_subject_dont_use_form', 'postinput' => '<span style="color: #444444;" class="middletext">' . $txt['contact_form_label_unchecked'] . $txt['contact_form_sender_subject_dont_use_form_default'] . '</span>'),
		array('text', 'contact_form_subject_prefix', '24', 'postinput' => '<span style="color: #444444;" class="middletext">' . $txt['contact_form_label_default'] . $txt['contact_form_subject_prefix_default'] . '</span>'),
		'',
		array('text', 'contact_form_display_title', '24', 'postinput' => '<span style="color: #444444;" class="middletext">' . $txt['contact_form_label_default'] . $txt['contact_form_title'] . '</span>'),
		array('text', 'contact_form_tab_label',     '24', 'postinput' => '<span style="color: #444444;" class="middletext">' . $txt['contact_form_label_default'] . $txt['contact_form_default_tab_label'] . '</span>'),
	);

	if (isset($_GET['save']))
	{
		checkSession();

		saveDBSettings($config_vars);
		writeLog();

		redirectexit('action=featuresettings;sa=contact');
	}

	$context['post_url'] = $scripturl . '?action=featuresettings2;save;sa=contact';
	$context['settings_title'] = $txt['contact_form_title'];

	prepareDBSettingContext($config_vars);
}

?>