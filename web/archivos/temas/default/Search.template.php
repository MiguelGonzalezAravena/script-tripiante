<?php
// Version: 1.1.1; Search

function template_main()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings;

	echo '
<script type="text/javascript">function errorr(search){if(search == \'\'){document.getElementById(\'errorss\').innerHTML=\'<br /><font class="size10" style="color: red;">Es necesario escribir una palabra para buscar.</font>\'; return false;}}</script>
<div class="box_buscador"><div class="box_title" style="width: 920px;"><div class="box_txt box_buscadort"><center>Buscador</center></div><div class="box_rss"><img alt="" src="', $settings['images_url'], '/blank.gif" style="width: 14px; height: 12px;" border="0" /></div></div><div style="width:912px;padding:4px;" class="windowbg"><form action="', $scripturl, '?action=search2" method="post" accept-charset="', $context['character_set'], '" name="searchform" id="searchform"><center>
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
	global $context, $settings, $options, $txt, $scripturl, $counter, $modSettings, $boardurl;

	if ($context['compact']) {
	
	echo '
<script type="text/javascript">function errorr(search){if(search == \'\'){document.getElementById(\'errorss\').innerHTML=\'<br /><font class="size10" style="color: red;">Es necesario escribir una palabra para buscar.</font>\'; return false;}}</script>
<div class="box_buscador"><div class="box_title" style="width: 920px;"><div class="box_txt box_buscadort"><center>Buscador</center></div><div class="box_rss"><img alt="" src="', $settings['images_url'], '/blank.gif" style="width: 14px; height: 12px;" border="0" /></div></div><div style="width:912px;padding:4px;" class="windowbg"><form action="', $scripturl, '?action=search2" method="post" accept-charset="', $context['character_set'], '" name="searchform" id="searchform"><center>
<div class="dataL">
<b class="size12">Buscar:</b><br/><input title="Busca con ', $context['forum_name'], '" type="text" onfocus="foco(this);" onblur="no_foco(this);" name="search"', !empty($context['search_params']['search']) ? ' value="' . $context['search_params']['search'] . '"' : '', ' style="width: 200px;" /></div><div class="dataR"><b class="size12">Usuario:</b><br/><input type="text" onfocus="foco(this);" onblur="no_foco(this);" name="userspec" value="', empty($context['search_params']['userspec']) ? '' : $context['search_params']['userspec'], '" style="width: 200px;" /></div>
<div class="clearBoth"></div>


<div class="dataL"><b class="size12">Orden:</b><br/><select style="width: 200px;" name="sort"><option value="ID_MSG|desc">M&aacute;s reciente</option><option value="ID_MSG|asc">M&aacute;s antiguo</option><option value="points|desc">M&aacute;s puntuados</option></select></div><div class="dataR"><b class="size12">Categor&iacute;as:</b><br/>

<select style="width:202px;" name="brd" class="select"><option value="0" selected="selected">Todas</option>';
	foreach ($context['boards'] as $board){
	echo '<option value="', $board['id'], '"', $board['selected'] ? ' selected="selected"' : '', '>', $board['name'], '</option>';}
	
echo '</select></div>

<br /><br /><label id="errorss"></label><br /><input class="login" onclick="return errorr(this.form.search.value);" style="font-size: 15px;width:200px;" value="Buscar" title="Buscar" type="submit" /></center></form></div></div>

';
if(!empty($context['topics']))
{
echo '<div style="float:left;width:700px;margin-right:8px;">
<table class="linksList" style="width:700px;"><thead><tr>
					<th>&nbsp;</th>
					<th style="text-align: left;">Mostrando <strong>', $counter, ' de 50</strong> resultados de <strong>1964</strong></th>
					<th>Fecha</th>
					<th>Puntos</th>

				</tr></thead><tbody>';
}

	if (!empty($context['search_errors']))
	{
echo '<span class="size12"><b>', implode('<br />', $context['search_errors']['messages']), '</b><br/><br/>
';
	}
		while ($topic = $context['get_topics']())
		{
		echo '<tr id="div_', $topic['id'], '">
					<td title="', 	$topic['board']['name'], '"><img title="', 	$topic['board']['name'], '" src="' . $settings['images_url'] . '/post/icono_', $topic['board']['id'], '.gif" alt="" /></td>
					<td style="text-align: left;"><a title="' , $topic['first_post']['subject'] , '" href="' . $boardurl . '/post/', $topic['id'], '/', $topic['board']['description'], '/' , ssi_amigable($topic['first_post']['subject']), '.html" class="titlePost">' , $topic['first_post']['subject'] , '</a></td>
					<td title="' , $topic['first_post']['fecha'] , '">' , $topic['first_post']['fecha'] , '</td>
					<td><span style="color:green;">' , $topic['first_post']['puntos'] , '</span></td>
				</tr>';
		}}
		/*
		<table width="100%"><tr><td width="100%"><div><div style="float: left;"><div class="box_icono4"><img title="', 	$topic['board']['name'], '" src="/Themes/default/images/post/icono_', $topic['board']['id'], '.gif"></div> <span title="' , $topic['first_post']['subject'] , '">' , $topic['first_post']['link'] , '</div><div align="right" class="opc_fav">Creado: ' , $topic['first_post']['fecha'] , ' por: ' , $topic['first_post']['name'] , ' | ' , $topic['first_post']['puntos'] , ' pts.</div></div></td></tr></table>';}}*/
if (!empty($context['topics'])){}	else
echo '<div class="noesta" style="width:922px;">', $txt['search_no_results'], '</div>';
if(!empty($context['topics'])) {
echo'</tbody></table>';
}
if ($context['page_index']) {
echo '<div class="windowbgpag" style="width:700px;">';
echo $context['page_index'];
echo '<div class="clearBoth"></div></div>';
}
echo '</div>';
if(!empty($context['topics']))
{
echo'
<div style="float:left;margin-bottom:8px;"><div class="publicidad" style="float:left;margin-bottom:8px;">

<div class="box_title" style="width:212px;"><div class="box_txt publicidad_r"><center>Publicidad</center></div>
<div class="box_rss"><img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width:14px;height:12px;" border="0" /></div></div>
<div class="windowbg" style="width:204px;padding:4px;"><center>', $modSettings['vertical'], '</center></div></div></div><br /><br />';
}
echo '<div style="clear:both"></div>';
}

?>