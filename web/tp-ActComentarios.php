<?php
@require_once($_SERVER['DOCUMENT_ROOT'] . '/Settings.php');
@require_once($_SERVER['DOCUMENT_ROOT'] . '/SSI.php');

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
    $ID_COMMENT = $row['ID_COMMENT'];
    $ID_TOPIC = $row['ID_TOPIC2'];
    $ID_MEMBER = $row['ID_MEMBER2'];
    $realName = censorText($row['realName']);
    $description = $row['description'];
    $subject = htmlentities(ssi_reducir($row['subject']));

    echo '
      <font class="size11">
        <b>
          <a title="" href="' . $boardurl . '/perfil/' . $realName . '">' . $realName . '</a>
        </b>
        &nbsp;-&nbsp;
        <a title="' . $subject . '"  href="' . $boardurl . '/post/' . $ID_TOPIC . '/' . $description . '/' . ssi_amigable($subject) . '.html#cmt_' . $ID_COMMENT . '">' . $subject . '</a>
      </font>
      <br style="margin: 0px; padding: 0px;" />';
  }
}

?>