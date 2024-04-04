# Tareas por hacer

A continuación se muestran las tareas que se encuentran pendientes o si es que se están trabajando.

### TO-DO

- [ ] Cambiar `get_magic_quotes_runtime()` deprecado.
- [ ] Cambiar `set_magic_quotes_gpc()` deprecado.
- [ ] Cambiar `mysql_pconnect()` deprecado.
- [ ] Cambiar `ereg()` deprecado.
- [ ] Cambiar `create_function()` deprecado.
- [ ] Desarrollar método pendiente `ssi_actualizar_compublicidad()` en `/web/tp-comunidadesActPub.php`.

### In Progress

- [ ] Embellecer código con tabulaciones correspondientes (Cambiar `	` por `  `).
- [ ] Eliminar o actualizar funciones deprecadas.
- [ ] Cambiar los `"` por los `'` (dentro de lo posible). Excepto cuando se trata de expresiones regulares o consultas MySQL.
- [ ] Usar únicamente archivo `Settings.php` y dejar de usar `config.php`.
- [ ] Agregar variable `$scripturl` o `$boardurl` a enlaces que lo requieran en algún archivo del proyecto.
- [ ] Actualizar a nuevos métodos de conexión a MySQL con MySQLi.

### Done ✓

- [x] Crear archivo TODO.md
- [x] Cambiar todos los `myql_query()` a `db_query()`.
- [x] Definir si usar `require_once` o `include`.
- [x] Verificar si es buena práctica utilizar o no utilizar `@` antes de los `require_once`.
- [x] Agregar variable `$scripturl` o `$boardurl` a enlaces que lo requieran en `SSI.php`.
- [x] Cambiar consultas que necesitan retornar un identificador con `db_insert_id()`.