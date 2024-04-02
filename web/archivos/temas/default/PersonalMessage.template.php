<?php
if (!defined('SMF'))
	die('Hacking attempt...');
	
function template_pm_above()
{
	global $context, $settings, $options, $txt, $modSettings;


	echo '<div style="width: 160px; float: left;"><div style="margin-bottom: 8px;" class="img_aletat"><div class="box_title" style="width: 158px;">';
	// Loop through every main area - giving a nice section heading.
	foreach ($context['pm_areas'] as $section)
	{
		echo '<div class="box_txt img_aletat">', $section['title'], ' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div><div class="box_rss"><img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 16px; height: 16px;" border="0"></div></div><div class="windowbg" style="padding: 4px; font-size: 13px; width: 150px;">';
		// Each sub area.
		foreach ($section['areas'] as $i => $area)
		{
				if ($i == $context['pm_area'])
					echo $area['link'], (empty($area['unread_messages']) ? '' : ' (' . $area['unread_messages'] . ')');
				else
					echo $area['link'], (empty($area['unread_messages']) ? '' : ' (' . $area['unread_messages'] . ')');

		}
	}
	echo '</div></div><div style="margin-bottom: 8px;" class="img_aletat"><div class="box_title" style="width: 158px;"><div class="box_txt img_aletat">Publicidad</div><div class="box_rss"><img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 16px; height: 16px;" border="0"></div></div><div class="windowbg" style="padding: 4px; font-size: 13px; width: 150px;"><p align="center">', $modSettings['vertical'], '</p></div></div></div> ';
}

function template_pm_below(){}

function template_folder()
{
	global $context, $settings, $options, $scripturl, $modSettings, $txt, $ID_MEMBER, $db_prefix;

if(!isset($context['sl-singlepm'])) {	
echo '<div style="margin-left:8px;width:757px;float:left;">';

if ($context['show_delete']) {
echo '<table class="linksList" style="width:754px;"><thead align="center"><tr><th>&nbsp;</th><th>', $txt[319], '</th><th>', ($context['from_or_to'] == 'from' ? $txt[318] : $txt[324]), '</th><th>', ($context['from_or_to'] == 'from' ? $txt[317] : $txt['317b']), '</th></tr></thead><tbody>';

$next_alternate = false;
while ($message = $context['get_pmessage']()) {
echo '<tr>
<td>
<a title="Eliminar mensaje" href="/mensajes/';
if(empty($_REQUEST['f'])|| $_REQUEST['f'] == 'inbox'){
echo 'recibidos';
} elseif($_REQUEST['f'] == 'outbox') {
echo 'enviados';
}
echo '/eliminar-', $message['id'], '"  onclick="if (!confirm(\'\xbfEstas seguro que desea eliminar este mensaje?\')) return false;">
<img width="10px" src="', $settings['images_url'], '/eliminar.gif"  alt="" /></a>
</td>
<td><a href="', $message['p'], '" title="', $message['subject'], '">', $message['subject'], '</a></td><td>', ($context['from_or_to'] == 'from' ? $message['member']['link'] : (empty($message['recipients']['to']) ? '' : implode(', ', $message['recipients']['to']))), '</td><td><span class="size11">', $message['time'], '</span></td>

</tr>';
		$next_alternate = $message['alternate'];
	}

	echo '</tbody></table>
';

} else {
	echo '<div class="noesta" style="width:754px;">', $txt[151];
if(empty($_REQUEST['f']) || $_REQUEST['f'] == 'inbox'){
echo 'recibidos';
} elseif($_REQUEST['f'] == 'outbox') {
echo 'enviados';
}
echo '...';
echo '</div>';
}
}
	if ((isset($context['sl-singlepm']) || (isset($modSettings['enableSinglePM']) && $modSettings['enableSinglePM'] ==0) || !isset($modSettings['enableSinglePM']))) 
	{
		while ($message = $context['get_pmessage']())
		{
$windowcss = $message['alternate'] == 0 ? 'windowbg' : 'windowbg2';

echo ' <div style="margin-left:8px;width:757px;float:left;"><a name="mp_', $message['id'], '"></a>

<div class="box_757"><div class="box_title" style="width:752px;"><div class="box_txt box_757-34"><center>', $message['subject'], '</center></div><div class="box_rss"><img alt="" src="', $settings['images_url'], '/blank.gif" style="width: 16px; height: 16px;" border="0" /></div></div>
                 <table class="windowbg" cellpadding="3" cellspacing="0" border="0" style="text-align:left;width:754px;">
';
echo '				  <tr>
                        <td align="left" style="width:50px;text-align:left;" valign="top"><b>Por:</b></td>
				        <td style="width:691px;text-align:left;" valign="top"><a href="/perfil/', $message['member']['username'], '">', $message['member']['name'], '</a></td>
                  </tr>
				  <tr>
					    <td align="left" style="width:50px;text-align:left;" valign="top"><b>Enviado:</b></td>

					    <td style="width:691px;text-align:left;" valign="top">', $message['time'], '</td>
				  </tr>
				  <tr>
						<td align="left" style="width:50px;text-align:left;" valign="top"><b>Asunto:</b></td>
						<td style="width:691px;text-align:left;" valign="top">', $message['subject'], '</td>
                  </tr>
';
echo '				  <tr>  <td align="left" style="width:50px;text-align:left;" valign="top"><b>Mensaje:</b></td>

						<td style="width:691px;text-align:left;" valign="top"><div class="personalmessage"><br /><br />', $message['body'], '</div></td>
					</tr></table>';
echo '<p align="center"><input class="login" style="font-size: 11px;" value="Eliminar MP" title="Eliminar MP" onclick="if (!confirm(\'\xbfEstas seguro que desea eliminar este mensaje?\')) return false; location.href=\'/mensajes/recibidos/eliminar-', $message['id'], '\'" type="button" />&nbsp;<input class="login" style="font-size: 11px;" value="Responder MP" title="Responder MP" onclick="location.href=\'/mensajes/responder/a/', $message['member']['name'], '/id/', $message['id'], '\'" type="button" /></p>';

		}

	}

	echo '</div></div><div style="clear:both"></div>';
}

function template_search(){}
function template_search_results(){}	

function template_send()
{
	global $context, $settings, $options, $scripturl, $modSettings, $txt;

echo '<div style="margin-left:8px;width:757px;float:left;"><script language="JavaScript" type="text/javascript">
function errorrojo3(to,subject,cuerpo_comment){
if(to == \'' . $context['user']['name'] . '\'){
document.getElementById(\'errors3\').innerHTML=\'<br /><font class="size10" style="color: red;">No te puedes autoenviar mensaje.</font>\'; return false;}
if(to == \'"' . $context['user']['name'] . '"\'){
document.getElementById(\'errors7\').innerHTML=\'<br /><font class="size10" style="color: red;">No te puedes autoenviar mensaje.</font>\'; return false;}
if(to == \'\'){
document.getElementById(\'errors4\').innerHTML=\'<br /><font class="size10" style="color: red;">Debes escribir el destinario del mensaje.</font>\'; return false;}
if(titulo == \'\'){
document.getElementById(\'errors5\').innerHTML=\'<br /><font class="size10" style="color: red;">Debes escribir un asunto.</font>\'; return false;}
if(cuerpo_comment == \'\'){
document.getElementById(\'errors6\').innerHTML=\'<br /><font class="size10" style="color: red;">Debes escribir un mensaje.</font>\'; return false;}}</script>

<div class="box_757"><div class="box_title" style="width: 752px;"><div class="box_txt box_757-34"><center>', $txt[321], '</center></div><div class="box_rss"><img src="', $settings['images_url'], '/blank.gif" style="width: 16px; height: 16px;" border="0"></div></div></div>
<form action="/mensajes/enviar/" method="post" accept-charset="', $context['character_set'], '" name="postmodify" id="postmodify" >
<div class="windowbg" style="width: 752px;">
<table cellpadding="3" cellspacing="1" ><tr><td><font class="size11"><b>De:</b>&nbsp;</font><br /><input style="background-color: #EBEBE4; color: #B0B1B3;" disabled="disabled" type="text" onfocus="foco(this);" onblur="no_foco(this);" value="' . $context['user']['name'] . '" size="40" /><br />
<font class="size11"><b>', $txt[150], ':</b>&nbsp;</font><br />
<input type="text" onfocus="foco(this);" onblur="no_foco(this);" name="to" id="to" value="', $context['to'], '" tabindex="', $context['tabindex']++, '" size="40" /><label id="errors3"></label><label id="errors7"></label><label id="errors4"></label><br /><font class="size11"><b>', $txt[70], ':</b>&nbsp;</font><br /><input type="text" onfocus="foco(this);" onblur="no_foco(this);" name="subject" value="', $context['subject'], '" tabindex="', $context['tabindex']++, '" size="40" maxlength="50" /><label id="errors5"></label><br />';

	if ($context['visual_verification'])
	{
		echo '
							<tr>
								<td align="right" valign="top">
									<b>', $txt['pm_visual_verification_label'], ':</b>
								</td>
								<td>';
		if ($context['use_graphic_library'])
			echo '
									<img src="', $context['verificiation_image_href'], '" alt="', $txt['pm_visual_verification_desc'], '" /><br />';
		else
			echo '
									<img src="', $context['verificiation_image_href'], ';letter=1" alt="', $txt['pm_visual_verification_desc'], '" />
									<img src="', $context['verificiation_image_href'], ';letter=2" alt="', $txt['pm_visual_verification_desc'], '" />
									<img src="', $context['verificiation_image_href'], ';letter=3" alt="', $txt['pm_visual_verification_desc'], '" />
									<img src="', $context['verificiation_image_href'], ';letter=4" alt="', $txt['pm_visual_verification_desc'], '" />
									<img src="', $context['verificiation_image_href'], ';letter=5" alt="', $txt['pm_visual_verification_desc'], '" /><br />';
		echo '
									<a href="', $context['verificiation_image_href'], ';sound" onclick="return reqWin(this.href, 400, 120);">', $txt['pm_visual_verification_listen'], '</a><br /><br />
									<input type="text" name="visual_verification_code" size="30" tabindex="', $context['tabindex']++, '" />
									<div class="smalltext">', $txt['pm_visual_verification_desc'], '</div>
								</td>
							</tr>';
	}

	theme_quickreply_box();
$context['copy_to_outbox'] = 1;

if ($context['allow_admin']){
echo '<font class="size11"><b>Opciones:</b></font>';
echo'<br><a href="javascript:void(0);" onclick="replaceText(\'Hola!\n\nLe informamos que usted esta corrumpiendo el protocolo escribiendo un texto entero en MAYUSCULA\n\nPara acceder al protocolo, presiona [url=/protocolo/]este enlace[/url].\n\nMuchas gracias por entender!\', document.forms.postmodify.message); return false;"><img src="', $settings['images_url'], '/edicion.gif" align="bottom" alt="Mayusculas" title="Mayusculas" /></a> | <a href="javascript:void(0);" onclick="replaceText(\'Hola!\nLamento contarte que tu post titulado [b]TITULO DEL POST[/b] ha sido eliminado.\n Causa: [b]CAUSA[/b], ideas aca: ' , $boardurl, '/contactanos/\nPara acceder al protocolo, presiona este [url=/protocolo/]enlace[/url].\nMuchas gracias por entender!\', document.forms.postmodify.message); return false;"><img src="', $settings['images_url'], '/eliminar.gif" align="bottom" alt="Post eliminado" title="Post eliminado" /></a>';
}
echo '<center><input class="login" type="submit" value="', $txt[148], '" tabindex="', $context['tabindex']++, '" onclick="return errorrojo3(this.form.to.value, this.form.subject.value, this.form.cuerpo_comment.value);"/></center><input type="hidden" name="sc" value="', $context['session_id'], '" /><input type="hidden" name="usuarien" value="',$context['user']['id'], '" /></td></tr></table></div></form></div><div style="clear:both"></div>';

}

function template_ask_delete() {
	global $context, $settings, $options, $scripturl, $modSettings, $txt;

	echo '
		<table border="0" width="80%" cellpadding="4" cellspacing="1" class="bordercolor" align="center">
			<tr class="titlebg">
				<td>', ($context['delete_all'] ? $txt[411] : $txt[412]), '</td>
			</tr>
			<tr>
				<td class="windowbg">
					', $txt[413], '<br />
					<br />
					<b><a href="', $scripturl, '?action=pm;sa=removeall2;f=', $context['folder'], ';', $context['current_label_id'] != -1 ? ';l=' . $context['current_label_id'] : '', ';sesc=', $context['session_id'], '">', $txt[163], '</a> - <a href="javascript:history.go(-1);">', $txt[164], '</a></b>
				</td>
			</tr>
		</table>';
}
function template_labels()
{
	global $context, $settings, $options, $scripturl, $txt;

	echo '
<form action="/?action=pm;sa=manlabels;sesc=', $context['session_id'], '" method="post" accept-charset="', $context['character_set'], '">
		<table width="100%" cellpadding="3" cellspacing="0" border="0">	
		<tr>
		<td width="100%" class="titulo_a">&nbsp;</td>
		<td width="100%" class="titulo_b"><center>', $txt['pm_label_add_new'], '</center></td>
		<td width="100%" class="titulo_c">&nbsp;</td>
		</tr></table>	
		<table width="100%" cellpadding="4" cellspacing="0" border="0" align="center" class="windowbg2">
			<tr class="windowbg2">
				<td align="right" width="40%">
					<b class="size11">', $txt['pm_label_name'], ':</b>
				</td>
				<td align="left">
					<input type="text" name="label" value="" size="30" maxlength="20" />
				</td>
			</tr>
			<tr class="windowbg2">
				<td colspan="2" align="center">
					<input class="login" type="submit" name="add" value="', $txt['pm_label_add_new'], '" style="font-weight: normal;" />
				</td>
			</tr>
		</table>
	</form>
<form action="/?action=pm;sa=manlabels;sesc=', $context['session_id'], '" method="post" accept-charset="', $context['character_set'], '" style="margin-top: 8px;">
	<table width="100%" cellpadding="3" cellspacing="0" border="0">	
		<tr>
		<td width="100%" class="titulo_a">&nbsp;</td>
		<td width="100%" class="titulo_b"><center>', $txt['pm_manage_labels'], '</center></td>
		<td width="100%" class="titulo_c">&nbsp;</td>
		</tr></table>	
		<table width="100%" class="windowbg">
			<tr class="windowbg2">
				<td colspan="2" style="padding: 1ex;"><center><span class="smalltext">', $txt['pm_labels_desc'], '</span></center></td>
			</tr>
			<tr >
				<td colspan="2" style="background-color: #BBC4AF; color: #FFFFFF;">
					<div style="float: right; width: 4%; text-align: center;"><input type="checkbox" class="check" onclick="invertAll(this, this.form);" /></div>
					', $txt['pm_label_name'], ':
				</td>
			</tr>';

		$alternate = true;
		foreach ($context['labels'] as $label)
		{
			if ($label['id'] != -1)
			{
				echo '
				<tr class="', $alternate ? 'windowbg2' : 'windowbg', '">
					<td>
						<input type="text" name="label_name[', $label['id'], ']" value="', $label['name'], '" size="30" maxlength="30" />
					</td>
					<td width="4%" align="center"><input type="checkbox" class="check" name="delete_label[', $label['id'], ']" /></td>
				</tr>';
				$alternate = !$alternate;
			}
		}

		echo '
			<tr>
				<td align="center" colspan="2">
				<br>	<input class="login" type="submit" name="save" value="Guardar Cambio" style="font-weight: normal;" />
					<input class="login" type="submit" name="delete" value="', $txt['quickmod_delete_selected'], '" style="font-weight: normal;" onclick="return confirm(\'', $txt['pm_labels_delete'], '\');" />
				</td>
			</tr>';
	
	echo '
		</table>
		<input type="hidden" name="sc" value="', $context['session_id'], '" />
	</form>';
}
function template_prune(){}
function template_report_message(){}
function template_report_message_complete(){}
function template_quickreply_box()
{
	global $context, $settings, $options, $txt, $modSettings;
	if (!empty($_REQUEST['pmsg']))
	{
$request = db_query("
SELECT *
FROM ({$db_prefix}personal_messages) 
WHERE ID_PM = ".$_REQUEST['pmsg']."
", __FILE__, __LINE__);
while ($row = mysqli_fetch_assoc($request))
	{
		censorText($row['body']);
		$row['body'] = trim(un_htmlspecialchars(strip_tags(strtr(parse_bbc($row['body'], false, $ID_MSG), array('<br />' => "\n", '</div>' => "\n", '</li>' => "\n", '&#91;' => '[', '&#93;' => ']')))));
		$comentario = $row['body'];
		$fecha = $row['msgtime'];
		$nombre = $row['fromName'];
 	 
	}
	mysqli_free_result($request);}
	
	echo '<b class="size11">Mensaje Privado:</b><br />';

$mesesano2 = array("1","2","3","4","5","6","7","8","9","10","11","12") ;
$diames2 = date(j,$fecha); $mesano2 = date(n,$fecha) - 1 ; $ano2 = date(Y,$fecha);
$seg2=date(s,$fecha); $hora2=date(H,$fecha); $min2=date(i,$fecha);
$fecha2="$diames2.$mesesano2[$mesano2].$ano2 a las $hora2:$min2:$seg2";

echo '<textarea style="height:160px;width:615px;" onfocus="foco(this);" onblur="no_foco(this);" id="cuerpo_comment" name="message" class="markItUpEditor" tabindex="', $context['tabindex']++, '">';
if (!empty($_REQUEST['pmsg']))
{echo'


El '.$fecha2.', '.$nombre.' Escribi&oacute;:
> '.str_replace("\n", "\n> ", $comentario);}
echo '</textarea><label id="errors6"></label>';

if (!empty($context['smileys']['postform']))
{
foreach ($context['smileys']['postform'] as $smiley_row)
{
foreach ($smiley_row['smileys'] as $smiley)
echo '<a href="javascript:void(0);" onclick="replaceText(\' ', $smiley['code'], '\', document.forms.postmodify.message); return false;"><img src="', $settings['smileys_url'], '/', $smiley['filename'], '" align="bottom" alt="', $smiley['description'], '" title="', $smiley['description'], '" /></a> ';
		}
	// If the smileys popup is to be shown... show it!
		if (!empty($context['smileys']['popup']))
		echo '<script type="text/javascript">function openpopup(){var winpops=window.open("/emoticones/","","width=255px,height=500px,scrollbars");}</script> <a href="javascript:openpopup()">[', $txt['more_smileys'], ']</a><br />';
	}


}
?>