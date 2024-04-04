<?php
// Version: 1.1.5; ModSettings

$txt['smf3'] = 'Esta p&aacute;gina te permite cambiar la configuraci&oacute;n de  las caracter&iacute;sticas, mods, y opciones b&aacute;sicas de tu foro.  Por favor revisa la <a href="' . $scripturl . '?action=theme;sa=settings;th=' . $settings['theme_id'] . ';sesc=' . $context['session_id'] . '">configuraci&oacute;n del tema</a> para m&aacute;s opciones.  Haz <i>clic</i> en los iconos de ayuda para m&aacute;s informaci&oacute;n acerca de alguna opci&oacute;n.';

$txt['mods_cat_features'] = 'Caracter&iacute;sticas b&aacute;sicas';
$txt['pollMode'] = 'Modo Encuesta';
$txt['smf34'] = 'Desactivar Encuestas';
$txt['smf32'] = 'Activar Encuestas';
$txt['smf33'] = 'Mostrar Encuestas como temas';
$txt['allow_guestAccess'] = 'Permitir a los visitantes navegar en el foro';
$txt['userLanguage'] = 'Activar Idioma seleccionado por el usuario';
$txt['allow_editDisplayName'] = '&iquest;Permitirle a los usuarios modificar su nombre?';
$txt['allow_hideOnline'] = '&iquest;Permitirle a los usuarios NO administradores ocultarse?';
$txt['allow_hideEmail'] = 'Permitirle a los usuarios esconder su email del p&uacute;blico (excepto a administradores)';
$txt['guest_hideContacts'] = 'No revelar detalles de contacto de los usuarios a los visitantes';
$txt['titlesEnable'] = 'Activar T&iacute;tulos Personalizado';
$txt['enable_buddylist'] = 'Activar Listas de Amigos';
$txt['default_personalText'] = 'Texto Personal por defecto';
$txt['max_signatureLength'] = 'N&uacute;mero m&aacute;ximo de caracteres permitido en firmas<div class="smalltext">(0 para que no haya m&aacute;x)</div>';
$txt['number_format'] = 'Formato de n&uacute;meros por defecto';
$txt['time_format'] = 'Formato de Tiempo por defecto';
$txt['time_offset'] = 'Diferencia horaria global<div class="smalltext">(agregado a las opciones espec&iacute;ficas de los usuarios.)</div>';
$txt['failed_login_threshold'] = 'Tiempo de espera al fallar un ingreso';
$txt['lastActive'] = 'Tiempo despu&eacute;s de su &uacute;ltima acci&oacute;n durante el cual los usuarios aparecer&aacute;n en l&iacute;nea';
$txt['trackStats'] = 'Rastrear Estad&iacute;sticas';
$txt['hitStats'] = 'Rastrear Hits (deben estar activadas las estad.)';
$txt['enableCompressedOutput'] = 'Activar Compresi&oacute;n de Salida';
$txt['databaseSession_enable'] = 'Usar sesiones almacenadas en la base de datos';
$txt['databaseSession_loose'] = 'Permitirle a los navegadores regresar a las p&aacute;ginas en el cache';
$txt['databaseSession_lifetime'] = 'Segundos para que expire una sesi&oacute;n no utilizada';
$txt['enableErrorLogging'] = 'Activar log de errores';
$txt['cookieTime'] = 'Duraci&oacute;n por defecto de las cookies para el ingreso (en minutos)';
$txt['localCookies'] = 'Activar el almacenamiento local de cookies<div class="smalltext">(SSI no funcionar&aacute; a&uacute;n con esto activado.)</div>';
$txt['globalCookies'] = '&iquest;Usar cookies independientes de subdominio?<div class="smalltext">&iexcl;Advertencia: Hay que deshabilitar las cookies locales primero!</div>';
$txt['securityDisable'] = '&iquest;Desactivar la seguridad en la administraci&oacute;n?';
$txt['send_validation_onChange'] = 'Enviar por email nueva contrase&ntilde;a si el usuario cambia su direcci&oacute;n de email';
$txt['approveAccountDeletion'] = 'Requerir la aprobaci&oacute;n de un administrador cuando un usuario borre su cuenta';
$txt['autoOptDatabase'] = '&iquest;Optimizar tablas cada cuantos d&iacute;as?<div class="smalltext">(0 para desactivar)</div>';
$txt['autoOptMaxOnline'] = 'M&aacute;ximos usuarios en l&iacute;nea mientras se optimiza<div class="smalltext">(0 para que no haya m&aacute;x)</div>';
$txt['autoFixDatabase'] = 'Arreglar tablas con problemas autom&aacute;ticamente';
$txt['allow_disableAnnounce'] = 'Permitir a los usuarios desactivar el recibir notificaciones de \'Foros de Anuncios\'';
$txt['disallow_sendBody'] = '&iquest;No permitir enviar el texto del mensaje en las notificaciones?';
$txt['modlog_enabled'] = 'Guardar log de las acciones de moderaci&oacute;n';
$txt['queryless_urls'] = 'Mostrar URLs sin ?s<div class="smalltext"><b>&iexcl;S&oacute;lo Apache!</b></div>';
$txt['max_image_width'] = 'Ancho m&aacute;ximo de las im&aacute;genes en los mensajes (0 = desactivar)';
$txt['max_image_height'] = 'Altura m&aacute;xima de las im&aacute;genes en los mensajes (0 = desactivar)';
$txt['mail_type'] = 'Tipo de Correo';
$txt['mail_type_default'] = '(Predeterminado de PHP)';
$txt['smtp_host'] = 'Servidor SMTP';
$txt['smtp_port'] = 'Puerto SMTP';
$txt['smtp_username'] = 'Usuario SMTP';
$txt['smtp_password'] = 'Contrase&ntilde;a SMTP';
$txt['enableReportPM'] = 'Activar el aviso de mensajes privados';
$txt['max_pm_recipients'] = 'M&aacute;ximo n&uacute;mero de destinatarios permitidos en un mensaje privado.<div class="smalltext">(0 para ilimitado, exceptuando admins)</div>';
$txt['pm_posts_verification'] = 'Usuarios con un n&uacute;mero de mensajes por debajo del establecido, deber&aacute;n introducir un c&oacute;digo cuando envien un mensaje personal.<div class="smalltext">(0 para ilimitados, administradores est&aacute;n exentos)</div>';
$txt['pm_posts_per_hour'] = 'N&uacute;mero de mensajes personales que pueden ser enviados por un usuario en una hora.<div class="smalltext">(0 para ilimitados, moderadores est&aacute;n exentos)</div>';

$txt['mods_cat_layout'] = 'Dise&ntilde;o (Temas)';
$txt['compactTopicPagesEnable'] = 'Activar Mod de Tema Compacto';
$txt['smf235'] = 'N&uacute;mero de p&aacute;ginas contiguas a mostrar:';
$txt['smf236'] = 'para mostrar';
$txt['todayMod'] = 'Activar Mod de Hoy';
$txt['smf290'] = 'Desactivado';
$txt['smf291'] = 'S&oacute;lo Hoy';
$txt['smf292'] = 'Hoy y Ayer';
$txt['topbottomEnable'] = 'Activar botones Ir Arriba/Ir Abajo';
$txt['onlineEnable'] = 'Mostrar Conectado/Desconectado en mensajes y en MP';
$txt['enableVBStyleLogin'] = 'Activar ingreso estilo VB';
$txt['defaultMaxMembers'] = 'Usuarios por p&aacute;gina en la Lista Completa de Usuarios';
$txt['timeLoadPageEnable'] = 'Mostrar tiempo tomado para crear cada p&aacute;gina';
$txt['disableHostnameLookup'] = '&iquest;Desactivar la b&uacute;squeda de los nombres de los servidores?';
$txt['who_enabled'] = 'Activar Qui&eacute;n est&aacute; en l&iacute;nea';

$txt['smf293'] = 'Karma ';
$txt['karmaMode'] = 'Modo de Karma';
$txt['smf64'] = 'Desactivar Karma|Activar Karma Total|Activar Karma Positivo/Negativo';
$txt['karmaMinPosts'] = 'Especifica el m&iacute;nimo n&uacute;mero de mensajes necesarios para modificar el karma';
$txt['karmaWaitTime'] = 'Especifica el tiempo de espera en horas';
$txt['karmaTimeRestrictAdmins'] = 'Restringir Administradores a esperar';
$txt['karmaLabel'] = 'Etiqueta del Karma';
$txt['karmaApplaudLabel'] = 'Etiqueta Karma para aplaudir';
$txt['karmaSmiteLabel'] = 'Etiqueta Karma para castigar';

$txt['caching_information'] = '<div align="center"><b><u>&iexcl;Importante! Lee esto antes de activar estas caracter&iacute;sticas.</b></u></div><br />
  SMF soporta hacer cach&eacute; a trav&eacute;s del uso de aceleradores. Los aceleradores soportados actualmente son:<br />
  <ul>
    <li>APC</li>
    <li>eAccelerator</li>
    <li>Turck MMCache</li>
    <li>Memcached</li>
    <li>Zend Platform/Performance Suite (No es Zend Optimizer)</li>
  </ul>
  Hacer cache solamente funcionar&aacute; en tu servidor si tienes PHP compilado con uno de los optimizadores arriba mencionados, o si tienes memcache 
  disponible. <br /><br />
  SMF realiza el cache en varios niveles. A mayor nivel de cache, m&aacute;s CPU ser&aacute; utilizado
  cuando se lea la informaci&oacute;n en el cache. Si dispones de esta funcionalidad te recomendamos que intentes primero hacer cache en el nivel 1.
  <br /><br />
  Ten en cuenta que si usas memcached debes proveer los detalles del servidor en la configuraci&oacute;n a continuaci&oacute;n. Esto debe ser introducido como una lista separada por comas
  como se muestra en el ejemplo a continuaci&oacute;n:<br />
  &quot;servidor1,servidor2,servidor3:puerto,servidor4&quot;<br /><br />
  Cabe mencionar que si el puerto no es especificado SMF usar&aacute; el puerto 11211. SMF intentar&aacute; realizar un balanceo de carga aleatorio entre los servidores.
  <br /><br />
  %s
  <hr />';

$txt['detected_no_caching'] = '<b style="color: red;">SMF no ha detectado ning&uacute;n acelerador compatible instalado en tu servidor.</b>';
$txt['detected_APC'] = '<b style="color: green">SMF ha detectado que tu servidor tiene instalado APC.';
$txt['detected_eAccelerator'] = '<b style="color: green">SMF ha detectado que tu servidor tiene instalado eAccelerator.';
$txt['detected_MMCache'] = '<b style="color: green">SMF ha detectado que tu servidor tiene instalado MMCache.';
$txt['detected_Zend'] = '<b style="color: green">SMF ha detectado que tu servidor tiene instalado Zend.';
$txt['detected_Memcached'] = '<b style="color: green">SMF ha detectado que tu servidor tiene Memcached instalado. ';

$txt['cache_enable'] = 'Nivel de Cache';
$txt['cache_off'] = 'Sin cache';
$txt['cache_level1'] = 'Nivel 1 de Cach&eacute;';
$txt['cache_level2'] = 'Nivel 2 de Cach&eacute; (No Recomendado)';
$txt['cache_level3'] = 'Nivel 3 de Cach&eacute; (No Recomendado)';
$txt['cache_memcached'] = 'Configuraci&oacute;n de Memcache';
$txt['other_options'] = 'Otras Opciones';
$txt['options_profile_comments'] = 'Opciones del muro en el perfil (Comentarios)';
$txt['time_profile_comment'] = 'Tiempo a esperar por cada comentario en el muro del perfil del usuario (en segundos)';
$txt['characters_limit_profile_comment'] = 'N&uacute;mero de car&aacute;cteres permitidos en el comentario.-';
$txt['characters_limit_quehago'] = 'L&iacute;mite de car&aacute;cteres del &#191;Qu&eacute; est&aacute;s haciendo ahora&#63;';
$txt['preview'] = 'Opciones de la vista previa (Cuando vas a crear / modificar un post)';
$txt['title_post_preview'] = 'N&uacute;mero de car&aacute;cteres m&iacute;nimos para visualizar un post (T&iacute;tulo).-';
$txt['body_post_preview'] = 'N&uacute;mero de car&aacute;cteres m&iacute;nimos para visualizar un post (Mensaje).-';
$txt['post_options'] = 'Opciones de los posts, im&aacute;genes y comentarios.-';
$txt['characters_limit_comments'] = 'L&iacute;mite de c&aacute;racteres permitidos en el comentario del post.-';
$txt['index_options'] = 'Opciones del &Iacute;ndice de tu sitio';
$txt['number_posts'] = 'L&iacute;mite de posts.-';
$txt['number_comments'] = 'L&iacute;mite de comentarios.-';
$txt['number_tops'] = 'L&iacute;mite de TOPS posts.-';
$txt['number_images'] = 'L&iacute;mite de &Uacute;ltimas im&aacute;genes.-';
$txt['monitor_options'] = 'Opciones del monitor';
$txt['monitor_post_comments'] = 'N&uacute;mero de comentarios a mostrar en "Comentarios en mis posts"';
$txt['monitor_image_comments'] = 'N&uacute;mero de comentarios a mostrar en "Comentarios en mis im&aacute;genes"';
$txt['monitor_image_bookmarks'] = 'N&uacute;mero de favoritos a mostrar en "Mis im&aacute;genes en favoritos"';
$txt['monitor_image_points'] = 'N&uacute;mero de puntos a mostrar en "Puntos obtenidos (im&aacute;genes)"';
$txt['monitor_post_points'] = 'N&uacute;mero de puntos a mostrar en "Puntos obtenidos (posts)"';
$txt['monitor_post_bookmarks'] = 'N&uacute;mero de favoritos a mostrar en "Mis posts en favoritos"';
$txt['monitor_friends'] = 'N&uacute;mero de amigos a mostrar en "Yo en amigos"';
$txt['bookmarks_options'] = 'Opciones de Favoritos';
$txt['bookmarks_posts'] = 'Cantidad de favoritos por p&aacute;gina (Posts)';
$txt['bookmarks_images'] = 'Cantidad de favoritos por p&aacute;gina (Im&aacute;genes)';
$txt['profile_comments_limit'] = 'L&iacute;mite de comentarios por p&aacute;gina.';
$txt['profile_posts_limit'] = 'L&iacute;mite de posts en su perfil';
$txt['profile_images_limit'] = 'L&iacute;mite de im&aacute;genes en su perfil';
$txt['user_posts'] = 'L&iacute;mite de posts por p&aacute;gina';
$txt['user_comments_posts'] = 'L&iacute;mite de comentarios en posts por p&aacute;gina';
$txt['user_comments_images'] = 'L&iacute;mite de comentarios en im&aacute;genes por p&aacute;gina';
$txt['user_images'] = 'L&iacute;mite de im&aacute;genes por p&aacute;gina';
$txt['user_friends'] = 'L&iacute;mite de amigos por p&aacute;gina';
$txt['user_friends2'] = 'L&iacute;mite de amigos en com&uacute;n por p&aacute;gina';
$txt['community_options'] = 'Opciones de Comunidades';
$txt['community_topics_general'] = 'L&iacute;mite de Temas por p&aacute;gina (&Iacute;ndice)';
$txt['community_comments_general'] = 'L&iacute;mite de &Uacute;ltimos Comentarios (&Iacute;ndice)';
$txt['community_tops'] = 'L&iacute;mite de TOPs Comunidades (&Iacute;ndice)';
$txt['community_latest'] = 'L&iacute;mite de &Uacute;ltimas comunidades (&Iacute;ndice)';
$txt['community_topics'] = 'L&iacute;mite de Temas por p&aacute;gina (Dentro de una comunidad)';
$txt['community_comments'] = 'L&iacute;mite de &Uacute;ltimos Comentarios (Dentro de una comunidad)';
$txt['community_members'] = 'L&iacute;mite de &Uacute;ltimos Miembros  (Dentro de una comunidad)';
$txt['general_options'] = 'General';
$txt['mod_history'] = 'L&iacute;mite de Logs en el Historial de Moderaci&oacute;n';
$txt['denunciations'] = 'L&iacute;mite de Denuncias (Todas) por p&aacute;gina';
$txt['notes'] = 'L&iacute;mite de Notas por p&aacute;gina';
$txt['proxyblock_reg'] = 'Bloquear registro si usas proxy';
$txt['proxyblock_index'] = 'Bloquear web si usas proxy';
$txt['radio'] = 'Activar Radio';
?>