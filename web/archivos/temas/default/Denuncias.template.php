<?php
@require_once('SSI.php');

function template_main()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $user_info, $db_prefix, $query_this_board, $func, $board;

$request = db_query("
SELECT *
FROM {$db_prefix}denunciations
WHERE TYPE = 'post'");
$context['denunciasss'] = mysqli_num_rows($request);

$RegistrosAMostrar		=	$modSettings['denunciations'];
if(isset($_GET['pag'])){
$RegistrosAEmpezar	=	($_GET['pag']-1)*$RegistrosAMostrar;
$PagAct	=	(int) $_GET['pag'];
} else {
$RegistrosAEmpezar	=	0;
$PagAct				=	1;
}
$request2	= db_query("
SELECT den.ID_DENUNCIATIONS, den.ID_TOPIC, den.ID_MEMBER, den.comment, den.reason, den.TYPE, m.ID_MEMBER,
m.realName AS realName1, m.memberName AS memberName1, b.ID_BOARD, b.description, m2.ID_TOPIC, m2.ID_BOARD, 
m2.subject, m2.ID_MEMBER, m3.ID_MEMBER, m3.recibir, m3.realName AS realName2, m3.memberName AS memberName2
FROM ({$db_prefix}denunciations AS den, {$db_prefix}members AS m, {$db_prefix}boards AS b, {$db_prefix}messages as m2, {$db_prefix}members as m3)
WHERE den.ID_TOPIC = m2.ID_TOPIC
AND den.ID_MEMBER = m.ID_MEMBER
AND m2.ID_BOARD = b.ID_BOARD
AND den.TYPE = 'post'
AND m3.ID_MEMBER = m2.ID_MEMBER
ORDER BY den.ID_DENUNCIATIONS DESC
LIMIT {$RegistrosAEmpezar}, {$RegistrosAMostrar}", __FILE__, __LINE__);
$count	=	mysqli_num_rows($request2);
echo '<div style="float:left;width:737px;margin-right:8px;"><div class="mennes"><div class="botnes"><ul>

<li>
<a href="/admin-denuncias/post/" title="Posts">Posts</a></li>
<li>
<a href="/admin-denuncias/imagen/" title="Im&aacute;genes">Im&aacute;genes</a></li>
<li>
<a href="/admin-denuncias/user/" title="Usuarios">Usuarios</a></li>
<li>
<a href="/admin-denuncias/comunidades/" title="Comunidades">Comunidades</a></li>
</ul><div style="clear: both;"></div></div></div><div class="clearBoth"></div>
<form action="/admin-denuncias/eliminar/" method="post" accept-charset="', $context['character_set'], '" name="eliminar" id="eliminar"><div class="box_745" style="float:left;"><div class="box_title" style="width: 745px;"><div style="text-align:center;" class="box_txt"><center>' . $context['page_title'] . '</center></div><div class="box_rss"><img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width:16px;height:16px;" border="0" /></div></div>
<div class="windowbg" style="width: 737px; padding: 4px;">
<table width="100%" style="padding:4px;border:none;">
';
if($count <= 0)
{
echo '<div class="noesta">No hay denuncias de posts hechos.</div>';
}
else
{
while($row	=	mysqli_fetch_assoc($request2)){
$comentario = htmlspecialchars($row['comment']);
$comentario = censorText($row['comment']);
echo '<td><input type="checkbox" name="campos['.$row['ID_DENUNCIATIONS'].']"></td>';
echo '<tr><td width="20%"><b class="size11">Post Denunciado: </b></td>
<td><a href="/post/'.$row['ID_TOPIC'].'/'.$row['description'].'/'.ssi_amigable($row['subject']).'.html" title="'.$row['subject'].'">'.$row['subject'].'</a>
</td></tr>';
echo '<tr><td width="20%"><b class="size11">Autor del post: </b></td>
<td><div style="margin-bottom:2px;"><span style="font-size:12px;"><a href="/perfil/'.$row['memberName2'].'" title="'.$row['realName2'].'">'.$row['realName2'].'</a>';
if($row['recibir'] == 'si')
{
echo '&nbsp;<a href="/mensajes/a/'.$row['memberName2'].'" title="Enviar mensaje"><img alt="" src="' . $settings['images_url'] . '/icons/mensaje_para.gif" border="0" /></a>';
}
echo '</span></div></td></tr>';
echo '<tr><td width="20%"><b class="size11">Informar del post: </b></td>
<td>';
if($row['recibir'] == 'si')
{
echo '<img src="' . $settings['images_url'] . '/icons/bullet-verde.gif" atl="" />';
}
elseif($row['recibir'] == 'no')
{
echo '<img src="' . $settings['images_url'] . '/icons/bullet-rojo.gif" atl="" />';
}
echo '</a>
</td></tr>';
echo '<tr><td width="20%"><b class="size11">Usuario que denunci&oacute;: </b></td>
<td><a href="/perfil/'.$row['memberName1'].'" title="'.$row['realName1'].'">'.$row['realName1'].'</a>
</td></tr>';
echo '<tr><td width="20%"><b class="size11">Raz&oacute;n: </b></td>
<td>'.$den1['reason'].'</td></tr>';
echo '<tr><td width="20%"><b class="size11">Comentario: </b></td>
<td>'.$comentario.'</td></tr>';
}
$NroRegistros	=	mysqli_num_rows(db_query("SELECT den.ID_DENUNCIATIONS, den.ID_TOPIC, den.ID_MEMBER, den.comment, den.reason, den.TYPE, m.ID_MEMBER,
m.realName AS realName1, m.memberName AS memberName1, b.ID_BOARD, b.description, m2.ID_TOPIC, m2.ID_BOARD, 
m2.subject, m2.ID_MEMBER, m3.ID_MEMBER, m3.recibir, m3.realName AS realName2, m3.memberName AS memberName2
FROM ({$db_prefix}denunciations AS den, {$db_prefix}members AS m, {$db_prefix}boards AS b, {$db_prefix}messages as m2, {$db_prefix}members as m3)
WHERE den.ID_TOPIC = m2.ID_TOPIC
AND den.ID_MEMBER = m.ID_MEMBER
AND m2.ID_BOARD = b.ID_BOARD
AND den.TYPE = 'post'
AND m3.ID_MEMBER = m2.ID_MEMBER
ORDER BY den.ID_DENUNCIATIONS DESC", __FILE__, __LINE__));
}

 $PagAnt=$PagAct-1;
 $PagSig=$PagAct+1;
 $PagUlt=$NroRegistros/$RegistrosAMostrar;
 $Res=$NroRegistros%$RegistrosAMostrar;
 if($Res>0) $PagUlt=floor($PagUlt)+1;
echo '</table></div><div class="windowbgpag" style="width:757px;">';
if($PagSig<$PagUlt){ echo '';}
if($PagAct>1) echo '<a href=\'/admin-denuncias/post/pag-' . $PagAnt . '\'>&#171; anterior</a>';
if($PagAct<$PagUlt)  echo '<a href=\'/admin-denuncias/post/pag-' . $PagSig . '\'>siguiente &#187;</a>';
if(!$count <= 0)
{
echo '</div><p align="right"><span class="size10">Denuncia/s Seleccionada/s:</span> <input class="login" style="font-size: 9px;" type="submit" value="Eliminar"></p>';
}
echo '
</div></form><div style="clear:both"></div>';

}

function template_imagen()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $user_info, $db_prefix, $query_this_board, $func, $board;

$request = db_query("
SELECT *
FROM {$db_prefix}denunciations
WHERE TYPE = 'imagen'");
$context['denunciasss'] = mysqli_num_rows($request);

$RegistrosAMostrar		=	$modSettings['denunciations'];
if(isset($_GET['pag'])){
$RegistrosAEmpezar	=	($_GET['pag']-1)*$RegistrosAMostrar;
$PagAct	=	(int) $_GET['pag'];
} else {
$RegistrosAEmpezar	=	0;
$PagAct				=	1;
}
$request2	= db_query("
SELECT den.ID_DENUNCIATIONS, den.ID_TOPIC, den.ID_MEMBER, den.comment, den.reason, den.TYPE, m.ID_MEMBER, m.realName, m.memberName, g.ID_PICTURE, g.title, g.ID_MEMBER
FROM ({$db_prefix}denunciations AS den, {$db_prefix}members AS m, {$db_prefix}gallery_pic AS g)
WHERE den.ID_TOPIC = g.ID_PICTURE
AND den.ID_MEMBER = m.ID_MEMBER
AND den.TYPE = 'imagen'
ORDER BY den.ID_DENUNCIATIONS DESC
LIMIT {$RegistrosAEmpezar}, {$RegistrosAMostrar}", __FILE__, __LINE__);
$count	=	mysqli_num_rows($request2);
echo '<div style="float:left;width:737px;margin-right:8px;"><div class="mennes"><div class="botnes"><ul>

<li>
<a href="/admin-denuncias/post/" title="Posts">Posts</a></li>
<li>
<a href="/admin-denuncias/imagen/" title="Im&aacute;genes">Im&aacute;genes</a></li>
<li>
<a href="/admin-denuncias/user/" title="Usuarios">Usuarios</a></li>
<li>
<a href="/admin-denuncias/comunidades/" title="Comunidades">Comunidades</a></li>
</ul><div style="clear: both;"></div></div></div><div class="clearBoth"></div>
<form action="/admin-denuncias/eliminar/" method="post" accept-charset="', $context['character_set'], '" name="eliminar" id="eliminar"><div class="box_745" style="float:left;"><div class="box_title" style="width: 745px;"><div style="text-align:center;" class="box_txt"><center>' . $context['page_title'] . '</center></div><div class="box_rss"><img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width:16px;height:16px;" border="0" /></div></div>
<div class="windowbg" style="width: 737px; padding: 4px;">
<table width="100%" style="padding:4px;border:none;">
';
if($count <= 0)
{
echo '<div class="noesta">No hay denuncias de im&aacute;genes hechas.</div>';
}
else
{
while($row	=	mysqli_fetch_assoc($request2)){
$comentario = htmlspecialchars($row['comment']);
$comentario = censorText($row['comment']);
echo '<tr>
<td><input type="checkbox" name="campos['.$row['ID_DENUNCIATIONS'].']"></td></tr>';
echo '<tr><td width="20%"><b class="size11">Post Denunciado: </b></td>
<td><a href="/imagenes/ver/'.$row['ID_TOPIC'].'" title="'.$row['title'].'">'.$row['title'].'</a>
</td></tr>';
echo '<tr><td width="20%"><b class="size11">Usuario que denunci&oacute;: </b></td>
<td><a href="/perfil/'.$row['memberName'].'" title="'.$row['realName'].'">'.$row['realName'].'</a>
</td></tr>';
echo '<tr><td width="20%"><b class="size11">Raz&oacute;n: </b></td>
<td>'.$row['reason'].'</td></tr>';
echo '<tr><td width="20%"><b class="size11">Comentario: </b></td>
<td>'.$comentario.'</td></tr>';
}
$NroRegistros	=	mysqli_num_rows(db_query("SELECT den.ID_DENUNCIATIONS, den.ID_TOPIC, den.ID_MEMBER, den.comment, den.reason, den.TYPE, m.ID_MEMBER, m.realName, m.memberName, g.ID_PICTURE, g.title, g.ID_MEMBER
FROM ({$db_prefix}denunciations AS den, {$db_prefix}members AS m, {$db_prefix}gallery_pic AS g)
WHERE den.ID_TOPIC = g.ID_PICTURE
AND den.ID_MEMBER = m.ID_MEMBER
AND den.TYPE = 'imagen'
ORDER BY den.ID_DENUNCIATIONS DESC ", __FILE__, __LINE__));
}

 $PagAnt=$PagAct-1;
 $PagSig=$PagAct+1;
 $PagUlt=$NroRegistros/$RegistrosAMostrar;
 $Res=$NroRegistros%$RegistrosAMostrar;
 if($Res>0) $PagUlt=floor($PagUlt)+1;
echo '</table></div><div class="windowbgpag" style="width:757px;">';
if($PagSig<$PagUlt){ echo '';}
if($PagAct>1) echo '<a href=\'/admin-denuncias/imagen/pag-' . $PagAnt . '\'>&#171; anterior</a>';
if($PagAct<$PagUlt)  echo '<a href=\'/admin-denuncias/imagen/pag-' . $PagSig . '\'>siguiente &#187;</a>';
if(!$count <= 0)
{
echo '</div><p align="right"><span class="size10">Denuncia/s Seleccionada/s:</span> <input class="login" style="font-size: 9px;" type="submit" value="Eliminar"></p>';
}
echo '</div></form><div style="clear:both"></div>';

}

function template_user()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $user_info, $db_prefix, $query_this_board, $func, $board;

$request = db_query("
SELECT *
FROM {$db_prefix}denunciations
WHERE TYPE = 'user'");
$context['denunciasss'] = mysqli_num_rows($request);

$RegistrosAMostrar		=	$modSettings['denunciations'];
if(isset($_GET['pag'])){
$RegistrosAEmpezar	=	($_GET['pag']-1)*$RegistrosAMostrar;
$PagAct	=	(int) $_GET['pag'];
} else {
$RegistrosAEmpezar	=	0;
$PagAct				=	1;
}
$request2	= db_query("
SELECT mem.ID_MEMBER, mem.memberName AS memberName1, mem.realName AS realName1, mem2.ID_MEMBER, mem2.memberName AS memberName2, mem2.realName AS realName2, den.ID_DENUNCIATIONS, den.ID_TOPIC, den.ID_MEMBER, den.TYPE, den.comment, den.reason
FROM ({$db_prefix}members AS mem, {$db_prefix}members AS mem2, {$db_prefix}denunciations AS den)
WHERE mem.ID_MEMBER = den.ID_MEMBER
AND den.ID_TOPIC = mem2.ID_MEMBER
AND den.TYPE = 'user'
ORDER BY den.ID_DENUNCIATIONS DESC
LIMIT {$RegistrosAEmpezar}, {$RegistrosAMostrar}", __FILE__, __LINE__);
$count	=	mysqli_num_rows($request2);
echo '<div style="float:left;width:737px;margin-right:8px;"><div class="mennes"><div class="botnes"><ul>

<li>
<a href="/admin-denuncias/post/" title="Posts">Posts</a></li>
<li>
<a href="/admin-denuncias/imagen/" title="Im&aacute;genes">Im&aacute;genes</a></li>
<li>
<a href="/admin-denuncias/user/" title="Usuarios">Usuarios</a></li>
<li>
<a href="/admin-denuncias/comunidades/" title="Comunidades">Comunidades</a></li>
</ul><div style="clear: both;"></div></div></div><div class="clearBoth"></div>
<form action="/admin-denuncias/eliminar/" method="post" accept-charset="', $context['character_set'], '" name="eliminar" id="eliminar"><div class="box_745" style="float:left;"><div class="box_title" style="width: 745px;"><div style="text-align:center;" class="box_txt"><center>' . $context['page_title'] . '</center></div><div class="box_rss"><img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width:16px;height:16px;" border="0" /></div></div>
<div class="windowbg" style="width: 737px; padding: 4px;">
<table width="100%" style="padding:4px;border:none;">
';
if($count <= 0)
{
echo '<div class="noesta">No hay denuncias de usuarios hechas.</div>';
}
else
{
while($row	=	mysqli_fetch_assoc($request2)){
$comentario = htmlspecialchars($row['comment']);
$comentario = censorText($row['comment']);
echo '<tr>
<td><input type="checkbox" name="campos['.$row['ID_DENUNCIATIONS'].']"></td></tr>';
echo '<tr><td width="20%"><b class="size11">Usuario Denunciado: </b></td>
<td><a href="/perfil/'.$row['memberName2'].'" title="'.$row['realName2'].'">'.$row['realName2'].'</a>
</td></tr>';
echo '<tr><td width="20%"><b class="size11">Usuario que denunci&oacute;: </b></td>
<td><a href="/perfil/'.$row['memberName1'].'" title="'.$row['realName1'].'">'.$row['realName1'].'</a>
</td></tr>';
echo '<tr><td width="20%"><b class="size11">Raz&oacute;n: </b></td>
<td>'.$row['reason'].'</td></tr>';
echo '<tr><td width="20%"><b class="size11">Comentario: </b></td>
<td>'.$comentario.'</td></tr>';
}
$NroRegistros	=	mysqli_num_rows(db_query("SELECT mem.ID_MEMBER, mem.memberName AS memberName1, mem.realName AS realName1, mem2.ID_MEMBER, mem2.memberName AS memberName2, mem2.realName AS realName2, den.ID_DENUNCIATIONS, den.ID_TOPIC, den.ID_MEMBER, den.TYPE, den.comment, den.reason
FROM ({$db_prefix}members AS mem, {$db_prefix}members AS mem2, {$db_prefix}denunciations AS den)
WHERE mem.ID_MEMBER = den.ID_MEMBER
AND den.ID_TOPIC = mem2.ID_MEMBER
AND den.TYPE = 'user'
ORDER BY den.ID_DENUNCIATIONS DESC ", __FILE__, __LINE__));
}

 $PagAnt=$PagAct-1;
 $PagSig=$PagAct+1;
 $PagUlt=$NroRegistros/$RegistrosAMostrar;
 $Res=$NroRegistros%$RegistrosAMostrar;
 if($Res>0) $PagUlt=floor($PagUlt)+1;
echo '</table></div><div class="windowbgpag" style="width:757px;">';
if($PagSig<$PagUlt){ echo '';}
if($PagAct>1) echo '<a href=\'/admin-denuncias/user/pag-' . $PagAnt . '\'>&#171; anterior</a>';
if($PagAct<$PagUlt)  echo '<a href=\'/admin-denuncias/user/pag-' . $PagSig . '\'>siguiente &#187;</a>';
if(!$count <= 0)
{
echo '<p align="right"><span class="size10">Denuncia/s Seleccionada/s:</span> <input class="login" style="font-size: 9px;" type="submit" value="Eliminar"></p>';
}
echo '</div></form><div style="clear:both"></div>';
}

function template_comunidades()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $user_info, $db_prefix, $query_this_board, $func, $board;

$request = db_query("
SELECT *
FROM {$db_prefix}denunciations
WHERE TYPE = 'comunidad'");
$context['denunciasss'] = mysqli_num_rows($request);

$RegistrosAMostrar		=	$modSettings['denunciations'];
if(isset($_GET['pag'])){
$RegistrosAEmpezar	=	($_GET['pag']-1)*$RegistrosAMostrar;
$PagAct	=	(int) $_GET['pag'];
} else {
$RegistrosAEmpezar	=	0;
$PagAct				=	1;
}
$request2	= db_query("
SELECT *
FROM ({$db_prefix}members AS mem, {$db_prefix}denunciations AS den, {$db_prefix}communities AS c)
WHERE mem.ID_MEMBER = den.ID_MEMBER
AND den.ID_TOPIC = c.ID_COMMUNITY
AND den.TYPE = 'comunidad'
ORDER BY den.ID_DENUNCIATIONS DESC
LIMIT {$RegistrosAEmpezar}, {$RegistrosAMostrar}", __FILE__, __LINE__);
$count	=	mysqli_num_rows($request2);
echo '<div style="float:left;width:737px;margin-right:8px;"><div class="mennes"><div class="botnes"><ul>

<li>
<a href="/admin-denuncias/post/" title="Posts">Posts</a></li>
<li>
<a href="/admin-denuncias/imagen/" title="Im&aacute;genes">Im&aacute;genes</a></li>
<li>
<a href="/admin-denuncias/user/" title="Usuarios">Usuarios</a></li>
<li>
<a href="/admin-denuncias/comunidades/" title="Comunidades">Comunidades</a></li>
</ul><div style="clear: both;"></div></div></div><div class="clearBoth"></div>
<form action="/admin-denuncias/eliminar/" method="post" accept-charset="', $context['character_set'], '" name="eliminar" id="eliminar"><div class="box_745" style="float:left;"><div class="box_title" style="width: 745px;"><div style="text-align:center;" class="box_txt"><center>' . $context['page_title'] . '</center></div><div class="box_rss"><img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width:16px;height:16px;" border="0" /></div></div>
<div class="windowbg" style="width: 737px; padding: 4px;">
<table width="100%" style="padding:4px;border:none;">
';
if($count <= 0)
{
echo '<div class="noesta">No hay denuncias de comunidades hechas.</div>';
}
else
{
while($row	=	mysqli_fetch_assoc($request2)){
$comentario = htmlspecialchars($row['comment']);
$comentario = censorText($row['comment']);
echo '<tr>
<td><input type="checkbox" name="campos['.$row['ID_DENUNCIATIONS'].']"></td></tr>';
echo '<tr><td width="20%"><b class="size11">Comunidad Denunciada: </b></td>
<td><a href="/comunidades/' . $row['friendly_url'].'" title="'.$row['friendly_url'].'">'.$row['title'].'</a>
</td></tr>';
echo '<tr><td width="20%"><b class="size11">Usuario que denunci&oacute;: </b></td>
<td><a href="/perfil/'.$row['memberName'].'" title="'.$row['realName'].'">'.$row['realName'].'</a>
</td></tr>';
echo '<tr><td width="20%"><b class="size11">Raz&oacute;n: </b></td>
<td>' . $row['reason'] . '</td></tr>';
echo '<tr><td width="20%"><b class="size11">Comentario: </b></td>
<td>' . $comentario . '</td></tr>';
}
$NroRegistros	=	mysqli_num_rows(db_query("SELECT *
FROM ({$db_prefix}members AS mem, {$db_prefix}denunciations AS den, {$db_prefix}communities AS c)
WHERE mem.ID_MEMBER = den.ID_MEMBER
AND den.ID_TOPIC = c.ID_COMMUNITY
AND den.TYPE = 'comunidad'
ORDER BY den.ID_DENUNCIATIONS DESC ", __FILE__, __LINE__));
}

 $PagAnt=$PagAct-1;
 $PagSig=$PagAct+1;
 $PagUlt=$NroRegistros/$RegistrosAMostrar;
 $Res=$NroRegistros%$RegistrosAMostrar;
 if($Res>0) $PagUlt=floor($PagUlt)+1;
echo '</table></div><div class="windowbgpag" style="width:757px;">';
if($PagSig<$PagUlt){ echo '';}
if($PagAct>1) echo '<a href=\'/admin-denuncias/comunidades/pag-' . $PagAnt . '\'>&#171; anterior</a>';
if($PagAct<$PagUlt)  echo '<a href=\'/admin-denuncias/comunidades/pag-' . $PagSig . '\'>siguiente &#187;</a>';
if(!$count <= 0)
{
echo '<p align="right"><span class="size10">Denuncia/s Seleccionada/s:</span> <input class="login" style="font-size: 9px;" type="submit" value="Eliminar"></p>';
}
echo '</div></form><div style="clear:both"></div>';
}

function template_eliminar()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $user_info, $db_prefix, $query_this_board, $func, $board;

if(!empty($_POST['campos']) || $context['allow_admin']) {
$aLista	=	array_keys($_POST['campos']);
db_query("DELETE FROM {$db_prefix}denunciations WHERE ID_DENUNCIATIONS IN (".implode(',',$aLista).")");

Header("Location: /admin-denuncias/");
}
}

?>