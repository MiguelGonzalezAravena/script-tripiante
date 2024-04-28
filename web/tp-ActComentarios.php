<?php
@require_once(dirname(dirname(__FILE__)) . '/SSI.php');

global $context, $db_prefix, $modSettings, $boardurl;

if ($context['user']['is_guest']) {
  echo '
    <div class="noesta-am">
      S&oacute;lo usuarios REGISTRADOS pueden actualizar los comentarios.
      <br />
      <a href="' . $boardurl . '/registrarse/">REG&Iacute;STRATE</a>
      &nbsp;-&nbsp;
      <a href="' . $boardurl . '/ingresar/">CON&Eacute;CTATE</a>
    </div>';
} else if ($context['user']['is_logged']) {
  $request = db_query("
    SELECT c.ID_COMMENT, c.ID_TOPIC AS ID_TOPIC2, c.ID_MEMBER AS ID_MEMBER2, m.ID_MEMBER, m.realName,
    t.ID_TOPIC, b.ID_BOARD, t.ID_BOARD, b.description, m2.subject, m2.ID_TOPIC, m2.subject AS subject2
    FROM ({$db_prefix}comments AS c, {$db_prefix}members AS m, {$db_prefix}topics AS t, {$db_prefix}boards as b, {$db_prefix}messages AS m2)
    WHERE c.ID_TOPIC = t.ID_TOPIC
    AND c.ID_MEMBER = m.ID_MEMBER
    AND b.ID_BOARD = t.ID_BOARD
    AND m2.ID_TOPIC = t.ID_TOPIC
    ORDER BY c.ID_COMMENT DESC
    LIMIT " . $modSettings['number_comments'], __FILE__, __LINE__);

  while ($row = mysqli_fetch_assoc($request)) {
    $realName = censorText($row['realName']);
    $description = $row['description'];
    $subject = ssi_reducir(htmlentities($row['subject'], ENT_QUOTES, 'UTF-8'));

    echo '
      <font class="size11">
        <b>
          <a title="" href="' . $boardurl . '/perfil/' . $realName . '">' . $realName . '</a>
        </b>
        -
        <a title="' . $subject . '"  href="' . $boardurl . '/post/' . $row['ID_TOPIC2'] . '/' . $description . '/' . ssi_amigable($subject) . '.html#cmt_' . $row['ID_COMMENT'] . '">' . $subject . '</a>
      </font>
      <br style="margin: 0px; padding: 0px;" />';
  }
}

?>