<?php
/*
Profile Comments
Version 2.0
by:vbgamer45
http://www.smfhacks.com
*/

// Pendiente
function template_commentmain()
{
   ProfileCommentsCopyright();
}

function template_add()
{
  global $context, $scripturl, $txt, $settings;

  // Get the profile id
  $u = (int) @$_REQUEST['u'];

   if (empty($u))
    fatal_error($txt['pcomments_err_noprofile']);

  // Load the spell checker?
  if ($context['show_spellchecking'])
    echo '
                  <script language="JavaScript" type="text/javascript" src="', $settings['default_theme_url'], '/spellcheck.js"></script>';


  echo '<div class="tborder">
<form method="POST" name="cprofile" id="cprofile" action="', $scripturl, '?action=comment&sa=add2">
<table border="0" cellpadding="0" cellspacing="0" bordercolor="#FFFFFF" width="100%" >
  <tr>
    <td width="50%" colspan="2"  align="center" class="catbg">
    <b>',$txt['pcomments_addcomment'],'</b></td>
  </tr>

  <tr>
    <td width="28%" class="windowbg2" align="right"><span class="gen"><b>',$txt['pcomments_subject'],'</b></span></td>
    <td width="72%" class="windowbg2"><input type="text" name="subject" size="64" maxlength="100" /></td>
  </tr>
  <tr>
    <td width="28%"  valign="top" class="windowbg2" align="right"><span class="gen"><b>',$txt['pcomments_acomment'] ,'</b></span></td>
    <td width="72%"  class="windowbg2"><table>
   ';
   theme_postbox('');
   echo '</table></td>
  </tr>
  <tr>
    <td width="28%" colspan="2" height="26" align="center" class="windowbg2">
    <input type="hidden" name="userid" value="', $u , '" />';



// Check if comments are autoapproved
     if(allowedTo('pcomments_autocomment') == false)
         echo $txt['pcomments_text_commentwait'] . '<br />';

     if ($context['show_spellchecking'])
       echo '
                     <input type="button" value="', $txt['spell_check'], '" onclick="spellCheck(\'cprofile\', \'comment\');" />';

echo '
    <input type="submit" value="',$txt['pcomments_addcomment'],'" name="submit" /></td>

  </tr>
</table>
</form>';


  if ($context['show_spellchecking'])
    if(function_exists('parse_bbc'))
      echo '<form action="', $scripturl, '?action=spellcheck" method="post" accept-charset="', $context['character_set'], '" name="spell_form" id="spell_form" target="spellWindow"><input type="hidden" name="spellstring" value="" /></form>';
    else
      echo '<form name="spell_form" id="spell_form" method="post" target="spellWindow" action="', $scripturl, '?action=spellcheck"><input type="hidden" name="spell_formname" value="" /><input type="hidden" name="spell_fieldname" value="" /><input type="hidden" name="spellstring" value="" /></form>';

  echo '</div>';
  
  
   ProfileCommentsCopyright();
}

function template_edit()
{
  global $db_prefix, $context, $scripturl, $txt, $settings;
  
  //Get the comment id
  $id = (int) $_REQUEST['id'];

  if (empty($id))
    fatal_error($txt['pcomments_err_nocom']);

  $dbresult = db_query("
  SELECT 
    p.ID_COMMENT, p.ID_MEMBER, p.comment, p.subject, p.date, p.COMMENT_MEMBER_ID 
  FROM {$db_prefix}profile_comments as p 
  WHERE p.ID_COMMENT = $id LIMIT 1", __FILE__, __LINE__);
  $row = mysqli_fetch_assoc($dbresult);
  mysqli_free_result($dbresult);
  // Load the spell checker?
  if ($context['show_spellchecking'])
    echo '
                  <script language="JavaScript" type="text/javascript" src="', $settings['default_theme_url'], '/spellcheck.js"></script>';


  echo '<div class="tborder">
<form method="POST" name="cprofile" id="cprofile" action="', $scripturl, '?action=comment&sa=edit2">
<table border="0" cellpadding="0" cellspacing="0" bordercolor="#FFFFFF" width="100%" >
  <tr>
    <td width="50%" colspan="2"  align="center" class="catbg">
    <b>',$txt['pcomments_editcomment'],'</b></td>
  </tr>
  <tr>
    <td width="28%" class="windowbg2" align="right"><span class="gen"><b>',$txt['pcomments_subject'],'</b></span></td>
    <td width="72%" class="windowbg2"><input type="text" name="subject" size="64" maxlength="100" value="', $row['subject'], '" /></td>
  </tr>
  <tr>
    <td width="28%"  valign="top" class="windowbg2" align="right"><span class="gen"><b>',$txt['pcomments_acomment'],'</b></span></td>
    <td width="72%"  class="windowbg2">
    <table>
   ';
   theme_postbox($row['comment']);
   echo '</table></td>
  </tr>
  <tr>
    <td width="28%" colspan="2" height="26" align="center" class="windowbg2">
    <input type="hidden" name="commentid" value="', $row['ID_COMMENT'], '" />';


  // Check if comments are autoapproved
     if (allowedTo('pcomments_autocomment') == false)
         echo $txt['pcomments_text_commentwait'] . '<br />';

   if ($context['show_spellchecking'])
       echo '
                     <input type="button" value="', $txt['spell_check'], '" onclick="spellCheck(\'cprofile\', \'comment\');" />';


echo '
    <input type="submit" value="',$txt['pcomments_editcomment'],'" name="submit" /></td>
  </tr>
</table>
</form>';

  if ($context['show_spellchecking'])
    if (function_exists('parse_bbc'))
      echo '<form action="', $scripturl, '?action=spellcheck" method="post" accept-charset="', $context['character_set'], '" name="spell_form" id="spell_form" target="spellWindow"><input type="hidden" name="spellstring" value="" /></form>';
    else
      echo '<form name="spell_form" id="spell_form" method="post" target="spellWindow" action="', $scripturl, '?action=spellcheck"><input type="hidden" name="spell_formname" value="" /><input type="hidden" name="spell_fieldname" value="" /><input type="hidden" name="spellstring" value="" /></form>';


  echo '</div>';

  
   ProfileCommentsCopyright();
}

function template_commentsadmin()
{
  global $txt, $context, $db_prefix, $scripturl;
  
  
  echo '
  <table border="0" width="80%" cellspacing="0" align="center" cellpadding="4" class="tborder">
    <tr class="titlebg">
      <td>' . $txt['pcomments_admin']. '</td>
    </tr>
    <tr class="windowbg">
      <td>
      <b>' . $txt['pcomments_com_wait_appproval']. '</b><br />';
    
    $context['start'] = (int) $_REQUEST['start'];
  
    $dbresult = db_query("
    SELECT 
      COUNT(*) AS total 
    FROM {$db_prefix}profile_comments 
    WHERE  approved = 0", __FILE__, __LINE__);
    $row = mysqli_fetch_assoc($dbresult);
    $total =  $row['total'];
  
    mysqli_free_result($dbresult);
  
  
      echo '
      <table class="tborder" cellspacing="0" align="center" cellpadding="4">
        <tr class="titlebg">
          <td>',$txt['pcomments_com_commnet'],'</td>
          <td>',$txt['pcomments_com_postedby'],'</td>
          <td>',$txt['pcomments_com_profile'],'</td>
          <td>',$txt['pcomments_com_date'],'</td>
          <td>',$txt['pcomments_com_options'],'</td>
        </tr>
      ';
      
      $dbresult = db_query("
      SELECT 
        p.ID_COMMENT, p.ID_MEMBER, p.comment, p.subject, p.date, m.realName, p.COMMENT_MEMBER_ID, m2.realName ProfileName 
      FROM ({$db_prefix}profile_comments as p)  
      LEFT JOIN {$db_prefix}members AS m ON (p.ID_MEMBER = m.ID_MEMBER)
      LEFT JOIN {$db_prefix}members AS m2 ON (p.COMMENT_MEMBER_ID = m2.ID_MEMBER)
      WHERE p.approved = 0 ORDER BY p.ID_COMMENT DESC  LIMIT $context[start],10", __FILE__, __LINE__);
      while($row = mysqli_fetch_assoc($dbresult))
      {
        echo '<td>',$row['subject'],'<br />',parse_bbc($row['comment']),'</td>';
        echo '<td><a href="',$scripturl,'?action=profile;u='  . $row['ID_MEMBER'] . '">' . $row['realName'],'</td>';
        echo '<td><a href="',$scripturl,'?action=profile;u='  . $row['COMMENT_MEMBER_ID'] . '">' . $row['ProfileName'],'</td>';
        echo '<td>',timeformat($row['date']),'</td>';
        echo '<td><a href="', $scripturl, '?action=comment;sa=approve;id=' . $row['ID_COMMENT'] . '">',$txt['pcomments_approve'],'</a><br />
        <a href="', $scripturl, '?action=comment;sa=delete;id=' . $row['ID_COMMENT'] . '">',$txt['pcomments_delcomment'],'</a></td>';
        
      }
      mysqli_free_result($dbresult);
      
      if ($total > 0)
      {
        echo '<tr class="titlebg">
            <td align="left" colspan="5">
            ' . $txt['pcomments_text_pages'];
    
              
            $context['page_index'] = constructPageIndex($scripturl . '?action=comment;sa=admin' , $_REQUEST['start'], $total, 10);
        
            echo $context['page_index'];
    
        echo '
            </td>
          </tr>';
      }
      
      echo '
      </table>
      
      
      <br />
<b>Has Profile Comments helped you?</b> Then support the developers:<br />
    <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
  <input type="hidden" name="cmd" value="_xclick">
  <input type="hidden" name="business" value="sales@visualbasiczone.com">
  <input type="hidden" name="item_name" value="Profile Comments">
  <input type="hidden" name="no_shipping" value="1">
  <input type="hidden" name="no_note" value="1">
  <input type="hidden" name="currency_code" value="USD">
  <input type="hidden" name="tax" value="0">
  <input type="hidden" name="bn" value="PP-DonationsBF">
  <input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-butcc-donate.gif" border="0" name="submit" alt="Make payments with PayPal - it is fast, free and secure!">
  <img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" />
</form>

      </td>
    </tr>
</table>';
  
 ProfileCommentsCopyright();
}

function ProfileCommentsCopyright()
{
  // DO NOT Edit this function
  
// http://www.smfhacks.com/copyright_removal.php
echo '
<div align="center"><!--Link must remain or contact me to pay to remove.-->Powered by <a href="http://www.smfhacks.com" target="blank">Profile Comments</a><!--End Copyright link--></div>';

}


?>