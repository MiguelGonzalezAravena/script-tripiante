<?php
// Version: 1.1; Widget


function template_manual_above() {}

function template_manual_below() {}

function template_manual_intro() { 
  global $context, $settings, $boardurl;

  $switch = true;

  echo '
  <script type="text/javascript">
    var ancho = new Array();
    var alto = new Array();
    ancho[\'0\'] = 350;
    alto[\'0\'] = 100;
    ancho[\'1\'] = 200;
    alto[\'1\'] = 200;
    ancho[\'2\'] = 200;
    alto[\'2\'] = 250;
    ancho[\'3\'] = 285;
    alto[\'3\'] = 134;
    ancho[\'4\'] = 200;
    alto[\'4\'] = 300;
    ancho[\'5\'] = 320;
    alto[\'5\'] = 100;
    ancho[\'6\'] = 320;
    alto[\'6\'] = 200;
    ancho[\'7\'] = 320;
    alto[\'7\'] = 300;

    function actualizar_preview(noselect) {
      document.getElementById("cantidad").value = parseInt(document.getElementById("cantidad").value);
      if (isNaN(document.getElementById("cantidad").value)) {
        document.getElementById("cantidad").value = "";
        alert("Debe ingresar un valor numerico en el campo cantidad de posts listados");
        return;
      }

      if (!document.getElementById("cantidad").value) {
        alert("Debe ingresar un valor en el campo cantidad de posts listados");
        document.getElementById("cantidad").focus();
        return;
      }

      if (document.getElementById("cantidad").value > 50) {
        alert("La cantidad maxima de posts listados es 50");
        document.getElementById("cantidad").focus();
        return;
      }

      if (document.getElementById("cantidad").value < 5) {
        alert("La cantidad minima de posts listados es 5");
        document.getElementById("cantidad").focus();
        return;
      }

      code = \'<div style="border: 1px solid rgb(213, 213, 213); padding: 2px 5px 5px; background: #D7D7D7 url(' . $settings['images_url'] . '/fondo2-widget.png) repeat-x scroll center top; width: \' + ancho[document.getElementById("tamano").value] +\'px; -moz-background-clip: -moz-initial; -moz-background-origin: -moz-initial; -moz-background-inline-policy: -moz-initial; text-align: left;"><a href="' . $boardurl . '/"><img src="' . $settings['images_url'] . '/widget-logo.png" alt="' . $context['forum_name'] . '" style="border: 0pt none ; margin: 0px 0px 5px 5px;" /></a><iframe src="' . $boardurl . '/wget/\' + document.getElementById("cat").value + \'-\' + document.getElementById("cantidad").value + \'-\' + ancho[document.getElementById("tamano").value] + \'/" style="border: 1px solid rgb(213, 213, 213); margin: 0pt; padding: 0pt; width: \'+ancho[document.getElementById("tamano").value]+\'px; height: \'+alto[document.getElementById("tamano").value]+\'px;" frameborder="0"></iframe></div>\';

      document.getElementById("widget-preview").innerHTML=code;
      document.getElementById("codigo").value = code;
      focus_code(noselect);
      return;
    }

    function focus_code(noselect) {
      if (!noselect)
        document.getElementById("codigo").focus();

      document.getElementById("codigo").select();
      return;
    }
  </script>
  <div>
    <div class="box_buscador">
      <div class="box_title" style="width: 919px;">
        <div class="box_txt box_buscadort">
          <center>Widget</center>
        </div>
        <div class="box_rss">
          <img  src="' . $settings['images_url'] . '/blank.gif" style="width: 14px; height: 12px;" border="0">
        </div>
      </div>
      <div style="width: 911px; padding: 4px; margin-bottom: 8px;" class="windowbg">
        Integra los &uacute;ltimos posts de <strong>' . $context['forum_name'] . '</strong> en tu web y estate siempre actualizado.
        <br /><br />

        En solo segundos podr&aacute;s tener un listado que estar&aacute; siempre 
        actualizado con los &uacute;ltimos posts publicados en <strong>' . $context['forum_name'] . '</strong>.
        <br /><br />

        Puedes personalizar el listado para que se adapte al estilo de tu sitio, puedes cambiar su tama&ntilde;o, color, cantidad de posts a listar y hasta puedes filtrar por categor&iacute;as.
        <br /><br />

        <b>&iquest;C&oacute;mo implementarlo?:</b><br />
        <b>1.</b> Personal&iacute;zalo a tu gusto. C&aacute;mbiale color, y elige el tama&ntilde;o.<br />

        <b>2.</b> Copia el c&oacute;digo generado y p&eacute;galo en tu p&aacute;gina.<br />

        <b>3.</b> Listo. Ya puedes disfrutar de <strong>' . $context['forum_name'] . '</strong> Widget.
        <br />
      </div>
    </div>
    <table style="width: 921px; margin: 0px;">
      <tr style="padding: 0px; margin: 0px;">
        <td style="20%; padding: 0px; margin: 0px;" height="25px" class="titlebg" align="center">
          <b><font color="#000">Personalizaci&oacute;n</font></b>
        </td>
        <td style="30%; padding: 0px; margin: 0px;" height="25px" class="titlebg" align="center">
          <b><font color="#000">C&oacute;digo</font></b>
        </td>
        <td style="50%; padding: 0px; margin: 0px;" height="25px" class="titlebg" align="center">
          <b><font color="#000">Ejemplo</font></b>
        </td>
      </tr>
      <tr style="padding: 0px; margin: 0px;">
        <td align="center" style="padding: 0px; margin:0px;" class="windowbg">
          <b>Categor&iacute;a:</b><br />
          <select id="cat" onchange="actualizar_preview();">
            <option value="" selected="selected">Todas</option>';

  foreach ($context['sitemap']['board'] as $board) {
    if ($board['level'] == 0 && $switch) {
      $switch = false;
    }

    echo '<option value="' . $board['id'] . '">' . $board['name'] . '</option>';
  }

  echo '
            </select>
            <br/>
            <b>Cantidad:</b><br />
            <input size="4" maxlength="2" id="cantidad" value="20" onchange="actualizar_preview();" type="text" onfocus="foco(this);" onblur="no_foco(this);" />
            <span class="smalltext">(max 50 - min 5)</span><br/>
            <b>Tama&ntilde;o:</b><br />
            <select id="tamano" onchange="actualizar_preview();">
              <option value="0">350 x 100</option>
              <option value="2">200 x 250</option>

              <option value="1">200 x 200</option>
              <option value="3">285 x 134</option>
              <option value="4">200 x 300</option>
              <option value="5">320 x 100</option>
              <option value="6">320 x 200</option>
              <option value="7">320 x 300</option>
            </select>
          </td>
          <td align="center" style="padding: 0px; margin:0px;" class="windowbg">
            <textarea onfocus="foco(this);" onblur="no_foco(this);" id="codigo" cols="47" rows="6" onClick="focus_code();"></textarea>
          </td>  
          <td align="center" style="padding: 0px; margin: 0px;" class="windowbg">
            <input type="hidden" size="4" maxlength="2" id="cantidad" value="20" onchange="actualizar_preview();" />
            <div id="widget-preview"></div>
            <script type="text/javascript">actualizar_preview(1);</script>
          </td>
        </table>
      </div>
      <div style="clear:both"></div>
    </div>';
}

?>