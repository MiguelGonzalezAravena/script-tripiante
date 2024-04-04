<?php
function template_buddy_center() {
  global $context, $settings, $scripturl, $txt;

  echo '
    <br />
    <table border="0" width="85%" cellspacing="1" cellpadding="4" class="bordercolor" align="center">
      <tr class="titlebg">
        <td colspan="10" height="26">
          &nbsp;<img src="' . $settings['images_url'] . '/icons/profile_sm.gif" alt="" align="top" />&nbsp;' . $txt['buddy_center'] . '
        </td>
      </tr>
      <tr class="catbg3">
        <td width="20%">' . $txt[68] . '</td>
        <td>' . $txt['online8'] . '</td>
        <td>' . $txt[69] . '</td>
        <td align="center">' . $txt[513] . '</td>
        <td align="center">' . $txt[603] . '</td>
        <td align="center">' . $txt[604] . '</td>
        <td align="center">' . $txt['MSN'] . '</td>
        <td></td>
        <td></td>
        <td></td>
      </tr>';

  // If they don't have any buddies don't list them!
  if (empty($context['buddies']))
    echo '<div class="noesta">' . $context['member']['name'] . ' no tiene ning&uacute;n amigo a&ntilde;adido.</div>';

  // Now loop through each buddy showing info on each.
  $alternate = false;
  $j = count ($context['buddies']) - 1; $i = 0;
  $first = true; $last = false;

  foreach ($context['buddies'] as $buddy) {
    $i++;

    echo '
      <tr class="' . ($alternate ? 'windowbg' : 'windowbg2') . '">
        <td>' . $buddy['link'] . '</td>
        <td align="center"><a href="' . $buddy['online']['href'] . '"><img src="' . $buddy['online']['image_href'] . '" alt="' . $buddy['online']['label'] . '" title="' . $buddy['online']['label'] . '" /></a></td>
        <td align="center">' . ($buddy['hide_email'] ? '' : '<a href="mailto:' . $buddy['email'] . '"><img src="' . $settings['images_url'] . '/email_sm.gif" alt="' . $txt[69] . '" title="' . $txt[69] . ' ' . $buddy['name'] . '" /></a>') . '</td>
        <td align="center">' . $buddy['icq']['link'] . '</td>
        <td align="center">' . $buddy['aim']['link'] . '</td>
        <td align="center">' . $buddy['yim']['link'] . '</td>
        <td align="center">' . $buddy['msn']['link'] . '</td>
        <td align="center">';

        if (!$first)
          echo '<a href="' . $scripturl . '?action=buddies;sa=order;u=' . $buddy['id'] . ';dir=up;sesc=' . $context['session_id'] . '"><img src="' . $settings['images_url'] . '/board_select_spot.gif" alt="' . $txt['buddy_remove'] . '" title="' . $txt['buddy_order_up'] . '" /></a>';
        else
          echo '&nbsp;';

    echo '	
        </td>
        <td align="center">';
        if (!$last)
          echo '<a href="' . $scripturl . '?action=buddies;sa=order;u=' . $buddy['id'] . ';dir=down;sesc=' . $context['session_id'] . '"><img src="' . $settings['images_url'] . '/smiley_select_spot.gif" alt="' . $txt['buddy_remove'] . '" title="' . $txt['buddy_order_down'] . '" /></a>';
        else
          echo '&nbsp;';
    echo '	
        </td>
        <td align="center"><a href="' . $scripturl . '?action=buddies;sa=remove;u=' . $buddy['id'] . ';sesc=' . $context['session_id'] . '"><img src="' . $settings['images_url'] . '/icons/delete.gif" alt="' . $txt['buddy_remove'] . '" title="' . $txt['buddy_remove'] . '" /></a></td>
      </tr>';

    $alternate = !$alternate;
    $first = false;

    if ($i == $j)
      $last = true;
  }

  echo '
    </table>';
  
  if (isset($context['unapproved'])) {
    echo '
      <br /><br />
      <table border="0" width="85%" cellspacing="1" cellpadding="4" class="bordercolor" align="center">
        <tr class="titlebg">
          <td colspan="9" height="26">
            &nbsp;
            <img src="' . $settings['images_url'] . '/icons/profile_sm.gif" alt="" align="top" />
            &nbsp;
            ' . $txt['buddy_unapproved'] . '
          </td>
        </tr>
        <tr class="catbg3">
          <td width="20%">' . $txt[68] . '</td>
          <td>' . $txt['online8'] . '</td>
          <td>' . $txt[69] . '</td>
          <td align="center">' . $txt[513] . '</td>
          <td align="center">' . $txt[603] . '</td>
          <td align="center">' . $txt[604] . '</td>
          <td align="center">' . $txt['MSN'] . '</td>
          <td></td>
          <td></td>
        </tr>';

  // Now loop through each buddy showing info on each.
  $alternate = false;

  foreach ($context['unapproved'] as $buddy) {
    echo '
      <tr class="' . ($alternate ? 'windowbg' : 'windowbg2') . '">
        <td>' . $buddy['link'] . '</td>
        <td align="center"><a href="' . $buddy['online']['href'] . '"><img src="' . $buddy['online']['image_href'] . '" alt="' . $buddy['online']['label'] . '" title="' . $buddy['online']['label'] . '" /></a></td>
        <td align="center">' . ($buddy['hide_email'] ? '' : '<a href="mailto:' . $buddy['email'] . '"><img src="' . $settings['images_url'] . '/email_sm.gif" alt="' . $txt[69] . '" title="' . $txt[69] . ' ' . $buddy['name'] . '" /></a>') . '</td>
        <td align="center">' . $buddy['icq']['link'] . '</td>
        <td align="center">' . $buddy['aim']['link'] . '</td>
        <td align="center">' . $buddy['yim']['link'] . '</td>
        <td align="center">' . $buddy['msn']['link'] . '</td>
        <td align="center"><a href="' . $scripturl . '?action=buddies;sa=approve;u=' . $buddy['id'] . ';sesc=' . $context['session_id'] . '"><img src="' . $settings['images_url'] . '/icons/online.gif" alt="' . $txt['buddy_approve'] . '" title="' . $txt['buddy_approve'] . '" /></a></td>
        <td align="center"><a href="' . $scripturl . '?action=buddies;sa=remove;u=' . $buddy['id'] . ';sesc=' . $context['session_id'] . '"><img src="' . $settings['images_url'] . '/icons/delete.gif" alt="' . $txt['buddy_remove'] . '" title="' . $txt['buddy_remove'] . '" /></a></td>
      </tr>';

    $alternate = !$alternate;
  }

  echo '
    </table>';
  }
  
  if (isset($context['pending'])) {
    echo '
      <br /><br />
      <table border="0" width="85%" cellspacing="1" cellpadding="4" class="bordercolor" align="center">
        <tr class="titlebg">
          <td colspan="9" height="26">
            &nbsp;<img src="' . $settings['images_url'] . '/icons/profile_sm.gif" alt="" align="top" />&nbsp;' . $txt['buddy_pending'] . '
          </td>
        </tr>
        <tr class="catbg3">
          <td width="20%">' . $txt[68] . '</td>
          <td>' . $txt['online8'] . '</td>
          <td>' . $txt[69] . '</td>
          <td align="center">' . $txt[513] . '</td>
          <td align="center">' . $txt[603] . '</td>
          <td align="center">' . $txt[604] . '</td>
          <td align="center">' . $txt['MSN'] . '</td>
          <td></td>
        </tr>';

  // Now loop through each buddy showing info on each.
  $alternate = false;

  foreach ($context['pending'] as $buddy) {
    echo '
      <tr class="' . ($alternate ? 'windowbg' : 'windowbg2') . '">
        <td>' . $buddy['link'] . '</td>
        <td align="center"><a href="' . $buddy['online']['href'] . '"><img src="' . $buddy['online']['image_href'] . '" alt="' . $buddy['online']['label'] . '" title="' . $buddy['online']['label'] . '" /></a></td>
        <td align="center">' . ($buddy['hide_email'] ? '' : '<a href="mailto:' . $buddy['email'] . '"><img src="' . $settings['images_url'] . '/email_sm.gif" alt="' . $txt[69] . '" title="' . $txt[69] . ' ' . $buddy['name'] . '" /></a>') . '</td>
        <td align="center">' . $buddy['icq']['link'] . '</td>
        <td align="center">' . $buddy['aim']['link'] . '</td>
        <td align="center">' . $buddy['yim']['link'] . '</td>
        <td align="center">' . $buddy['msn']['link'] . '</td>
        <td align="center"><a href="' . $scripturl . '?action=buddies;sa=remove;u=' . $buddy['id'] . ';sesc=' . $context['session_id'] . '"><img src="' . $settings['images_url'] . '/icons/delete.gif" alt="' . $txt['buddy_remove'] . '" title="' . $txt['buddy_remove'] . '" /></a></td>
      </tr>';

    $alternate = !$alternate;
  }

  echo '
    </table>';
  }
}

?>