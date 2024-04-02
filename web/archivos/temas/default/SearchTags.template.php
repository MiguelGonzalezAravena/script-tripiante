<?php
// Version: 1.1.1; Search

function template_main()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings;

	echo '
<script type="text/javascript">function errorr(search){if(search == \'\'){document.getElementById(\'errorss\').innerHTML=\'<br /><font class="size10" style="color: red;">Es necesario escribir una palabra para buscar.</font>\'; return false;}}</script>
<div class="box_buscador"><div class="box_title" style="width: 920px;"><div class="box_txt box_buscadort"><center>Buscador</center></div><div class="box_rss"><img alt="" src="', $settings['images_url'], '/blank.gif" style="width: 14px; height: 12px;" border="0" /></div></div><div style="width:912px;padding:4px;" class="windowbg"><form action="', $scripturl, '?action=searchtag2" method="post" accept-charset="', $context['character_set'], '" name="searchform" id="searchform"><center>
<div class="dataL">
<b class="size12">Buscar:</b><br/><input title="Busca con ', $context['forum_name'], '" type="text" onfocus="foco(this);" onblur="no_foco(this);" name="search"', !empty($context['search_params']['search']) ? ' value="' . $context['search_params']['search'] . '"' : '', ' style="width: 200px;" /></div><div class="dataR"><b class="size12">Usuario:</b><br/><input type="text" onfocus="foco(this);" onblur="no_foco(this);" name="userspec" value="', empty($context['search_params']['userspec']) ? '' : $context['search_params']['userspec'], '" style="width: 200px;" /></div>
<div class="clearBoth"></div>


<div class="dataL"><b class="size12">Orden:</b><br/><select style="width: 200px;" name="sort"><option value="ID_MSG|desc">M&aacute;s reciente</option><option value="ID_MSG|asc">M&aacute;s antiguo</option><option value="points">M&aacute;s puntuados</option></select></div><div class="dataR"><b class="size12">Categor&iacute;as:</b><br/>

<select style="width:202px;" name="brd" class="select"><option value="0" selected="selected">Todas</option>';
	foreach ($context['boards'] as $board){
	echo '<option value="', $board['id'], '"', $board['selected'] ? ' selected="selected"' : '', '>', $board['name'], '</option>';}
	
echo '</select></div>

<br /><br /><label id="errorss"></label><br /><input class="login" onclick="return errorr(this.form.search.value);" style="font-size: 15px;width:200px;" value="Buscar" title="Buscar" type="submit" /></center></form></div></div>

<div style="clear:both"></div>';

}

function template_results()
{
	global $context, $settings, $options, $txt, $scripturl, $counter, $modSettings;

	if ($context['compact'])
	{
echo '<script type="text/javascript">function errorr(tag){if(tag == \'\'){document.getElementById(\'errorss\').innerHTML=\'<br /><font class="size10" style="color: red;">Es necesario escribir un TAG para buscar.</font>\'; return false;}}</script>

<div class="box_buscador"><div class="box_title" style="width: 920px;"><div class="box_txt box_buscadort"><center>Buscador por tags</center></div><div class="box_rss"><img alt="" src="', $settings['images_url'], '/blank.gif" style="width:14px;height:12px;" border="0" /></div></div>

<div style="width:912px;padding:4px;" class="windowbg"><form name="buscador" action="', $scripturl, '?action=searchtag2" method="post"><center><b class="size11">TAG a buscar: </b>&nbsp;<input title="Busca con ', $context['forum_name'], '" type="text" onfocus="foco(this);" onblur="no_foco(this);" name="search"', !empty($context['search_params']['search']) ? ' value="' . $context['search_params']['search'] . '"' : '', ' style="width: 200px;" />&nbsp;<b class="size11">Orden:</b>&nbsp;<select style="width: 200px;" name="sort"><option value="ID_MSG|desc">M&aacute;s reciente</option><option value="ID_MSG|asc">M&aacute;s antiguo</option><option value="points|desc">M&aacute;s puntuados</option></select>&nbsp;<b class="size11">Categor&iacute;as:</b>&nbsp;<select style="width: 200px;" name="board"><option value="0" selected="selected">Todas</option>';
	foreach ($context['boards'] as $board){
	echo '<option value="', $board['id'], '"', $board['selected'] ? ' selected="selected"' : '', '>', $board['name'], '</option>';}
	
echo '</select><label id="errorss"></label><br><input class="login" onclick="return errorr(this.form.tag.value);" style="font-size: 15px; width: 200px;" value="Buscar" title="Buscar" type="submit"></center></form></div></div>
';
if(!empty($context['topics']))
{
echo '<div style="float:left;width:708px;margin-bottom:8px;"><table class="linksList" style="width:700px;">

<thead align="center"><tr><th>&nbsp;</th><th style="text-align:left;">X Posts con el tag: ' . $context['search_params']['search'] . '</th><th>Fecha</th><th>Puntos</th></tr></thead><tbody><tr>';
}

	if (!empty($context['search_errors']))
	{
echo '<span class="size12"><b>', implode('<br />', $context['search_errors']['messages']), '</b><br/><br/>
';
	}
		while ($topic = $context['get_topics']())
		{
		echo '<tr><td><img alt="" title="', 	$topic['board']['name'], '" src="', $settings['images_url'], '/post/icono_', $topic['board']['id'], '.gif" /></td>
<td style="text-align: left;"><a rel="dc:relation" target="_self" href="/post/', $topic['id'], '/', $topic['board']['description'], '/' , ssi_amigable($topic['first_post']['subject']), '.html" title="' , $topic['first_post']['subject'] , '" alt="' , $topic['first_post']['subject'] , '">' , ssi_reducir($topic['first_post']['subject']) , '</a></td><td title="' , $topic['first_post']['fecha'] , '">' , $topic['first_post']['fecha'] , '</td><td title="' , $topic['first_post']['puntos'] , ' Puntos" style="color:green;">' , $topic['first_post']['puntos'] , '</td>';
		}}
		/*
		<table width="100%"><tr><td width="100%"><div><div style="float: left;"><div class="box_icono4"><img title="', 	$topic['board']['name'], '" src="/Themes/default/images/post/icono_', $topic['board']['id'], '.gif"></div> <span title="' , $topic['first_post']['subject'] , '">' , $topic['first_post']['link'] , '</div><div align="right" class="opc_fav">Creado: ' , $topic['first_post']['fecha'] , ' por: ' , $topic['first_post']['name'] , ' | ' , $topic['first_post']['puntos'] , ' pts.</div></div></td></tr></table>';}}*/
if (!empty($context['topics'])){}	else
echo '<div class="noesta" style="width:922px;">', $txt['search_no_results'], '</div>';
if ($context['page_index'])
echo $context['page_index'];
if(!empty($context['topics']))
{
echo '</tbody></table>';
}
echo '</div>';
if(!empty($context['topics']))
{
echo'<div style="float:left;width:212px;margin-bottom:8px;"><div class="publicidad"  style="margin-bottom:8px;"><div class="box_title" style="width: 212px;"><div class="box_txt publicidad_r">Tags relacionados</div><div class="box_rss"><img alt="" src="/images/blank.gif" style="width:14px;height:12px;" border="0" /></div></div><div class="windowbg" style="width:204px;padding:4px;">';
		$request = db_query("SELECT t.ID_TAG
			FROM ({$db_prefix}tags_log AS tl INNER JOIN {$db_prefix}tags AS t ON tl.ID_TAG = t.ID_TAG) INNER JOIN {$db_prefix}messages AS m ON m.ID_TOPIC = tl.ID_TOPIC
			WHERE t.tag = '" . $context['search_params']['search'] . "'", __FILE__, __LINE__);
			$context['tags'] = array();
		while ($row = mysqli_fetch_assoc($request))
			{ $context['tags'][] = array('ID' => $row['ID_TAG']); }

		foreach ($context['tags'] as $valtags) {
		$valins = $valins.$valtags['ID'].", ";
		}
		
		$valins = substr($valins,0,strlen($valins)-2);

		$request = db_query("
		SELECT m.ID_MSG, m.subject, t.ID_TOPIC, t.ID_BOARD, m.hiddenOption, m.hiddenValue, ts.tag, ts.ID_TAG,
		b.name AS bname, b.description, t.numReplies, m.ID_MEMBER, IFNULL(mem.realName, m.posterName) AS posterName,
		mem.hideEmail, IFNULL(mem.emailAddress, m.posterEmail) AS posterEmail, m.modifiedTime
		FROM ({$db_prefix}topics AS t, {$db_prefix}messages AS m, {$db_prefix}boards AS b)
		LEFT JOIN {$db_prefix}members AS mem ON (mem.ID_MEMBER = m.ID_MEMBER), {$db_prefix}tags AS ts, {$db_prefix}tags_log AS tl 
		WHERE m.ID_MSG = t.ID_FIRST_MSG
		AND t.ID_BOARD = b.ID_BOARD
		AND m.ID_TOPIC = tl.ID_TOPIC
		AND tl.ID_TAG = ts.ID_TAG
		AND	ts.tag <> '{$context['search_params']['search']}'
		ORDER BY RAND()
		LIMIT 10
		", __FILE__, __LINE__);
		$context['posts10'] = array();
		while ($row = mysqli_fetch_assoc($request))
		{
				$context['posts10'][$row['ID_MSG']] = array(
				'tag' => $row['tag'],
				);
		}
		
		foreach($context['posts10'] as $tag) {
echo '<div class="entrybc"><a rel="dc:relation" target="_self" href="/tags/' . $tag['tag'] . '" title="' . $tag['tag'] . '"><b>' . $tag['tag'] . '</b></a></div>
<div style="clear: left;"></div>';
}
echo '</div></div><div class="publicidad"><div class="box_title" style="width: 212px;"><div class="box_txt publicidad_r">Publicidad</div><div class="box_rss"><img alt="" src="/images/blank.gif" style="width:14px;height:12px;" border="0" /></div></div><div class="windowbg" style="width:204px;padding:4px;"><center>', $modSettings['vertical'], '</center></div></div></div></div><div style="clear:both"></div>
';
}
echo '<div style="clear:both"></div>';
}

?>