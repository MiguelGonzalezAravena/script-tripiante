<?php
function template_main()
{
	global $context, $txt, $modSettings, $db_prefix, $scripturl, $settings, $boardurl;

include_once('SSI.php');

/* Comentarios en mis posts */
	echo '<div>

<div style="float:left;width:708px;">
<div class="box_r_buscador" style="margin-right:8px;margin-botton:8px;">
<div class="box_title" style="width: 698px;"><div class="box_txt box_r_buscadort"><center>Comentarios en mis posts</center></div><div class="box_rss"><img alt="" src="', $settings['images_url'], '/blank.gif" style="width:14px;height:12px;" border="0" /></div></div><div class="windowbg" style="width:690px;padding:4px;"><table>
';

if (!empty($context['monitorcom'])){
foreach ($context['monitorcom'] as $monitorcom){
echo '<tr><td valign="top" width="16"><img alt="" src="', $settings['images_url'], '/post/icono_'.$monitorcom['ID_BOARD'].'.gif" title="'.$monitorcom['bname'].'" /></td><td><b class="size11"><a title="'.censorText($monitorcom['titulo']).'" href="/post/'.$monitorcom['id'].'/'.$monitorcom['description'].'/'.ssi_amigable($monitorcom['titulo']).'.html">'.$monitorcom['titulo'].'</a></b><div class="size11">' . timeformat($monitorcom['posterTime']) . ': <a href="/post/'.$monitorcom['id'].'/'.$monitorcom['description'].'/'.ssi_amigable($monitorcom['titulo']).'.html#cmt_'.$monitorcom['ID_COMMENT'].'">'.$monitorcom['comment'].'</a></div></td></tr>';}}
else
{
echo '<div class="noesta">Nada por aca...</div>';
}

echo '</table></div></div>
';
/* Comentarios en mis posts */

/* Comentarios en mis imágenes */
echo '<div class="box_r_buscador" style="margin-right:8px;">
<div class="box_title" style="width: 698px;"><div class="box_txt box_r_buscadort"><center>Comentarios en mis im&aacute;genes</center></div>
<div class="box_rss"><img alt="" src="', $settings['images_url'], '/blank.gif" style="width:14px;height:12px;" border="0" /></div></div><div class="windowbg" style="width:690px;padding:4px;"><table>
';
if(!empty($context['monitorimg'])){
foreach ($context['monitorimg'] as $monitorimg){
echo '<tr><td valign="top"><span class="icons fot2"> <b class="size11"><a title="'.$monitorimg['title'].'" href="/imagenes/ver/'.$monitorimg['ID_PICTURE'].'">'.$monitorimg['title'].'</a></b><div class="size11">' . timeformat($monitorimg['date']) . ': <a href="/imagenes/ver/'.$monitorimg['ID_PICTURE'].'#cmt_'.$monitorimg['ID_COMMENT'].'">'.$monitorimg['comment'].'</a></div></td></tr>';}}
else
{
echo '<div class="noesta">Nada por aca...</div>';
}
echo '</table></div></div>
';
/* Comentarios en mis imágenes */

echo '<div class="box_timos" style="margin-right:8px;float:left;">

<div class="box_title" style="width: 340px;"><div class="box_txt box_timos"><center>Mis im&aacute;genes en favoritos</center></div>
<div class="box_rss"><img alt="" src="', $settings['images_url'], '/blank.gif" style="width:14px;height:12px;" border="0" /></div></div><div class="windowbg" style="width:332px;padding:4px;">';
if (!empty($context['monitorfavimagenes'])){
foreach ($context['monitorfavimagenes'] as $monitorfavimagenes){
echo '<img alt="Imagen" title="Imagen" src="', $settings['images_url'], '/icons/foto.gif" /> <b><a href="/imagenes/ver/' . $monitorfavimagenes['ID_PICTURE'] . '/" title="' . $monitorfavimagenes['title'] . '">' . $monitorfavimagenes['title'] . '</a></b><br /><p align="right" class="size11" style="margin:0px;padding:0px;"><b>Lo agreg&oacute;: <a href="/perfil/' . $monitorfavimagenes['realName'] . '" title="' . $monitorfavimagenes['realName'] . '"><span style="color:orange;">' . $monitorfavimagenes['realName'] . '</span></a></b></p><hr />
';}
}
else
{
echo '<div class="noesta">Nada por aca...</div> ';
}
echo '</div></div>';

echo '<div class="box_timoh" style="margin-right:8px;float:left;">
<div class="box_title" style="width: 348px;"><div class="box_txt box_timoh"><center>Puntos obtenidos (im&aacute;genes)</center></div>
<div class="box_rss"><img alt="" src="', $settings['images_url'], '/blank.gif" style="width:14px;height:12px;" border="0" /></div></div><div class="windowbg" style="width:340px;padding:4px;">';
if (!empty($context['monitorpunimagenes'])){
foreach ($context['monitorpunimagenes'] as $monitorfavimagenes){
 echo '<img alt="Imagen" title="Imagen" src="', $settings['images_url'], '/icons/foto.gif" /> <b><a href="/imagenes/ver/' . $monitorfavimagenes['ID_PICTURE'] . '/" title="' . $monitorfavimagenes['title'] . '">' . $monitorfavimagenes['title'] . '</a></b><br /><p align="right" class="size11" style="margin:0px;padding:0px;"><b><span style="color:green;">+' . $monitorfavimagenes['amount'] . '</span> - <a href="/perfil/' . $monitorfavimagenes['realName'] . '" title="' . $monitorfavimagenes['realName'] . '"><span style="color:orange;">' . $monitorfavimagenes['realName'] . '</span></a></b></p><hr />
';
}
}
else
{
echo '<div class="noesta">Nada por aca...</div> ';
}
echo '</div></div> </div>
';

/* Últimos Puntos Obtenidos */

echo '<div style="float:left;width: 212px;margin-bottom:8px;">
<div class="publicidad" style="margin-bottom:8px;"><div class="box_title" style="width: 212px;"><div class="box_txt publicidad_r"><center>Puntos obtenidos (posts)</center></div><div class="box_rss"><img src="', $settings['images_url'], '/blank.gif" style="width: 14px; height: 12px;" border="0" /></div></div><div class="windowbg" style="width: 204px;padding:4px;">
';
if (!empty($context['monitorpun'])){
foreach ($context['monitorpun'] as $monitorpun){
echo '<img alt="'.$monitorpun['bname'].'" title="'.$monitorpun['bname'].'" src="', $settings['images_url'], '/post/icono_'.$monitorpun['ID_BOARD'].'.gif" /> <b><a href="/post/'.$monitorpun['ID_TOPIC'].'/'.$monitorpun['description'].'/'.ssi_amigable($monitorpun['titulo']).'.html" title="'.censorText($monitorpun['titulo']).'">'.$monitorpun['titulo'].'</a></b><br /><p align="right" class="size11" style="margin:0px;padding:0px;"><b><span style="color:green;">+'.$monitorpun['amount'].'</span> - <a href="/perfil/'.$monitorpun['realName'].'" title="'.$monitorpun['realName'].'"><span style="color:orange;">'.$monitorpun['realName'].'</span></a></b></p><hr />';}}
else 
{
echo '<div class="noesta">Nada por aca...</div> </div></div>';
}
echo '</div></div>';

/* Mis posts en favoritos */
echo '<div class="publicidad" style="margin-bottom:8px;"><div class="box_title" style="width: 212px;"><div class="box_txt publicidad_r"><center>Mis posts en favorito</center></div><div class="box_rss"><img  src="', $settings['images_url'], '/blank.gif" style="width: 14px; height: 12px;" border="0"/></div></div><div class="windowbg" style="width: 204px;padding:4px;">
';
if (!empty($context['monitorfav'])){
foreach ($context['monitorfav'] as $monitorfav){
echo '<img alt="'.$monitorfav['bname'].'" title="'.$monitorfav['bname'].'" src="', $settings['images_url'], '/post/icono_'.$monitorfav['ID_BOARD'].'.gif" /> <b><a href="/post/'.$monitorfav['ID_TOPIC'].'/'.$monitorfav['description'].'/'.ssi_amigable($monitorfav['titulo']).'.html" title="'.censorText($monitorfav['titulo']).'">'.$monitorfav['titulo'].'</a></b><br /><p align="right" class="size11" style="margin:0px;padding:0px;"><b>Lo agreg&oacute;: <a href="/perfil/'.$monitorfav['realName'].'" title="'.$monitorfav['realName'].'"><span style="color:orange;">'.$monitorfav['realName'].'</span></a></b></p><hr />';}}
else
{
echo'<div class="noesta">Nada por aca...</div>';
}
echo ' </div></div>';

echo '<div class="publicidad" style="margin-bottom:8px;"><div class="box_title" style="width: 212px;"><div class="box_txt publicidad_r"><center>Yo en amigos</center></div><div class="box_rss"><img  src="', $settings['images_url'], '/blank.gif" style="width: 14px; height: 12px;" border="0"/></div></div><div class="windowbg" style="width: 204px;padding:4px;-align:left">
';
if (!empty($context['yoamigos'])){
foreach ($context['yoamigos'] as $monitoramigos){
echo '<b>Qui&eacute;n:</b> <a href="/perfil/', $monitoramigos['memberName'], '" title="', $monitoramigos['realName'], '">', $monitoramigos['realName'], '</a><br /><p align="right" class="size11" style="margin:0px;padding:0px;"><b>Cu&aacute;ndo: <span style="color:orange;">', $monitoramigos['time_updated'], '</span></a></b></p><hr />
';}}
else
{
echo '<div class="noesta">Nada por aca...</div>';
}
echo '</div></div>';
echo ' </div></div>

<div style="clear:both"></div>';
}
?>