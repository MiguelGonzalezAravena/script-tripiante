<?php
if (!defined('SMF'))
  die('Hacking attempt...');

function ViewModlog() {
  global $db_prefix, $txt, $modSettings, $context;

  loadTemplate('Modlog');
  loadLanguage('Modlog');

  $context['page_title'] = $txt['modlog_view'];

  if (isset($_POST['removeall']))
    db_query("DELETE FROM {$db_prefix}mod_history", __FILE__, __LINE__);

  $result = db_query("
    SELECT m.ID_HISTORY, m.ID_MODERATOR, m.ID_MEMBER, m.ID_TOPIC, m.TYPE, m.ACTION, m.subject, m.reason, mem.ID_MEMBER, mem.realName as MrealName, mem.memberName as MmemberName, mem2.ID_MEMBER, mem2.realName, mem2.memberName
    FROM ({$db_prefix}mod_history AS m, {$db_prefix}members AS mem, {$db_prefix}members AS mem2)
    WHERE m.ID_MODERATOR = mem.ID_MEMBER
    AND m.ID_MEMBER = mem2.ID_MEMBER
    ORDER BY m.ID_HISTORY DESC
    LIMIT " . $modSettings['mod_history'], __FILE__, __LINE__);

  $context['historial'] = array();

  while ($row = mysqli_fetch_assoc($result)) {
    $context['historial'][] = array(
      'realName' => $row['MrealName'],
      'memberName' => $row['MmemberName'],
      'realName2' => $row['realName'],
      'memberName2' => $row['memberName'],
      'ID_TOPIC' => $row['ID_TOPIC'],
      'TYPE' => $row['TYPE'],
      'ACTION' => $row['ACTION'],
      'subject' => $row['subject'],
      'reason' => $row['reason'],
    );
  }

  mysqli_free_result($result);
}

?>