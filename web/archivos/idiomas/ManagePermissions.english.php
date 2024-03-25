<?php
// Version: 1.1; ManagePermissions

$txt['permissions_title'] = 'Establecer Permisos';
$txt['permissions_modify'] = 'Modificar';
$txt['permissions_access'] = 'Tiene acceso';
$txt['permissions_allowed'] = 'Permitido';
$txt['permissions_denied'] = 'Denegado';

$txt['permissions_switch'] = 'Cambiar a';
$txt['permissions_global'] = 'Global';
$txt['permissions_local'] = 'Local ';

$txt['permissions_groups'] = 'Permisos por Grupo de usuarios';
$txt['permissions_all'] = 'todo';
$txt['permissions_none'] = 'ninguno';
$txt['permissions_set_permissions'] = 'Establecer permisos';

$txt['permissions_with_selection'] = 'Con selecci&oacute;n';
$txt['permissions_apply_pre_defined'] = 'Aplicar el perfil predefinido de permisos';
$txt['permissions_select_pre_defined'] = 'Seleccionar un perfil predeterminado';
$txt['permissions_copy_from_board'] = 'Copiar permisos de este Foro';
$txt['permissions_select_board'] = 'Seleccionar el foro';
$txt['permissions_like_group'] = 'Establecer los permisos como los de este grupo';
$txt['permissions_select_membergroup'] = 'Seleccionar un grupo de usuarios';
$txt['permissions_add'] = 'Agregar Permiso';
$txt['permissions_remove'] = 'Limpiar Permiso';
$txt['permissions_deny'] = 'Denegar Permiso';
$txt['permissions_select_permission'] = 'Seleccionar un permiso';

// All of the following block of strings should not use entities, instead use \\" for &quot; etc.
$txt['permissions_only_one_option'] = 'Puedes seleccionar solamente una acci&#243;n para modificar los permisos';
$txt['permissions_no_action'] = 'No se seleccion&#243; ninguna acci&#243;n';
$txt['permissions_deny_dangerous'] = 'Est&#225;s a punto de negar uno o m&#225;s permisos.\\nEsto puede ser peligroso y causar resultados inesperados si \\"accidentalmente\\" dejaste a alguien en los grupos a los que les negar&#225;s permisos.\\n\\n&#191;Est&#225;s seguro que deseas continuar?';

$txt['permissions_boards'] = 'Permisos por Foro';

$txt['permissions_modify_group'] = 'Modificar Grupo';
$txt['permissions_general'] = 'Permisos Generales';
$txt['permissions_board'] = 'Permisos Globales del Foro';
$txt['permissions_commit'] = 'Guardar cambios';
$txt['permissions_modify_local'] = 'Modificar Permisos Locales';
$txt['permissions_on'] = 'en el foro';
$txt['permissions_local_for'] = 'Permisos Locales por grupo';
$txt['permissions_option_on'] = 'P';
$txt['permissions_option_off'] = 'X';
$txt['permissions_option_deny'] = 'N';
$txt['permissions_option_desc'] = 'Para cada permiso puedes escoger \'Permitir\' (P), \'Rechazar\' (X), o <span style="color: red;">\'Negar\' (N)</span>.<br /><br />Recuerda que si niegas un permiso, cualquier usuario - sea moderador o cualquier otra cosa - que se encuentre en ese grupo ser&aacute; negado tambi&eacute;n.<br />Por esta raz&oacute;n, debes usar negar con cautela, solamente cuando sea <b>necesario</b>. Rechazar, por el contrario, niega el acceso a menos que haya sido permitido.';

$txt['permissiongroup_general'] = 'General ';
$txt['permissionname_view_stats'] = 'Ver las estad&iacute;sticas del foro';
$txt['permissionhelp_view_stats'] = 'Las estad&iacute;sticas del foro es una p&aacute;gina resumiendo todas las estad&iacute;sticas del foro, tales como el n&uacute;mero de usuarios, n&uacute;mero diario de mensajes, y varias estad&iacute;sticas de los 10 mejores. Activar este permiso agrega un enlace en la parte inferior del &iacute;ndice del foro (\'[M&aacute;s estad&iacute;sticas]\').';
$txt['permissionname_view_mlist'] = 'Ver la lista de usuarios';
$txt['permissionhelp_view_mlist'] = 'La lista de usuarios muestra a todos los usuarios que se han registrado en tu foro. La lista puede ser ordenada, as&iacute; como buscar en ella. La lista de usuarios de enlaza desde el &iacute;ndice del foro y la p&aacute;gina de estad&iacute;sticas, al hacer clic en el n&uacute;mero de usuarios.';
$txt['permissionname_who_view'] = 'Ver Qui&eacute;n est&aacute; en l&iacute;nea';
$txt['permissionhelp_who_view'] = 'Qui&eacute;n est&aacute; en l&iacute;nea muestra a todos los usuarios que est&aacute;n conectados, as&iacute; como lo que est&aacute;n haciendo. Este permiso funcionar&aacute; solamente si tienes activado tambi&eacute;n en \'Config. y Opciones\'. Puedes accesar la pantalla de \'Qui&eacute;n est&aacute; en l&iacute;nea\' haciendo clic en el enlace de la secci&oacute;n de \'Usuarios en l&iacute;nea\' en el &iacute;ndice del foro.';
$txt['permissionname_search_posts'] = 'Buscar por mensajes y temas';
$txt['permissionhelp_search_posts'] = 'El permiso para b&uacute;squeda le permite al usuario buscar en todos los foros a los que tiene acceso. Cuando el permiso de b&uacute;squeda est&aacute; activado, el bot&oacute;n de \'Buscar\' ser&aacute; agregado a la barra de botones del foro.';
$txt['permissionname_karma_edit'] = 'Cambiar el karma de otras personas';
$txt['permissionhelp_karma_edit'] = 'Karma es una funci&oacute;n que muestra la popularidad de un usuario. Para poder usar esta funci&oacute;n, necesitar tenerla activa en \'Config. y Opciones\'. Este permiso le permitir&aacute; a un grupo el poder votar. Este permiso no tiene efecto en invitados.';

$txt['permissiongroup_pm'] = 'Mensajer&iacute;a Personal';
$txt['permissionname_pm_read'] = 'Leer mensajes personales';
$txt['permissionhelp_pm_read'] = 'Este permiso le permite a los usuarios acceder a la secci&oacute;n de Mensajes Personales para leer sus mensajes. Sin este permiso, un usuario no puede enviar mensajes personales.';
$txt['permissionname_pm_send'] = 'Enviar mensajes personales';
$txt['permissionhelp_pm_send'] = 'Env&iacute;a mensajes personales a otros usuarios registrados. Necesita del permiso \'Leer mensajes personales\'.';

$txt['permissiongroup_calendar'] = 'Calendario';
$txt['permissionname_calendar_view'] = 'Ver el calendario';
$txt['permissionhelp_calendar_view'] = 'El calendario muestra para cada mes, los cumplea&ntilde;os, eventos y d&iacute;as festivos. Este permiso permite el acceso al calendario. Cuando este permiso est&aacute; activo, un bot&oacute;n ser&aacute; agregado a la barra de botones del foro y se mostrar&aacute; una lista en la parte inferior del &iacute;ndice del foro con los cumplea&ntilde;os, eventos y d&iacute;as festivos pr&oacute;ximos. El calendario necesita ser activado en desde \'Config. y Opciones\'.';
$txt['permissionname_calendar_post'] = 'Crear eventos en el calendario';
$txt['permissionhelp_calendar_post'] = 'Un evento es un tema enlazado a cierta fecha, o rango de fechas. Se puede hacer la creaci&oacute;n de eventos desde el calendario. Un evento puede crearse solamente si el usuario que crea el evento puede crear nuevos temas.';
$txt['permissionname_calendar_edit'] = 'Editar eventos en el calendario';
$txt['permissionhelp_calendar_edit'] = 'Un evento es un tema enlazado a cierta fecha, o rango de fechas. Los eventos pueden ser editados al hacer clic en el asterisco rojo (*) al lado del evento, en la vista del calendario. Para poder modificar un evento, el usuario debe tener los permisos necesarios para editar el primer mensaje del tema que est&aacute; enlazado al evento.';
$txt['permissionname_calendar_edit_own'] = 'Eventos propios';
$txt['permissionname_calendar_edit_any'] = 'Cualquier evento';

$txt['permissiongroup_maintenance'] = 'Administraci&oacute;n del Foro';
$txt['permissionname_admin_forum'] = 'Administrar el foro y la base de datos';
$txt['permissionhelp_admin_forum'] = 'Este permiso le permite a un usuario cambiar la config. del foro y de los temas, manejar paquetes, y usar las herramientas de mantenimiento del foro y la base de datos. Usa este permiso con precacuci&oacute;n, ya que es muy poderoso.';
$txt['permissionname_manage_boards'] = 'Administrar foros y categor&iacute;as';
$txt['permissionhelp_manage_boards'] = 'Este permiso permite la creaci&oacute;n, edici&oacute;n y eliminaci&oacute;n de foros y categor&iacute;as.';
$txt['permissionname_manage_attachments'] = 'Administrar archivos adjuntos y avatares';
$txt['permissionhelp_manage_attachments'] = 'Este permiso permite el acceso al centro de archivos adjuntos, donde est&aacute;n listados todos los archivos adjuntos y avatares del foro, y de donde pueden ser eliminados.';
$txt['permissionname_manage_smileys'] = 'Administrar smileys';
$txt['permissionhelp_manage_smileys'] = 'Este permite accesar al centro de smileys. En el centro de smileys puedes a&ntilde;adir, editar y emilinar smileys y conjuntos de smileys.';
$txt['permissionname_edit_news'] = 'Editar Noticias';
$txt['permissionhelp_edit_news'] = 'La funci&oacute;n de noticias permite que en cada pantalla aparezca una l&iacute;nea aleatoria de noticias. Para usar la funci&oacute;n de noticias, activala en la Config. del foro.';

$txt['permissiongroup_member_admin'] = 'Administraci&oacute;n de usuarios';
$txt['permissionname_moderate_forum'] = 'Moderar usuarios del foro';
$txt['permissionhelp_moderate_forum'] = 'Estos permisos incluyen funciones importantes de la moderaci&oacute;n de usuarios:<ul><li>acceso al mantenimiento de registro</li><li>acceso a ver/eliminar usuarios</li><li>informaci&oacute;n extensa de perfil, incluyendo IP del usuario (oculta) y estado</li><li>activar cuentas</li><li>obtener aprobaci&oacute;n de notificaciones y aprobar cuentas</li><li>immune a ignorar MP</li><li>muchas peque&ntilde;as cosas</li></ul>';
$txt['permissionname_manage_membergroups'] = 'Administrar y asignar grupos de usuarios';
$txt['permissionhelp_manage_membergroups'] = 'Este permiso le permite un usuario editar los grupos de usuarios y asignarle grupos de usuarios a otros usuarios.';
$txt['permissionname_manage_permissions'] = 'Administrar permisos';
$txt['permissionhelp_manage_permissions'] = 'Este permiso le permite a un usuario editar todos los permisos de un grupo de usuarios, en foros individuales o globalmente.';
$txt['permissionname_manage_bans'] = 'Administrar la lista de accesos prohibidos';
$txt['permissionhelp_manage_bans'] = 'Este permiso le permite a un usuario agregar o eliminar usuarios, direcciones IP, nombres de servidores y direcciones de email de la lista de usuarios con acceso prohibido. Tambi&eacute;n les permite ver y eliminar las entradas del log de los usuarios con acceso prohibido que intentaron entrar.';
$txt['permissionname_send_mail'] = 'Enviar un email del foro a los usuarios';
$txt['permissionhelp_send_mail'] = 'Env&iacute;o masivo de emails a todos los usuarios del foro, o solamente a algunos grupos de usuarios v&iacute;a email o mensaje personal (este &uacute;ltimo necesita el permiso \'Enviar Mensaje Personal\').';

$txt['permissiongroup_profile'] = 'Perfiles de Usuarios';
$txt['permissionname_profile_view'] = 'Ver resumen del perfil y estad&iacute;sticas';
$txt['permissionhelp_profile_view'] = 'Este permiso le permite a los usuarios hacer clic en un nombre de usuario para ver un resumen de la configuraci&oacute;n de su perfil, algunas estad&iacute;sticas, y todos los mensajes del usuario.';
$txt['permissionname_profile_view_own'] = 'Perfil propio';
$txt['permissionname_profile_view_any'] = 'Cualquier perfil';
$txt['permissionname_profile_identity'] = 'Editar la config. de la cuenta';
$txt['permissionhelp_profile_identity'] = 'La config. de la cuenta son las caracter&iacute;sticas b&aacute;sicas de un perfil, tales como contrase&ntilde;a, direcci&oacute;n de email, grupo, e idioma preferido.';
$txt['permissionname_profile_identity_own'] = 'Perfil propio';
$txt['permissionname_profile_identity_any'] = 'Cualquier perfil';
$txt['permissionname_profile_extra'] = 'Editar la config. adicional del perfil';
$txt['permissionhelp_profile_extra'] = 'La config. adicional del perfil incluye datos del avatar, preferencias del tema, notificaciones, y mensajes personales.';
$txt['permissionname_profile_extra_own'] = 'Perfil propio';
$txt['permissionname_profile_extra_any'] = 'Cualquier perfil';
$txt['permissionname_profile_title'] = 'Editar t&iacute;tulo personalizado';
$txt['permissionhelp_profile_title'] = 'El t&iacute;tulo personalizado se muestra en la p&aacute;gina donde se muestran temas, y debajo del perfil de cada usuario que tiene un t&iacute;tulo personalizado.';
$txt['permissionname_profile_title_own'] = 'Perfil propio';
$txt['permissionname_profile_title_any'] = 'Cualquier perfil';
$txt['permissionname_profile_remove'] = 'Borrar cuenta';
$txt['permissionhelp_profile_remove'] = 'Este permiso le permite a un usuario borrar su propia cuenta, cuando teiene el valor de \'Cuenta propia\'.';
$txt['permissionname_profile_remove_own'] = 'Cuenta propia';
$txt['permissionname_profile_remove_any'] = 'Cualquier cuenta';
$txt['permissionname_profile_server_avatar'] = 'Seleccionar un avatar del servidor';
$txt['permissionhelp_profile_server_avatar'] = 'Si est&aacute; activo, permitir&acute; a un usuario seleccionar un avatar de la colecci&oacute;n instalada en el servidor.';
$txt['permissionname_profile_upload_avatar'] = 'Subir un avatar al servidor';
$txt['permissionhelp_profile_upload_avatar'] = 'Permite a un usuario subir sus avatares personales al servidor.';
$txt['permissionname_profile_remote_avatar'] = 'Seleccionar un avatar remoto';
$txt['permissionhelp_profile_remote_avatar'] = 'Debido a que los avatares pueden influir negativamente en el tiempo requerido para crear una p&aacute;gina, es posible deshabilitar a algunos grupos de usar avatares almacenados en servidores externos. ';

$txt['permissiongroup_general_board'] = 'General ';
$txt['permissionname_moderate_board'] = 'Moderar foro';
$txt['permissionhelp_moderate_board'] = 'El permiso para moderar el foro agrega algunos pocos permisos que le permitan a un moderador ser un moderador real. Los permisos incluyen responder a temas bloqueados, cambiar la expiraci&oacute;n de una encuesta, y ver los resultados de &eacute;stas';

$txt['permissiongroup_topic'] = 'Temas';
$txt['permissionname_post_new'] = 'Publicar nuevos temas';
$txt['permissionhelp_post_new'] = 'Este permiso le permite a los usuarios publicar nuevos temas. No permite publicar respuestas a temas.';
$txt['permissionname_merge_any'] = 'Combinar cualquier tema';
$txt['permissionhelp_merge_any'] = 'Combinar dos o m&aacute;s temas en uno. El orden de los mensajes dentro del tema combinado se basar&aacute;n en la hora en la que los mensajes fueron creados. Un usuario solamente puede mezclar temas en los foros en los que se le permita a los usuarios combinar. Para combinar m&uacute;ltiples en uno, un usuario debe activar la moderaci&oacute;n r&aacute;pida en la config. de su perfil.';
$txt['permissionname_split_any'] = 'Dividir cualquier tema';
$txt['permissionhelp_split_any'] = 'Dividir un tema en dos temas separados.';
$txt['permissionname_send_topic'] = 'Enviar temas a amigos';
$txt['permissionhelp_send_topic'] = 'Este permiso le permite a un usuario enviar un tema por email a un amigo, al introducir su direcci&oacute;n de email, permite asimismo agregar un mensaje.';
$txt['permissionname_make_sticky'] = 'Fijar temas';
$txt['permissionhelp_make_sticky'] = 'Los temas fijados son temas que siempre permanecen en la parte superior de un foro. Pueden ser &uacute;tiles para anuncios u otros mensajes importantes.';
$txt['permissionname_move'] = 'Mover temas';
$txt['permissionhelp_move'] = 'Mover un tema de un foro a otro. Los usuarios pueden seleccionar solamente foros destinos a los que tengan acceso.';
$txt['permissionname_move_own'] = 'Tema propio';
$txt['permissionname_move_any'] = 'Cualquier tema';
$txt['permissionname_lock'] = 'Bloquear temas';
$txt['permissionhelp_lock'] = 'Este permiso le permite a un usuario bloquear un tema. Esto se puede hacer para asegurarse que nadie responda a un tema. Solamente usuarios con el permiso de \'Moderar foro\' pueden todav&iacute;a publicar en temas bloqueados.';
$txt['permissionname_lock_own'] = 'Tema propio';
$txt['permissionname_lock_any'] = 'Cualquier tema';
$txt['permissionname_remove'] = 'Borrar temas';
$txt['permissionhelp_remove'] = 'Borrar temas completos. &iexcl;Este permiso no le permite borrar mensajes espec&iacute;ficos dentro del tema!';
$txt['permissionname_remove_own'] = 'Tema propio';
$txt['permissionname_remove_any'] = 'Cualquier tema';
$txt['permissionname_post_reply'] = 'Publicar respuestas a temas';
$txt['permissionhelp_post_reply'] = 'Este permiso permite el responder a temas.';
$txt['permissionname_post_reply_own'] = 'Tema propio';
$txt['permissionname_post_reply_any'] = 'Cualquier tema';
$txt['permissionname_modify_replies'] = 'Modificar respuestas a los temas propios';
$txt['permissionhelp_modify_replies'] = 'Este permiso le permite a un usuario que inici&oacute; un tema, el modificar todas las respuestas en su tema.';
$txt['permissionname_delete_replies'] = 'Borrar respuestas en sus temas propios';
$txt['permissionhelp_delete_replies'] = 'Este permiso le permite a un usuario que inici&oacute; un tema eliminar todas las respuestas en su tema.';
$txt['permissionname_announce_topic'] = 'Tema de anuncios';
$txt['permissionhelp_announce_topic'] = 'Esto permite enviar un email de anuncios acerca de un tema a todos los usuarios o solamente a algunos grupos de usuarios.';

$txt['permissiongroup_post'] = 'Mensajes';
$txt['permissionname_delete'] = 'Borrar mensajes';
$txt['permissionhelp_delete'] = 'Borrar mensajes. Esto no le permite a un usuario borrar el primer mensaje de un tema.';
$txt['permissionname_delete_own'] = 'Mensajes propios';
$txt['permissionname_delete_any'] = 'Cualquier mensaje';
$txt['permissionname_modify'] = 'Modificar mensajes';
$txt['permissionhelp_modify'] = 'Editar mensajes';
$txt['permissionname_modify_own'] = 'Mensajes propios';
$txt['permissionname_modify_any'] = 'Cualquier mensaje';
$txt['permissionname_report_any'] = 'Reportar mensajes a los moderadores';
$txt['permissionhelp_report_any'] = 'Este permiso agrega un enlace a cada mensaje, permitiendole a un usuario reportar el mensaje al moderador. Al reportarlo, todos los moderadores de ese foro recibir&aacute;n un email con un enlace al mensaje reportado y una descripci&oacute;n del problema (proporcionado por el usuario que lo report&oacute;).';

$txt['permissiongroup_poll'] = 'Encuestas';
$txt['permissionname_poll_view'] = 'Ver encuestas';
$txt['permissionhelp_poll_view'] = 'Este permiso le permite a un usuario ver una encuesta. Sin este permiso, el usuario ver&aacute; solamente el tema.';
$txt['permissionname_poll_vote'] = 'Votar en encuestas';
$txt['permissionhelp_poll_vote'] = 'Este permiso le permite a un usuario emitir un voto. Esto no aplica a invitados.';
$txt['permissionname_poll_post'] = 'Publicar encuestas';
$txt['permissionhelp_poll_post'] = 'Este permiso le permite a un usuario publicar una nueva encuesta.';
$txt['permissionname_poll_add'] = 'Agregar encuestas a temas';
$txt['permissionhelp_poll_add'] = 'Agregar encuestas a temas le permite a un usuario agregar una encuesta despu&eacute;s de haber creado el tema. Este permiso necesita permisos suficientes para editar el primer mensaje de un tema.';
$txt['permissionname_poll_add_own'] = 'Temas propios';
$txt['permissionname_poll_add_any'] = 'Cualquier tema';
$txt['permissionname_poll_edit'] = 'Editar encuestas';
$txt['permissionhelp_poll_edit'] = 'Este permiso permite editar las opciones de una encuesta as&iacute; como reiniciar la encuesta. Para editar el m&aacute;ximo n&uacute;mero de votos y la fecha de expiraci&oacute;n, el usuario necesita tener el permiso de \'Moderar foro\'.';
$txt['permissionname_poll_edit_own'] = 'Encuesta propia';
$txt['permissionname_poll_edit_any'] = 'Cualquier encuesta';
$txt['permissionname_poll_lock'] = 'Bloquear encuestas';
$txt['permissionhelp_poll_lock'] = 'El bloqueo de encuestas impide que la encuesta acepte m&aacute;s votos.';
$txt['permissionname_poll_lock_own'] = 'Encuesta propia';
$txt['permissionname_poll_lock_any'] = 'Cualquier encuesta';
$txt['permissionname_poll_remove'] = 'Eliminar encuestas';
$txt['permissionhelp_poll_remove'] = 'Este permiso permite la eliminaci&oacute;n de encuestas.';
$txt['permissionname_poll_remove_own'] = 'Encuesta propia';
$txt['permissionname_poll_remove_any'] = 'Cualquier encuesta';

$txt['permissiongroup_notification'] = 'Notificaciones';
$txt['permissionname_mark_any_notify'] = 'Solicitar notificaci&oacute;n en respuestas';
$txt['permissionhelp_mark_any_notify'] = 'Este permiso le permite a los usuarios recibir notificaciones cada que alguien responda en un tema al que est&eacute;n suscritos.';
$txt['permissionname_mark_notify'] = 'Solicitar notificaci&oacute;n en temas nuevos';
$txt['permissionhelp_mark_notify'] = 'Este permise le permite a un usuario el recibir un email cada que se cree un nuevo tema en el foro en el que est&eacute;n suscritos.';

$txt['permissiongroup_attachment'] = 'Archivos adjuntos';
$txt['permissionname_view_attachments'] = 'Ver archivos adjuntos';
$txt['permissionhelp_view_attachments'] = 'Los archivos adjuntos son archivos que est&aacute;n adjuntos a mensajes publicados. Esta opci&oacute;n puede ser activada y configurada en \'Config. y Opciones\'. Debido a que los archivos adjuntos no se accesan directamente, puedes evitar que &eacute;stos sean descargados por usuarios que no tengan este permiso.';
$txt['permissionname_post_attachment'] = 'Publicar archivos adjuntos';
$txt['permissionhelp_post_attachment'] = 'Los archivos adjuntos son archivos que est&aacute;n adjuntos a mensajes publicados. Un mensaje puede tener varios archivos adjuntos.';

$txt['permissionicon'] = '';

$txt['permission_settings_title'] = 'Configuraci&oacute;n de Permisos';
$txt['groups_manage_permissions'] = 'Grupos permitidos para modificar permisos';
$txt['permission_settings_submit'] = 'Guardar';
$txt['permission_settings_enable_deny'] = 'Activar la opci&oacute;n para denegar permisos';
// Escape any single quotes in here twice.. 'it\'s' -> 'it\\\'s'.
$txt['permission_disable_deny_warning'] = 'Al desactivar esta opci&oacute;n se actualizar&aacute; \\\'Denegar\\\' permisos a \\\'No permitir\\\'.';
$txt['permission_by_membergroup_desc'] = 'Puedes establecer todos los permisos globales para cada grupo. Estos permisos son v&aacute;lidos en todos los foros que no hayan sido sobreescritos por permisos locales establecidos en la pantalla \'Permisos por foro\'.';
$txt['permission_by_board_desc'] = 'Puedes establecer cu&aacute;ndo un foro utiliza los permisos globales o tiene permisos espec&iacute;ficos. Al utilizar permisos locales en el foro, puedes establecer cada permiso para cada grupo.';
$txt['permission_settings_desc'] = 'Puedes establecer qui&eacute;n tiene permisos para cambiar los permisos, as&iacute; como cuan sofisticados deber&iacute;an ser los permisos del sistema.';
$txt['permission_settings_enable_postgroups'] = 'Activar permisos para grupos basados en el n&uacute;mero de mensajes';
// Escape any single quotes in here twice.. 'it\'s' -> 'it\\\'s'.
$txt['permission_disable_postgroups_warning'] = 'Al desactivar esta opci&oacute;n se eliminar&aacute;n los permisos actualmente establecidos a grupos basados en el n&uacute;mero de mensajes.';
$txt['permission_settings_enable_by_board'] = 'Activar permisos avanzados por foro';
// Escape any single quotes in here twice.. 'it\'s' -> 'it\\\'s'.
$txt['permission_disable_by_board_warning'] = 'Al desactivar esta opci&oacute;n se eliminar&aacute;n los permisos establecidos a nivel de foros.';


// Begin SMFShop code
$txt['permissiongroup_shop'] = 'SMFShop';

$txt['permissionname_shop_main'] = 'Allow access to SMFShop';
$txt['permissionname_shop_buy'] = 'Buy Items';
$txt['permissionname_shop_invother'] = 'View Other Members\' Inventory';
$txt['permissionname_shop_sendmoney'] = 'Send Money to Someone';
$txt['permissionname_shop_senditems'] = 'Send an Item to Someone';
$txt['permissionname_shop_bank'] = 'Bank';
$txt['permissionname_shop_trade'] = 'Trade Centre';

$txt['permissionhelp_shop_main'] = 'If this option is unticked, the user will not be able to use the shop at all. If you want to disable access to only a particular section of the shop, use the permissions below this one.';
// End SMFShop code

// Added for member notepad mod.
$txt['permissionname_use_notepad'] = 'Use Member NotePad';
$txt['permissionhelp_use_notepad'] = 'Allow this group to use Member NotePad.';

?>