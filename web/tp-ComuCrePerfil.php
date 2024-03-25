<?php
@require_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');

$user = htmlentities(addslashes($_REQUEST['user']));

$RegistrosAMostrar = 10;
if(isset($_GET['pag'])) {
$RegistrosAEmpezar = ($_GET['pag']-1)*$RegistrosAMostrar;
$PagAct = $_GET['pag'];
} else {
$RegistrosAEmpezar=0;
$PagAct=1;
}

$request = mysql_query("
SELECT *
FROM ({$db_prefix}communities AS c, {$db_prefix}members AS mem, {$db_prefix}community_members AS cm)
WHERE c.ID_COMMUNITY = cm.ID_COMMUNITY
AND c.ID_MEMBER = mem.ID_MEMBER
AND c.ID_MEMBER = cm.ID_MEMBER
AND cm.ID_MEMBER = mem.ID_MEMBER
AND mem.memberName = '$user'
LIMIT $RegistrosAEmpezar, $RegistrosAMostrar");
$rows = mysql_num_rows($request);
if($rows == 0) {
echo '<div class="noesta" style="width:541px;">Este usuario no tiene comunidades creadas.</div>';	
} elseif($rows > 0) {
echo '<div id="ComuCrePerfil"><div class="clearBoth"></div>
<table class="linksList" style="width:541px;"><thead><tr>
					<th>&nbsp;</th>
					<th style="text-align: left;">Comunidades creadas</th>
					<th>Miembros</th>
                    <th>Temas</th>

				</tr></thead><tbody>';
while($row=mysql_fetch_assoc($request)){
$request = mysql_query("SELECT COUNT(ID_COMMUNITY) AS temas FROM community_topic WHERE ID_COMMUNITY = " . $row['ID_COMMUNITY']);
$row1 = mysql_fetch_assoc($request);
$request = mysql_query("SELECT COUNT(ID_COMMUNITY) AS miembros FROM community_members WHERE ID_COMMUNITY = " . $row['ID_COMMUNITY']);
$row2 = mysql_fetch_assoc($request);
echo '<tr>
<td title="Comunidades"><img title="Comunidades" src="' . $settings['images_url'] . '/comunidades/comunidad.png" alt="Comunidades" /></td>
<td style="text-align: left;"><a title="' . $row['title'] . '" alt="' . $row['title'] . '" href="/comunidades/' . $row['friendly_url'] . '" class="titlePost">' . $row['title'] . '</a></td>
<td>' . $row2['miembros'] . '</td>
<td>' . $row1['temas'] . '</td>
</tr>
';
}
echo '</tbody></table><div class="clearBoth"></div><div class="windowbgpag" style="width:537px;">';

$NroRegistros=mysql_num_rows(mysql_query("SELECT *
FROM ({$db_prefix}communities AS c, {$db_prefix}members AS mem, {$db_prefix}community_members AS cm)
WHERE c.ID_COMMUNITY = cm.ID_COMMUNITY
AND c.ID_MEMBER = mem.ID_MEMBER
AND c.ID_MEMBER = cm.ID_MEMBER
AND cm.ID_MEMBER = mem.ID_MEMBER
AND mem.memberName = '$user'"));
$PagAnt=$PagAct-1;
$PagSig=$PagAct+1;
$PagUlt=$NroRegistros/$RegistrosAMostrar;
$Res=$NroRegistros%$RegistrosAMostrar;
if($Res>0) $PagUlt=floor($PagUlt)+1;
if($PagAct>1) echo '<a style="cursor: pointer;" onclick="ComuCrePerfil(\''  . $user . '\',\'' . $PagAnt . '\');">&#171; anterior</a>';
if($PagAct<$PagUlt)  echo '<a style="cursor: pointer;" onclick="ComuCrePerfil(\''  . $user . '\',\'' . $PagSig . '\');">siguiente &#187;</a>';
echo '</div></div>';
}