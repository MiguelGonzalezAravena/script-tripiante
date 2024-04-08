<?php
function showCodeImage($code) {
  global $settings, $user_info, $modSettings;

  // What type are we going to be doing?
  $imageType = empty($modSettings['disable_visual_verification']) ? 0 : $modSettings['disable_visual_verification'];

  // Special case to allow the admin center to show samples.
  if ($user_info['is_admin'] && isset($_GET['type']))
    $imageType = (int) $_GET['type'];
  // Just incase PM is on, reg is off.
  else if ($imageType == 1)
    $imageType = 0;

  // Some quick references for what we do.
  // Do we show no, low or high noise?
  $noiseType = $imageType == 0 ? 'low' : ($imageType == 4 ? 'high' : 'none');
  // Can we have more than one font in use?
  $varyFonts = $imageType == 4 ? true : false;
  // Just a plain white background?
  $simpleBGColor = $imageType != 4 ? true : false;
  // Plain black foreground?
  $simpleFGColor = $imageType == 1 ? true : false;
  // High much to rotate each character.
  $rotationType = $imageType == 2 ? 'none' : ($imageType != 4 ? 'high' : 'low');
  // Do we show some characters inversed?
  $showReverseChars = $imageType == 4 ? true : false;
  // Special case for not showing any characters.
  $disableChars = $imageType == 1 ? true : false;
  // What do we do with the font colours. Are they one color, close to one color or random?
  $fontColorType = $imageType == 2 ? 'plain' : ($imageType == 4 ? 'random' : 'cyclic');
  // Are the fonts random sizes?
  $fontSizeRandom = $imageType == 4 ? true : false;
  // How much space between characters?
  $fontHorSpace = $imageType == 4 ? 'high' : ($imageType == 2 ? 'medium' : 'minus');
  // Where do characters sit on the image? (Fixed position or random/very random)
  $fontVerPos = $imageType == 2 ? 'fixed' : ($imageType == 4 ? 'vrandom' : 'random');
  // Make font semi-transparent?
  $fontTrans = $imageType == 3 || $imageType == 0 ? true : false;
  // Give the image a border?
  $hasBorder = $simpleBGColor;

  // Is this GD2? Needed for pixel size.
  $testGD = get_extension_funcs('gd');
  $gd2 = in_array('imagecreatefrompng', $testGD) && function_exists('imagecreatefrompng');
  unset($testGD);

  // The amount of pixels inbetween characters.
  $character_spacing = 1;

  // What color is the background - generally white unless we're on "hard".
  if ($simpleBGColor)
    $background_color = array(255, 255, 255);
  else
    $background_color = isset($settings['verification_background']) ? $settings['verification_background'] : array(236, 237, 243);

  // The color of the characters shown (red, green, blue).
  if ($simpleFGColor)
    $foreground_color = array(255, 234, 0);
  else {
    $foreground_color = array(255, 234, 0);

    // Has the theme author requested a custom color?
    if (isset($settings['verification_foreground']))
      $foreground_color = $settings['verification_foreground'];
  }

  if (!is_dir($settings['default_theme_dir'] . '/fonts'))
    return false;

  // Get a list of the available fonts.
  $font_dir = dir($settings['default_theme_dir'] . '/fonts');
  $font_list = array();
  $ttfont_list = array();

  while ($entry = $font_dir->read()) {
    if (preg_match('~^(.+)\.gdf$~', $entry, $matches) === 1)
      $font_list[] = $entry;
    else if (preg_match('~^(.+)\.ttf$~', $entry, $matches) === 1)
      $ttfont_list[] = $entry;
  }

  if (empty($font_list))
    return false;

  // For non-hard things don't even change fonts.
  if (!$varyFonts) {
    $font_list = array($font_list[0]);

    // Try use Screenge if we can - it looks good!
    if (in_array('Screenge.ttf', $ttfont_list))
      $ttfont_list = array('Screenge.ttf');
    else
      $ttfont_list = empty($ttfont_list) ? array() : array($ttfont_list[0]);
  }

  // Create a list of characters to be shown.
  $characters = array();
  $loaded_fonts = array();

  for ($i = 0; $i < strlen($code); $i++) {
    $characters[$i] = array(
      'id' => $code{$i},
      'font' => array_rand($font_list),
    );

    $loaded_fonts[$characters[$i]['font']] = null;
  }

  // Load all fonts and determine the maximum font height.
  foreach ($loaded_fonts as $font_index => $dummy)
    $loaded_fonts[$font_index] = imageloadfont($settings['default_theme_dir'] . '/fonts/' . $font_list[$font_index]);

  // Determine the dimensions of each character.
  $total_width = $character_spacing * strlen($code) + 20;
  $max_height = 0;

  foreach ($characters as $char_index => $character) {
    $characters[$char_index]['width'] = imagefontwidth($loaded_fonts[$character['font']]);
    $characters[$char_index]['height'] = imagefontheight($loaded_fonts[$character['font']]);

    $max_height = max($characters[$char_index]['height'] + 5, $max_height);
    $total_width += $characters[$char_index]['width'];
  }

  // Create an image.
  $code_image = $gd2 ? imagecreatetruecolor($total_width, $max_height) : imagecreate($total_width, $max_height);

  // Draw the background.
  $bg_color = imagecolorallocate($code_image, $background_color[0], $background_color[1], $background_color[2]);
  imagefilledrectangle($code_image, 0, 0, $total_width - 1, $max_height - 1, $bg_color);

  // Randomize the foreground color a little.
  for ($i = 0; $i < 3; $i++)
    $foreground_color[$i] = mt_rand(max($foreground_color[$i] - 3, 0), min($foreground_color[$i] + 3, 255));

  $fg_color = imagecolorallocate($code_image, $foreground_color[0], $foreground_color[1], $foreground_color[2]);

  // Color for the dots.
  for ($i = 0; $i < 3; $i++)
    $dotbgcolor[$i] = $background_color[$i] < $foreground_color[$i] ? mt_rand(0, max($foreground_color[$i] - 20, 0)) : mt_rand(min($foreground_color[$i] + 20, 255), 255);

  $randomness_color = imagecolorallocate($code_image, $dotbgcolor[0], $dotbgcolor[1], $dotbgcolor[2]);

  // Fill in the characters.
  if (!$disableChars) {
    $cur_x = 0;

    foreach ($characters as $char_index => $character) {
      // Can we use true type fonts?
      $can_do_ttf = function_exists('imagettftext');

      // How much rotation will we give?
      if ($rotationType == 'none')
        $angle = 0;
      else
        $angle = mt_rand(-100, 100) / ($rotationType == 'high' ? 6 : 10);

      // What colour shall we do it?
      if ($fontColorType == 'cyclic') {
        // Here we'll pick from a set of acceptance types.
        $colours = array(
          array(0, 0, 0),
          array(0, 0, 0),
          array(0, 0, 0),
          array(0, 0, 0),
          array(0, 0, 0),
          array(0, 0, 0),
        );

        if (!isset($last_index))
          $last_index = -1;

        $new_index = $last_index;

        while ($last_index == $new_index)
          $new_index = mt_rand(0, count($colours) - 1);

        $char_fg_color = $colours[$new_index];
        $last_index = $new_index;
      }
      else if ($fontColorType == 'random')
        $char_fg_color = array(mt_rand(max($foreground_color[0] - 2, 0), $foreground_color[0]), mt_rand(max($foreground_color[1] - 2, 0), $foreground_color[1]), mt_rand(max($foreground_color[2] - 2, 0), $foreground_color[2]));
      else
        $char_fg_color = array($foreground_color[0], $foreground_color[1], $foreground_color[2]);

      if (!empty($can_do_ttf)) {
        // GD2 handles font size differently.
        if ($fontSizeRandom)
          $font_size = $gd2 ? mt_rand(17, 19) : mt_rand(18, 25);
        else
          $font_size = $gd2 ? 18 : 24;
  
        // Work out the sizes - also fix the character width cause TTF not quite so wide!
        $font_x = $fontHorSpace == 'minus' && $cur_x > 0 ? $cur_x - 3 : $cur_x + 5;
        $font_y = $max_height - ($fontVerPos == 'vrandom' ? mt_rand(2, 8) : ($fontVerPos == 'random' ? mt_rand(3, 5) : 5));
  
        // What font face?
        if (!empty($ttfont_list))
          $fontface = $settings['default_theme_dir'] . '/fonts/' . $ttfont_list[mt_rand(0, count($ttfont_list) - 1)];
  
        // What color are we to do it in?
        $is_reverse = $showReverseChars ? mt_rand(0, 1) : false;
        $char_color = function_exists('imagecolorallocatealpha') && $fontTrans ? imagecolorallocatealpha($code_image, $char_fg_color[0], $char_fg_color[1], $char_fg_color[2], 50) : imagecolorallocate($code_image, $char_fg_color[0], $char_fg_color[1], $char_fg_color[2]);
  
        $fontcord = @imagettftext($code_image, $font_size, $angle, $font_x, $font_y, $char_color, $fontface, $character['id']);
        if (empty($fontcord))
          $can_do_ttf = false;
        else if ($is_reverse) {
          imagefilledpolygon($code_image, $fontcord, 4, $fg_color);

          // Put the character back!
          imagettftext($code_image, $font_size, $angle, $font_x, $font_y, $randomness_color, $fontface, $character['id']);
        }
  
        if ($can_do_ttf)
          $cur_x = max($fontcord[2], $fontcord[4]) + ($angle == 0 ? 0 : 3);
      }

      if (!$can_do_ttf) {
        // Rotating the characters a little...
        if (function_exists('imagerotate')) {
          $char_image = function_exists('imagecreatetruecolor') ? imagecreatetruecolor($character['width'], $character['height']) : imagecreate($character['width'], $character['height']);
          $char_bgcolor = imagecolorallocate($char_image, $background_color[0], $background_color[1], $background_color[2]);

          imagefilledrectangle($char_image, 0, 0, $character['width'] - 1, $character['height'] - 1, $char_bgcolor);
          imagechar($char_image, $loaded_fonts[$character['font']], 0, 0, $character['id'], imagecolorallocate($char_image, $char_fg_color[0], $char_fg_color[1], $char_fg_color[2]));

          $rotated_char = imagerotate($char_image, mt_rand(-100, 100) / 10, $char_bgcolor);

          imagecopy($code_image, $rotated_char, $cur_x, 0, 0, 0, $character['width'], $character['height']);
          imagedestroy($rotated_char);
          imagedestroy($char_image);
        }
    
        // Sorry, no rotation available.
        else
          imagechar($code_image, $loaded_fonts[$character['font']], $cur_x, floor(($max_height - $character['height']) / 2), $character['id'], imagecolorallocate($code_image, $char_fg_color[0], $char_fg_color[1], $char_fg_color[2]));

        $cur_x += $character['width'] + $character_spacing;
      }
    }
  }
  // If disabled just show a cross.
  else {
    imageline($code_image, 0, 0, $total_width, $max_height, $fg_color);
    imageline($code_image, 0, $max_height, $total_width, 0, $fg_color);
  }

  // Make the background color transparent on the hard image.
  if (!$simpleBGColor)
    imagecolortransparent($code_image, $bg_color);
  if ($hasBorder)
    imagerectangle($code_image, 0, 0, $total_width - 1, $max_height - 1, $fg_color);

  // Add some noise to the background?
  if ($noiseType != 'none') {
    for ($i = mt_rand(0, 2); $i < $max_height; $i += mt_rand(1, 2))
      for ($j = mt_rand(0, 10); $j < $total_width; $j += mt_rand(1, 15))
        imagesetpixel($code_image, $j, $i, mt_rand(0, 1) ? $fg_color : $randomness_color);

    // Put in some lines too?
    if ($noiseType == 'high') {
      $num_lines = 2;
      for ($i = 0; $i < $num_lines; $i++) {
        if (mt_rand(0, 1)) {
          $x1 = mt_rand(0, $total_width);
          $x2 = mt_rand(0, $total_width);
          $y1 = 0;
          $y2 = $max_height;
        } else {
          $y1 = mt_rand(0, $max_height);
          $y2 = mt_rand(0, $max_height);
          $x1 = 0;
          $x2 = $total_width;
        }

        imageline($code_image, $x1, $y1, $x2, $y2, mt_rand(0, 1) ? $fg_color : $randomness_color);
      }
    }
  }

  // Show the image.
  if (function_exists('imagegif')) {
    header('Content-type: image/gif');
    imagegif ($code_image);
  } else {
    header('Content-type: image/png');
    imagepng($code_image);
  }

  // Bail out.
  imagedestroy($code_image);
  die();
}

return showCodeImage($_GET['id']);
?>