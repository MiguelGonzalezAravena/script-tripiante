<?php

function template_main()
{
	global $txt, $context, $scripturl;

	echo '
<div class="box_buscador"><div class="box_title" style="width: 920px;"><div class="box_txt box_buscadort"><center>',$txt['smftags_popular'], '</center></div><div class="box_rss"><img alt="" src="/images/blank.gif" style="width:14px;height:12px;" border="0" /></div></div><div style="width: 912px;padding:4px;" class="windowbg"><center>En esta nube se reflejan los 100 tags m&aacute;s populares. Cuanto m&aacute;s grande es la palabra, mayor cantidad de veces fue utilizada.<br />
ordenar: <a href="/tags-alfa/" title="alfab&eacute;ticamente">alfab&eacute;ticamente</a> | <a href="/tags-importancia/" title="por importancia">por importancia</a><hr />
';


  	if (isset($context['poptags']))
  		echo $context['poptags'];



 echo '
</center></div></div><div style="clear:both"></div>';

}

function template_results()
{
	global $scripturl, $txt, $context;
echo '
	<table border="0" cellpadding="0" cellspacing="0"  align="center" width="95%">
  <tr>
  	<td align="center"  class="catbg">' . $txt['smftags_resultsfor'] . $context['tag_search'] . '</td>
  	</tr>
  	<tr>
  	<td>
		<table border="0" width="100%" cellspacing="1" cellpadding="4">
					<tr>
						<td class="catbg3">',$txt['smftags_subject'],'</td>
						<td class="catbg3" width="11%">',$txt['smftags_startedby'],'</td>
						<td class="catbg3" width="4%" align="center">',$txt['smftags_replies'],'</td>
						<td class="catbg3" width="4%" align="center">', $txt['smftags_views'], '</td>
					</tr>';
		foreach ($context['tags_topics'] as $i => $topic)
		{
				echo '<tr>';
					echo '<td class="windowbg2"><a href="' . $scripturl . '?topic=' . $topic['ID_TOPIC'] . '.0">' . $topic['subject'] . '</a></td>';
					echo '<td class="windowbg"><a href="' . $scripturl . '?action=profile;u=' . $topic['ID_MEMBER'] . '">' . $topic['posterName'] . '</a></td>';
					echo '<td class="windowbg2">' . $topic['numReplies'] . '</td>';
					echo '<td class="windowbg2">' . $topic['numViews'] . '</td>';
				echo '</tr>';

		}
echo '

  	</tr>
  	</table></td></tr></table><br />
 ';

}
function template_addtag()
{
		global $scripturl, $txt, $context;

	echo '
<form method="POST" action="', $scripturl, '?action=tags;sa=addtag2">
<table border="1" cellpadding="0" cellspacing="0" align="center" width="50%">
  <tr>
    <td width="50%" colspan="2"  align="center" class="catbg">
    <b>', $txt['smftags_addtag2'], '</b></td>
  </tr>
  <tr>
    <td width="28%"  class="windowbg2" align="right"><span class="gen"><b>', $txt['smftags_tagtoadd'], '</b></span></td>
    <td width="72%" class="windowbg2"><input type="text" name="tag" size="64" maxlength="100" /></td>
  </tr>

  <tr>
    <td width="28%" colspan="2" align="center" class="windowbg2">
    <input type="hidden" name="topic" value="', $context['tags_topic'], '" />
    <input type="submit" value="', $txt['smftags_addtag2'], '" name="submit" /></td>

  </tr>
</table>
</form>
';

}
function template_admin_settings()
{
	global $scripturl, $txt, $modSettings;

	echo '
	<table border="0" width="80%" cellspacing="0" align="center" cellpadding="4" class="tborder">
		<tr class="titlebg">
			<td>' . $txt['smftags_settings']. '</td>
		</tr>
		<tr class="windowbg">
			<td>
			<b>' . $txt['smftags_settings']. '</b><br />
			<form method="post" action="' . $scripturl . '?action=tags;sa=admin2">
				<table border="0" width="100%" cellspacing="0" align="center" cellpadding="4">
				<tr><td width="30%">' . $txt['smftags_set_mintaglength'] . '</td><td><input type="text" name="smftags_set_mintaglength" value="' .  $modSettings['smftags_set_mintaglength'] . '" /></td></tr>
				<tr><td width="30%">' . $txt['smftags_set_maxtaglength'] . '</td><td><input type="text" name="smftags_set_maxtaglength" value="' .  $modSettings['smftags_set_maxtaglength'] . '" /></td></tr>
				<tr><td width="30%">' . $txt['smftags_set_maxtags'] . '</td><td><input type="text" name="smftags_set_maxtags" value="' .  $modSettings['smftags_set_maxtags'] . '" /></td></tr>
				<tr>
				<td clospan="2"><b>',$txt['smftags_tagcloud_settings'],'</b></td>
				</tr>
				<tr><td width="30%">' . $txt['smftags_set_cloud_tags_to_show'] . '</td><td><input type="text" name="smftags_set_cloud_tags_to_show" value="' .  $modSettings['smftags_set_cloud_tags_to_show'] . '" /></td></tr>
				<tr><td width="30%">' . $txt['smftags_set_cloud_tags_per_row'] . '</td><td><input type="text" name="smftags_set_cloud_tags_per_row" value="' .  $modSettings['smftags_set_cloud_tags_per_row'] . '" /></td></tr>
				<tr><td width="30%">' . $txt['smftags_set_cloud_max_font_size_precent'] . '</td><td><input type="text" name="smftags_set_cloud_max_font_size_precent" value="' .  $modSettings['smftags_set_cloud_max_font_size_precent'] . '" /></td></tr>
				<tr><td width="30%">' . $txt['smftags_set_cloud_min_font_size_precent'] . '</td><td><input type="text" name="smftags_set_cloud_min_font_size_precent" value="' .  $modSettings['smftags_set_cloud_min_font_size_precent'] . '" /></td></tr>
				</table>

				<input type="submit" name="savesettings" value="', $txt['smftags_savesettings'],  '" />
			</form>
<b>Has SMF Tags helped you?</b> Then support the developers:<br />
    <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
	<input type="hidden" name="cmd" value="_xclick">
	<input type="hidden" name="business" value="sales@visualbasiczone.com">
	<input type="hidden" name="item_name" value="SMF Tags">
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

	//The Copyright is required to remain or contact me to purchase link removal.
echo '<br /><div align="center"><span class="smalltext">Powered By <a href="http://www.smfhacks.com" target="blank">SMF Tags</a></span></div>';
}

function template_suggesttag()
{
	global $scripturl, $txt;

	echo '
<form method="POST" action="', $scripturl, '?action=tags;sa=suggest2">
<table border="1" cellpadding="0" cellspacing="0" width="100%">
  <tr>
    <td width="50%" colspan="2" align="center" class="catbg">
    <b>', $txt['smftags_suggest'], '</b></td>
  </tr>
  <tr>
    <td width="28%"  class="windowbg2" align="right"><span class="gen"><b>', $txt['smftags_tagtosuggest'], '</b></span></td>
    <td width="72%" class="windowbg2"><input type="text" name="tag" size="64" maxlength="100" /></td>
  </tr>

  <tr>
    <td width="28%" colspan="2" align="center" class="windowbg2">
    <input type="submit" value="', $txt['smftags_suggest'], '" name="submit" /></td>

  </tr>
</table>
</form>';
	//The Copyright is required to remain or contact me to purchase link removal.
	echo '<br /><div align="center"><span class="smalltext">Powered By <a href="http://www.smfhacks.com" target="blank">SMF Tags</a></span></div>';
}
?>