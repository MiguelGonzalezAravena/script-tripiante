<?php
// Version: 1.1; PersonalMessage

$txt[143] = 'Mensajes recibidos';
$txt[148] = 'Enviar mensaje';
$txt[150] = 'Para';
$txt[1502] = 'Cco';
$txt[316] = 'Recibidos';
$txt[320] = 'Enviados';
$txt[321] = 'Enviar mensaje';
$txt[411] = 'Borrar Mensajes';
// Don't translate "PMBOX" in this string.
$txt[412] = 'Borrar todos los mensajes de tu PMBOX';
$txt[413] = '¿Est&aacute;s seguro que deseas borrar todos los mensajes?';
$txt[535] = 'Destinatario';
// Don't translate the word "SUBJECT" here, as it is used to format the message - use numeric entities as well.
$txt[561] = 'Nuevo Mensaje Privado: SUBJECT';
// Don't translate SENDER or MESSAGE in this language string; they are replaced with the corresponding text - use numeric entities too.
$txt[562] = 'Acaban de enviarte un mensaje privado de parte de SENDER en ' . $context['forum_name'] . '.' . "\n\n" . 'IMPORTANTE: Recuerda, esto es solamente una notificaci&#243;n. Por favor, no respondas a este email.' . "\n\n" . 'El mensaje que te enviaron fue:' . "\n\n" . 'MESSAGE';
$txt[748] = '(m&uacute;ltiples destinatarios como \'nombreusuario1, nombreusuario2\')';
// Use numeric entities in the below string.
$txt['instant_reply'] = 'Responde a este mensaje privado aqu&#237;:';

$txt['smf249'] = '\xbfEstas seguro que desea eliminar este mensaje?';

$txt['sent_to'] = 'Enviado a';
$txt['reply_to_all'] = 'Responder a Todos';

$txt['pm_capacity'] = 'Capacidad';
$txt['pm_currently_using'] = '%s mensajes, %s%% lleno.';

$txt['pm_error_user_not_found'] = 'No se pudo encontrar al usuario \'%s\'.';
$txt['pm_error_ignored_by_user'] = 'El usuario \'%s\' ha bloqueado tu mensaje privado.';
$txt['pm_error_data_limit_reached'] = 'El mensaje privado no se pudo enviar a \'%s\' deb&iacute;do a que excediste el l&iacute;mite de mensajes privados.';
$txt['pm_successfully_sent'] = 'El mensaje privado se envi&oacute; satisfactoriamente a \'%s\'.';
$txt['pm_too_many_recipients'] = 'No puedes enviar mensajes privados a m&aacute;s de %d destinatario(s) a la vez.';
$txt['pm_too_many_per_hour'] = 'Has excedido el l&iacute;mite de %d mensajes personales por hora.';
$txt['pm_send_report'] = 'Enviar informe';
$txt['pm_save_outbox'] = 'Guardar una copia en mi buz&oacute;n Enviados';
$txt['pm_undisclosed_recipients'] = 'Destinatario(s) sin revelar';

$txt['pm_read'] = 'Leer';
$txt['pm_replied'] = 'Respondido A';

// Message Pruning.
$txt['pm_prune'] = 'Purgar Mensajes';
$txt['pm_prune_desc1'] = 'Borrar todos tus mensajes privados m&aacute;s antiguos de ';
$txt['pm_prune_desc2'] = 'd&iacute;as.';
$txt['pm_prune_warning'] = '¿Est&aacute;s seguro que deseas borrar tus mensajes privados?';

// Actions Drop Down.
$txt['pm_actions_title'] = 'Acciones adicionales';
$txt['pm_actions_delete_selected'] = 'Borrar seleccionados';
$txt['pm_actions_filter_by_label'] = 'Filtrar por etiqueta';
$txt['pm_actions_go'] = 'Ir';

// Manage Labels Screen.
$txt['pm_apply'] = 'Aplicar';
$txt['pm_manage_labels'] = 'Administrar Etiquetas';
$txt['pm_labels_delete'] = '¿Est&aacute;s seguro que deseas borrar las etiquetas seleccionadas?';
$txt['pm_labels_desc'] = 'Aqu&iacute; puedes agregar, editar y borrar etiquetas utilizadas en el centro de mensajes privados.';
$txt['pm_label_add_new'] = 'Agregar nueva etiqueta';
$txt['pm_label_name'] = 'Nombre de la etiqueta';
$txt['pm_labels_no_exist'] = '¡No tienes ninguna etiqueta dada de alta!';

// Labeling Drop Down.
$txt['pm_current_label'] = 'Etiqueta';
$txt['pm_msg_label_title'] = 'Etiquetar Mensaje';
$txt['pm_msg_label_apply'] = 'Agregar etiqueta';
$txt['pm_msg_label_remove'] = 'Eliminar etiqueta';
$txt['pm_msg_label_inbox'] = 'Bandeja de Entrada';
$txt['pm_sel_label_title'] = 'Etiquetar seleccionados';
$txt['labels_too_many'] = '&iexcl;Lo sentimos, %s mensajes ya tienen el n&uacute;mero m&aacute;ximo de etiquetas permitido!';

// Sidebar Headings.
$txt['pm_labels'] = 'Etiquetas';
$txt['pm_messages'] = 'Mensajes';
$txt['pm_preferences'] = 'Configuraci&oacute;n';

$txt['pm_is_replied_to'] = 'Has reenviado o respondido a este mensaje.';

// Reporting messages.
$txt['pm_report_to_admin'] = 'Informar al Admin';
$txt['pm_report_title'] = 'Informar sobre Mensaje Privado';
$txt['pm_report_desc'] = 'Desde esta p&aacute;gina puedes informar sobre un mensaje privado a los administradores del foro. Aseg&uacute;rate de incluir una descripci&oacute;n de la raz&oacute;n por la que quieres informar a los administradores, que se enviar&aacute; con el contenido del mensaje original.';
$txt['pm_report_admins'] = 'Administrador al que quieres informar';
$txt['pm_report_all_admins'] = 'Enviarlo a todos los administradores';
$txt['pm_report_reason'] = 'Raz&oacute;n por la que informas sobre este mensaje';
$txt['pm_report_message'] = 'Mensaje a Informar';

// Important - The following strings should use numeric entities.
$txt['pm_report_pm_subject'] = '[REPORTAR] ';
// In the below string, do not translate "{REPORTER}" or "{SENDER}".
$txt['pm_report_pm_user_sent'] = '{REPORTER} te informa sobre un mensaje privado, enviado por {SENDER}, por la siguiente raz&#243;n:';
$txt['pm_report_pm_other_recipients'] = 'Otros destinatarios del mensaje:';
$txt['pm_report_pm_hidden'] = '%d destinatario(s) oculto(s)';
$txt['pm_report_pm_unedited_below'] = 'Debajo est&#225; el contenido original del mensaje privado:';
$txt['pm_report_pm_sent'] = 'Enviado:';

$txt['pm_report_done'] = 'Gracias por enviar este informe. Deber&iacute;as tener noticias de los admins en breve';
$txt['pm_report_return'] = 'Volver a la Bandeja de Entrada';

$txt['pm_search_title'] = 'Buscar Mensajes Privados';
$txt['pm_search_bar_title'] = 'Buscar Mensajes';
$txt['pm_search_text'] = 'Buscar por:';
$txt['pm_search_go'] = 'Buscar';
$txt['pm_search_advanced'] = 'B&uacute;squeda avanzada';
$txt['pm_search_user'] = 'por usuario';
$txt['pm_search_match_all'] = 'Coincidir todas las palabras';
$txt['pm_search_match_any'] = 'Coincidir con cualquier palabra';
$txt['pm_search_options'] = 'Opciones';
$txt['pm_search_post_age'] = 'Antig&uuml;edad';
$txt['pm_search_show_complete'] = 'Mostrar los resultados como mensajes.';
$txt['pm_search_subject_only'] = 'Asuntos y autores solamente.';
$txt['pm_search_between'] = 'Entre';
$txt['pm_search_between_and'] = 'y';
$txt['pm_search_between_days'] = 'd&iacute;as';
$txt['pm_search_order'] = 'Ordenar resultados por';
$txt['pm_search_choose_label'] = 'Escoger d&oacute;nde buscar, o buscar en todos';

$txt['pm_search_results'] = 'Resultados de la B&uacute;squeda';
$txt['pm_search_none_found'] = 'Ning&uacute;n mensaje encontrado';

$txt['pm_search_orderby_relevant_first'] = 'M&aacute;s relevantes primero';
$txt['pm_search_orderby_recent_first'] = 'M&aacute;s recientes primero';
$txt['pm_search_orderby_old_first'] = 'M&aacute;s antiguos primero';

$txt['pm_visual_verification_label'] = 'Verificaci&oacute;n';
$txt['pm_visual_verification_desc'] = 'Por favor introduzca el c&oacute;digo de la imagen para enviar este mensaje personal.';
$txt['pm_visual_verification_listen'] = 'Escuchar las Letras';

?>