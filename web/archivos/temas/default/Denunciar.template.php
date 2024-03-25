<?php
function template_manual_intro()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $db_prefix;

$context['ID_DEL_POST'] = $_GET['id'];
$request = db_query("
SELECT m.subject, m.ID_TOPIC, m.ID_MEMBER, ma.ID_MEMBER, ma.memberName, t.ID_MEMBER_STARTED
FROM ({$db_prefix}messages AS m, {$db_prefix}members AS ma, {$db_prefix}topics AS t)
WHERE m.ID_TOPIC = {$context['ID_DEL_POST']}
AND m.ID_MEMBER = ma.ID_MEMBER
AND t.ID_TOPIC  = m.ID_TOPIC
AND t.ID_TOPIC = {$context['ID_DEL_POST']}
", __FILE__, __LINE__);
	while ($row = mysql_fetch_assoc($request)){
			$titulo = $row['subject'];
			$id = $row['ID_TOPIC'];
			$usuario = $row['memberName'];		
			$user = $row['ID_MEMBER'];		
			$started = $row['ID_MEMBER_STARTED'];		
			}
	mysql_free_result($request);

if($context['user']['is_guest'])
{
fatal_error('Disculpe, para denunciar un post debe autentificarte.', false);
}
elseif(empty($_GET['id']))
{
fatal_error('Debes ingresar la ID del post a denunciar.', false);
}
elseif($context['ID_DEL_POST'] != $id)
{
fatal_error('El post que deseas denunciar no existe', false);
}
elseif($started == $context['user']['id'])
{
fatal_error('Disculpe, pero no puedes denunciar tus post, si tiene alg&uacute;n problema, b&oacute;rralo o ed&iacute;talo t&uacute;.', false);
}
else
{
echo '<script type="text/javascript">function errorrojos(comentario){if(comentario == \'\'){
document.getElementById(\'errorss\').innerHTML=\'<br /><font class="size10" style="color: red;">Es necesario escribir un comentario sobre la denuncia.</font>\'; return false;}}</script>';
echo '<div><div class="box_buscador">
<div class="box_title" style="width: 921px;"><div class="box_txt box_buscadort"><center>Denunciar Post</center></div>
<div class="box_rss"><img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width:14px;height:12px;" border="0" /></div></div>
<table border="0px" class="windowbg" width="921px">
	<tr align="center"><td align="center" >
    <form action="/denuncia/enviar/" method="post">
			<p align="center" class="size11"><b>Denunciar el post:</b> <br />
', $id , ' / ', $titulo, '
			<p align="center" class="size11"><b>Creado por:</b> <br />

', $usuario, '
<br /><br /><font class="size11"><b>Raz&oacute;n de la denuncia:</b></font><br />
			<select name="razon" style="color: black; background-color: rgb(250, 250, 250); font-size: 12px;">
			<option value="Re-post">Re-post</option>
			<option value="Se hace Spam">Se hace Spam</option>
			<option value="Tiene enlaces muertos">Tiene enlaces muertos</option>
			<option value="Es Racista o irrespetuoso">Es Racista o irrespetuoso</option>

			<option value="Contiene informacion personal">Contiene informaci&oacute;n personal</option>
			<option value="El Titulo esta en mayuscula">El Titulo esta en may&uacute;scula</option>
			<option value="Contiene Pornografia">Contiene Pornografia</option>
			<option value="Es Gore o asqueroso">Es Gore o asqueroso</option>
			<option value="Esta mal la fuente">Est&aacute; mal la fuente</option>

			<option value="Post demasiado pobre">Post demasiado pobre</option>
			<option value="Pide contrasena y no esta">Pide contrase&ntilde;a y no est&aacute;</option>
			<option value="No cumple con el protocolo">No cumple con el protocolo</option>
			<option value="Otra razon (especificar)">Otra raz&oacute;n (especificar)</option>
			</select><br /><br />
			<font class="size11"><b>Aclaraci&oacute;n y comentarios:</b></font><br />

			<textarea name="comentario" cols="40" rows="5" wrap="hard" tabindex="6"></textarea><label id="errorss"></label><br /><font size="1">En el caso de ser Re-post se debe indicar el enlace del 
post original.</font>
<br /><br /><input onclick="return errorrojos(this.form.comentario.value);" class="login" type=submit value="Denunciar Post" /><br /><input type="hidden" name="ID_TOPIC" value="', $id , '"><input type="hidden" name="tipo" value="post" /></form></td></tr></table></div></div><div style="clear:both"></div></div>'; 
}
}

function template_manual_login()
{
echo '<div align="center"><div class="box_errors"><div class="box_title" style="width: 388px"><div class="box_txt box_error" align="left">Denuncia enviada</div><div class="box_rss"><img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width:14px;height:12px;" border="0" /></div></div><div class="windowbg" style="width:380px;padding:4px;"><br />Tu denuncia ha sido enviada correctamente.-<br /><br /><input class="login" style="font-size: 11px;" type="submit" title="Ir a la P&aacute;gina principal" value="Ir a la P&aacute;gina principal" onclick="location.href=\''.$boardurl.'/\'" /><br /><br /></div></div><br /></div><div style="clear:both"></div></div>';}
function template_manual_above(){}
function template_manual_below(){}
?>