<?php
@require_once('SSI.php');

function template_main()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $user_info, $db_prefix, $query_this_board, $func, $board;

echo '
<form action="', $scripturl, '?action=publicity;m=guardar" method="post" accept-charset="', $context['character_set'], '" name="guardar" id="guardar"><div class="box_745" style="float:left;"><div class="box_title" style="width: 745px;"><div style="text-align:center;" class="box_txt"><center>' . $context['page_title'] . '</center></div><div class="box_rss"><img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width:16px;height:16px;" border="0" /></div></div>
<div class="windowbg" style="width: 737px; padding: 4px;">
<table width="100%" style="padding:4px;border:none;">
';
echo '
<tr>
<td>Horizontal (728x90)</td><td><textarea name="horizontal" style="width:100%; height: 200px;">' . $modSettings['horizontal'] . '</textarea></td>
</tr>
';
echo '
<tr>
<td>Horizontal 2 (468x60)</td><td><textarea name="horizontal2" style="width:100%; height: 200px;">' . $modSettings['horizontal2'] . '</textarea></td>
</tr>
';
echo '
<tr>
<td>Vertical (120x600)</td><td><textarea name="vertical" style="width:100%; height: 200px;">' . $modSettings['vertical'] . '</textarea></td>
</tr>
';
echo '
<tr>
<td>Vertical 2 (160x600)</td><td><textarea name="vertical2" style="width:100%; height: 200px;">' . $modSettings['vertical2'] . '</textarea></td>
</tr>
';
echo '
<tr>
<td>Destacados 1 (300x250)</td><td><textarea name="Highlights1" style="width:100%; height: 200px;">' . $modSettings['Highlights1'] . '</textarea></td>
</tr>
';
echo '
<tr>
<td>Destacados 2 (300x250)</td><td><textarea name="Highlights2" style="width:100%; height: 200px;">' . $modSettings['Highlights2'] . '</textarea></td>
</tr>
';
echo '
<tr>
<td><input type="submit" name="Guardar Publicidad" value="Guardar Publicidad" class="login" /></td>
</tr>';
echo '</table></div>';
echo '</div></form><div style="clear:both"></div>';

}

function template_guardar()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $user_info, $db_prefix, $query_this_board, $func, $board;

$Horizontal		=	$_POST['horizontal'];
$Horizontal2		=	$_POST['horizontal2'];
$Vertical		=	$_POST['vertical'];
$Vertical2		=	$_POST['vertical'];
$Highlights1	=	$_POST['Highlights1'];
$Highlights2	=	$_POST['Highlights2'];

mysql_query("UPDATE {$db_prefix}settings SET value = '$Horizontal' WHERE variable = 'horizontal' ");
mysql_query("UPDATE {$db_prefix}settings SET value = '$Horizontal2' WHERE variable = 'horizontal2' ");
mysql_query("UPDATE {$db_prefix}settings SET value = '$Vertical' WHERE variable = 'vertical' ");
mysql_query("UPDATE {$db_prefix}settings SET value = '$Vertical2' WHERE variable = 'vertical2' ");
mysql_query("UPDATE {$db_prefix}settings SET value = '$Highlights1' WHERE variable = 'Highlights1' ");
mysql_query("UPDATE {$db_prefix}settings SET value = '$Highlights2' WHERE variable = 'Highlights2' ");
redirectexit('action=publicity');
}

?>