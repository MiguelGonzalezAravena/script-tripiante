<?php
function template_shop_above() {}
function template_shop_below() {}

function template_main() {
  header("Location: /");
  echo '<a href="#" onclick="$(\'#amount\').val(15)"></a>';
}

function template_message() {
  global $context, $scripturl, $boardurl;

  echo '
    <div>
      <table align="center" width="392px" cellpadding="3" cellspacing="0" border="0">
        <tr>
          <td width="100%" class="titulo_a">&nbsp;</td>
          <td width="100%" class="titulo_b"><center>Puntos</center></td>
          <td width="100%" class="titulo_c">&nbsp;</td>
        </tr>
      </table>
      <table align="center" class="windowbg" width="392px">
        <tr class="windowbg">
          <td align="center">
            <br />
            ' . $context['shop_buy_message'] . '
            <br />
            <br />
            <input class="login" style="font-size: 11px;" type="submit" title="Volver al post" value="Volver al post" onclick="location.href=\'' . $scripturl . '?topic=' . $_GET['topic'] . '\'" />
            <input class="login" style="font-size: 11px;" type="submit" title="Ir a la P&aacute;gina principal" value="Ir a la P&aacute;gina principal" onclick="location.href=\'' . $boardurl . '/\'" />
            <br />
            <br />
          </td>
        </tr>
          </table>
          </div>';
}

?>