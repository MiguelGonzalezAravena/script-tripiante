<?php
// Version: 1.1; Display

function template_main()
{
	global $topic, $context, $settings, $options, $txt, $scripturl, $modSettings;
	global $db_prefix, $user_info, $board, $query_this_board, $func, $boardurl;

$cantidad++;
echo '<script type="text/javascript">function errorrojo2(causa){if(causa == \'\'){document.getElementById(\'errors\').innerHTML=\'Es necesaria la causa de la eliminaci&oacute;n.\'; return false;}}</script><a name="arriba"></a>';


$request = mysql_query("SELECT * FROM {$db_user}denunciations AS den WHERE den.ID_TOPIC = " . $_GET['topic'] . " AND den.TYPE = 'post'");
$context['contando'] = mysql_num_rows($request);

if($context['contando'] > 5 && empty($context['allow_admin'])) {
fatal_error('Post eliminado por acumulaci&oacute;n de denuncias, se encuentra en proceso de revisi&oacute;n.', false);
} elseif($context['contando'] > 5 && $context['allow_admin']) {
echo '<p align="center" style="color: #FF0000;">Verificar Post - Tiene ', $context['contando'], ' denuncias</p>';
} elseif($context['user']['is_guest'] && $context['can_view_post'] == '1') {
fatal_error('Este post es privado, para verlo debes autentificarte.', false);
}

/* Aqu&#237; puedes empezar a modificar el Dise&#241;o del post */
while ($message = $context['get_message']()) {
$firma = str_replace('if(this.width >720) {this.width=720}','if(this.width > 375) {this.width=375}', $message['member']['signature']);
echo '<div style="margin-bottom:8px;"><div class="box_140" style="float:left; margin-right:8px;">
<div class="box_title" style="width: 138px;"><div class="box_txt box_140-34">Publicado por:</div>
<div class="box_rss"><div class="icon_img"><a href="/rss/post-user/', $message['member']['username'], '"><img alt="" src="', $settings['images_url']. '/icons/tpbig-v1-iconos.gif?v3.2.3" style="cursor:pointer;margin-top:-352px;display:inline;" /></a></div></div></div><div class="windowbg" style="width: 130px; padding: 4px;">
<center>';

if (!empty($settings['show_user_images']) && empty($options['show_no_avatars']) && !empty($message['member']['avatar']['name'])) {
echo '<div class="fondoavatar" style="overflow:hidden;width:130px;" align="center"><a href="/perfil/', $message['member']['username'], '" title="Ver Perfil"><img src="', $message['member']['avatar']['name'], '" width="105" alt="" class="avatar" border="0" onerror="error_avatar(this)" /></a><br /><span class="mp">', $message['member']['blurb'], '</span></div></center><br/>';
} else {
echo '<div class="fondoavatar" style="overflow: auto; width: 130px;" align="center"><a href="/perfil/', $message['member']['username'], '" title="Ver Perfil"><img src="', $boardurl, '/avatar.gif" alt="Sin Avatar" border="0"></a><br><span class="mp">', $message['member']['blurb'], '</span></div></center><br>';
}
echo '<b><a href="/perfil/', $message['member']['username'], '" style="font-size:14px;color:#FF6600;">', $message['member']['name'], '</a></b><br />';
echo '<b style="font-size:12px;color:#747474;text-shadow: #6A5645 0px 1px 1px;">', (!empty($message['member']['group']) ? $message['member']['group'] : $message['member']['post_group']), '</b><br />';
echo '<span title="', (!empty($message['member']['group']) ? $message['member']['group'] : $message['member']['post_group']), '">', $message['member']['group_stars'], '</span>';
if (!empty($settings['show_gender']) && $message['member']['gender']['image'] != '') {
echo ' <span title="', $message['member']['gender']['name'], '">', $message['member']['gender']['image'], '</span>';
}
if($message['member']['title']) {
echo ' <img alt="" title="'. ssi_pais($message['member']['title'])  . '" src="', $settings['images_url'], '/icons/banderas/'.$message['member']['title'].'.gif" />';
} else {
echo ' <img alt="" title="" src="', $settings['images_url'], '/icons/banderas/ot.gif">';
}
if (!empty($message['member']['estado_icon'])) {
echo ' <img title="Estado: ', ssi_estado_icon($message['member']['estado_icon']), '" src="', $settings['images_url'], '/icons/estado/', $message['member']['estado_icon'], '.gif" alt="" />';
}
echo '<br /><br />';	

if ($settings['show_profile_buttons']) {
if ($context['can_send_pm']) {
echo '<div style="margin-bottom:2px;"><span style="font-size:12px;"><img alt="" src="' . $settings['images_url'] . '/icons/mensaje_para.gif" border="0" /> <a href="/mensajes/a/', $message['member']['username'], '" title="Enviar mensaje">Enviar mensaje</a></span></div>';
}
if ($context['user']['is_logged'])	 {
echo '<div style="margin-bottom:4px;"><span class="icons fot2" style="font-size:12px;"><a href="/imagenes/', $message['member']['name'], '" title="Sus im&aacute;genes"> Sus im&aacute;genes</a></span></div>';
}
}
echo '<br /><div class="hrs"></div>';
			
$iduser = $message['member']['id'];
$request = mysql_query("SELECT * FROM {$db_prefix}comments WHERE ID_MEMBER = " . $iduser);
$context['comentuser'] = mysql_num_rows($request);

echo '<br /><div class="fondoavatar" style="overflow:hidden;width:130px;"><b style="color:#FE8F47;text-shadow: #6A5645 0px 1px 1px;">PUNTOS:</b> <b><span id="cant_pts_post">', $message['member']['moneyBank'], '</span></b><br />
<b style="color:#FE8F47;text-shadow: #6A5645 0px 1px 1px;">POST:</b> <b><a href="/user-post/', $message['member']['name'], '">', $message['member']['topics'], '</a></b><br />
<b style="color:#FE8F47;text-shadow: #6A5645 0px 1px 1px;">COMENTARIOS:</b> <b><a href="/user-comment/', $message['member']['name'], '">', $context['comentuser'], '</a></b></div><br />';
if ($context['user']['is_guest']) {
echo '<div class="hrs"></div><div class="size11"><br><a href="/registrarse/" target="_blank" rel="nofollow">&iexcl;REG&Iacute;STRATE!</a> es <b>GRATIS</b></div><center>', $modSettings['vertical'], '</center></p>';
}
echo '<span class="size11"></span></div></div>';

 echo '<div class="box_780" style="float:left;">
<div class="box_title" style="width: 772px;"><div class="box_txt box_780-34"><center>', $context['subject'], '</center></div><div class="box_rss"><div class="icon_img"><a href="/imprimir/post/'  .$topic.  '"><img alt="" src="', $settings['images_url'], '/icons/tpbig-v1-iconos.gif?v3.2.3" style="cursor:pointer;margin-top:-640px;display:inline;" /></a></div></div></div><div class="windowbg" style="width:772px;" id="post_' . $message['id'] . '">
<div class="post-contenido" property="dc:content">';
if($context['user']['is_guest']) {
echo '<div align="center" style="-moz-border-radius: 5px;-webkit-border-radius:5px;display:block;margin-bottom:25px;margin-top:10px;padding:2px;border: solid 1px #D5CCC3;background:#FFF;">', $modSettings['horizontal'], '<br /><a href="' . $boardurl . '/registrarse/" style="font-size:12px;color:#FFB600;margin-bottom:3px;"><b>REG&Iacute;STRATE GRATIS Y ELIMINA ESTA PUBLICIDAD, ADEM&Aacute;S TENDR&Aacute;S ACCESO A TODOS LOS POSTS Y FUNCIONES</b></a></div>
';
}
echo $message['body'];
if($context['user']['is_guest']) {
echo '<div align="center" style="-moz-border-radius: 5px;-webkit-border-radius:5px;display:block;margin-bottom:10px;margin-top:25px;padding:2px;border: solid 1px #D5CCC3;background:#FFF;">', $modSettings['horizontal'], '<br /><a href="' . $boardurl . '/registrarse/" style="font-size:12px;color:#FFB600;margin-bottom:3px;"><b>REG&Iacute;STRATE GRATIS Y ELIMINA ESTA PUBLICIDAD, ADEM&Aacute;S TENDR&Aacute;S ACCESO A TODOS LOS POSTS Y FUNCIONES</b></a><br /></div>';
}

echo '</div></div>
<!-- info del post --><div style="width:780px;margin-top:8px;"><div style="width:380px;float:left;margin-right:8px;#margin-right:8px;_margin-right:6px;">

<div class="box_390" style="width:380px;">
<div class="box_title" style="width:378px;"><div class="box_txt box_390-34">Opciones</div>
<div class="box_rss"><span id="cargando_opciones" style="display:none;"><img alt="" src="' . $settings['images_url'] . '/icons/cargando.gif" style="width:16px;height:16px;" border="0" /></span><span id="cargando_opciones2"><img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width:16px;height:16px;" border="0" /></span></div></div><div class="windowbg" style="width:370px;padding:4px;"><form action="/eliminar-post/', $context['current_topic'], '/" method="post" accept-charset="', $context['character_set'], '" style="margin:0px;padding:0px;" name="causa" id="causa"><span class="size11">
'; 

if($context['allow_admin'] || $iduser == $context['user']['id']) {
echo '<input class="login" style="font-size: 11px;" value="Editar post" title="Editar post" onclick="location.href=\'/editar-post/id-', $context['current_topic'], '\'" type="button"> <input class="login" style="font-size: 11px;" value="Eliminar post" title="Eliminar post" onclick="if (!confirm(\'\xbfEstas seguro que desea eliminar este post?\')) return false; return errorrojo2(this.form.causa.value);" type="submit"> <input type="text" id="causa" name="causa" maxlength="50" size="30"><center><label id="errors"></label></center><div class="hrs"></div>';
} else {
if ($message['can_remove']) {
echo '<input class="login" style="font-size: 11px;" value="Editar post" title="Editar post" onclick="location.href=\'/editar-post/id-', $context['current_topic'], '\'" type="button"> <input class="login" style="font-size: 11px;" value="Eliminar post" title="Eliminar post" onclick="if (!confirm(\'\xbfEstas seguro que desea eliminar este post?\')) return false; location.href=\'/eliminar-post/', $context['current_topic'], '/\'" type="button"><div class="hrs"></div>';
}
}
if($iduser == $context['user']['id'] || $context['allow_admin']) {
echo '</form>';
}
if($context['Conocido'] || $context['Vecino'] || $context['Amigo'] || $context['Familiar'] || $context['Casero'] || $context['allow_admin']) {
echo '<span id="span_opciones1" class="size10"><b class="size11">Dar puntos:</b> ';

$puntos	=	$context['user']['money'];
if($context['user']['money'] > 0) {
for($o = 1; $o <= $puntos;$o++) {
echo '<a href="#" onclick="votar_post(\''.$context['current_topic'].'\',\''.$o.'\'); return false;" title="Dar '.$o.' puntos">'.$o.'</a>'; $contarpuntos++;
if($contarpuntos<$puntos) {
echo ' - ';
}
}
echo ' <i><strong>de ', $context['user']['money'], ' disp.</strong></i></span>
';
} else {
echo 'A las <u style="cursor:default;" title="Horario Chileno">22:00 HS</u> se recargan tus puntos.';
}
} else {
echo '<b class="size11"><center>Usuarios no registrados y <span title="Primer rango">turistas</span> no puede calificar.</center></b>';
}
echo '<div class="hrs"></div>';
if($context['user']['is_logged']) {
echo '<center><span id="span_opciones2" style="text-align: center; display: block;"><a class="icons agregar_favoritos"  href="#" onclick="add_favoritos(\'', $context['current_topic'], '\'); return false;" >Agregar a Favoritos</a> | <a  class="icons denunciar_post" title="Denunciar post" href="/denuncia/post-', $context['current_topic'], '"/>Denunciar post</a> | <a class="icons recomendar_post" href="/enviar-a-amigo/', $context['current_topic'], '">Enviar a un amigo</a></span></center>';
} else {
echo '<a class="icons recomendar_post" href="/enviar-a-amigo/', $context['current_topic'], '">Enviar a un amigo</a>';
}
echo '<div class="hrs"></div><b class="size13">Posts relacionados:</b><br />';
if(!empty($context['posts10'])) {
foreach ($context['posts10'] as $posts10) {
echo '<div class="entry_item"><div class="icon"><img alt="" title="'.$posts10['bname'].'" src="', $settings['images_url'], '/post/icono_'.$posts10['idb'].'.gif" /></div><div class="text_container">

<a rel="dc:relation" href="/post/'.$posts10['id'].'/'.$posts10['description'].'/'.ssi_amigable($posts10['subject']).'.html" title="'.$posts10['subject'].'" target="_self" >'.$posts10['subject'].'</a>
</div></div>

<div style="clear: left;"></div>

';
}
} else {
echo 'No hay posts relacionados.';
}
			
echo '</span></div></div></div><div style="width:386px;float:left;"><div class="box_390"><div class="box_title" style="width:384px;"><div class="box_txt box_390-34">Informaci&oacute;n del Post</div><div class="box_rss"><img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width:16px;height:16px;" border="0" /></div></div>
<div class="windowbg" style="width: 376px; padding: 4px;"><span class="size11"><center><span class="icons visitas">&nbsp;', $context['num_views'], '&nbsp;visitas</span><span class="icons fav"><span id="cant_favs_post">', $context['fav1'], '</span>&nbsp;favoritos</span><span class="icons puntos"><span id="cant_pts_post_dos">'.$context['points-post'].'</span> puntos</span></center><div class="hrs"></div>

<b>Creado el:</b>&nbsp;<span property="dc:date" content="', $message['time'], '">', $message['time'], '</span><div class="hrs"></div>
<b>Categor&iacute;a:</b> <a href="/categoria/', $message['board']['description'], '" title="', $message['board']['name'], '">', $message['board']['name'], '</a><div class="hrs"></div><b>Tags:</b> ';
if ($context['topic_tags']) {
foreach ($context['topic_tags'] as $i => $tags) {
echo '<a href="/tags/' . $tags['tag'] . '" title="' . $tags['tag'] . '">' . $tags['tag'] . '</a>';
$count	=	count($context['topic_tags']);
$contar++;
if($contar<$count)
echo ' - ';
}
} else {
echo 'Este post no tiene tags';
}
$link = $boardurl . $_SERVER['REQUEST_URI'];
echo '<div class="hrs"></div><div style="float:left; margin-right:4px;"><b>Agregar a:</b></div>

<div class="icon_img" style="float:left; margin-right:4px;"><a href="http://technorati.com/faves/?add=' . $link . '" rel="nofollow" target="_blank" title="Agregar a: Technorati"><img alt="" src="' . $settings['images_url'] . '/icons/tpbig-v1-iconos.gif?v3.2.3" style="cursor:pointer;margin-top:-391px; display:inline;" /></a></div>

<div class="icon_img" style="float: left; margin-right:4px;"><a href="http://www.facebook.com/share.php?u=' . $link . '" rel="nofollow" target="_blank" title="Agregar a: Facebook"><img alt="" src="' . $settings['images_url'] . '/icons/tpbig-v1-iconos.gif?v3.2.3" style="cursor:pointer;margin-top:-411px;display:inline;" /></a></div>

<div class="icon_img" style="float: left; margin-right:4px;"><a href="http://twitter.com/home?status=Les recomiendo este post: ' . $link . '" rel="nofollow" target="_blank" title="Agregar a: Twitter"><img alt="" src="' . $settings['images_url'] . '/icons/tpbig-v1-iconos.gif?v3.2.3" style="cursor:pointer;margin-top:-472px;display:inline;" /></a></div>

<div class="icon_img" style="float: left; margin-right:4px;"><a href="http://del.icio.us/post?url=' . $link . '" rel="nofollow" target="_blank" title="Agregar a: Del.icio"><img alt="" src="' . $settings['images_url'] . '/icons/tpbig-v1-iconos.gif?v3.2.3" style="cursor:pointer;margin-top:-432px;display:inline;" /></a></div>

<div class="icon_img"><a href="http://digg.com/submit?phase=2&url=' . $link . '" rel="nofollow" target="_blank" title="Agregar a: Digg"><img alt="" src="' . $settings['images_url'] . '/icons/tpbig-v1-iconos.gif?v3.2.3" style="cursor:pointer; margin-top:-453px;display:inline;" /></a></div></span></div></div>';

if (!empty($message['member']['signature']) && empty($options['show_no_signatures'])) {
echo '<div class="box_390" style="margin-top:8px;"><div class="box_title" style="width:384px;"><div class="box_txt box_390-34">Firma</div><div class="box_rss"><img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width:16px;height:16px;" border="0" /></div></div><div class="windowbg" style="width: 376px; padding: 4px;">'.censorText($firma).'</div></div>';
}
echo '</div></div><!-- fin info del post -->';

/* Comentarios */
echo '<!-- comentarios --><div style="margin-bottom:8px;">
<div class="box_780" style="float:left;margin-bottom:8px;margin-top:8px;">';
if($message['can_remove']) {
echo '<form action="/comentario/eliminar/" method="post" accept-charset="', $context['character_set'], '" name="coments" id="coments">';
}
echo '<div class="box_title" style="width: 772px;"><div class="box_txt box_780-34">Comentarios</div>
<div class="box_rss"><div class="icon_img"><a href="/rss/post-comment/', $context['current_topic'], '"><img alt="" src="' . $settings['images_url'] . '/icons/tpbig-v1-iconos.gif?v3.2.3" style="cursor:pointer;margin-top:-352px;display:inline;" /></a></div></div></div><div class="windowbg" style="width: 764px; padding: 4px;">';
if($context['haycom']) {
foreach ($context['comentarios'] AS $coment) {
echo '<div id="cmt_'.$coment['id'].'"><span class="size12">';
if ($message['can_remove'] || $context['allow_admin']) {
echo '<input type="checkbox" name="campos['.$coment['id'].']">';
}
echo ' <a onclick="citar_comment('.$coment['id'].')" href="javascript:void(0)">#'.$cantidad++.'</a> <b id="autor_cmnt_'.$coment['id'].'" user_comment="'.$coment['nomuser'].'" text_comment=\''.$coment['comentario2'].'\'><a href="/perfil/'.$coment['nommem'].'">'.$coment['nomuser'].'</a></b> | 
<span class="size10">' . date("d.n.y H:i:s", $coment['fecha']) . '</span>';
if($context['user']['is_logged']) {
echo ' <a  href="/mensajes/a/'.$coment['nommem'].'" title="Enviar MP a: '.$coment['nomuser'].'"><img alt="" src="' . $settings['images_url'] . '/icons/mensaje_para.gif" style="margin-top:2px;margin-rigth:2px;" align="top" border="0" /></a><a class="icons citar" onclick="citar_comment('.$coment['id'].')" href="javascript:void(0)" title="Citar Comentario"><img alt="" src="' . $settings['images_url'] . '/espacio.gif" align="top" border="0" /></a>';
}
echo 'dijo:<br /><div style="overflow: hidden;">'. $coment['comment'] .'</div></span></div><div class="hrs"></div>';
}}else{
echo '<div id="no_comentarios" class="noesta">Este post no tiene comentarios';
if ($context['is_locked']) {echo ' y se encuentra cerrado, por lo tanto no se permiten nuevos comentarios';}

echo '.-</div>';}
echo '<div id="return_agregar_comentario" style="display:none;"></div></div>';
if($context['haycom']) {
	if ($message['can_remove']) {
echo '<span class="size10">Comentarios Seleccionados:</span>
<input class="login" style="font-size: 9px;" value="Eliminar" type="submit">
<input name="topic" value="', $context['current_topic'], '" type="hidden">
<input name="userid" value="', $context['user']['id'], '" type="hidden">
<input name="memberid" value="', $message['member']['id'], '" type="hidden">
';
	}
}
if($context['user']['is_logged']) {
echo '</form>';
}

echo '</div></div>';
echo '
<!-- fin comentarios -->';
if (($context['can_reply']) && !empty($options['display_quick_reply']) && !$context['is_locked'] && $context['user']['is_logged']) {
echo '<!-- comentar --><a name="comentar"></a>
<div style="margin-bottom:8px;" class="agregar_comentario">

<div class="box_780" style="float:left;margin-bottom:8px;">
<div class="box_title" style="width: 772px;"><div class="box_txt box_780-34">Agregar un nuevo comentario</div>
<div class="box_rss"><span id="gif_cargando_add_comment" style="display:none;"><img alt="" src="' . $settings['images_url'] . '/icons/cargando.gif" style="width:16px;height:16px;" border="0" /></span><span id="gif_cargando_add_comment2"><img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width:16px;height:16px;" border="0" /></span></div></div><div class="windowbg" style="width: 764px;padding: 4px;font-size:11px;"><form name="nuevocoment"><table><tr><td width="400px" valign="top"><div style="width:400px;"><center><div class="msg_add_comment"></div></center>';
theme_quickreply_box();
echo '<td valign="top"><img src="' . $settings['images_url'] . '/icons/bullet-verde.gif" atl=""><b> Se eliminan los comentarios:</b><br />

<img src="' . $settings['images_url'] . '/icons/bullet-rojo.gif" atl=""> Que contenga tipograf&iacute;as muy grandes, abuso de mayusculas o con el claro efecto de llamar la atenci&oacute;n.<br />
<img src="' . $settings['images_url'] . '/icons/bullet-rojo.gif" atl=""> Que el usuario haya comentado primero su post.<br />
<img src="' . $settings['images_url'] . '/icons/bullet-rojo.gif" atl=""> Que contengan insultos, ofensas, etc. (hacia otro usuario o de forma general).<br />
<img src="' . $settings['images_url'] . '/icons/bullet-rojo.gif" atl=""> Que sea un comentario racistas.<br />

<img src="' . $settings['images_url'] . '/icons/bullet-rojo.gif" atl=""> Que contenga SPAM.

<p style="padding:0px;margin:0px;" align="right">[<a href="/protocolo/" title="Protocolo completo">Protocolo completo</a>]</p><br />
<input class="login" type="button" id="button_add_comment" value="Enviar Comentario" onclick="add_comment(\'', $context['current_topic'], '\'); return false;" tabindex="2" /></td></tr></table><div style="clear: left;"></div></form></span>
</form></div></div></div>
<!-- fin comentar --></div>';
}

if($context['user']['is_guest'])
{
echo '<div style="clear: left;"></div><div class="noesta-am" style="width:774px;">Para poder comentar necesitas estar <a href="/registrarse/" style="color:#FFB600;" title="Reg&iacute;strarse">Registrado</a>. Si ya tienes usuario <a href="/ingresar/" style="color:#FFB600;" title="Conectarse">&iexcl;Con&eacute;ctate!</a></div>';
}

echo '</div></div><div style="clear:both"></div>';
}

}

function template_quickreply_box()
{
	global $context, $settings, $options, $txt, $modSettings;
	
echo '<textarea onfocus="foco(this);" onblur="no_foco(this);" style="height:90px;width:390px;" id="cuerpo_comment" name="cuerpo_comment" class="markItUpEditor" tabindex="1"></textarea>';
if (!empty($context['smileys']['postform']))
{
	foreach($context['smileys']['postform'] as $smiley_row)
	{
		foreach($smiley_row['smileys'] as $smiley)
		{
		echo '<a href="javascript:void(0);" onclick="replaceText(\' ', $smiley['code'], '\', document.forms.nuevocoment.cuerpo_comment); return false;"><img src="', $settings['smileys_url'], '/', $smiley['filename'], '" align="bottom" alt="', $smiley['description'], '" title="', $smiley['description'], '" /></a> ';
		}
	}
	if (!empty($context['smileys']['popup']))
	{
	echo '<script type="text/javascript">function openpopup(){var winpops=window.open("/emoticones/","","width=255px,height=500px,scrollbars");}</script><a href="javascript:openpopup()">[', $txt['more_smileys'], ']</a></div></td>';
	}
}
}
?>