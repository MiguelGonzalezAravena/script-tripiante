<?php
// Version: 1.1; ManageMembers

$txt['membergroups_title'] = 'Manejar Grupos de usuarios';
$txt['membergroups_description'] = 'Los grupos de usuarios sirven para agrupar usuarios que tienen la misma configuraci&oacute;n de permisos, apariencia, o derechos de acceso. Algunos grupos est&aacute;n basados en el n&uacute;mero de mensajes que un usuario ha publicado. Puedes asignar a alguien a un grupo seleccionando su perfil y cambiando la configuraci&oacute;n de su cuenta.';
$txt['membergroups_modify'] = 'Modificar';

$txt['membergroups_add_group'] = 'Agregar grupo';
$txt['membergroups_regular'] = 'Grupos regulares';
$txt['membergroups_post'] = 'Grupos basados en el conteo de mensajes';

$txt['membergroups_new_group'] = 'Agregar Grupo';
$txt['membergroups_group_name'] = 'Nombre del grupo de usuarios';
$txt['membergroups_new_board'] = 'Foros Visibles';
$txt['membergroups_new_board_desc'] = 'Foros que el grupo de usuarios puede ver.';
$txt['membergroups_new_board_post_groups'] = '<em>Nota: normalmente, los grupos basados en conteo de mensajes no necesitan acceso porque el grupo en el que el usuario se encuentra les dar&aacute; acceso.</em>';
$txt['membergroups_new_as_type'] = 'por tipo';
$txt['membergroups_new_as_copy'] = 'en base a';
$txt['membergroups_new_copy_none'] = '(ninguno)';
$txt['membergroups_can_edit_later'] = 'puedes editarlos despu&eacute;s.';

$txt['membergroups_edit_group'] = 'Editar Grupo';
$txt['membergroups_edit_name'] = 'Nombre del grupo';
$txt['membergroups_edit_post_group'] = 'Este grupo esta basado en el conteo de mensajes';
$txt['membergroups_min_posts'] = 'N&uacute;mero de mensajes requeridos';
$txt['membergroups_online_color'] = 'Color en la lista de usuarios en l&iacute;nea';
$txt['membergroups_star_count'] = 'N&uacute;mero de im&aacute;genes de estrella';
$txt['membergroups_star_image'] = 'Archivo de imagen de estrella';
$txt['membergroups_star_image_note'] = 'puedes usar $language para usar el idioma del usuario.';
$txt['membergroups_max_messages'] = 'M&aacute;x. mensajes privados';
$txt['membergroups_max_messages_note'] = '0 = ilimitado';
$txt['membergroups_edit_save'] = 'Guardar';
$txt['membergroups_delete'] = 'Borrar';
$txt['membergroups_confirm_delete'] = '&iexcl;&iquest;Est&aacute;s seguro que deseas borrar este grupo?!';

$txt['membergroups_members_title'] = 'Mostrando todos los usuarios del grupo';
$txt['membergroups_members_no_members'] = 'Este grupo se encuantra actualmente vac&iacute;o';
$txt['membergroups_members_add_title'] = 'Agregar un usuario a este grupo';
$txt['membergroups_members_add_desc'] = 'Lista de Usuarios a Agregar';
$txt['membergroups_members_add'] = 'Agregar Usuarios';
$txt['membergroups_members_remove'] = 'Eliminarlo del Grupo';

$txt['membergroups_postgroups'] = 'Grupos basados en el n&uacute;mero de mensajes';

$txt['membergroups_edit_groups'] = 'Editar Grupos';
$txt['membergroups_settings'] = 'Configuraci&oacute;n de Grupos';
$txt['groups_manage_membergroups'] = 'Grupos permitidos para cambiar grupos';
$txt['membergroups_settings_submit'] = 'Guardar';
$txt['membergroups_select_permission_type'] = 'Seleccionar perfil de permisos';
$txt['membergroups_images_url'] = '{theme URL}/images/ ';
$txt['membergroups_select_visible_boards'] = 'Mostrar foros';

$txt['admin_browse_approve'] = 'Usuarios que est&aacute;n esperando aprobaci&oacute;n de sus cuentas';
$txt['admin_browse_approve_desc'] = 'Desde aqu&iacute; puedes manejar a todos los usuarios que est&aacute;n esperando la aprobaci&oacute;n de sus cuentas.';
$txt['admin_browse_activate'] = 'Usuarios que sus cuentas est&aacute;n esperando activaci&oacute;n';
$txt['admin_browse_activate_desc'] = 'Esta pantalla lista todos los usuarios que a&uacute;n no han activado sus cuentas.';
$txt['admin_browse_awaiting_approval'] = 'Esperando Aprobaci&oacute;n <span style="font-weight: normal">(%d)</span>';
$txt['admin_browse_awaiting_activate'] = 'Esperando Activaci&oacute;n <span style="font-weight: normal">(%d)</span>';

$txt['admin_browse_username'] = 'Nombre de usuario';
$txt['admin_browse_email'] = 'Direcci&oacute;n email';
$txt['admin_browse_ip'] = 'Direcci&oacute;n IP';
$txt['admin_browse_registered'] = 'Registrado';
$txt['admin_browse_id'] = 'ID';
$txt['admin_browse_with_selected'] = 'Seleccionado con';
$txt['admin_browse_no_members_approval'] = 'Ning&uacute;n usuario est&aacute; esperando aprobaci&oacute;n.';
$txt['admin_browse_no_members_activate'] = 'Ning&uacute;n usuario tiene actualmente su cuenta pendiente de activar.';

// Don't use entities in the below strings, except the main ones. (lt, gt, quot.)
$txt['admin_browse_warn'] = '&#191;Todos los usuarios seleccionados?';
$txt['admin_browse_outstanding_warn'] = '&#191;Todos los usuarios afectados?';
$txt['admin_browse_w_approve'] = 'Aprobar';
$txt['admin_browse_w_activate'] = 'Activar';
$txt['admin_browse_w_delete'] = 'Borrar';
$txt['admin_browse_w_reject'] = 'Rechazar';
$txt['admin_browse_w_remind'] = 'Recordar';
$txt['admin_browse_w_approve_deletion'] = 'Aprobar (Borrar Cuentas)';
$txt['admin_browse_w_email'] = 'y enviar email';
$txt['admin_browse_w_approve_require_activate'] = 'Esperando activaci&#243;n o aprobaci&#243;n';

$txt['admin_browse_filter_by'] = 'Filtrar por';
$txt['admin_browse_filter_show'] = 'Mostrando';
$txt['admin_browse_filter_type_0'] = 'Cuentas nuevas sin activar';
$txt['admin_browse_filter_type_2'] = 'Cambios de Email sin activar';
$txt['admin_browse_filter_type_3'] = 'Nuevas cuentas sin aprobar';
$txt['admin_browse_filter_type_4'] = 'Borrado de cuentas sin aprobar';
$txt['admin_browse_filter_type_5'] = 'Cuentas "debajo de la edad" sin aprobar';

$txt['admin_browse_outstanding'] = 'Usuarios Excepcionales';
$txt['admin_browse_outstanding_days_1'] = 'Con todos los usuarios que se registraron hace m&aacute;s de';
$txt['admin_browse_outstanding_days_2'] = 'd&iacute;as';
$txt['admin_browse_outstanding_perform'] = 'Realiza la siguiente acci&oacute;n';
$txt['admin_browse_outstanding_go'] = 'Realizar Acci&oacute;n';

// Use numeric entities in the below nine strings.
$txt['admin_approve_reject'] = 'Registro rechazado';
$txt['admin_approve_reject_desc'] = 'Lamento informarte que tu solicitud de pertenecer al foro ' . $context['forum_name'] . ' ha sido rechazada.';
$txt['admin_approve_delete'] = 'Cuenta borrada';
$txt['admin_approve_delete_desc'] = 'Tu cuenta en ' . $context['forum_name'] . ' ha sido borrada.  Probablemente fue debido a que nunca activaste tu cuenta, en cuyo caso puedes registrarte nuevamente.';
$txt['admin_approve_remind'] = 'Recordatorio de Registro';
$txt['admin_approve_remind_desc'] = 'No has activado tu cuenta a&#250;n en';
$txt['admin_approve_remind_desc2'] = 'Por favor, haz clic en el enlace siguiente para activar tu cuenta:';
$txt['admin_approve_accept_desc'] = 'Tu cuenta ha sido activada manualmente por el administrador. Ya puedes ingresar y publicar mensajes.';
$txt['admin_approve_require_activation'] = 'Tu cuenta en ' . $context['forum_name'] . ' ha sido aprobada por el administrador del foro, y debe ser activada ahora antes de que puedas publicar.';

?>