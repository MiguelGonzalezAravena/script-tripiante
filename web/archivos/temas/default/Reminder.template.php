<?php
// Version: 1.1; Reminder

function template_main()
{
	global $context, $settings, $options, $txt, $scripturl;

	echo '<div align="center"><form action="/recuperar-pass/enviando/" method="post" accept-charset="', $context['character_set'], '">
<div class="box_title" style="width: 363px;"><div class="box_txt box_363-34">', $txt[194], ' </div><div class="box_rss"><div class="icon_img"><img src="', $settings['images_url'], '/blank.gif?v3.2.3"></div></div></div><table border="0" width="363" cellspacing="0" cellpadding="4" class="windowbg"><tr align="left"><td colspan="2" class="smalltext" style="padding: 2ex;">', $txt['pswd4'], '</td></tr>

<tr align="left"><td width="40%" align="center"><b class="size11">', $txt['smf100'], ':</b><br /><input tabindex="1" type="text" onfocus="foco(this);" onblur="no_foco(this);" name="user" size="30" /></td></tr><tr><td colspan="2" align="center"><input tabindex="3" class="login" type="submit" value="', $txt['sendtopic_send'], '" /></td></tr></table><input type="hidden" name="sc" value="', $context['session_id'], '" /></form></div><div style="clear:both"></div>';
}

function template_sent()
{
	global $context, $settings, $options, $txt, $scripturl;

	echo '
		<br />
		<table border="0" width="80%" cellspacing="0" cellpadding="4" class="tborder" align="center">
			<tr class="titlebg">
				<td>' . $context['page_title'] . '</td>
			</tr><tr>
				<td class="windowbg" align="left" cellpadding="3" style="padding-top: 3ex; padding-bottom: 3ex;">
					' . $context['description'] . '
				</td>
			</tr>
		</table>';
}

function template_set_password()
{
	global $context, $settings, $options, $txt, $scripturl;

	echo '
	<br />
	<form action="', $scripturl, '?action=reminder;sa=setpassword2" method="post" accept-charset="', $context['character_set'], '">
		<table border="0" width="440" cellspacing="0" cellpadding="4" class="tborder" align="center">
			<tr class="titlebg">
				<td colspan="2">', $context['page_title'], '</td>
			</tr><tr class="windowbg">
				<td width="45%">
					<b>', $txt[81], ': </b><br />
					<span class="smalltext">', $txt[596], '</span>
				</td>
				<td valign="top"><input type="password" name="passwrd1" size="22" /></td>
			</tr><tr class="windowbg">
				<td width="45%"><b>', $txt[82], ': </b></td>
				<td><input type="password" name="passwrd2" size="22" /></td>
			</tr><tr class="windowbg">
				<td colspan="2" align="right"><input type="submit" value="', $txt[10], '" /></td>
			</tr>
		</table>
		<input type="hidden" name="code" value="', $context['code'], '" />
		<input type="hidden" name="u" value="', $context['memID'], '" />
		<input type="hidden" name="sc" value="', $context['session_id'], '" />
	</form>';
}

function template_ask()
{
	global $context, $settings, $options, $txt, $scripturl;

	echo '
	<br />
	<form action="', $scripturl, '?action=reminder;sa=secret2" method="post" accept-charset="', $context['character_set'], '">
		<table border="0" width="440" cellspacing="0" cellpadding="4" class="tborder" align="center">
			<tr class="titlebg">
				<td colspan="2">', $txt[194], '</td>
			</tr><tr class="windowbg">
				<td colspan="2" class="smalltext" style="padding: 2ex;">', $txt['pswd6'], '</td>
			</tr><tr class="windowbg2">
				<td width="45%"><b>', $txt['pswd1'], ':</b></td>
				<td>', $context['secret_question'], '</td>
			</tr><tr class="windowbg2">
				<td width="45%"><b>', $txt['pswd2'], ':</b> </td>
				<td><input type="text" name="secretAnswer" size="22" /></td>
			</tr><tr class="windowbg2">
				<td width="45%">
					<b>', $txt[81], ': </b><br />
					<span class="smalltext">', $txt[596], '</span>
				</td>
				<td valign="top"><input type="password" name="passwrd1" size="22" /></td>
			</tr><tr class="windowbg2">
				<td width="45%"><b>', $txt[82], ': </b></td>
				<td><input type="password" name="passwrd2" size="22" /></td>
			</tr><tr class="windowbg2">
				<td colspan="2" align="right" style="padding: 1ex;"><input type="submit" value="', $txt[10], '" /></td>
			</tr>
		</table>

		<input type="hidden" name="user" value="', $context['remind_user'], '" />
		<input type="hidden" name="sc" value="', $context['session_id'], '" />
	</form>';
}

?>