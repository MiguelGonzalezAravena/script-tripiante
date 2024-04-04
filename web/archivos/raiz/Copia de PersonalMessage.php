<?php
if (!defined('SMF'))
  die('Hacking attempt...');

function MessageMain() {
  global $txt, $sourcedir, $context, $user_info, $user_settings, $db_prefix, $ID_MEMBER;

  // No guests!
  is_not_guest();

  // You're not supposed to be here at all, if you can't even read PMs.
  isAllowedTo('pm_read');

  // This file contains the basic functions for sending a PM.
  require_once($sourcedir . '/Subs-Post.php');

  if (loadLanguage('PersonalMessage', '', false) === false)
    loadLanguage('InstantMessage');
  if (WIRELESS)
    $context['sub_template'] = WIRELESS_PROTOCOL . '_pm';
  else
  {
    if (loadTemplate('PersonalMessage', false) === false)
      loadTemplate('InstantMessage');
  }

  // Load up the members maximum message capacity.
  if (!$user_info['is_admin'])
  {
    // !!! Why do we do this?  It seems like if they have any limit we should use it.
    $request = db_query("
      SELECT MAX(maxMessages) AS topLimit, MIN(maxMessages) AS bottomLimit
      FROM {$db_prefix}membergroups
      WHERE ID_GROUP IN (" . implode(', ', $user_info['groups']) . ')', __FILE__, __LINE__);
    list ($maxMessage, $minMessage) = mysqli_fetch_row($request);
    mysqli_free_result($request);

    $context['message_limit'] = $minMessage == 0 ? 0 : $maxMessage;
  }
  else
    $context['message_limit'] = 0;

  // Prepare the context for the capacity bar.
  if (!empty($context['message_limit']))
  {
    $bar = ($user_info['messages'] * 100) / $context['message_limit'];

    $context['limit_bar'] = array(
      'messages' => $user_info['messages'],
      'allowed' => $context['message_limit'],
      'percent' => $bar,
      'bar' => min(100, (int) $bar),
      'text' => sprintf($txt['pm_currently_using'], $user_info['messages'], round($bar, 1)),
    );
  }

  // We should probably cache this information for speed.
  $context['labels'] = $user_settings['messageLabels'] == '' ? array() : explode(',', $user_settings['messageLabels']);
  foreach ($context['labels'] as $k => $v)
    $context['labels'][(int) $k] = array('id' => $k, 'name' => trim($v), 'messages' => 0, 'unread_messages' => 0);
  $context['labels'][-1] = array('id' => -1, 'name' => $txt['pm_msg_label_inbox'], 'messages' => 0, 'unread_messages' => 0);

  // !!! The idea would be to cache this information in the members table, and invlidate it when they are sent messages.
  $result = db_query("
    SELECT labels, is_read, COUNT(*) AS num
    FROM {$db_prefix}pm_recipients
    WHERE ID_MEMBER = $ID_MEMBER
    GROUP BY labels, is_read", __FILE__, __LINE__);
  while ($row = mysqli_fetch_assoc($result))
  {
    $this_labels = explode(',', $row['labels']);
    foreach ($this_labels as $this_label)
    {
      $context['labels'][(int) $this_label]['messages'] += $row['num'];
      if (!($row['is_read'] & 1))
        $context['labels'][(int) $this_label]['unread_messages'] += $row['num'];
    }
  }
  mysqli_free_result($result);

  // This determines if we have more labels than just the standard inbox.
  $context['currently_using_labels'] = count($context['labels']) > 1 ? 1 : 0;

  // Some stuff for the labels...
  $context['current_label_id'] = isset($_REQUEST['l']) && isset($context['labels'][(int) $_REQUEST['l']]) ? (int) $_REQUEST['l'] : -1;
  $context['current_label'] = &$context['labels'][(int) $context['current_label_id']]['name'];
  $context['folder'] = !isset($_REQUEST['f']) || $_REQUEST['f'] != 'outbox' ? 'inbox' : 'outbox';

  // This is convenient.  Do you know how annoying it is to do this every time?!
  $context['current_label_redirect'] = 'action=pm;f=' . $context['folder'] . (isset($_GET['start']) ? ';start=' . $_GET['start'] : '') . (isset($_REQUEST['l']) ? ';l=' . $_REQUEST['l'] : '');
  $subActions = array(
    'addbuddy' => 'WirelessAddBuddy',
    'manlabels' => 'ManageLabels',
    'outbox' => 'MessageFolder',
    'pmactions' => 'MessageActionsApply',
    'prune' => 'MessagePrune',
    'removeall' => 'MessageKillAllQuery',
    'removeall2' => 'MessageKillAll',
    'report' => 'ReportMessage',
    'search' => 'MessageSearch',
    'search2' => 'MessageSearch2',
    'send' => 'MessagePost',
    'send2' => 'MessagePost2',
  );

  if (!isset($_REQUEST['sa']) || !isset($subActions[$_REQUEST['sa']]))
    MessageFolder();
  else
  {
    messageIndexBar($_REQUEST['sa']);
    $subActions[$_REQUEST['sa']]();
  }
}

// A sidebar to easily access different areas of the section
function messageIndexBar($area)
{
  global $txt, $context, $scripturl, $sc, $modSettings, $settings, $user_info;

  $context['pm_areas'] = array(
    'folders' => array(
      'title' => $txt['pm_messages'],
      'areas' => array(
        'send' => array('link' => '<img src="' . $settings['images_url'] . '/icons/mensaje_enviar.gif" alt=""> <a href="/mensajes/redactar/" title="' . $txt[321] . '"> ' . $txt[321] . '</a>', 'href' => '/mensajes/redactar/'),
        'inbox' => array('link' => '<br><img src="' . $settings['images_url'] . '/icons/mensaje.gif" alt=""> <a href="/mensajes/recibidos/" title="' . $txt[316] . '"> ' . $txt[316] . ' </a>', 'href' => '/mensajes/recibidos/'),
        'outbox' => array('link' => '<br><img src="' . $settings['images_url'] . '/icons/mensaje_para.gif" alt=""> <a href="/mensajes/enviados/" title="' . $txt[320] . '"> ' . $txt[320] . '</a>', 'href' => '/mensajes/enviados/'),
      ),
    ),
    'labels' => array(
      'title' => $txt['pm_labels'],
      'areas' => array(),
    ),
  );

  // Handle labels.
  if (empty($context['currently_using_labels']))
    unset($context['pm_areas']['labels']);
  else
  {
    // Note we send labels by id as it will have less problems in the querystring.
    foreach ($context['labels'] as $label)
    {
      if ($label['id'] == -1)
        continue;
      $context['pm_areas']['labels']['areas']['label' . $label['id']] = array(
        'link' => '<a href="' . $scripturl . '?action=pm;l=' . $label['id'] . '">' . $label['name'] . '</a>',
        'href' => $scripturl . '?action=pm;l=' . $label['id'],
        'unread_messages' => &$context['labels'][(int) $label['id']]['unread_messages'],
        'messages' => &$context['labels'][(int) $label['id']]['messages'],
      );
    }
  }

  $context['pm_areas']['folders']['areas']['inbox']['unread_messages'] = &$context['labels'][-1]['unread_messages'];
  $context['pm_areas']['folders']['areas']['inbox']['messages'] = &$context['labels'][-1]['messages'];

  // Do we have a limit on the amount of messages we can keep?
  if (!empty($context['message_limit']))
  {
    $bar = round(($user_info['messages'] * 100) / $context['message_limit'], 1);

    $context['limit_bar'] = array(
      'messages' => $user_info['messages'],
      'allowed' => $context['message_limit'],
      'percent' => $bar,
      'bar' => $bar > 100 ? 100 : (int) $bar,
      'text' => sprintf($txt['pm_currently_using'], $user_info['messages'], $bar)
    );

    // Force it in to somewhere.
    $context['pm_areas']['pref']['areas']['limit_bar'] = array('limit_bar' => true);
  }

  // Where we are now.
  $context['pm_area'] = $area;

  // obExit will know what to do!
  if (!WIRELESS)
    $context['template_layers'][] = 'pm';
}

// A folder, ie. outbox/inbox.
function MessageFolder()
{
  global $txt, $scripturl, $db_prefix, $ID_MEMBER, $modSettings, $context;
  global $messages_request, $user_info, $recipients, $options;

  if(isset($_GET['p']))
    $context['sl-singlepm'] = (int) $_GET['p'];

  if(isset($context['sl-singlepm']) && !isset($modSettings['enableSinglePM']))
    $modSettings['enableSinglePM'] = 1;
    
  if (isset($_GET['start']) && $_GET['start'] != 'new')
    $_GET['start'] = (int) $_GET['start'];
  elseif (!isset($_GET['start']) && !empty($options['view_newest_pm_first']))
    $_GET['start'] = 0;
  else
    $_GET['start'] = 'new';

  // Set up some basic theme stuff.
  $context['allow_hide_email'] = !empty($modSettings['allow_hideEmail']);
  $context['from_or_to'] = $context['folder'] != 'outbox' ? 'from' : 'to';
  $context['get_pmessage'] = 'prepareMessageContext';

  $labelQuery = $context['folder'] != 'outbox' ? "
      AND FIND_IN_SET('$context[current_label_id]', pmr.labels)" : '';

  // Set the index bar correct!
  messageIndexBar($context['current_label_id'] == -1 ? $context['folder'] : 'label' . $context['current_label_id']);

  // Sorting the folder.
  $sort_methods = array(
    'date' => 'pm.ID_PM',
    'name' => "IFNULL(mem.realName, '')",
    'subject' => 'pm.subject',
  );

  // They didn't pick one, use the forum default.
  if (!isset($_GET['sort']) || !isset($sort_methods[$_GET['sort']]))
  {
    $context['sort_by'] = 'date';
    $_GET['sort'] = 'pm.ID_PM';
    $descending = false;
  }
  // Otherwise use the defaults: ascending, by date.
  else
  {
    $context['sort_by'] = $_GET['sort'];
    $_GET['sort'] = $sort_methods[$_GET['sort']];
    $descending = isset($_GET['desc']);
  }

  if (!empty($options['view_newest_pm_first']))
    $descending = !$descending;

  $context['sort_direction'] = $descending ? 'down' : 'up';

  // Why would you want access to your outbox if you're not allowed to send anything?
  if ($context['folder'] == 'outbox')
    isAllowedTo('pm_send');

  // Set the text to resemble the current folder.
  $pmbox = $context['folder'] != 'outbox' ? $txt[316] : $txt[320];
  $txt[412] = str_replace('PMBOX', $pmbox, $txt[412]);


  if(isset($context['sl-singlepm']))
    $context['linktree'][] = array(
    'url' => $scripturl . '?action=pm;f=' . $context['folder'] . ';p=' . $context['sl-singlepm'],
    'name' => $txt['singlepm_vmsg'] . $context['sl-singlepm']
    );
      if(isset($context['sl-singlepm']))
    --$context['sl-singlepm'];

  // Mark all messages as read if in the inbox.
  if ($context['folder'] != 'outbox' && !empty($context['labels'][(int) $context['current_label_id']]['unread_messages']))
    markMessages(null, $context['current_label_id']);

  // Figure out how many messages there are.
  if ($context['folder'] == 'outbox')
    $request = db_query("
      SELECT COUNT(*)
      FROM {$db_prefix}personal_messages
      WHERE ID_MEMBER_FROM = $ID_MEMBER
        AND deletedBySender = 0", __FILE__, __LINE__);
  else
    $request = db_query("
      SELECT COUNT(*)
      FROM {$db_prefix}pm_recipients AS pmr
      WHERE pmr.ID_MEMBER = $ID_MEMBER
        AND pmr.deleted = 0$labelQuery", __FILE__, __LINE__);
  list ($max_messages) = mysqli_fetch_row($request);
  mysqli_free_result($request);

  // Only show the button if there are messages to delete.
  $context['show_delete'] = $max_messages > 0;

  // Start on the last page.
  if (!is_numeric($_GET['start']) || $_GET['start'] >= $max_messages)
    $_GET['start'] = ($max_messages - 1) - (($max_messages - 1) % $modSettings['defaultMaxMessages']);
  elseif ($_GET['start'] < 0)
    $_GET['start'] = 0;

  // ... but wait - what if we want to start from a specific message?
  if (isset($_GET['pmid']))
  {
    $_GET['pmid'] = (int) $_GET['pmid'];

    // With only one page of PM's we're gonna want page 1.
    if ($max_messages <= $modSettings['defaultMaxMessages'])
      $_GET['start'] = 0;
    else
    {
      if ($context['folder'] == 'outbox')
        $request = db_query("
          SELECT COUNT(*)
          FROM {$db_prefix}personal_messages
          WHERE ID_MEMBER_FROM = $ID_MEMBER
            AND deletedBySender = 0
            AND ID_PM " . ($descending ? '>' : '<') . " $_GET[pmid]", __FILE__, __LINE__);
      else
        $request = db_query("
          SELECT COUNT(*)
          FROM {$db_prefix}pm_recipients AS pmr
          WHERE pmr.ID_MEMBER = $ID_MEMBER
            AND pmr.deleted = 0$labelQuery
            AND ID_PM " . ($descending ? '>' : '<') . " $_GET[pmid]", __FILE__, __LINE__);

      list ($_GET['start']) = mysqli_fetch_row($request);
      mysqli_free_result($request);

      // To stop the page index's being abnormal, start the page on the page the message would normally be located on...
      $_GET['start'] = $modSettings['defaultMaxMessages'] * (int) ($_GET['start'] / $modSettings['defaultMaxMessages']);
    }
  }

  // Set up the page index.
  $context['page_index'] = constructPageIndex($scripturl . '?action=pm;f=' . $context['folder'] . (isset($_REQUEST['l']) ? ';l=' . (int) $_REQUEST['l'] : '') . ';sort=' . $context['sort_by'] . (isset($_GET['desc']) ? ';desc' : ''), $_GET['start'], $max_messages, $modSettings['defaultMaxMessages']);
  $context['start'] = $_GET['start'];

  // Determine the navigation context (especially useful for the wireless template).
  $context['links'] = array(
    'first' => $_GET['start'] >= $modSettings['defaultMaxMessages'] ? $scripturl . '?action=pm;start=0' : '',
    'prev' => $_GET['start'] >= $modSettings['defaultMaxMessages'] ? $scripturl . '?action=pm;start=' . ($_GET['start'] - $modSettings['defaultMaxMessages']) : '',
    'next' => $_GET['start'] + $modSettings['defaultMaxMessages'] < $max_messages ? $scripturl . '?action=pm;start=' . ($_GET['start'] + $modSettings['defaultMaxMessages']) : '',
    'last' => $_GET['start'] + $modSettings['defaultMaxMessages'] < $max_messages ? $scripturl . '?action=pm;start=' . (floor(($max_messages - 1) / $modSettings['defaultMaxMessages']) * $modSettings['defaultMaxMessages']) : '',
    'up' => $scripturl,
  );
  $context['page_info'] = array(
    'current_page' => $_GET['start'] / $modSettings['defaultMaxMessages'] + 1,
    'num_pages' => floor(($max_messages - 1) / $modSettings['defaultMaxMessages']) + 1
  );

  // Load the messages up...
  // !!!SLOW This query uses a filesort. (inbox only.)
  $request = db_query("
    SELECT pm.ID_PM, pm.ID_MEMBER_FROM
    FROM ({$db_prefix}personal_messages AS pm" . ($context['folder'] == 'outbox' ? ')' . ($context['sort_by'] == 'name' ? "
      LEFT JOIN {$db_prefix}pm_recipients AS pmr ON (pmr.ID_PM = pm.ID_PM)" : '') : ", {$db_prefix}pm_recipients AS pmr)") . ($context['sort_by'] == 'name' ? ("
      LEFT JOIN {$db_prefix}members AS mem ON (mem.ID_MEMBER = " . ($context['folder'] == 'outbox' ? 'pmr.ID_MEMBER' : 'pm.ID_MEMBER_FROM') . ")") : '') . "
    WHERE " . ($context['folder'] == 'outbox' ? "pm.ID_MEMBER_FROM = $ID_MEMBER
      AND pm.deletedBySender = 0" : "pmr.ID_PM = pm.ID_PM
      AND pmr.ID_MEMBER = $ID_MEMBER
      AND pmr.deleted = 0$labelQuery") . (empty($_GET['pmsg']) ? '' : "
      AND pm.ID_PM = " . (int) $_GET['pmsg']) . "
ORDER BY " . ($_GET['sort'] == 'pm.ID_PM' && $context['folder'] != 'outbox' ? 'pmr.ID_PM' : $_GET['sort']) . ($descending ? ' DESC' : ' DESC') . "
    ". (empty($_GET['pmsg']) ? (isset($context['sl-singlepm']) ? 'LIMIT '.$context['sl-singlepm'].', 1' : "LIMIT $_GET[start], $modSettings[defaultMaxMessages]" ) : (isset($context['sl-singlepm']) ? 'LIMIT '.$context['sl-singlepm'].', 1' : "LIMIT $_GET[start], $modSettings[defaultMaxMessages]" )), __FILE__, __LINE__);

  // Load the ID_PMs and ID_MEMBERs and initialize recipients.
  $pms = array();
  $posters = $context['folder'] == 'outbox' ? array($ID_MEMBER) : array();
  $recipients = array();
  while ($row = mysqli_fetch_assoc($request))
  {
    if (!isset($recipients[$row['ID_PM']]))
    {
      $pms[] = $row['ID_PM'];
      if (!empty($row['ID_MEMBER_FROM']) && $context['folder'] != 'outbox')
        $posters[] = $row['ID_MEMBER_FROM'];
      $recipients[$row['ID_PM']] = array(
        'to' => array(),
        'bcc' => array()
      );
    }
  }
  mysqli_free_result($request);

  if (!empty($pms))
  {

if(isset($context['sl-singlepm']) && empty($pms))
    fatal_error($txt['singlepm_prange'], false);
    
        $request = db_query("
      SELECT pmr.ID_PM, mem_to.ID_MEMBER AS ID_MEMBER_TO, mem_to.realName AS toName, pmr.bcc, pmr.labels, pmr.is_read
      FROM {$db_prefix}pm_recipients AS pmr
        LEFT JOIN {$db_prefix}members AS mem_to ON (mem_to.ID_MEMBER = pmr.ID_MEMBER)
      WHERE pmr.ID_PM IN (" . implode(', ', $pms) . ")", __FILE__, __LINE__);
    $context['message_labels'] = array();
    $context['message_replied'] = array();
    while ($row = mysqli_fetch_assoc($request))
    {
      if ($context['folder'] == 'outbox' || empty($row['bcc']))
        $recipients[$row['ID_PM']][empty($row['bcc']) ? 'to' : 'bcc'][] = empty($row['ID_MEMBER_TO']) ? $txt[28] : '<a href="' . $scripturl . '?action=profile;u=' . $row['ID_MEMBER_TO'] . '">' . $row['toName'] . '</a>';

      if ($row['ID_MEMBER_TO'] == $ID_MEMBER && $context['folder'] != 'outbox')
      {
        $context['message_replied'][$row['ID_PM']] = $row['is_read'] & 2;

        $row['labels'] = $row['labels'] == '' ? array() : explode(',', $row['labels']);
        foreach ($row['labels'] as $v)
        {
          if (isset($context['labels'][(int) $v]))
            $context['message_labels'][$row['ID_PM']][(int) $v] = array('id' => $v, 'name' => $context['labels'][(int) $v]['name']);
        }
      }
    }
    mysqli_free_result($request);

    // Load any users....
    $posters = array_unique($posters);
    if (!empty($posters))
      loadMemberData($posters);

    // Execute the query!
    $messages_request = db_query("
      SELECT pm.ID_PM, pm.subject, pm.ID_MEMBER_FROM, pm.body, pm.msgtime, pm.fromName
      FROM {$db_prefix}personal_messages AS pm" . ($context['folder'] == 'outbox' ? "
        LEFT JOIN {$db_prefix}pm_recipients AS pmr ON (pmr.ID_PM = pm.ID_PM)" : '') . ($context['sort_by'] == 'name' ? " 
        LEFT JOIN {$db_prefix}members AS mem ON (mem.ID_MEMBER = " . ($context['folder'] == 'outbox' ? 'pmr.ID_MEMBER' : 'pm.ID_MEMBER_FROM') . ")" : '') . "
      WHERE pm.ID_PM IN (" . implode(',', $pms) . ")" . ($context['folder'] == 'outbox' ? "
      GROUP BY pm.ID_PM" : '') . "
      ORDER BY $_GET[sort] DESC
      LIMIT " . count($pms), __FILE__, __LINE__);
  }
  else
    $messages_request = false;
  $context['signature_enabled'] = substr($modSettings['signature_settings'], 0, 1) == 1;
  $context['can_send_pm'] = allowedTo('pm_send');
  if (!WIRELESS)
    $context['sub_template'] = 'folder';
  $context['page_title'] = $txt[143];
}

// Get a personal message for the theme.  (used to save memory.)
function prepareMessageContext($reset = false)
{
  global $txt, $scripturl, $modSettings, $context, $messages_request, $memberContext, $recipients;

  // Count the current message number....
  static $counter = null;
  if ($counter === null || $reset)
    $counter = $context['start'];

  static $temp_pm_selected = null;
  if ($temp_pm_selected === null)
  {
    $temp_pm_selected = isset($_SESSION['pm_selected']) ? $_SESSION['pm_selected'] : array();
    $_SESSION['pm_selected'] = array();
  }

  // Bail if it's false, ie. no messages.
  if ($messages_request == false)
    return false;

  // Reset the data?
  if ($reset == true)
    return @mysqli_data_seek($messages_request, 0);

  // Get the next one... bail if anything goes wrong.
  $message = mysqli_fetch_assoc($messages_request);
  if (!$message)
    return(false);

  // Use '(no subject)' if none was specified.
  $message['subject'] = $message['subject'] == '' ? $txt[24] : $message['subject'];

  // Load the message's information - if it's not there, load the guest information.
  if (!loadMemberContext($message['ID_MEMBER_FROM']))
  {
    $memberContext[$message['ID_MEMBER_FROM']]['name'] = $message['fromName'];
    $memberContext[$message['ID_MEMBER_FROM']]['id'] = 0;
    $memberContext[$message['ID_MEMBER_FROM']]['group'] = $txt[28];
    $memberContext[$message['ID_MEMBER_FROM']]['link'] = $message['fromName'];
    $memberContext[$message['ID_MEMBER_FROM']]['email'] = '';
    $memberContext[$message['ID_MEMBER_FROM']]['hide_email'] = true;
    $memberContext[$message['ID_MEMBER_FROM']]['is_guest'] = true;
  }

  // Censor all the important text...
  censorText($message['body']);
  censorText($message['subject']);

  // Run UBBC interpreter on the message.
  $message['body'] = parse_bbc($message['body'], true, 'pm' . $message['ID_PM']);

  // Send the array.
  $output = array(
    'alternate' => $counter % 2,
    'id' => $message['ID_PM'],
    'member' => &$memberContext[$message['ID_MEMBER_FROM']],
    'subject' => $message['subject'],
    'time' => timeformat($message['msgtime']),
    'timestamp' => forum_time(true, $message['msgtime']),
    'counter' => $counter,
    'body' => $message['body'],
    'recipients' => &$recipients[$message['ID_PM']],
    'number_recipients' => count($recipients[$message['ID_PM']]['to']),
    'labels' => &$context['message_labels'][$message['ID_PM']],
    'fully_labeled' => count($context['message_labels'][$message['ID_PM']]) == count($context['labels']),
    'is_replied_to' => &$context['message_replied'][$message['ID_PM']],
    'is_selected' => !empty($temp_pm_selected) && in_array($message['ID_PM'], $temp_pm_selected),
  );

  $counter++;

  $output['p'] = $boardurl . '/mensajes/leer/' . $counter;

  return $output;
}

// Send a new message?
function MessagePost()
{
  global $txt, $sourcedir, $db_prefix, $ID_MEMBER, $scripturl, $modSettings;
  global $context, $options, $func, $language, $user_info;

  isAllowedTo('pm_send');

  if (loadLanguage('PersonalMessage', '', false) === false)
    loadLanguage('InstantMessage');
  // Just in case it was loaded from somewhere else.
  if (!WIRELESS)
  {
    if (loadTemplate('PersonalMessage', false) === false)
      loadTemplate('InstantMessage');
    $context['sub_template'] = 'send';
  }

  // Extract out the spam settings - cause it's neat.
  list ($modSettings['max_pm_recipients'], $modSettings['pm_posts_verification'], $modSettings['pm_posts_per_hour']) = explode(',', $modSettings['pm_spam_settings']);

  $context['show_spellchecking'] = !empty($modSettings['enableSpellChecking']) && function_exists('pspell_new');

  // Set the title...
  $context['page_title'] = $txt[148];

  $context['reply'] = isset($_REQUEST['pmsg']) || isset($_REQUEST['quote']);

  // Check whether we've gone over the limit of messages we can send per hour.
  if (!empty($modSettings['pm_posts_per_hour']) && !allowedTo(array('admin_forum', 'moderate_forum', 'send_mail')))
  {
    // How many have they sent this last hour?
    $request = db_query("
      SELECT COUNT(pr.ID_PM) AS postCount
      FROM ({$db_prefix}personal_messages AS pm, {$db_prefix}pm_recipients AS pr)
      WHERE pm.ID_MEMBER_FROM = $ID_MEMBER
        AND pm.msgtime > " . (time() - 3600) . "
        AND pr.ID_PM = pm.ID_PM", __FILE__, __LINE__);
    list ($postCount) = mysqli_fetch_row($request);
    mysqli_free_result($request);

    if (!empty($postCount) && $postCount >= $modSettings['pm_posts_per_hour'])
    {
      // Excempt moderators.
      $request = db_query("
        SELECT ID_MEMBER
        FROM {$db_prefix}moderators
        WHERE ID_MEMBER = $ID_MEMBER", __FILE__, __LINE__);
      if (mysqli_num_rows($request) == 0)
        fatal_error(sprintf($txt['pm_too_many_per_hour'], $modSettings['pm_posts_per_hour']));
      mysqli_free_result($request);
    }
  }

  // Quoting/Replying to a message?
  if (!empty($_REQUEST['pmsg']))
  {
    $_REQUEST['pmsg'] = (int) $_REQUEST['pmsg'];

    // Get the quoted message (and make sure you're allowed to see this quote!).
    $request = db_query("
      SELECT
        pm.ID_PM, pm.body, pm.subject, pm.msgtime, mem.memberName,
        IFNULL(mem.ID_MEMBER, 0) AS ID_MEMBER, IFNULL(mem.realName, pm.fromName) AS realName
      FROM ({$db_prefix}personal_messages AS pm" . ($context['folder'] == 'outbox' ? '' : ", {$db_prefix}pm_recipients AS pmr") . ")
        LEFT JOIN {$db_prefix}members AS mem ON (mem.ID_MEMBER = pm.ID_MEMBER_FROM)
      WHERE pm.ID_PM = $_REQUEST[pmsg]" . ($context['folder'] == 'outbox' ? "
        AND pm.ID_MEMBER_FROM = $ID_MEMBER" : "
        AND pmr.ID_PM = $_REQUEST[pmsg]
        AND pmr.ID_MEMBER = $ID_MEMBER") . "
      LIMIT 1", __FILE__, __LINE__);
    if (mysqli_num_rows($request) == 0)
      fatal_lang_error('pm_not_yours', false);
    $row_quoted = mysqli_fetch_assoc($request);
    mysqli_free_result($request);

    // Censor the message.
    censorText($row_quoted['subject']);
    censorText($row_quoted['body']);

    // Add 'Re: ' to it....
    if (!isset($context['response_prefix']) && !($context['response_prefix'] = cache_get_data('response_prefix')))
    {
      if ($language === $user_info['language'])
        $context['response_prefix'] = $txt['response_prefix'];
      else
      {
        loadLanguage('index', $language, false);
        $context['response_prefix'] = $txt['response_prefix'];
        loadLanguage('index');
      }
      cache_put_data('response_prefix', $context['response_prefix'], 600);
    }
    $form_subject = $row_quoted['subject'];
    if ($context['reply'] && trim($context['response_prefix']) != '' && $func['strpos']($form_subject, trim($context['response_prefix'])) !== 0)
      $form_subject = $context['response_prefix'] . $form_subject;

    if (isset($_REQUEST['quote']))
    {
      // Remove any nested quotes and <br />...
      $form_message = preg_replace('~<br( /)?' . '>~i', "\n", $row_quoted['body']);
      if (!empty($modSettings['removeNestedQuotes']))
        $form_message = preg_replace(array('~\n?\[quote.*?\].+?\[/quote\]\n?~is', '~^\n~', '~\[/quote\]~'), '', $form_message);
      if (empty($row_quoted['ID_MEMBER']))
        $form_message = '[quote author=&quot;' . $row_quoted['realName'] . "&quot;]\n" . $form_message . "\n[/quote]";
      else
        $form_message = '[quote author=' . $row_quoted['realName'] . ' link=action=profile;u=' . $row_quoted['ID_MEMBER'] . ' date=' . $row_quoted['msgtime'] . "]\n" . $form_message . "\n[/quote]";
    }
    else
      $form_message = '';

    // Do the BBC thang on the message.
    $row_quoted['body'] = parse_bbc($row_quoted['body'], true, 'pm' . $row_quoted['ID_PM']);

    // Set up the quoted message array.
    $context['quoted_message'] = array(
      'id' => $row_quoted['ID_PM'],
      'member' => array(
        'name' => $row_quoted['realName'],
        'username' => $row_quoted['memberName'],
        'id' => $row_quoted['ID_MEMBER'],
        'href' => !empty($row_quoted['ID_MEMBER']) . '/perfil/' . $row_quoted['memberName'],
        'link' => !empty($row_quoted['ID_MEMBER']) ? '<a href="/perfil/' . $row_quoted['memberName'] . '" title="' . $row_quoted['memberName'] . '">' . $row_quoted['realName'] . '</a>' : $row_quoted['realName'],
      ),
      'subject' => $row_quoted['subject'],
      'time' => timeformat($row_quoted['msgtime']),
      'timestamp' => forum_time(true, $row_quoted['msgtime']),
      'body' => $row_quoted['body']
    );
  }
  else
  {
    $context['quoted_message'] = false;
    $form_subject = '';
    $form_message = '';
  }

  // Sending by ID?  Replying to all?  Fetch the realName(s).
  if (isset($_REQUEST['u']))
  {
    // Store all the members who are getting this...
    $membersTo = array();

    // If the user is replying to all, get all the other members this was sent to..
    if ($_REQUEST['u'] == 'all' && isset($row_quoted))
    {
      // Firstly, to reply to all we clearly already have $row_quoted - so have the original member from.
      $membersTo[] = '&quot;' . $row_quoted['realName'] . '&quot;';

      // Now to get the others.
      $request = db_query("
        SELECT mem.realName
        FROM {$db_prefix}pm_recipients AS pmr
          LEFT JOIN {$db_prefix}members AS mem ON (mem.ID_MEMBER = pmr.ID_MEMBER)
        WHERE pmr.ID_PM = $_REQUEST[pmsg]
          AND pmr.ID_MEMBER != $ID_MEMBER
          AND bcc = 0", __FILE__, __LINE__);
      while ($row = mysqli_fetch_assoc($request))
        $membersTo[] = '&quot;' . htmlspecialchars($row['realName']) . '&quot;';
      mysqli_free_result($request);
    }
    else
    {
      $_REQUEST['u'] = explode(',', $_REQUEST['u']);
      foreach ($_REQUEST['u'] as $key => $uID)
        $_REQUEST['u'][$key] = $uID;

      $request = db_query("
        SELECT realName, memberName
        FROM {$db_prefix}members
        WHERE memberName = '" . implode(', ', $_REQUEST['u']) . "'
        LIMIT " . count($_REQUEST['u']), __FILE__, __LINE__);
      while ($row = mysqli_fetch_assoc($request))
        $membersTo[] = '&quot;' . $row['realName'] . '&quot;';
      mysqli_free_result($request);
    }

    // Create the 'to' string - Quoting it, just in case it's something like bob,i,like,commas,man.
    $_REQUEST['to'] = implode(', ', $membersTo);
  }

  // Set the defaults...
  $context['subject'] = $form_subject != '' ? $form_subject : $txt[24];
  $context['message'] = str_replace(array('"', '<', '>'), array('&quot;', '&lt;', '&gt;'), $form_message);
  $context['to'] = isset($_REQUEST['to']) ? stripslashes($_REQUEST['to']) : '';
  $context['bcc'] = isset($_REQUEST['bcc']) ? stripslashes($_REQUEST['bcc']) : '';
  $context['post_error'] = array();
  $context['copy_to_outbox'] = !empty($options['copy_to_outbox']);

  $context['visual_verification'] = !$user_info['is_admin'] && !empty($modSettings['pm_posts_verification']) && $user_info['posts'] < $modSettings['pm_posts_verification'];
  if ($context['visual_verification'])
  {
    $context['use_graphic_library'] = in_array('gd', get_loaded_extensions());
    $context['verificiation_image_href'] = $scripturl . '?action=verificationcode;rand=' . md5(mt_rand());

    // Skip I, J, L, O, Q, S and Z.
    $character_range = array_merge(range('A', 'H'), array('K', 'M', 'N', 'P'), range('R', 'Z'));

    // Generate a new code.
    $_SESSION['visual_verification_code'] = '';
    for ($i = 0; $i < 5; $i++)
      $_SESSION['visual_verification_code'] .= $character_range[array_rand($character_range)];
  }

  // Register this form and get a sequence number in $context.
  checkSubmitOnce('register');
}

// An error in the message...
function messagePostError($error_types, $to, $bcc)
{
  global $txt, $context, $scripturl, $modSettings, $db_prefix, $ID_MEMBER, $boardurl;
  global $func, $user_info;

  $context['show_spellchecking'] = !empty($modSettings['enableSpellChecking']) && function_exists('pspell_new');

  if (!WIRELESS)
    $context['sub_template'] = 'send';

  if (isset($_REQUEST['u']))
    $_REQUEST['u'] = is_array($_REQUEST['u']) ? $_REQUEST['u'] : explode(',', $_REQUEST['u']);

  $context['page_title'] = $txt[148];

  // Set everything up like before....
  $context['to'] = stripslashes($to);
  $context['bcc'] = stripslashes($bcc);
  $context['subject'] = isset($_REQUEST['subject']) ? $func['htmlspecialchars'](stripslashes($_REQUEST['subject'])) : '';
  $context['message'] = isset($_REQUEST['message']) ? str_replace(array('  '), array('&nbsp; '), $func['htmlspecialchars'](stripslashes($_REQUEST['message']))) : '';
  $context['copy_to_outbox'] = !empty($_REQUEST['outbox']);
  $context['reply'] = !empty($_REQUEST['replied_to']);

  if ($context['reply'])
  {
    $_REQUEST['replied_to'] = (int) $_REQUEST['replied_to'];

    $request = db_query("
      SELECT
        pm.ID_PM, pm.body, pm.subject, pm.msgtime, mem.memberName,
        IFNULL(mem.ID_MEMBER, 0) AS ID_MEMBER, IFNULL(mem.realName, pm.fromName) AS realName
      FROM ({$db_prefix}personal_messages AS pm" . ($context['folder'] == 'outbox' ? '' : ", {$db_prefix}pm_recipients AS pmr") . ")
        LEFT JOIN {$db_prefix}members AS mem ON (mem.ID_MEMBER = pm.ID_MEMBER_FROM)
      WHERE pm.ID_PM = $_REQUEST[replied_to]" . ($context['folder'] == 'outbox' ? "
        AND pm.ID_MEMBER_FROM = $ID_MEMBER" : "
        AND pmr.ID_PM = $_REQUEST[replied_to]
        AND pmr.ID_MEMBER = $ID_MEMBER") . "
      LIMIT 1", __FILE__, __LINE__);
    if (mysqli_num_rows($request) == 0)
      fatal_lang_error('pm_not_yours', false);
    $row_quoted = mysqli_fetch_assoc($request);
    mysqli_free_result($request);

    censorText($row_quoted['subject']);
    censorText($row_quoted['body']);

    $context['quoted_message'] = array(
      'id' => $row_quoted['ID_PM'],
      'member' => array(
        'name' => $row_quoted['realName'],
        'username' => $row_quoted['memberName'],
        'id' => $row_quoted['ID_MEMBER'],
        'href' => !empty($row_quoted['ID_MEMBER']) . '/perfil/' . $row_quoted['memberName'],
        'link' => !empty($row_quoted['ID_MEMBER']) ? '<a href="/perfil/' . $row_quoted['memberName'] . '">' . $row_quoted['realName'] . '</a>' : $row_quoted['realName'],
      ),
      'subject' => $row_quoted['subject'],
      'time' => timeformat($row_quoted['msgtime']),
      'timestamp' => forum_time(true, $row_quoted['msgtime']),
      'body' => parse_bbc($row_quoted['body'], true, 'pm' . $row_quoted['ID_PM']),
    );
  }

  // Set each of the errors for the template.
  loadLanguage('Errors');
  $context['post_error'] = array(
    'messages' => array(),
  );
  foreach ($error_types as $error_type)
  {
    // There is no compatible language string. So lets work around that.
    if ($error_type == 'wrong_verification_code')
      $txt['error_wrong_verification_code'] = $txt['visual_verification_failed'];

    $context['post_error'][$error_type] = true;
    if (isset($txt['error_' . $error_type]))
      $context['post_error']['messages'][] = $txt['error_' . $error_type];
  }

  // Check whether we need to show the code again.
  $context['visual_verification'] = !$user_info['is_admin'] && !empty($modSettings['pm_posts_verification']) && $user_info['posts'] < $modSettings['pm_posts_verification'];
  if ($context['visual_verification'])
  {
    $context['use_graphic_library'] = in_array('gd', get_loaded_extensions());
    $context['verificiation_image_href'] = $scripturl . '?action=verificationcode;rand=' . md5(mt_rand());
  }

  // No check for the previous submission is needed.
  checkSubmitOnce('free');

  // Acquire a new form sequence number.
  checkSubmitOnce('register');
}

// Send it!
function MessagePost2()
{
  global $txt, $ID_MEMBER, $context, $sourcedir;
  global $db_prefix, $user_info, $modSettings, $scripturl, $func;

  isAllowedTo('pm_send');
  require_once($sourcedir . '/Subs-Auth.php');

  $to=$POST['to'];

  if($to == $context['user']['name'])
  fatal_error('Tu no puedes enviarte Mensajes privados.');
  
  if (loadLanguage('PersonalMessage', '', false) === false)
    loadLanguage('InstantMessage');

  // Extract out the spam settings - it saves database space!
  list ($modSettings['max_pm_recipients'], $modSettings['pm_posts_verification'], $modSettings['pm_posts_per_hour']) = explode(',', $modSettings['pm_spam_settings']);

  // Check whether we've gone over the limit of messages we can send per hour - fatal error if fails!
  if (!empty($modSettings['pm_posts_per_hour']) && !allowedTo(array('admin_forum', 'moderate_forum', 'send_mail')))
  {
    // How many messages have they sent this last hour?
    $request = db_query("
      SELECT COUNT(pr.ID_PM) AS postCount
      FROM ({$db_prefix}personal_messages AS pm, {$db_prefix}pm_recipients AS pr)
      WHERE pm.ID_MEMBER_FROM = $ID_MEMBER
        AND pm.msgtime > " . (time() - 3600) . "
        AND pr.ID_PM = pm.ID_PM", __FILE__, __LINE__);
    list ($postCount) = mysqli_fetch_row($request);
    mysqli_free_result($request);

    if (!empty($postCount) && $postCount >= $modSettings['pm_posts_per_hour'])
    {
      // Excempt moderators.
      $request = db_query("
        SELECT ID_MEMBER
        FROM {$db_prefix}moderators
        WHERE ID_MEMBER = $ID_MEMBER", __FILE__, __LINE__);
      if (mysqli_num_rows($request) == 0)
        fatal_error(sprintf($txt['pm_too_many_per_hour'], $modSettings['pm_posts_per_hour']));
      mysqli_free_result($request);
    }
  }

  // Initialize the errors we're about to make.
  $post_errors = array();

  // If your session timed out, show an error, but do allow to re-submit.
  if (checkSession('post', '', false) != '')
    $post_errors[] = 'session_timeout';

  $_REQUEST['subject'] = isset($_REQUEST['subject']) ? trim($_REQUEST['subject']) : '';
  $_REQUEST['to'] = empty($_POST['to']) ? (empty($_GET['to']) ? '' : $_GET['to']) : stripslashes($_POST['to']);
  $_REQUEST['bcc'] = empty($_POST['bcc']) ? (empty($_GET['bcc']) ? '' : $_GET['bcc']) : stripslashes($_POST['bcc']);

  // Did they make any mistakes?
  if ($_REQUEST['subject'] == '')
    $post_errors[] = 'no_subject';
  if (!isset($_REQUEST['message']) || $_REQUEST['message'] == '')
    $post_errors[] = 'no_message';
  elseif (!empty($modSettings['max_messageLength']) && $func['strlen']($_REQUEST['message']) > $modSettings['max_messageLength'])
    $post_errors[] = 'long_message';
  if (empty($_REQUEST['to']) && empty($_REQUEST['bcc']) && empty($_REQUEST['u']))
    $post_errors[] = 'no_to';

  // Wrong verification code?
  if (!$user_info['is_admin'] && !empty($modSettings['pm_posts_verification']) && $user_info['posts'] < $modSettings['pm_posts_verification'] && (empty($_REQUEST['visual_verification_code']) || strtoupper($_REQUEST['visual_verification_code']) !== $_SESSION['visual_verification_code']))
    $post_errors[] = 'wrong_verification_code';

  // If they did, give a chance to make ammends.
  if (!empty($post_errors))
    return messagePostError($post_errors, $func['htmlspecialchars']($_REQUEST['to']), $func['htmlspecialchars']($_REQUEST['bcc']));

  // Want to take a second glance before you send?
  if (isset($_REQUEST['preview']))
  {
    // Set everything up to be displayed.
    $context['preview_subject'] = $func['htmlspecialchars'](stripslashes($_REQUEST['subject']));
    $context['preview_message'] = $func['htmlspecialchars'](stripslashes($_REQUEST['message']), ENT_QUOTES);
    preparsecode($context['preview_message'], true);

    // Parse out the BBC if it is enabled.
    $context['preview_message'] = parse_bbc($context['preview_message']);

    // Censor, as always.
    censorText($context['preview_subject']);
    censorText($context['preview_message']);

    // Set a descriptive title.
    $context['page_title'] = $txt[507] . ' - ' . $context['preview_subject'];

    // Pretend they messed up :P.
    return messagePostError(array(), $func['htmlspecialchars']($_REQUEST['to']), $func['htmlspecialchars']($_REQUEST['bcc']));
  }

  // Protect from message spamming.
  spamProtection('spam');

  // Prevent double submission of this form.
  checkSubmitOnce('check');

  // Initialize member ID array.
  $recipients = array(
    'to' => array(),
    'bcc' => array()
  );

  // Format the to and bcc members.
  $input = array(
    'to' => array(),
    'bcc' => array()
  );

  if (empty($_REQUEST['u']))
  {
    // To who..?
    if (!empty($_REQUEST['to']))
    {
      // We're going to take out the "s anyway ;).
      $_REQUEST['to'] = strtr($_REQUEST['to'], array('\\"' => '"'));

      preg_match_all('~"([^"]+)"~', $_REQUEST['to'], $matches);
      $input['to'] = array_unique(array_merge($matches[1], explode(',', preg_replace('~"([^"]+)"~', '', $_REQUEST['to']))));
    }

    // Your secret's safe with me!
    if (!empty($_REQUEST['bcc']))
    {
      // We're going to take out the "s anyway ;).
      $_REQUEST['bcc'] = strtr($_REQUEST['bcc'], array('\\"' => '"'));

      preg_match_all('~"([^"]+)"~', $_REQUEST['bcc'], $matches);
      $input['bcc'] = array_unique(array_merge($matches[1], explode(',', preg_replace('~"([^"]+)"~', '', $_REQUEST['bcc']))));
    }

    foreach ($input as $rec_type => $rec)
    {
      foreach ($rec as $index => $member)
        if (strlen(trim($member)) > 0)
          $input[$rec_type][$index] = $func['htmlspecialchars']($func['strtolower'](stripslashes(trim($member))));
        else
          unset($input[$rec_type][$index]);
    }

    // Find the requested members - bcc and to.
    $foundMembers = findMembers(array_merge($input['to'], $input['bcc']));

    // Store IDs of the members that were found.
    foreach ($foundMembers as $member)
    {
      // It's easier this way.
      $member['name'] = strtr($member['name'], array('&#039;' => '\''));

      foreach ($input as $rec_type => $to_members)
        if (array_intersect(array($func['strtolower']($member['username']), $func['strtolower']($member['name']), $func['strtolower']($member['email'])), $to_members))
        {
          $recipients[$rec_type][] = $member['id'];

          // Get rid of this username. The ones that remain were not found.
          $input[$rec_type] = array_diff($input[$rec_type], array($func['strtolower']($member['username']), $func['strtolower']($member['name']), $func['strtolower']($member['email'])));
        }
    }
  }
  else
  {
    $_REQUEST['u'] = explode(',', $_REQUEST['u']);
    foreach ($_REQUEST['u'] as $key => $uID)
      $_REQUEST['u'][$key] = (int) $uID;

    $request = db_query("
      SELECT ID_MEMBER
      FROM {$db_prefix}members
      WHERE ID_MEMBER IN (" . implode(',', $_REQUEST['u']) . ")
      LIMIT " . count($_REQUEST['u']), __FILE__, __LINE__);
    while ($row = mysqli_fetch_assoc($request))
      $recipients['to'][] = $row['ID_MEMBER'];
    mysqli_free_result($request);
  }

  // Before we send the PM, let's make sure we don't have an abuse of numbers.
  if (!empty($modSettings['max_pm_recipients']) && count($recipients['to']) + count($recipients['bcc']) > $modSettings['max_pm_recipients'] && !allowedTo(array('moderate_forum', 'send_mail', 'admin_forum')))
  {
    $context['send_log'] = array(
      'sent' => array(),
      'failed' => array(sprintf($txt['pm_too_many_recipients'], $modSettings['max_pm_recipients'])),
    );
  }
  // Do the actual sending of the PM.
  else
  {
    if (!empty($recipients['to']) || !empty($recipients['bcc']))
      $context['send_log'] = sendpm($recipients, $_REQUEST['subject'], $_REQUEST['message'], !empty($_REQUEST['outbox']));
    else
      $context['send_log'] = array(
        'sent' => array(),
        'failed' => array()
      );
  }

  // Add a log message for all recipients that were not found.
  foreach ($input as $rec_type => $rec)
  {
    // Either bad_to or bad_bcc.
    if (!empty($rec) && !in_array('bad_' . $rec_type, $post_errors))
      $post_errors[] = 'bad_' . $rec_type;
    foreach ($rec as $i => $member)
    {
      $context['send_log']['failed'][] = sprintf($txt['pm_error_user_not_found'], $input[$rec_type][$i]);
    }
  }

  // Mark the message as "replied to".
  if (!empty($context['send_log']['sent']) && !empty($_REQUEST['replied_to']) && isset($_REQUEST['f']) && $_REQUEST['f'] == 'inbox')
  {
    db_query("
      UPDATE {$db_prefix}pm_recipients
      SET is_read = is_read | 2
      WHERE ID_PM = " . (int) $_REQUEST['replied_to'] . "
        AND ID_MEMBER = $ID_MEMBER
      LIMIT 1", __FILE__, __LINE__);
  }

  // If one or more of the recipient were invalid, go back to the post screen with the failed usernames.
  if (!empty($context['send_log']['failed']))
    return messagePostError($post_errors, empty($input['to']) ? '' : '&quot;' . implode('&quot;, &quot;', $input['to']) . '&quot;', empty($input['bcc']) ? '' : '&quot;' . implode('&quot;, &quot;', $input['bcc']) . '&quot;');

  // Go back to the where they sent from, if possible...
  redirectexit($context['current_label_redirect']);
}

// This function lists all buddies for wireless protocols.
function WirelessAddBuddy()
{
  global $scripturl, $txt, $db_prefix, $user_info, $context;

  isAllowedTo('pm_send');
  $context['page_title'] = $txt['wireless_pm_add_buddy'];

  $current_buddies = empty($_REQUEST['u']) ? array() : explode(',', $_REQUEST['u']);
  foreach ($current_buddies as $key => $buddy)
    $current_buddies[$key] = (int) $buddy;

  $base_url = $scripturl . '?action=pm;sa=send;user=' . (empty($current_buddies) ? '' : implode(',', $current_buddies) . ',');
  $context['pm_href'] = $scripturl . '?action=pm;sa=send' . (empty($current_buddies) ? '' : ';user=' . implode(',', $current_buddies));

  $context['buddies'] = array();
  if (!empty($user_info['buddies']))
  {
    $request = db_query("
      SELECT ID_MEMBER, realName
      FROM {$db_prefix}members
      WHERE ID_MEMBER IN (" . implode(',', $user_info['buddies']) . ")
      ORDER BY realName
      LIMIT " . count($user_info['buddies']), __FILE__, __LINE__);
    while ($row = mysqli_fetch_assoc($request))
      $context['buddies'][] = array(
        'id' => $row['ID_MEMBER'],
        'name' => $row['realName'],
        'selected' => in_array($row['ID_MEMBER'], $current_buddies),
        'add_href' => $base_url . $row['ID_MEMBER'],
      );
    mysqli_free_result($request);
  }
}

// This function performs all additional stuff...
function MessageActionsApply()
{
  global $txt, $db_prefix, $ID_MEMBER, $context, $user_info;


  if (isset($_REQUEST['del_selected']))
    $_REQUEST['pm_action'] = 'delete';

  if (isset($_REQUEST['pm_action']) && $_REQUEST['pm_action'] != '' && !empty($_REQUEST['pms']) && is_array($_REQUEST['pms']))
  {
    foreach ($_REQUEST['pms'] as $pm)
      $_REQUEST['pm_actions'][(int) $pm] = $_REQUEST['pm_action'];
  }

  if (empty($_REQUEST['pm_actions']))
    redirectexit($context['current_label_redirect']);

  $to_delete = array();
  $to_label = array();
  $label_type = array();
  foreach ($_REQUEST['pm_actions'] as $pm => $action)
  {
    if ($action === 'delete')
      $to_delete[] = (int) $pm;
    else
    {
      if (substr($action, 0, 4) == 'add_')
      {
        $type = 'add';
        $action = substr($action, 4);
      }
      elseif (substr($action, 0, 4) == 'rem_')
      {
        $type = 'rem';
        $action = substr($action, 4);
      }
      else
        $type = 'unk';

      if ($action == '-1' || $action == '0' || (int) $action > 0)
      {
        $to_label[(int) $pm] = (int) $action;
        $label_type[(int) $pm] = $type;
      }
    }
  }

  // Deleting, it looks like?
  if (!empty($to_delete))
    deleteMessages($to_delete, $context['folder']);

  // Are we labeling anything?
  if (!empty($to_label) && $context['folder'] == 'inbox')
  {
    $updateErrors = 0;

    // Get information about each message...
    $request = db_query("
      SELECT ID_PM, labels
      FROM {$db_prefix}pm_recipients
      WHERE ID_MEMBER = $ID_MEMBER
        AND ID_PM IN (" . implode(',', array_keys($to_label)) . ")
      LIMIT " . count($to_label), __FILE__, __LINE__);
    while ($row = mysqli_fetch_assoc($request))
    {
      $labels = $row['labels'] == '' ? array('-1') : explode(',', trim($row['labels']));

      // Already exists?  Then... unset it!
      $ID_LABEL = array_search($to_label[$row['ID_PM']], $labels);
      if ($ID_LABEL !== false && $label_type[$row['ID_PM']] !== 'add')
        unset($labels[$ID_LABEL]);
      elseif ($label_type[$row['ID_PM']] !== 'rem')
        $labels[] = $to_label[$row['ID_PM']];

      $set = implode(',', array_unique($labels));
      if ($set == '')
        $set = '-1';

      // Check that this string isn't going to be too large for the database.
      if ($set > 60)
        $updateErrors++;
      else
      {
        db_query("
          UPDATE {$db_prefix}pm_recipients
          SET labels = '$set'
          WHERE ID_PM = $row[ID_PM]
            AND ID_MEMBER = $ID_MEMBER
          LIMIT 1", __FILE__, __LINE__);
      }
    }
    mysqli_free_result($request);

    // Any errors?
    // !!! Separate the sprintf?
    if (!empty($updateErrors))
      fatal_error(sprintf($txt['labels_too_many'], $updateErrors));
  }

  // Back to the folder.
  $_SESSION['pm_selected'] = array_keys($to_label);
  redirectexit($context['current_label_redirect'] . (count($to_label) == 1 ? '#' . $_SESSION['pm_selected'][0] : ''), count($to_label) == 1 && $context['browser']['is_ie']);
}

// Are you sure you want to PERMANENTLY (mostly) delete ALL your messages?
function MessageKillAllQuery()
{
  global $txt, $context;

  // Only have to set up the template....
  $context['sub_template'] = 'ask_delete';
  $context['page_title'] = $txt[412];
  $context['delete_all'] = $_REQUEST['f'] == 'all';

  // And set the folder name...
  $txt[412] = str_replace('PMBOX', $context['folder'] != 'outbox' ? $txt[316] : $txt[320], $txt[412]);
}

// Delete ALL the messages!
function MessageKillAll()
{
  global $context;

  // If all then delete all messages the user has.
  if ($_REQUEST['f'] == 'all')
    deleteMessages(null, null);
  // Otherwise just the selected folder.
  else
    deleteMessages(null, $_REQUEST['f'] != 'outbox' ? 'inbox' : 'outbox');

  // Done... all gone.
  redirectexit($context['current_label_redirect']);
}

// This function allows the user to delete all messages older than so many days.
function MessagePrune()
{
  global $txt, $context, $db_prefix, $ID_MEMBER, $scripturl;

  // Actually delete the messages.
  if (isset($_REQUEST['age']))
  {
    // Calculate the time to delete before.
    $deleteTime = time() - (86400 * (int) $_REQUEST['age']);

    // Array to store the IDs in.
    $toDelete = array();

    // Select all the messages they have sent older than $deleteTime.
    $request = db_query("
      SELECT ID_PM
      FROM {$db_prefix}personal_messages
      WHERE deletedBySender = 0
        AND ID_MEMBER_FROM = $ID_MEMBER
        AND msgtime < $deleteTime", __FILE__, __LINE__);
    while ($row = mysqli_fetch_row($request))
      $toDelete[] = $row[0];
    mysqli_free_result($request);

    // Select all messages in their inbox older than $deleteTime.
    $request = db_query("
      SELECT pmr.ID_PM
      FROM ({$db_prefix}pm_recipients AS pmr, {$db_prefix}personal_messages AS pm)
      WHERE pmr.deleted = 0
        AND pmr.ID_MEMBER = $ID_MEMBER
        AND pm.ID_PM = pmr.ID_PM
        AND pm.msgtime < $deleteTime", __FILE__, __LINE__);
    while ($row = mysqli_fetch_assoc($request))
      $toDelete[] = $row['ID_PM'];
    mysqli_free_result($request);

    // Delete the actual messages.
    deleteMessages($toDelete);

    // Go back to their inbox.
    redirectexit($context['current_label_redirect']);
  }

  $context['sub_template'] = 'prune';
  $context['page_title'] = $txt['pm_prune'];
}

// Delete the specified personal messages.
function deleteMessages($personal_messages, $folder = null, $owner = null)
{
  global $ID_MEMBER, $db_prefix, $user_info;

  if ($owner === null)
    $owner = array($ID_MEMBER);
  elseif (empty($owner))
    return;
  elseif (!is_array($owner))
    $owner = array($owner);

  if ($personal_messages !== null)
  {
    if (empty($personal_messages) || !is_array($personal_messages))
      return;

    foreach ($personal_messages as $index => $delete_id)
      $personal_messages[$index] = (int) $delete_id;

    $where =  '
        AND ID_PM IN (' . implode(', ', array_unique($personal_messages)) . ')';
  }
  else
    $where = '';

  if ($folder == 'outbox' || $folder === null)
  {
    db_query("
      UPDATE {$db_prefix}personal_messages
      SET deletedBySender = 1
      WHERE ID_MEMBER_FROM IN (" . implode(', ', $owner) . ")
        AND deletedBySender = 0$where", __FILE__, __LINE__);
  }
  if ($folder != 'outbox' || $folder === null)
  {
    // Calculate the number of messages each member's gonna lose...
    $request = db_query("
      SELECT ID_MEMBER, COUNT(*) AS numDeletedMessages, IF(is_read & 1, 1, 0) AS is_read
      FROM {$db_prefix}pm_recipients
      WHERE ID_MEMBER IN (" . implode(', ', $owner) . ")
        AND deleted = 0$where
      GROUP BY ID_MEMBER, is_read", __FILE__, __LINE__);
    // ...And update the statistics accordingly - now including unread messages!.
    while ($row = mysqli_fetch_assoc($request))
    {
      if ($row['is_read'])
        updateMemberData($row['ID_MEMBER'], array('instantMessages' => $where == '' ? 0 : "instantMessages - $row[numDeletedMessages]"));
      else
        updateMemberData($row['ID_MEMBER'], array('instantMessages' => $where == '' ? 0 : "instantMessages - $row[numDeletedMessages]", 'unreadMessages' => $where == '' ? 0 : "unreadMessages - $row[numDeletedMessages]"));

      // If this is the current member we need to make their message count correct.
      if ($ID_MEMBER == $row['ID_MEMBER'])
      {
        $user_info['messages'] -= $row['numDeletedMessages'];
        if (!($row['is_read']))
          $user_info['unread_messages'] -= $row['numDeletedMessages'];
      }
    }
    mysqli_free_result($request);

    // Do the actual deletion.
    db_query("
      UPDATE {$db_prefix}pm_recipients
      SET deleted = 1
      WHERE ID_MEMBER IN (" . implode(', ', $owner) . ")
        AND deleted = 0$where", __FILE__, __LINE__);
  }

  // If sender and recipients all have deleted their message, it can be removed.
  $request = db_query("
    SELECT pm.ID_PM, pmr.ID_PM AS recipient
    FROM {$db_prefix}personal_messages AS pm
      LEFT JOIN {$db_prefix}pm_recipients AS pmr ON (pmr.ID_PM = pm.ID_PM AND deleted = 0)
    WHERE pm.deletedBySender = 1
      " . str_replace('ID_PM', 'pm.ID_PM', $where) . "
    HAVING recipient IS null", __FILE__, __LINE__);
  $remove_pms = array();
  while ($row = mysqli_fetch_assoc($request))
    $remove_pms[] = $row['ID_PM'];
  mysqli_free_result($request);

  if (!empty($remove_pms))
  {
    db_query("
      DELETE FROM {$db_prefix}personal_messages
      WHERE ID_PM IN (" . implode(', ', $remove_pms) . ")
      LIMIT " . count($remove_pms), __FILE__, __LINE__);

    db_query("
      DELETE FROM {$db_prefix}pm_recipients
      WHERE ID_PM IN (" . implode(', ', $remove_pms) . ')', __FILE__, __LINE__);
  }
}

// Mark personal messages read.
function markMessages($personal_messages = null, $label = null, $owner = null)
{
  global $ID_MEMBER, $db_prefix, $context, $user_info;

  if ($owner === null)
    $owner = $ID_MEMBER;

  db_query("
    UPDATE {$db_prefix}pm_recipients
    SET is_read = is_read | 1
    WHERE ID_MEMBER = $owner
      AND NOT (is_read & 1)" . ($label === null ? '' : "
      AND FIND_IN_SET($label, labels)") . ($personal_messages !== null ? "
      AND ID_PM IN (" . implode(', ', $personal_messages) . ")
    LIMIT " . count($personal_messages) : ''), __FILE__, __LINE__);

  if ($owner == $ID_MEMBER)
  {
    foreach ($context['labels'] as $label)
      $context['labels'][(int) $label['id']]['unread_messages'] = 0;
  }

  // If something wasn't marked as read, get the number of unread messages remaining.
  if (db_affected_rows() > 0)
  {
    $result = db_query("
      SELECT labels, COUNT(*) AS num
      FROM {$db_prefix}pm_recipients
      WHERE ID_MEMBER = $owner
        AND NOT (is_read & 1)
      GROUP BY labels", __FILE__, __LINE__);
    $total_unread = 0;
    while ($row = mysqli_fetch_assoc($result))
    {
      $total_unread += $row['num'];

      if ($owner != $ID_MEMBER)
        continue;

      $this_labels = explode(',', $row['labels']);
      foreach ($this_labels as $this_label)
        $context['labels'][(int) $this_label]['unread_messages'] += $row['num'];
    }
    mysqli_free_result($result);

    updateMemberData($owner, array('unreadMessages' => $total_unread));

    // If it was for the current member, reflect this in the $user_info array too.
    if ($owner == $ID_MEMBER)
      $user_info['unread_messages'] = $total_unread;
  }
}

// This function handles adding, deleting and editing labels on messages.
function ManageLabels()
{
  global $txt, $context, $db_prefix, $ID_MEMBER, $scripturl, $func;

  $context['page_title'] = $txt['pm_manage_labels'];
  $context['sub_template'] = 'labels';
  $the_labels = array();
  // Add all existing labels to the array to save, slashing them as necessary...
  foreach ($context['labels'] as $label)
  {
    if ($label['id'] != -1)
      $the_labels[$label['id']] = addslashes($label['name']);
  }

  if (isset($_GET['sesc']))
  {
    // This will be for updating messages.
    $message_changes = array();
    $new_labels = array();

    // Adding a new label?
    if (isset($_POST['add']))
    {
      $_POST['label'] = strtr($func['htmlspecialchars'](trim($_POST['label'])), array(',' => '&#044;'));

      if ($func['strlen']($_POST['label']) > 30)
        $_POST['label'] = $func['substr']($_POST['label'], 0, 30);
      if ($_POST['label'] != '')
        $the_labels[] = $_POST['label'];
    }
    // Deleting an existing label?
    elseif (isset($_POST['delete'], $_POST['delete_label']))
    {
      $i = 0;
      foreach ($the_labels as $id => $name)
      {
        if (isset($_POST['delete_label'][$id]))
        {
          unset($the_labels[$id]);
          $message_changes[$id] = true;
        }
        else
          $new_labels[$id] = $i++;
      }
    }
    // The hardest one to deal with... changes.
    elseif (isset($_POST['save']) && !empty($_POST['label_name']))
    {
      $i = 0;
      foreach ($the_labels as $id => $name)
      {
        if ($id == -1)
          continue;
        elseif (isset($_POST['label_name'][$id]))
        {
          $_POST['label_name'][$id] = trim(strtr($func['htmlspecialchars']($_POST['label_name'][$id]), array(',' => '&#044;')));

          if ($func['strlen']($_POST['label_name'][$id]) > 30)
            $_POST['label_name'][$id] = $func['substr']($_POST['label_name'][$id], 0, 30);
          if ($_POST['label_name'][$id] != '')
          {
            $the_labels[(int) $id] = $_POST['label_name'][$id];
            $new_labels[$id] = $i++;
          }
          else
          {
            unset($the_labels[(int) $id]);
            $message_changes[(int) $id] = true;
          }
        }
        else
          $new_labels[$id] = $i++;
      }
    }

    // Save the label status.
    updateMemberData($ID_MEMBER, array('messageLabels' => "'" . implode(',', $the_labels) . "'"));

    // Update all the messages currently with any label changes in them!
    if (!empty($message_changes))
    {
      $searchArray = array_keys($message_changes);

      if (!empty($new_labels))
      {
        for ($i = max($searchArray) + 1, $n = max(array_keys($new_labels)); $i <= $n; $i++)
          $searchArray[] = $i;
      }

      // Now find the messages to change.
      $request = db_query("
        SELECT ID_PM, labels
        FROM {$db_prefix}pm_recipients
        WHERE FIND_IN_SET('" . implode("', labels) OR FIND_IN_SET('", $searchArray) . "', labels)
          AND ID_MEMBER = $ID_MEMBER", __FILE__, __LINE__);
      while ($row = mysqli_fetch_assoc($request))
      {
        // Do the long task of updating them...
        $toChange = explode(',', $row['labels']);

        foreach ($toChange as $key => $value)
          if (in_array($value, $searchArray))
          {
            if (isset($new_labels[$value]))
              $toChange[$key] = $new_labels[$value];
            else
              unset($toChange[$key]);
          }

        if (empty($toChange))
          $toChange[] = '-1';

        // Update the message.
        db_query("
          UPDATE {$db_prefix}pm_recipients
          SET labels = '" . implode(',', array_unique($toChange)) . "'
          WHERE ID_PM = $row[ID_PM]
            AND ID_MEMBER = $ID_MEMBER
          LIMIT 1", __FILE__, __LINE__);
      }
      mysqli_free_result($request);
    }

    // To make the changes appear right away, redirect.
    redirectExit('action=pm;sa=manlabels');
  }
}

function theme_quickreply_box()
{
  global $txt, $modSettings, $db_prefix;
  global $context, $settings, $user_info;

  if (isset($settings['use_default_images']) && $settings['use_default_images'] == 'defaults' && isset($settings['default_template']))
  {
    $temp1 = $settings['theme_url'];
    $settings['theme_url'] = $settings['default_theme_url'];

    $temp2 = $settings['images_url'];
    $settings['images_url'] = $settings['default_images_url'];

    $temp3 = $settings['theme_dir'];
    $settings['theme_dir'] = $settings['default_theme_dir'];
  }

  // Initialize smiley array...
  $context['smileys'] = array(
    'postform' => array(),
    'popup' => array(),
  );
  loadLanguage('Post');

  // Load smileys - don't bother to run a query if we're not using the database's ones anyhow.
  if (empty($modSettings['smiley_enable']) && $user_info['smiley_set'] != 'none')
    $context['smileys']['postform'][] = array(
      'last' => true,
    );
  elseif ($user_info['smiley_set'] != 'none')
  {
    if (($temp = cache_get_data('posting_smileys', 480)) == null)
    {
      $request = db_query("
        SELECT code, filename, description, smileyRow, hidden
        FROM {$db_prefix}smileys
        WHERE hidden IN (0, 2)
        ORDER BY smileyRow, smileyOrder", __FILE__, __LINE__);
      while ($row = mysqli_fetch_assoc($request))
      {
        $row['code'] = htmlspecialchars($row['code']);
        $row['filename'] = htmlspecialchars($row['filename']);
        $row['description'] = htmlspecialchars($row['description']);

        $context['smileys'][empty($row['hidden']) ? 'postform' : 'popup'][$row['smileyRow']]['smileys'][] = $row;
      }
      mysqli_free_result($request);

      cache_put_data('posting_smileys', $context['smileys'], 480);
    }
    else
      $context['smileys'] = $temp;
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

  // Set a flag so the sub template knows what to do...
  $context['show_bbc'] = !empty($modSettings['enableBBC']) && !empty($settings['show_bbc']);

  // Generate a list of buttons that shouldn't be shown - this should be the fastest way to do this.
  if (!empty($modSettings['disabledBBC']))
  {
    $disabled_tags = explode(',', $modSettings['disabledBBC']);
    foreach ($disabled_tags as $tag)
      $context['disabled_tags'][trim($tag)] = true;
  }

  // Go!  Supa-sub-template-smash!
  template_quickreply_box();

  // Switch the URLs back... now we're back to whatever the main sub template is.  (like folder in PersonalMessage.)
  if (isset($settings['use_default_images']) && $settings['use_default_images'] == 'defaults' && isset($settings['default_template']))
  {
    $settings['theme_url'] = $temp1;
    $settings['images_url'] = $temp2;
    $settings['theme_dir'] = $temp3;
  }
}
function ReportMessage(){}
function MessageSearch(){}
function MessageSearch2(){}
?>