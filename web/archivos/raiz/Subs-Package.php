<?php
if (!defined('SMF'))
  die('Hacking attempt...');

function read_tgz_file($gzfilename, $destination, $single_file = false, $overwrite = false) {
  if (substr($gzfilename, 0, 7) == 'http://') {
    $data = fetch_web_data($gzfilename);

    if ($data === false)
      return false;
  } else {
    $data = @file_get_contents($gzfilename);

    if ($data === false)
      return false;
  }

  return read_tgz_data($data, $destination, $single_file, $overwrite);
}

// Extract tar.gz data.  If destination is null, return a listing.
function read_tgz_data($data, $destination, $single_file = false, $overwrite = false) {
  // This function sorta needs gzinflate!
  if (!function_exists('gzinflate'))
    fatal_lang_error('package_no_zlib');

  umask(0);
  if (!$single_file && $destination !== null && !file_exists($destination))
    mktree($destination, 0777);

  // No signature?
  if (strlen($data) < 2)
    return false;

  $id = unpack('H2a/H2b', substr($data, 0, 2));
  if (strtolower($id['a'] . $id['b']) != '1f8b')
  {
    // Okay, this ain't no tar.gz, but maybe it's a zip file.
    if (substr($data, 0, 2) == 'PK')
      return read_zip_data($data, $destination, $single_file, $overwrite);
    else
      return false;
  }

  $flags = unpack('Ct/Cf', substr($data, 2, 2));

  // Not deflate!
  if ($flags['t'] != 8)
    return false;
  $flags = $flags['f'];

  $offset = 10;
  $octdec = array('mode', 'uid', 'gid', 'size', 'mtime', 'checksum', 'type');

  // "Read" the filename and comment. // !!! Might be mussed.
  if ($flags & 12)
  {
    while ($flags & 8 && $data{$offset++} != "\0")
      continue;
    while ($flags & 4 && $data{$offset++} != "\0")
      continue;
  }

  $crc = unpack('Vcrc32/Visize', substr($data, strlen($data) - 8, 8));
  $data = @gzinflate(substr($data, $offset, strlen($data) - 8 - $offset));

  if ($crc['crc32'] != smf_crc32($data))
    return false;

  $blocks = strlen($data) / 512 - 1;
  $offset = 0;

  $return = array();

  while ($offset < $blocks)
  {
    $header = substr($data, $offset << 9, 512);
    $current = unpack('a100filename/a8mode/a8uid/a8gid/a12size/a12mtime/a8checksum/a1type/a100linkname/a6magic/a2version/a32uname/a32gname/a8devmajor/a8devminor/a155path', $header);

    foreach ($current as $k => $v)
    {
      if (in_array($k, $octdec))
        $current[$k] = octdec(trim($v));
      else
        $current[$k] = trim($v);
    }

    $checksum = 256;
    for ($i = 0; $i < 148; $i++)
      $checksum += ord($header{$i});
    for ($i = 156; $i < 512; $i++)
      $checksum += ord($header{$i});

    if ($current['checksum'] != $checksum)
      return $return;

    $size = ceil($current['size'] / 512);
    $current['data'] = substr($data, ++$offset << 9, $current['size']);
    $offset += $size;

    // Not a directory and doesn't exist already...
    if (substr($current['filename'], -1, 1) != '/' && !file_exists($destination . '/' . $current['filename']))
      $write_this = true;
    // File exists... check if it is newer.
    else if (substr($current['filename'], -1, 1) != '/')
      $write_this = $overwrite || filemtime($destination . '/' . $current['filename']) < $current['mtime'];
    // Folder... create.
    else if ($destination !== null && !$single_file)
    {
      // Protect from accidental parent directory writing...
      $current['filename'] = strtr($current['filename'], array('../' => '', '/..' => ''));

      if (!file_exists($destination . '/' . $current['filename']))
        mktree($destination . '/' . $current['filename'], 0777);
      $write_this = false;
    }
    else
      $write_this = false;

    if ($write_this && $destination !== null)
    {
      if (strpos($current['filename'], '/') !== false && !$single_file)
        mktree($destination . '/' . dirname($current['filename']), 0777);

      // Is this the file we're looking for?
      if ($single_file && ($destination == $current['filename'] || $destination == '*/' . basename($current['filename'])))
        return $current['data'];
      // If we're looking for another file, keep going.
      else if ($single_file)
        continue;

      package_put_contents($destination . '/' . $current['filename'], $current['data']);
    }

    if (substr($current['filename'], -1, 1) != '/')
      $return[] = array(
        'filename' => $current['filename'],
        'size' => $current['size'],
        'skipped' => false
      );
  }

  if ($destination !== null && !$single_file)
    package_flush_cache();

  if ($single_file)
    return false;
  else
    return $return;
}

// Extract zip data.  If destination is null, return a listing.
function read_zip_data($data, $destination, $single_file = false, $overwrite = false)
{
  umask(0);
  if ($destination !== null && !file_exists($destination) && !$single_file)
    mktree($destination, 0777);

  // Look for the PK header...
  if (substr($data, 0, 2) != 'PK')
    return false;

  // Find the central whosamawhatsit at the end; if there's a comment it's a pain.
  if (substr($data, -22, 4) == 'PK' . chr(5) . chr(6))
    $p = -22;
  else
  {
    // Have to find where the comment begins, ugh.
    for ($p = -22; $p > -strlen($data); $p--)
    {
      if (substr($data, $p, 4) == 'PK' . chr(5) . chr(6))
        break;
    }
  }

  $return = array();

  // Get the basic zip file info.
  $zip_info = unpack('vfiles/Vsize/Voffset', substr($data, $p + 10, 10));

  $p = $zip_info['offset'];
  for ($i = 0; $i < $zip_info['files']; $i++)
  {
    // Make sure this is a file entry...
    if (substr($data, $p, 4) != 'PK' . chr(1) . chr(2))
      return false;

    // Get all the important file information.
    $file_info = unpack('Vcrc/Vcompressed_size/Vsize/vfilename_len/vextra_len/vcomment_len/vdisk/vinternal/Vexternal/Voffset', substr($data, $p + 16, 30));
    $file_info['filename'] = substr($data, $p + 46, $file_info['filename_len']);

    // Skip all the information we don't care about anyway.
    $p += 46 + $file_info['filename_len'] + $file_info['extra_len'] + $file_info['comment_len'];

    // If this is a file, and it doesn't exist.... happy days!
    if (substr($file_info['filename'], -1, 1) != '/' && !file_exists($destination . '/' . $file_info['filename']))
      $write_this = true;
    // If the file exists, we may not want to overwrite it.
    else if (substr($file_info['filename'], -1, 1) != '/')
      $write_this = $overwrite;
    // This is a directory, so we're gonna want to create it. (probably...)
    else if ($destination !== null && !$single_file)
    {
      // Just a little accident prevention, don't mind me.
      $file_info['filename'] = strtr($file_info['filename'], array('../' => '', '/..' => ''));

      if (!file_exists($destination . '/' . $file_info['filename']))
        mktree($destination . '/' . $file_info['filename'], 0777);
      $write_this = false;
    }
    else
      $write_this = false;

    // Okay!  We can write this file, looks good from here...
    if ($write_this && $destination !== null)
    {
      if (strpos($file_info['filename'], '/') !== false && !$single_file)
        mktree($destination . '/' . dirname($file_info['filename']), 0777);

      // Check that the data is there and does exist.
      if (substr($data, $file_info['offset'], 4) != 'PK' . chr(3) . chr(4))
        return false;

      // Get the actual compressed data.
      $file_info['data'] = substr($data, $file_info['offset'] + 30 + $file_info['filename_len'] + $file_info['extra_len'], $file_info['compressed_size']);

      // Only inflate it if we need to ;).
      if ($file_info['compressed_size'] != $file_info['size'])
        $file_info['data'] = @gzinflate($file_info['data']);

      // If we're looking for a specific file, and this is it... ka-bam, baby.
      if ($single_file && ($destination == $file_info['filename'] || $destination == '*/' . basename($file_info['filename'])))
        return $file_info['data'];
      // Oh?  Another file.  Fine.  You don't like this file, do you?  I know how it is.  Yeah... just go away.  No, don't apologize.  I know this file's just not *good enough* for you.
      else if ($single_file)
        continue;

      package_put_contents($destination . '/' . $file_info['filename'], $file_info['data']);
    }

    if (substr($file_info['filename'], -1, 1) != '/')
      $return[] = array(
        'filename' => $file_info['filename'],
        'size' => $file_info['size'],
        'skipped' => false
      );
  }

  if ($destination !== null && !$single_file)
    package_flush_cache();

  if ($single_file)
    return false;
  else
    return $return;
}

// Checks the existence of a remote file since file_exists() does not do remote.
function url_exists($url)
{
  $a_url = parse_url($url);

  if (!isset($a_url['scheme']))
    return false;

  // Attempt to connect...
  $temp = '';
  $fid = fsockopen($a_url['host'], !isset($a_url['port']) ? 80 : $a_url['port'], $temp, $temp, 8);
  if (!$fid)
    return false;

  fputs($fid, 'HEAD ' . $a_url['path'] . " HTTP/1.0\r\nHost: " . $a_url['host'] . "\r\n\r\n");
  $head = fread($fid, 1024);
  fclose($fid);

  return preg_match('~^HTTP/.+\s+200~i', $head) == 1;
}

// Load the installed packages.
function loadInstalledPackages()
{
  global $boarddir;

  $installed_mods = file($boarddir . '\\web\\archivos/paquetes/installed.list');

  $installed = array();
  for ($i = 0, $n = count($installed_mods); $i < $n; $i++)
  {
    // Skip any empty lines.
    if (trim($installed_mods[$i]) == '')
      continue;

    // Ignore errors with borked installed.list's.
    list ($name, $file, $id, $version) = array_pad(explode('|^|', $installed_mods[$i]), 4, '');

    // Pretty simple, eh? // !!! Verify stripslashes?
    $installed[] = array(
      'name' => stripslashes($name),
      'filename' => stripslashes($file),
      'id' => $id,
      'version' => trim($version)
    );
  }

  return $installed;
}

function saveInstalledPackages($instmods)
{
  global $boarddir;

  // Attempt to make the installed.list file writable if it isn't yet.
  if (!is_writable($boarddir . '\\web\\archivos/paquetes/installed.list'))
    package_chmod($boarddir . '\\web\\archivos/paquetes/installed.list');

  $data = '';
  foreach ($instmods as $packageInfo)
  {
    if (empty($packageInfo))
      continue;

    $data .= trim($packageInfo['name']) . '|^|' . trim($packageInfo['filename']) . '|^|' . trim($packageInfo['id']) . '|^|' . trim($packageInfo['version']) . "\n";
  }

  package_put_contents($boarddir . '\\web\\archivos/paquetes/installed.list', $data);
}

function getPackageInfo($gzfilename)
{
  global $boarddir;

  // Extract package-info.xml from downloaded file. (*/ is used because it could be in any directory.)
  if (strpos($gzfilename, 'http://') !== false)
    $packageInfo = read_tgz_data(fetch_web_data($gzfilename, '', true), '*/package-info.xml', true);
  else
  {
    if (!file_exists($boarddir . '\\web\\archivos/paquetes/' . $gzfilename))
      return false;

    if (is_file($boarddir . '\\web\\archivos/paquetes/' . $gzfilename))
      $packageInfo = read_tgz_file($boarddir . '\\web\\archivos/paquetes/' . $gzfilename, '*/package-info.xml', true);
    else if (file_exists($boarddir . '\\web\\archivos/paquetes/' . $gzfilename . '/package-info.xml'))
      $packageInfo = file_get_contents($boarddir . '\\web\\archivos/paquetes/' . $gzfilename . '/package-info.xml');
    else
      return false;
  }

  // Parse package-info.xml into an xmlArray.
  $packageInfo = new xmlArray($packageInfo);

  // !!! Error message of some sort?
  if (!$packageInfo->exists('package-info[0]'))
    return false;

  $packageInfo = $packageInfo->path('package-info[0]');

  $package = $packageInfo->to_array();
  $package['xml'] = $packageInfo;
  $package['filename'] = $gzfilename;

  if (!isset($package['type']))
    $package['type'] = 'modification';

  return $package;
}

function packageRequireFTP($destination_url, $files = null)
{
  global $context, $modSettings, $package_ftp, $boarddir, $txt;

  // Try to make them writable the manual way.
  if ($files !== null)
  {
    foreach ($files as $k => $file)
    {
      // If this file doesn't exist, then we actually want to look at the directory, no?
      if (!file_exists($file))
        $file = dirname($file);

      // This looks odd, but it's an attempt to work around PHP suExec.
      if (!@is_writable($file))
        @chmod($file, 0755);
      if (!@is_writable($file))
        @chmod($file, 0777);
      if (!@is_writable(dirname($file)))
        @chmod($file, 0755);
      if (!@is_writable(dirname($file)))
        @chmod($file, 0777);

      $fp = is_dir($file) ? @opendir($file) : @fopen($file, 'rb');
      if (@is_writable($file) && $fp)
      {
        unset($files[$k]);
        if (!is_dir($file))
          fclose($fp);
        else
          closedir($fp);
      }
    }

    // No FTP required!
    if (empty($files))
      return;
  }

  // They've opted to not use FTP, and try anyway.
  if (isset($_SESSION['pack_ftp']) && $_SESSION['pack_ftp'] == false)
  {
    if ($files === null)
      return;

    foreach ($files as $k => $file)
    {
      // This looks odd, but it's an attempt to work around PHP suExec.
      if (!file_exists($file))
      {
        mktree(dirname($file), 0755);
        @touch($file);
        @chmod($file, 0755);
      }

      if (!@is_writable($file))
        @chmod($file, 0777);
      if (!@is_writable(dirname($file)))
        @chmod(dirname($file), 0777);

      if (@is_writable($file))
        unset($files[$k]);
    }

    return;
  }
  else if (isset($_SESSION['pack_ftp']))
  {
    $package_ftp = new ftp_connection($_SESSION['pack_ftp']['server'], $_SESSION['pack_ftp']['port'], $_SESSION['pack_ftp']['username'], package_crypt($_SESSION['pack_ftp']['password']));

    if ($files === null)
      return;

    foreach ($files as $k => $file)
    {
      $ftp_file = strtr($file, array($_SESSION['pack_ftp']['root'] => ''));

      // This looks odd, but it's an attempt to work around PHP suExec.
      if (!file_exists($file))
      {
        mktree(dirname($file), 0755);
        $package_ftp->create_file($ftp_file);
        $package_ftp->chmod($ftp_file, 0755);
      }

      if (!@is_writable($file))
        $package_ftp->chmod($ftp_file, 0777);
      if (!@is_writable(dirname($file)))
        $package_ftp->chmod(dirname($ftp_file), 0777);

      if (@is_writable($file))
        unset($files[$k]);
    }

    return;
  }

  if (isset($_POST['ftp_none']))
  {
    $_SESSION['pack_ftp'] = false;

    packageRequireFTP($destination_url, $files);
    return;
  }
  else if (isset($_POST['ftp_username']))
  {
    $ftp = new ftp_connection($_POST['ftp_server'], $_POST['ftp_port'], $_POST['ftp_username'], $_POST['ftp_password']);

    if ($ftp->error === false)
    {
      // Common mistake, so let's try to remedy it...
      if (!$ftp->chdir($_POST['ftp_path']))
      {
        $ftp_error = $ftp->last_message;
        $ftp->chdir(preg_replace('~^/home[2]?/[^/]+?~', '', $_POST['ftp_path']));
      }
    }
  }

  if (!isset($ftp) || $ftp->error !== false)
  {
    if (!isset($ftp))
      $ftp = new ftp_connection(null);
    else if ($ftp->error !== false && !isset($ftp_error))
      $ftp_error = $ftp->last_message === null ? '' : $ftp->last_message;

    list ($username, $detect_path, $found_path) = $ftp->detect_path($boarddir);

    if ($found_path)
      $_POST['ftp_path'] = $detect_path;
    else if (!isset($_POST['ftp_path']))
      $_POST['ftp_path'] = isset($modSettings['package_path']) ? $modSettings['package_path'] : $detect_path;

    if (!isset($_POST['ftp_username']))
      $_POST['ftp_username'] = $username;

    $context['package_ftp'] = array(
      'server' => isset($_POST['ftp_server']) ? $_POST['ftp_server'] : (isset($modSettings['package_server']) ? $modSettings['package_server'] : 'localhost'),
      'port' => isset($_POST['ftp_port']) ? $_POST['ftp_port'] : (isset($modSettings['package_port']) ? $modSettings['package_port'] : '21'),
      'username' => isset($_POST['ftp_username']) ? $_POST['ftp_username'] : (isset($modSettings['package_username']) ? $modSettings['package_username'] : ''),
      'path' => $_POST['ftp_path'],
      'error' => empty($ftp_error) ? null : $ftp_error,
      'destination' => $destination_url,
    );

    $context['page_title'] = $txt['package_ftp_necessary'];
    $context['sub_template'] = 'ftp_required';
    obExit();
  }
  else
  {
    if (!in_array($_POST['ftp_path'], array('', '/')))
    {
      $ftp_root = strtr($boarddir, array($_POST['ftp_path'] => ''));
      if (substr($ftp_root, -1) == '/' && ($_POST['ftp_path'] == '' || substr($_POST['ftp_path'], 0, 1) == '/'))
        $ftp_root = substr($ftp_root, 0, -1);
    }
    else
      $ftp_root = $boarddir;

    $_SESSION['pack_ftp'] = array(
      'server' => $_POST['ftp_server'],
      'port' => $_POST['ftp_port'],
      'username' => $_POST['ftp_username'],
      'password' => package_crypt($_POST['ftp_password']),
      'path' => $_POST['ftp_path'],
      'root' => $ftp_root,
    );

    if (!isset($modSettings['package_path']) || $modSettings['package_path'] != $_POST['ftp_path'])
      updateSettings(array('package_path' => $_POST['ftp_path']));

    packageRequireFTP($destination_url, $files);
  }
}

// Parses a package-info.xml file - method can be 'install', 'upgrade', or 'uninstall'.
function parsePackageInfo(&$packageXML, $testing_only = true, $method = 'install', $previous_version = '')
{
  global $boarddir, $forum_version, $context;

  // Mayday!  That action doesn't exist!!
  if (empty($packageXML) || !$packageXML->exists($method))
    return array();

  // We haven't found the package script yet...
  $script = false;
  $the_version = strtr($forum_version, array('SMF ' => ''));

  // Emulation support...
  if (!empty($_SESSION['version_emulate']))
    $the_version = $_SESSION['version_emulate'];

  // Get all the versions of this method and find the right one.
  $these_methods = $packageXML->set($method);
  foreach ($these_methods as $this_method)
  {
    // They specified certain versions this part is for.
    if ($this_method->exists('@for'))
    {
      // Don't keep going if this won't work for this version of SMF.
      if (!matchPackageVersion($the_version, $this_method->fetch('@for')))
        continue;
    }

    // Upgrades may go from a certain old version of the mod.
    if ($method == 'upgrade' && $this_method->exists('@from'))
    {
      // Well, this is for the wrong old version...
      if (!matchPackageVersion($previous_version, $this_method->fetch('@from')))
        continue;
    }

    // We've found it!
    $script = $this_method;
    break;
  }

  // Bad news, a matching script wasn't found!
  if ($script === false)
    return array();

  // Find all the actions in this method - in theory, these should only be allowed actions. (* means all.)
  $actions = $script->set('*');
  $return = array();

  $temp_auto = 0;
  $temp_path = $boarddir . '\\web\\archivos/paquetes/temp/' . (isset($context['base_path']) ? $context['base_path'] : '');

  // This is the testing phase... nothing shall be done yet.
  foreach ($actions as $action)
  {
    $actionType = $action->name();

    if ($actionType == 'readme' || $actionType == 'code' || $actionType == 'modification' || $actionType == 'redirect')
    {
      // !!! TODO: Make sure the file actually exists?  Might not work when testing?
      if ($action->exists('@type') && $action->fetch('@type') == 'inline')
      {
        $filename = $temp_path . '$auto_' . $temp_auto++ . ($actionType == 'readme' || $actionType == 'redirect' ? '.txt' : ($actionType == 'code' ? '.php' : '.mod'));
        package_put_contents($filename, $action->fetch('.'));
        $filename = strtr($filename, array($temp_path => ''));
      }
      else
        $filename = $action->fetch('.');

      $return[] = array(
        'type' => $actionType,
        'filename' => $filename,
        'description' => '',
        'reverse' => $action->exists('@reverse') && $action->fetch('@reverse') == 'true',
        'boardmod' => $action->exists('@format') && $action->fetch('@format') == 'boardmod',
        'redirect_url' => $action->exists('@url') ? $action->fetch('@url') : '',
        'redirect_timeout' => $action->exists('@timeout') ? (int) $action->fetch('@timeout') : '',
        'parse_bbc' => $action->exists('@parsebbc') && $action->fetch('@parsebbc') == 'true',
      );

      continue;
    }
    else if ($actionType == 'error')
    {
      $return[] = array(
        'type' => 'error',
      );
    }

    $this_action = &$return[];
    $this_action = array(
      'type' => $actionType,
      'filename' => $action->fetch('@name'),
      'description' => $action->fetch('.')
    );

    // If there is a destination, make sure it makes sense.
    if (substr($actionType, 0, 6) != 'remove')
      $this_action['destination'] = parse_path($action->fetch('@destination')) . '/' . basename($this_action['filename']);
    else
      $this_action['filename'] = parse_path($this_action['filename']);

    // If we're moving or requiring (copying) a file.
    if (substr($actionType, 0, 4) == 'move' || substr($actionType, 0, 7) == 'require')
    {
      if ($action->exists('@from'))
        $this_action['source'] = parse_path($action->fetch('@from'));
      else
        $this_action['source'] = $temp_path . $this_action['filename'];
    }

    // Check if these things can be done. (chmod's etc.)
    if ($actionType == 'create-dir')
    {
      if (!mktree($this_action['destination'], false))
      {
        $temp = $this_action['destination'];
        while (!file_exists($temp) && strlen($temp) > 1)
          $temp = dirname($temp);

        $return[] = array(
          'type' => 'chmod',
          'filename' => $temp
        );
      }
    }
    else if ($actionType == 'create-file')
    {
      if (!mktree(dirname($this_action['destination']), false))
      {
        $temp = dirname($this_action['destination']);
        while (!file_exists($temp) && strlen($temp) > 1)
          $temp = dirname($temp);

        $return[] = array(
          'type' => 'chmod',
          'filename' => $temp
        );
      }

      if (!is_writable($this_action['destination']) && (file_exists($this_action['destination']) || !is_writable(dirname($this_action['destination']))))
        $return[] = array(
          'type' => 'chmod',
          'filename' => $this_action['destination']
        );
    }
    else if ($actionType == 'require-dir')
    {
      if (!mktree($this_action['destination'], false))
      {
        $temp = $this_action['destination'];
        while (!file_exists($temp) && strlen($temp) > 1)
          $temp = dirname($temp);

        $return[] = array(
          'type' => 'chmod',
          'filename' => $temp
        );
      }
    }
    else if ($actionType == 'require-file')
    {
      if (!mktree(dirname($this_action['destination']), false))
      {
        $temp = dirname($this_action['destination']);
        while (!file_exists($temp) && strlen($temp) > 1)
          $temp = dirname($temp);

        $return[] = array(
          'type' => 'chmod',
          'filename' => $temp
        );
      }

      if (!is_writable($this_action['destination']) && (file_exists($this_action['destination']) || !is_writable(dirname($this_action['destination']))))
        $return[] = array(
          'type' => 'chmod',
          'filename' => $this_action['destination']
        );
    }
    else if ($actionType == 'move-dir' || $actionType == 'move-file')
    {
      if (!mktree(dirname($this_action['destination']), false))
      {
        $temp = dirname($this_action['destination']);
        while (!file_exists($temp) && strlen($temp) > 1)
          $temp = dirname($temp);

        $return[] = array(
          'type' => 'chmod',
          'filename' => $temp
        );
      }

      if (!is_writable($this_action['destination']) && (file_exists($this_action['destination']) || !is_writable(dirname($this_action['destination']))))
        $return[] = array(
          'type' => 'chmod',
          'filename' => $this_action['destination']
        );
    }
    else if ($actionType == 'remove-dir')
    {
      if (!is_writable($this_action['filename']) && file_exists($this_action['destination']))
        $return[] = array(
          'type' => 'chmod',
          'filename' => $this_action['filename']
        );
    }
    else if ($actionType == 'remove-file')
    {
      if (!is_writable($this_action['filename']) && file_exists($this_action['filename']))
        $return[] = array(
          'type' => 'chmod',
          'filename' => $this_action['filename']
        );
    }
  }

  // Only testing - just return a list of things to be done.
  if ($testing_only)
    return $return;

  umask(0);

  $failure = false;
  $not_done = array(array('type' => '!'));
  foreach ($return as $action)
  {
    if ($action['type'] == 'modification' || $action['type'] == 'code' || $action['type'] == 'redirect')
      $not_done[] = $action;

    if ($action['type'] == 'create-dir')
    {
      if (!mktree($action['destination'], 0755) || !is_writable($action['destination']))
        $failure |= !mktree($action['destination'], 0777);
    }
    else if ($action['type'] == 'create-file')
    {
      if (!mktree(dirname($action['destination']), 0755) || !is_writable(dirname($action['destination'])))
        $failure |= !mktree(dirname($action['destination']), 0777);

      // Create an empty file.
      package_put_contents($action['destination'], package_get_contents($action['source']), $testing_only);

      if (!file_exists($action['destination']))
        $failure = true;
    }
    else if ($action['type'] == 'require-dir')
      copytree($action['source'], $action['destination']);
    else if ($action['type'] == 'require-file')
    {
      if (!mktree(dirname($action['destination']), 0755) || !is_writable(dirname($action['destination'])))
        $failure |= !mktree(dirname($action['destination']), 0777);

      package_put_contents($action['destination'], package_get_contents($action['source']), $testing_only);

      $failure |= !copy($action['source'], $action['destination']);
    }
    else if ($action['type'] == 'move-file')
    {
      if (!mktree(dirname($action['destination']), 0755) || !is_writable(dirname($action['destination'])))
        $failure |= !mktree(dirname($action['destination']), 0777);

      $failure |= !rename($action['source'], $action['destination']);
    }
    else if ($action['type'] == 'move-dir')
    {
      if (!mktree($action['destination'], 0755) || !is_writable($action['destination']))
        $failure |= !mktree($action['destination'], 0777);

      $failure |= !rename($action['source'], $action['destination']);
    }
    else if ($action['type'] == 'remove-dir')
      deltree($action['filename']);
    else if ($action['type'] == 'remove-file')
    {
      package_chmod($action['filename']);

      $failure |= !unlink($action['filename']);
    }
  }

  return $not_done;
}

// This is such a pain I created a function for it :P.
function matchPackageVersion($version, $versions)
{
  $version = strtolower($version);
  $for = explode(',', strtolower($versions));

  // Trim them all!
  for ($i = 0, $n = count($for); $i < $n; $i++)
    $for[$i] = trim($for[$i]);

  // The version is explicitly defined... too easy.
  if (in_array($version, $for) || in_array('all', $for))
    return true;

  foreach ($for as $list)
  {
    if (substr($list, -1) == '*' && strpos($list, '-') === false)
    {
      // "Nothing" is the lowest alphanumeric character, z the highest.
      $list = substr($list, 0, -1) . '-' . substr($list, 0, -1) . 'z';
    }
    // Look for a version specification like "1.0-1.2".
    else if (strpos($list, '-') === false)
      continue;

    list ($lower, $upper) = explode('-', $list);

    if (trim($lower) <= $version && trim($upper) >= $version)
      return true;
  }

  // Well, I guess it doesn't match...
  return false;
}

function parse_path($path)
{
  global $modSettings, $boarddir, $sourcedir, $settings;

  $dirs = array(
    '\\' => '/',
    '$boarddir' => $boarddir,
    '$sourcedir' => $sourcedir,
    '$avatardir' => $modSettings['avatar_directory'],
    '$avatars_dir' => $modSettings['avatar_directory'],
    '$themedir' => $settings['default_theme_dir'],
    '$imagesdir' => $settings['default_theme_dir'] . '/' . basename($settings['default_images_url']),
    '$themes_dir' => $boarddir . '/Themes',
    '$languagedir' => $settings['default_theme_dir'] . '/languages',
    '$languages_dir' => $settings['default_theme_dir'] . '/languages',
    '$smileysdir' => $modSettings['smileys_dir'],
    '$smileys_dir' => $modSettings['smileys_dir'],
  );

  if (strlen($path) == 0)
    trigger_error('parse_path(): There should never be an empty filename', E_USER_ERROR);

  return strtr($path, $dirs);
}

function deltree($dir, $delete_dir = true)
{
  global $package_ftp;

  if (!file_exists($dir))
    return;

  $current_dir = @opendir($dir);
  if ($current_dir == false)
  {
    if ($delete_dir && isset($package_ftp))
    {
      $ftp_file = strtr($dir, array($_SESSION['pack_ftp']['root'] => ''));
      if (!is_writable($dir . '/' . $entryname))
        $package_ftp->chmod($ftp_file, 0777);
      $package_ftp->unlink($ftp_file);
    }

    return;
  }

  while ($entryname = readdir($current_dir))
  {
    if (in_array($entryname, array('.', '..')))
      continue;

    if (is_dir($dir . '/' . $entryname))
      deltree($dir . '/' . $entryname);
    else
    {
      // Here, 755 doesn't really matter since we're deleting it anyway.
      if (isset($package_ftp))
      {
        $ftp_file = strtr($dir . '/' . $entryname, array($_SESSION['pack_ftp']['root'] => ''));

        if (!is_writable($dir . '/' . $entryname))
          $package_ftp->chmod($ftp_file, 0777);
        $package_ftp->unlink($ftp_file);
      }
      else
      {
        if (!is_writable($dir . '/' . $entryname))
          @chmod($dir . '/' . $entryname, 0777);
        unlink($dir . '/' . $entryname);
      }
    }
  }

  closedir($current_dir);

  if ($delete_dir)
  {
    if (isset($package_ftp))
    {
      $ftp_file = strtr($dir, array($_SESSION['pack_ftp']['root'] => ''));
      if (!is_writable($dir . '/' . $entryname))
        $package_ftp->chmod($ftp_file, 0777);
      $package_ftp->unlink($ftp_file);
    }
    else
    {
      if (!is_writable($dir))
        @chmod($dir, 0777);
      @rmdir($dir);
    }
  }
}

function mktree($strPath, $mode)
{
  global $package_ftp;

  if (is_dir($strPath))
  {
    if (!is_writable($strPath) && $mode !== false)
    {
      if (isset($package_ftp))
        $package_ftp->chmod(strtr($strPath, array($_SESSION['pack_ftp']['root'] => '')), $mode);
      else
        @chmod($strPath, $mode);
    }

    $test = @opendir($strPath);
    if ($test)
    {
      closedir($test);
      return is_writable($strPath);
    }
    else
      return false;
  }
  // Is this an invalid path and/or we can't make the directory?
  if ($strPath == dirname($strPath) || !mktree(dirname($strPath), $mode))
    return false;

  if (!is_writable(dirname($strPath)) && $mode !== false)
  {
    if (isset($package_ftp))
      $package_ftp->chmod(dirname(strtr($strPath, array($_SESSION['pack_ftp']['root'] => ''))), $mode);
    else
      @chmod(dirname($strPath), $mode);
  }

  if ($mode !== false && isset($package_ftp))
    return $package_ftp->create_dir(strtr($strPath, array($_SESSION['pack_ftp']['root'] => '')));
  else if ($mode === false)
  {
    $test = @opendir(dirname($strPath));
    if ($test)
    {
      closedir($test);
      return true;
    }
    else
      return false;
  }
  else
  {
    mkdir($strPath, $mode);
    $test = @opendir($strPath);
    if ($test)
    {
      closedir($test);
      return true;
    }
    else
      return false;
  }
}

function copytree($source, $destination)
{
  global $package_ftp;

  if (!file_exists($destination) || !is_writable($destination))
    mktree($destination, 0755);
  if (!is_writable($destination))
    mktree($destination, 0777);

  $current_dir = opendir($source);
  if ($current_dir == false)
    return;

  while ($entryname = readdir($current_dir))
  {
    if (in_array($entryname, array('.', '..')))
      continue;

    if (isset($package_ftp))
      $ftp_file = strtr($destination . '/' . $entryname, array($_SESSION['pack_ftp']['root'] => ''));

    if (is_file($source . '/' . $entryname))
    {
      if (isset($package_ftp) && !file_exists($destination . '/' . $entryname))
        $package_ftp->create_file($ftp_file);
      else if (!file_exists($destination . '/' . $entryname))
        @touch($destination . '/' . $entryname);
    }

    package_chmod($destination . '/' . $entryname);

    if (is_dir($source . '/' . $entryname))
      copytree($source . '/' . $entryname, $destination . '/' . $entryname);
    else if (file_exists($destination . '/' . $entryname))
      package_put_contents($destination . '/' . $entryname, package_get_contents($source . '/' . $entryname));
    else
      copy($source . '/' . $entryname, $destination . '/' . $entryname);
  }

  closedir($current_dir);
}

function listtree($path, $sub_path = '')
{
  $data = array();

  $dir = @dir($path . $sub_path);
  if (!$dir)
    return array();
  while ($entry = $dir->read())
  {
    if ($entry == '.' || $entry == '..')
      continue;

    if (is_dir($path . $sub_path . '/' . $entry))
      $data = array_merge($data, listtree($path, $sub_path . '/' . $entry));
    else
      $data[] = array(
        'filename' => $sub_path == '' ? $entry : $sub_path . '/' . $entry,
        'size' => filesize($path . $sub_path . '/' . $entry),
        'skipped' => false,
      );
  }
  $dir->close();

  return $data;
}

// Parse an xml based modification file.
function parseModification($file, $testing = true, $undo = false)
{
  global $boarddir, $sourcedir, $settings, $txt, $modSettings, $package_ftp;

  @set_time_limit(600);
  $xml = new xmlArray(strtr($file, array("\r" => '')));
  $actions = array();
  $everything_found = true;

  if (!$xml->exists('modification') || !$xml->exists('modification/file'))
  {
    $actions[] = array(
      'type' => 'error',
      'filename' => '-',
      'debug' => $txt['package_modification_malformed']
    );
    return $actions;
  }

  $files = $xml->set('modification/file');
  foreach ($files as $file)
  {
    $working_file = parse_path(trim($file->fetch('@name')));

    if ($working_file[0] != '/' && $working_file[1] != ':')
    {
      trigger_error('parseModification(): The filename \'' . $working_file . '\' is not a full path!', E_USER_WARNING);

      $working_file = $boarddir . '/' . $working_file;
    }

    // Doesn't exist - give an error or what?
    if (!file_exists($working_file) && (!$file->exists('@error') || !in_array(trim($file->fetch('@error')), array('ignore', 'skip'))))
    {
      $actions[] = array(
        'type' => 'missing',
        'filename' => $working_file,
        'debug' => $txt['package_modification_missing']
      );

      $everything_found = false;
      continue;
    }
    // Skip the file if it doesn't exist.
    else if (!file_exists($working_file) && $file->exists('@error') && trim($file->fetch('@error')) === 'skip')
    {
      $actions[] = array(
        'type' => 'skipping',
        'filename' => $working_file,
      );
      continue;
    }
    // Okay, we're creating this file then...?
    else if (!file_exists($working_file))
      $working_data = '';
    // Phew, it exists!  Load 'er up!
    else
      $working_data = str_replace("\r", '', package_get_contents($working_file));

    $actions[] = array(
      'type' => 'opened',
      'filename' => $working_file
    );

    $operations = $file->exists('operation') ? $file->set('operation') : array();
    foreach ($operations as $operation)
    {
      // Convert operation to an array.
      $actual_operation = array(
        'searches' => array(),
        'error' => $operation->exists('@error') && in_array(trim($operation->fetch('@error')), array('ignore', 'fatal', 'required')) ? trim($operation->fetch('@error')) : 'fatal',
      );

      // The 'add' parameter is used for all searches in this operation.
      $add = $operation->exists('add') ? $operation->fetch('add') : '';

      // Grab all search items of this operation (in most cases just 1).
      $searches = $operation->set('search');
      foreach ($searches as $i => $search)
        $actual_operation['searches'][] = array(
          'position' => $search->exists('@position') && in_array(trim($search->fetch('@position')), array('before', 'after', 'replace', 'end')) ? trim($search->fetch('@position')) : 'replace',
          'is_reg_exp' => $search->exists('@regexp') && trim($search->fetch('@regexp')) === 'true',
          'loose_whitespace' => $search->exists('@whitespace') && trim($search->fetch('@whitespace')) === 'loose',
          'search' => $search->fetch('.'),
          'add' => $add,
          'preg_search' => '',
          'preg_replace' => '',
        );

      // At least one search should be defined.
      if (empty($actual_operation['searches']))
      {
        $actions[] = array(
          'type' => 'failure',
          'filename' => $working_file,
          'search' => $search['search'],
        );

        // Skip to the next operation.
        continue;
      }

      // Reverse the operations in case of undoing stuff.
      if ($undo)
      {
        foreach ($actual_operation['searches'] as $i => $search)
        {

          // Reverse modification of regular expressions are not allowed.
          if ($search['is_reg_exp'])
          {
            if ($actual_operation['error'] === 'fatal')
              $actions[] = array(
                'type' => 'failure',
                'filename' => $working_file,
                'search' => $search['search'],
              );

            // Continue to the next operation.
            continue 2;
          }

          // The replacement is now the search subject...
          if ($search['position'] === 'replace' || $search['position'] === 'end')
            $actual_operation['searches'][$i]['search'] = $search['add'];
          else
          {
            // Reversing a before/after modification becomes a replacement.
            $actual_operation['searches'][$i]['position'] = 'replace';

            if ($search['position'] === 'before')
              $actual_operation['searches'][$i]['search'] .= $search['add'];
            else if ($search['position'] === 'after')
              $actual_operation['searches'][$i]['search'] = $search['add'] . $search['search'];
          }

          // ...and the search subject is now the replacement.
          $actual_operation['searches'][$i]['add'] = $search['search'];
        }
      }

      // Sort the search list so the replaces come before the add before/after's.
      if (count($actual_operation['searches']) !== 1)
      {
        $replacements = array();

        foreach ($actual_operation['searches'] as $i => $search)
        {
          if ($search['position'] === 'replace')
          {
            $replacements[] = $search;
            unset($actual_operation['searches'][$i]);
          }
        }
        $actual_operation['searches'] = array_merge($replacements, $actual_operation['searches']);
      }

      // Create regular expression replacements from each search.
      foreach ($actual_operation['searches'] as $i => $search)
      {
        // Not much needed if the search subject is already a regexp.
        if ($search['is_reg_exp'])
          $actual_operation['searches'][$i]['preg_search'] = $search['search'];
        else
        {
          // Make the search subject fit into a regular expression.
          $actual_operation['searches'][$i]['preg_search'] = preg_quote($search['search'], '~');

          // Using 'loose', a random amount of tabs and spaces may be used.
          if ($search['loose_whitespace'])
            $actual_operation['searches'][$i]['preg_search'] = preg_replace('~[ \t]+~', '[ \t]+', $actual_operation['searches'][$i]['preg_search']);
        }

        // Shuzzup.  This is done so we can safely use a regular expression. ($0 is bad!!)
        $actual_operation['searches'][$i]['preg_replace'] = strtr($search['add'], array('$' => '[$PACK' . 'AGE1$]', '\\' => '[$PACK' . 'AGE2$]'));

        // Before, so the replacement comes after the search subject :P
        if ($search['position'] === 'before')
        {
          $actual_operation['searches'][$i]['preg_search'] = '(' . $actual_operation['searches'][$i]['preg_search'] . ')';
          $actual_operation['searches'][$i]['preg_replace'] = '$1' . $actual_operation['searches'][$i]['preg_replace'];
        }

        // After, after what?
        else if ($search['position'] === 'after')
        {
          $actual_operation['searches'][$i]['preg_search'] = '(' . $actual_operation['searches'][$i]['preg_search'] . ')';
          $actual_operation['searches'][$i]['preg_replace'] .= '$1';
        }

        // Position the replacement at the end of the file (or just before the closing PHP tags).
        else if ($search['position'] === 'end')
        {
          if ($undo)
          {
            $actual_operation['searches'][$i]['preg_replace'] = '';
          }
          else
          {
            $actual_operation['searches'][$i]['preg_search'] = '(\\n\\?\\>)?$';
            $actual_operation['searches'][$i]['preg_replace'] .= '$1';
          }
        }

        // Testing 1, 2, 3...
        $failed = preg_match('~' . $actual_operation['searches'][$i]['preg_search'] . '~s', $working_data) === 0;

        // Nope, search pattern not found.
        if ($failed && $actual_operation['error'] === 'fatal')
        {
          $actions[] = array(
            'type' => 'failure',
            'filename' => $working_file,
            'search' => $actual_operation['searches'][$i]['preg_search'],
          );

          $everything_found = false;
          continue;
        }

        // Found, but in this case, that means failure!
        else if (!$failed && $actual_operation['error'] === 'required')
        {
          $actions[] = array(
            'type' => 'failure',
            'filename' => $working_file,
            'search' => $actual_operation['searches'][$i]['preg_search'],
          );

          $everything_found = false;
          continue;
        }

        // Replace it into nothing? That's not an option...unless it's an undoing end.
        if ($search['add'] === '' && ($search['position'] !== 'end' || !$undo))
          continue;

        // Finally, we're doing some replacements.
        $working_data = preg_replace('~' . $actual_operation['searches'][$i]['preg_search'] . '~s', $actual_operation['searches'][$i]['preg_replace'], $working_data, 1);

        $actions[] = array(
          'type' => 'replace',
          'filename' => $working_file,
          'search' => $actual_operation['searches'][$i]['preg_search'],
          'replace' =>  $actual_operation['searches'][$i]['preg_replace'],
        );
      }
    }

    // Fix any little helper symbols ;).
    $working_data = strtr($working_data, array('[$PACK' . 'AGE1$]' => '$', '[$PACK' . 'AGE2$]' => '\\'));

    package_chmod($working_file);

    if ((file_exists($working_file) && !is_writable($working_file)) || (!file_exists($working_file) && !is_writable(dirname($working_file))))
      $actions[] = array(
        'type' => 'chmod',
        'filename' => $working_file
      );

    if (basename($working_file) == 'Settings_bak.php')
      continue;

    if (!$testing && !empty($modSettings['package_make_backups']) && file_exists($working_file))
    {
      // No, no, not Settings.php!
      if (basename($working_file) == 'Settings.php')
        @copy($working_file, dirname($working_file) . '/Settings_bak.php');
      else
        @copy($working_file, $working_file . '~');
    }

    // Always call this, even if in testing, because it won't really be written in testing mode.
    package_put_contents($working_file, $working_data, $testing);

    $actions[] = array(
      'type' => 'saved',
      'filename' => $working_file
    );
  }

  $actions[] = array(
    'type' => 'result',
    'status' => $everything_found
  );

  return $actions;
}

// Parses a BoardMod format mod file...
function parseBoardMod($file, $testing = true, $undo = false)
{
  global $boarddir, $sourcedir, $settings, $txt, $modSettings;

  @set_time_limit(600);
  $file = strtr($file, array("\r" => ''));

  $working_file = null;
  $working_search = null;
  $working_data = '';
  $replace_with = null;

  $actions = array();
  $everything_found = true;

  while (preg_match('~<(edit file|file|search|search for|add|add after|replace|add before|add above|above|before)>\n(.*?)\n</\\1>~is', $file, $code_match) != 0)
  {
    // Edit a specific file.
    if ($code_match[1] == 'file' || $code_match[1] == 'edit file')
    {
      // Backup the old file.
      if ($working_file !== null)
      {
        package_chmod($working_file);

        // Don't even dare.
        if (basename($working_file) == 'Settings_bak.php')
          continue;

        if (!is_writable($working_file))
          $actions[] = array(
            'type' => 'chmod',
            'filename' => $working_file
          );

        if (!$testing && !empty($modSettings['package_make_backups']) && file_exists($working_file))
        {
          if (basename($working_file) == 'Settings.php')
            @copy($working_file, dirname($working_file) . '/Settings_bak.php');
          else
            @copy($working_file, $working_file . '~');
        }

        package_put_contents($working_file, $working_data, $testing);
      }

      if ($working_file !== null)
        $actions[] = array(
          'type' => 'saved',
          'filename' => $working_file
        );

      // Make sure the file exists!
      $working_file = parse_path($code_match[2]);

      if ($working_file[0] != '/' && $working_file[1] != ':')
      {
        trigger_error('parseBoardMod(): The filename \'' . $working_file . '\' is not a full path!', E_USER_WARNING);

        $working_file = $boarddir . '/' . $working_file;
      }

      if (!file_exists($working_file))
      {
        $places_to_check = array($boarddir, $sourcedir, $settings['default_theme_dir'], $settings['default_theme_dir'] . '/languages');

        foreach ($places_to_check as $place)
          if (file_exists($place . '/' . $working_file))
          {
            $working_file = $place . '/' . $working_file;
            break;
          }
      }

      if (file_exists($working_file))
      {
        // Load the new file.
        $working_data = str_replace("\r", '', package_get_contents($working_file));

        $actions[] = array(
          'type' => 'opened',
          'filename' => $working_file
        );
      }
      else
      {
        $actions[] = array(
          'type' => 'missing',
          'filename' => $working_file
        );

        $working_file = null;
        $everything_found = false;
      }

      // Can't be searching for something...
      $working_search = null;
    }
    // Search for a specific string.
    else if (($code_match[1] == 'search' || $code_match[1] == 'search for') && $working_file !== null)
    {
      if ($working_search !== null)
      {
        $actions[] = array(
          'type' => 'error',
          'filename' => $working_file
        );

        $everything_found = false;
      }

      $working_search = $code_match[2];
    }
    // Must've already loaded a search string.
    else if ($working_search !== null)
    {
      // This is the base string....
      $replace_with = $code_match[2];

      // Add this afterward...
      if ($code_match[1] == 'add' || $code_match[1] == 'add after')
        $replace_with = $working_search . "\n" . $replace_with;
      // Add this beforehand.
      else if ($code_match[1] == 'before' || $code_match[1] == 'add before' || $code_match[1] == 'above' || $code_match[1] == 'add above')
        $replace_with .= "\n" . $working_search;
      // Otherwise.. replace with $replace_with ;).
    }

    // If we have a search string, replace string, and open file..
    if ($working_search !== null && $replace_with !== null && $working_file !== null)
    {
      // Make sure it's somewhere in the string.
      if ($undo)
      {
        $temp = $replace_with;
        $replace_with = $working_search;
        $working_search = $temp;
      }

      if (strpos($working_data, $working_search) !== false)
      {
        $working_data = str_replace($working_search, $replace_with, $working_data);

        $actions[] = array(
          'type' => 'replace',
          'filename' => $working_file,
          'search' => $working_search,
          'replace' => $replace_with
        );
      }
      // It wasn't found!
      else
      {
        $actions[] = array(
          'type' => 'failure',
          'filename' => $working_file,
          'search' => $working_search
        );

        $everything_found = false;
      }

      // These don't hold any meaning now.
      $working_search = null;
      $replace_with = null;
    }

    // Get rid of the old tag.
    $file = substr_replace($file, '', strpos($file, $code_match[0]), strlen($code_match[0]));
  }

  // Backup the old file.
  if ($working_file !== null)
  {
    package_chmod($working_file);

    if (!is_writable($working_file))
      $actions[] = array(
        'type' => 'chmod',
        'filename' => $working_file
      );

    if (!$testing && !empty($modSettings['package_make_backups']) && file_exists($working_file))
    {
      if (basename($working_file) == 'Settings.php')
        @copy($working_file, dirname($working_file) . '/Settings_bak.php');
      else
        @copy($working_file, $working_file . '~');
    }

    package_put_contents($working_file, $working_data, $testing);
  }

  if ($working_file !== null)
    $actions[] = array(
      'type' => 'saved',
      'filename' => $working_file
    );

  $actions[] = array(
    'type' => 'result',
    'status' => $everything_found
  );

  return $actions;
}

function package_get_contents($filename)
{
  global $package_cache, $modSettings;

  if (!isset($package_cache))
  {
    // Windows doesn't seem to care about the memory_limit.
    if (!empty($modSettings['package_disable_cache']) || ini_set('memory_limit', '128M') !== false || strpos(strtolower(PHP_OS), 'win') !== false)
      $package_cache = array();
    else
      $package_cache = false;
  }

  if (strpos($filename, 'Packages/') !== false || $package_cache === false || !isset($package_cache[$filename]))
    return file_get_contents($filename);
  else
    return $package_cache[$filename];
}

function package_put_contents($filename, $data, $testing = false)
{
  global $package_ftp, $package_cache, $modSettings;
  static $text_filetypes = array('php', 'txt', '.js', 'css', 'vbs', 'tml', 'htm');

  if (!isset($package_cache))
  {
    // Try to increase the memory limit - we don't want to run out of ram!
    if (!empty($modSettings['package_disable_cache']) || ini_set('memory_limit', '128M') !== false || strpos(strtolower(PHP_OS), 'win') !== false)
      $package_cache = array();
    else
      $package_cache = false;
  }

  if (isset($package_ftp))
    $ftp_file = strtr($filename, array($_SESSION['pack_ftp']['root'] => ''));

  if (!file_exists($filename) && isset($package_ftp))
    $package_ftp->create_file($ftp_file);
  else if (!file_exists($filename))
    @touch($filename);

  package_chmod($filename);

  if (!$testing && (strpos($filename, 'Packages/') !== false || $package_cache === false))
  {
    $fp = @fopen($filename, in_array(substr($filename, -3), $text_filetypes) ? 'w' : 'wb');

    // We should show an error message or attempt a rollback, no?
    if (!$fp)
      return false;

    fwrite($fp, $data);
    fclose($fp);
  }
  else if (strpos($filename, 'Packages/') !== false || $package_cache === false)
    return strlen($data);
  else
  {
    $package_cache[$filename] = $data;

    // Permission denied, eh?
    $fp = @fopen($filename, 'r+');
    if (!$fp)
      return false;
    fclose($fp);
  }

  return strlen($data);
}

function package_flush_cache($trash = false)
{
  global $package_ftp, $package_cache;
  static $text_filetypes = array('php', 'txt', '.js', 'css', 'vbs', 'tml', 'htm');

  if (empty($package_cache))
    return;

  // First, let's check permissions!
  foreach ($package_cache as $filename => $data)
  {
    if (isset($package_ftp))
      $ftp_file = strtr($filename, array($_SESSION['pack_ftp']['root'] => ''));

    if (!file_exists($filename) && isset($package_ftp))
      $package_ftp->create_file($ftp_file);
    else if (!file_exists($filename))
      @touch($filename);

    package_chmod($filename);

    $fp = fopen($filename, 'r+');
    if (!$fp && !$trash)
    {
      // We should have package_chmod()'d them before, no?!
      trigger_error('package_flush_cache(): some files are still not writable', E_USER_WARNING);
      return;
    }
    fclose($fp);
  }

  if ($trash)
  {
    $package_cache = array();
    return;
  }

  foreach ($package_cache as $filename => $data)
  {
    $fp = fopen($filename, in_array(substr($filename, -3), $text_filetypes) ? 'w' : 'wb');
    fwrite($fp, $data);
    fclose($fp);
  }

  $package_cache = array();
}

function package_chmod($filename)
{
  global $package_ftp;

  if (file_exists($filename) && is_writable($filename))
    return;

  // File exists, but isn't writable and we have FTP.
  if (file_exists($filename) && isset($package_ftp))
  {
    $ftp_file = strtr($filename, array($_SESSION['pack_ftp']['root'] => ''));

    $package_ftp->chmod($ftp_file, 0755);
    if (!is_writable($filename))
      $package_ftp->chmod($ftp_file, 0777);
  }
  // File exists, but no FTP help.
  else if (file_exists($filename))
  {
    @chmod($filename, 0755);
    if (!is_writable($filename))
      @chmod($filename, 0777);
  }
  // File does not exist, and we have FTP.
  else if (isset($package_ftp))
  {
    $ftp_file = strtr(dirname($filename), array($_SESSION['pack_ftp']['root'] => ''));

    $package_ftp->chmod($ftp_file, 0755);
    if (!is_writable($filename))
      $package_ftp->chmod($ftp_file, 0777);
  }
  // File does not exist, and no FTP.
  else
  {
    $filename = dirname($filename);

    @chmod($filename, 0755);
    if (!is_writable($filename))
      @chmod($filename, 0777);
  }
}

function package_crypt($pass)
{
  $n = strlen($pass);

  $salt = session_id();
  while (strlen($salt) < $n)
    $salt .= session_id();

  for ($i = 0; $i < $n; $i++)
    $pass{$i} = chr(ord($pass{$i}) ^ (ord($salt{$i}) - 32));

  return $pass;
}

function package_create_backup($id = 'backup')
{
  global $db_prefix, $sourcedir, $boarddir;

  $files = array();

  $base_files = array('index.php', 'SSI.php', 'agreement.txt', 'ssi_examples.php', 'ssi_examples.shtml');
  foreach ($base_files as $file)
  {
    if (file_exists($boarddir . '/' . $file))
      $files[realpath($boarddir . '/' . $file)] = array(
        empty($_REQUEST['use_full_paths']) ? $file : $boarddir . '/' . $file,
        stat($boarddir . '/' . $file)
      );
  }

  $dirs = array(
    $sourcedir => empty($_REQUEST['use_full_paths']) ? 'Sources/' : strtr($sourcedir . '/', '\\', '/')
  );

  $request = db_query("
    SELECT value
    FROM {$db_prefix}themes
    WHERE ID_MEMBER = 0
      AND variable = 'theme_dir'", __FILE__, __LINE__);
  while ($row = mysqli_fetch_assoc($request))
    $dirs[$row['value']] = empty($_REQUEST['use_full_paths']) ? 'Themes/' . basename($row['value']) . '/' : strtr($row['value'] . '/', '\\', '/');
  mysqli_free_result($request);

  while (!empty($dirs))
  {
    list ($dir, $dest) = each($dirs);
    unset($dirs[$dir]);

    $listing = @dir($dir);
    if (!$listing)
      continue;
    while ($entry = $listing->read())
    {
      if (preg_match('~^(\.{1,2}|CVS|backup.*|help|images|.*\~)$~', $entry) != 0)
        continue;

      $filepath = realpath($dir . '/' . $entry);
      if (isset($files[$filepath]))
        continue;

      $stat = stat($dir . '/' . $entry);
      if ($stat['mode'] & 040000)
      {
        $files[$filepath] = array($dest . $entry . '/', $stat);
        $dirs[$dir . '/' . $entry] = $dest . $entry . '/';
      }
      else
        $files[$filepath] = array($dest . $entry, $stat);
    }
    $listing->close();
  }

  if (!file_exists($boarddir . '\\web\\archivos/paquetes/backups'))
    mktree($boarddir . '\\web\\archivos/paquetes/backups', 0777);
  if (!is_writable($boarddir . '\\web\\archivos/paquetes/backups'))
    package_chmod($boarddir . '\\web\\archivos/paquetes/backups');
  $output_file = $boarddir . '\\web\\archivos/paquetes/backups/' . date('Y-m-d_') . preg_replace('~[$\\\\/:<>|?*"\']~', '', $id);
  $output_ext = '.tar' . (function_exists('gzopen') ? '.gz' : '');

  if (file_exists($output_file . $output_ext))
  {
    $i = 2;
    while (file_exists($output_file . '_' . $i . $output_ext))
      $i++;
    $output_file = $output_file . '_' . $i . $output_ext;
  }
  else
    $output_file .= $output_ext;

  @set_time_limit(300);
  if (function_exists('apache_reset_timeout'))
    apache_reset_timeout();

  if (function_exists('gzopen'))
  {
    $fwrite = 'gzwrite';
    $fclose = 'gzclose';
    $output = gzopen($output_file, 'wb');
  }
  else
  {
    $fwrite = 'fwrite';
    $fclose = 'fclose';
    $output = fopen($output_file, 'wb');
  }

  foreach ($files as $real_file => $file)
  {
    if (!file_exists($real_file))
      continue;

    $stat = $file[1];
    if (substr($file[0], -1) == '/')
      $stat['size'] = 0;

    $current = pack('a100a8a8a8a12a12a8a1a100a6a2a32a32a8a8a155a12', $file[0], decoct($stat['mode']), sprintf('%06d', decoct($stat['uid'])), sprintf('%06d', decoct($stat['gid'])), decoct($stat['size']), decoct($stat['mtime']), '', 0, '', '', '', '', '', '', '', '', '');

    $checksum = 256;
    for ($i = 0; $i < 512; $i++)
      $checksum += ord($current{$i});

    $fwrite($output, substr($current, 0, 148) . pack('a8', decoct($checksum)) . substr($current, 156, 511));

    if ($stat['size'] == 0)
      continue;

    $fp = fopen($real_file, 'rb');
    while (!feof($fp))
      $fwrite($output, fread($fp, 16384));
    fclose($fp);

    $fwrite($output, pack('a' . (512 - $stat['size'] % 512), ''));
  }

  $fwrite($output, pack('a1024', ''));
  $fclose($output);
}

// Get the contents of a URL, irrespective of allow_url_fopen.
function fetch_web_data($url, $post_data = '', $keep_alive = false)
{
  global $webmaster_email;
  static $keep_alive_dom = null, $keep_alive_fp = null;

  preg_match('~^(http|ftp)(s)?://([^/:]+)(:(\d))?(.+)$~', $url, $match);

  // An FTP url.  We should try connecting and RETRieving it...
  if (isset($match[1]) && $match[1] == 'ftp')
  {
    // Establish a connection and attempt to enable passive mode.
    $ftp = new ftp_connection(($match[2] ? 'ssl://' : '') . $match[3], empty($match[5]) ? 21 : $match[5], 'anonymous', $webmaster_email);
    if ($ftp->error !== false || !$ftp->passive())
      return false;

    // I want that one *points*!
    fwrite($ftp->connection, 'RETR ' . $match[6] . "\r\n");

    // Since passive mode worked (or we would have returned already!) open the connection.
    $fp = @fsockopen($ftp->pasv['ip'], $ftp->pasv['port'], $err, $err, 5);
    if (!$fp)
      return false;

    // The server should now say something in acknowledgement.
    $ftp->check_response(150);

    $data = '';
    while (!feof($fp))
      $data .= fread($fp, 4096);
    fclose($fp);

    // All done, right?  Good.
    $ftp->check_response(226);
    $ftp->close();
  }
  // This is more likely; a standard HTTP URL.
  else if (isset($match[1]) && $match[1] == 'http')
  {
    if ($keep_alive && $match[3] == $keep_alive_dom)
      $fp = $keep_alive_fp;
    if (empty($fp))
    {
      // Open the socket on the port we want...
      $fp = @fsockopen(($match[2] ? 'ssl://' : '') . $match[3], empty($match[5]) ? ($match[2] ? 443 : 80) : $match[5], $err, $err, 5);
      if (!$fp)
        return false;
    }

    if ($keep_alive)
    {
      $keep_alive_dom = $match[3];
      $keep_alive_fp = $fp;
    }

    // I want this, from there, and I'm not going to be bothering you for more (probably.)
    if (empty($post_data))
    {
      fwrite($fp, 'GET ' . $match[6] . " HTTP/1.0\r\n");
      fwrite($fp, 'Host: ' . $match[3] . (empty($match[5]) ? ($match[2] ? '443' : '') : ':' . $match[5]) . "\r\n");
      fwrite($fp, 'User-Agent: PHP/SMF' . "\r\n");
      if ($keep_alive)
        fwrite($fp, 'Connection: Keep-Alive' . "\r\n\r\n");
      else
        fwrite($fp, 'Connection: close' . "\r\n\r\n");
    }
    else
    {
      fwrite($fp, 'POST ' . $match[6] . " HTTP/1.0\r\n");
      fwrite($fp, 'Host: ' . $match[3] . (empty($match[5]) ? ($match[2] ? '443' : '') : ':' . $match[5]) . "\r\n");
      fwrite($fp, 'User-Agent: PHP/SMF' . "\r\n");
      if ($keep_alive)
        fwrite($fp, 'Connection: Keep-Alive' . "\r\n");
      else
        fwrite($fp, 'Connection: close' . "\r\n");
      fwrite($fp, 'Content-Type: application/x-www-form-urlencoded' . "\r\n");
      fwrite($fp, 'Content-Length: ' . strlen($post_data) . "\r\n\r\n");
      fwrite($fp, $post_data);
    }

    // Make sure we get a 200 OK.
    $response = fgets($fp, 768);
    if (strpos($response, ' 200 ') === false && strpos($response, ' 201 ') === false)
      return false;

    // Skip the headers...
    while (!feof($fp) && trim($header = fgets($fp, 4096)) != '')
    {
      if (preg_match('~content-length:\s*(\d+)~i', $header, $match) != 0)
        $content_length = $match[1];
      else if (preg_match('~connection:\s*close~i', $header) != 0)
      {
        $keep_alive_dom = null;
        $keep_alive = false;
      }

      continue;
    }

    $data = '';
    if (isset($content_length))
    {
      while (!feof($fp) && strlen($data) < $content_length)
        $data .= fread($fp, $content_length - strlen($data));
    }
    else
    {
      while (!feof($fp))
        $data .= fread($fp, 4096);
    }

    if (!$keep_alive)
      fclose($fp);
  }
  else
  {
    // Umm, this shouldn't happen?
    trigger_error('fetch_web_data(): Bad URL', E_USER_NOTICE);
    $data = false;
  }

  return $data;
}

// An xml array.  Reads in xml, allows you to access it simply.  Version 1.1.
class xmlArray
{
  // The array and debugging output level.
  var $array, $debug_level, $trim;

  // Create an xml array.
  // the xml data, trim elements?, debugging output level, reserved.
  //ie. $xml = new xmlArray(file('data.xml'));
  function xmlArray($data, $auto_trim = false, $level = null, $is_clone = false)
  {
    // If we're using this try to get some more memory.
     @ini_set('memory_limit', '32M');

    // Set the debug level.
    $this->debug_level = $level !== null ? $level : error_reporting();
    $this->trim = $auto_trim;

    // Is the data already parsed?
    if ($is_clone)
    {
      $this->array = $data;
      return;
    }

    // Is the input an array? (ie. passed from file()?)
    if (is_array($data))
      $data = implode('', $data);

    // Remove any xml declaration or doctype, and parse out comments and CDATA.
    $data = preg_replace('/<!--.*?-->/s', '', $this->_to_cdata(preg_replace(array('/^<\?xml.+?\?' . '>/is', '/<!DOCTYPE[^>]+?' . '>/s'), '', $data)));

    // Now parse the xml!
    $this->array = $this->_parse($data);
  }

  // Get the root element's name.
  //ie. echo $element->name();
  function name()
  {
    return isset($this->array['name']) ? $this->array['name'] : '';
  }

  // Get a specified element's value or attribute by path.
  // the path to the element to fetch, whether to include elements?
  //ie. $data = $xml->fetch('html/head/title');
  function fetch($path, $get_elements = false)
  {
    // Get the element, in array form.
    $array = $this->path($path);

    if ($array === false)
      return false;

    // Getting elements into this is a bit complicated...
    if ($get_elements && !is_string($array))
    {
      $temp = '';

      // Use the _xml() function to get the xml data.
      foreach ($array->array as $val)
      {
        // Skip the name and any attributes.
        if (is_array($val))
          $temp .= $this->_xml($val, null);
      }

      // Just get the XML data and then take out the CDATAs.
      return $this->_to_cdata($temp);
    }

    // Return the value - taking care to pick out all the text values.
    return is_string($array) ? $array : $this->_fetch($array->array);
  }

  // Get an element, returns a new xmlArray.
  // the path to the element to get, always return full result set? (ie. don't contract a single item.)
  //ie. $element = $xml->path('html/body');
  function path($path, $return_full = false)
  {
    // Split up the path.
    $path = explode('/', $path);

    // Start with a base array.
    $array = $this->array;

    // For each element in the path.
    foreach ($path as $el)
    {
      // Deal with sets....
      if (strpos($el, '[') !== false)
      {
        $lvl = (int) substr($el, strpos($el, '[') + 1);
        $el = substr($el, 0, strpos($el, '['));
      }
      // Find an attribute.
      else if (substr($el, 0, 1) == '@')
      {
        // It simplifies things if the attribute is already there ;).
        if (isset($array[$el]))
          return $array[$el];
        else
        {
          if (function_exists('debug_backtrace'))
          {
            $trace = debug_backtrace();
            $i = 0;
            while ($i < count($trace) && isset($trace[$i]['class']) && $trace[$i]['class'] == get_class($this))
              $i++;
            $debug = ' from ' . $trace[$i - 1]['file'] . ' on line ' . $trace[$i - 1]['line'];
          }
          else
            $debug = '';

          // Cause an error.
          if ($this->debug_level & E_NOTICE)
            trigger_error('Undefined XML attribute: ' . substr($el, 1) . $debug, E_USER_NOTICE);
          return false;
        }
      }
      else
        $lvl = null;

      // Find this element.
      $array = $this->_path($array, $el, $lvl);
    }

    // Clean up after $lvl, for $return_full.
    if ($return_full && (!isset($array['name']) || substr($array['name'], -1) != ']'))
      $array = array('name' => $el . '[]', $array);

    // Create the right type of class...
    $newClass = get_class($this);

    // Return a new xmlArray for the result.
    return $array === false ? false : new $newClass($array, $this->trim, $this->debug_level, true);
  }

  // Check if an element exists.
  // the path to the element to get.
  //ie. echo $xml->exists('html/body') ? 'y' : 'n';
  function exists($path)
  {
    // Split up the path.
    $path = explode('/', $path);

    // Start with a base array.
    $array = $this->array;

    // For each element in the path.
    foreach ($path as $el)
    {
      // Deal with sets....
      if (strpos($el, '[') !== false)
      {
        $lvl = (int) substr($el, strpos($el, '[') + 1);
        $el = substr($el, 0, strpos($el, '['));
      }
      // Find an attribute.
      else if (substr($el, 0, 1) == '@')
        return isset($array[$el]);
      else
        $lvl = null;

      // Find this element.
      $array = $this->_path($array, $el, $lvl, true);
    }

    return $array !== false;
  }

  // Count the number of occurances of a path.
  // the path to search for.
  //ie. echo $xml->count('html/head/meta');
  function count($path)
  {
    // Get the element, always returning a full set.
    $temp = $this->path($path, true);

    // Start at zero, then count up all the numeric keys.
    $i = 0;
    foreach ($temp->array as $item)
    {
      if (is_array($item))
        $i++;
    }

    return $i;
  }

  // Get an array of xmlArray's for use with foreach.
  // the path to search for.
  //ie. foreach ($xml->set('html/body/p') as $p)
  function set($path)
  {
    // None as yet, just get the path.
    $array = array();
    $xml = $this->path($path, true);

    foreach ($xml->array as $val)
    {
      // Skip these, they aren't elements.
      if (!is_array($val) || $val['name'] == '!')
        continue;

      // Create the right type of class...
      $newClass = get_class($this);

      // Create a new xmlArray and stick it in the array.
      $array[] = new $newClass($val, $this->trim, $this->debug_level, true);
    }

    return $array;
  }

  // Create an xml file from an xml array.
  // the path to the element. (optional)
  //ie. echo $this->create_xml()
  function create_xml($path = null)
  {
    // Was a path specified?  If so, use that array.
    if ($path !== null)
    {
      $path = $this->path($path);

      // The path was not found!!!
      if ($path === false)
        return false;

      $path = $path->array;
    }
    // Just use the current array.
    else
      $path = $this->array;

    // Add the xml declaration to the front.
    return '<?xml version="1.0"?' . '>' . $this->_xml($path, 0);
  }

  // Output the xml in an array form.
  // the path to output.
  //ie. print_r($xml->to_array());
  function to_array($path = null)
  {
    // Are we doing a specific path?
    if ($path !== null)
    {
      $path = $this->path($path);

      // The path was not found!!!
      if ($path === false)
        return false;

      $path = $path->array;
    }
    // No, so just use the current array.
    else
      $path = $this->array;

    return $this->_array($path);
  }

  // Parse data into an array. (privately used...)
  function _parse($data)
  {
    // Start with an 'empty' array with no data.
    $current = array(
    );

    // Loop until we're out of data.
    while ($data != '')
    {
      // Find and remove the next tag.
      preg_match('/\A<([\w\-:]+)((?:\s+.+?)?)(\s\/)?' . '>/', $data, $match);
      if (isset($match[0]))
        $data = preg_replace('/' . preg_quote($match[0], '/') . '/s', '', $data, 1);

      // Didn't find a tag?  Keep looping....
      if (!isset($match[1]) || $match[1] == '')
      {
        // If there's no <, the rest is data.
        if (strpos($data, '<') === false)
        {
          $text_value = $this->_from_cdata($data);
          $data = '';

          if ($text_value != '')
            $current[] = array(
              'name' => '!',
              'value' => $text_value
            );
        }
        // If the < isn't immediately next to the current position... more data.
        else if (strpos($data, '<') > 0)
        {
          $text_value = $this->_from_cdata(substr($data, 0, strpos($data, '<')));
          $data = substr($data, strpos($data, '<'));

          if ($text_value != '')
            $current[] = array(
              'name' => '!',
              'value' => $text_value
            );
        }
        // If we're looking at a </something> with no start, kill it.
        else if (strpos($data, '<') !== false && strpos($data, '<') == 0)
        {
          if (strpos($data, '<', 1) !== false)
          {
            $text_value = $this->_from_cdata(substr($data, 0, strpos($data, '<', 1)));
            $data = substr($data, strpos($data, '<', 1));

            if ($text_value != '')
              $current[] = array(
                'name' => '!',
                'value' => $text_value
              );
          }
          else
          {
            $text_value = $this->_from_cdata($data);
            $data = '';

            if ($text_value != '')
              $current[] = array(
                'name' => '!',
                'value' => $text_value
              );
          }
        }

        // Wait for an actual occurance of an element.
        continue;
      }

      // Create a new element in the array.
      $el = &$current[];
      $el['name'] = $match[1];

      // If this ISN'T empty, remove the close tag and parse the inner data.
      if ((!isset($match[3]) || $match[3] != ' /') && (!isset($match[2]) || $match[2] != ' /'))
      {
        // Because PHP 5.2.0+ seems to croak using regex, we'll have to do this the less fun way.
        $last_tag_end = strpos($data, '</' . $match[1]. '>');
        if ($last_tag_end === false)
          continue;

        $offset = 0;
        while (1 == 1)
        {
          // Where is the next start tag?
          $next_tag_start = strpos($data, '<' . $match[1], $offset);
          // If the next start tag is after the last end tag then we've found the right close.
          if ($next_tag_start === false || $next_tag_start > $last_tag_end)
            break;

          // If not then find the next ending tag.
          $next_tag_end = strpos($data, '</' . $match[1]. '>', $offset);

          // Didn't find one? Then just use the last and sod it.
          if ($next_tag_end === false)
            break;
          else
          {
            $last_tag_end = $next_tag_end;
            $offset = $next_tag_start + 1;
          }
        }
        // Parse the insides.
        $inner_match = substr($data, 0, $last_tag_end);
        // Data now starts from where this section ends.
        $data = substr($data, $last_tag_end + strlen('</' . $match[1]. '>'));

        if (!empty($inner_match))
        {
          // Parse the inner data.
          if (strpos($inner_match, '<') !== false)
            $el += $this->_parse($inner_match);
          else if (trim($inner_match) != '')
          {
            $text_value = $this->_from_cdata($inner_match);
            if ($text_value != '')
              $el[] = array(
                'name' => '!',
                'value' => $text_value
              );
          }
        }
      }

      // If we're dealing with attributes as well, parse them out.
      if (isset($match[2]) && $match[2] != '')
      {
        // Find all the attribute pairs in the string.
        preg_match_all('/([\w:]+)="(.+?)"/', $match[2], $attr, PREG_SET_ORDER);

        // Set them as @attribute-name.
        foreach ($attr as $match_attr)
          $el['@' . $match_attr[1]] = $match_attr[2];
      }
    }

    // Return the parsed array.
    return $current;
  }

  // Get a specific element's xml. (privately used...)
  function _xml($array, $indent)
  {
    $indentation = $indent !== null ? '' . str_repeat('	', $indent) : '';

    // This is a set of elements, with no name...
    if (is_array($array) && !isset($array['name']))
    {
      $temp = '';
      foreach ($array as $val)
        $temp .= $this->_xml($val, $indent);
      return $temp;
    }

    // This is just text!
    if ($array['name'] == '!')
      return $indentation . '<![CDATA[' . $array['value'] . ']]>';
    else if (substr($array['name'], -2) == '[]')
      $array['name'] = substr($array['name'], 0, -2);

    // Start the element.
    $output = $indentation . '<' . $array['name'];

    $inside_elements = false;
    $output_el = '';

    // Run through and recurively output all the elements or attrbutes inside this.
    foreach ($array as $k => $v)
    {
      if (substr($k, 0, 1) == '@')
        $output .= ' ' . substr($k, 1) . '="' . $v . '"';
      else if (is_array($v))
      {
        $output_el .= $this->_xml($v, $indent === null ? null : $indent + 1);
        $inside_elements = true;
      }
    }

    // Indent, if necessary.... then close the tag.
    if ($inside_elements)
      $output .= '>' . $output_el . $indentation . '</' . $array['name'] . '>';
    else
      $output .= ' />';

    return $output;
  }

  // Return an element as an array...
  function _array($array)
  {
    $return = array();
    $text = '';
    foreach ($array as $value)
    {
      if (!is_array($value) || !isset($value['name']))
        continue;

      if ($value['name'] == '!')
        $text .= $value['value'];
      else
        $return[$value['name']] = $this->_array($value);
    }

    if (empty($return))
      return $text;
    else
      return $return;
  }

  // Parse out CDATA tags. (htmlspecialchars them...)
  function _to_cdata($data)
  {
    $inCdata = $inComment = false;
    $output = '';

    $parts = preg_split('~(<!\[CDATA\[|\]\]>|<!--|-->)~', $data, -1, PREG_SPLIT_DELIM_CAPTURE);
    foreach ($parts as $part)
    {
      // Handle XML comments.
      if (!$inCdata && $part === '<!--')
        $inComment = true;
      if ($inComment && $part === '-->')
        $inComment = false;
      else if ($inComment)
        continue;

      // Handle Cdata blocks.
      else if (!$inComment && $part === '<![CDATA[')
        $inCdata = true;
      else if ($inCdata && $part === ']]>')
        $inCdata = false;
      else if ($inCdata)
        $output .= htmlentities($part, ENT_QUOTES);

      // Everything else is kept as is.
      else
        $output .= $part;
    }

    return $output;
  }

  // Turn the CDATAs back to normal text.
  function _from_cdata($data)
  {
    // Get the HTML translation table and reverse it.
    $trans_tbl = array_flip(get_html_translation_table(HTML_ENTITIES, ENT_QUOTES));

    // Translate all the entities out.
    $data = strtr(preg_replace('~&#(\d{1,4});~e', "chr('\$1')", $data), $trans_tbl);

    return $this->trim ? trim($data) : $data;
  }

  // Given an array, return the text from that array. (recursive and privately used.)
  function _fetch($array)
  {
    // Don't return anything if this is just a string.
    if (is_string($array))
      return '';

    $temp = '';
    foreach ($array as $text)
    {
      // This means it's most likely an attribute or the name itself.
      if (!isset($text['name']))
        continue;

      // This is text!
      if ($text['name'] == '!')
        $temp .= $text['value'];
      // Another element - dive in ;).
      else
        $temp .= $this->_fetch($text);
    }

    // Return all the bits and pieces we've put together.
    return $temp;
  }

  // Get a specific array by path, one level down. (privately used...)
  function _path($array, $path, $level, $no_error = false)
  {
    // Is $array even an array?  It might be false!
    if (!is_array($array))
      return false;

    // Asking for *no* path?
    if ($path == '' || $path == '.')
      return $array;
    $paths = explode('|', $path);

    // A * means all elements of any name.
    $show_all = in_array('*', $paths);

    $results = array();

    // Check each element.
    foreach ($array as $value)
    {
      if (!is_array($value) || $value['name'] === '!')
        continue;

      if ($show_all || in_array($value['name'], $paths))
      {
        // Skip elements before "the one".
        if ($level !== null && $level > 0)
          $level--;
        else
          $results[] = $value;
      }
    }

    // No results found...
    if (empty($results))
    {
      if (function_exists('debug_backtrace'))
      {
        $trace = debug_backtrace();
        $i = 0;
        while ($i < count($trace) && isset($trace[$i]['class']) && $trace[$i]['class'] == get_class($this))
          $i++;
        $debug = ' from ' . $trace[$i - 1]['file'] . ' on line ' . $trace[$i - 1]['line'];
      }
      else
        $debug = '';

      // Cause an error.
      if ($this->debug_level & E_NOTICE && !$no_error)
        trigger_error('Undefined XML element: ' . $path . $debug, E_USER_NOTICE);
      return false;
    }
    // Only one result.
    else if (count($results) == 1 || $level !== null)
      return $results[0];
    // Return the result set.
    else
      return $results + array('name' => $path . '[]');
  }
}

// http://www.faqs.org/rfcs/rfc959.html
class ftp_connection
{
  var $connection = 'no_connection', $error = false, $last_message, $pasv = array();

  // Create a new FTP connection...
  function ftp_connection($ftp_server, $ftp_port = 21, $ftp_user = 'anonymous', $ftp_pass = 'ftpclient@simplemachines.org')
  {
    if ($ftp_server !== null)
      $this->connect($ftp_server, $ftp_port, $ftp_user, $ftp_pass);
  }

  function connect($ftp_server, $ftp_port = 21, $ftp_user = 'anonymous', $ftp_pass = 'ftpclient@simplemachines.org')
  {
    if (substr($ftp_server, 0, 6) == 'ftp://')
      $ftp_server = substr($ftp_server, 6);
    else if (substr($ftp_server, 0, 7) == 'ftps://')
      $ftp_server = 'ssl://' . substr($ftp_server, 7);
    if (substr($ftp_server, 0, 7) == 'http://')
      $ftp_server = substr($ftp_server, 7);
    $ftp_server = strtr($ftp_server, array('/' => '', ':' => '', '@' => ''));

    // Connect to the FTP server.
    $this->connection = @fsockopen($ftp_server, $ftp_port, $err, $err, 5);
    if (!$this->connection)
    {
      $this->error = 'bad_server';
      return;
    }

    // Get the welcome message...
    if (!$this->check_response(220))
    {
      $this->error = 'bad_response';
      return;
    }

    // Send the username, it should ask for a password.
    fwrite($this->connection, 'USER ' . $ftp_user . "\r\n");
    if (!$this->check_response(331))
    {
      $this->error = 'bad_username';
      return;
    }

    // Now send the password... and hope it goes okay.
    fwrite($this->connection, 'PASS ' . $ftp_pass . "\r\n");
    if (!$this->check_response(230))
    {
      $this->error = 'bad_password';
      return;
    }
  }

  function chdir($ftp_path)
  {
    if (!is_resource($this->connection))
      return false;

    // No slash on the end, please...
    if ($ftp_path !== '/' && substr($ftp_path, -1) === '/')
      $ftp_path = substr($ftp_path, 0, -1);

    fwrite($this->connection, 'CWD ' . $ftp_path . "\r\n");
    if (!$this->check_response(250))
    {
      $this->error = 'bad_path';
      return false;
    }

    return true;
  }

  function chmod($ftp_file, $chmod)
  {
    if (!is_resource($this->connection))
      return false;

    if ($ftp_file == '')
      $ftp_file = '.';

    // Convert the chmod value from octal (0777) to text ("777").
    fwrite($this->connection, 'SITE CHMOD ' . decoct($chmod) . ' ' . $ftp_file . "\r\n");
    if (!$this->check_response(200))
    {
      $this->error = 'bad_file';
      return false;
    }

    return true;
  }

  function unlink($ftp_file)
  {
    // We are actually connected, right?
    if (!is_resource($this->connection))
      return false;

    // Delete file X.
    fwrite($this->connection, 'DELE ' . $ftp_file . "\r\n");
    if (!$this->check_response(250))
    {
      fwrite($this->connection, 'RMD ' . $ftp_file . "\r\n");

      // Still no love?
      if (!$this->check_response(250))
      {
        $this->error = 'bad_file';
        return false;
      }
    }

    return true;
  }

  function check_response($desired)
  {
    // Wait for a response that isn't continued with -, but don't wait too long.
    $time = time();
    do
      $this->last_message = fgets($this->connection, 1024);
    while (substr($this->last_message, 3, 1) != ' ' && time() - $time < 5);

    // Was the desired response returned?
    return is_array($desired) ? in_array(substr($this->last_message, 0, 3), $desired) : substr($this->last_message, 0, 3) == $desired;
  }

  function passive()
  {
    // We can't create a passive data connection without a primary one first being there.
    if (!is_resource($this->connection))
      return false;

    // Request a passive connection - this means, we'll talk to you, you don't talk to us.
    @fwrite($this->connection, "PASV\r\n");
    $time = time();
    do
      $response = fgets($this->connection, 1024);
    while (substr($response, 3, 1) != ' ' && time() - $time < 5);

    // If it's not 227, we weren't given an IP and port, which means it failed.
    if (substr($response, 0, 4) != '227 ')
    {
      $this->error = 'bad_response';
      return false;
    }

    // Snatch the IP and port information, or die horribly trying...
    if (preg_match('~\((\d+),\s*(\d+),\s*(\d+),\s*(\d+),\s*(\d+)(?:,\s*(\d+))\)~', $response, $match) == 0)
    {
      $this->error = 'bad_response';
      return false;
    }

    // This is pretty simple - store it for later use ;).
    $this->pasv = array('ip' => $match[1] . '.' . $match[2] . '.' . $match[3] . '.' . $match[4], 'port' => $match[5] * 256 + $match[6]);

    return true;
  }

  function create_file($ftp_file)
  {
    // First, we have to be connected... very important.
    if (!is_resource($this->connection))
      return false;

    // I'd like one passive mode, please!
    if (!$this->passive())
      return false;

    // Seems logical enough, so far...
    fwrite($this->connection, 'STOR ' . $ftp_file . "\r\n");

    // Okay, now we connect to the data port.  If it doesn't work out, it's probably "file already exists", etc.
    $fp = @fsockopen($this->pasv['ip'], $this->pasv['port'], $err, $err, 5);
    if (!$fp || !$this->check_response(150))
    {
      $this->error = 'bad_file';
      @fclose($fp);
      return false;
    }

    // This may look strange, but we're just closing it to indicate a zero-byte upload.
    fclose($fp);
    if (!$this->check_response(226))
    {
      $this->error = 'bad_response';
      return false;
    }

    return true;
  }

  function list_dir($ftp_path = '', $search = false)
  {
    // Are we even connected...?
    if (!is_resource($this->connection))
      return false;

    // Passive... non-agressive...
    if (!$this->passive())
      return false;

    // Get the listing!
    fwrite($this->connection, 'LIST -1' . ($search ? 'R' : '') . ($ftp_path == '' ? '' : ' ' . $ftp_path) . "\r\n");

    // Connect, assuming we've got a connection.
    $fp = @fsockopen($this->pasv['ip'], $this->pasv['port'], $err, $err, 5);
    if (!$fp || !$this->check_response(array(150, 125)))
    {
      $this->error = 'bad_response';
      @fclose($fp);
      return false;
    }

    // Read in the file listing.
    $data = '';
    while (!feof($fp))
      $data .= fread($fp, 4096);;
    fclose($fp);

    // Everything go okay?
    if (!$this->check_response(226))
    {
      $this->error = 'bad_response';
      return false;
    }

    return $data;
  }

  function locate($file, $listing = null)
  {
    if ($listing === null)
      $listing = $this->list_dir('', true);
    $listing = explode("\n", $listing);

    @fwrite($this->connection, "PWD\r\n");
    $time = time();
    do
      $response = fgets($this->connection, 1024);
    while (substr($response, 3, 1) != ' ' && time() - $time < 5);

    // Check for 257!
    if (preg_match('~^257 "(.+?)" ~', $response, $match) != 0)
      $current_dir = strtr($match[1], array('""' => '"'));
    else
      $current_dir = '';

    for ($i = 0, $n = count($listing); $i < $n; $i++)
    {
      if (trim($listing[$i]) == '' && isset($listing[$i + 1]))
      {
        $current_dir = substr(trim($listing[++$i]), 0, -1);
        $i++;
      }

      // Okay, this file's name is:
      $listing[$i] = $current_dir . '/' . trim(strlen($listing[$i]) > 30 ? strrchr($listing[$i], ' ') : $listing[$i]);

      if (substr($file, 0, 1) == '*' && substr($listing[$i], -(strlen($file) - 1)) == substr($file, 1))
        return $listing[$i];
      if (substr($file, -1) == '*' && substr($listing[$i], 0, strlen($file) - 1) == substr($file, 0, -1))
        return $listing[$i];
      if (basename($listing[$i]) == $file || $listing[$i] == $file)
        return $listing[$i];
    }

    return false;
  }

  function create_dir($ftp_dir)
  {
    // We must be connected to the server to do something.
    if (!is_resource($this->connection))
      return false;

    // Make this new beautiful directory!
    fwrite($this->connection, 'MKD ' . $ftp_dir . "\r\n");
    if (!$this->check_response(257))
    {
      $this->error = 'bad_file';
      return false;
    }

    return true;
  }

  function detect_path($filesystem_path, $lookup_file = null)
  {
    $username = '';

    if (isset($_SERVER['DOCUMENT_ROOT']))
    {
      if (preg_match('~^/home[2]?/([^/]+?)/public_html~', $_SERVER['DOCUMENT_ROOT'], $match))
      {
        $username = $match[1];

        $path = strtr($_SERVER['DOCUMENT_ROOT'], array('/home/' . $match[1] . '/' => '', '/home2/' . $match[1] . '/' => ''));

        if (substr($path, -1) == '/')
          $path = substr($path, 0, -1);

        if (strlen(dirname($_SERVER['PHP_SELF'])) > 1)
          $path .= dirname($_SERVER['PHP_SELF']);
      }
      else if (substr($filesystem_path, 0, 9) == '/var/www/')
        $path = substr($filesystem_path, 8);
      else
        $path = strtr(strtr($filesystem_path, array('\\' => '/')), array($_SERVER['DOCUMENT_ROOT'] => ''));
    }
    else
      $path = '';

    if (is_resource($this->connection) && $this->list_dir($path) == '')
    {
      $data = $this->list_dir('', true);

      if ($lookup_file === null)
        $lookup_file = $_SERVER['PHP_SELF'];

      $found_path = dirname($this->locate('*' . basename(dirname($lookup_file)) . '/' . basename($lookup_file), $data));
      if ($found_path == false)
        $found_path = dirname($this->locate(basename($lookup_file)));
      if ($found_path != false)
        $path = $found_path;
    }
    else if (is_resource($this->connection))
      $found_path = true;

    return array($username, $path, isset($found_path));
  }

  function close()
  {
    // Goodbye!
    fwrite($this->connection, "QUIT\r\n");
    fclose($this->connection);

    return true;
  }
}

// crc32 doesn't work as expected on 64-bit functions - make our own.
// http://www.php.net/crc32#79567
if (!function_exists('smf_crc32'))
{
  function smf_crc32($number)
  {
    $crc = crc32($number);
  
    if ($crc & 0x80000000) {
      $crc ^= 0xffffffff;
      $crc += 1;
      $crc = -$crc;
    }
  
    return $crc;
  } 
}

?>