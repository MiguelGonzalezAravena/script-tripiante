<?php
function template_articlesmain()
{
	global $scripturl, $txt, $context, $subcats_linktree, $settings;

		// List all the catagories
		echo '
		<div class="box_buscador" style="margin-bottom:8px;">
<div class="box_title" style="width:922px;"><div class="box_txt box_buscadort">', $txt['smfarticles_indextitle'], '</div>

<div class="box_rss"><img alt="" src="', $settings['images_url'], '/espacio.gif" style="width:14px;height:12px;" border="0" /></div></div><div style="width:914px;padding:4px;" class="windowbg"><table align="center"><tr>';

$i= 0;
		foreach ($context['articles_cat'] as $row)
		{

			$totalarticles = GetArticleTotals($row['seotitle']);

	 if($i%3==0)
	 {
		 echo '</tr><tr><td style="padding-right:150px;"><img alt="" src="', $settings['images_url'], '/ayuda/carpeta.png" title="', $row['title'], '" /> <a href="/ayuda/categoria/', $row['seotitle'], '">', $row['title'], '</a> (', $totalarticles, ')</td>';
	 } else {
		 echo '<td style="padding-right:150px;"><img alt="" src="', $settings['images_url'], '/ayuda/carpeta.png" title="', $row['title'], '" /> <a href="/ayuda/categoria/', $row['seotitle'], '">', $row['title'], '</a> (', $totalarticles, ')</td>';
	 }
	  $i++;

					}

		echo '</tr></table></div></div>
<div class="box_460" style="float:left;margin-right:4px;"><div class="box_title" style="width: 456px;"><div class="box_txt box_460-34">5 articulos recientes</div><div class="box_rss"><img alt="" src="', $settings['images_url'], '/espacio.gif" style="width:14px;height:12px;" border="0" /></div></div>
<div style="width: 448px;padding:4px;" class="windowbg">';
$request	=	db_query("
SELECT * 
FROM {$db_prefix}articles
ORDER BY date DESC
LIMIT 5
", __FILE__, __LINE__);
while($row	=	mysql_fetch_assoc($request)) {
echo '<img alt="', $row['title'], '" src="', $settings['images_url'], '/ayuda/articulo.png" title="', $row['title'], '" />&nbsp;<a href="/ayuda/articulo/', $row['ID_ARTICLE'], '">', ssi_reducir($row['title']), '</a> (', timeformat($row['date']), ')<br />';
}

echo '</div></div>';
echo '<div style="float:left;" class="box_460"><div class="box_title" style="width: 460px;"><div class="box_txt box_460-34">5 articulos m&aacute;s populares (Por visitas)</div><div class="box_rss"><img alt="" src="', $settings['images_url'], '/espacio.gif" style="width:14px;height:12px;" border="0" /></div></div>
<div style="width: 452px;padding:4px;" class="windowbg">';
$request2	=	db_query("
SELECT * 
FROM {$db_prefix}articles
ORDER BY views DESC
LIMIT 5
", __FILE__, __LINE__);
while($row2	=	mysql_fetch_assoc($request2)) {
echo '<img alt="', $row2['title'], '" src="', $settings['images_url'], '/ayuda/articulo.png" title="', $row2['title'], '" />&nbsp;<a href="/ayuda/articulo/', $row2['ID_ARTICLE'], '">', ssi_reducir($row2['title']), '</a> (', $row2['views'], ' ', $txt['smfarticles_cviews'] ,')<br />';
}
echo '</div></div></div>';

		if ($context['m_cats'])
		{
			echo '	<table border="0" cellspacing="1" cellpadding="5" class="bordercolor" style="margin-top: 1px;" align="center" width="90%">
			<tr class="titlebg">
				<td align="center">', $txt['smfarticles_articlespanel'], '</td>
			</tr>
			<tr>
				<td class="windowbg2" align="center">
				<a href="', $scripturl, '?action=articles;sa=addcat">', $txt['smfarticles_addcat'], '</a>&nbsp;';
			
			if ($context['addarticle'])
				echo '<a href="', $scripturl, '?action=articles;sa=addarticle">', $txt['smfarticles_addarticle'], '</a>&nbsp;';

			echo '<a href="', $scripturl, '?action=articles;sa=admin">', $txt['smfarticles_articlessettings'], '</a>&nbsp;
			<a href="', $scripturl, '?action=articles;sa=adminperm">', $txt['edit_permissions'], '</a>
			<br />',
			$txt['smfarticles_thereare'], '<b>', $context['articlesapproval'], '</b>', $txt['smfarticles_waitingapproval'],' <a href="', $scripturl, '?action=articles;sa=alist">', $txt['smfarticles_articlecheckapproval'], '</a>
			<br />			
			</td>
			</tr>
			</table>
';
echo '<table><tr><td>';
		foreach ($context['articles_cat'] as $row)
		{
			if ($context['m_cats'])
			{
			echo '<div style=""><p>', $row['title'], ' <br /><a href="', $scripturl, '?action=articles;sa=catup;cat=', $row['ID_CAT'], '">', $txt['smfarticles_txtup'], '</a>&nbsp;<a href="', $scripturl, '?action=articles;sa=catdown;cat=', $row['ID_CAT'], '">', $txt['smfarticles_txtdown'], '</a>
				<a href="', $scripturl, '?action=articles;sa=editcat;cat=', $row['ID_CAT'], '">', $txt['smfarticles_txtedit'], '</a>&nbsp;<a href="', $scripturl, '?action=articles;sa=deletecat;cat=', $row['ID_CAT'], '">', $txt['smfarticles_txtdel'], '</a>
				<br />
				<a href="', $scripturl, '?action=articles;sa=catperm;cat=', $row['ID_CAT'], '">', $txt['smfarticles_txt_perm'], '</a>
				</p></div>';
				}
				}
				echo '</td></tr></table>';
		}

}

function template_articlelisting()
{
	global $txt, $scripturl, $context, $modSettings, $settings;
	
	ShowSubCats($context['articles_cat_id'],$context['m_cats']);
	
	echo '
<table width="921px" border="0" cellpadding="0" cellspacing="0"><tr><td valign="top"><div class="box_buscador">
<div class="box_title" style="width: 922px;"><div class="box_txt box_buscadort">', $context['articles_cat_title'], '</div>

<div class="box_rss"><img alt="" src="', $settings['images_url'] ,'/espacio.gif" style="width:14px;height:12px;" border="0" /></div></div><div style="width:914px;padding:4px;" class="windowbg">';

foreach($context['articles_listing'] as $row)
			{

				
				echo '<img alt="" src="', $settings['images_url'] ,'/ayuda/articulo.png" title="', $row['title'], '" />&nbsp;<a href="/ayuda/articulo/', $row['ID_ARTICLE'], '">', $row['title'], '</a>';
if($context['m_cats']) {
echo '&nbsp;<a href="' , $scripturl , '?action=articles;sa=editarticle&id=' , $row['ID_ARTICLE'] , '">', $txt['smfarticles_txtedit'] ,'</a>&nbsp;<a href="' , $scripturl , '?action=articles;sa=deletearticle&id=' , $row['ID_ARTICLE'] , '">', $txt['smfarticles_txtdel'] ,'</a>&nbsp;<a href="' , $scripturl , '?action=articles;sa=noapprove&id=' , $row['ID_ARTICLE'] , '">' , $txt['smfarticles_txtunapprove'] , '</a>';
}
echo '<br />';
			}
		

echo'<br /></div></div></td></tr></table></div>';

			// See if they are allowed to add articles
			if ($context['addarticle'])
				echo '<a href="' , $scripturl , '?action=articles;sa=addarticle;cat=' , $context['articles_cat_id'] , '">' , $txt['smfarticles_addarticle'] , '</a>&nbsp;<br /><br />';

}

function template_viewarticle()
{
	global $txt, $context, $user_info, $scripturl, $modSettings, $settings, $ID_MEMBER, $memberContext;
	
	$m_cats = $context['m_cats'];

	// Show the main article
	
if ($m_cats == true || $context['article']['ID_MEMBER'] == $ID_MEMBER)
echo '<table border="0" cellpadding="0" cellspacing="0" align="center" width="90%">
						<tr>
							<td style="padding-right: 1ex;" align="right" >
						<table cellpadding="0" cellspacing="0" align="left">
									<tr>
						', DoToolBarStrip($context['articles']['view_article'], 'bottom'), '
							</tr>
							</table>
						</td>
						</tr>
					</table>';

	echo '<table width="921px" border="0" cellpadding="0" cellspacing="0"><tr><td valign="top"><div class="box_buscador"><div class="box_title" style="width: 922px;"><div class="box_txt box_buscadort"><center>', $context['article']['title'], '</center></div>
<div class="box_rss"><img alt="" src="', $settings['images_url'], '/espacio.gif" style="width:14px;height:12px;" border="0" /></div></div><div style="width:914px;padding:4px;" class="windowbg"><div class="post-contenido" property="dc:content"><div align="center"><span style="font-family: Lucida Sans;">
			
		',parse_bbc($context['article_page']['pagetext']),'
		 
<br></span></div><hr class="divider"/>

<center><b style="color:green;">', $context['article']['views'], '  ', $txt['smfarticles_cviews'] ,' | ', timeformat($context['article']['date']), ' </b></center></div></div></div></td></tr></table></div>';
			
		
}

function template_addcat()
{
	global $scripturl, $txt, $context, $settings;

	echo '<center><div class="box_title" style="width:539px;"><div class="box_txt">' , $txt['smfarticles_addcat'] , '</div><div class="box_rss"><img alt="" src="/images/blank.gif" style="width: 14px; height: 12px;" border="0"></div></div>
<div class="windowbg" style="width:531px;padding:4px;"><form style="margin: 0px; padding: 0px;" action="' , $scripturl , '?action=articles;sa=addcat2"" method="POST" name="cateform" accept-charset="UTF-8"><table>
<tr><td style="width:100px;"><b>' , $txt['smfarticles_ctitle'] , '</b></td><td> <input type="text" name="title" size="64" maxlenght="100" onfocus="foco(this);" onblur="no_foco(this);" style="width:300px;" /> </td></tr>

<tr><td style="width:100px;"><b>' , $txt['smfarticles_parentcategory'] ,'</b></td><td>
<select name="parent">
	 <option value="0">' , $txt['smfarticles_text_catnone'] , '</option>
	 ';

	foreach ($context['articles_cat'] as $i => $category)
	{
		echo '<option value="' , $category['ID_CAT']  , '" ' , (($context['articles_parent']  == $category['ID_CAT']) ? ' selected="selected"' : '') ,'>' , $category['title'] , '</option>';
	}

	echo '</select>';
	
	echo '</table>
<p align="right" style="padding:0px;margin:0px;"><input type="submit" class="login" value="' , $txt['smfarticles_addcat'] , '" name="submit" /></p></form></center>';


}

function template_editcat()
{
	global $scripturl, $txt, $context, $settings;

echo '<center>
<div class="box_title" style="width:539px;"><div class="box_txt">' , $txt['smfarticles_editcat'] , '</div><div class="box_rss"><img alt="" src="/images/blank.gif" style="width: 14px; height: 12px;" border="0"></div></div>
<div class="windowbg" style="width:531px;padding:4px;">
<form style="margin: 0px; padding: 0px;" action="' , $scripturl , '?action=articles;sa=editcat2" method="POST" name="catform" accept-charset="UTF-8"><table>
<tr><td style="width:100px;"><b>' , $txt['smfarticles_ctitle'] , '</b></td><td> <input value="' , $context['articles_data']['title'] , '" type="text" name="title" onfocus="foco(this);" onblur="no_foco(this);" style="width:300px;" size="64" maxlength="100"/> </td></tr>

<tr><td style="width:100px;"><b>' , $txt['smfarticles_parentcategory'] ,'</b></td>
<td> <select name="parent"><option value="0">' , $txt['smfarticles_text_catnone'] , '</option>
';

	foreach ($context['articles_cat'] as $i => $category)
		echo '<option value="' , $category['ID_CAT']  , '" ' , (($context['articles_data']['ID_PARENT'] == $category['ID_CAT']) ? ' selected="selected"' : '') ,'>' , $category['title'] , '</option>';

	echo '</select>';

	echo '</table>
<input type="hidden" value="' , $context['articles_data']['ID_CAT'] , '" name="catid" />
<p align="right" style="padding:0px;margin:0px;"><input type="submit" class="login" value="' , $txt['smfarticles_editcat'] , '" name="submit" /></p></form></centery>
';

}

function template_deletecat()
{
	global $context, $scripturl, $txt;

	echo '<div class="tborder" >
	<form method="post" action="' , $scripturl , '?action=articles;sa=deletecat2">
<table border="1" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#FFFFFF" width="100%">
  <tr>
	 <td width="50%" colspan="2" align="center" class="catbg">
	 <b>' , $txt['smfarticles_deltcat'] , '</b></td>
  </tr>
  <tr>
	 <td width="28%" colspan="2" align="center" class="windowbg2">
	 <b>' , $txt['smfarticles_warndel2'] , '</b>
	 <br />
	 <input type="hidden" value="' , $context['arcticle_cat'], '" name="catid" />
	 <input type="submit" value="' , $txt['smfarticles_deltcat'] , '" name="submit" /></td>
  </tr>
</table>
</form></div>';


}

function template_addarticle()
{
	global $scripturl, $txt, $modSettings, $context, $settings;
	
	echo '
<form method="post" enctype="multipart/form-data" action="' , $scripturl , '?action=articles;sa=addarticle2" name="addarticle" id="addarticle">
<table  cellpadding="0" cellspacing="0" class="tborder" width="100%">
  <tr>
	 <td width="50%" colspan="2"  align="center" class="catbg">
	 <b>' , $txt['smfarticles_addarticle'] , '</b></td>
  </tr>
  <tr>
	 <td width="28%" class="windowbg2" align="right"><span class="gen"><b>' , $txt['smfarticles_ctitle'] , '</b></span></td>
	 <td width="72%" class="windowbg2"><input type="text" name="title" size="64" maxlength="100" /></td>
  </tr>
  <tr>
	 <td width="28%"  class="windowbg2" align="right"><span class="gen"><b>' , $txt['smfarticles_category'] , '</b></span></td>
	 <td width="72%"  class="windowbg2"><select name="catid">';
		foreach ($context['articles_cat'] as $row)
			  echo '<option value="' , $row['ID_CAT'] , '" ' , (($row['ID_CAT'] == $context['articles_catid']) ? 'selected="selected" ' : '') ,' >' , $row['title'] , '</option>';

echo '</select>
	 </td>
  </tr>
  <tr>
	 <td width="28%" valign="top" class="windowbg2" align="right"><span class="gen"><b>' , $txt['smfarticles_summary'] , '</b></span></td>
	 <td width="72%" class="windowbg2"><textarea style="height:300px;width:615px;" onfocus="foco(this);" onblur="no_foco(this);" id="markItUp" name="description" class="markItUpEditor"></textarea>
	 </td>
  </tr>
	  <tr>
	<td class="windowbg2" colspan="2" align="center">
	<hr />
	<b>',$txt['smfarticles_articletext'],'</b>
	</td>
	</tr>
	
	<tr>
	<td class="windowbg2" colspan="2" align="center">
	<table>
	';
	echo '</table>
	</td>
	</tr>';
	
	if ($modSettings['smfarticles_allow_attached_images'] > 0)
	{
			echo '<tr class="windowbg2">
				<td valign="top" align="right"><b>', $txt['smfarticles_txt_upload_image'], '</b></td>
				<td><input type="file" size="75" name="uploadimage" /><br /><br /></td>
			</tr>
			';
	}
	
	echo '
  <tr>
	 <td width="28%" colspan="2" align="center" class="windowbg2">';
	
	  if ($context['show_spellchecking'])
		echo '
									<input type="button" value="', $txt['spell_check'], '" tabindex="', $context['tabindex']++, '" onclick="spellCheck(\'addarticle\', \'message\');" />';

	
	echo '
	 <input type="submit" value="', $txt['smfarticles_addarticle'], '" name="submit" /></td>

  </tr>
</table>
</form>';
	
		// Some hidden information is needed in order to make the spell checking work.
	if ($context['show_spellchecking'])
		echo '
		<form name="spell_form" id="spell_form" method="post" accept-charset="', $context['character_set'], '" target="spellWindow" action="', $scripturl, '?action=spellcheck"><input type="hidden" name="spellstring" value="" /></form>';

}

function template_editarticle()
{
	global  $scripturl, $txt, $settings, $context, $modSettings;
	
	// Load the spell checker?
	if ($context['show_spellchecking'])
		echo '<script language="JavaScript" type="text/javascript" src="', $settings['default_theme_url'], '/spellcheck.js"></script>';


	echo '<div class="tborder">
<form method="post" enctype="multipart/form-data" action="' , $scripturl , '?action=articles;sa=editarticle2" name="editarticle" id="editarticle">
<table border="1" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#FFFFFF" width="100%">
  <tr>
	 <td width="50%" colspan="2"  align="center" class="catbg">
	 <b>' , $txt['smfarticles_editarticle'] , '</b></td>
  </tr>
  <tr>
	 <td width="28%" class="windowbg2" align="right"><span class="gen"><b>' , $txt['smfarticles_ctitle'] , '</b></span></td>
	 <td width="72%" class="windowbg2"><input type="text" name="title" size="64" maxlength="100" value="' , $context['article_data']['title'] , '" /></td>
  </tr>
  <tr>
	 <td width="28%"  class="windowbg2" align="right"><span class="gen"><b>' , $txt['smfarticles_category'] , '</b></span></td>
	 <td width="72%"  class="windowbg2"><select name="catid">';

		foreach($context['articles_cat'] as $row2)
			  echo '<option value="' , $row2['ID_CAT'] , '" ' , (($row2['ID_CAT'] == $context['article_data']['ID_CAT']) ? 'selected="selected" ' : '') ,'>' , $row2['title'] , '</option>';
	

echo '</select>
	 </td>
  </tr>

  <tr>
	 <td width="28%" valign="top" class="windowbg2" align="right"><span class="gen"><b>' , $txt['smfarticles_summary'] , '</b></span></td>
	 <td width="72%" class="windowbg2"><textarea rows="6" name="description" cols="54">' , $context['article_data']['description'] , '</textarea></td>
  </tr>
	 <tr>
	<td class="windowbg2" colspan="2" align="center">
	<hr />
	<b>',$txt['smfarticles_articletext'],'</b>
	</td>
	</tr>
	
	<tr>
	<td class="windowbg2" colspan="2" align="center">
	<table>
	';
	theme_postbox($context['article_page']['pagetext']);
	echo '</table>
	</td>
	</tr>';
	
	if ($modSettings['smfarticles_allow_attached_images'] > 0)
	{
			echo '<tr class="windowbg2">
				<td align="right" valign="top"><b>', $txt['smfarticles_txt_upload_image'], '</b></td>
				<td><input type="file" size="75" name="uploadimage" /><br /><br /></td>
			</tr>
			';
			
			echo '<tr class="windowbg2">
				<td colspan="2" align="center">
					<table align="center">';
							
					foreach($context['articles_images'] as $row)
					{
						echo '<tr>
							<td>
								<a href="javascript:void(0);" onclick="replaceText(\'[img]' .$modSettings['articles_url'] . $row['filename']  . '[/img]\', document.forms.editarticle.message); return false;"><img src="' .$modSettings['articles_url'] . $row['thumbnail']  . '" alt="" /></a>

							</td>
							<td>
								', round($row['filesize'] / 1024, 2) . 'kb
							</td>
							<td>
								<a href="' . $scripturl . '?action=articles;sa=delimage;id=' . $row['ID_FILE'] . '">' . $txt['smfarticles_txtdel'] . '</a>
							</td>
						</tr>';
						
					}
			
					echo '</table>
				</td>
				</tr>';
			
	}
	
	echo '
  <tr>
	 <td width="28%" colspan="2" align="center" class="windowbg2">';

	if ($context['show_spellchecking'])
		echo '
									<input type="button" value="', $txt['spell_check'], '" tabindex="', $context['tabindex']++, '" onclick="spellCheck(\'editarticle\', \'message\');" />';

		
echo '
	 <input type="hidden" value="' , $context['article_id'] , '" name="id" />
	 <input type="submit" value="' , $txt['smfarticles_editarticle'] , '" name="submit" /></td>

  </tr>
</table>
</form></div>';


		// Some hidden information is needed in order to make the spell checking work.
	if ($context['show_spellchecking'])
		echo '
		<form name="spell_form" id="spell_form" method="post" accept-charset="', $context['character_set'], '" target="spellWindow" action="', $scripturl, '?action=spellcheck"><input type="hidden" name="spellstring" value="" /></form>';

}
function template_deletearticle()
{
	global $scripturl, $txt, $context;

	echo '<div class="tborder" ><form method="post" action="' , $scripturl , '?action=articles;sa=deletearticle2">
<table border="1" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#FFFFFF" width="100%">
  <tr>
	 <td width="50%" colspan="2" align="center" class="catbg">
	 <b>' , $txt['smfarticles_delarticle'] , '</b></td>
  </tr>
  <tr>
	 <td width="28%" colspan="2"  align="center" class="windowbg2">"',$context['article_title'],'"<br />
	 <b>' , $txt['smfarticles_warndel'] , '</b>
	 <br />
	 <input type="hidden" value="' , $context['article_id'] , '" name="id" />
	 <input type="submit" value="' , $txt['smfarticles_delarticle'] , '" name="submit" /></td>
  </tr>
</table>
</form></div>';

}
function template_approvearticles()
{
	global $settings, $scripturl, $txt, $context;

	// Edit and Delete permissions
	$editarticle = $context['editarticle'];
	$deletearticle = $context['deletearticle'];

	
echo '
<table border="0" width="80%" cellspacing="0" align="center" cellpadding="4" class="tborder">
		<tr class="titlebg">
			<td>', $txt['smfarticles_approvearticles'], '</td>
		</tr>
		<tr class="windowbg">
			<td class="windowbg">
<table  class="bordercolor" cellpadding="0" cellspacing="0" width="100%">
  <tr>
	 <td class="catbg"><b>', $txt['smfarticles_ctitle'], '</b></td>
	 <td class="catbg"><b>', $txt['smfarticles_category'], '</b></td>
	 <td class="catbg"><b>', $txt['smfarticles_submittedby'], '</b></td>
	 <td class="catbg"><b>', $txt['smfarticles_options'], '</b></td>
  </tr>';

foreach($context['articles_list'] as $row)
{
  echo '<tr>
  <td class="windowbg2">' , $row['title'] , '</td>
  <td class="windowbg2">' , $row['catname'] , '</td>';

  if ($row['realName'] == '')
		echo '<td class="windowbg2">' , $txt['smfarticles_txtguest'] , '</td>';
  else
	  echo '<td class="windowbg2"><a href="' , $scripturl , '?action=profile;u=' , $row['ID_MEMBER'] , '">' , $row['realName'] , '</a></td>';


  echo '<td  class="windowbg2">
  <a href="' , $scripturl , '?action=articles;sa=approve&id=' , $row['ID_ARTICLE'] , '">' , $txt['smfarticles_txtapprove'] , '</a>&nbsp;';

  if ($editarticle)
		echo '<a href="' , $scripturl , '?action=articles;sa=editarticle&id=' , $row['ID_ARTICLE'] , '">' , $txt['smfarticles_txtedit'] , '</a>&nbsp;';
  if ($deletearticle)
		echo '<a href="' , $scripturl , '?action=articles;sa=deletearticle&id=' , $row['ID_ARTICLE'] , '">' , $txt['smfarticles_txtdel'] ,'</a>';

  echo '</td>
  </tr>';

 }

	 // Show the pages
	echo '<tr class="titlebg">
				<td align="left" colspan="4">', $txt['smfarticles_pages']
,$context['page_index'],
						
			'</td>
		</tr>
	</table>
	</td>
	</tr>
	</table>';


}

function template_settings()
{
	global $scripturl, $txt, $modSettings, $currentVersion;
	
echo '
<table border="0" width="80%" cellspacing="0" align="center" cellpadding="4" class="tborder">
		<tr class="titlebg">
			<td>', $txt['smfarticles_articlesconfig'], '</td>
		</tr>
		<tr class="windowbg">
			<td class="windowbg">
			<br />
			
			<b>' , $txt['smfarticles_articlesconfig'] , '</b><br />
			<form method="post" action="' , $scripturl , '?action=articles;sa=admin2">
			' , $txt['smfarticles_setarticlessperpage'] , '&nbsp;<input type="text" name="smfarticles_setarticlesperpage" value="' ,  $modSettings['smfarticles_setarticlesperpage'] , '" /><br />
			<input type="checkbox" name="smfarticles_countsubcats" ' , ($modSettings['smfarticles_countsubcats'] ? ' checked="checked" ' : '') , ' /> ' , $txt['smfarticles_countsubcats'] , '<br />
			
				<b>' , $txt['smfarticles_listingdisplay'] , '</b><br />

				<input type="checkbox" name="smfarticles_disp_views" ' , ($modSettings['smfarticles_disp_views'] ? ' checked="checked" ' : '') , ' /> ' , $txt['smfarticles_disp_views'] , '<br />
				<input type="checkbox" name="smfarticles_disp_membername" ' , ($modSettings['smfarticles_disp_membername'] ? ' checked="checked" ' : '') , ' /> ' , $txt['smfarticles_disp_membername'] , '<br />
				<input type="checkbox" name="smfarticles_disp_date" ' , ($modSettings['smfarticles_disp_date'] ? ' checked="checked" ' : '') , ' /> ' , $txt['smfarticles_disp_date'] , '<br />
				<br />
				
				
				<br />
				<input type="submit" name="savesettings" value="' , $txt['smfarticles_settings_save'] ,'" />
			</form>
			
			<br />
			<br />
			<form method="post" action="' , $scripturl , '?action=articles;sa=recount">
			<input type="submit" value="',$txt['smfarticles_txt_recount_article_totals'],'" />
			</form>
			
			
			
			</td>
		</tr>

				</table>';
}

function template_catperm()
{
	global $scripturl, $txt, $context;
	
	echo '

	<table border="0" width="80%" cellspacing="0" align="center" cellpadding="4" class="tborder">
		<tr class="titlebg">
			<td>' ,$txt['smfarticles_text_catperm'] , ' - ' , $context['articles_cat_name']  , '</td>
		</tr>
		<tr class="windowbg">
		<td>
		<form method="post" action="' , $scripturl , '?action=articles;sa=catperm2">
		<table align="center" class="tborder">
		<tr class="titlebg">
			<td colspan="2">'  , $txt['smfarticles_text_addperm'] , '</td>
		</tr>

			  <tr class="windowbg2">
				  <td align="right"><b>' , $txt['smfarticles_groupname'] , '</b>&nbsp;</td>
				  <td><select name="groupname">
								  <option value="-1">' , $txt['membergroups_guests'] , '</option>
								<option value="0">' , $txt['membergroups_members'] , '</option>';
								foreach ($context['groups'] as $group)
									echo '<option value="', $group['ID_GROUP'], '">', $group['groupName'], '</option>';

							echo '</select>
				</td>
			  </tr>
			  <tr class="windowbg2">
				  <td align="right"><input type="checkbox" name="view" checked="checked" /></td>
				  <td><b>' , $txt['smfarticles_perm_view'] ,'</b></td>
			  </tr>
			  <tr class="windowbg2">
				  <td align="right"><input type="checkbox" name="add" checked="checked" /></td>
				  <td><b>' , $txt['smfarticles_perm_add'] ,'</b></td>
			  </tr>
			  <tr class="windowbg2">
				  <td align="right"><input type="checkbox" name="edit" checked="checked" /></td>
				  <td><b>' , $txt['smfarticles_perm_edit'] ,'</b></td>
			  </tr>
			  <tr class="windowbg2">
				  <td align="right"><input type="checkbox" name="delete" checked="checked" /></td>
				  <td><b>' , $txt['smfarticles_perm_delete'] ,'</b></td>
			  </tr>

			  <tr class="windowbg2">
				  <td align="center" colspan="2">
				  <input type="hidden" name="cat" value="' , $context['articles_cat'] , '" />
				  <input type="submit" value="' , $txt['smfarticles_text_addperm'] , '" /></td>

			  </tr>
		</table>
		</form>
		</td>
		</tr>
			<tr class="windowbg">
			<td>
			<table cellspacing="0" cellpadding="10" border="0" align="center" width="90%" class="tborder">
			<tr class="catbg">
				<td>' , $txt['smfarticles_groupname'] , '</td>
				<td>' ,  $txt['smfarticles_perm_view']  , '</td>
				<td>' ,  $txt['smfarticles_perm_add']  , '</td>
				<td>' ,  $txt['smfarticles_perm_edit']  , '</td>
				<td>' ,  $txt['smfarticles_perm_delete']  , '</td>
				<td>' ,  $txt['smfarticles_options']  , '</td>
				</tr>';

		// Show the member groups
			foreach($context['articles_membergroup'] as $row)
			{

				echo '<tr class="windowbg2">
				<td>'  , $row['groupName'] , '</td>
				<td>' , ($row['view'] ? $txt['smfarticles_perm_allowed'] : $txt['smfarticles_perm_denied']) , '</td>
				<td>' , ($row['addarticle'] ? $txt['smfarticles_perm_allowed'] : $txt['smfarticles_perm_denied']) , '</td>
				<td>' , ($row['editarticle'] ? $txt['smfarticles_perm_allowed'] : $txt['smfarticles_perm_denied']) , '</td>
				<td>' , ($row['delarticle'] ? $txt['smfarticles_perm_allowed'] : $txt['smfarticles_perm_denied']) , '</td>
				<td><a href="' , $scripturl , '?action=articles;sa=catpermdelete&id=' , $row['ID'] , '">' , $txt['smfarticles_txtdel'] , '</a></td>
				</tr>';

			}
			
			// Show Regular members
			foreach($context['articles_reggroup'] as $row)
			{

				echo '<tr class="windowbg2">
				<td>'  , $txt['membergroups_members'] , '</td>
				<td>' , ($row['view'] ? $txt['smfarticles_perm_allowed'] : $txt['smfarticles_perm_denied']) , '</td>
				<td>' , ($row['addarticle'] ? $txt['smfarticles_perm_allowed'] : $txt['smfarticles_perm_denied']) , '</td>
				<td>' , ($row['editarticle'] ? $txt['smfarticles_perm_allowed'] : $txt['smfarticles_perm_denied']) , '</td>
				<td>' , ($row['delarticle'] ? $txt['smfarticle_perm_allowed'] : $txt['smfarticles_perm_denied']) , '</td>
				<td><a href="' , $scripturl , '?action=articles;sa=catpermdelete&id=' , $row['ID'] , '">' , $txt['smfarticles_txtdel'] , '</a></td>
				</tr>';
			}
			
			// Show Guests
			foreach($context['articles_guest'] as $row)
			{
				echo '<tr class="windowbg2">
				<td>'  , $txt['membergroups_guests'] , '</td>
				<td>' , ($row['view'] ? $txt['smfarticles_perm_allowed'] : $txt['smfarticles_perm_denied']) , '</td>
				<td>' , ($row['addarticle'] ? $txt['smfarticles_perm_allowed'] : $txt['smfarticles_perm_denied']) , '</td>
				<td>' , ($row['editarticle'] ? $txt['smfarticles_perm_allowed'] : $txt['smfarticles_perm_denied']) , '</td>
				<td>' , ($row['delarticle'] ? $txt['smfarticles_perm_allowed'] : $txt['smfarticles_perm_denied']) , '</td>
				<td><a href="' , $scripturl , '?action=articles;sa=catpermdelete&id=' , $row['ID'] , '">' , $txt['smfarticles_txtdel'] , '</a></td>
				</tr>';
			}
			

		echo '
				</table>
			</td>
		</tr>
</table>';

}
function template_catpermlist()
{
	global $scripturl, $txt, $context;

echo '
<table border="0" width="80%" cellspacing="0" align="center" cellpadding="4" class="tborder">
		<tr class="titlebg">
			<td>', $txt['smfarticles_catpermlist'], '</td>
		</tr>
		<tr class="windowbg">
			<td>
			<table cellspacing="0" cellpadding="10" border="0" align="center" width="90%" class="tborder">
			<tr class="catbg">
				<td>' , $txt['smfarticles_groupname'] , '</td>
				<td>' , $txt['smfarticles_category']  , '</td>
				<td>' ,  $txt['smfarticles_perm_view']  , '</td>
				<td>' ,  $txt['smfarticles_perm_add']  , '</td>
				<td>' ,  $txt['smfarticles_perm_edit']  , '</td>
				<td>' ,  $txt['smfarticles_perm_delete']  , '</td>
				<td>' ,  $txt['smfarticles_options']  , '</td>
				</tr>';

			// Show the member groups
		 
			foreach($context['articles_mbgroups'] as $row)
			{

				echo '<tr class="windowbg2">
				<td>'  , $row['groupName'] , '</td>
				<td><a href="' , $scripturl , '?action=articles;sa=catperm;cat=' , $row['ID_CAT'] , '">'  , $row['catname'] , '</a></td>
				<td>' , ($row['view'] ? $txt['smfarticles_perm_allowed'] : $txt['smfarticles_perm_denied']) , '</td>
				<td>' , ($row['addarticle'] ? $txt['smfarticles_perm_allowed'] : $txt['smfarticles_perm_denied']) , '</td>
				<td>' , ($row['editarticle'] ? $txt['smfarticles_perm_allowed'] : $txt['smfarticles_perm_denied']) , '</td>
				<td>' , ($row['delarticle'] ? $txt['smfarticles_perm_allowed'] : $txt['smfarticles_perm_denied']) , '</td>
				<td><a href="' , $scripturl , '?action=articles;sa=catpermdelete&id=' , $row['ID'] , '">' , $txt['smfarticles_txtdel'] , '</a></td>
				</tr>';

			}
			
			// Show Regular members
			  foreach($context['articles_regular'] as $row)
			{

				echo '<tr class="windowbg2">
				<td>'  , $txt['membergroups_members'] , '</td>
				<td><a href="' , $scripturl , '?action=articles;sa=catperm;cat=' , $row['ID_CAT'] , '">'  , $row['catname'] , '</a></td>
				<td>' , ($row['view'] ? $txt['smfarticles_perm_allowed'] : $txt['smfarticles_perm_denied']) , '</td>
				<td>' , ($row['addarticle'] ? $txt['smfarticles_perm_allowed'] : $txt['smfarticles_perm_denied']) , '</td>
				<td>' , ($row['editarticle'] ? $txt['smfarticles_perm_allowed'] : $txt['smfarticles_perm_denied']) , '</td>
				<td>' , ($row['delarticle'] ? $txt['smfarticles_perm_allowed'] : $txt['smfarticles_perm_denied']) , '</td>
				<td><a href="' , $scripturl , '?action=articles;sa=catpermdelete&id=' , $row['ID'] , '">' , $txt['smfarticles_txtdel'] , '</a></td>
				</tr>';
			}
			
			
			// Show Guests
			foreach($context['articles_guests'] as $row)
			{

				echo '<tr class="windowbg2">
				<td>'  , $txt['membergroups_guests'] , '</td>
				<td><a href="' , $scripturl , '?action=articles;sa=catperm;cat=' , $row['ID_CAT'] , '">'  , $row['catname'] , '</a></td>
				<td>' , ($row['view'] ? $txt['smfarticles_perm_allowed'] : $txt['smfarticles_perm_denied']) , '</td>
				<td>' , ($row['addarticle'] ? $txt['smfarticles_perm_allowed'] : $txt['smfarticles_perm_denied']) , '</td>
				<td>' , ($row['editarticle'] ? $txt['smfarticles_perm_allowed'] : $txt['smfarticles_perm_denied']) , '</td>
				<td>' , ($row['delarticle'] ? $txt['smfarticles_perm_allowed'] : $txt['smfarticles_perm_denied']) , '</td>
				<td><a href="' , $scripturl , '?action=articles;sa=catpermdelete&id=' , $row['ID'] , '">' , $txt['smfarticles_txtdel'] , '</a></td>
				</tr>';
			}
			

		echo '


				</table>
			</td>
		</tr>

</table>';

}
function template_myarticles()
{
	global $txt, $context, $scripturl, $modSettings, $scripturl;
	
	// Setup their permissions
	$addarticle = $context['addarticle'];
	$editarticle = $context['editarticle'];
	$deletearticle = $context['deletearticle'];
	
	echo '
		<div style="padding: 3px;">', theme_linktree(), '</div>';
	
	echo '<table border="0" cellspacing="0" cellpadding="4" align="center" width="90%" class="tborder" >
					<tr class="titlebg">
						<td align="center">', $txt['smfarticles_indextitle'], '</td>
					</tr>
					</table>
				<table border="0" cellpadding="0" cellspacing="0" align="center" width="90%">
						<tr>
							<td style="padding-right: 1ex;" align="right" >
						<table cellpadding="0" cellspacing="0" align="right">
									<tr>
						', DoToolBarStrip($context['articles']['buttons'], 'top'), '
							</tr>
							</table>
						</td>
						</tr>
					</table>
				<br />';


			echo '<br /><table cellspacing="0" cellpadding="10" border="0" align="center" width="90%" class="tborder">
					<tr>
						<td class="titlebg">
						<a href="' , $scripturl , '?action=articles;sa=myarticles;start=' , $context['start'] , ';sort=title;sorto=' , $context['articles_newsorto'] , '">' , $txt['smfarticles_ctitle'] , '</a></td>';

						if (!empty($modSettings['smfarticles_disp_rating']))
							echo '<td class="titlebg"><a href="' , $scripturl , '?action=articles;sa=myarticles;start=' , $context['start'] , ';sort=rating;sorto=' , $context['articles_newsorto'] , '">' , $txt['smfarticles_crating'] , '</a></td>';
						
						if (!empty($modSettings['smfarticles_disp_totalcomment']))	
							echo '<td class="titlebg"><a href="' , $scripturl , '?action=articles;sa=myarticles;start=' , $context['start'] , ';sort=comment;sorto=' , $context['articles_newsorto'] , '">' , $txt['smfarticles_txt_comments'] , '</a></td>';

						if (!empty($modSettings['smfarticles_disp_views']))
							echo '<td class="titlebg"><a href="' , $scripturl , '?action=articles;sa=myarticles;start=' , $context['start'] , ';sort=views;sorto=' , $context['articles_newsorto'] , '">' , $txt['smfarticles_cviews'] , '</a></td>';
				
						if (!empty($modSettings['smfarticles_disp_membername']))
							echo '<td class="titlebg"><a href="' , $scripturl , '?action=articles;sa=myarticles;start=' , $context['start'] , ';sort=username;sorto=' , $context['articles_newsorto'] , '">' , $txt['smfarticles_cusername'] , '</a></td>';
						
						if (!empty($modSettings['smfarticles_disp_date']))
							echo '<td class="titlebg"><a href="' , $scripturl , '?action=articles;sa=myarticles;start=' , $context['start'] , ';sort=date;sorto=' , $context['articles_newsorto'] , '">' , $txt['smfarticles_cdate'] , '</a></td>';

						echo '
						<td class="titlebg">' , $txt['smfarticles_options'] ,'</td>
					</tr>';
			
			$max_num_stars = 5;
			
			$styleclass = "windowbg";
						
			foreach($context['articles_listing'] as $row)
			{

				echo '<tr  class="' , $styleclass  , '">
				<td><a href="' , $scripturl , '?action=articles;sa=view;article=', $row['ID_ARTICLE'], '">', $row['title'], '</a></td>';
		
				if (!empty($modSettings['smfarticles_disp_rating']))
					echo '<td>' , GetStarsByPrecent(($row['totalratings'] != 0) ? ($row['rating'] / ($row['totalratings']* $max_num_stars) * 100) : 0) ,'</td>';
			
				if (!empty($modSettings['smfarticles_disp_totalcomment']))
					echo '<td>', $row['commenttotal'], '</td>';				
					
				if (!empty($modSettings['smfarticles_disp_views']))
					echo '<td>', $row['views'], '</td>';

				// Check if it was a guest article
				if (!empty($modSettings['smfarticles_disp_membername']))
					if ($row['realName'] != '')
						echo '<td><a href="', $scripturl, '?action=profile;u=', $row['ID_MEMBER'], '">', $row['realName'], '</a></td>';
					else
						echo '<td>', $txt['smfarticles_txtguest'], '</td>';

				if (!empty($modSettings['smfarticles_disp_date']))
					echo '<td>', timeformat($row['date']), '</td>';

				echo '<td>';

				if ($editarticle)
					echo '<a href="' , $scripturl , '?action=articles;sa=editarticle&id=' , $row['ID_ARTICLE'] , '">' , $txt['smfarticles_txtedit'] , '</a>&nbsp;';
				if ($deletearticle)
					echo '<a href="' , $scripturl , '?action=articles;sa=deletearticle&id=' , $row['ID_ARTICLE'] , '">' , $txt['smfarticles_txtdel'] , '</a>&nbsp;';
				
				echo '</td>
				</tr>';
				
				
				// Alternate style class 
				
				if ($styleclass == 'windowbg')
					$styleclass = 'windowbg2';
				else 
					$styleclass = 'windowbg';
			}
			
		
			
			
			echo '</table>';
			
			// Show the pages
				echo '
				<table cellspacing="0" cellpadding="10" border="0" align="center" width="90%" class="tborder">
				<tr class="titlebg">
						<td align="left">
						',$txt['smfarticles_pages'],$context['page_index'],'
						</td>
					</tr>';



		
			// Show return to articles index link
			echo '

			<tr class="titlebg"><td align="center">';

	
			// See if they are allowed to add articles
			if ($addarticle)
			{
				echo '<a href="' , $scripturl , '?action=articles;sa=addarticle">' , $txt['smfarticles_addarticle'] , '</a>&nbsp;';
				echo '<br /><br />';
			}

			echo '
			<a href="', $scripturl, '?action=articles">', $txt['smfarticles_returnindex'], '</a>
			</td></tr></table>';

}
function template_search()
{
	global $txt, $context, $scripturl, $settings;
	
	echo '<table border="0" cellspacing="0" cellpadding="4" align="center" width="90%" class="tborder" >
					<tr class="titlebg">
						<td align="center">', $txt['smfarticles_indextitle'], '</td>
					</tr>
					</table>
				<table border="0" cellpadding="0" cellspacing="0" align="center" width="90%">
						<tr>
							<td style="padding-right: 1ex;" align="right" >
						<table cellpadding="0" cellspacing="0" align="right">
									<tr>
						', DoToolBarStrip($context['articles']['buttons'], 'top'), '
							</tr>
							</table>
						</td>
						</tr>
					</table>
				<br />';
				
	
	
	echo '
<form method="post" action="' , $scripturl , '?action=articles;sa=search2">
<table border="0" cellpadding="0" cellspacing="0" width="90%"  class="tborder" align="center">
  <tr>
	 <td width="100%" colspan="2" align="center" class="catbg">
	 <b>' ,$txt['smfarticles_search_article'] , '</b></td>
  </tr>
  <tr class="windowbg2">
	 <td width="50%"  align="right"><b>' , $txt['smfarticles_search_for'] , '</b>&nbsp;</td>
	 <td width="50%"><input type="text" name="searchfor" size= "50" />
	 </td>
  </tr>
  <tr class="windowbg2" align="center">
	  <td colspan="2"><input type="checkbox" name="searchtitle" checked="checked" />' , $txt['smfarticles_search_title'] , '&nbsp;<input type="checkbox" name="searchdescription" checked="checked" />' , $txt['smfarticles_search_description'] , '&nbsp;
	  </td>
  </tr>
  <tr class="windowbg2">
	  <td colspan="2" align="center">
	  <hr />
	  <b>',$txt['smfarticles_search_advsearch'],'</b><br />
	  <hr />
	  
	  </td>
  </tr>
	 <tr class="windowbg2">
	 <td width="30%" align="right">' , $txt['smfarticles_category'], '&nbsp;</td>
	  <td width="70%">
		<select name="cat">
		 <option value="0">' , $txt['smfarticles_text_catnone'] , '</option>
	 ';
	 
	foreach ($context['articles_cat'] as $i => $category)
		echo '<option value="' , $category['ID_CAT']  , '" >' , $category['title'] , '</option>';
	
	
	echo '</select></td>
	 </tr>
	 <tr class="windowbg2">
	  <td width="30%" align="right">' , $txt['smfarticles_search_daterange'], '&nbsp;</td>
	  <td width="70%">
		<select name="daterange">
		 <option value="0">' , $txt['smfarticles_search_alltime']  , '</option>
		 <option value="30">' , $txt['smfarticles_search_days30']  , '</option>
		 <option value="60">' , $txt['smfarticles_search_days60']  , '</option>
		 <option value="90">' , $txt['smfarticles_search_days90']  , '</option>
		 <option value="180">' , $txt['smfarticles_search_days180']  , '</option>
		 <option value="365">' , $txt['smfarticles_search_days365']  , '</option>
		 
</select></td>
	 </tr>
	 
	 <tr class="windowbg2">
	  <td width="30%"  align="right">' , $txt['smfarticles_search_membername'], '&nbsp;</td>
	  <td width="70%">
		<input type="text" name="pic_postername" id="pic_postername" value="" />
	  <a href="', $scripturl, '?action=findmember;input=pic_postername;quote=1;sesc=', $context['session_id'], '" onclick="return reqWin(this.href, 350, 400);"><img src="', $settings['images_url'], '/icons/assist.gif" alt="', $txt['find_members'], '" /></a> 
	  <a href="', $scripturl, '?action=findmember;input=pic_postername;quote=1;sesc=', $context['session_id'], '" onclick="return reqWin(this.href, 350, 400);">', $txt['find_members'], '</a>
	  </td>
	 </tr>
	 
	 
  <tr>
	 <td width="100%" colspan="2"  align="center" class="windowbg2"><br />
	 <input type="submit" value="' , $txt['smfarticles_search'] , '" name="submit" />
	 
	 <br /></td>

  </tr>
  <tr class="titlebg">
  <td align="center" colspan="2">
  <a href="' , $scripturl , '?action=articles">' , $txt['smfarticles_returnindex'] , '</a>
  </td>
  </tr>
</table>
</form>
<br />

';

}
function template_search_results()
{
	global $txt, $context, $scripturl, $modSettings;
	
	// Setup their permissions
	$m_cats = allowedTo('articles_admin');
	
	echo '<table border="0" cellspacing="0" cellpadding="4" align="center" width="90%" class="tborder" >
					<tr class="titlebg">
						<td align="center">', $txt['smfarticles_searchresults'], '</td>
					</tr>
					</table>
				<table border="0" cellpadding="0" cellspacing="0" align="center" width="90%">
						<tr>
							<td style="padding-right: 1ex;" align="right" >
						<table cellpadding="0" cellspacing="0" align="right">
									<tr>
						', DoToolBarStrip($context['articles']['buttons'], 'top'), '
							</tr>
							</table>
						</td>
						</tr>
					</table>
				<br />';
	
	
			$spancount = 1;
			
			echo '<br /><table cellspacing="0" cellpadding="10" border="0" align="center" width="90%" class="tborder">
					<tr>
						<td class="titlebg">
						' , $txt['smfarticles_ctitle'] , '</td>';

						if (!empty($modSettings['smfarticles_disp_rating']))
						{
							echo '<td class="titlebg">' , $txt['smfarticles_crating'] , '</td>';
							$spancount++;
						}
						
						if (!empty($modSettings['smfarticles_disp_totalcomment']))
						{	
							echo '<td class="titlebg">' , $txt['smfarticles_txt_comments'] , '</td>';
							$spancount++;
						}
							
						if (!empty($modSettings['smfarticles_disp_views']))
						{
							echo '<td class="titlebg">' , $txt['smfarticles_cviews'] , '</td>';
							$spancount++;
						}
						
						if (!empty($modSettings['smfarticles_disp_membername']))
						{
							echo '<td class="titlebg">' , $txt['smfarticles_cusername'] , '</td>';
							$spancount++;
						}
						
						if (!empty($modSettings['smfarticles_disp_date']))
						{
							echo '<td class="titlebg">' , $txt['smfarticles_cdate'] , '</td>';
							$spancount++;
						}
							
						if ($m_cats)
						{
							echo '
							<td class="titlebg">' , $txt['smfarticles_options'] ,'</td>';
							$spancount++;
						}
						
						echo '
					</tr>';
			
			$max_num_stars = 5;
			
			$styleclass = "windowbg";
						
			foreach($context['articles_listing'] as $row)
			{

				echo '<tr  class="' , $styleclass  , '">
				<td><a href="' , $scripturl , '?action=articles;sa=view;article=', $row['ID_ARTICLE'], '">', $row['title'], '</a></td>';
		
				if (!empty($modSettings['smfarticles_disp_rating']))
					echo '<td>' , GetStarsByPrecent(($row['totalratings'] != 0) ? ($row['rating'] / ($row['totalratings']* $max_num_stars) * 100) : 0), '</td>';
	
				if (!empty($modSettings['smfarticles_disp_totalcomment']))
					echo '<td>', $row['commenttotal'], '</td>';

									
					
				if (!empty($modSettings['smfarticles_disp_views']))
					echo '<td>', $row['views'], '</td>';

	
					
		
				// Check if it was a guest article
				if (!empty($modSettings['smfarticles_disp_membername']))
					if ($row['realName'] != '')
						echo '<td><a href="', $scripturl, '?action=profile;u=', $row['ID_MEMBER'], '">', $row['realName'], '</a></td>';
					else
						echo '<td>', $txt['smfarticles_txtguest'], '</td>';

				if (!empty($modSettings['smfarticles_disp_date']))
					echo '<td>', timeformat($row['date']), '</td>';

				if ($m_cats)
				{
					echo '<td>
					<a href="' , $scripturl , '?action=articles;sa=editarticle&id=' , $row['ID_ARTICLE'] , '">' , $txt['smfarticles_txtedit'] , '</a>&nbsp;
					<a href="' , $scripturl , '?action=articles;sa=deletearticle&id=' , $row['ID_ARTICLE'] , '">' , $txt['smfarticles_txtdel'] , '</a>&nbsp;
					<a href="' , $scripturl , '?action=articles;sa=noapprove&id=' , $row['ID_ARTICLE'] , '">' , $txt['smfarticles_txtunapprove'] , '</a>
					</td>';
				}

				echo '</tr>';
				
				
				// Alternate style class 
				if ($styleclass == 'windowbg')
					$styleclass = 'windowbg2';
				else 
					$styleclass = 'windowbg';
			}
			
		
			// Show the pages
				echo '
				
				<tr class="titlebg">
						<td align="left" colspan="', $spancount, '">
						', $txt['smfarticles_pages'],$context['page_index'],'
						</td>
					</tr>';

			// Show return to articles index link
			echo '
			<tr class="titlebg"><td align="center" colspan="', $spancount, '">';


			echo '
			<a href="', $scripturl, '?action=articles">', $txt['smfarticles_returnindex'], '</a>
			</td></tr></table>';
			


}

?>