<?php
// Version: 1.1; Printpage

function template_print_above()
{
	global $context, $settings, $options, $txt, $boardurl;

	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html version="XHTML+RDFa 1.0"  xmlns="http://www.w3.org/1999/xhtml" lang="es" xml:lang="es" >
<head profile="http://purl.org/NET/erdf/profile">
  <link rel="schema.dc" href="http://purl.org/dc/elements/1.1/" />
  <link rel="schema.foaf" href="http://xmlns.com/foaf/0.1/" />
<meta http-equiv="Content-Type" content="text/html; charset=', $context['character_set'], '" />
<meta name="description" content="', $context['topic_subject'], '" />
<meta name="robots" content="all" />
<meta name="keywords" content="linksharing, enlaces, juegos, musica, links, noticias, imagenes, videos, animaciones, arte, tecnologia, celulares, argentina, comunidad, cw" />
<link rel="search" type="application/opensearchdescription+xml" title="CasitaWeb!" href="/cw-buscador-web.xml" />
<link rel="icon" href="/favicon.png" type="image/x-icon" />
<link rel="shortcut icon" href="/favicon.png" type="image/x-icon" />
<link rel="apple-touch-icon" href="/web/imagenes/apple-touch-icon.png" />
<link rel="alternate" type="application/atom+xml" title="&Uacute;ltimos posts" href="', $boardurl, '/rss/ultimos-post" />
<link rel="alternate" type="application/atom+xml" title="&Uacute;ltimos comentarios" href="', $boardurl, '/rss/ultimos-comment" />
<title>', $txt[668], ' - ', $context['topic_subject'], '</title>
<style type="text/css">body{color: black;background-color:white;align:center;}
body,td,.normaltext{font-family:Arial, helvetica, serif;font-size: xx-small;align:center;}
*, a:link, a:visited, a:hover, a:active{color: black !important;}
table{empty-cells: show;}
.code{font-size: xxx-small;font-family: monospace;border: 1px solid black;margin: 1px;padding: 1px;}
.quote{font-size: xxx-small;border: 1px solid black;margin: 1px;padding: 1px;}
.smalltext, .quoteheader, .codeheader{font-size: xxx-small;}
hr{height: 1px;border: 0;color: black;background-color: black;}</style></head><body onload="javascript:window.print();">
<center><h1 class="largetext">', $context['forum_name'], ' - ', $context['topic_subject'], '</h1>
';
foreach ($context['posts'] as $post)
{
echo $boardurl . '/post/', $post['ID_TOPIC'], '/', $post['description'], '/', $post['subject_html'], '.html</h2></center>';
}
echo '
<div align="center">
';
}

function template_main()
{
	global $context, $settings, $options, $txt;

	foreach ($context['posts'] as $post)
		echo '<div style="width:80%;"><center><br />
					<hr size="2" width="100%" />
					', $txt[196], ': <b>', $context['topic_subject'], '</b><br />

					', $txt[197], ': <b>', $post['member'], '</b> en <b>', $post['time'], '</b>
					<hr />
					<div style="margin:0px 5ex;">', $post['body'], '</div>';
}

function template_print_below()
{
	global $context, $settings, $options;

	echo '</center><br /><br /><hr /><font size="1">', theme_copyright(), '</font></center></div></div></body></html>';
}
?>
