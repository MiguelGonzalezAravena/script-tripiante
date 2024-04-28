<?php
// Version: 1.1; Printpage

function template_print_above() {
  global $context, $boardurl;

  echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html version="XHTML+RDFa 1.0"  xmlns="http://www.w3.org/1999/xhtml" lang="es" xml:lang="es" >
<head profile="http://purl.org/NET/erdf/profile">
  <link rel="schema.dc" href="http://purl.org/dc/elements/1.1/" />
  <link rel="schema.foaf" href="http://xmlns.com/foaf/0.1/" />
<meta http-equiv="Content-Type" content="text/html; charset="', $context['character_set'], '" />
<meta name="robots" content="all" />
<meta name="keywords" content="linksharing, enlaces, juegos, musica, links, noticias, imagenes, videos, animaciones, arte, tecnologia, celulares, chile, comunidad, tp" />
<link rel="search" type="application/opensearchdescription+xml" title="', $context['forum_name'], '" href="/cw-buscador-web.xml" />
<link rel="icon" href="/favicon.png" type="image/x-icon" />
<link rel="shortcut icon" href="/favicon.png" type="image/x-icon" />
<link rel="apple-touch-icon" href="/web/imagenes/apple-touch-icon.png" />
<link rel="alternate" type="application/atom+xml" title="&Uacute;ltimos posts" href="', $boardurl, '/rss/ultimos-post" />

<link rel="alternate" type="application/atom+xml" title="&Uacute;ltimos comentarios" href="', $boardurl, '/rss/ultimos-comment" />
<title>Imprimir Imagen - ', $context['topic_subject'], '</title>
<style type="text/css">
body{color:black;background-color:white;align:center;}
body, td, .normaltext{font-family: Arial, helvetica, serif;font-size:xx-small;}
*, a:link, a:visited, a:hover, a:active{color:black!important;}
table{empty-cells: show;}
.code{font-size: xxx-small;font-family: monospace;border: 1px solid black;margin: 1px;padding: 1px;}
.quote{font-size: xxx-small;border: 1px solid black;margin: 1px;padding: 1px;}
.smalltext, .quoteheader, .codeheader{font-size: xxx-small;}
hr{height: 1px;border: 0;color: black;background-color: black;}</style></head><body onload="javascript:window.print();"><center><h1 class="largetext">', $context['topic_subject'], '</h1>';
foreach ($context['image'] as $post) {
echo $boardurl . '/imagenes/ver/', $post['ID_PICTURE'], '<br /><b></b><hr />';
echo '<img alt="" onload="if (this.width > 750) {this.width=750}" src="', $post['filename'], '" title="', $post['title'], '" />';
}
}

function template_main() {}

function template_print_below() {
  global $mbname, $boardurl;

  echo '</center><center><hr />&copy; ' . date("Y") . ' ' . $mbname . ' - '  . $boardurl . '</center></body></html>';
}

?>
