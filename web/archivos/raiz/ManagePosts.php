<?php
if (!defined('SMF'))
  die('Hacking attempt...');

function ManagePostSettings() {
  global $context, $txt, $scripturl;

  // Boldify "Posts and Topics" on the admin bar.
  adminIndex('posts_and_topics');

  $subActions = array(
    'posts' => array('ModifyPostSettings', 'admin_forum'),
    'hidetagspecial' => array('ModifyHideTagSpecialSettings', 'admin_forum'),
    'bbc' => array('ModifyBBCSettings', 'admin_forum'),
    'censor' => array('SetCensor', 'moderate_forum'),
    'topics' => array('ModifyTopicSettings', 'admin_forum'),
  );

  // Default the sub-action to 'view ban list'.
  $_REQUEST['sa'] = isset($_REQUEST['sa']) && isset($subActions[$_REQUEST['sa']]) ? $_REQUEST['sa'] : (allowedTo('admin_forum') ? 'posts' : 'censor');

  // Make sure you can do this.
  isAllowedTo($subActions[$_REQUEST['sa']][1]);

  $context['page_title'] = $txt['manageposts_title'];

  // Tabs for browsing the different ban functions.
  $context['admin_tabs'] = array(
    'title' => $txt['manageposts_title'],
    'help' => 'posts_and_topics',
    'description' => $txt['manageposts_description'],
    'tabs' => array()
  );
  if (allowedTo('admin_forum'))
  {
    $context['admin_tabs']['tabs'][] = array(
      'title' => $txt['manageposts_settings'],
      'description' => $txt['manageposts_settings_description'],
      'href' => $scripturl . '?action=postsettings;sa=posts',
      'is_selected' => $_REQUEST['sa'] == 'posts',
    );
    $context['admin_tabs']['tabs'][] = array(
      'title' => $txt['hidetagspecial_titel'],
      'description' => $txt['hidetagspecial_description'],
      'href' => $scripturl . '?action=postsettings;sa=hidetagspecial',
      'is_selected' => $_REQUEST['sa'] == 'hidetagspecial',
    );
    $context['admin_tabs']['tabs'][] = array(
      'title' => $txt['manageposts_bbc_settings'],
      'description' => $txt['manageposts_bbc_settings_description'],
      'href' => $scripturl . '?action=postsettings;sa=bbc',
      'is_selected' => $_REQUEST['sa'] == 'bbc',
    );
  }
  if (allowedTo('moderate_forum'))
    $context['admin_tabs']['tabs'][] = array(
      'title' => $txt[135],
      'description' => $txt[141],
      'href' => $scripturl . '?action=postsettings;sa=censor',
      'is_selected' => $_REQUEST['sa'] == 'censor',
      'is_last' => !allowedTo('admin_forum'),
    );
  if (allowedTo('admin_forum'))
    $context['admin_tabs']['tabs'][] = array(
      'title' => $txt['manageposts_topic_settings'],
      'description' => $txt['manageposts_topic_settings_description'],
      'href' => $scripturl . '?action=postsettings;sa=topics',
      'is_selected' => $_REQUEST['sa'] == 'topics',
      'is_last' => true,
    );

  // Call the right function for this sub-acton.
  $subActions[$_REQUEST['sa']][0]();
}

// Set the censored words.
function SetCensor()
{
  global $txt, $modSettings, $context;

  if (!empty($_POST['save_censor']))
  {
    // Make sure censoring is something they can do.
    checkSession();

    $censored_vulgar = array();
    $censored_proper = array();

    // Rip it apart, then split it into two arrays.
    if (isset($_POST['censortext']))
    {
      $_POST['censortext'] = explode("\n", strtr($_POST['censortext'], array("\r" => '')));

      foreach ($_POST['censortext'] as $c)
        list ($censored_vulgar[], $censored_proper[]) = array_pad(explode('=', trim($c)), 2, '');
    }
    else if (isset($_POST['censor_vulgar'], $_POST['censor_proper']))
    {
      if (is_array($_POST['censor_vulgar']))
      {
        foreach ($_POST['censor_vulgar'] as $i => $value)
          if ($value == '')
          {
            unset($_POST['censor_vulgar'][$i]);
            unset($_POST['censor_proper'][$i]);
          }

        $censored_vulgar = $_POST['censor_vulgar'];
        $censored_proper = $_POST['censor_proper'];
      }
      else
      {
        $censored_vulgar = explode("\n", strtr($_POST['censor_vulgar'], array("\r" => '')));
        $censored_proper = explode("\n", strtr($_POST['censor_proper'], array("\r" => '')));
      }
    }

    // Set the new arrays and settings in the database.
    $updates = array(
      'censor_vulgar' => implode("\n", $censored_vulgar),
      'censor_proper' => implode("\n", $censored_proper),
      'censorWholeWord' => empty($_POST['censorWholeWord']) ? '0' : '1',
      'censorIgnoreCase' => empty($_POST['censorIgnoreCase']) ? '0' : '1',
    );

    updateSettings($updates);
  }

  if (isset($_POST['censortest']))
  {
    $censorText = htmlspecialchars(stripslashes($_POST['censortest']), ENT_QUOTES);
    $context['censor_test'] = strtr(censorText($censorText), array('"' => '&quot;'));
  }

  // Set everything up for the template to do its thang.
  $censor_vulgar = explode("\n", $modSettings['censor_vulgar']);
  $censor_proper = explode("\n", $modSettings['censor_proper']);

  $context['censored_words'] = array();
  for ($i = 0, $n = count($censor_vulgar); $i < $n; $i++)
  {
    if (empty($censor_vulgar[$i]))
      continue;

    // Skip it, it's either spaces or stars only.
    if (trim(strtr($censor_vulgar[$i], '*', ' ')) == '')
      continue;

    $context['censored_words'][htmlspecialchars(trim($censor_vulgar[$i]))] = isset($censor_proper[$i]) ? htmlspecialchars($censor_proper[$i]) : '';
  }

  $context['sub_template'] = 'edit_censored';
  $context['page_title'] = $txt[135];
}

// Modify all settings related to posts and posting.
function ModifyPostSettings()
{
  global $context, $txt, $db_prefix, $modSettings;

  // Setup the template.
  $context['sub_template'] = 'edit_post_settings';
  $context['page_title'] = $txt['manageposts_settings'];

  // Saving?
  if (isset($_POST['save_settings']))
  {
    checkSession();

    // Let's find out if they want things way too long...
    if (!empty($_POST['max_messageLength']) && $_POST['max_messageLength'] != $modSettings['max_messageLength'])
    {
      $request = db_query("
        SHOW COLUMNS
        FROM {$db_prefix}messages", false, false);
      if ($request !== false)
      {
        while ($row = mysqli_fetch_assoc($request))
          if ($row['Field'] == 'body')
            $body_type = $row['Type'];
        mysqli_free_result($request);
      }

      $request = db_query("
        SHOW INDEX
        FROM {$db_prefix}messages", false, false);
      if ($request !== false)
      {
        while ($row = mysqli_fetch_assoc($request))
          if ($row['Column_name'] == 'body' && (isset($row['Index_type']) && $row['Index_type'] == 'FULLTEXT' || isset($row['Comment']) && $row['Comment'] == 'FULLTEXT'))
            $fulltext = true;
        mysqli_free_result($request);
      }

      if (isset($body_type) && $_POST['max_messageLength'] > 65535 && $body_type == 'text')
      {
        // !!! Show an error message?!
        // MySQL only likes fulltext indexes on text columns... for now?
        if (!empty($fulltext))
          $_POST['max_messageLength'] = 65535;
        else
        {
          // Make it longer so we can do their limit.
          db_query("
            ALTER TABLE {$db_prefix}messages
            CHANGE COLUMN body body mediumtext", __FILE__, __LINE__);
        }
      }
      else if (isset($body_type) && $_POST['max_messageLength'] <= 65535 && $body_type != 'text')
      {
        // Shorten the column so we can have the benefit of fulltext searching again!
        db_query("
          ALTER TABLE {$db_prefix}messages
          CHANGE COLUMN body body text", __FILE__, __LINE__);
      }
    }

    // Update the actual settings.
    updateSettings(array(
      'removeNestedQuotes' => empty($_POST['removeNestedQuotes']) ? '0' : '1',
      'enableEmbeddedFlash' => empty($_POST['enableEmbeddedFlash']) ? '0' : '1',
      'enableSpellChecking' => empty($_POST['enableSpellChecking']) ? '0' : '1',
      'max_messageLength' => empty($_POST['max_messageLength']) ? '0' : (int) $_POST['max_messageLength'],
      'fixLongWords' => empty($_POST['fixLongWords']) ? '0' : (int) $_POST['fixLongWords'],
      'topicSummaryPosts' => empty($_POST['topicSummaryPosts']) ? '0' : (int) $_POST['topicSummaryPosts'],
      'spamWaitTime' => empty($_POST['spamWaitTime']) ? '0' : (int) $_POST['spamWaitTime'],
      'edit_wait_time' => empty($_POST['edit_wait_time']) ? '0' : (int) $_POST['edit_wait_time'],
      'edit_disable_time' => empty($_POST['edit_disable_time']) ? '0' : (int) $_POST['edit_disable_time'],
    ));
  }

  // Check if your PHP is able to use spell checking.
  $context['spellcheck_installed'] = function_exists('pspell_new');
}

//This is everything you need for the hide tag special in posts :)
function ModifyHideTagSpecialSettings() {
  global $txt, $context, $db_prefix, $modSettings;

  // Setup the template.
  $context['sub_template'] = 'edit_hidetagspecial_settings';
  $context['page_title'] = $txt['hidetagspecial_titel'];

  // Wanna save this page?
  if (isset($_POST['save_settings'])) {
    checkSession();
    
    //Prepare Textareas :)
    
    $_POST['hide_hiddentext'] = htmlspecialchars(stripslashes($_POST['hide_hiddentext']), ENT_QUOTES);
    $_POST['hide_unhiddentext'] = htmlspecialchars(stripslashes($_POST['hide_unhiddentext']), ENT_QUOTES);
  
    //Allowed Groups ;)
    if (!empty($_POST['hide_autounhidegroups'])) {
      $new_array = array();
      foreach($_POST['hide_autounhidegroups'] as $i) {
        $i = (int) $i;
        if (!empty($i))
          $new_array[$i] = $i;
      }
      $_POST['hide_autounhidegroups'] = implode(',', $new_array);
    }
    
    $_POST['hide_posUnhiddenText'] = (int) $_POST['hide_posUnhiddenText'];
    
    // Update the actual settings.
    updateSettings(array(
      'minWordLen' => empty($_POST['minWordLen']) ? '0' : (int) $_POST['minWordLen'],
      'minChar' => empty($_POST['minChar']) ? '0' : (int) $_POST['minChar'],
      'hide_MUIswitch' => empty($_POST['hide_MUIswitch']) ? '0' : '1',
      'hide_enableHTML' => empty($_POST['hide_enableHTML']) ? '0' : '1',
      'hide_useSpanTag' => empty($_POST['hide_useSpanTag']) ? '0' : '1',
      'hide_enableUnhiddenText' => empty($_POST['hide_enableUnhiddenText']) ? '0' : '1',
      'hide_hiddentext' => empty($_POST['hide_hiddentext']) ? '' : $_POST['hide_hiddentext'],
      'hide_unhiddentext' => empty($_POST['hide_unhiddentext']) ? '' : $_POST['hide_unhiddentext'],
      'hide_posUnhiddenText' => empty($_POST['hide_posUnhiddenText']) || $_POST['hide_posUnhiddenText'] > 4 ? 4 : $_POST['hide_posUnhiddenText'],
      'hide_onlyonetimeinfo' => empty($_POST['hide_onlyonetimeinfo']) ? '0' : '1',
      'hide_noinfoforguests' => empty($_POST['hide_noinfoforguests']) ? '0' : '1',
      'hide_autounhidegroups' => empty($_POST['hide_autounhidegroups']) ? '' : $_POST['hide_autounhidegroups'],
      'hide_minpostunhide' => empty($_POST['hide_minpostunhide']) ? '0' : (int) $_POST['hide_minpostunhide'],
      'hide_minpostautounhide' => empty($_POST['hide_minpostautounhide']) ? '0' : (int) $_POST['hide_minpostautounhide'],
      'hide_karmaenable' => !empty($_POST['hide_karmaenable']) && !empty($modSettings['karmaMode']) ? '1' : '0',
      'hide_minkarmaunhide' => empty($_POST['hide_minkarmaunhide']) ? '0' : (int) $_POST['hide_minkarmaunhide'],
      'hide_minkarmaautounhide' => empty($_POST['hide_minkarmaautounhide']) ? '0' : (int) $_POST['hide_minkarmaautounhide'],
      'hide_minimumkarmaandpost' => empty($_POST['hide_minimumkarmaandpost']) ? '0' : '1',
      'hide_onlykarmagood' => empty($_POST['hide_onlykarmagood']) ? '0' : '1',
    ));

    redirectexit('action=postsettings;sa=hidetagspecial');
  }

  //Load membergroups.
  $modSettings['hide_autounhidegroups'] = !empty($modSettings['hide_autounhidegroups']) ? explode(',', $modSettings['hide_autounhidegroups']) : array();
  $request = db_query("
    SELECT groupName, ID_GROUP, minPosts
    FROM {$db_prefix}membergroups
    ORDER BY minPosts, ID_GROUP != 1, ID_GROUP != 2, ID_GROUP != 3, groupName", __FILE__, __LINE__);
  while ($row = mysqli_fetch_assoc($request))
  {
    $context['groups'][(int) $row['ID_GROUP']] = array(
      'id' => $row['ID_GROUP'],
      'name' => trim($row['groupName']),
      'checked' => in_array($row['ID_GROUP'], $modSettings['hide_autounhidegroups']),
      'is_post_group' => $row['minPosts'] != -1,
    );
  }
  mysqli_free_result($request);
  
  //Sorry for the lazyness... but it's easier...
  loadLanguage('ManageBoards');

  //Fix something the first time :D
  if (empty($modSettings['hide_posUnhiddenText'])) 
    updateSettings(array('hide_posUnhiddenText' => 4));
}

// Bulletin Board Code...a lot of Bulletin Board Code.
function ModifyBBCSettings()
{
  global $context, $txt, $modSettings, $helptxt;

  // Setup the template.
  $context['sub_template'] = 'edit_bbc_settings';
  $context['page_title'] = $txt['manageposts_bbc_settings_title'];

  // Ask parse_bbc() for its bbc code list.
  $temp = parse_bbc(false);
  $bbcTags = array();
  foreach ($temp as $tag)
    $bbcTags[] = $tag['tag'];

  $bbcTags = array_unique($bbcTags);
  $totalTags = count($bbcTags);

  // The number of columns we want to show the BBC tags in.
  $numColumns = 3;

  // In case we're saving.
  if (isset($_POST['save_settings']))
  {
    checkSession();

    if (!isset($_POST['enabledTags']))
      $_POST['enabledTags'] = array();
    else if (!is_array($_POST['enabledTags']))
      $_POST['enabledTags'] = array($_POST['enabledTags']);

    // Update the actual settings.
    updateSettings(array(
      'enableBBC' => empty($_POST['enableBBC']) ? '0' : '1',
      'enablePostHTML' => empty($_POST['enablePostHTML']) ? '0' : '1',
      'autoLinkUrls'  => empty($_POST['autoLinkUrls']) ? '0' : '1',
      'disabledBBC' => implode(',', array_diff($bbcTags, $_POST['enabledTags'])),
    ));
  }

  $context['bbc_columns'] = array();
  $tagsPerColumn = ceil($totalTags / $numColumns);
  $disabledTags = empty($modSettings['disabledBBC']) ? array() : explode(',', $modSettings['disabledBBC']);

  $col = 0;
  $i = 0;
  foreach ($bbcTags as $tag)
  {
    if ($i % $tagsPerColumn == 0 && $i != 0)
      $col++;

    $context['bbc_columns'][$col][] = array(
      'tag' => $tag,
      'is_enabled' => !in_array($tag, $disabledTags),
      // !!! 'tag_' . ?
      'show_help' => isset($helptxt[$tag]),
    );

    $i++;
  }

  $context['bbc_all_selected'] = empty($disabledTags);
}

// Function for modifying topic settings. Not very exciting.
function ModifyTopicSettings()
{
  global $context, $txt, $modSettings;

  // Setup the template.
  $context['sub_template'] = 'edit_topic_settings';
  $context['page_title'] = $txt['manageposts_topic_settings'];

  // Wanna save this page?
  if (isset($_POST['save_settings']))
  {
    checkSession();

    // Update the actual settings.
    updateSettings(array(
      'enableStickyTopics' => empty($_POST['enableStickyTopics']) ? '0' : '1',
      'enableParticipation' => empty($_POST['enableParticipation']) ? '0' : '1',
      'oldTopicDays' => empty($_POST['oldTopicDays']) ? '0' : (int) $_POST['oldTopicDays'],
      'defaultMaxTopics' => empty($_POST['defaultMaxTopics']) ? '0' : (int) $_POST['defaultMaxTopics'],
      'defaultMaxMessages' => empty($_POST['defaultMaxMessages']) ? '0' : (int) $_POST['defaultMaxMessages'],
      'hotTopicPosts' => empty($_POST['hotTopicPosts']) ? '0' : (int) $_POST['hotTopicPosts'],
      'hotTopicVeryPosts' => empty($_POST['hotTopicVeryPosts']) ? '0' : (int) $_POST['hotTopicVeryPosts'],
      'enableAllMessages' => empty($_POST['enableAllMessages']) ? '0' : (int) $_POST['enableAllMessages'],
      'enablePreviousNext' => empty($_POST['enablePreviousNext']) ? '0' : '1',
    ));
  }
}

?>