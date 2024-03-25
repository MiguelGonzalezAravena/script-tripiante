<?php
// Version: 1.0; TOS

function template_manual_above(){}
function template_manual_below(){}
function template_manual_intro()
{
	global $context, $scripturl, $txt, $modSettings, $settings, $boardurl, $mbname;

	echo'
	<div id="cuerpocontainer">

<div><div class="box_buscador"><div class="box_title" style="width: 920px;"><div class="box_txt box_buscadort"><center>T&eacute;rminos y Condiciones</center></div><div class="box_rss"><img alt="" src="', $settings['images_url'] ,'/blank.gif" style="width: 14px; height: 12px;" border="0"></div></div><div class="windowbg" style="width:912px;padding:4px;"><center><p align="left"><i>El acceso a este sitio y la utilizaci&oacute;n de sus servicios est&aacute; sujeta a los t&eacute;rminos y condiciones generales que se establecen a continuaci&oacute;n:</i><br /><br /><b>1.</b> ', $mbname ,' se reserva el derecho de admisi&oacute;n y/o permanencia de los usuarios en &eacute;sta Web.<br /><b>2.</b> ', $mbname ,' no se hace responsable por alg&uacute;n problema t&uacute;cnico, que pueda generar alg&uacute;n archivo o programa bajado de &eacute;sta Web.
<br />
<b>3.</b> ', $mbname ,' no se hace responsable del mal uso de de los archivos aportados, ya sean Programas, M&uacute;sica, Libros, Etc.
<br />
<b>4.</b> El acceso a ', $mbname ,' es libre y sin restrincci&oacute;n de edades.

<br />
<b>5.</b> Es bueno saber que en ', $mbname ,' no se encuentra nada alojado aca. es decir, los enlaces que se encuentran en internet son agregados a trav&eacute;s de Posts/Topicos en ', $mbname ,'.
<br />
<b>6.</b> Una vez registrado en la web es obligatoria leer y respetar el <a href="/protocolo/" target="_blank" title="Protocolo">protocolo</a>, ya que son nuestras normas de la Web.
<br />
<b>7.</b> Una vez registrado en ', $mbname ,'!, quiere decir que ha aceptado los t&eacute;rminos y condiciones de ', $mbname ,'. por lo tanto, tiene que respetar el <a href="/protocolo/" target="_blank" title="Protocolo">protocolo</a>, ya que son nuestras normas que debe cumplir cada usuario de ', $mbname ,' para poder mantener su puesto, de lo contrario se aplicaran sansiones como por ejemplo la expulsaci&oacute;n.
<br />

<b>8.</b> ', $mbname ,' como opci&oacute;n predeterminada no revela datos personales del usuario. Solo se logra revelar cuando el usuario acepta revelarlos.
<br /></p>
 <center>&copy; ', $mbname ,' 2010. Todos los derechos reservados. Prohibida su reproducci&oacute;n total o parcial.<br /><br />
<span class="size11">Sitio programado y dise&ntilde;ado completamente por <a href="/perfil/Miguelithox">Miguelithox</a> y <a href="/perfil/Peludo_08">Peludo_08</a>.</span></center></center></div></div></div>';

}

?>