<?php
// Version 1.2.0;


function template_Begin() {}

function template_Boards() {
  global $context, $settings, $boardurl;

  if(isset($context['sitemap']['board'])) {
    $switch = false;
  }
    
  echo '
      <div class="box_300" style="float: left; margin-right: 8px;" align="left">
        <div class="box_title" style="width: 300px;"><div class="box_txt box_300-34">General</div>
        <div class="box_rss">
          <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 16px; height: 16px;" border="0" />
        </div>
      </div>
      <div class="windowbg" style="padding: 4px; width: 292px;">
        <span class="size11">
          <a href="' . $boardurl . '/ayuda/" title="Ayuda">Ayuda</a>
          <br />
          <a href="' . $boardurl . '/buscar/" title="Buscador">Buscador</a>
          <br />
          <a href="' . $boardurl . '/chat/" title="Chat">Chat</a>
          <br />
          <a href="' . $boardurl . '/contactanos/" title="Contacto">Contacto</a>
          <br />
          <a href="' . $boardurl . '/enlazanos/" title="Enl&aacute;zanos">Enl&aacute;zanos</a>
          <br />
          <a href="' . $boardurl . '/protocolo/" title="Protocolo">Protocolo</a>
          <br />
          <a href="' . $boardurl . '/widget/" title="Widget">Widget</a>
          <br />
          <a href="' . $boardurl . '/terminos-y-condiciones/" title="T&eacute;rminos y condiciones">T&eacute;rminos y condiciones</a>
          <br />
          <a href="' . $boardurl . '/tops/" title="TOPs">TOPs</a>
        </span>
        <br />
      </div>
    </div>
    <div class="box_300" style="float: left; margin-right: 8px;" align="left">
      <div class="box_title" style="width: 300px;">
        <div class="box_txt box_300-34">Categor&iacute;as</div>
        <div class="box_rss">
          <img src="' . $settings['images_url'] . '/blank.gif" style="width: 16px; height: 16px;" border="0" alt="" />
        </div>
      </div>
      <div class="windowbg" style="padding: 4px; width: 292px;">
        <span class="size11">';

  foreach($context['sitemap']['board'] as $board) {
    if ($board['level'] == 0 && $switch) {
      $switch = false;
    }

    echo '
      <a href="' . $boardurl . '/categoria/' . $board['description'] . '" title="' . $board['name'] . '">' . $board['name'] . '</a>
      <br />';
  }
    echo '
            </span>
          </div>
        </div>
        <div class="box_300" style="float: left;" align="left">
          <div class="box_title" style="width: 300px;">
            <div class="box_txt box_300-34">RSS</div>
            <div class="box_rss">
              <img alt="" src="' . $settings['images_url'] . '/blank.gif" style="width: 16px; height: 16px;" border="0" />
            </div>
          </div>
          <div class="windowbg" style="padding: 4px; width: 292px;">
            <span class="size11">
              <a href="' . $boardurl . '/rss/ultimos-post/" title="&Uacute;ltimos posts">&Uacute;ltimos posts</a><br />
              <a href="' . $boardurl . '/rss/ultimos-comment/" title="&Uacute;ltimos comentarios">&Uacute;ltimos comentarios</a>
            </span>
            <br />
          </div>
        </div>
        <div style="clear: both;"></div>
        <div style="display:none;"></div>
        <div style="clear:both"></div>
      </div>';
}

?>