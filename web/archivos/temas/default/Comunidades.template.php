<?php
function rango($valor)
{
$valor = str_replace("1", "Administrador", $valor);
$valor = str_replace("2", "Moderador", $valor);
$valor = str_replace("3", "Posteador", $valor);
$valor = str_replace("4", "Comentador", $valor);
$valor = str_replace("5", "Visitante", $valor);
return $valor;
}

function rango_img($valor)
{

$valor = str_replace("1", "admin", $valor);
$valor = str_replace("2", "mod", $valor);
$valor = str_replace("3", "posteador", $valor);
$valor = str_replace("4", "comentador", $valor);
$valor = str_replace("5", "comentador", $valor);
return $valor;
}

function panel()
{
	global $context, $settings, $txt, $boardurl, $scripturl, $ID_MEMBER, $db_prefix, $modSettings;
$id	 =	htmlentities(addslashes($_REQUEST['id']));
$numMembers	=	db_query("SELECT * FROM ({$db_prefix}community_members AS cm, {$db_prefix}communities AS c) WHERE cm.ID_COMMUNITY = c.ID_COMMUNITY AND c.friendly_url = '$id' ", __FILE__, __LINE__);
$Members	=	mysql_num_rows($numMembers);
$numTopics	=	db_query("SELECT * FROM ({$db_prefix}community_topic AS ct, {$db_prefix}communities AS c) WHERE ct.ID_COMMUNITY = c.ID_COMMUNITY AND c.friendly_url = '$id' ", __FILE__, __LINE__);
$Topics	=	mysql_num_rows($numTopics);

echo '<div style="margin-bottom:8px;"><div style="margin-bottom:10px;margin-right:8px;float:left;">
<div class="box_title" style="width:160px;"><div class="box_txt box_perfil-36">Comunidad</div>
<div class="box_rss"><img alt="" src="', $settings['images_url'], '/blank.gif" style="width:16px;height:16px;" border="0" /></div></div><div class="windowbg" style="width:152px;padding:4px;"><center><a href="/comunidades/', $context['comunidad']['friendly_url'], '/"><img src="', $context['comunidad']['logo'], '" width="120px" height="120px" alt="" class="avatar" title="Logo de la comunidad" onerror="error_avatar(this)" /></a></center><br/>
<a href="/comunidades/', $context['comunidad']['friendly_url'], '/" title="', $context['comunidad']['title'], '" alt="', $context['comunidad']['title'], '"><b class="size15">', $context['comunidad']['title'], '</b></a><br/><br/><div class="hrs"></div>
<a href="/comunidades/', $context['comunidad']['friendly_url'], '/miembros">', $Members, ' Miembros</a><br/>

', $Topics, ' Temas<br/><div class="hrs"></div><br /><center>';

if($context['rango']['grade'] == 1 && $context['usercomunidad'] == 1 || $context['allow_admin']) {

echo '<input onclick="javascript:window.location.href=\'/comunidades/', $context['comunidad']['friendly_url'], '/publicitar\'" alt="" class="PublCom" title="" value=" " align="top" type="submit" /><br /><br />
<input onclick="javascript:window.location.href=\'/comunidades/', $context['comunidad']['friendly_url'], '/editar\'" alt="" class="EdiCom" title="" value=" " align="top" type="submit" /><br /><br />
<input onclick="if (!confirm(\'\xbfEstas seguro que desea ELIMINAR esta comunidad?\')) return false; javascript:window.location.href=\'/web/tp-comunidadesEliComu.php?id=', $context['comunidad']['friendly_url'], '\'" alt="" class="EliCom" title="" value=" " align="top" type="submit" /><br /><br />';
}
if($context['usercomunidad'] == 1) {
echo '<input onclick="if (!confirm(\'\xbfEstas seguro que desea abandonar esta comunidad?\')) return false; javascript:window.location.href=\'/web/tp-comunidadesAbanCom.php?id=', $context['comunidad']['friendly_url'], '\'" alt="" class="AbandCom" title="" value=" " align="top" type="submit" /><br /><br />
';
echo '<div class="hrs"></div> <img src="/images/comunidades/', rango_img($context['rango']['grade']), '.png" alt="', rango($context['rango']['grade']), '" title="', rango($context['rango']['grade']), '" /> ' , rango($context['rango']['grade']);

} else {
echo '<input onclick="javascript:window.location.href=\'/comunidades/', $context['comunidad']['friendly_url'], '/denunciar\'" alt="" class="DenCom" title="" value=" " align="top" type="submit" />';
echo '<br /><br /><input onclick="if (!confirm(\'\xbfEstas seguro que desea unirse a esta comunidad?\')) return false; javascript:window.location.href=\'/web/tp-comunidadesUnirCom.php?id=', $context['comunidad']['friendly_url'], '\'" alt="" class="unirCom" title="" value=" " align="top" type="submit" />
';
}
echo '<br /><br /></center></div><p align="center">', $modSettings['vertical'], '</p></div>
';
}

function ultimos_miembros()
{
	global $context, $txt, $db_prefix, $boardurl, $modSettings, $ID_MEMBER, $settings;

$id			=	htmlentities(addslashes($_REQUEST['id']));
$request	=	db_query("
SELECT cm.ID, cm.ID_COMMUNITY, cm.ID_MEMBER, cm.date, cm.name, c.ID_COMMUNITY, c.friendly_url
FROM ({$db_prefix}community_members AS cm, {$db_prefix}communities AS c)
WHERE cm.ID_COMMUNITY = c.ID_COMMUNITY
AND c.friendly_url = '$id'
ORDER BY cm.ID DESC
LIMIT " . $modSettings['community_members'], __FILE__, __LINE__);
$context['ultimos_miembros'] = array();
while($row = mysql_fetch_assoc($request))
	$context['ultimos_miembros'][] = array(
		'name' => $row['name'],
		'date' => timeformat($row['date']),
	);

echo '<div style="margin-bottom:8px;"><div class="box_title" style="width:201px;"><div class="box_txt box_perfil2-36">&Uacute;ltimos Miembros</div><div class="box_rss"><img alt="" src="/images/blank.gif" style="width: 14px; height: 12px;" border="0"></div></div>
<div class="windowbg" style="width:193px;padding:4px;">';
foreach($context['ultimos_miembros'] as $ultm) {
echo '<font class="size11"><b><a href="/perfil/', $ultm['name'], '" title="', $ultm['name'], '" alt="', $ultm['name'], '">', $ultm['name'], '</a></b> - ', $ultm['date'], ' </font><br style="margin: 0px; padding: 0px;">';
}
echo '</div></div>';
}

function ultimos_comentarios()
{
	global $context, $settings, $db_prefix, $modSettings;

$id			=	htmlentities(addslashes($_REQUEST['id']));
echo '<div style="float:left;margin-bottom:8px;margin-left:8px;"><div style="margin-bottom:8px;"><div class="box_title" style="width:201px;"><div class="box_txt box_perfil2-36">&Uacute;ltimos comentarios</div><div class="box_rss"><div class="icon_img"><img alt="Actualizar" onclick="actualizar_comentarios_com_id(\'', $context['comunidad']['friendly_url'], '\'); return false;" src="', $settings['images_url'], '/icons/tpbig-v1-iconos2.gif?v3.2.3" style="cursor: pointer; margin-top: -96px; display: inline;" title="Actualizar"></div></div></div><div class="windowbg" style="width:193px;padding:4px;"><span id="ult_comm">';
@require_once('web/tp-comunidadesActComc.php');
echo '</span></div></div>';	
}

function destacados()
{
	global $context, $txt, $db_prefix, $boardurl, $modSettings, $ID_MEMBER, $settings;


$request	=	db_query("
SELECT cp.ID_COMMUNITY, c.ID_COMMUNITY, c.logo, c.friendly_url, c.title, cp.expire
FROM ({$db_prefix}community_publicity AS cp, {$db_prefix}communities AS c)
WHERE c.ID_COMMUNITY = cp.ID_COMMUNITY
ORDER BY RAND()
LIMIT 1", __FILE__, __LINE__);
$context['destacados']	=	array();
$context['contar']		=	mysql_num_rows($request);
while($row = mysql_fetch_assoc($request)) {
	$context['destacados'][] = array(
		'ID_COMMUNITY' => $row['ID_COMMUNITY'],
		'title' => $row['title'],
		'logo' => $row['logo'],
		'friendly_url' => $row['friendly_url'],
		'expire' => $row['expire'],
	);
}

echo '<div style="margin-bottom:8px;"><div class="box_title" style="width:201px;"><div class="box_txt box_perfil2-36">Destacados</div><div class="box_rss"><img alt="" src="/images/blank.gif" style="width: 14px; height: 12px;" border="0"></div></div>
<div class="windowbg" style="width:193px;padding:4px;"><center>';
foreach($context['destacados'] as $des) {
if($context['contar'] == 1 && $des['expire'] < time()) {
db_query("DELETE FROM {$db_prefix}community_publicity WHERE ID_COMMUNITY = " . $des['ID_COMMUNITY']);
} elseif($context['contar'] == 1 && $des['expire'] > time()) {
echo '<a href="/comunidades/', $des['friendly_url'], '/" title="', $des['title'], '" alt="', $des['title'], '"><img src="', $des['logo'], '" width="120px" height="120px" alt="', $dest['title'], '" class="avatar" title="', $des['title'], '" onerror="error_avatar(this)" /></a>
<br/><div class="hrs"></div>
<a href="/comunidades/', $des['friendly_url'], '/" title="', $des['title'], '" alt="', $des['title'], '"><b class="size15">', $des['title'], '</b></a>';
}
}
echo '</center></div></div></div><div style="clear:both"></div>';
}

function template_main()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $db_prefix;

/* Contadores */
$contador++;
/* Contadores */
echo '<div class="tagacom2"><ul></ul></div><div style="clear: left;margin-bottom:8px;"></div><div style="text-align:left;">
<div style="float:left;height:auto;margin-right:6px;">
<div class="ultimos_postsa" style="margin-bottom:4px;">
<div class="crear_comunidad">
<div style="float:left;width:255px;height:auto;font-size:11px;"><b>', $context['forum_name'], '</b> le ofrece a cada usuario la posibilidad que el mismo pueda tener una comunidad siendo el administrador, y asi compartir gustos e intereses.</div>
<div><a href="/crear-comunidades/"><img src="', $settings['images_url'], '/comunidades/btn-crear_comunidad.png" alt="Crear Comunidad" title="Crear Comunidad"/></a></div></div>
<div class="box_title" style="width:378px;"><div class="box_txt ultimos_posts">&Uacute;ltimos temas</div>
<div class="box_rss"><div class="icon_img"><a href="/rss/ultimos-temas/"><img alt="" src="', $settings['images_url'], '/icons/tpbig-v1-iconos.gif?v3.2.3" style="cursor:pointer;margin-top:-352px;display:inline;" /></a></div></div></div>
<!-- empiezan los post -->
<div class="windowbg" style="width:370px;padding:4px;">';
$RegistrosAMostrar		=	$modSettings['community_topics_general'];
if(isset($_GET['pag'])) {
$RegistrosAEmpezar	=	($_GET['pag']-1)*$RegistrosAMostrar;
$PagAct	=	$_GET['pag'];
} else {
$RegistrosAEmpezar	=	0;
$PagAct				=	1;
}
$categoria	=	htmlentities(addslashes($_GET['categoria']), ENT_QUOTES, "UTF-8");
if(!empty($categoria)) {
$AND	=	"AND cc.friendly_url = '$categoria'";
}
$request2	= db_query("
SELECT ct.ID_TOPIC, ct.ID_COMMUNITY, ct.subject, ct.posterName, cc.ID_CATEGORY, cc.name, c.ID_COMMUNITY, c.title, c.friendly_url, c.oficial, c.view, c.ID_CATEGORY, cc.friendly_url AS friendly_url2
FROM ({$db_prefix}community_topic AS ct, {$db_prefix}community_categories AS cc, {$db_prefix}communities AS c)
WHERE ct.ID_COMMUNITY = c.ID_COMMUNITY
AND cc.ID_CATEGORY = c.ID_CATEGORY
$AND
ORDER BY ct.ID_TOPIC DESC
LIMIT {$RegistrosAEmpezar}, {$RegistrosAMostrar}", __FILE__, __LINE__);
$count	=	mysql_num_rows($request2);
if($count <= 0) {
echo '<div class="noesta">No hay temas hechos.</div>';
} else {
while($row	=	mysql_fetch_assoc($request2)) {
echo '<div class="comunidad_tema"><div>
<div style="float:left;margin-right:5px;"><img src="', $settings['images_url'], '/comunidades/categorias/', $row['friendly_url2'], '.png" alt="', $row['name'], '" title="', $row['name'], '" /></div><div><a style="color:#89601A;font-weight:bold;font-size:13px;" href="/comunidades/', $row['friendly_url'], '/', $row['ID_TOPIC'], '/', ssi_amigable($row['subject']), '.html" target="_self" title="', $row['subject'], '" alt="', $row['subject'], '">', ssi_reducir($row['subject']), '</a></div></div>

<div class="size10">En <a href="/comunidades/', $row['friendly_url'], '/" target="_self" title="', $row['title'], '" alt="', $row['title'], '">', $row['title'], '</a> por <a href="/perfil/', $row['posterName'], '" target="_self" title="', $row['posterName'], '">', $row['posterName'], '</a></div></div>
<div class="hrs"></div>
';
}
if(empty($categoria)) {
	$NroRegistros	=	mysql_num_rows(db_query("SELECT * FROM ({$db_prefix}community_topic AS ct, {$db_prefix}community_categories AS cc, {$db_prefix}communities AS c) WHERE ct.ID_COMMUNITY = c.ID_COMMUNITY AND cc.ID_CATEGORY = c.ID_CATEGORY ORDER BY ct.ID_TOPIC DESC ", __FILE__, __LINE__));
} else {
	$NroRegistros	=	mysql_num_rows(db_query("SELECT * FROM ({$db_prefix}community_topic AS ct, {$db_prefix}community_categories AS cc, {$db_prefix}communities AS c) WHERE ct.ID_COMMUNITY = c.ID_COMMUNITY AND cc.ID_CATEGORY = c.ID_CATEGORY AND cc.friendly_url = '$categoria' ORDER BY ct.ID_TOPIC DESC ", __FILE__, __LINE__));
}
}

 $PagAnt=$PagAct-1;
 $PagSig=$PagAct+1;
 $PagUlt=$NroRegistros/$RegistrosAMostrar;
 $Res=$NroRegistros%$RegistrosAMostrar;
 if($Res>0) $PagUlt=floor($PagUlt)+1;
echo '</div><div class="windowbgpag" style="width:378px;">
';
if(empty($categorias)) {
	if($PagAct>1) echo '<a href="/comunidades/pag-' . $PagAnt . '">&#171; anterior</a>';
	if($PagAct<$PagUlt)  echo '<a href="/comunidades/pag-' . $PagSig . '">siguiente &#187;</a>';
} else {
	if($PagAct>1) echo '<a href="/comunidades/' . $categoria . '/pag-' . $PagAnt . '">&#171; anterior</a>';
	if($PagAct<$PagUlt)  echo '<a href="/comunidades/' . $categoria . '/pag-' . $PagSig . '">siguiente &#187;</a>';
}
echo '</div></div></div></div>';
echo '<div style="float:left;margin-right:8px;">

<div class="act_comments"><div class="box_title" style="width:361px;"><div class="box_txt ultimos_comments">TOPs comunidades</div><div class="box_rss"><img alt="" src="/images/blank.gif" style="width:16px;height:16px;" border="0" /></div></div><div class="windowbg" style="padding:4px;width:353px;margin-bottom:8px;">';
foreach ($context['tops_comunidades'] as $tops) {
echo '<div class="comunidad_tema"><div><div style="float:left;margin-right:5px;"><img src="' . $settings['images_url'] . '/comunidades/categorias/', $tops['friendly_url2'], '.png" alt="" title="" alt="" /></div><div><a style="color:#89601A;font-weight:bold;font-size:13px;" href="/comunidades/', $tops['friendly_url'], '/" target="_self" title="', $tops['title'], '" alt="', $tops['title'], '">', ssi_reducir($tops['title']), '</a> (', $tops['cuenta'], ' miembros)</div></div></div>
<div class="hrs"></div>';
}
echo '</div></div>
<div class="act_comments"><div class="box_title" style="width:361px;"><div class="box_txt ultimos_comments">&Uacute;ltimas comunidades creadas</div><div class="box_rss"><img alt="" src="/images/blank.gif" style="width:16px;height:16px;" border="0" /></div></div><div class="windowbg" style="padding:4px;width:353px;margin-bottom:8px;">
';
foreach ($context['ultimas_comunidades'] as $ultimas_comunidades) {
echo '<div class="comunidad_tema"><div><div style="float:left;margin-right:5px;"><img src="', $settings['images_url'], '/comunidades/categorias/', $ultimas_comunidades['friendly_url'], '.png" alt="" title="', $ultimas_comunidades['name'], '" alt="', $ultimas_comunidades['name'], '" /></div><div><a style="color:#89601A;font-weight:bold;font-size:13px;" href="/comunidades/', $ultimas_comunidades['friendly_url2'], '/" target="_self" title="', $ultimas_comunidades['title'], '" alt="', $ultimas_comunidades['title'], '">', ssi_reducir($ultimas_comunidades['title']), '</a></div></div>
<div class="size10">Comunidad creada por <a href="/perfil/', $ultimas_comunidades['realName'], '" target="_self" title="', $ultimas_comunidades['realName'], '" alt="', $ultimas_comunidades['realName'], '">', $ultimas_comunidades['realName'], '</a> | ', $ultimas_comunidades['date'], '</a></div></div>
<div class="hrs"></div>
';
}

echo '</div></div>
<div class="act_comments"><div class="box_title" style="width:361px;"><div class="box_txt ultimos_comments">&Uacute;ltimos comentarios</div><div class="box_rss"><div class="icon_img"><img alt="Actualizar" onclick="actualizar_comentarios_com(); return false;" src="', $settings['images_url'], '/icons/tpbig-v1-iconos2.gif?v3.2.3" style="cursor: pointer; margin-top: -96px; display: inline;" title="Actualizar"></div></div></div><div class="windowbg" style="padding:4px;width:353px;margin-bottom:8px;"><span id="ult_comm">';
ssi_respuestas();
echo '</span></div></div>';
echo '<div class="act_comments"><div class="box_title" style="width:361px;"><div class="box_txt ultimos_comments">Destacados</div><div class="box_rss"><img alt="" src="/images/blank.gif" style="width:16px;height:16px;" border="0" /></div></div><div class="windowbg" style="padding:4px;width:353px;margin-bottom:8px;"><center><p align="center" style="margin:0px;padding:0px;">';
ssi_destacados();
echo '</p></center></div></div></div>';

echo '<div style="float:left;">
<div class="img_aletat">

<div class="box_title" style="width: 163px;"><div class="box_txt img_aletat">Destacados</div><div class="box_rss"><img alt="" src="/images/blank.gif" style="width:16px;height:16px;" border="0" /></div></div>
<div class="windowbg" style="padding:4px;width:155px;margin-bottom:8px;"><center>';
$request	=	db_query("
SELECT cp.ID_COMMUNITY, c.ID_COMMUNITY, c.logo, c.friendly_url, c.title, cp.expire
FROM ({$db_prefix}community_publicity AS cp, {$db_prefix}communities AS c)
WHERE c.ID_COMMUNITY = cp.ID_COMMUNITY
ORDER BY RAND()
LIMIT 1", __FILE__, __LINE__);
$context['destacados']	=	array();
$context['contar']		=	mysql_num_rows($request);
while($row = mysql_fetch_assoc($request)) {
	$context['destacados'][] = array(
		'ID_COMMUNITY' => $row['ID_COMMUNITY'],
		'title' => $row['title'],
		'logo' => $row['logo'],
		'friendly_url' => $row['friendly_url'],
		'expire' => $row['expire'],
	);
}
if($contar == 0) {
foreach($context['destacados'] as $des) {
if($context['contar'] == 1 && $des['expire'] < time()) {
db_query("DELETE FROM {$db_prefix}community_publicity WHERE ID_COMMUNITY = " . $des['ID_COMMUNITY']);
} elseif($context['contar'] == 1 && $des['expire'] > time()) {
echo '<a href="/comunidades/', $des['friendly_url'], '/" title="', $des['title'], '" alt="', $des['title'], '"><img src="', $des['logo'], '" width="120px" height="120px" alt="', $des['title'], '" class="avatar" title="', $des['title'], '" onerror="error_avatar(this)" /></a>
<br/><div class="hrs"></div>
<a href="/comunidades/', $des['friendly_url'], '/" title="', $des['title'], '" alt="', $des['title'], '"><b class="size15">', $des['title'], '</b></a>';
}
}
}
echo '</center></div></div>
<div class="img_aletat">
<div class="box_title" style="width: 163px;"><div class="box_txt img_aletat">Publicidad</div><div class="box_rss"><img alt="" src="/images/blank.gif" style="width:16px;height:16px;" border="0" /></div></div>
<div class="windowbg" style="padding:4px;width:155px;margin-bottom:8px;"><center>', $modSettings['vertical'], '</center></div></div></div><div style="clear:both"></div>';

}

function template_crear()
{
	global $context, $scripturl, $txt, $settings, $boardurl, $ID_MEMBER, $db_prefix, $boardurl;

echo '<div class="tagacom2"><ul><li><a href="/comunidades/" title="Comunidades">Comunidades</a></li><li id="activer">Crear Comunidad</li></ul></div><div style="clear: left;margin-bottom:8px;"></div>';

echo '<div style="clear: left;margin-bottom:8px;"></div><div style="width:354px;float:left;margin-right:8px;">
<div class="box_354" style="margin-bottom:8px;">
<div class="box_title" style="width:352px;"><div class="box_txt box_354-34">Importante</div><div class="box_rss"><div class="icon_img"><img src="', $settings['images_url'], '/blank.gif" style="width:16px;height:16px;" border="0" alt="" /></div></div></div><div style="width:344px;padding:4px;" class="windowbg">Antes de crear una comunidad es importante que leas el <a href="/protocolo/">protocolo</a>.<br /><br />
Al crear la comunidad vas a ser due&ntilde;o/Administrador de tal por lo tanto tendr&aacute;s todos los permisos de un Administrador.<br /><br />
Podes crear tu propio protocolo para tu comunidad, pero siempre respetando el protocolo general.<br /><br />

Si tenes dudas sobre las comunidades visita <a href="' . $boardurl. '/ayuda/categoria/comunidades/">este enlace</a>.</div></div>

<div class="noesta-am" style="margin-bottom:8px;">Tienes ';
$request	=	db_query("SELECT * FROM {$db_prefix}communities WHERE ID_MEMBER = " . $context['user']['id'], __FILE__, __LINE__);
$count		=	 mysql_num_rows($request);
ssi_grupos();
if($context['Turista']) {
echo 1 - $count;
} elseif($context['Conocido']) {
echo 2 - $count;
} elseif($context['Vecino']) {
echo 4 - $count;
} elseif($context['Amigo']) {
echo 6 - $count;
} elseif($context['Familiar']) {
echo 8 - $count;
} elseif($context['Casero']) {
echo 10 - $count;
} elseif($context['Abastecedor']) {
echo 15 - $count;
} elseif($context['Heredero']) {
echo 15 - $count;
} elseif($context['Hermano Mayor']) {
echo 15 - $count;
} elseif($context['Padre']) {
echo 15 - $count;
}
echo ' comunidades disponibles para crear</div>

<div class="box_354"><div class="box_title" style="width:352px;"><div class="box_txt box_354-34">Destacados</div><div class="box_rss"><div class="icon_img"><img src="', $settings['images_url'], '/blank.gif" style="width:16px;height:16px;" border="0" alt="" /></div></div></div><div style="width:344px;padding:4px;" class="windowbg"><p align="center">', ssi_destacados(), '</p></div></div></div>
';
echo '<div style="width:560px;float:left;"><div class="box_560"><div class="box_title" style="width: 558px;"><div class="box_txt box_560-34">Crear nueva comunidad</div><div class="box_rss"><div class="icon_img"><img src="/images/blank.gif" style="width:16px;height:16px;" border="0" alt="" /></div></div></div>
<div class="windowbg" style="width:550px;padding:4px;"><form name="add_comunidad" method="post" action="/comunidades/creando/"><div class="form-container"><div class="dataL"><label for="uname">Nombre de la comunidad</label><input onfocus="foco(this);" onblur="no_foco(this);" class="c_input" value="" name="nombre" tabindex="1" datatype="text" dataname="Nombre" type="text" /></div><div class="dataR"><label for="uname" style="float:left;">Nombre corto</label><span class="gif_cargando" id="shortname" style="top:0px;float:right;display:none;"><img src="/images/icons/cargando.gif" alt="" /></span><input onfocus="foco(this);" class="c_input" value="" name="shortname" tabindex="2" onkeyup="com.crear_shortname_key(this.value)" onblur="no_foco(this);com.crear_shortname_check(this.value)" datatype="text" dataname="Nombre corto" style="width:254px;" type="text" /><div class="desform">URL de la comunidad: <br /><strong>' . $boardurl . '/comunidades/<span id="preview_shortname"></span></strong></div><span id="msg_crear_shortname"></span></div><div class="clearBoth"></div><div class="dataL"><label for="uname">Imagen</label><input onfocus="foco(this);" onblur="no_foco(this);" class="c_input" value="http://" name="imagen" tabindex="3" datatype="url" dataname="Imagen" type="text" /></div><div class="dataR"><span class="gif_cargando floatR" id="subcategoria" style="top: 0px;"></span><label for="fname">Categoria</label><select style="width:264px;margin-top:5px; height: 25px;vertical-align:middle;" name="categoria">

<option value="-1" selected="true">Elegir una categor&iacute;a</option>';
foreach ($context['foro'] as $foro) {
echo '<option value="', $foro['ID_CATEGORY'] ,'">', $foro['name'] ,'</option>
';
}
echo '</select></div><div class="clearBoth"></div>
<div class="data"><label for="uname">Descripci&oacute;n</label><textarea onfocus="foco(this);" onblur="no_foco(this);" class="c_input_desc autogrow" style="display:block;width:540px;" name="descripcion" tabindex="7" datatype="text" dataname="Descripcion"></textarea></div></div><hr style="clear: both; margin-bottom: 15px; margin-top: 20px;" class="divider" /><div class="dataL dataRadio"><label for="lname"><b>Acceso</b></label><div class="postLabel"><br />

<fieldset><legend><label for="privada_1" class="tit_lab">Todos</label></legend>
<table><tr><td style="width:18px;padding:0px;margin:0px;"><input name="privada" id="privada_1" value="1" checked="checked" tabindex="9" type="radio" /></td><td><p class="descRadio">Toda persona que entra a ', $context['forum_name'], ' tiene la posibilidad de entrar y ver el contenido de tu comunidad.</p></td></tr></table></fieldset>

<fieldset><legend><label for="privada_2" class="tit_lab">S&oacute;lo usuarios registrados</label></legend><table><tr><td style="width:18px;padding:0px;margin:0px;"><input name="privada" id="privada_2" value="2" type="radio" /></td><td><p class="descRadio">Todo aquel que no este registrado en ' . $context['forum_name'] . ' no podr&aacute; ver el contenido de tu comunidad.</p></td></tr></table></fieldset> 
</div></div>
<div class="dataR dataRadio" id="rango_default"><label for="fname"><b>Permisos</b></label><div class="postLabel"><br />
<fieldset><legend><label for="permisos_1" class="tit_lab">Posteador</label></legend>	
<table><tr><td style="width:18px;padding:0px;margin:0px;"><input name="rango_default" id="permisos_1" value="3" checked="checked" tabindex="11" type="radio" /></td><td><p class="descRadio">Los usuarios al ingresar en tu comunidad podr&aacute;n comentar y crear temas.</p></td></tr></table></fieldset>

<fieldset><legend><label for="permisos_2" class="tit_lab">Comentador</label></legend><table><tr><td style="width:18px;padding:0px;margin:0px;"><input name="rango_default" id="permisos_2" value="4" type="radio" /></td><td><p class="descRadio">Los usuarios al participar en tu  comunidad s&oacute;lo podr&aacute;n comentar pero no estar&aacute;n habilitados para crear nuevos temas.</p></td></tr></table></fieldset>
<fieldset><legend><label for="permisos_3" class="tit_lab">Visitante</label></legend><table><tr><td style="width:18px;padding:0px;margin:0px;"><input name="rango_default" id="permisos_3" value="5" type="radio" /></td><td>
<p class="descRadio">Los usuarios al participar en tu comunidad no podr&aacute;n comentar ni tampoco crear temas.</p></td></tr></table></fieldset></div>
<fieldset><legend class="tit_lab">Nota:</legend><p class="descRadio">
El permiso seleccionado se le asignar&aacute; autom&aacute;ticamente al usuario que se haga miembro, sin embargo, podr&aacute;s modificar el permiso a cada usuario especifico.</p>

</fieldset></div>
<hr style="clear: both; margin-bottom: 15px; margin-top: 20px;" class="divider" /><div id="buttons" align="right"><input tabindex="14" title="Crear comunidad" value="Crear comunidad" class="login" name="Enviar" type="submit" /></div></form></div></div><div style="clear:both"></div>';

}


function template_crear2(){}
function template_editar()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $boardurl;

	if($context['comunidad']['view'] == 2 && $context['user']['is_guest']) {
	$context['page_title']	=	$txt[18];
	echo '<div class="tagacom2"><ul style="margin-bottom:8px;"><li><a href="/comunidades/" title="Comunidades">Comunidades</a></li><li><a href="/comunidades/categoria/', $context['comunidad']['friendly_url2'], '" title="', $context['comunidad']['bname'], '" alt="', $context['comunidad']['bname'], '">', $context['comunidad']['bname'], '</a></li> <li><a href="/comunidades/', $context['comunidad']['friendly_url'], '" title="', $context['comunidad']['title'], '" alt="', $context['comunidad']['title'], '">', $context['comunidad']['title'], '</a></li> <li id="activer">Editar Comunidad</li><div style="clear: both;"></div></ul></div>';
	fatal_error('S&oacute;lo usuarios registrados tienen acceso a esta comunidad.-', false);

	} else {
echo '<div class="tagacom2"><ul style="margin-bottom:8px;"><li><a href="/comunidades/" title="Comunidades">Comunidades</a></li><li><a href="/comunidades/categoria/', $context['comunidad']['friendly_url2'], '" title="', $context['comunidad']['bname'], '" alt="', $context['comunidad']['bname'], '">', $context['comunidad']['bname'], '</a></li> <li><a href="/comunidades/', $context['comunidad']['friendly_url'], '" title="', $context['comunidad']['title'], '" alt="', $context['comunidad']['title'], '">', $context['comunidad']['title'], '</a></li> <li id="activer">Editar Comunidad</li><div style="clear: both;"></div></ul></div>';
echo '<div style="width:354px;float:left;margin-right:8px;">
<div class="box_354"><div class="box_title" style="width:352px;"><div class="box_txt box_354-34">Destacados</div><div class="box_rss"><div class="icon_img"><img src="/images/blank.gif" style="width:16px;height:16px;" border="0" alt="" /></div></div></div><div style="width:344px;padding:4px;" class="windowbg"><p align="center">', ssi_destacados(), '</p></div></div></div>
';
echo '<div style="width:560px;float:left;">
<div class="box_560"><div class="box_title" style="width: 558px;"><div class="box_txt box_560-34">Editar Comunidad</div><div class="box_rss"><div class="icon_img"><img src="/images/blank.gif" style="width:16px;height:16px;" border="0" alt="" /></div></div></div>

<div class="windowbg" style="width:550px;padding:4px;">
<form name="add_comunidad" method="post" action="' . $boardurl . '/?action=comunidades;sa=editar1">
<div class="form-container">
<div class="dataL"><label for="uname">Nombre de la comunidad</label><input onfocus="foco(this);" onblur="no_foco(this);" class="c_input" value="',  $context['editor']['title'], '" name="nombre" tabindex="1" datatype="text" dataname="Nombre" type="text" /></div><div class="dataR"><label for="uname" style="float:left;">Nombre corto</label>
<br/>
<div style="padding-top:6px;"><strong style="color:green;">', $boardurl, '/comunidades/',  $context['editor']['friendly_url'], '</strong></div>
</div><div class="clearBoth"></div><div class="dataL">
<label for="uname">Imagen</label><input onfocus="foco(this);" onblur="no_foco(this);" class="c_input" value="', $context['editor']['logo'], '" name="imagen" tabindex="3" datatype="url" dataname="Imagen" type="text" /></div><div class="dataR"><span class="gif_cargando floatR" id="subcategoria" style="top: 0px;"></span><label for="fname">Categoria</label><select style="width:264px;margin-top:5px; height: 25px;vertical-align:middle;" name="categoria">
<option value="-1">Elegir una categor&iacute;a</option>';
foreach($context['foro'] as $for) {
echo'<option value="', $for['ID_CATEGORY'] ,'"' . ($context['editor']['ID_CATEGORY'] == $for['ID_CATEGORY']  ? ' selected="selected"' : '') .'>', $for['name'] ,'</option>';
}
echo '</select></div><div class="clearBoth"></div><div class="data"><label for="uname">Descripci&oacute;n</label><textarea onfocus="foco(this);" onblur="no_foco(this);" class="c_input_desc autogrow" style="display:block;width:540px;" name="descripcion" tabindex="7" datatype="text" dataname="Descripcion">', $context['editor']['description'], '</textarea></div></div><hr style="clear: both; margin-bottom: 15px; margin-top: 20px;" class="divider" />

<div class="dataL dataRadio"><label for="lname"><b>Acceso</b></label><div class="postLabel"><br />
<fieldset><legend><label for="privada_1" class="tit_lab">Todos</label></legend>
<table><tr><td style="width:18px;padding:0px;margin:0px;"><input name="privada" id="privada_1" value="1" ' . ($context['editor']['view'] == 1  ? 'checked="checked" ' : '') .'type="radio" /></td><td><p class="descRadio">Toda persona que entra a ', $context['forum_name'], ' tiene la posibilidad de entrar y ver el contenido de tu comunidad.</p></td></tr></table></fieldset>
<fieldset><legend><label for="privada_2" class="tit_lab">S&oacute;lo usuarios registrados</label></legend><table><tr><td style="width:18px;padding:0px;margin:0px;"><input name="privada" id="privada_2" value="2" ' . ($context['editor']['view'] == 2  ? 'checked="checked" ' : '') .'type="radio" /></td><td><p class="descRadio">Todo aquel que no este registrado en ', $context['forum_name'], ' no podr&aacute; ver el contenido de tu comunidad.</p></td></tr></table></fieldset></div></div><div class="dataR dataRadio" id="rango_default"><label for="fname">

<b>Permisos</b></label><div class="postLabel"><br /><fieldset><legend><label for="permisos_1" class="tit_lab">Posteador</label></legend><table><tr><td style="width:18px;padding:0px;margin:0px;"><input name="rango_default" id="permisos_1" value="3" ' . ($context['editor']['grade'] == 3  ? 'checked="checked" ' : '') .'tabindex="11" type="radio" /></td><td><p class="descRadio">Los usuarios al ingresar en tu comunidad podr&aacute;n comentar y crear temas.</p></td></tr></table></fieldset>

<fieldset><legend><label for="permisos_2" class="tit_lab">Comentador</label></legend><table><tr><td style="width:18px;padding:0px;margin:0px;"><input name="rango_default" id="permisos_2" value="4" ' . ($context['editor']['grade'] == 4  ? 'checked="checked" ' : '') .'type="radio" /></td><td><p class="descRadio">Los usuarios al participar en tu  comunidad s&oacute;lo podr&aacute;n comentar pero no estar&aacute;n habilitados para crear nuevos temas.</p></td></tr></table></fieldset>
<fieldset><legend><label for="permisos_3" class="tit_lab">Visitante</label></legend><table><tr><td style="width:18px;padding:0px;margin:0px;"><input name="rango_default" id="permisos_3" value="5" ' . ($context['editor']['grade'] == 5  ? 'checked="checked" ' : '') .'type="radio" /></td><td>
<p class="descRadio">Los usuarios al participar en tu comunidad no podr&aacute;n comentar ni tampoco crear temas.</p></td></tr></table></fieldset></div>
<fieldset><legend class="tit_lab">Nota:</legend><p class="descRadio">
El permiso seleccionado se le asignar&aacute; autom&aacute;ticamente al usuario que se haga miembro, sin embargo, podr&aacute;s modificar el permiso a cada usuario especifico.</p>

</fieldset></div><hr style="clear: both; margin-bottom: 15px; margin-top: 20px;" class="divider" /><div id="buttons" align="right">
<input value="', $context['editor']['ID_COMMUNITY'], '" name="idcom" type="hidden" />
<input tabindex="14" title="Editar comunidad" value="Editar comunidad" class="login" name="Enviar" type="submit" /></div></form></div></div><div style="clear:both"></div>';
}
}

function template_editar2()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings;
echo'
<div class="comunidad_titulo1" style="width:500px; margin-left: 200px;">Comunidad</div>
<div class="comunidad_bloke1" style="width:500px; margin-left: 200px;">
<center><b><font color="green">Felicidades, tu comunidad se ha edito con exito!</font></b><br><br><br>
<span class="boton3" style="width:50px;"><a href="' . $scripturl . '?action=comunidades;sa=comunidad;id=' . $context['editor']['id_comunidad'] . '">Volver</a></span></center>
<br></div>';
}

function template_borrar()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings;
echo'
<div class="comunidad_titulo1" style="width:500px; margin-left: 200px;">Borrar Comunidad</div>
<div class="comunidad_bloke1" style="width:500px; margin-left: 200px;">

<form method="POST" name="cprofile" id="cprofile" action="' . $scripturl . '?action=comunidades;sa=borrar2" >
<br>
<b>Titulo:</b> ' . $context['editor']['titulo'] . '
<br>
<b>Miembros: </b>' . $context['editor']['miembros'] . '
<br>
<b>Descripci&oacute;n: </b>
' . $context['editor']['descripcion'] . '
<BR><BR><BR>

<center><input  class="boton3" type="submit" value="Eliminar Comunidad" name="submit" />
<span class="boton3" style="width:50px;"><a href="' . $scripturl . '?action=comunidades;sa=comunidad;id=' . $context['editor']['id_comunidad'] . '">Volver</a></span></center>
</form>
</div>
';
}

function template_comunidad()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $db_prefix, $boardurl, $ID_MEMBER;


	echo '<div class="tagacom2"><ul style="margin-bottom:8px;"><li><a href="/comunidades/" title="Comunidades">Comunidades</a></li> <li><a href="/comunidades/categoria/', $context['comunidad']['friendly_url2'], '" title="', $context['comunidad']['bname'], '" alt="', $context['comunidad']['bname'], '">', $context['comunidad']['bname'], '</a></li> <li id="activer">', $context['comunidad']['title'], '</li><div style="clear: both;"></div></ul></div>';
$request	=	db_query("SELECT * FROM {$db_prefix}community_banned WHERE ID_COMMUNITY = " . $context['comunidad']['ID_COMMUNITY'] . " AND ID_MEMBER = $ID_MEMBER", __FILE__, __LINE__);
$ban		=	mysql_num_rows($request);
$row		=	mysql_fetch_assoc($request);

if($context['comunidad']['view'] == 2 && $context['user']['is_guest']) {
$context['page_title']	=	$txt[18];
fatal_error('S&oacute;lo usuarios registrados tienen acceso a esta comunidad.-', false);
} elseif($ban >= 1 && $row['expire'] < time()) {
db_query("DELETE FROM {$db_prefix}community_banned WHERE ID_COMMUNITY = " . $context['comunidad']['ID_COMMUNITY'] . " AND ID_MEMBER = $ID_MEMBER", __FILE__, __LINE__);
redirectexit($boardurl . '/comunidades/' . $context['comunidad']['friendly_url']);
} elseif($ban >= 1 && $row['expire'] > time()) {
echo '<div align="center"><div class="box_errors"><div class="box_title" style="width: 388px"><div class="box_txt box_error" align="left">&iexcl;Atenci&oacute;n!</div><div class="box_rss"><img alt="" src="/images/blank.gif" style="width:14px;height:12px;" border="0" /></div></div><div class="windowbg" style="width:380px;padding:4px;"><br /><center><b style="color:red;">Tu cuenta en esta comunidad se encuentra baneada.</b><br/> 
<b>Raz&oacute;n:</b> ', $row['reason'], '<br/> 
<b>Por:</b> ', $row['modName'], '<br/> 
<b>Expira:</b> ', $row['day'], '&nbsp;d&iacute;a(s)</center><br /><br /><input class="login" style="font-size: 11px;" type="submit" title="Ir a la P&aacute;gina principal" value="Ir a la P&aacute;gina principal" onclick="location.href=\'', $boardurl, '/\'" /><br /><br /></div></div><br /><div align="center"><p align="center" style="padding:0px;margin:0px;"><br />Publicidad</p></div></div><div style="clear:both"></div>';
} elseif($ban <= 0) {
panel();
$id					=	htmlentities(addslashes($_REQUEST['id']));
echo '<div style="margin-bottom:8px;float:left;"><div class="box_title" style="width:539px;"><div class="box_txt">', $context['comunidad']['title'], '</div><div class="box_rss"><img alt="" src="', $settings['images_url'], '/blank.gif" style="width: 14px; height: 12px;" border="0"></div></div><div class="windowbg" style="width:531px;padding:4px;"><table>
<tr><td valign="top" style="padding:4px;font-size:13px;"><b>Descripci&oacute;n:</b></td> <td style="padding:4px;width:360px;white-space:pre-wrap;overflow:hidden;display:block;height:100%;background-color:#FFF;border: solid 1px #BDCFE1;">', $context['comunidad']['description'], '</td></tr>
<tr><td valign="top" style="padding:4px;font-size:13px;"><b>Categor&iacute;a:</b></td> <td  style="padding:4px;">', $context['comunidad']['link_category'], '</td></tr>
<tr><td valign="top" style="padding:4px;font-size:13px;"><b>Comunidad creada el:</b></td> <td  style="padding:4px;" title="', timeformat($context['comunidad']['date']), '">', timeformat($context['comunidad']['date']), '</td></tr>
<tr><td valign="top" style="padding:4px;font-size:13px;"><b>Due&ntilde;o:</b></td> ', $context['comunidad']['creator'], '</tr>
</table></div>';
$request	=	db_query("SELECT * FROM ({$db_prefix}communities AS c, {$db_prefix}community_topic AS ct) WHERE c.ID_COMMUNITY = ct.ID_COMMUNITY AND c.friendly_url = '$id' AND ct.isSticky = 1 ", __FILE__, __LINE__);
$sticky		=	mysql_num_rows($request);
echo '<div class="box_title" style="width:539px;margin-top:8px;"><div class="box_txt">Temas Fijados</div><div class="box_rss"><img alt="" src="', $settings['images_url'], '/blank.gif" style="width: 14px; height: 12px;" border="0"></div></div><div class="windowbg" style="width:531px;padding:4px;margin-bottom:4px;">';

if($sticky == 0) {
echo '<div class="noesta">No hay temas fijados.</div>';
} else {
$fijados	=	db_query("
SELECT ct.ID_COMMUNITY, ct.ID_TOPIC, ct.locked, ct.isSticky, ct.ID_MEMBER, ct.subject, ct.posterName, ct.posterTime, ct.numViews, c.ID_COMMUNITY, c.friendly_url
FROM ({$db_prefix}community_topic AS ct, {$db_prefix}communities AS c)
WHERE ct.ID_COMMUNITY = c.ID_COMMUNITY
AND c.friendly_url = '$id'
AND ct.isSticky = 1
ORDER BY ct.ID_TOPIC DESC
LIMIT 10
", __FILE__, __LINE__);
while($row2 = mysql_fetch_assoc($fijados)) {
echo '<div class="comunidad_tema"><div>

<div style="float:left;margin-right:5px;"><img src="', $settings['images_url'], '/comunidades/temas_fijo.png" alt="" title="Anuncio" /></div><div><a style="color:#89601A;font-weight:bold;font-size:13px;" href="/comunidades/', $row2['friendly_url'], '/', $row2['ID_TOPIC'], '/', ssi_amigable($row2['subject']), '.html" target="_self" title="', $row2['subject'], '" alt="', $row2['subject'], '">', ssi_reducir($row2['subject']), '</a></div></div><div class="size10">Por <a href="/perfil/', $row2['posterName'], '" target="_self" title="', $row2['posterName'], '" alt="', $row2['posterName'], '">', $row2['posterName'], '</a></div></div><div class="hrs"></div>
';
}
}
echo '</div>';
if($context['user']['is_logged'] && $context['rango']['grade'] == 1 || $context['rango']['grade'] == 2 || $context['rango']['grade'] == 3 || $context['allow_admin'] ) {
echo '<p align="right" style="padding:0px;margin:0px;"><input onclick="javascript:window.location.href=\'/comunidades/', $context['comunidad']['friendly_url'], '/crear-tema\'" alt="" class="comCrearTema" title="" value=" " type="submit" align="top" /></p>';
}
echo '<div class="box_title" style="width:539px;margin-top:4px;"><div class="box_txt">Temas</div><div class="box_rss"><img alt="" src="', $settings['images_url'], '/blank.gif" style="width: 14px; height: 12px;" border="0"></div></div><div class="windowbg" style="width:531px;padding:4px;">';

$temas	=	db_query("SELECT * FROM {$db_prefix}community_topic WHERE ID_COMMUNITY = " . $context['comunidad']['ID_COMMUNITY'], __FILE__, __LINE__);
$row		=	mysql_num_rows($temas);
if($row > 0) {
template_ultimospost();
} else {
echo '<div class="noesta">No hay temas creados.</div>';
}
echo '</div></div>
';
ultimos_comentarios();
ultimos_miembros();
destacados();
}
}

function template_ultimospost()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings;

@require_once('SSI.php');
$id					=	htmlentities(addslashes($_REQUEST['id']));

$RegistrosAMostrar	=	$modSettings['community_topics'];
if(isset($_GET['pag'])) {
$RegistrosAEmpezar	=	($_GET['pag'] - 1) * $RegistrosAMostrar;
$PagAct				=	$_GET['pag'];
} else {
$RegistrosAEmpezar	=	0;
$PagAct				=	1;
}
$dbresult2 = db_query("
SELECT ct.ID_COMMUNITY, ct.ID_TOPIC, ct.locked, ct.isSticky, ct.ID_MEMBER, ct.subject, ct.posterName, ct.posterTime, ct.numViews, c.ID_COMMUNITY , c.friendly_url
FROM ({$db_prefix}community_topic AS ct, {$db_prefix}communities AS c)
WHERE ct.ID_COMMUNITY = c.ID_COMMUNITY
AND c.friendly_url = '$id'
ORDER BY ct.ID_TOPIC DESC
LIMIT {$RegistrosAEmpezar}, {$RegistrosAMostrar}", __FILE__, __LINE__);
while ($row = mysql_fetch_assoc($dbresult2)) {
echo '<div class="comunidad_tema"><div>

<div style="float:left;margin-right:5px;"><img src="', $settings['images_url'] ,'/comunidades/temas.png" alt="" title="', $row['subject'], '" alt="', $row['subject'], '" /></div><div><a style="color:#89601A;font-weight:bold;font-size:13px;" href="/comunidades/', $row['friendly_url'], '/', $row['ID_TOPIC'], '/', ssi_amigable($row['subject']), '.html" target="_self" title="', $row['subject'], '" alt="', $row['subject'], '">', ssi_reducir($row['subject']), '</a></div></div>
<div class="size10">Por <a href="/perfil/', $row['posterName'], '" target="_self" title="', $row['posterName'], '" alt="', $row['posterName'], '">', $row['posterName'], '</a></div></div>
<div class="hrs"></div>
';
}
 $NroRegistros	=	mysql_num_rows(mysql_query("
SELECT ct.ID_COMMUNITY, ct.ID_TOPIC, ct.locked, ct.isSticky, ct.ID_MEMBER, ct.subject, ct.posterName, ct.posterTime, ct.numViews, c.ID_COMMUNITY , c.friendly_url
FROM ({$db_prefix}community_topic AS ct, {$db_prefix}communities AS c)
WHERE ct.ID_COMMUNITY = c.ID_COMMUNITY
AND c.friendly_url = '$id'
ORDER BY ct.ID_TOPIC DESC "));
$PagAnt=$PagAct-1;
$PagSig=$PagAct+1;
$PagUlt=$NroRegistros/$RegistrosAMostrar;
$Res=$NroRegistros%$RegistrosAMostrar;
if($Res>0) $PagUlt=floor($PagUlt)+1;
echo '<p align="right" style="margin:0px;padding:0px;"><div align="right" class="paginacion2"><b>
';
if($PagAct>1) { echo '<a href="/comunidades/', $context['comunidad']['friendly_url'], '/pag-', $PagAnt, '">&#171;</a>'; }
if($PagAct<$PagUlt) { echo '<a href="/comunidades/', $context['comunidad']['friendly_url'], '/pag-', $PagSig, '">&#187;</a>'; }
echo '</b></div></p>';
}

function template_vermiembros()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $boardurl, $ID_MEMBER;
	
echo '<div class="tagacom2"><ul style="margin-bottom:8px;"><li><a href="/comunidades/" title="Comunidades">Comunidades</a></li> <li><a href="/comunidades/categoria/', $context['comunidad']['friendly_url2'], '" title="', $context['comunidad']['bname'], '" alt="', $context['comunidad']['bname'], '">', $context['comunidad']['bname'], '</a></li> <li><a href="/comunidades/', $context['comunidad']['friendly_url'], '" title="', $context['comunidad']['title'], '">', $context['comunidad']['title'], '</a></li> <li id="activer">Miembros</li><div style="clear: both;"></div></ul></div>';
$request	=	db_query("SELECT * FROM {$db_prefix}community_banned WHERE ID_COMMUNITY = " . $context['comunidad']['ID_COMMUNITY'] . " AND ID_MEMBER = $ID_MEMBER", __FILE__, __LINE__);
$ban		=	mysql_num_rows($request);
$row		=	mysql_fetch_assoc($request);

if($context['comunidad']['view'] == 2 && $context['user']['is_guest']) {
$context['page_title']	=	$txt[18];
fatal_error('S&oacute;lo usuarios registrados tienen acceso a esta comunidad.-', false);
} elseif($ban >= 1 && $row['expire'] < time()) {
db_query("DELETE FROM {$db_prefix}community_banned WHERE ID_COMMUNITY = " . $context['comunidad']['ID_COMMUNITY'] . " AND ID_MEMBER = $ID_MEMBER", __FILE__, __LINE__);
redirectexit($boardurl . '/comunidades/' . $context['comunidad']['friendly_url']);
} elseif($ban >= 1 && $row['expire'] > time()) {
echo '<div align="center"><div class="box_errors"><div class="box_title" style="width: 388px"><div class="box_txt box_error" align="left">&iexcl;Atenci&oacute;n!</div><div class="box_rss"><img alt="" src="/images/blank.gif" style="width:14px;height:12px;" border="0" /></div></div><div class="windowbg" style="width:380px;padding:4px;"><br /><center><b style="color:red;">Tu cuenta en esta comunidad se encuentra baneada.</b><br/> 
<b>Raz&oacute;n:</b> ', $row['reason'], '<br/> 
<b>Por:</b> ', $row['modName'], '<br/> 
<b>Expira:</b> ', $row['day'], '&nbsp;d&iacute;a(s)</center><br /><br /><input class="login" style="font-size: 11px;" type="submit" title="Ir a la P&aacute;gina principal" value="Ir a la P&aacute;gina principal" onclick="location.href=\'', $boardurl, '/\'" /><br /><br /></div></div><br /><div align="center"><p align="center" style="padding:0px;margin:0px;"><br />Publicidad</p></div></div><div style="clear:both"></div>';
} elseif($ban <= 0) {

	if($context['comunidad']['view'] == 2 && $context['user']['is_guest']) {
	$context['page_title']	=	$txt[18];
	fatal_error('S&oacute;lo usuarios registrados tienen acceso a esta comunidad.-', false);
	}

$id	=	htmlentities(addslashes($_REQUEST['id']));
panel();
echo '<div style="float:left;">
<div class="post-com" style="width:519px;padding:20px 0px 20px 20px;">
<div id="miem-com">';

$RegistrosAMostrar		=	8;
if(isset($_GET['pag'])) {
$RegistrosAEmpezar	=	($_GET['pag']-1)*$RegistrosAMostrar;
$PagAct	=	$_GET['pag'];
} else {
$RegistrosAEmpezar	=	0;
$PagAct				=	1;
}
$request2	= db_query("
SELECT cm.ID_MEMBER, cm.grade, cm.date, cm.name, cm.ID_COMMUNITY, c.ID_COMMUNITY, c.friendly_url, mem.ID_MEMBER, mem.gender, mem.avatar, mem.usertitle
FROM ({$db_prefix}community_members AS cm, {$db_prefix}communities AS c, {$db_prefix}members AS mem)
WHERE cm.ID_COMMUNITY = c.ID_COMMUNITY
AND c.friendly_url = '$id'
AND cm.ID_MEMBER = mem.ID_MEMBER
ORDER BY cm.date DESC
LIMIT {$RegistrosAEmpezar}, {$RegistrosAMostrar}", __FILE__, __LINE__);

while($members	=	mysql_fetch_assoc($request2)) {
echo '<ul style="padding:0px;margin:0px;background:#000;"><li class="miem-com-den">
<h4><a href="/perfil/', $members['name'], '/" title="Perfil de ', $members['name'], '">', $members['name'], '</a></h4>
<div style="float:left;"><a href="/perfil/', $members['name'], '/" title="Perfil de ', $members['name'], '"><img src="';
if(empty($members['avatar'])) {
echo $boardurl . '/avatar.gif';
} else {
echo $members['avatar'];
}
echo '" onerror="error_avatar(this)" width="75px" height="75px"></a></div>

<div style="float:left;width:165px;">
	<ul class="miem-com-denDos">
		<li style="border-top:#90B6DC solid 1px;"><b>Rango:</b> ', rango($members['grade']), '</li>
		<li><b>Pa&iacute;s:</b> ', ssi_pais($members['usertitle']), '</li>
		  <li><b>Sexo:</b> ', ssi_sexo1($members['gender']), ' </li>

		  <li><a href="/mensajes/a/', $members['name'], '" title="Enviar mensaje privado">Enviar mensaje privado</a></li>';
if($context['rango']['grade'] == 1 && $context['usercomunidad'] == 1) {
echo '<li><a href="/comunidades/', $context['comunidad']['friendly_url'], '/adm-miembros/', $members['name'], '" title="Administrar miembro" style="color:red;">Administrar miembro</a></li>';
}
echo '</ul></div></li></ul>';
}
$NroRegistros	=	mysql_num_rows(db_query("SELECT cm.ID_MEMBER, cm.grade, cm.date, cm.name, cm.ID_COMMUNITY, c.ID_COMMUNITY, c.friendly_url, mem.ID_MEMBER, mem.gender, mem.avatar, mem.usertitle
FROM ({$db_prefix}community_members AS cm, {$db_prefix}communities AS c, {$db_prefix}members AS mem)
WHERE cm.ID_COMMUNITY = c.ID_COMMUNITY
AND c.friendly_url = '$id'
AND cm.ID_MEMBER = mem.ID_MEMBER ", __FILE__, __LINE__));


 $PagAnt=$PagAct-1;
 $PagSig=$PagAct+1;
 $PagUlt=$NroRegistros/$RegistrosAMostrar;
 $Res=$NroRegistros%$RegistrosAMostrar;
 if($Res>0) $PagUlt=floor($PagUlt)+1;
echo '<div class="clearBoth"></div></div></div><div class="windowbgpag" style="width:378px;">
';

if($PagAct>1) echo '<a href="/comunidades/', $context['comunidad']['friendly_url'], '/miembros/pag-' . $PagAnt . '">&#171; anterior</a>';
if($PagAct<$PagUlt)  echo '<a href="/comunidades/', $context['comunidad']['friendly_url'], '/miembros/pag-' . $PagSig . '">siguiente &#187;</a>';
echo '</div></div>';

ultimos_comentarios();
ultimos_miembros();
destacados();
}
}

function template_adminmiembro()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings;

$request	=	db_query("SELECT * FROM {$db_prefix}community_banned WHERE ID_COMMUNITY = " . $context['comunidad']['ID_COMMUNITY'] . " AND ID_MEMBER = " . $context['adminmiembro']['ID_MEMBER'], __FILE__, __LINE__);
$count		=	mysql_fetch_assoc($request);
echo '<div class="tagacom2"><ul style="margin-bottom:8px;"><li><a href="/comunidades/" title="Comunidades">Comunidades</a></li> <li><a href="/comunidades/categoria/', $context['comunidad']['friendly_url2'], '" title="', $context['comunidad']['bname'], '" alt="', $context['comunidad']['bname'], '">', $context['comunidad']['bname'], '</a></li> <li><a href="/comunidades/', $context['comunidad']['friendly_url'], '" title="', $context['comunidad']['title'], '" alt="', $context['comunidad']['title'], '">', $context['comunidad']['title'], '</a></li> <li><a href="/comunidades/', $context['comunidad']['friendly_url'], '/miembros" title="Miembros">Miembros</a></li> <li id="activer">Administrar miembro</li><div style="clear: both;"></div></ul></div>';
panel();
if($context['usercomunidad'] == 0 || $context['rango']['grade'] != 1 && $context['usercomunidad'] == 1) {
fatal_error('No puedes estar ac&aacute;.-', false);
} elseif($_REQUEST['us'] == $context['user']['name'] && $context['usercomunidad'] == 1) {
echo '<div class="noesta" style="width:541px;margin-bottom:8px;float:left;">No puedes administrarte a ti mismo.-</div>';
} elseif($context['adminmiembro']['grade'] == 1) {
echo '<div class="noesta" style="width:541px;margin-bottom:8px;float:left;">No se puede administrar un miembro con rango Administrador.-</div>';
} elseif($context['conteomiembro'] == 0) {
echo '<div class="noesta" style="width:541px;margin-bottom:8px;float:left;">Este miembro no esta en esta comunidad.</div>';
} else {

echo '<div style="margin-bottom:8px;float:left;">
<div class="box_title" style="width:539px;"><div class="box_txt">Administrar miembro</div><div class="box_rss"><img alt="" src="', $settings['images_url'], '/blank.gif" style="width: 14px; height: 12px;" border="0"></div></div>
<div class="windowbg" style="width:531px;padding:4px;"><form style="margin: 0px; padding: 0px;" action="/?action=comunidades;sa=adminmiembro2;id=', $context['comunidad']['friendly_url'], '" method="POST" accept-charset="UTF-8"><table>

<tr><td style="width:100px;"><b>Miembro:</b></td> <td>', $context['adminmiembro']['name'], '</td></tr>
<tr>
<td style="width:100px;"><b>Rango:</b></td> 
<td><select name="rango" style="height:85px;" size="15">
<option value="1"'; if($context['adminmiembro']['grade'] == 1) { echo ' selected="selected"'; } echo '>Administrador</option>
<option value="2"'; if($context['adminmiembro']['grade'] == 2) { echo ' selected="selected"'; } echo '>Moderador</option>
<option value="3"'; if($context['adminmiembro']['grade'] == 3) { echo ' selected="selected"'; } echo '>Posteador</option>
<option value="4"'; if($context['adminmiembro']['grade'] == 4) { echo ' selected="selected"'; } echo '>Comentador</option>
<option value="5"'; if($context['adminmiembro']['grade'] == 5) { echo ' selected="selected"'; } echo '>Visitante</option>
</select></td></tr></table><div class="hrs"></div>';
if($count >= 1) {
echo '<div class="noesta">Usuario baneado<br/>

Por: vicent48<br/>
Raz&oacute;n: Probando<br/>
Expira: 1&nbsp;d&iacute;a(s)</div>

<table><tr><td style="width:100px;"><b>Desbanear:</b></td> <td><input name="desbanear" type="checkbox" value="1" /><br/></td></tr></table>';
} elseif($count <= 0) {
echo '<table><tr><td style="width:100px;"><b>Banear:</b></td> <td><input name="banear" type="checkbox" value="1" /><br/></td></tr>

<tr><td style="width:100px;"><b>Raz&oacute;n:</b></td> <td><input onfocus="foco(this);" onblur="no_foco(this);" title="Raz&oacute;n" value="" type="text" name="razon" /></td></tr>

<tr><td style="width:100px;"><b>Expira:</b></td> <td><input onfocus="foco(this);"onblur="no_foco(this);" title="Expira" value="" type="text" name="expira" /> D&iacute;a(s) / Vacio: para siempre</td></tr></table>';
}
echo '<div class="hrs"></div><p style="margin:0px;margin:0px;" align="right"><input alt="" class="login" title="Aceptar" value="Aceptar" type="submit" /></p>
<input name="miembro-cuestion" value="', $context['adminmiembro']['ID_MEMBER'], '" type="hidden" />

</form></center></div><div class="clearBoth"></div></div>';

}
ultimos_comentarios();
ultimos_miembros();
destacados();
}

function template_denunciar()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $ID_MEMBER;

	echo '<div class="tagacom2"><ul style="margin-bottom:8px;"><li><a href="/comunidades/" title="Comunidades">Comunidades</a></li> <li><a href="/comunidades/categoria/', $context['comunidad']['friendly_url2'], '" title="', $context['comunidad']['bname'], '" alt="', $context['comunidad']['bname'], '">', $context['comunidad']['bname'], '</a></li> <li><a href="/comunidades/', $context['comunidad']['friendly_url'], '" title="', $context['comunidad']['title'], '" a�t="', $context['comunidad']['title'], '">', $context['comunidad']['title'], '</a></li> <li id="activer">Denunciar comunidad</li><div style="clear: both;"></div></ul></div>';
$request	=	db_query("SELECT * FROM {$db_prefix}community_banned WHERE ID_COMMUNITY = " . $context['comunidad']['ID_COMMUNITY'] . " AND ID_MEMBER = $ID_MEMBER", __FILE__, __LINE__);
$ban		=	mysql_num_rows($request);
$row		=	mysql_fetch_assoc($request);

if($context['comunidad']['view'] == 2 && $context['user']['is_guest']) {
$context['page_title']	=	$txt[18];
fatal_error('S&oacute;lo usuarios registrados tienen acceso a esta comunidad.-', false);
} elseif($ban >= 1 && $row['expire'] < time()) {
db_query("DELETE FROM {$db_prefix}community_banned WHERE ID_COMMUNITY = " . $context['comunidad']['ID_COMMUNITY'] . " AND ID_MEMBER = $ID_MEMBER", __FILE__, __LINE__);
redirectexit($boardurl . '/comunidades/' . $context['comunidad']['friendly_url']);
} elseif($ban >= 1 && $row['expire'] > time()) {
echo '<div align="center"><div class="box_errors"><div class="box_title" style="width: 388px"><div class="box_txt box_error" align="left">&iexcl;Atenci&oacute;n!</div><div class="box_rss"><img alt="" src="/images/blank.gif" style="width:14px;height:12px;" border="0" /></div></div><div class="windowbg" style="width:380px;padding:4px;"><br /><center><b style="color:red;">Tu cuenta en esta comunidad se encuentra baneada.</b><br/> 
<b>Raz&oacute;n:</b> ', $row['reason'], '<br/> 
<b>Por:</b> ', $row['modName'], '<br/> 
<b>Expira:</b> ', $row['day'], '&nbsp;d&iacute;a(s)</center><br /><br /><input class="login" style="font-size: 11px;" type="submit" title="Ir a la P&aacute;gina principal" value="Ir a la P&aacute;gina principal" onclick="location.href=\'', $boardurl, '/\'" /><br /><br /></div></div><br /><div align="center"><p align="center" style="padding:0px;margin:0px;"><br />Publicidad</p></div></div><div style="clear:both"></div>';
} elseif($ban <= 0) {

	if($context['comunidad']['view'] == 2 && $context['user']['is_guest']) {
	$context['page_title']	=	$txt[18];
	fatal_error('S&oacute;lo usuarios registrados tienen acceso a esta comunidad.-', false);
	}
	
$id	=	htmlentities(addslashes($_REQUEST['id']));
$request	=	db_query("
SELECT *
FROM {$db_prefix}communities
WHERE friendly_url = '$id'
", __FILE__, __LINE__);
$row	=	mysql_fetch_assoc($request);
panel();
$request2	=	db_query("
SELECT *
FROM {$db_prefix}community_members
WHERE ID_COMMUNITY = {$context['editor']['ID_COMMUNITY']}
AND ID_MEMBER = $ID_MEMBER
LIMIT 1", __FILE__, __LINE__);
$row2	=	mysql_num_rows($request2);
if($row2 > 0 || $context['user']['is_guest']) {
echo '<div class="noesta" style="width:541px;margin-bottom:8px;float:left;">No puedes denunciar esta comunidad.-</div>';
} else {
echo '<div style="margin-bottom:8px;float:left;">
<div class="box_title" style="width:539px;"><div class="box_txt">Denunciar Comunidad</div><div class="box_rss"><img alt="" src="', $settings['images_url'], '/blank.gif" style="width: 14px; height: 12px;" border="0"></div></div>
<div class="windowbg" style="width:531px;padding:4px;"><form style="margin: 0px; padding: 0px;" action="/web/tp-comunidadesDen.php" method="POST" accept-charset="UTF-8"><table>
<tr><td style="width:100px;"><b>Comunidad:</b></td><td> ', $context['comunidad']['title'], '</td></tr>
<tr><td style="width:100px;"><b>Raz&oacute;n:</b></td><td> <input value="" type="text" name="razon" onfocus="foco(this);" onblur="no_foco(this);" style="width:300px;" /> </td></tr>
<tr><td style="width:100px;"><b>Comentario:</b></td><td> <textarea onfocus="foco(this);" onblur="no_foco(this);" style="width:300px;" name="comentario"></textarea></td></tr>

</table>
<input type="hidden" value="', $row['ID_COMMUNITY'], '" name="comu" />
<p align="right" style="padding:0px;margin:0px;"><input type="submit" class="login" value="Enviar" name="enviar" /></p></form>

</div><div class="clearBoth"></div></div>';
}
ultimos_comentarios();
ultimos_miembros();
destacados();
}
}

function template_notema(){}

function template_nuevotema()
{
	global $context, $txt, $settings, $options, $ID_MEMBER, $db_prefix, $modSettings;
echo '<div class="tagacom2"><ul><li><a href="/comunidades/" title="Comunidades">Comunidades</a></li><li id="activer">Crear Tema</li></ul></div><div style="clear: left;margin-bottom:8px;"></div>';
$request	=	db_query("SELECT * FROM {$db_prefix}community_banned WHERE ID_COMMUNITY = " . $context['comunidad']['ID_COMMUNITY'] . " AND ID_MEMBER = $ID_MEMBER", __FILE__, __LINE__);
$ban		=	mysql_num_rows($request);
$row		=	mysql_fetch_assoc($request);

if($context['comunidad']['view'] == 2 && $context['user']['is_guest']) {
$context['page_title']	=	$txt[18];
fatal_error('S&oacute;lo usuarios registrados tienen acceso a esta comunidad.-', false);
} elseif($ban >= 1 && $row['expire'] < time()) {
db_query("DELETE FROM {$db_prefix}community_banned WHERE ID_COMMUNITY = " . $context['comunidad']['ID_COMMUNITY'] . " AND ID_MEMBER = $ID_MEMBER", __FILE__, __LINE__);
redirectexit($boardurl . '/comunidades/' . $context['comunidad']['friendly_url']);
} elseif($ban >= 1 && $row['expire'] > time()) {
echo '<div align="center"><div class="box_errors"><div class="box_title" style="width: 388px"><div class="box_txt box_error" align="left">&iexcl;Atenci&oacute;n!</div><div class="box_rss"><img alt="" src="/images/blank.gif" style="width:14px;height:12px;" border="0" /></div></div><div class="windowbg" style="width:380px;padding:4px;"><br /><center><b style="color:red;">Tu cuenta en esta comunidad se encuentra baneada.</b><br/> 
<b>Raz&oacute;n:</b> ', $row['reason'], '<br/> 
<b>Por:</b> ', $row['modName'], '<br/> 
<b>Expira:</b> ', $row['day'], '&nbsp;d&iacute;a(s)</center><br /><br /><input class="login" style="font-size: 11px;" type="submit" title="Ir a la P&aacute;gina principal" value="Ir a la P&aacute;gina principal" onclick="location.href=\'', $boardurl, '/\'" /><br /><br /></div></div><br /><div align="center"><p align="center" style="padding:0px;margin:0px;"><br />', $modSettings['horizontal'], '</p></div></div><div style="clear:both"></div>';
} elseif($ban <= 0) {
echo '<div style="width:354px;float:left;margin-right:8px;"><div class="box_354" style="margin-bottom:8px;"><div class="box_title" style="width:352px;"><div class="box_txt box_354-34">Importante</div><div class="box_rss"><div class="icon_img"><img src="/images/blank.gif" style="width:16px;height:16px;" border="0" alt="" /></div></div></div><div style="width:344px;padding:4px;" class="windowbg">Antes de crear un nuevo tema es importante que leas el <a href="/protocolo/">protocolo</a>.<br /><br />
Al ser el creador del tema, tenes el permiso de editarlo, eliminarlo, eliminar comentarios, bloquear comentarios.<br /><br />
Si desea que su tema este fijado en la comunidad debe comunicarse con lo(s) Administrador(es) o Moderador(es) de la comunidad ya que ellos son los &uacute;nicos capaces de fijarlo.<br /><br />
Si tenes dudas sobre las comunidades visita <a href="' . $boardurl . '/ayuda/categoria/comunidades/">este enlace</a>.</div></div>

<div class="box_354"><div class="box_title" style="width:352px;"><div class="box_txt box_354-34">Destacados</div><div class="box_rss"><div class="icon_img"><img src="', $settings['images_url'], '/blank.gif" style="width:16px;height:16px;" border="0" alt="" /></div></div></div>
<div style="width:344px;padding:4px;" class="windowbg"><p align="center">', ssi_destacados(), '</p></div></div></div>
';
$tabindex++;
echo '<div style="width:560px;float:left;">
<div class="box_560"><div class="box_title" style="width: 558px;"><div class="box_txt box_560-34"> Crear nuevo tema</div><div class="box_rss"><div class="icon_img"><img src="', $settings['images_url'], '/blank.gif" style="width:16px;height:16px;" border="0" alt="" /></div></div></div>
<div class="windowbg" style="width:550px;padding:4px;">
<form name="add_comunidad" id="nuevocoment" method="post" action="/comunidades/agregando/', $context['comunidad']['friendly_url'], '/">
<div class="form-container"><label for="uname">Titulo:</label><input style="width:540px;" onfocus="foco(this);" onblur="no_foco(this);" class="c_input" value="" name="titulo" tabindex="1" datatype="text" dataname="Titulo" type="text"><div class="clearBoth"></div><div class="data"><label for="uname">Cuerpo:</label>';
nuevotema_smileys();
echo '<div class="clearBoth"></div><br /><fieldset style="width:200px;"><legend><span class="tit_lab">Opciones</span></legend>';
if($context['allow_admin'] || $context['rango']['grade'] == 4 || $context['rango']['grade'] == 5) {
echo '<label for="sticky"><input name="sticky" id="sticky" value="1" type="checkbox" /> Fijar</label>';
}
echo '<label for="nocoment"><input name="nocoment" id="nocoment" value="1" type="checkbox" /> No permitir comentarios</label></fieldset> 

<input name="comun" value="', $context['comunidad']['friendly_url'], '" type="hidden" /></div><hr style="clear: both; margin-bottom: 15px; margin-top: 20px;" class="divider" /><div id="buttons" align="right"><input tabindex="14" title="Crear tema" value="Crear tema" class="login" name="Enviar" type="submit"/></div></form>
</div></div><div style="clear:both"></div>';
}
}

function template_nuevotemabox()
{
	global $context, $txt, $settings, $options, $ID_MEMBER, $db_prefix, $boardurl;

echo '<textarea style="height:300px;width:543px;" onfocus="foco(this);" onblur="no_foco(this);" id="markItUp" name="cuerpo_comment" class="markItUpEditor" tabindex="3"></textarea>';
if (!empty($context['smileys']['postform'])) {
foreach($context['smileys']['postform'] as $smiley_row) {
foreach($smiley_row['smileys'] as $smiley) {
echo '<a href="javascript:void(0);" onclick="replaceText(\' ', $smiley['code'], '\', document.forms.nuevocoment.cuerpo_comment); return false;"><img src="', $settings['smileys_url'], '/', $smiley['filename'], '" align="bottom" alt="', $smiley['description'], '" title="', $smiley['description'], '" /></a> ';
}
}
if (!empty($context['smileys']['popup'])) {
echo '<script type="text/javascript">function openpopup(){var winpops=window.open("/emoticones/","","width=255px,height=500px,scrollbars");}</script><a href="javascript:openpopup()">[', $txt['more_smileys'], ']</a></div>';
}
}
}

function template_vertema()
{
	global $context, $txt, $settings, $options, $ID_MEMBER, $db_prefix, $boardurl, $PagUlt, $modSettings;

echo '<div class="tagacom2"><ul style="margin-bottom:8px;"><li><a href="/comunidades/" title="Comunidades">Comunidades</a></li> <li><a href="/comunidades/categoria/', $context['comunidad']['friendly_url2'], '" title="', $context['comunidad']['bname'], '">', $context['comunidad']['bname'], '</a></li> 
<li><a href="/comunidades/', $context['comunidad']['friendly_url'], '" title="', $context['comunidad']['title'], '" alt="', $context['comunidad']['title'], '">', $context['comunidad']['title'], '</a></li>
<li id="activer">', $context['tema']['subject'], '</li><div style="clear: both;"></div></ul></div>';

$request	=	db_query("SELECT * FROM {$db_prefix}community_banned WHERE ID_COMMUNITY = " . $context['comunidad']['ID_COMMUNITY'] . " AND ID_MEMBER = $ID_MEMBER", __FILE__, __LINE__);
$ban		=	mysql_num_rows($request);
$row		=	mysql_fetch_assoc($request);

if($context['comunidad']['view'] == 2 && $context['user']['is_guest']) {
$context['page_title']	=	$txt[18];
fatal_error('S&oacute;lo usuarios registrados tienen acceso a esta comunidad.-', false);
} elseif($ban >= 1 && $row['expire'] < time()) {
db_query("DELETE FROM {$db_prefix}community_banned WHERE ID_COMMUNITY = " . $context['comunidad']['ID_COMMUNITY'] . " AND ID_MEMBER = $ID_MEMBER", __FILE__, __LINE__);
redirectexit($boardurl . '/comunidades/' . $context['comunidad']['friendly_url']);
} elseif($ban >= 1 && $row['expire'] > time()) {
echo '<div align="center"><div class="box_errors"><div class="box_title" style="width: 388px"><div class="box_txt box_error" align="left">&iexcl;Atenci&oacute;n!</div><div class="box_rss"><img alt="" src="/images/blank.gif" style="width:14px;height:12px;" border="0" /></div></div><div class="windowbg" style="width:380px;padding:4px;"><br /><center><b style="color:red;">Tu cuenta en esta comunidad se encuentra baneada.</b><br/> 
<b>Raz&oacute;n:</b> ', $row['reason'], '<br/> 
<b>Por:</b> ', $row['modName'], '<br/> 
<b>Expira:</b> ', $row['day'], '&nbsp;d&iacute;a(s)</center><br /><br /><input class="login" style="font-size: 11px;" type="submit" title="Ir a la P&aacute;gina principal" value="Ir a la P&aacute;gina principal" onclick="location.href=\'', $boardurl, '/\'" /><br /><br /></div></div><br /><div align="center"><p align="center" style="padding:0px;margin:0px;"><br />Publicidad</p></div></div><div style="clear:both"></div>';
} elseif($ban <= 0) {

panel();
echo '<div style="float:left;"><div style="margin-bottom:30px;"><div class="post-com">
<div class="post-user"><table style="padding:0px;margin:0px;"><tr style="padding:0px;margin:0px;"><td valign="top"><a href="/perfil/', $context['tema']['posterName'], '" title="', $context['tema']['posterName'], '" alt="', $context['tema']['posterName'], '"><img src="';
if(empty($context['tema']['avatar'])) {
echo $boardurl . '/avatar.gif';
} else {
echo $context['tema']['avatar'];
}
echo '" width="100px" height="100px" alt="', $context['tema']['posterName'], '" class="avatar" title="', $context['tema']['posterName'], '" onerror="error_avatar(this)" /></a></td>
<td valign="top" style="width:160px;"><b class="size15"><a href="/perfil/', $context['tema']['posterName'], '" title="', $context['tema']['posterName'], '" alt="', $context['tema']['posterName'], '">', $context['tema']['posterName'], '</a></b><br/>
<img src="', $settings['images_url'], '/comunidades/', rango_img($context['tema']['grade']), '.png" title="', rango($context['tema']['grade']), '" alt="', rango($context['tema']['grade']), '" /> ', rango($context['tema']['grade']), '<br/>
<img alt="', ssi_pais($context['tema']['usertitle']), '" title="', ssi_pais($context['tema']['usertitle']), '" src="', $settings['images_url'], '/icons/banderas/', $context['tema']['usertitle'], '.gif" /> ', ssi_pais($context['tema']['usertitle']), '<br/>
', ssi_sexo2(ssi_sexo3($context['tema']['gender'])), ' ', ssi_sexo1($context['tema']['gender']), '<br/><a href="/mensajes/a/', $context['tema']['posterName'], '" title="Enviar mensaje privado"><img src="', $settings['images_url'], '/icons/mensaje_para.gif" alt="Enviar mensaje privado" /> Enviar mensaje privado</a><br/></td>
<td>', $modSettings['horizontal2'], '</td>
</tr></table></div>

<div style="padding:6px;"><div style="padding:0px;margin:0px;"><div style="float:left;padding-left:3px;"><img src="', $settings['images_url'], '/comunidades/temas.png" alt="" /></div> <div style="font-size:15px;padding-left:2px;"><a href="/comunidades/', $context['comunidad']['friendly_url'], '/', $context['tema']['ID_TOPIC'], '/', ssi_amigable($context['tema']['subject']), '.html" title="', $context['tema']['subject'], '" alt="', $context['tema']['subject'], '">', $context['tema']['subject'], '</a></div></div><hr style="border: 1px solid #517BA1;background:px;" /></div>
<div class="post-contenido" property="dc:content" id="post_', $context['tema']['ID_TOPIC'], '">', parse_bbc($context['tema']['body']), '</div><div class="post-datos">

<table align="center"><tr>
<td style="width:200px;"><b>Compartir:</b></td>
<td style="width:230px;"><b>Calificar:</b></td>
<td style="width:200px;"><b>Creado:</b></td>
<td style="width:100px;"><b>Visitas:</b></td>
</tr><tr>
';
$link	=	$boardurl . '/comunidades/' . $context['comunidad']['friendly_url'] . '/' . $context['tema']['ID_TOPIC'] . '/' . ssi_amigable($context['tema']['subject']) . '.html';
echo '
<td><div class="icon_img" style="float:left; margin-right:4px;"><a href="http://technorati.com/faves/?add=' . $link . '" rel="nofollow" target="_blank" title="Agregar a: Technorati"><img alt="" src="', $settings['images_url'], '/icons/tpbig-v1-iconos.gif?v3.2.3" style="cursor:pointer;margin-top:-391px; display:inline;" /></a></div>

<div class="icon_img" style="float: left; margin-right:4px;"><a href="http://www.facebook.com/share.php?u=' . $link . '" rel="nofollow" target="_blank" title="Agregar a: Facebook"><img alt="" src="', $settings['images_url'], '/icons/tpbig-v1-iconos.gif?v3.2.3" style="cursor:pointer;margin-top:-411px;display:inline;" /></a></div>

<div class="icon_img" style="float: left; margin-right:4px;"><a href="http://twitter.com/home?status=Les recomiendo este post: ' . $link . '" rel="nofollow" target="_blank" title="Agregar a: Twitter"><img alt="" src="', $settings['images_url'], '/icons/tpbig-v1-iconos.gif?v3.2.3" style="cursor:pointer;margin-top:-472px;display:inline;" /></a></div>

<div class="icon_img" style="float: left; margin-right:4px;"><a href="http://del.icio.us/post?url=' . $link . '" rel="nofollow" target="_blank" title="Agregar a: Del.icio"><img alt="" src="', $settings['images_url'], '/icons/tpbig-v1-iconos.gif?v3.2.3" style="cursor:pointer;margin-top:-432px;display:inline;" /></a></div>

<div class="icon_img"><a href="http://digg.com/submit?phase=2&#38;url=' . $link . '" rel="nofollow" target="_blank" title="Agregar a: Digg"><img alt="" src="', $settings['images_url'], '/icons/tpbig-v1-iconos.gif?v3.2.3" style="cursor:pointer; margin-top:-453px;display:inline;" /></a></div></td>


<td>
<span id="votos_total2"><a href="javascript:com.tema_votar(1,', $context['tema']['ID_TOPIC'], ')" class="thumbs thumbsUp" title="Votar positivo"></a>
<a href="javascript:com.tema_votar(-1,', $context['tema']['ID_TOPIC'], ')" class="thumbs thumbsDown" title="Votar negativo"></a></span>
<span id="votos_total" class="ok">', $context['tema']['points'], '</span></td>
<td><span style="font-size: 11px;" title="', timeformat($context['tema']['posterTime']), '">', timeformat($context['tema']['posterTime']), '</span></td>
<td><span style="font-size: 11px;">', $context['tema']['numViews'], '</span></td><div class="clearBoth"></div>
';
$cuenta	=	db_query("
SELECT *
FROM ({$db_prefix}community_comments AS cc, {$db_prefix}community_topic AS ct)
WHERE cc.ID_TOPIC = ct.ID_TOPIC
AND ct.ID_TOPIC = {$context['tema']['ID_TOPIC']}
AND cc.ID_TOPIC = {$context['tema']['ID_TOPIC']}
", __FILE__, __LINE__);
$contarcomentarios	=	mysql_num_rows($cuenta);
echo '
</tr></table></div></div><div class="clearBoth" style="margin-top:3px;"></div><div><div style="float:left;">';
if($context['tema']['locked'] == 1) {
echo '<img src="', $settings['images_url'], '/comunidades/cerrado.png" alt="Tema cerrado" title="Tema cerrado" /> ';
}
if($context['tema']['isSticky'] == 1) {
echo '<img src="', $settings['images_url'], '/comunidades/fijado.png" alt="Tema fijado" title="Tema fijado" /> ';
}
echo '</div>';
echo '<div>';
if($context['tema']['ID_MEMBER'] == $ID_MEMBER && $context['usercomunidad'] == 1 || $context['allow_admin']) {
echo '<p align="right" style="margin:0px;padding:0px;"><input class="login" style="font-size: 11px;" value="Editar tema" title="Editar tema" onclick="location.href=\'/comunidades/editar-tema/', $context['tema']['ID_TOPIC'], '\'" type="button" /> <input class="login" style="font-size: 11px;" value="Eliminar tema" title="Eliminar tema" onclick="if (!confirm(\'\xbfEstas seguro que desea eliminar este tema?\')) return false;location.href=\'/web/tp-comunidadesEliTem.php?id=', $context['tema']['ID_TOPIC'], '\'" type="button" /></p>';
}
echo '</div></div></div><div style="margin-bottom:5px;"><div class="icon_img" style="float:left;margin-right:5px;"><a href="/rss/temas-comment/', $context['tema']['ID_TOPIC'], '"><img alt="" src="', $settings['images_url'], '/icons/tpbig-v1-iconos.gif?v3.2.3" style="cursor: pointer; margin-top: -352px; display: inline;"/></a></div><div><b style="font-size:14px;">Comentarios (<span id="nrocoment">', $contarcomentarios, '</span>)</b></div></div><div id="comentarios">
';

include($_SERVER['DOCUMENT_ROOT'] . '/web/tp-comunidadesComenCar.php');

if (!empty($options['display_quick_reply']) && !$context['tema']['locked'] == 1 && $context['user']['is_logged']) {
	if($context['usercomunidad'] == 1 && $context['rango']['grade'] == 1 || $context['usercomunidad'] == 1 && $context['rango']['grade'] == 2 || $context['usercomunidad'] == 1 && $context['rango']['grade'] == 3 || $context['usercomunidad'] == 1 && $context['rango']['grade'] == 4 || $context['allow_admin']) {
echo '<div style="margin-bottom:5px;margin-top:25px;" id="comentar"><b style="font-size:14px;">Agregar un nuevo comentario</b></div>

<div class="post-com"><div class="coment-user"><div class="msg_comentar"></div><form name="nuevocoment">';
theme_quickreply_box();
$RegistrosAMostrar		=	10;
$NroRegistros	=	mysql_num_rows(db_query("
SELECT c.comment, c.comment AS comentario2, c.ID_TOPIC, c.ID_MEMBER, c.ID_COMMENT, c.posterTime, c.posterName, c.ID_COMMUNITY
FROM ({$db_prefix}community_comments AS c) 
WHERE c.ID_TOPIC = " . $context['tema']['ID_TOPIC'] . "
ORDER BY c.ID_COMMENT ASC", __FILE__, __LINE__));
$PagUlt=$NroRegistros/$RegistrosAMostrar;
$PagUlt=floor($PagUlt)+1;

echo '<br />

<input class="login" type="button" id="button_comentar" value="Enviar Comentario" onclick="ComComentar(', $context['tema']['ID_TOPIC'],',', $PagUlt, '); return false;" tabindex="2" /></p>
</form>

</div></div></div></div><div style="clear:both"></div>
';

}
}
}
}

function template_quickreply_box()
{
	global $context, $settings, $options, $txt, $modSettings;

echo '<textarea onfocus="foco(this);" onblur="no_foco(this);" style="height:90px;width:735px;" id="cuerpo_comment" name="cuerpo_comment" class="markItUpEditor" tabindex="1"></textarea><p align="right" style="padding:0px;margin:0px;">';
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
	echo '<script type="text/javascript">function openpopup(){var winpops=window.open("/emoticones/","","width=255px,height=500px,scrollbars");}</script><a href="javascript:openpopup()">[', $txt['more_smileys'], ']</a>';
	}
}
}

function template_publicitar()
{
	global $context, $settings, $options, $txt, $modSettings;

echo '<div class="tagacom2"><ul style="margin-bottom:8px;"><li><a href="/comunidades/" title="Comunidades">Comunidades</a></li> <li><a href="/comunidades/categoria/', $context['comunidad']['friendly_url2'], '" title="', $context['comunidad']['bname'], '" alt="', $context['comunidad']['bname'], '">', $context['comunidad']['bname'], '</a></li> <li><a href="/comunidades/', $context['comunidad']['friendly_url'], '" title="', $context['comunidad']['title'], '" alt="', $context['comunidad']['title'], '">', $context['comunidad']['title'], '</a></li> <li id="activer">Publicitar</li><div style="clear: both;"></div></ul></div>';
panel();	
if($context['rango']['grade'] != 1 && $context['usercomunidad'] == 1 || $context['rango']['grade'] != 1 && $context['usercomunidad'] == 0) { 
echo '<div class="noesta" style="width:541px;margin-bottom:8px;float:left;">No puedes publicitar esta comunidad.-</div>';
} elseif($context['rango']['grade'] == 1 && $context['usercomunidad'] == 1) {
echo '<div style="margin-bottom:8px;float:left;">
<div class="box_title" style="width:539px;"><div class="box_txt">Publicitar Comunidad</div><div class="box_rss"><img alt="" src="', $settings['images_url'], '/blank.gif" style="width: 14px; height: 12px;" border="0"></div></div>
<div class="windowbg" style="width:531px;padding:4px;"><ul><li>Para publicitar tu comunidadad tenes que tener 500 o m&aacute;s puntos.</li>
<li>La publicidad vale 100 puntos.</li>

<li>Estar&aacute; a la vista de todos durante 24HS.</li>

<form style="margin: 0px; padding: 0px;" action="/comunidades/publicitar2" method="POST" accept-charset="UTF-8">
<input type="hidden" value="', $context['comunidad']['friendly_url'], '" name="id" />
<p align="right" style="padding:0px;margin:0px;"><input type="submit" class="login" value="Publicitar" name="enviar" /></p></form>

</div><div class="clearBoth"></div></div>';
}
ultimos_comentarios();
ultimos_miembros();
destacados();
}

function template_editartema()
{
	global $context, $settings, $options, $txt, $modSettings, $ID_MEMBER;
	
$id	=	htmlentities(addslashes($_REQUEST['id']));
echo '<div class="tagacom2"><ul style="margin-bottom:8px;"><li><a href="/comunidades/" title="Comunidades">Comunidades</a></li><li><a href="/comunidades/categoria/', $context['editar']['friendly_url2'], '" title="', $context['editar']['bname'], '" alt="', $context['editar']['bname'], '">', $context['editar']['bname'], '</a></li> <li><a href="/comunidades/', $context['editar']['friendly_url'], '" title="', $context['editar']['title'], '" alt="', $context['editar']['title'], '">', $context['editar']['title'], '</a></li> <li><a href="/comunidades/', $context['editar']['friendly_url'], '/', $context['editar']['ID_TOPIC'], '/', ssi_amigable($context['editar']['subject']), '.html" title="', $context['editar']['subject'], '" alt="', $context['editar']['subject'], '">', $context['editar']['subject'], '</a></li> <li id="activer">Editar Tema</li><div style="clear: both;"></div></ul></div>';
$request	=	db_query("SELECT * FROM {$db_prefix}community_banned WHERE ID_COMMUNITY = " . $context['editar']['ID_COMMUNITY'] . " AND ID_MEMBER = $ID_MEMBER", __FILE__, __LINE__);
$ban		=	mysql_num_rows($request);
$row		=	mysql_fetch_assoc($request);

if($context['editar']['view'] == 2 && $context['user']['is_guest']) {
$context['page_title']	=	$txt[18];
fatal_error('S&oacute;lo usuarios registrados tienen acceso a esta comunidad.-', false);
} elseif($ban >= 1 && $row['expire'] < time()) {
db_query("DELETE FROM {$db_prefix}community_banned WHERE ID_COMMUNITY = " . $context['editar']['ID_COMMUNITY'] . " AND ID_MEMBER = $ID_MEMBER", __FILE__, __LINE__);
redirectexit($boardurl . '/comunidades/' . $context['editar']['friendly_url']);
} elseif($ban >= 1 && $row['expire'] > time()) {
echo '<div align="center"><div class="box_errors"><div class="box_title" style="width: 388px"><div class="box_txt box_error" align="left">&iexcl;Atenci&oacute;n!</div><div class="box_rss"><img alt="" src="/images/blank.gif" style="width:14px;height:12px;" border="0" /></div></div><div class="windowbg" style="width:380px;padding:4px;"><br /><center><b style="color:red;">Tu cuenta en esta comunidad se encuentra baneada.</b><br/> 
<b>Raz&oacute;n:</b> ', $row['reason'], '<br/> 
<b>Por:</b> ', $row['modName'], '<br/> 
<b>Expira:</b> ', $row['day'], '&nbsp;d&iacute;a(s)</center><br /><br /><input class="login" style="font-size: 11px;" type="submit" title="Ir a la P&aacute;gina principal" value="Ir a la P&aacute;gina principal" onclick="location.href=\'', $boardurl, '/\'" /><br /><br /></div></div><br /><div align="center"><p align="center" style="padding:0px;margin:0px;"><br />', $modSettings['horizontal'], '</p></div></div><div style="clear:both"></div>';
} elseif($ban <= 0 || $context['usercomunidad'] == 1 && $context['rango']['grade'] == 1 || $context['allow_admin'] || $context['editar']['ID_MEMBER'] == $context['user']['id']) {
echo '<div style="width:354px;float:left;margin-right:8px;">
<div class="box_354"><div class="box_title" style="width:352px;"><div class="box_txt box_354-34">Destacados</div><div class="box_rss"><div class="icon_img"><img src="/images/blank.gif" style="width:16px;height:16px;" border="0" alt="" /></div></div></div>
<div style="width:344px;padding:4px;" class="windowbg"><p align="center">', ssi_destacados(), '</p></div></div></div>
';

echo '<div style="width:560px;float:left;">

<div class="box_560"><div class="box_title" style="width: 558px;"><div class="box_txt box_560-34"> Editar tema</div><div class="box_rss"><div class="icon_img"><img src="/images/blank.gif" style="width:16px;height:16px;" border="0" alt="" /></div></div></div>
<div class="windowbg" style="width:550px;padding:4px;">
<form name="add_comunidad" method="post" action="/comunidades/editar-tema2/">
<div class="form-container"><label for="uname">Titulo:</label><input style="width:540px;" onfocus="foco(this);" onblur="no_foco(this);" class="c_input" value="', $context['editar']['subject'], '" name="titulo" tabindex="1" datatype="text" dataname="Titulo" type="text"><div class="clearBoth"></div><div class="data"><label for="uname">Cuerpo:</label>';
theme_quickreply_box2();
echo '<br /><fieldset style="width:200px;"><legend><span class="tit_lab">Opciones</span></legend><label for="sticky"><input name="sticky"'; if($context['editar']['isSticky'] == 1) { echo ' checked="checked"'; } echo ' id="sticky" value="1" type="checkbox" /> Fijar</label><label for="nocoment"><input name="nocoment"'; if($context['editar']['locked'] == 1) { echo ' checked="checked"'; } echo ' id="nocoment" value="1" type="checkbox" /> No permitir comentarios</label></fieldset> 

<input name="id_tema" value="', $id, '" type="hidden" /></div><hr style="clear: both; margin-bottom: 15px; margin-top: 20px;" class="divider" /><div id="buttons" align="right"><input tabindex="14" title="Editar tema" value="Editar tema" class="login" name="Enviar" type="submit"/></div></form>
</div></div><div style="clear:both"></div>';
} elseif($context['editar']['ID_MEMBER'] != $context['user']['id'] || !$context['allow_admin']) {
fatal_error('', false);
}
}

function template_quickreply_box2()
{
	global $context, $settings, $options, $txt, $modSettings;

echo '<textarea style="height:300px;width:543px;" onfocus="foco(this);" onblur="no_foco(this);" id="markItUp" name="cuerpo_comment" class="markItUpEditor" tabindex="3">', $context['editar']['body'], '</textarea>
';
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
	echo '<script type="text/javascript">function openpopup(){var winpops=window.open("/emoticones/","","width=255px,height=500px,scrollbars");}</script><a href="javascript:openpopup()">[', $txt['more_smileys'], ']</a></div><div class="clearBoth"></div>';
	}
}
}
?>