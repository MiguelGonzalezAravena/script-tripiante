<?php
function template_main()
{
	global $txt, $context, $scripturl, $settings, $modSettings, $user_info, $ID_MEMBER, $db_prefix;


echo '<div style="float:left;width:757px;margin-right:8px;"><div class="mennes"><div class="botnes"><ul>

<li>
<a href="/favoritos/post/" title="Posts">Posts</a></li>
<li>

<a href="/favoritos/imagen/" title="Im&aacute;genes">Im&aacute;genes</a>
</li>

</ul><div style="clear: both;"></div></div></div><div class="clearBoth"></div><div class="box_757"><div class="box_title" style="width: 755px;"><div class="box_txt box_757-34"><center>', $txt['bookmark_list'], '</center></div><div class="box_rss"><img alt="" src="', $settings['images_url'], '/blank.gif" style="width:16px;height:16px;" border="0"/></div></div><div style="width:747px;padding:4px;" class="windowbg"><form action="/favoritos/eliminar/" method="post">';

$RegistrosAMostrar		=	$modSettings['bookmarks_posts'];
if(isset($_GET['pag'])) {
$RegistrosAEmpezar	=	($_GET['pag']-1)*$RegistrosAMostrar;
$PagAct	=	$_GET['pag'];
} else {
$RegistrosAEmpezar	=	0;
$PagAct				=	1;
}
$request2	= db_query("
SELECT ms.posterTime, t.points, b.ID_BOARD, b.name, b.description, mem.realName, ms.ID_BOARD, t.ID_TOPIC, mem.ID_MEMBER, mem.memberName, ms.ID_MEMBER, ms.ID_TOPIC, ms.subject, ms.ID_MEMBER	
FROM ({$db_prefix}bookmarks AS bm, {$db_prefix}boards AS b, {$db_prefix}topics AS t, {$db_prefix}messages AS ms, {$db_prefix}members AS mem)
WHERE bm.ID_MEMBER = $ID_MEMBER
AND t.ID_TOPIC = bm.ID_TOPIC
AND t.ID_TOPIC = ms.ID_TOPIC
AND ms.ID_BOARD = b.ID_BOARD
AND mem.ID_MEMBER = ms.ID_MEMBER
AND bm.TYPE = 'posts'
AND $user_info[query_see_board]
ORDER BY bm.ID_TOPIC DESC
LIMIT {$RegistrosAEmpezar}, {$RegistrosAMostrar}", __FILE__, __LINE__);
$context['bookmarks'] = mysql_num_rows($request2);
if(!empty($context['bookmarks'])) {
while($row	=	mysql_fetch_assoc($request2)) {
echo '<div class="entryf"><div class="icon"><img alt="', $row['name'], '" title="', $row['name'], '" src="', $settings['images_url'], '/post/icono_', $row['ID_BOARD'], '.gif" /></div><div class="text_container"><div class="title"><a href="/post/', $row['ID_TOPIC'], '/', $row['description'], '/', ssi_amigable($row['subject']), '.html">', $row['subject'], '</a></div><div style="margin:0pt;float:left;" class="data"><p style="margin:0px;padding:0px;" align="right">', $txt['was_created_by'], ' <a style="color:#717171;" href="/perfil/', $row['memberName'], '">', $row['realName'], '</a> | pts: ', $row['points'], ' | <a title="', $txt['send_to_friend'], '" href="/enviar-a-amigo/', $row['ID_TOPIC'], '"><img alt="" src="', $settings['images_url'], '/icons/icono-enviar-mensaje.gif" /></a> | <input name="remove_bookmarks[]" type="checkbox" value="', $row['ID_TOPIC'], '" /></p></div></div></div>';
}
$NroRegistros	=	mysql_num_rows(db_query("SELECT ms.posterTime, t.points, b.ID_BOARD, b.name, b.description, mem.realName, ms.ID_BOARD, t.ID_TOPIC, mem.ID_MEMBER, mem.memberName, ms.ID_MEMBER, ms.ID_TOPIC, ms.subject, ms.ID_MEMBER	
FROM ({$db_prefix}bookmarks AS bm, {$db_prefix}boards AS b, {$db_prefix}topics AS t, {$db_prefix}messages AS ms, {$db_prefix}members AS mem)
WHERE bm.ID_MEMBER = $ID_MEMBER
AND t.ID_TOPIC = bm.ID_TOPIC
AND t.ID_TOPIC = ms.ID_TOPIC
AND ms.ID_BOARD = b.ID_BOARD
AND mem.ID_MEMBER = ms.ID_MEMBER
AND bm.TYPE = 'posts'
AND $user_info[query_see_board]
ORDER BY bm.ID_TOPIC DESC", __FILE__, __LINE__));


 $PagAnt=$PagAct-1;
 $PagSig=$PagAct+1;
 $PagUlt=$NroRegistros/$RegistrosAMostrar;
 $Res=$NroRegistros%$RegistrosAMostrar;
 if($Res>0) $PagUlt=floor($PagUlt)+1;
echo '<div style="clear: left;"></div><p style="margin-top:5px;margin-left:0px;margin-right:0px;margin-bottom:0px;padding:0px;" align="right"><input class="login" style="font-size: 12px;" value="', $txt['bookmark_delete'], '" type="submit" name="send" /></p></form></div>';
} else {
echo '<div class="noesta">', $txt['bookmark_list_empty'], '</div>';
}
 echo '</div><div class="windowbgpag" style="width:757px;">';

if($PagAct>1) echo '<a href=\'/favoritos/post/pag-' . $PagAnt . '\'>&#171; anterior</a>';
if($PagAct<$PagUlt)  echo '<a href=\'/favoritos/post/pag-' . $PagSig . '\'>siguiente &#187;</a>';
echo '</div><div class="clearBoth"></div><div style="clear: both;"></div></div></div>';
echo '<div width="160px" style="float:left;width:160px;"><div style="float: left; margin-bottom:8px;" class="img_aletat"><div class="box_title" style="width: 160px;"><div class="box_txt img_aletat">Publicidad</div><div class="box_rss"><img src="' . $settings['images_url'] . '/blank.gif" style="width: 16px; height: 16px;" border="0" alt="" /></div></div><div class="windowbg" style="width: 150px; padding: 4px;"><center>', $modSettings['vertical'], '</center></div></div></div><div style="clear:both"></div></div>
';

}
?>