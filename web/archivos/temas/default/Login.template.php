<?php
// Version: 1.1; Login

// This is just the basic "login" form.
function template_login()
{
	global $context, $settings, $options, $scripturl, $modSettings, $txt;

if (!$context['login_error']) {
echo '<table align="center"><tr align="center"><td align="center">
<form action="/conectandose/" method="post" accept-charset="', $context['character_set'], '" name="frmLogin" id="frmLogin"  onsubmit="hashLoginPassword(this, \'' . $context['session_id'] . '\');"><div class="ultimos_postsa" style="margin:8px;"><div class="box_title" style="width: 378px;"><div class="box_txt ultimos_posts" align="left">', $txt[34], '</div><div class="box_rss"><img alt="" src="', $settings['images_url'], '/blank.gif" width="16px" /></div></div><div class="windowbg"  style="width: 370px; padding:4px;"><center>

<table width="100%"><tr><td width="150px" style="text-align:right;"><b class="size11">', $txt[35], ':</b></td>
<td width="150px" style="text-align:left;"><input tabindex="1" size="30" maxlength="64" style="font-size:10px;width:150px;" id="user" name="user" class="ilogin" type="text" onfocus="foco(this);" onblur="no_foco(this);" /></td></tr>

<tr><td width="150px" style="text-align:right;"><b class="size11">', $txt[36], ':</b></td>
<td width="150px" style="text-align:left;"><input tabindex="2" size="30" maxlength="64" style="font-size:10px;width:150px;" id="passwrd" name="passwrd" class="ilogin" type="password" onfocus="foco(this);" onblur="no_foco(this);" /></td></tr></table><br /><input class="login" type="submit" value="', $txt['login'], '" title="', $txt['login'], '" style="width:150px;" /><div class="hrs"></div><a href="/recuperar-pass/"><b>', $txt[315], '</b></a><br />
', $txt['invited_register_question'], ' <a href="/registrarse/" style="font-weight: bold; color: rgb(255, 0, 0);">', $txt['invited_register_res'], '</a>

<input type="hidden" name="hash_passwrd" value="" /></center></div></div></form>
<script language="JavaScript" type="text/javascript">document.forms.frmLogin.user.focus();</script>

</td></tr></table><div style="clear:both"></div>';

} else {
echo '<div align="center"><div class="box_errors"><div class="box_title" style="width: 388px"><div class="box_txt box_error" align="left">', $txt[106], '</div><div class="box_rss"><img alt="" src="', $settings['images_url'], '/blank.gif" style="width:14px;height:12px;" border="0" /></div></div><div class="windowbg" style="width:380px;padding:4px;"><br />', $context['login_error'], '<br /><br /><input class="login" style="font-size: 11px;" type="submit" title="', $txt['back'] ,'" value="', $txt['back'] ,'" onclick="location.href=\'/ingresar/\'" /> <input class="login" style="font-size: 11px;" type="submit" title="', $txt['go_to_index_page'] ,'" value="', $txt['go_to_index_page'] ,'" onclick="location.href=\'/\'" /><br /><br /></div></div><br /></div><div style="clear:both"></div>';
}

	if (isset($context['login_show_undelete']))
		echo '<b style="color: red;">', $txt['undelete_account'], ':</b>';

}

// Tell a guest to get lost or login!
function template_kick_guest()
{
template_login();
}

// This is for maintenance mode.
function template_maintenance()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	// Display the administrator's message at the top.
	echo '<form action="/conectandose/" method="post" accept-charset="', $context['character_set'], '">
	<div><div class="box_buscador"><div class="box_title" style="width:920px;"><div class="box_txt box_buscadort">', $context['title'], '</div><div class="box_rss"><img alt="" src="', $settings['images_url'], '/blank.gif" style="width:14px;height:12px;" border="0" /></div></div><div style="width: 912px;padding:4px;" class="windowbg" border="0">
', $context['description'], '
<br /><br /></div></div>
<div class="box_buscador"><div class="box_title" style="width: 920px;"><div class="box_txt box_buscadort">Iniciar Sesi&oacute;n como Administrador</div><div class="box_rss"><img alt="" src="', $settings['images_url'], '/blank.gif" style="width:14px;height:12px;" border="0" /></div></div><div style="width: 912px;padding:4px;text-align:left;" class="windowbg" border="0"><font class="size12">
				<table border="0" width="90%" align="center">
					<tr>
						<td><b>', $txt[35], ':</b></td>
						<td><input type="text" name="user" size="15" /></td></tr><tr>
						<td><b>', $txt[36], ':</b></td>
						<td><input type="password" name="passwrd" size="15" /> &nbsp;</td>
					</tr><tr>
						<td align="center" colspan="4"><input type="submit" value="', $txt[34], '" class="login" /></td>
					</tr>
				</table><br /><br />', $modSettings['horizontal'], '</font></div></div>';
}

// This is for the security stuff - makes administrators login every so often.
function template_admin_login()
{
	global $context, $settings, $options, $scripturl, $txt;

	// Since this should redirect to whatever they were doing, send all the get data.
	echo '<table align="center"><tr align="center"><td align="center">
<form action="', $scripturl, $context['get_data'], '" method="post" accept-charset="', $context['character_set'], '" name="frmLogin" id="frmLogin"  onsubmit="hashAdminPassword(this, \'', $context['user']['username'], '\', \'', $context['session_id'], '\');"><div class="ultimos_postsa" style="margin:8px;"><div class="box_title" style="width: 378px;"><div class="box_txt ultimos_posts" align="left">', $txt[34], '</div><div class="box_rss"><img alt="" src="', $settings['images_url'], '/blank.gif" width="16px" /></div></div><div class="windowbg"  style="width: 370px; padding:4px;"><center>

<table width="100%">
<tr><td width="150px" style="text-align:right;"><b class="size11">', $txt[36], ':</b></td>
<td width="150px" style="text-align:left;"><input type="password" name="admin_pass" size="24" maxlength="64" style="font-size:10px;width:150px;" id="passwrd" name="passwrd" class="ilogin" onfocus="foco(this);" onblur="no_foco(this);" /></td></tr></table><br /><input class="login" type="submit" value="', $txt[34], '" title="', $txt[34], '" style="width:150px;" />
';
echo $context['post_data'], '

	<input type="hidden" name="admin_hash_pass" value="" />
<script language="JavaScript" type="text/javascript">document.forms.frmLogin.user.focus();</script>

</td></tr></table><div style="clear:both"></div>';
}

// Activate your account manually?
function template_retry_activate()
{
	global $context, $settings, $options, $txt, $scripturl;

	// Just ask them for their code so they can try it again...
	echo '
		<br />
		<form action="', $scripturl, '?action=activate;u=', $context['member_id'], '" method="post" accept-charset="', $context['character_set'], '">
			<table border="0" width="600" cellpadding="4" cellspacing="0" class="tborder" align="center">
				<tr class="titlebg">
					<td colspan="2">', $context['page_title'], '</td>';

	// You didn't even have an ID?
	if (empty($context['member_id']))
		echo '
				</tr><tr class="windowbg">
					<td align="right" width="40%">', $txt['invalid_activation_username'], ':</td>
					<td><input type="text" name="user" size="30" /></td>';

	echo '
				</tr><tr class="windowbg">
					<td align="right" width="40%">', $txt['invalid_activation_retry'], ':</td>
					<td><input type="text" name="code" size="30" /></td>
				</tr><tr class="windowbg">
					<td colspan="2" align="center" style="padding: 1ex;"><input type="submit" value="', $txt['invalid_activation_submit'], '" /></td>
				</tr>
			</table>
		</form>';
}

// Activate your account manually?
function template_resend()
{
	global $context, $settings, $options, $txt, $scripturl;

	// Just ask them for their code so they can try it again...
	echo '
		<br />
		<form action="', $scripturl, '?action=activate;sa=resend" method="post" accept-charset="', $context['character_set'], '">
			<table border="0" width="600" cellpadding="4" cellspacing="0" class="tborder" align="center">
				<tr class="titlebg">
					<td colspan="2">', $context['page_title'], '</td>
				</tr><tr class="windowbg">
					<td align="right" width="40%">', $txt['invalid_activation_username'], ':</td>
					<td><input type="text" name="user" size="40" value="', $context['default_username'], '" /></td>
				</tr><tr class="windowbg">
					<td colspan="2" style="padding-top: 3ex; padding-left: 3ex;">', $txt['invalid_activation_new'], '</td>
				</tr><tr class="windowbg">
					<td align="right" width="40%">', $txt['invalid_activation_new_email'], ':</td>
					<td><input type="text" name="new_email" size="40" /></td>
				</tr><tr class="windowbg">
					<td align="right" width="40%">', $txt['invalid_activation_password'], ':</td>
					<td><input type="password" name="passwd" size="30" /></td>
				</tr><tr class="windowbg">';

	if ($context['can_activate'])
		echo '
					<td colspan="2" style="padding-top: 3ex; padding-left: 3ex;">', $txt['invalid_activation_known'], '</td>
				</tr><tr class="windowbg">
					<td align="right" width="40%">', $txt['invalid_activation_retry'], ':</td>
					<td><input type="text" name="code" size="30" /></td>
				</tr><tr class="windowbg">';

	echo '
					<td colspan="2" align="center" style="padding: 1ex;"><input type="submit" value="', $txt['invalid_activation_resend'], '" /></td>
				</tr>
			</table>
		</form>';
}

?>