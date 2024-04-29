# Tareas por hacer

A continuación se muestran las tareas que se encuentran pendientes o si es que se están trabajando.

### TO-DO

- [ ] Desarrollar método pendiente `ssi_actualizar_compublicidad()` en `/web/tp-comunidadesActPub.php`.
- [ ] Solucionar error al paginar comentarios en temas de comunidades.
- [ ] Página de administración pide contraseña siempre. Esto es debido a que `$_POST['sc']` != `$sc`.
- [ ] Vista previa al Publicar no funciona.
- [ ] Editar tema no funciona (siendo administrador de la comunidad).
- [ ] Guardar cambios en perfil no guarda.
- [ ] Actualizar apariencia no guarda.
- [ ] ¿Archivo `tp-votar-comunidad.php` hace lo mismo que `tp-comunidadesVotarTema.php`?
- [ ] Mejorar código en archivo `Profile.template.php`.
- [ ] ¿De dónde viene la función `tp_query_string()`?

### In Progress

- [ ] Embellecer código con tabulaciones correspondientes (Cambiar `	` por `  `).
- [ ] Eliminar o actualizar funciones deprecadas.
- [ ] Cambiar los `"` por los `'` (dentro de lo posible). Excepto cuando se trata de expresiones regulares o consultas MySQL.
- [ ] Cambiar `strftime()` deprecado.

### Done ✓

- [x] Crear archivo TODO.md
- [x] Cambiar todos los `mysql_query()` a `db_query()`.
- [x] Definir si usar `require_once` o `include`.
- [x] Verificar si es buena práctica utilizar o no utilizar `@` antes de los `require_once`.
- [x] Agregar variable `$scripturl` o `$boardurl` a enlaces que lo requieran en `SSI.php`.
- [x] Cambiar consultas que necesitan retornar un identificador con `db_insert_id()`.
- [x] Cambiar `create_function()` deprecado.
- [x] Usar únicamente archivo `Settings.php` y dejar de usar `config.php`.
- [x] Agregar variable `$scripturl` o `$boardurl` a enlaces que lo requieran en algún archivo del proyecto.
- [x] Actualizar a nuevos métodos de conexión a MySQL con MySQLi.
- [x] Cambiar `get_magic_quotes_runtime()` deprecado.
- [x] Cambiar `set_magic_quotes_gpc()` deprecado.
- [x] Cambiar `mysql_pconnect()` deprecado.
- [x] Cambiar `ereg()` deprecado.
- [x] Arreglar widget.
- [x] Eliminar uso de `$func` deprecado.