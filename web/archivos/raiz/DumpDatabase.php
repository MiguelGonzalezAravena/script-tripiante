<?php
if (!defined('SMF'))
  die('Hacking attempt...');

function DumpDatabase2() {
  global $db_name, $db_prefix, $scripturl, $context, $modSettings, $crlf;

  // Administrators only!
  if (!allowedTo('admin_forum'))
    fatal_lang_error('no_dump_database');

  // You can't dump nothing!
  if (!isset($_GET['struct']) && !isset($_GET['data']))
    $_GET['data'] = true;

  checkSession('get');

  // Attempt to stop from dying...
  @set_time_limit(600);
  @ini_set('memory_limit', '128M');

  // Start saving the output... (don't do it otherwise for memory reasons.)
  if (isset($_REQUEST['compress']) && function_exists('gzencode'))
  {
    // Make sure we're gzipping output, but then say we're not in the header ^_^.
    if (empty($modSettings['enableCompressedOutput']))
      @ob_start('ob_gzhandler');
    // Try to clean any data already outputted.
    else if (ob_get_length() != 0)
    {
      ob_end_clean();
      @ob_start('ob_gzhandler');
    }

    // Send faked headers so it will just save the compressed output as a gzip.
    header('Content-Type: application/x-gzip');
    header('Accept-Ranges: bytes');
    header('Content-Encoding: none');

    // Gecko browsers... don't like this. (Mozilla, Firefox, etc.)
    if (!$context['browser']['is_gecko'])
      header('Content-Transfer-Encoding: binary');

    // The file extension will include .gz...
    $extension = '.sql.gz';
  }
  else
  {
    // Get rid of the gzipping alreading being done.
    if (!empty($modSettings['enableCompressedOutput']))
      @ob_end_clean();
    // If we can, clean anything already sent from the output buffer...
    else if (function_exists('ob_clean') && ob_get_length() != 0)
      ob_clean();

    // Tell the client to save this file, even though it's text.
    header('Content-Type: application/octetstream');
    header('Content-Encoding: none');

    // This time the extension should just be .sql.
    $extension = '.sql';
  }

  // This should turn off the session URL parser.
  $scripturl = '';

  // Send the proper headers to let them download this file.
  header('Content-Disposition: filename="' . $db_name . '-' . (empty($_GET['struct']) ? 'data' : (empty($_GET['data']) ? 'structure' : 'complete')) . '_' . date('Y-m-d') . $extension . '"');
  header('Cache-Control: private');
  header('Connection: close');

  // This makes things simpler when using it so very very often.
  $crlf = "\r\n";

  // SQL Dump Header.
  echo
    '# ==========================================================', $crlf,
    '#', $crlf,
    '# Database dump of tables in `', $db_name, '`', $crlf,
    '# ', timeformat(time(), false), $crlf,
    '#', $crlf,
    '# ==========================================================', $crlf,
    $crlf;

  // Get all tables in the database....
  if (preg_match('~^`(.+?)`\.(.+?)$~', $db_prefix, $match) != 0)
  {
    $queryTables = db_query("
      SHOW TABLES
      FROM `" . strtr($match[1], array('`' => '')) . "`
      LIKE '" . str_replace('_', '\_', $match[2]) . "%'", false, false);
  }
  else
  {
    $queryTables = db_query("
      SHOW TABLES
      LIKE '" . str_replace('_', '\_', $db_prefix) . "%'", false, false);
  }

  // Dump each table.
  while ($tableName = mysqli_fetch_row($queryTables))
  {
    if (function_exists('apache_reset_timeout'))
      apache_reset_timeout();

    // Are we dumping the structures?
    if (isset($_GET['struct']))
    {
      echo
        $crlf,
        '#', $crlf,
        '# Table structure for table `', $tableName[0], '`', $crlf,
        '#', $crlf,
        $crlf,
        'DROP TABLE IF EXISTS `', $tableName[0], '`;', $crlf,
        $crlf,
        getTableSQLData($tableName[0]), ';', $crlf;
    }

    // How about the data?
    if (!isset($_GET['data']) || substr($tableName[0], -10) == 'log_errors')
      continue;

    // Are there any rows in this table?
    $get_rows = getTableContent($tableName[0]);

    // No rows to get - skip it.
    if (empty($get_rows))
      continue;

    echo
      $crlf,
      '#', $crlf,
      '# Dumping data in `', $tableName[0], '`', $crlf,
      '#', $crlf,
      $crlf,
      $get_rows,
      '# --------------------------------------------------------', $crlf;
  }
  mysqli_free_result($queryTables);

  echo
    $crlf,
    '# Done', $crlf;

  exit;
}

// Get the content (INSERTs) for a table.
function getTableContent($tableName)
{
  global $crlf;

  // Get everything from the table.
  $result = db_query("
    SELECT /*!40001 SQL_NO_CACHE */ *
    FROM `$tableName`", false, false);

  // The number of rows, just for record keeping and breaking INSERTs up.
  $num_rows = @mysqli_num_rows($result);
  $current_row = 0;

  if ($num_rows == 0)
    return '';

  $fields = array_keys(mysqli_fetch_assoc($result));
  mysqli_data_seek($result, 0);

  // Start it off with the basic INSERT INTO.
  $data = 'INSERT INTO `' . $tableName . '`' . $crlf . "\t(`" . implode('`, `', $fields) . '`)' . $crlf . 'VALUES ';

  // Loop through each row.
  while ($row = mysqli_fetch_row($result))
  {
    $current_row++;

    // Get the fields in this row...
    $field_list = array();
    for ($j = 0; $j < mysqli_num_fields($result); $j++)
    {
      // Try to figure out the type of each field. (NULL, number, or 'string'.)
      if (!isset($row[$j]))
        $field_list[] = 'NULL';
      else if (is_numeric($row[$j]))
        $field_list[] = $row[$j];
      else
        $field_list[] = "'" . mysqli_escape_string($row[$j]) . "'";
    }

    // 'Insert' the data.
    $data .= '(' . implode(', ', $field_list) . ')';

    // Start a new INSERT statement after every 250....
    if ($current_row > 249 && $current_row % 250 == 0)
      $data .= ';' . $crlf . 'INSERT INTO `' . $tableName . '`' . $crlf . "\t(`" . implode('`, `', $fields) . '`)' . $crlf . 'VALUES ';
    // All done!
    else if ($current_row == $num_rows)
      $data .= ';' . $crlf;
    // Otherwise, go to the next line.
    else
      $data .= ',' . $crlf . "\t";
  }
  mysqli_free_result($result);

  // Return an empty string if there were no rows.
  return $num_rows == 0 ? '' : $data;
}

// Get the schema (CREATE) for a table.
function getTableSQLData($tableName)
{
  global $crlf;

  // Start the create table...
  $schema_create = 'CREATE TABLE `' . $tableName . '` (' . $crlf;

  // Find all the fields.
  $result = db_query("
    SHOW FIELDS
    FROM `$tableName`", false, false);
  while ($row = @mysqli_fetch_assoc($result))
  {
    // Make the CREATE for this column.
    $schema_create .= '  ' . $row['Field'] . ' ' . $row['Type'] . ($row['Null'] != 'YES' ? ' NOT NULL' : '');

    // Add a default...?
    if (isset($row['Default']))
    {
      // Make a special case of auto-timestamp.
      // TO-DO: Verificar los 2 argumentos de mysqli_escape_string()
      if ($row['Default'] == 'CURRENT_TIMESTAMP')
        $schema_create .= ' /*!40102 NOT NULL default CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP */';
      else
        $schema_create .= ' default ' . (is_numeric($row['Default']) ? $row['Default'] : "'" . mysqli_escape_string($row['Default']) . "'");
    }

    // And now any extra information. (such as auto_increment.)
    $schema_create .= ($row['Extra'] != '' ? ' ' . $row['Extra'] : '') . ',' . $crlf;
  }
  @mysqli_free_result($result);

  // Take off the last comma.
  $schema_create = substr($schema_create, 0, -strlen($crlf) - 1);

  // Find the keys.
  $result = db_query("
    SHOW KEYS
    FROM `$tableName`", false, false);
  $indexes = array();
  while ($row = @mysqli_fetch_assoc($result))
  {
    // IS this a primary key, unique index, or regular index?
    $row['Key_name'] = $row['Key_name'] == 'PRIMARY' ? 'PRIMARY KEY' : (empty($row['Non_unique']) ? 'UNIQUE ' : ($row['Comment'] == 'FULLTEXT' || (isset($row['Index_type']) && $row['Index_type'] == 'FULLTEXT') ? 'FULLTEXT ' : 'KEY ')) . $row['Key_name'];

    // Is this the first column in the index?
    if (empty($indexes[$row['Key_name']]))
      $indexes[$row['Key_name']] = array();

    // A sub part, like only indexing 15 characters of a varchar.
    if (!empty($row['Sub_part']))
      $indexes[$row['Key_name']][$row['Seq_in_index']] = $row['Column_name'] . '(' . $row['Sub_part'] . ')';
    else
      $indexes[$row['Key_name']][$row['Seq_in_index']] = $row['Column_name'];
  }
  @mysqli_free_result($result);

  // Build the CREATEs for the keys.
  foreach ($indexes as $keyname => $columns)
  {
    // Ensure the columns are in proper order.
    ksort($columns);

    $schema_create .= ',' . $crlf . '  ' . $keyname . ' (' . implode($columns, ', ') . ')';
  }

  // Now just get the comment and type... (MyISAM, etc.)
  $result = db_query("
    SHOW TABLE STATUS
    LIKE '" . strtr($tableName, array('_' => '\\_', '%' => '\\%')) . "'", false, false);
  $row = @mysqli_fetch_assoc($result);
  @mysqli_free_result($result);

  // Probably MyISAM.... and it might have a comment.
  $schema_create .= $crlf . ') TYPE=' . (isset($row['Type']) ? $row['Type'] : $row['Engine']) . ($row['Comment'] != '' ? ' COMMENT="' . $row['Comment'] . '"' : '');

  return $schema_create;
}

?>