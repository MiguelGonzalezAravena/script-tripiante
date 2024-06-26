<?php
if (!defined('SMF'))
  die('Hacking attempt...');
  
function ComunidadesMain() {
  loadTemplate('Comunidades');

  $subActions = array(
    'comunidad' => 'comunidad',
    'notema' => 'notema',
    'sermiembro' => 'sermiembro',
    'sermiembro2' => 'sermiembro2',
    'salirmiembro' => 'salirmiembro',
    'adminmiembro' => 'adminmiembro', /* ADMINISTRAR LOS MIEMBROS */
    'adminmiembro2' => 'adminmiembro2',
    'vermiembros' => 'vermiembros',
    'crear' => 'crear', /* CREAR LA COMUNIDAD */
    'crear2' => 'crear2',
    'crear3' => 'crear3',
    'editar' => 'editar', /* EDITA LA COMUNIADAD */
    'editar1' => 'editar1',
    'editar2' => 'editar2',
    'editar_adm' => 'editar_adm',
    'editar1_adm' => 'editar1_adm',
    'editar2_adm' => 'editar2_adm',
    'borrar' => 'borrar',
    'borrar2' => 'borrar2',
    'borrar3' => 'borrar3',
    'borrar_adm' => 'borrar_adm',
    'borrar2_adm' => 'borrar2_adm',
    'borrar3_adm' => 'borrar3_adm',
    'denunciar' => 'denunciar', /* DENUNCIAR COMUNIDAD */
    'denunciar2' => 'denunciar2',
    'nuevotema' => 'nuevotema',
    'nuevotema2' => 'nuevotema2',
    'vertema' => 'vertema',
    'editartema' => 'editartema',
    'editartema2' => 'editartema2',
    'publicitar' => 'publicitar',
    'publicitar2' => 'publicitar2',
    'eliminartema' => 'eliminartema',
  );

  if (!empty($subActions[@$_GET['sa']])) {
    $subActions[$_GET['sa']]();
  } else {
    Main();
  }
}

function Main() {
  global $context, $txt, $modSettings, $db_prefix, $ID_MEMBER;

  $context['page_title'] = $txt[18];

  $request = db_query("
    SELECT
      c.ID_COMMUNITY, c.ID_CATEGORY, c.friendly_url AS friendly_url2, c.title, c.date,
      c.ID_MEMBER, mem.ID_MEMBER, mem.realName, cc.ID_CATEGORY, cc.friendly_url, cc.name
    FROM ({$db_prefix}communities AS c, {$db_prefix}members AS mem, {$db_prefix}community_categories AS cc)
    WHERE c.ID_MEMBER = mem.ID_MEMBER
    AND c.ID_CATEGORY = cc.ID_CATEGORY
    ORDER BY ID_COMMUNITY DESC
    LIMIT " . $modSettings['community_latest'], __FILE__, __LINE__);

  $context['ultimas_comunidades'] = array();

  while ($row = mysqli_fetch_assoc($request)) {
    $context['ultimas_comunidades'][] = array(
      'friendly_url' => $row['friendly_url'],
      'title' => $row['title'],
      'realName' => $row['realName'],
      'friendly_url' => $row['friendly_url'],
      'friendly_url2' => $row['friendly_url2'],
      'date' => timeformat($row['date']),
      'name' => $row['name'],
    );
  }

  mysqli_free_result($request);

  $requestd = db_query("
    SELECT ID_COMMUNITY, friendly_url, title, logo, oficial
    FROM {$db_prefix}communities
    WHERE oficial = 1
    ORDER BY RAND()
    LIMIT 1 ", __FILE__, __LINE__);

  $context['destacadas'] = array();

  while ($row = mysqli_fetch_assoc($requestd)) {
    $context['destacadas'][] = array(
      'friendly_url' => $row['friendly_url'],
      'title' => $row['title'],
      'logo' => $row['logo'],
    );
  }

  mysqli_free_result($requestd);

  @require_once('SSI.php');

  /* TOP Comunidades */
  $starttime = mktime(0, 0, 0, date("n"), date("j"), date("Y")) - (date("N")*3600*24);
  $starttime = forum_time(false, $starttime);
      
  $request = db_query("
    SELECT COUNT(cm.ID_MEMBER) AS cuenta, c.ID_COMMUNITY, c.ID_CATEGORY, c.friendly_url, cm.ID_COMMUNITY, c.date, c.title, mem.ID_MEMBER, cm.ID_MEMBER, cc.ID_CATEGORY, cc.name, cc.friendly_url AS friendly_url2
    FROM ({$db_prefix}communities AS c, {$db_prefix}community_members AS cm, {$db_prefix}members AS mem, {$db_prefix}community_categories AS cc)
    WHERE cm.ID_COMMUNITY = c.ID_COMMUNITY
    AND c.ID_COMMUNITY = cm.ID_COMMUNITY
    AND mem.ID_MEMBER = cm.ID_MEMBER
    AND c.date > " . $starttime . "
    AND c.ID_CATEGORY = cc.ID_CATEGORY
    GROUP BY cm.ID_COMMUNITY
    ORDER BY cuenta DESC
    LIMIT " . $modSettings['community_tops'], __FILE__, __LINE__);

  $context['tops_comunidades'] = array();
  while ($row = mysqli_fetch_assoc($request))
    $context['tops_comunidades'][] = array(
      'friendly_url' => $row['friendly_url'],
      'friendly_url2' => $row['friendly_url2'],
      'title' => $row['title'],
      'cuenta' => $row['cuenta'],
    );

  mysqli_free_result($request);
  /* TOP Comunidades */

  $dbresult2 = db_query("
    SELECT ID_MEMBER
    FROM {$db_prefix}community_user
    WHERE ID_MEMBER = $ID_MEMBER
    LIMIT 1", __FILE__, __LINE__);

  $context['tengocomunidad'] = mysqli_num_rows($dbresult2);

  $dbresult5 = db_query("
    SELECT ID_MEMBER, ID_COMMUNITY, friendly_url, logo
    FROM {$db_prefix}communities
    WHERE ID_MEMBER = $ID_MEMBER
    LIMIT 1", __FILE__, __LINE__);

  $row = mysqli_fetch_assoc($dbresult5);

  $context['comunidad'] = array(
    'ID_COMMUNITY' => $row['ID_COMMUNITY'],
    'ID_MEMBER' => $row['ID_MEMBER'],
    'friendly_url' => $row['friendly_url'],
    'logo' => $row['logo'],
  );

  mysqli_free_result($dbresult5);

  $request11 = db_query("
    SELECT c.ID_COMMUNITY, c.friendly_url, m.ID_COMMUNITY, m.ID_MEMBER
    FROM ({$db_prefix}communities AS c, {$db_prefix}community_members AS m)
    WHERE m.ID_MEMBER = $ID_MEMBER
    AND c.ID_COMMUNITY = m.ID_COMMUNITY", __FILE__, __LINE__);

  while ($row = mysqli_fetch_assoc($request11)) {
    $context['miscomunidades'][] = array(
      'ID' => $row['ID_COMMUNITY'],
      'titulo' => $row['friendly_url'],
    );
  }

  $requestnew = db_query("
    SELECT ID_MEMBER, realName, memberName, dateRegistered
    FROM {$db_prefix}members
    ORDER BY dateRegistered DESC
    LIMIT 10", __FILE__, __LINE__);

  $context['user_nuevos'] = array();

  while ($row = mysqli_fetch_assoc($requestnew))
    $context['user_nuevos'][] = array(
      'ID_MEMBER' => $row['ID_MEMBER'],
      'memberName' => $row['memberName'],
      'realName' => $row['realName'],
      'fecha' => $row['dateRegistered'],
    );

  mysqli_free_result($requestnew);
}

function crear() {
  global $context, $db_prefix, $ID_MEMBER;

  is_not_guest();
  loadlanguage('Post');

  $context['sub_template']  = 'crear';
  $context['page_title'] = 'Crear nueva comunidad';

  $dbresult3 = db_query("
    SELECT ID_CATEGORY, name
    FROM {$db_prefix}community_categories
    ORDER BY ID_CATEGORY ASC", __FILE__, __LINE__);

  $context['foro'] = array();

  while ($row = mysqli_fetch_assoc($dbresult3)) {
    $context['foro'][] = array(
      'ID_CATEGORY' => $row['ID_CATEGORY'],
      'name' => $row['name'],
    );
  }

  mysqli_free_result($dbresult3);

  $dbresult2 = db_query("
    SELECT ID_MEMBER
    FROM {$db_prefix}community_members
    WHERE ID_MEMBER = $ID_MEMBER
    LIMIT 1", __FILE__, __LINE__);

  $context['usercrear'] = mysqli_num_rows($dbresult2);
}

function crear2() {
  global $db_prefix, $ID_MEMBER, $boardurl, $context;

  @require_once('SSI.php');

  $request = db_query("
    SELECT *
    FROM {$db_prefix}communities
    WHERE ID_MEMBER = " . $ID_MEMBER, __FILE__, __LINE__);

  $count = mysqli_num_rows($request);

  ssi_grupos();

  if ($context['Turista']) {
    $result = 1 - $count;
  } else if ($context['Conocido']) {
    $result = 2 - $count;
  } else if ($context['Vecino']) {
    $result = 4 - $count;
  } else if ($context['Amigo']) {
    $result = 6 - $count;
  } else if ($context['Familiar']) {
    $result = 8 - $count;
  } else if ($context['Casero']) {
    $result = 10 - $count;
  } else if ($context['Abastecedor']) {
    $result = 15 - $count;
  } else if ($context['Heredero']) {
    $result = 15 - $count;
  } else if ($context['Hermano Mayor']) {
    $result = 15 - $count;
  } else if ($context['Padre']) {
    $result = 15 - $count;
  }

  if (!isset($_POST['nombre']) && !isset($_POST['shortname']) && !isset($_POST['imagen']) && !isset($_POST['categoria']) && !isset($_POST['descripcion']) && !isset($_POST['privada']) && !isset($_POST['rango_default'])) {
    fatal_error('Rellena todos los campos para poder crear tu comunidad.-', false);
  } else if ($result <= 0) {
    fatal_error('No puedes crear m&aacute;s comunidades.-', false);
  } else {
    $title = htmlentities(addslashes($_POST['nombre']), ENT_QUOTES, 'UTF-8');
    $shortname = htmlentities(addslashes($_POST['shortname']), ENT_QUOTES, 'UTF-8');
    $logo = htmlentities(addslashes($_POST['imagen']), ENT_QUOTES, 'UTF-8');
    $country = htmlentities(addslashes($_POST['pais']), ENT_QUOTES, 'UTF-8');
    $ID_CATEGORY = (int) $_POST['categoria'];
    $description = htmlentities(addslashes($_POST['descripcion']), ENT_QUOTES, 'UTF-8');
    $view = htmlentities(addslashes($_POST['privada']), ENT_QUOTES, 'UTF-8');
    $grade = htmlentities(addslashes($_POST['rango_default']), ENT_QUOTES, 'UTF-8');
    $date = time();

    $request = db_query("
      INSERT INTO {$db_prefix}communities (title, friendly_url, logo, ID_CATEGORY, description, view, grade, date, ID_MEMBER)
      VALUES ('$title', '$shortname', '$logo', $ID_CATEGORY, '$description', '$view', '$grade', $date, $ID_MEMBER)", __FILE__, __LINE__);

    $request5 = db_query("
      SELECT *
      FROM {$db_prefix}communities
      WHERE friendly_url = '$shortname'", __FILE__, __LINE__);

    $add = mysqli_fetch_assoc($request5);
    $ID_COMMUNITY = $add['ID_COMMUNITY'];
    $request3 = db_query("
      INSERT INTO {$db_prefix}community_members (ID_COMMUNITY, ID_MEMBER, grade, date, name)
      VALUES ($ID_COMMUNITY, $ID_MEMBER, 1, $date, '" . $context['user']['name'] . "')", __FILE__, __LINE__);

    $request4 = db_query("
      INSERT INTO {$db_prefix}community_user (ID_COMMUNITY, ID_MEMBER)
      VALUES ($ID_COMMUNITY, $ID_MEMBER)", __FILE__, __LINE__);

    if ($request3 && $request4) {
      redirectexit($boardurl . '/comunidades/' . $add['friendly_url'] . '/');
    }
  }
}

function editar() {
  global $txt, $context, $db_prefix, $ID_MEMBER, $boardurl;

  loadlanguage('Post');

  $context['sub_template'] = 'editar';
  $context['page_title'] = $txt[18];
  $id = htmlentities(addslashes($_REQUEST['id']), ENT_QUOTES, 'UTF-8');

  $result = db_query("
    SELECT ID_MEMBER
    FROM {$db_prefix}community_user
    WHERE ID_MEMBER = $ID_MEMBER
    LIMIT 1", __FILE__, __LINE__);

  $alreadyAdded = mysqli_num_rows($result) != 1;

  mysqli_free_result($result);

  if ($alreadyAdded) {
    fatal_error('No tienes permiso para editar esta comunidad', false);
  }

  $result = db_query("
    SELECT cm.ID_MEMBER, cm.ID_COMMUNITY, cm.grade, c.friendly_url, c.ID_COMMUNITY, cm.grade
    FROM ({$db_prefix}community_members AS cm, {$db_prefix}communities AS c)
    WHERE cm.ID_MEMBER = $ID_MEMBER
    AND cm.ID_COMMUNITY = c.ID_COMMUNITY
    AND c.friendly_url = '$id'
    LIMIT 1", __FILE__, __LINE__);

  $context['usercomunidad'] = mysqli_num_rows($result);
  $row = mysqli_fetch_assoc($result);
  $context['rango'] = array(
    'grade' => $row['grade'],
  );

  mysqli_free_result($result);

  $dbresult = db_query("
    SELECT
      c.ID_MEMBER, c.ID_COMMUNITY, c.description, c.oficial, c.ID_CATEGORY, c.view, c.grade, c.title, c.friendly_url, c.logo, c.numMembers, c.date,
      c.numPosts, b.ID_CATEGORY, b.name AS bname, b.friendly_url AS friendly_url2, mem.realName, mem.memberName, mem.ID_MEMBER
    FROM ({$db_prefix}communities AS c, {$db_prefix}community_categories AS b, {$db_prefix}members AS mem)
    WHERE mem.ID_MEMBER = c.ID_MEMBER
    AND c.friendly_url = '$id'
    AND c.ID_CATEGORY = b.ID_CATEGORY
    AND mem.ID_MEMBER = c.ID_MEMBER
    LIMIT 1", __FILE__, __LINE__);

  $row = mysqli_fetch_assoc($dbresult);

  $context['comunidad'] = array(
    'ID_COMMUNITY' => $row['ID_COMMUNITY'],
    'grade' => $row['grade'],
    'numMembers' => $row['numMembers'],
    'numPosts' => $row['numPosts'],
    'ID_MEMBER' => $row['ID_MEMBER'],
    'description' => $row['description'],
    'ID_CATEGORY' => $row['ID_CATEGORY'],
    'view' => $row['view'],
    'grade' => $row['grade'],
    'title' => $row['title'],
    'friendly_url' => $row['friendly_url'],
    'logo' => $row['logo'],
    'date' => $row['date'],
    'friendly_url2' => $row['friendly_url2'],
    'bname' => $row['bname'],
    'oficial' => $row['oficial'],
    'link_category' => '<a href="' . $boardurl . '/comunidades/home/' . $row['friendly_url2'] . '/" title="' . $row['bname'] . '">' . $row['bname'] . '</a>',
    'creator' => '<a title="Ver el perfil de ' . $row['realName'] . '" href="' . $boardurl . '/perfil/' . $row['memberName'] . '">' . $row['realName'] . '</a>',
  );

  mysqli_free_result($dbresult);

  $dbresult = db_query("
    SELECT c.ID_MEMBER, c.ID_COMMUNITY, c.description, c.ID_CATEGORY, c.view, c.grade, c.title, c.friendly_url, c.logo, mem.ID_MEMBER
    FROM {$db_prefix}communities AS c, {$db_prefix}members AS mem
    WHERE c.ID_MEMBER = mem.ID_MEMBER
    AND c.friendly_url = '$id'
    LIMIT 1", __FILE__, __LINE__);

  $row = mysqli_fetch_assoc($dbresult);
  $context['editor'] = array(
    'ID_COMMUNITY' => $row['ID_COMMUNITY'],
    'ID_MEMBER' => $row['ID_MEMBER'],
    'ID_CATEGORY' => $row['ID_CATEGORY'],
    'grade' => $row['grade'],
    'view' => $row['view'],
    'title' => $row['title'],
    'friendly_url' => $row['friendly_url'],
    'logo' => $row['logo'],
    'description' => $row['description'],
  );

  mysqli_free_result($dbresult);

  $dbresult3 = db_query("
    SELECT ID_CATEGORY, name
    FROM {$db_prefix}community_categories
    ORDER BY ID_CATEGORY DESC", __FILE__, __LINE__);

  $context['foro'] = array();
  while ($row = mysqli_fetch_assoc($dbresult3)) {
    $context['foro'][] = array(
      'ID_CATEGORY' => $row['ID_CATEGORY'],
      'name' => $row['name'],
    );
  }

  mysqli_free_result($dbresult3);
}

function editar1() {
  global $txt, $context, $db_prefix, $boardurl;

  is_not_guest();
  loadlanguage('Post');

  $context['page_title'] = $txt[18];
  $title = htmlentities(addslashes($_POST['nombre']), ENT_QUOTES, 'UTF-8');
  $logo = htmlentities(addslashes($_POST['imagen']), ENT_QUOTES, 'UTF-8');
  $ID_CATEGORY = (int) $_POST['categoria'];
  $description = htmlentities(addslashes($_POST['descripcion']), ENT_QUOTES, 'UTF-8');
  $view = (int) $_POST['privada'];
  $grade = (int) $_POST['rango_default'];
  $ID_COMMUNITY = (int) $_POST['idcom'];

  if (!empty($title) && !empty($logo) && !empty($ID_CATEGORY) && !empty($description) && !empty($view) && !empty($grade) && !empty($ID_COMMUNITY)) {
    $request = db_query("
      SELECT *
      FROM {$db_prefix}communities
      WHERE ID_COMMUNITY = $ID_COMMUNITY", __FILE__, __LINE__);

    $row = mysqli_fetch_assoc($request);

    mysqli_free_result($request);

    $request = db_query("
      UPDATE {$db_prefix}communities
      SET view = $view, grade = $grade, ID_CATEGORY = $ID_CATEGORY, title = '$title', logo = '$logo', description = '$description'
      WHERE ID_COMMUNITY = $ID_COMMUNITY
      LIMIT 1", __FILE__, __LINE__);

    // TO-DO: ¿Llamar aquí a mysqli_free_result() ?
    if ($request) {
      redirectexit($boardurl . '/comunidades/' . $row['friendly_url']);
    }
  } else {
    fatal_error('Rellena todos los campos para editar tu comunidad', false);
  }
}

function borrar() {
  global $context, $db_prefix;

  is_not_guest();
  loadlanguage('Post');

  $context['sub_template'] = 'borrar';
  $context['page_title'] = ' Borrar Comunidad';

  $id = htmlentities(addslashes($_GET['id']), ENT_QUOTES, 'UTF-8');

  $dbresult = db_query("
    SELECT c.ID_MEMBER, c.ID_COMMUNITY, c.description, c.ID_CATEGORY, c.view, c.grade, c.title, c.friendly_url, c.logo, c.numMembers, mem.ID_MEMBER
    FROM ({$db_prefix}communities AS c, {$db_prefix}members AS mem)
    WHERE c.ID_MEMBER = mem.ID_MEMBER
    AND friendly_url = '$id'
    LIMIT 1", __FILE__, __LINE__);

  $row = mysqli_fetch_assoc($dbresult);

  $context['editor'] = array(
    'ID_COMMUNITY' => $row['ID_COMMUNITY'],
    'ID_MEMBER' => $row['ID_MEMBER'],
    'numMembers' => $row['numMembers'],
    'ID_CATEGORY' => $row['ID_CATEGORY'],
    'view' => $row['view'],
    'grade' => $row['grade'],
    'title' => $row['title'],
    'friendly_url' => $row['friendly_url'],
    'logo' => $row['logo'],
    'description' => $row['description'],
  );

  mysqli_free_result($dbresult);
}

function borrar2() {
  global $context, $db_prefix, $boardurl;

  is_not_guest();
  loadlanguage('Post');

  $id = htmlentities(addslashes($_REQUEST['id']), ENT_QUOTES, 'UTF-8');
  $context['page_title'] = 'Borrar Comunidad';

  $request = db_query("
    SELECT *
    FROM ({$db_prefix}community_user AS cu, {$db_prefix}communities AS c)
    WHERE cu.ID_MEMBER = c.ID_MEMBER
    AND c.friendly_url = '$id'
    LIMIT 1", __FILE__, __LINE__);

  $alreadyAdded = mysqli_num_rows($request) != 1;

  mysqli_free_result($request);

  $request = db_query("
    SELECT *
    FROM ({$db_prefix}communities AS c, {$db_prefix}members AS mem)
    WHERE c.ID_MEMBER = mem.ID_MEMBER
    AND c.friendly_url = '$id'
    LIMIT 1", __FILE__, __LINE__);

  $row = mysqli_fetch_assoc($request);

  $context['adminmiembro1'] = array(
    'ID_COMMUNITY' => $row['ID_COMMUNITY'],
  );

  mysqli_free_result($request);

  $id_comunidad = $context['adminmiembro1']['ID_COMMUNITY'];

  if ($alreadyAdded) {
    fatal_error('No puedes eliminar esta comunidad', false);
  } else {
    db_query("
      DELETE FROM {$db_prefix}community_user
      WHERE ID_COMMUNITY = $id_comunidad
      LIMIT 1", __FILE__, __LINE__);

    db_query("
      DELETE FROM {$db_prefix}community_members
      WHERE ID_COMMUNITY = " . $id_comunidad, __FILE__, __LINE__);

    db_query("
      DELETE FROM {$db_prefix}community_topic
      WHERE ID_COMMUNITY = " . $id_comunidad, __FILE__, __LINE__);

    db_query("
      DELETE FROM {$db_prefix}community_comment
      WHERE ID_COMMUNITY = " . $id_comunidad, __FILE__, __LINE__);

    // TO-DO: Esta debería ser la última tabla en eliminarse
    $request = db_query("
      DELETE FROM {$db_prefix}communities
      WHERE friendly_url = '$id'", __FILE__, __LINE__);

    if ($request) {
      redirectexit($boardurl . '/comunidades/');
    }
  }
}

function comunidad() {
  global $txt, $context, $db_prefix, $ID_MEMBER, $boardurl;

  loadlanguage('Post');
  $context['sub_template'] = 'comunidad';

  $id = htmlentities(addslashes($_REQUEST['id']), ENT_QUOTES, 'UTF-8');

  $temasimpo = db_query("
    SELECT ct.ID_TOPIC, ct.ID_MEMBER, ct.isSticky, ct.ID_COMMUNITY, ct.locked, ct.subject, ct.posterTime, c.friendly_url, c.ID_COMMUNITY
    FROM ({$db_prefix}community_topic AS ct, {$db_prefix}communities AS c)
    WHERE ct.ID_COMMUNITY = c.ID_COMMUNITY
    AND c.friendly_url = '$id'
    AND ct.isSticky = 1
    ORDER BY ct.isSticky  = 1 DESC, ct.posterTime DESC", __FILE__, __LINE__);

  while ($row = mysqli_fetch_assoc($temasimpo)) {
    $context['temasimpo'][] = array(
      'ID_TOPIC' => $row['ID_TOPIC'],
      'locked' => $row['locked'],
      'id_member' => $row['ID_MEMBER'],
      'subject' => shorten_subject($row['subject'], 36),
      'posterName' => $row['posterName'],
      'time' => $row['posterTime'],
      'isSticky' => $row['isSticky'],
    );
  }

  mysqli_free_result($temasimpo);

  $dbresult0 = db_query("
    SELECT ID_CATEGORY, name
    FROM {$db_prefix}community_categories
    ORDER BY ID_CATEGORY DESC", __FILE__, __LINE__);

  while ($row = mysqli_fetch_assoc($dbresult0)) {
    $context['foro'][] = array(
      'ID_CATEGORY' => $row['ID_CATEGORY'],
      'name' => $row['name'],
    );
  }

  mysqli_free_result($dbresult0);

  $result = db_query("
    SELECT friendly_url
    FROM {$db_prefix}communities
    WHERE friendly_url = '$id'
    LIMIT 1", __FILE__, __LINE__);

  $context['existecomunidad'] = mysqli_num_rows($result);

  $result = db_query("
    SELECT cm.ID_MEMBER, cm.ID_COMMUNITY, cm.grade, c.friendly_url, c.ID_COMMUNITY, cm.grade
    FROM ({$db_prefix}community_members AS cm, {$db_prefix}communities AS c)
    WHERE cm.ID_MEMBER = $ID_MEMBER
    AND cm.ID_COMMUNITY = c.ID_COMMUNITY
    AND c.friendly_url = '$id'
    LIMIT 1", __FILE__, __LINE__);

  $context['usercomunidad'] = mysqli_num_rows($result);
  $row = mysqli_fetch_assoc($result);

  $context['rango'] = array(
    'grade' => $row['grade'],
  );

  mysqli_free_result($result);

  $dbresult = db_query("
    SELECT c.ID_MEMBER, c.ID_COMMUNITY, c.description, c.oficial, c.ID_CATEGORY, c.view, c.grade, c.title, c.friendly_url, c.logo, c.numMembers, c.date,
    c.numPosts, b.ID_CATEGORY, b.name AS bname, b.friendly_url AS friendly_url2, mem.realName, mem.memberName, mem.ID_MEMBER
    FROM ({$db_prefix}communities AS c, {$db_prefix}community_categories AS b, {$db_prefix}members AS mem)
    WHERE mem.ID_MEMBER = c.ID_MEMBER
    AND c.friendly_url = '$id'
    AND c.ID_CATEGORY = b.ID_CATEGORY
    AND mem.ID_MEMBER = c.ID_MEMBER
    LIMIT 1", __FILE__, __LINE__);

  $row = mysqli_fetch_assoc($dbresult);

  $context['comunidad'] = array(
    'ID_COMMUNITY' => $row['ID_COMMUNITY'],
    'grade' => $row['grade'],
    'numMembers' => $row['numMembers'],
    'numPosts' => $row['numPosts'],
    'ID_MEMBER' => $row['ID_MEMBER'],
    'description' => $row['description'],
    'ID_CATEGORY' => $row['ID_CATEGORY'],
    'view' => $row['view'],
    'grade' => $row['grade'],
    'title' => $row['title'],
    'friendly_url' => $row['friendly_url'],
    'logo' => $row['logo'],
    'date' => $row['date'],
    'friendly_url2' => $row['friendly_url2'],
    'bname' => $row['bname'],
    'oficial' => $row['oficial'],
    'link_category' => '<a href="' . $boardurl . '/comunidades/categoria/' . $row['friendly_url2'] . '" title="' . $row['bname'] . '" alt="' . $row['bname'] . '">' . $row['bname'] . '</a>',
    'creator' => '<td  style="padding:4px;" title="' . $row['realName'] . '" alt="' . $row['realName'] . '"><a href="' . $boardurl . '/perfil/' . $row['memberName'] . '" title="' . $row['realName'] . '" alt="' . $row['realName'] . '">' . $row['realName'] . '</a></td>',
  );

  mysqli_free_result($dbresult);

  if (!empty($context['existecomunidad'])) {
    $context['page_title'] = 'Comunidad de ' . $context['comunidad']['title'];
  } else if (empty($context['existecomunidad'])) {
    $context['page_title'] = $txt[18];
    fatal_error('La comunidad no existe.-', false);
  }

  $dbresult = db_query("
    SELECT cm.ID_COMMUNITY, cm.ID_MEMBER, cm.name, c.ID_COMMUNITY, c.friendly_url
    FROM {$db_prefix}community_members AS cm, {$db_prefix}communities AS c
    WHERE cm.ID_COMMUNITY = c.ID_COMMUNITY
    AND c.friendly_url = '$id'
    ORDER BY cm.ID_MEMBER DESC
    LIMIT 15", __FILE__, __LINE__);

  while ($row = mysqli_fetch_assoc($dbresult)) {
    $context['comunidades'][] = array(
      'ID_COMMUNITY' => $row['ID_COMMUNITY'],
      'ID_MEMBER' => $row['ID_MEMBER'],
      'name' => $row['name'],
    );
  }

  mysqli_free_result($dbresult);

  $dbresult3 = db_query("
    SELECT ct.ID_TOPIC, ct.ID_COMMUNITY, cc.ID_TOPIC, cc.posterName, cc.ID_MEMBER, cc.ID_COMMUNITY, c.ID_COMMUNITY, c.friendly_url
    FROM ({$db_prefix}community_topic AS ct, {$db_prefix}community_comments AS cc, {$db_prefix}communities AS c)
    WHERE ct.ID_COMMUNITY = cc.ID_COMMUNITY
    AND cc.ID_COMMUNITY = c.ID_COMMUNITY
    AND c.ID_COMMUNITY = ct.ID_COMMUNITY
    AND cc.ID_COMMUNITY = '$id'
    AND ct.ID_TOPIC = cc.ID_TOPIC
    ORDER BY cc.ID_COMMENT DESC
    LIMIT 15", __FILE__, __LINE__);

  while ($row = mysqli_fetch_assoc($dbresult3)) {
    $context['comentarios'][] = array(
      'ID_TOPIC' => $row['ID_TOPIC'],
      'titulo' => shorten_subject($row['subject'], 23),
      'memberName' => $row['posterName'],
      'ID_MEMBER' => $row['ID_MEMBER'],
    );
  }

  mysqli_free_result($dbresult3);
}

function vermiembros() {
  global $context, $db_prefix, $ID_MEMBER;

  loadlanguage('Post');
  $context['sub_template'] = 'vermiembros';

  $id = htmlentities(addslashes($_REQUEST['id']), ENT_QUOTES, 'UTF-8');

  // Para saber si eres administrador
  $result = db_query("
    SELECT cm.ID_MEMBER, cm.ID_COMMUNITY, cm.grade, c.friendly_url, c.ID_COMMUNITY
    FROM ({$db_prefix}community_members AS cm, {$db_prefix}communities AS c)
    WHERE cm.ID_MEMBER = $ID_MEMBER
    AND cm.ID_COMMUNITY = c.ID_COMMUNITY
    AND c.friendly_url = '$id'
    LIMIT 1", __FILE__, __LINE__);

  $context['usercomunidad'] = mysqli_num_rows( $result);
  $row = mysqli_fetch_assoc($result);
  $context['rango'] = array(
    'grade' => $row['grade'],
  );

  mysqli_free_result($result);

  // ¿Quién es administrador?
  $dbresult3 = db_query("
    SELECT cu.ID_COMMUNITY, cu.ID_MEMBER, c.friendly_url, c.ID_COMMUNITY
    FROM ({$db_prefix}community_user AS cu, {$db_prefix}communities AS c)
    WHERE cu.ID_COMMUNITY = c.ID_COMMUNITY
    AND c.friendly_url = '$id'
    LIMIT 1", __FILE__, __LINE__);

  $row = mysqli_fetch_assoc($dbresult3);
  $context['selectadmin'] = array(
    'ID_MEMBER' => $row['ID_MEMBER'],
  );

  mysqli_free_result($dbresult3);

  $result = db_query("
    SELECT cm.ID_MEMBER, cm.ID_COMMUNITY, cm.grade, c.friendly_url, c.ID_COMMUNITY, cm.grade
    FROM ({$db_prefix}community_members AS cm, {$db_prefix}communities AS c)
    WHERE cm.ID_MEMBER = $ID_MEMBER
    AND cm.ID_COMMUNITY = c.ID_COMMUNITY
    AND c.friendly_url = '$id'
    LIMIT 1", __FILE__, __LINE__);

  $context['usercomunidad'] = mysqli_num_rows($result);
  $row = mysqli_fetch_assoc($result);
  $context['rango'] = array(
    'grade' => $row['grade'],
  );

  mysqli_free_result($result);

  $dbresult = db_query("
    SELECT
      c.ID_MEMBER, c.ID_COMMUNITY, c.description, c.oficial, c.ID_CATEGORY, c.view, c.grade, c.title, c.friendly_url, c.logo, c.numMembers,
      c.date, c.numPosts, b.ID_CATEGORY, b.name AS bname, b.friendly_url AS friendly_url2, mem.realName, mem.memberName, mem.ID_MEMBER
    FROM ({$db_prefix}communities AS c, {$db_prefix}community_categories AS b, {$db_prefix}members AS mem)
    WHERE mem.ID_MEMBER = c.ID_MEMBER
    AND c.friendly_url = '$id'
    AND c.ID_CATEGORY = b.ID_CATEGORY
    AND mem.ID_MEMBER = c.ID_MEMBER
    LIMIT 1", __FILE__, __LINE__);

  $row = mysqli_fetch_assoc($dbresult);

  $context['comunidad'] = array(
    'ID_COMMUNITY' => $row['ID_COMMUNITY'],
    'grade' => $row['grade'],
    'numMembers' => $row['numMembers'],
    'numPosts' => $row['numPosts'],
    'ID_MEMBER' => $row['ID_MEMBER'],
    'description' => $row['description'],
    'ID_CATEGORY' => $row['ID_CATEGORY'],
    'view' => $row['view'],
    'grade' => $row['grade'],
    'title' => $row['title'],
    'friendly_url' => $row['friendly_url'],
    'logo' => $row['logo'],
    'date' => $row['date'],
    'friendly_url2' => $row['friendly_url2'],
    'bname' => $row['bname'],
    'oficial' => $row['oficial'],
  );

  mysqli_free_result($dbresult);

  $context['page_title'] = $context['comunidad']['title'];

  $dbresult = db_query("
    SELECT cm.ID_COMMUNITY, cm.ID_MEMBER, cm.grade, cm.date, cm.name, m.ID_MEMBER, m.avatar, c.ID_COMMUNITY, c.friendly_url
    FROM {$db_prefix}community_members AS cm, {$db_prefix}members as m, {$db_prefix}communities AS c
    WHERE cm.ID_COMMUNITY = c.ID_COMMUNITY
    AND c.friendly_url = '$id'
    AND m.ID_MEMBER = cm.ID_MEMBER", __FILE__, __LINE__);

  while ($row = mysqli_fetch_assoc($dbresult)) {
    $context['comunidades'][] = array(
      'ID_COMMUNITY' => $row['ID_COMMUNITY'],
      'ID_MEMBER' => $row['ID_MEMBER'],
      'grade' => $row['grade'],
      'date' => $row['date'],
      'name' => $row['name'],
      'avatar' => $row['avatar'],
    );
  }

  mysqli_free_result($dbresult);
}

function adminmiembro() {
  global $context, $db_prefix, $ID_MEMBER, $context;

  is_not_guest();
  loadlanguage('Post');

  $context['sub_template']  = 'adminmiembro';
  $id = htmlentities(addslashes($_REQUEST['id']), ENT_QUOTES, 'UTF-8');
  $us = htmlentities(addslashes($_REQUEST['us']), ENT_QUOTES, 'UTF-8');

  $dbresult = db_query("
    SELECT
      c.ID_MEMBER, c.ID_COMMUNITY, c.description, c.oficial, c.ID_CATEGORY, c.view, c.grade, c.title, c.friendly_url, c.logo, c.numMembers,
      c.date, c.numPosts, b.ID_CATEGORY, b.name AS bname, b.friendly_url AS friendly_url2, mem.realName, mem.memberName, mem.ID_MEMBER
    FROM ({$db_prefix}communities AS c, {$db_prefix}community_categories AS b, {$db_prefix}members AS mem)
    WHERE mem.ID_MEMBER = c.ID_MEMBER
    AND c.friendly_url = '$id'
    AND c.ID_CATEGORY = b.ID_CATEGORY
    AND mem.ID_MEMBER = c.ID_MEMBER
    LIMIT 1", __FILE__, __LINE__);

  $row = mysqli_fetch_assoc($dbresult);

  $context['comunidad'] = array(
    'ID_COMMUNITY' => $row['ID_COMMUNITY'],
    'grade' => $row['grade'],
    'numMembers' => $row['numMembers'],
    'numPosts' => $row['numPosts'],
    'ID_MEMBER' => $row['ID_MEMBER'],
    'description' => $row['description'],
    'ID_CATEGORY' => $row['ID_CATEGORY'],
    'view' => $row['view'],
    'grade' => $row['grade'],
    'title' => $row['title'],
    'friendly_url' => $row['friendly_url'],
    'logo' => $row['logo'],
    'date' => $row['date'],
    'friendly_url2' => $row['friendly_url2'],
    'bname' => $row['bname'],
    'oficial' => $row['oficial'],
  );

  mysqli_free_result($dbresult);

  $context['page_title'] = $context['comunidad']['title'];
  $result = db_query("
    SELECT cm.ID_MEMBER, cm.ID_COMMUNITY, cm.grade, c.friendly_url, c.ID_COMMUNITY, cm.grade
    FROM ({$db_prefix}community_members AS cm, {$db_prefix}communities AS c)
    WHERE cm.ID_MEMBER = $ID_MEMBER
    AND cm.ID_COMMUNITY = c.ID_COMMUNITY
    AND c.friendly_url = '$id'
    LIMIT 1", __FILE__, __LINE__);
    
  $context['usercomunidad'] = mysqli_num_rows($result);
  $row = mysqli_fetch_assoc($result);
  $context['rango'] = array(
    'grade' => $row['grade'],
  );

  mysqli_free_result($result);

  $request = db_query("
    SELECT cm.ID_COMMUNITY, cm.ID_MEMBER, cm.grade, cm.date, cm.name, mem.ID_MEMBER, mem.avatar, cm.name, mem.memberName, c.ID_COMMUNITY, c.friendly_url
    FROM ({$db_prefix}community_members AS cm, {$db_prefix}members AS mem, {$db_prefix}communities AS c)
    WHERE cm.ID_COMMUNITY = c.ID_COMMUNITY
    AND mem.memberName = cm.name
    AND cm.ID_MEMBER = mem.ID_MEMBER
    AND c.friendly_url = '$id'
    AND mem.memberName = '$us'
    LIMIT 1", __FILE__, __LINE__);

  $context['conteomiembro'] = mysqli_num_rows($request);
  $rowmem = mysqli_fetch_assoc($request);
  $context['adminmiembro'] = array(
    'ID_MEMBER' => $rowmem['ID_MEMBER'],
    'grade' => $rowmem['grade'],
    'date' => $rowmem['date'],
    'name' => $rowmem['memberName'],
    'avatar' => $rowmem['avatar'],
    );

  mysqli_free_result($request);
}

function adminmiembro2() {
  global $context, $db_prefix, $ID_MEMBER, $boardurl;

  is_not_guest();
  loadlanguage('Post');

  $rango = (int) $_REQUEST['rango'];
  $miembro = (int) $_REQUEST['miembro-cuestion'];
  $banear = (int) $_REQUEST['banear'];
  $desbanear = (int) $_REQUEST['desbanear'];
  $expira = (int) $_REQUEST['expira'];
  $id = htmlentities(addslashes($_REQUEST['id']), ENT_QUOTES, 'UTF-8');
  $razon = htmlentities(addslashes($_REQUEST['razon']), ENT_QUOTES, 'UTF-8');
  $modName = $context['user']['name'];
  $tiempo1 = $expira * 86400;
  $tiempo2 = time() + $tiempo1;

  $request = db_query("
    SELECT *
    FROM {$db_prefix}communities
    WHERE friendly_url = '$id'", __FILE__, __LINE__);

  $row2 = mysqli_fetch_assoc($request);

  $comunidad = $row2['ID_COMMUNITY'];

  $dbresult3 = db_query("
    SELECT * FROM {$db_prefix}community_members
    WHERE ID_MEMBER = $miembro
    AND ID_COMMUNITY = $comunidad ", __FILE__, __LINE__);

  $row = mysqli_fetch_assoc($dbresult3);

  $result = db_query("
    SELECT cm.ID_MEMBER, cm.ID_COMMUNITY, cm.grade, c.friendly_url, c.ID_COMMUNITY, cm.grade
    FROM ({$db_prefix}community_members AS cm, {$db_prefix}communities AS c)
    WHERE cm.ID_MEMBER = $ID_MEMBER
    AND cm.ID_COMMUNITY = c.ID_COMMUNITY
    AND c.friendly_url = '$id'
    LIMIT 1", __FILE__, __LINE__);

  $context['usercomunidad'] = mysqli_num_rows($result);
  $rowk = mysqli_fetch_assoc($result);
  $context['rango'] = array(
    'grade' => $rowk['grade'],
  );

  mysqli_free_result($result);

  $request = db_query("
    SELECT ID_MEMBER, ID_COMMUNITY
    FROM {$db_prefix}community_members
    WHERE ID_MEMBER = " . $row['ID_MEMBER'] . "
    AND ID_COMMUNITY = " . $comunidad . "
    LIMIT 1", __FILE__, __LINE__);

  $alreadyAdded = mysqli_num_rows($request);

  mysqli_free_result($request);

  if ($alreadyAdded <= 0) {
    fatal_error('Este usuario no existe en tu comunidad.', false);
  } else if ($context['allow_admin'] || $context['rango']['grade'] == 1 && $context['usercomunidad'] == 1) {
    $result1 = db_query("
      UPDATE {$db_prefix}community_members
      SET grade = $rango
      WHERE ID_MEMBER = " . $row['ID_MEMBER'] . "
      AND ID_COMMUNITY = " . $comunidad . "
      LIMIT 1", __FILE__, __LINE__);
    if ($desbanear == 1) {
      db_query("
        DELETE FROM {$db_prefix}community_banned
        WHERE ID_COMMUNITY = $comunidad
        AND ID_MEMBER = " . $row['ID_MEMBER'], __FILE__, __LINE__);
    } else if ($banear == 1 && !empty($tiempo2) && !empty($expira)) {
      db_query("
        INSERT INTO {$db_prefix}community_banned (modName, ID_MEMBER, ID_COMMUNITY, reason, expire, day)
        VALUES ('$modName', $miembro, $comunidad, '$razon', $tiempo2, $expira)", __FILE__, __LINE__);
    }
    // TO-DO: ¿Es necesario este caso?
    /*
    else if ($banear == 1 && !empty($tiempo2) && !empty($expira)) {
      db_query("
        INSERT INTO {$db_prefix}community_banned (modName, ID_MEMBER, ID_COMMUNITY, reason, expire, day)
        VALUES ('$modName', $miembro, $comunidad, '99999', $tiempo2, $expira)", __FILE__, __LINE__);
    }
    */

    if ($result1) {
      redirectexit($boardurl . '/comunidades/' . $row2['friendly_url'] . '/miembros');
    }
  }
}

function denunciar() {
  global $context, $db_prefix, $ID_MEMBER;

  loadlanguage('Post');

  $context['sub_template'] = 'denunciar';
  $id = htmlentities(addslashes($_REQUEST['id']), ENT_QUOTES, 'UTF-8');

  $request = db_query("
    SELECT
      c.ID_MEMBER, c.ID_COMMUNITY, c.description, c.oficial, c.ID_CATEGORY, c.view, c.grade, c.title, c.friendly_url, c.logo, c.numMembers,
      c.date, c.numPosts, b.ID_CATEGORY, b.name AS bname, b.friendly_url AS friendly_url2, mem.realName, mem.memberName, mem.ID_MEMBER
    FROM ({$db_prefix}communities AS c, {$db_prefix}community_categories AS b, {$db_prefix}members AS mem)
    WHERE mem.ID_MEMBER = c.ID_MEMBER
    AND c.friendly_url = '$id'
    AND c.ID_CATEGORY = b.ID_CATEGORY
    AND mem.ID_MEMBER = c.ID_MEMBER
    LIMIT 1", __FILE__, __LINE__);

  $row = mysqli_fetch_assoc($request);

  $context['comunidad'] = array(
    'ID_COMMUNITY' => $row['ID_COMMUNITY'],
    'grade' => $row['grade'],
    'numMembers' => $row['numMembers'],
    'numPosts' => $row['numPosts'],
    'ID_MEMBER' => $row['ID_MEMBER'],
    'description' => $row['description'],
    'ID_CATEGORY' => $row['ID_CATEGORY'],
    'view' => $row['view'],
    'grade' => $row['grade'],
    'title' => $row['title'],
    'friendly_url' => $row['friendly_url'],
    'logo' => $row['logo'],
    'date' => $row['date'],
    'friendly_url2' => $row['friendly_url2'],
    'bname' => $row['bname'],
    'oficial' => $row['oficial'],
  );

  mysqli_free_result($request);

  $request = db_query("
    SELECT cm.ID_MEMBER, cm.ID_COMMUNITY, cm.grade, c.friendly_url, c.ID_COMMUNITY, cm.grade
    FROM ({$db_prefix}community_members AS cm, {$db_prefix}communities AS c)
    WHERE cm.ID_MEMBER = $ID_MEMBER
    AND cm.ID_COMMUNITY = c.ID_COMMUNITY
    AND c.friendly_url = '$id'
    LIMIT 1", __FILE__, __LINE__);

  $context['usercomunidad'] = mysqli_num_rows($request);
  $row = mysqli_fetch_assoc($request);
  $context['rango'] = array(
    'grade' => $row['grade'],
  );

  mysqli_free_result($request);

  $request = db_query("
    SELECT ID_MEMBER, ID_COMMUNITY, description, ID_CATEGORY, view, grade, title, friendly_url, logo
    FROM {$db_prefix}communities
    WHERE friendly_url = '$id'
    LIMIT 1", __FILE__, __LINE__);

  $row = mysqli_fetch_assoc($request);
  $context['editor'] = array(
    'ID_COMMUNITY' => $row['ID_COMMUNITY'],
    'ID_MEMBER' => $row['ID_MEMBER'],
    'ID_CATEGORY' => $row['ID_CATEGORY'],
    'view' => $row['view'],
    'grade' => $row['grade'],
    'titulo' => $row['titulo'],
    'title' => $row['title'],
    'logo' => $row['logo'],
    'description' => $row['description'],
  );

  mysqli_free_result($request);

  $context['page_title'] = $context['editor']['title'];
}

function notema() {
  global $context;

  loadlanguage('Post');

  $context['sub_template']  = 'notema';
  $context['page_title'] = 'Comunidad';
}

function editar_adm() {
  global $context, $db_prefix;

  is_not_guest();
  loadlanguage('Post');

  $context['sub_template']  = 'editar_adm';
  $context['page_title'] = 'Editar Comunidad';

  $id = (int) $_GET['id'];
  $id_member = $context['user']['id'];

  $request = db_query("
    SELECT id_user, id_comunidad, descripcion, id_categoria, ver, rango, titulo, titulocorto, logo, blok
    FROM {$db_prefix}comunidades
    WHERE id_comunidad = $id
    LIMIT 1", __FILE__, __LINE__);

  $row = mysqli_fetch_assoc($request);

  $context['editor'] = array(
    'id_comunidad' => $row['id_comunidad'],
    'id_user' => $row['id_user'],
    'id_categoria' => $row['id_categoria'],
    'ver' => $row['ver'],
    'rango' => $row['rango'],
    'titulo' => $row['titulo'],
    'titulocorto' => $row['titulocorto'],
    'logo' => $row['logo'],
    'blok' => $row['blok'],
    'descripcion' => $row['descripcion'],
  );

  mysqli_free_result($request);

  $request = db_query("
    SELECT ID_CATEGORY, name, friendly_url
    FROM {$db_prefix}c_categories
    ORDER BY ID_CATEGORY DESC", __FILE__, __LINE__);

  while ($row = mysqli_fetch_assoc($request)) {
    $context['foro'][] = array(
      'name' => $row['name'],
      'ID_BOARD' => $row['ID_BOARD'],
      'friendly_url' => $row['friendly_url'],
    );
  }

  mysqli_free_result($request);
}

function editar1_adm() {
  global $context, $db_prefix;

  is_not_guest();
  loadlanguage('Post');

  $context['sub_template']  = 'editar1_adm';
  $context['page_title'] = ' Editar Comunidad';

  $id = (int) $_GET['id'];
  $ver = (int) $_REQUEST['ver'];
  $rango = (int) $_REQUEST['rango'];
  $id_categoria = (int) $_REQUEST['id_categoria'];
  $titulo = htmlspecialchars($_REQUEST['titulo'], ENT_QUOTES, 'UTF-8');
  $titulocorto = htmlspecialchars($_REQUEST['titulocorto'], ENT_QUOTES, 'UTF-8');
  $logo = htmlspecialchars($_REQUEST['logo'], ENT_QUOTES, 'UTF-8');
  $descripcion = htmlspecialchars($_REQUEST['comment'], ENT_QUOTES, 'UTF-8');
  $blok = htmlspecialchars($_REQUEST['blok'], ENT_QUOTES, 'UTF-8');

  $result1 = db_query("
    UPDATE {$db_prefix}comunidades
    SET ver = $ver, rango = $rango, blok = '$blok', id_categoria = $id_categoria, titulo = '$titulo', titulocorto = '$titulocorto', logo = '$logo', descripcion = '$descripcion'
    WHERE id_comunidad = $id
    LIMIT 1", __FILE__, __LINE__);

  if ($result1) {
    redirectexit('action=comunidades;sa=editar2_adm;id='. $id);
  }
}

function editar2_adm() {
  global $context, $db_prefix;

  is_not_guest();
  loadlanguage('Post');

  $context['sub_template'] = 'editar2_adm';
  $context['page_title'] = 'Comunidad';

  $id_member = $context['user']['id'];

  $dbresult = db_query("
    SELECT id_user, id_comunidad
    FROM {$db_prefix}comunidades
    WHERE id_user = $id_member
    LIMIT 1", __FILE__, __LINE__);

  $row = mysqli_fetch_assoc($dbresult);

  $context['editor'] = array(
    'id_comunidad' => $row['id_comunidad'],
  );

  mysqli_free_result($dbresult);
}

function borrar_adm() {
  global $context, $db_prefix;

  is_not_guest();
  loadlanguage('Post');

  $id = (int) $_GET['id'];
  $context['sub_template'] = 'borrar_adm';

  $context['page_title'] = 'Borrar Comunidad';

  $id_member = $context['user']['id'];

  $request = db_query("
    SELECT id_user, id_comunidad, descripcion, id_categoria, ver, rango, titulo, titulocorto, logo, miembros
    FROM {$db_prefix}comunidades
    WHERE id_comunidad = $id
    LIMIT 1", __FILE__, __LINE__);

  $row = mysqli_fetch_assoc($request);

  $context['editor'] = array(
    'id_comunidad' => $row['id_comunidad'],
    'id_user' => $row['id_user'],
    'miembros' => $row['miembros'],
    'id_categoria' => $row['id_categoria'],
    'ver' => $row['ver'],
    'rango' => $row['rango'],
    'titulo' => $row['titulo'],
    'titulocorto' => $row['titulocorto'],
    'logo' => $row['logo'],
    'descripcion' => $row['descripcion'],
  );

  mysqli_free_result($request);
}

function borrar2_adm() {
  global $context, $db_prefix;

  is_not_guest();
  loadlanguage('Post');

  $context['page_title'] = 'Borrar Comunidad';

  $id = (int) $_GET['id'];
 
  $request = db_query("
    SELECT id_user, id_comunidad
    FROM {$db_prefix}comunidades
    WHERE id_comunidad = $id
    LIMIT 1", __FILE__, __LINE__);

  $row = mysqli_fetch_assoc($request);

  $context['user'] = array(
    'id_user' => $row['id_user'],
  );

  mysqli_free_result($request);

  $id_user = $context['user']['id_user'];

  db_query("
    DELETE FROM {$db_prefix}comunidades_user
    WHERE id_user = $id_user
    LIMIT 1", __FILE__, __LINE__);

  db_query("
    DELETE FROM {$db_prefix}comunidades_miembros
    WHERE id_comunidades = $id", __FILE__, __LINE__);

  $result1 = db_query("
    DELETE FROM {$db_prefix}comunidades
    WHERE id_comunidad = $id", __FILE__, __LINE__);

  if ($result1) {
    redirectexit('action=comunidades;sa=borrar3_adm;id='. $id);
  }
}

function borrar3_adm() {
  global $context;

  is_not_guest();
  loadlanguage('Post');

  $context['sub_template'] = 'borrar3_adm';
  $context['page_title'] = 'Borrar Comunidad';
}

function nuevotema() {
  global $context, $txt, $ID_MEMBER, $db_prefix;

  is_not_guest();
  loadlanguage('Post');

  $context['sub_template'] = 'nuevotema';
  $context['page_title'] = $txt[18];

  $id = htmlentities(addslashes($_GET['id']), ENT_QUOTES, 'UTF-8');

  $request = db_query("
    SELECT cm.ID_MEMBER, cm.ID_COMMUNITY, cm.grade, c.friendly_url, c.ID_COMMUNITY, cm.grade
    FROM ({$db_prefix}community_members AS cm, {$db_prefix}communities AS c)
    WHERE cm.ID_MEMBER = $ID_MEMBER
    AND cm.ID_COMMUNITY = c.ID_COMMUNITY
    AND c.friendly_url = '$id'
    LIMIT 1", __FILE__, __LINE__);

  $context['usercomunidad'] = mysqli_num_rows($request);
  $row = mysqli_fetch_assoc($request);

  $context['rango'] = array(
    'grade' => $row['grade'],
  );

  mysqli_free_result($request);

  $request = db_query("
    SELECT
      c.ID_MEMBER, c.ID_COMMUNITY, c.description, c.oficial, c.ID_CATEGORY, c.view, c.grade, c.title, c.friendly_url, c.logo, c.numMembers,
      c.date, c.numPosts, b.ID_CATEGORY, b.name AS bname, b.friendly_url AS friendly_url2, mem.realName, mem.memberName, mem.ID_MEMBER
    FROM ({$db_prefix}communities AS c, {$db_prefix}community_categories AS b, {$db_prefix}members AS mem)
    WHERE mem.ID_MEMBER = c.ID_MEMBER
    AND c.friendly_url = '$id'
    AND c.ID_CATEGORY = b.ID_CATEGORY
    AND mem.ID_MEMBER = c.ID_MEMBER
    LIMIT 1", __FILE__, __LINE__);

  $row = mysqli_fetch_assoc($request);

  $context['comunidad'] = array(
    'ID_COMMUNITY' => $row['ID_COMMUNITY'],
    'grade' => $row['grade'],
    'numMembers' => $row['numMembers'],
    'numPosts' => $row['numPosts'],
    'ID_MEMBER' => $row['ID_MEMBER'],
    'description' => $row['description'],
    'ID_CATEGORY' => $row['ID_CATEGORY'],
    'view' => $row['view'],
    'grade' => $row['grade'],
    'title' => $row['title'],
    'friendly_url' => $row['friendly_url'],
    'logo' => $row['logo'],
    'date' => $row['date'],
    'friendly_url2' => $row['friendly_url2'],
    'bname' => $row['bname'],
    'oficial' => $row['oficial'],
  );

  mysqli_free_result($request);

  $request = db_query("
    SELECT cm.ID_MEMBER, cm.ID_COMMUNITY, cm.grade, c.friendly_url, c.ID_COMMUNITY, cm.grade
    FROM ({$db_prefix}community_members AS cm, {$db_prefix}communities AS c)
    WHERE cm.ID_MEMBER = $ID_MEMBER
    AND cm.ID_COMMUNITY = c.ID_COMMUNITY
    AND c.friendly_url = '$id'
    LIMIT 1", __FILE__, __LINE__);

  $context['usercomunidad'] = mysqli_num_rows( $request);
  $row = mysqli_fetch_assoc($request);

  $context['rango'] = array(
    'grade' => $row['grade'],
  );

  mysqli_free_result($request);

  if (!$context['usercomunidad'] == 1) {
    fatal_error('Tienes que ser miembro para realizar esta operaci&oacute;n.-', false);
  }
}

function nuevotema2() {
  global $context, $boardurl, $ID_MEMBER, $db_prefix;

  is_not_guest();
  loadLanguage('Post');

  $context['sub_template'] = 'nuevotema2';

  $id = htmlentities(addslashes($_REQUEST['id']), ENT_QUOTES, 'UTF-8');

  $request = db_query("
    SELECT cm.ID_MEMBER, cm.ID_COMMUNITY, cm.grade, c.friendly_url, c.ID_COMMUNITY, cm.grade
    FROM ({$db_prefix}community_members AS cm, {$db_prefix}communities AS c)
    WHERE cm.ID_MEMBER = $ID_MEMBER
    AND cm.ID_COMMUNITY = c.ID_COMMUNITY
    AND c.friendly_url = '$id'
    LIMIT 1", __FILE__, __LINE__);

  $context['usercomunidad'] = mysqli_num_rows($request);
  $row = mysqli_fetch_assoc($request);

  $context['rango'] = array(
    'grade' => $row['grade'],
  );

  mysqli_free_result($request);

  if (empty($_POST['titulo'])) {
    fatal_error('El campo <b>T&iacute;tulo</b> es requerido para esta operaci&oacute;n.-', false);
  } else if (empty($_POST['cuerpo_comment'])) {
    fatal_error('El campo <b>Cuerpo</b> es requerido para esta operaci&oacute;n.-', false);
  } else if (isset($_POST['Enviar']) && isset($_POST['titulo']) && isset($_POST['cuerpo_comment'])) {
    $request = db_query("
      SELECT *
      FROM {$db_prefix}communities
      WHERE friendly_url = '$id'", __FILE__, __LINE__);

    $row2 = mysqli_fetch_assoc($request);

    $ID_COMMUNITY = $row2['ID_COMMUNITY'];
    $ID_COMMUNITY2 = htmlentities($_POST['comun'], ENT_QUOTES, 'UTF-8');
    $subject = htmlentities($_POST['titulo'], ENT_QUOTES, 'UTF-8');
    $body = htmlspecialchars($_POST['cuerpo_comment'], ENT_QUOTES, 'UTF-8');
    // TO-DO: Verificar si isSticky y locked son booleanas o integer
    $isSticky = (int) $_POST['sticky'];
    $locked = (int) $_POST['nocoment'];
    $posterName = $context['user']['name'];
    $posterIP = $_SERVER['REMOTE_ADDR'];
    $posterTime = time();
    $grade = $context['rango']['grade'];

    $request = db_query("
      SELECT * 
      FROM {$db_prefix}communities
      WHERE friendly_url = '$ID_COMMUNITY2'", __FILE__, __LINE__);

    $verify2 = mysqli_fetch_assoc($request);

    if ($verify2['ID_COMMUNITY'] == $ID_COMMUNITY) {
      db_query("
        INSERT INTO {$db_prefix}community_topic(ID_MEMBER, ID_COMMUNITY, isSticky, locked, posterTime, posterName, posterIP, subject, body, grade, tags)
        VALUES ($ID_MEMBER, $ID_COMMUNITY, $isSticky, $locked, $posterTime, '$posterName', '$posterIP', '$subject', '$body', $grade, '')", __FILE__, __LINE__);

      $insertar['ID_TOPIC'] = db_insert_id();
    } else {
      fatal_error('No perteneces a esta comunidad.-', false);
    }

    $request2 = db_query("
      SELECT *
      FROM {$db_prefix}communities AS c, {$db_prefix}community_topic AS ct
      WHERE c.ID_COMMUNITY = ct.ID_COMMUNITY
      AND c.ID_COMMUNITY = $ID_COMMUNITY
      AND ct.posterName = '$posterName'
      ORDER BY ct.posterTime DESC", __FILE__, __LINE__);

    $row2 = mysqli_fetch_assoc($request2);

    redirectexit($boardurl . '/comunidades/' . $verify2['friendly_url'] . '/');
  }
}

function vertema() {
  global $context, $boardurl, $ID_MEMBER, $db_prefix;

  loadLanguage('Post');
  $context['sub_template'] = 'vertema';

  $id = htmlentities(addslashes($_REQUEST['id']), ENT_QUOTES, 'UTF-8');
  $tema = (int) $_REQUEST['tema'];

  $request = db_query("
    SELECT cm.ID_MEMBER, cm.ID_COMMUNITY, cm.grade, c.friendly_url, c.ID_COMMUNITY, cm.grade
    FROM ({$db_prefix}community_members AS cm, {$db_prefix}communities AS c)
    WHERE cm.ID_MEMBER = '$ID_MEMBER'
    AND cm.ID_COMMUNITY = c.ID_COMMUNITY
    AND c.friendly_url = '$id'
    LIMIT 1", __FILE__, __LINE__);

  $context['usercomunidad'] = mysqli_num_rows($request);
  $row = mysqli_fetch_assoc($request);

  $context['rango'] = array(
    'grade' => $row['grade'],
  );

  mysqli_free_result($request);

  $request = db_query("
    SELECT
      c.ID_MEMBER, c.ID_COMMUNITY, c.description, c.oficial, c.ID_CATEGORY, c.view, c.grade, c.title, c.friendly_url, c.logo, c.numMembers, c.date,
      c.numPosts, b.ID_CATEGORY, b.name AS bname, b.friendly_url AS friendly_url2, mem.realName, mem.memberName, mem.ID_MEMBER, ct.ID_COMMUNITY, ct.ID_TOPIC
    FROM ({$db_prefix}communities AS c, {$db_prefix}community_categories AS b, {$db_prefix}members AS mem, {$db_prefix}community_topic AS ct)
    WHERE ct.ID_COMMUNITY = c.ID_COMMUNITY
    AND ct.ID_TOPIC = $tema
    AND mem.ID_MEMBER = c.ID_MEMBER
    AND c.ID_CATEGORY = b.ID_CATEGORY
    AND mem.ID_MEMBER = c.ID_MEMBER
    LIMIT 1", __FILE__, __LINE__);

  $row = mysqli_fetch_assoc($request);

  $context['comunidad'] = array(
    'ID_COMMUNITY' => $row['ID_COMMUNITY'],
    'grade' => $row['grade'],
    'numMembers' => $row['numMembers'],
    'numPosts' => $row['numPosts'],
    'ID_MEMBER' => $row['ID_MEMBER'],
    'description' => $row['description'],
    'ID_CATEGORY' => $row['ID_CATEGORY'],
    'view' => $row['view'],
    'title' => $row['title'],
    'friendly_url' => $row['friendly_url'],
    'logo' => $row['logo'],
    'date' => $row['date'],
    'friendly_url2' => $row['friendly_url2'],
    'bname' => $row['bname'],
    'oficial' => $row['oficial'],
    'link_category' => '<a href="' . $boardurl . '/comunidades/home/' . $row['friendly_url2'] . '/" title="' . $row['bname'] . '">' . $row['bname'] . '</a>',
    'creator' => '<a title="Ver el perfil de ' . $row['realName'] . '" href="' . $boardurl . '/perfil/' . $row['memberName'] . '">' . $row['realName'] . '</a>',
  );

  mysqli_free_result($request);

  if (empty($tema)) {
    fatal_error('El tema no existe.-', false);
  } else if (!empty($tema)) {
    $request = db_query("
      SELECT
        ct.ID_TOPIC, ct.ID_COMMUNITY, ct.subject, ct.body, ct.isSticky, ct.posterTime, ct.points, ct.posterName, ct.numViews, mem.ID_MEMBER, mem.realName,
        mem.memberName, mem.gender, mem.avatar, mem.usertitle, mem.personalText, mem.estado_icon, c.ID_COMMUNITY, c.friendly_url, ct.ID_MEMBER, ct.locked,
        ct.grade
      FROM ({$db_prefix}community_topic AS ct, {$db_prefix}communities AS c, {$db_prefix}members AS mem, {$db_prefix}community_members AS cm)
      WHERE c.ID_COMMUNITY = ct.ID_COMMUNITY
      AND ct.ID_TOPIC = $tema
      AND mem.ID_MEMBER = ct.ID_MEMBER
      AND ct.ID_COMMUNITY = c.ID_COMMUNITY
      AND mem.ID_MEMBER = ct.ID_MEMBER", __FILE__, __LINE__);

    $context['tema'] = array();

    while ($row = mysqli_fetch_assoc($request)) {
      $context['tema'] = array(
        'ID_TOPIC' => $row['ID_TOPIC'],
        'subject' => $row['subject'],
        'body' => $row['body'],
        'isSticky' => $row['isSticky'],
        'posterTime' => $row['posterTime'],
        'points' => $row['points'],
        'locked' => $row['locked'],
        'posterName' => $row['posterName'],
        'gender' => $row['gender'],
        'avatar' => $row['avatar'],
        'usertitle' => $row['usertitle'],
        'personalText' => $row['personalText'],
        'estado_icon' => $row['estado_icon'],
        'grade' => $row['grade'],
        'numViews' => $row['numViews'],
        'ID_MEMBER' => $row['ID_MEMBER'],
        'ID_POST_GROUP' => $row['ID_POST_GROUP'],
        'ID_GROUP' => $row['ID_GROUP'],
        'ID_COMMUNITY' => $row['ID_COMMUNITY'],
      );
    }

    mysqli_free_result($request);

    $context['page_title'] = $context['tema']['subject'];

    $request = db_query("
      SELECT *
      FROM {$db_prefix}community_comments
      WHERE ID_TOPIC = " . $tema, __FILE__, __LINE__);

    $context['haycom'] = mysqli_fetch_assoc($request);

    $request = db_query("
      SELECT c.comment, c.comment AS comentario2, c.ID_TOPIC, c.ID_MEMBER, c.ID_COMMENT, c.posterTime, c.posterName, c.ID_COMMUNITY
      FROM {$db_prefix}community_comments AS c
      WHERE c.ID_TOPIC = $tema
      ORDER BY c.ID_COMMENT ASC", __FILE__, __LINE__);

    $context['comentarios'] = array();

    while ($row = mysqli_fetch_assoc($request)) {
      $row['comment'] = parse_bbc($row['comment'], '1', $row['ID_MSG']);
      $row['comentario0'] = parse_bbc($row['comentario0'], '0', $row['ID_MSG']);

      $context['comentarios'][] = array(
        'comentario2' => censorText($row['comentario2']),
        'comment' => censorText($row['comment']),
        'citar' => censorText($row['comentario0']),
        'user' => $row['ID_MEMBER'],
        'nomuser' => censorText($row['posterName']),
        'nommem' => censorText($row['posterName']),
        'id' => $row['ID_COMMENT'],
        'fecha' => $row['posterTime'],
      );
    }

    mysqli_free_result($request);

    if (empty($_SESSION['ultimo_tema_visto']) || $_SESSION['ultimo_tema_visto'] != $tema) {
      db_query("
        UPDATE {$db_prefix}community_topic
        SET numViews = numViews + 1
        WHERE ID_TOPIC = $tema
        LIMIT 1", __FILE__, __LINE__);

      $_SESSION['ultimo_tema_visto'] = $tema;
    }
  }
}

function theme_quickreply_box() {
  global $modSettings, $db_prefix;
  global $context, $settings, $user_info;

  if (isset($settings['use_default_images']) && $settings['use_default_images'] == 'defaults' && isset($settings['default_template'])) {
    $temp1 = $settings['theme_url'];
    $settings['theme_url'] = $settings['default_theme_url'];
    $temp2 = $settings['images_url'];
    $settings['images_url'] = $settings['default_images_url'];
    $temp3 = $settings['theme_dir'];
    $settings['theme_dir'] = $settings['default_theme_dir'];
  }

  $context['smileys'] = array(
    'postform' => array(),
    'popup' => array(),
  );

  loadLanguage('Post');

  if (empty($modSettings['smiley_enable']) && $user_info['smiley_set'] != 'none')
    $context['smileys']['postform'][] = array();
  else if ($user_info['smiley_set'] != 'none') {
    if (($temp = cache_get_data('posting_smileys', 480)) == null) {
      $request = db_query("
        SELECT code, filename, description, smileyRow, hidden
        FROM {$db_prefix}smileys
        WHERE hidden IN (0, 2)
        ORDER BY smileyRow, smileyOrder", __FILE__, __LINE__);

      while ($row = mysqli_fetch_assoc($request)) {
        $row['code'] = htmlspecialchars($row['code']);
        $row['filename'] = htmlspecialchars($row['filename']);
        $row['description'] = htmlspecialchars($row['description']);

        $context['smileys'][empty($row['hidden']) ? 'postform' : 'popup'][$row['smileyRow']]['smileys'][] = $row;
      }

      mysqli_free_result($request);
      cache_put_data('posting_smileys', $context['smileys'], 480);
    }
    else
      $context['smileys'] = $temp;
  }

  foreach (array_keys($context['smileys']) as $location) {
    foreach ($context['smileys'][$location] as $j => $row) {
      $n = count($context['smileys'][$location][$j]['smileys']);

      for ($i = 0; $i < $n; $i++) {
        $context['smileys'][$location][$j]['smileys'][$i]['code'] = addslashes($context['smileys'][$location][$j]['smileys'][$i]['code']);
        $context['smileys'][$location][$j]['smileys'][$i]['js_description'] = addslashes($context['smileys'][$location][$j]['smileys'][$i]['description']);
      }

      $context['smileys'][$location][$j]['smileys'][$n - 1]['last'] = true;
    }

    if (!empty($context['smileys'][$location]))
      $context['smileys'][$location][count($context['smileys'][$location]) - 1]['last'] = true;
  }

  $settings['smileys_url'] = $modSettings['smileys_url'] . '/' . $user_info['smiley_set'];
  $context['show_bbc'] = !empty($modSettings['enableBBC']) && !empty($settings['show_bbc']);

  if (!empty($modSettings['disabledBBC'])) {
    $disabled_tags = explode(',', $modSettings['disabledBBC']);

    foreach ($disabled_tags as $tag)
      $context['disabled_tags'][trim($tag)] = true;
  }

  template_quickreply_box();

  if (isset($settings['use_default_images']) && $settings['use_default_images'] == 'defaults' && isset($settings['default_template'])) {
    $settings['theme_url'] = $temp1;
    $settings['images_url'] = $temp2;
    $settings['theme_dir'] = $temp3;
  }
}

function nuevotema_smileys() {
  global $modSettings, $db_prefix;
  global $context, $settings, $user_info;
  
  if (isset($settings['use_default_images']) && $settings['use_default_images'] == 'defaults' && isset($settings['default_template'])) {
    $temp1 = $settings['theme_url'];
    $settings['theme_url'] = $settings['default_theme_url'];
    $temp2 = $settings['images_url'];
    $settings['images_url'] = $settings['default_images_url'];
    $temp3 = $settings['theme_dir'];
    $settings['theme_dir'] = $settings['default_theme_dir'];
  }

  $context['smileys'] = array(
    'postform' => array(),
    'popup' => array(),
  );

  loadLanguage('Post');

  if (empty($modSettings['smiley_enable']) && $user_info['smiley_set'] != 'none')
    $context['smileys']['postform'][] = array();
  else if ($user_info['smiley_set'] != 'none') {
    if (($temp = cache_get_data('posting_smileys', 480)) == null) {
      $request = db_query("
        SELECT code, filename, description, smileyRow, hidden
        FROM {$db_prefix}smileys
        WHERE hidden IN (0, 2)
        ORDER BY smileyRow, smileyOrder", __FILE__, __LINE__);

      while ($row = mysqli_fetch_assoc($request)) {
        $row['code'] = htmlspecialchars($row['code']);
        $row['filename'] = htmlspecialchars($row['filename']);
        $row['description'] = htmlspecialchars($row['description']);

        $context['smileys'][empty($row['hidden']) ? 'postform' : 'popup'][$row['smileyRow']]['smileys'][] = $row;
      }

      mysqli_free_result($request);
      cache_put_data('posting_smileys', $context['smileys'], 480);
    }
    else
      $context['smileys'] = $temp;
  }

  foreach (array_keys($context['smileys']) as $location) {
    foreach ($context['smileys'][$location] as $j => $row) {
      $n = count($context['smileys'][$location][$j]['smileys']);
      for ($i = 0; $i < $n; $i++) {
        $context['smileys'][$location][$j]['smileys'][$i]['code'] = addslashes($context['smileys'][$location][$j]['smileys'][$i]['code']);
        $context['smileys'][$location][$j]['smileys'][$i]['js_description'] = addslashes($context['smileys'][$location][$j]['smileys'][$i]['description']);
      }

      $context['smileys'][$location][$j]['smileys'][$n - 1]['last'] = true;
    }

    if (!empty($context['smileys'][$location]))
      $context['smileys'][$location][count($context['smileys'][$location]) - 1]['last'] = true;
  }

  $settings['smileys_url'] = $modSettings['smileys_url'] . '/' . $user_info['smiley_set'];
  $context['show_bbc'] = !empty($modSettings['enableBBC']) && !empty($settings['show_bbc']);

  if (!empty($modSettings['disabledBBC'])) {
    $disabled_tags = explode(',', $modSettings['disabledBBC']);

    foreach ($disabled_tags as $tag)
      $context['disabled_tags'][trim($tag)] = true;
  }

  template_nuevotemabox();

  if (isset($settings['use_default_images']) && $settings['use_default_images'] == 'defaults' && isset($settings['default_template'])) {
    $settings['theme_url'] = $temp1;
    $settings['images_url'] = $temp2;
    $settings['theme_dir'] = $temp3;
  }
}

function publicitar() {
  global $context, $ID_MEMBER, $db_prefix;

  is_not_guest();
  loadlanguage('Post');

  $context['sub_template'] = 'publicitar';
  $id = htmlentities(addslashes($_REQUEST['id']), ENT_QUOTES, 'UTF-8');

  $request = db_query("
    SELECT
      c.ID_MEMBER, c.ID_COMMUNITY, c.description, c.oficial, c.ID_CATEGORY, c.view, c.grade, c.title, c.friendly_url, c.logo, c.numMembers,
      c.date, c.numPosts, b.ID_CATEGORY, b.name AS bname, b.friendly_url AS friendly_url2, mem.realName, mem.memberName, mem.ID_MEMBER
    FROM ({$db_prefix}communities AS c, {$db_prefix}community_categories AS b, {$db_prefix}members AS mem)
    WHERE mem.ID_MEMBER = c.ID_MEMBER
    AND c.friendly_url = '$id'
    AND c.ID_CATEGORY = b.ID_CATEGORY
    AND mem.ID_MEMBER = c.ID_MEMBER
    LIMIT 1", __FILE__, __LINE__);

  $row = mysqli_fetch_assoc($request);

  $context['comunidad'] = array(
    'ID_COMMUNITY' => $row['ID_COMMUNITY'],
    'grade' => $row['grade'],
    'numMembers' => $row['numMembers'],
    'numPosts' => $row['numPosts'],
    'ID_MEMBER' => $row['ID_MEMBER'],
    'description' => $row['description'],
    'ID_CATEGORY' => $row['ID_CATEGORY'],
    'view' => $row['view'],
    'grade' => $row['grade'],
    'title' => $row['title'],
    'friendly_url' => $row['friendly_url'],
    'logo' => $row['logo'],
    'date' => $row['date'],
    'friendly_url2' => $row['friendly_url2'],
    'bname' => $row['bname'],
    'oficial' => $row['oficial'],
  );

  mysqli_free_result($request);

  $context['page_title'] = $context['comunidad']['title'];

  $request = db_query("
    SELECT cm.ID_MEMBER, cm.ID_COMMUNITY, cm.grade, c.friendly_url, c.ID_COMMUNITY, cm.grade
    FROM ({$db_prefix}community_members AS cm, {$db_prefix}communities AS c)
    WHERE cm.ID_MEMBER = $ID_MEMBER
    AND cm.ID_COMMUNITY = c.ID_COMMUNITY
    AND c.friendly_url = '$id'
    LIMIT 1", __FILE__, __LINE__);

  $context['usercomunidad'] = mysqli_num_rows($request);
  $row = mysqli_fetch_assoc($request);

  $context['rango'] = array(
    'grade' => $row['grade'],
  );
  mysqli_free_result($request);
}

function publicitar2() {
  global $context, $boardurl, $ID_MEMBER, $db_prefix;

  is_not_guest();

  $id = htmlentities(addslashes($_REQUEST['id']), ENT_QUOTES, 'UTF-8');

  $request = db_query("
    SELECT
      c.ID_MEMBER, c.ID_COMMUNITY, c.description, c.publicity, c.ID_CATEGORY, c.view, c.grade, c.title, c.friendly_url, c.logo, c.numMembers,
      c.date, c.numPosts, b.ID_CATEGORY, b.name AS bname, b.friendly_url AS friendly_url2, mem.realName, mem.memberName, mem.ID_MEMBER
    FROM ({$db_prefix}communities AS c, {$db_prefix}community_categories AS b, {$db_prefix}members AS mem)
    WHERE mem.ID_MEMBER = c.ID_MEMBER
    AND c.friendly_url = '$id'
    AND c.ID_CATEGORY = b.ID_CATEGORY
    AND mem.ID_MEMBER = c.ID_MEMBER
    LIMIT 1", __FILE__, __LINE__);

  $row = mysqli_fetch_assoc($request);

  $context['comunidad'] = array(
    'ID_COMMUNITY' => $row['ID_COMMUNITY'],
    'grade' => $row['grade'],
    'numMembers' => $row['numMembers'],
    'numPosts' => $row['numPosts'],
    'ID_MEMBER' => $row['ID_MEMBER'],
    'description' => $row['description'],
    'ID_CATEGORY' => $row['ID_CATEGORY'],
    'view' => $row['view'],
    'grade' => $row['grade'],
    'title' => $row['title'],
    'friendly_url' => $row['friendly_url'],
    'logo' => $row['logo'],
    'date' => $row['date'],
    'friendly_url2' => $row['friendly_url2'],
    'bname' => $row['bname'],
    'publicity' => $row['publicity'],
  );

  mysqli_free_result($request);

  $request = db_query("
    SELECT cm.ID_MEMBER, cm.ID_COMMUNITY, cm.grade, c.friendly_url, c.ID_COMMUNITY, cm.grade
    FROM ({$db_prefix}community_members AS cm, {$db_prefix}communities AS c)
    WHERE cm.ID_MEMBER = $ID_MEMBER
    AND cm.ID_COMMUNITY = c.ID_COMMUNITY
    AND c.friendly_url = '$id'
    LIMIT 1", __FILE__, __LINE__);

  $context['usercomunidad'] = mysqli_num_rows($request);
  $row = mysqli_fetch_assoc($request);

  $context['rango'] = array(
    'grade' => $row['grade'],
  );

  mysqli_free_result($request);

  $request = db_query("
    SELECT *
    FROM {$db_prefix}communities AS c, {$db_prefix}community_publicity AS cp
    WHERE cp.ID_COMMUNITY = c.ID_COMMUNITY
    AND c.friendly_url = '" . $context['comunidad']['friendly_url'] . "'", __FILE__, __LINE__);

  $pub = mysqli_num_rows($request);

  mysqli_free_result($request);

  $request = db_query("
    SELECT *
    FROM {$db_prefix}members
    WHERE ID_MEMBER = " . $ID_MEMBER, __FILE__, __LINE__);

  $row = mysqli_fetch_assoc($request);

  if ($row['moneyBank'] < 500 && $context['usercomunidad'] == 1 && $context['rango']['grade'] == 1) {
    fatal_error('Para publicitar tu comunidad debes tener m&aacute;s de 500 puntos.-', false);
  } else if ($pub == 1 && $context['usercomunidad'] == 1 && $context['rango']['grade'] == 1) {
    fatal_error('Ya tienes cr&eacute;dito en publicidad.-', false);
  } else if ($row['moneyBank'] > 500 && $context['usercomunidad'] == 1 && $context['rango']['grade'] == 1) {
    $expire = time() + 86400;
    $result1 = db_query("
      UPDATE {$db_prefix}members
      SET moneyBank = moneyBank - 100
      WHERE ID_MEMBER = " . $ID_MEMBER, __FILE__, __LINE__);

    $result2 = db_query("
      INSERT INTO {$db_prefix}community_publicity (ID_MEMBER, ID_COMMUNITY, expire)
      VALUES ($ID_MEMBER, " . $context['comunidad']['ID_COMMUNITY'] . ", $expire)", __FILE__, __LINE__);

    if ($result1 && $result2) {
      redirectexit($boardurl . '/comunidades/' . $context['comunidad']['friendly_url']);
    } else {
      fatal_error('Algo ha fallado [ result1: ' . $result1 . ', result2: ' . $result2 . ' ] ', false);
    }
  }
}

function editartema() {
  global $context, $txt, $ID_MEMBER, $db_prefix;

  is_not_guest();

  $context['sub_template'] = 'editartema';
  $context['page_title'] = $txt[18];

  $id = (int) $_REQUEST['id'];

  if (!$context['allow_admin']) {
    fatal_error('No puedes editar un tema si no eres Administrador', false);
  }

  $request = db_query("
    SELECT
      ct.ID_MEMBER, ct.ID_COMMUNITY, ct.subject, ct.body, ct.isSticky, ct.locked, c.ID_COMMUNITY, c.friendly_url,
      c.title, ct.ID_TOPIC, cc.friendly_url AS friendly_url2, cc.ID_CATEGORY, c.ID_CATEGORY, cc.name AS bname, c.view
    FROM {$db_prefix}community_topic AS ct, {$db_prefix}communities AS c, {$db_prefix}community_categories AS cc
    WHERE ct.ID_COMMUNITY = c.ID_COMMUNITY
    AND ct.ID_TOPIC = $id
    AND cc.ID_CATEGORY = c.ID_CATEGORY", __FILE__, __LINE__);

  $context['editar'] = array();

  while ($row = mysqli_fetch_assoc($request)) {
    $context['editar'] = array(
      'ID_COMMUNITY' => $row['ID_COMMUNITY'],
      'ID_TOPIC' => $row['ID_TOPIC'],
      'grade' => $row['grade'],
      'ID_MEMBER' => $row['ID_MEMBER'],
      'locked' => $row['locked'],
      'view' => $row['view'],
      'isSticky' => $row['isSticky'],
      'body' => $row['body'],
      'subject' => $row['subject'],
      'title' => $row['title'],
      'friendly_url' => $row['friendly_url'],
      'friendly_url2' => $row['friendly_url2'],
      'bname' => $row['bname'],
    );
  }

  mysqli_free_result($request);

  $request = db_query("
    SELECT cm.ID_MEMBER, cm.ID_COMMUNITY, cm.grade, c.friendly_url, c.ID_COMMUNITY, cm.grade
    FROM ({$db_prefix}community_members AS cm, {$db_prefix}communities AS c)
    WHERE cm.ID_MEMBER = $ID_MEMBER
    AND cm.ID_COMMUNITY = c.ID_COMMUNITY
    AND c.ID_COMMUNITY = " . $context['editar']['ID_COMMUNITY'] . "
    LIMIT 1", __FILE__, __LINE__);

  $context['usercomunidad'] = mysqli_num_rows($request);
  $context['rango'] = array();

  while ($row = mysqli_fetch_assoc($request)) {
    $context['rango'] = array(
      'grade' => $row['grade'],
    );
  }

  mysqli_free_result($request);
}

function theme_quickreply_box2() {
  global $txt, $modSettings, $db_prefix;
  global $context, $settings, $user_info;

  if (isset($settings['use_default_images']) && $settings['use_default_images'] == 'defaults' && isset($settings['default_template'])) {
    $temp1 = $settings['theme_url'];
    $settings['theme_url'] = $settings['default_theme_url'];
    $temp2 = $settings['images_url'];
    $settings['images_url'] = $settings['default_images_url'];
    $temp3 = $settings['theme_dir'];
    $settings['theme_dir'] = $settings['default_theme_dir'];
  }

  $context['smileys'] = array(
    'postform' => array(),
    'popup' => array(),
  );

  loadLanguage('Post');

  if (empty($modSettings['smiley_enable']) && $user_info['smiley_set'] != 'none')
    $context['smileys']['postform'][] = array();
  else if ($user_info['smiley_set'] != 'none') {
    if (($temp = cache_get_data('posting_smileys', 480)) == null) {
      $request = db_query("
        SELECT code, filename, description, smileyRow, hidden
        FROM {$db_prefix}smileys
        WHERE hidden IN (0, 2)
        ORDER BY smileyRow, smileyOrder", __FILE__, __LINE__);

      while ($row = mysqli_fetch_assoc($request)) {
        $row['code'] = htmlspecialchars($row['code']);
        $row['filename'] = htmlspecialchars($row['filename']);
        $row['description'] = htmlspecialchars($row['description']);
        $context['smileys'][empty($row['hidden']) ? 'postform' : 'popup'][$row['smileyRow']]['smileys'][] = $row;
      }

      mysqli_free_result($request);
      cache_put_data('posting_smileys', $context['smileys'], 480);
    }
    else
      $context['smileys'] = $temp;
  }

  foreach (array_keys($context['smileys']) as $location) {
    foreach ($context['smileys'][$location] as $j => $row) {
      $n = count($context['smileys'][$location][$j]['smileys']);

      for ($i = 0; $i < $n; $i++) {
        $context['smileys'][$location][$j]['smileys'][$i]['code'] = addslashes($context['smileys'][$location][$j]['smileys'][$i]['code']);
        $context['smileys'][$location][$j]['smileys'][$i]['js_description'] = addslashes($context['smileys'][$location][$j]['smileys'][$i]['description']);
      }

      $context['smileys'][$location][$j]['smileys'][$n - 1]['last'] = true;
    }

    if (!empty($context['smileys'][$location]))
      $context['smileys'][$location][count($context['smileys'][$location]) - 1]['last'] = true;
  }

  $settings['smileys_url'] = $modSettings['smileys_url'] . '/' . $user_info['smiley_set'];
  $context['show_bbc'] = !empty($modSettings['enableBBC']) && !empty($settings['show_bbc']);

  if (!empty($modSettings['disabledBBC'])) {
    $disabled_tags = explode(',', $modSettings['disabledBBC']);
    foreach ($disabled_tags as $tag)
      $context['disabled_tags'][trim($tag)] = true;
  }

  template_quickreply_box2();

  if (isset($settings['use_default_images']) && $settings['use_default_images'] == 'defaults' && isset($settings['default_template'])) {
    $settings['theme_url'] = $temp1;
    $settings['images_url'] = $temp2;
    $settings['theme_dir'] = $temp3;
  }
}

function editartema2() {
  global $context, $boardurl, $ID_MEMBER, $db_prefix;

  is_not_guest();

  $id_tema = (int) $_POST['id_tema'];
  $subject = htmlentities($_POST['titulo'], ENT_QUOTES, 'UTF-8');
  $body = htmlentities($_POST['cuerpo_comment'], ENT_QUOTES, 'UTF-8');
  $isSticky = (int) $_POST['sticky'];
  $locked = (int) $_POST['nocoment'];

  $request = db_query("
    SELECT ct.ID_MEMBER, ct.ID_COMMUNITY, ct.subject, ct.body, ct.isSticky, ct.locked, c.ID_COMMUNITY, c.friendly_url, ct.ID_TOPIC
    FROM {$db_prefix}community_topic AS ct, {$db_prefix}communities AS c
    WHERE ct.ID_COMMUNITY = c.ID_COMMUNITY
    AND ct.ID_TOPIC = " . $id_tema, __FILE__, __LINE__);

  $context['editar'] = array();

  while ($row = mysqli_fetch_assoc($request)) {
    $context['editar'] = array(
      'ID_COMMUNITY' => $row['ID_COMMUNITY'],
      'ID_TOPIC' => $row['ID_TOPIC'],
      'grade' => $row['grade'],
      'ID_MEMBER' => $row['ID_MEMBER'],
      'locked' => $row['locked'],
      'isSticky' => $row['isSticky'],
      'body' => $row['body'],
      'subject' => $row['subject'],
      'friendly_url' => $row['friendly_url'],
    );
  }

  mysqli_free_result($request);

  $request = db_query("
    SELECT cm.ID_MEMBER, cm.ID_COMMUNITY, cm.grade, c.friendly_url, c.ID_COMMUNITY, cm.grade
    FROM ({$db_prefix}community_members AS cm, {$db_prefix}communities AS c)
    WHERE cm.ID_MEMBER = $ID_MEMBER
    AND cm.ID_COMMUNITY = c.ID_COMMUNITY
    AND c.ID_COMMUNITY = " . $context['editar']['ID_COMMUNITY'] . "
    LIMIT 1", __FILE__, __LINE__);

  $context['usercomunidad'] = mysqli_num_rows($request);
  $context['rango'] = array();

  while ($row = mysqli_fetch_assoc($request)) {
    $context['rango'] = array(
      'grade' => $row['grade'],
    );
  }

  mysqli_free_result($request);

  if (empty($locked)) {
    $locked = 0;
  }

  if (empty($isSticky)) {
    $isSticky = 0;
  }

  if (!empty($subject) && !empty($body) && !empty($id_tema) || $context['editar']['ID_MEMBER'] == $ID_MEMBER || $context['allow_admin'] || $context['usercomunidad'] == 1) {
    db_query("
      UPDATE {$db_prefix}community_topic
      SET subject = '$subject', body = '$body', isSticky = $isSticky, locked = $locked
      WHERE ID_COMMUNITY = " . $context['editar']['ID_COMMUNITY'] . "
      AND ID_TOPIC = " . $context['editar']['ID_TOPIC'], __FILE__, __LINE__);

    redirectexit($boardurl . '/comunidades/' . $context['editar']['friendly_url']);
  } else if ($context['editar']['ID_MEMBER'] != $ID_MEMBER || !$context['allow_admin']) {
    fatal_error('No puedes editar este tema', false);
  }
}

function eliminartema() {
  global $context, $txt, $boardurl, $db_prefix, $ID_MEMBER;

  is_not_guest();

  $context['sub_template'] = 'editartema';
  $context['page_title'] = $txt[18];

  $id = (int) $_REQUEST['id'];

  $request = db_query("
    SELECT ct.ID_COMMUNITY, c.ID_COMMUNITY, ct.ID_TOPIC, c.grade, ct.ID_MEMBER, ct.locked, ct.isSticky, ct.body, ct.subject
    FROM {$db_prefix}community_topic AS ct, {$db_prefix}communities AS c
    WHERE ct.ID_COMMUNITY = c.ID_COMMUNITY
    AND ct.ID_TOPIC = " . $id, __FILE__, __LINE__);

  $row = mysqli_fetch_assoc($request);

  $context['editar'] = array(
    'ID_COMMUNITY' => $row['ID_COMMUNITY'],
    'ID_TOPIC' => $row['ID_TOPIC'],
    'grade' => $row['grade'],
    'ID_MEMBER' => $row['ID_MEMBER'],
    'locked' => $row['locked'],
    'isSticky' => $row['isSticky'],
    'body' => $row['body'],
    'subject' => $row['subject'],
  );

  mysqli_free_result($request);

  $request = db_query("
    SELECT
      c.ID_MEMBER, c.ID_COMMUNITY, c.description, c.publicity, c.ID_CATEGORY, c.view, c.grade, c.title, c.friendly_url, c.logo, c.numMembers,
      c.date, c.numPosts, b.ID_CATEGORY, b.name AS bname, b.friendly_url AS friendly_url2, mem.realName, mem.memberName, mem.ID_MEMBER
    FROM ({$db_prefix}communities AS c, {$db_prefix}community_categories AS b, {$db_prefix}members AS mem)
    WHERE mem.ID_MEMBER = c.ID_MEMBER
    AND c.ID_COMMUNITY = " . $context['editar']['ID_COMMUNITY'] . "
    AND c.ID_CATEGORY = b.ID_CATEGORY
    AND mem.ID_MEMBER = c.ID_MEMBER
    LIMIT 1", __FILE__, __LINE__);

  $row = mysqli_fetch_assoc($request);

  $context['comunidad'] = array(
    'ID_COMMUNITY' => $row['ID_COMMUNITY'],
    'grade' => $row['grade'],
    'numMembers' => $row['numMembers'],
    'numPosts' => $row['numPosts'],
    'ID_MEMBER' => $row['ID_MEMBER'],
    'description' => $row['description'],
    'ID_CATEGORY' => $row['ID_CATEGORY'],
    'view' => $row['view'],
    'grade' => $row['grade'],
    'title' => $row['title'],
    'friendly_url' => $row['friendly_url'],
    'logo' => $row['logo'],
    'date' => $row['date'],
    'friendly_url2' => $row['friendly_url2'],
    'bname' => $row['bname'],
    'publicity' => $row['publicity'],
  );

  mysqli_free_result($request);

  $request = db_query("
    SELECT cm.ID_MEMBER, cm.ID_COMMUNITY, cm.grade, c.friendly_url, c.ID_COMMUNITY, cm.grade
    FROM ({$db_prefix}community_members AS cm, {$db_prefix}communities AS c)
    WHERE cm.ID_MEMBER = $ID_MEMBER
    AND cm.ID_COMMUNITY = c.ID_COMMUNITY
    AND c.ID_COMMUNITY = " . $context['comunidad']['ID_COMMUNITY'] . "
    ", __FILE__, __LINE__);

  $context['usercomunidad'] = mysqli_num_rows($request);

  $row2 = mysqli_fetch_assoc($request);
  $context['rango'] = array(
    'grade' => $row2['grade'],
  );

  mysqli_free_result($request);

  if (!$context['allow_admin']) {
    fatal_error('No puedes eliminar este tema.-', false);
  } else if ($context['usercomunidad'] == 1 && $context['rango']['grade'] == 1 || $context['usercomunidad'] == 1 && $context['rango']['grade'] == 2 || $context['editar']['ID_MEMBER'] == $ID_MEMBER || $context['allow_admin']) {
    db_query("
      DELETE FROM {$db_prefix}community_comments
      WHERE ID_TOPIC = " . $context['editar']['ID_TOPIC'], __FILE__, __LINE__);

    db_query("
      DELETE FROM {$db_prefix}community_votes
      WHERE ID_TOPIC = " . $context['editar']['ID_TOPIC'], __FILE__, __LINE__);

    db_query("
      DELETE FROM {$db_prefix}denunciations
      WHERE ID_TOPIC = " . $context['editar']['ID_TOPIC'] . "
      AND TYPE = 'comunidades'", __FILE__, __LINE__);

    $result = db_query("
      DELETE FROM {$db_prefix}community_topic
      WHERE ID_TOPIC = " . $context['editar']['ID_TOPIC'], __FILE__, __LINE__);

    if ($result) {
      redirectexit($boardurl . '/comunidades/' . $context['comunidad']['friendly_url']);
    }
  }
}

?>