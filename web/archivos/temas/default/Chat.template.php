<?php
// Version: 1.1; Chat

function template_manual_above(){}
function template_manual_below(){}
function template_manual_intro()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings, $chatid;

	echo '<div><div class="box_buscador"><div class="box_title" style="width:920px;"><div class="box_txt box_buscadort">', $txt['chat'] ,'</div><div class="box_rss"><img alt="" src="', $settings['images_url']. '/blank.gif" style="width:14px;height:12px;" border="0" /></div></div><div style="width: 912px;padding:4px;" class="windowbg" border="0">
<embed src="http://www.xatech.com/web_gear/chat/chat.swf" quality="high" width="100%" height="500" name="chat" FlashVars="id=' . $chatid . '&rl=SpanishArgentina" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://xat.com/update_flash.shtml"  />
<br /><br /></div></div>
<div class="box_buscador"><div class="box_title" style="width: 920px;"><div class="box_txt box_buscadort">', $txt['aclaracion'] ,'</div><div class="box_rss"><img alt="" src="', $settings['images_url']. '/blank.gif" style="width:14px;height:12px;" border="0" /></div></div><div style="width: 912px;padding:4px;text-align:left;" class="windowbg" border="0"><font class="size12">
', $txt['rules'] ,'
</font></div></div>';
}
?>