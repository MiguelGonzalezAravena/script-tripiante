<?php
if (!defined('SMF'))
  die('Hacking attempt...');

loadLanguage('Shop');
isAllowedTo('shop_admin');

// Inventory administration
function ShopInventory() {
  global $context, $db_prefix, $txt;

  adminIndex('shop_inventory');

  // If we need to do something (ie. not the main inventory admin page)
  if (!empty($_GET['do']) && $_GET['do'] != '') {
    if ($_GET['do'] == 'editmoney') {
      // Check inputs were numbers
      $_POST['money_pocket'] = (float) $_POST['money_pocket'];
      $_POST['money_bank'] = (float) $_POST['money_bank'];
      $_POST['userid'] = (int) $_POST['userid'];
  
      // Update the user's details
      db_query("
        UPDATE {$db_prefix}members
        SET
          money = {$_POST['money_pocket']},
          moneyBank = {$_POST['money_bank']}
        WHERE ID_MEMBER = {$_POST['userid']}
        LIMIT 1", __FILE__, __LINE__);

      // Tell the user that everthing worked find
      $context['shop_message'] = sprintf($txt['shop_changed_money'], $_POST['userid'], $_POST['money_pocket'], $_POST['money_bank']);
    }
    
    // User ID rather than name?
    if (isset($_REQUEST['userid']) && $_REQUEST['userid'] != 0) {
      $_REQUEST['userid'] = (int) $_REQUEST['userid'];
      $clause = 'ID_MEMBER = ' . $_REQUEST['userid'];
    }
    // A name is passed instead
    else {
      // This code from PersonalMessage.php, lines 1531-1535. It trims the " characters off the membername posted, 
      // and then puts all names into an array
      $_REQUEST['searchfor'] = strtr($_REQUEST['searchfor'], array('\\"' => '"'));
      preg_match_all('~"([^"]+)"~', $_REQUEST['searchfor'], $matches);
      $searchforArray = array_unique(array_merge($matches[1], explode(',', preg_replace('~"([^"]+)"~', '', $_REQUEST['searchfor']))));
      
      // We only want the first memberName found
      $searchfor = $searchforArray[0];
      
      $clause = 'memberName = "' . $searchfor . '"';
    }

    // Get the user's information
    $result = db_query("
      SELECT ID_MEMBER, money, moneyBank, memberName, realName
      FROM {$db_prefix}members
      WHERE {$clause}
      LIMIT 1", __FILE__, __LINE__);

    // If this user doesn't exist
    if (mysqli_num_rows($result) == 0) {
      $context['shop_inventory_search'] = 'message';
      // Show an error!
      // $context['shop_message'] = sprintf($txt['shop_member_no_exist'], $_REQUEST['searchfor']);
      fatal_error(sprintf($txt['shop_member_no_exist'], $_REQUEST['searchfor']));
    } else {
      // Get their information
      $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

      mysqli_free_result($result);

      // Set up the array of inventory information
      $context['shop_inv'] = array(
        'member' => $row['ID_MEMBER'],
        'realName' => $row['realName'],
        'money_pocket' => $row['money'],
        'money_bank' => $row['moneyBank']);
    }
  }

  // We need to load the inventory template
  $context['sub_template'] = 'inventory';

  // Set the page title
  $context['page_title'] = $txt['shop'] . ' - ' . $txt['shop_admin_inventory'];

  // Load the administration template
  loadTemplate('ShopAdmin');
}

function ShopUserGroup() {
  global $context, $db_prefix, $txt;
  adminIndex('shop_usergroup');

  // If form wasn't submitted yet...
  if (!isset($_GET['step']) || $_GET['step'] == 1) {
    // Start with an empty list
    $context['shop_usergroups'] = array();

    // Get all non post-based membergroups
    $result = db_query("
      SELECT ID_GROUP, groupName
      FROM {$db_prefix}membergroups
      WHERE minPosts = -1", __FILE__, __LINE__);
  
    // For each membergroup, add it to the list
    while ($row = mysqli_fetch_assoc($result))
      $context['shop_usergroups'][] = array(
        'id' => $row['ID_GROUP'],
        'groupName' => $row['groupName']
      );

    mysqli_free_result($result);
  }
  // If the user has submitted the form
  else {
    // Adding, or subtracting?
    $action = ($_POST['m_action'] == 'sub') ? '-' : '+';

    // Make sure inputs were numeric
    $_POST['usergroup'] = (int) $_POST['usergroup'];
    $_POST['value'] = (float) $_POST['value'];

    // Do it!
    db_query("
      UPDATE {$db_prefix}members
      SET money = money {$action}{$_POST['value']}
      WHERE ID_GROUP = {$_POST['usergroup']}", __FILE__, __LINE__);
  }

  // We're using the "usergroup" template
  $context['sub_template'] = 'usergroup';
  $context['page_title'] = $txt['shop'] . ' - ' . $txt['shop_admin_usergroup'];

  // Load the actual template
  loadTemplate('ShopAdmin');
}
?>
