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
FROM {$db_prefix}community_topic AS ct, {$db_prefix}communities AS c, {$db_prefix}members AS m
WHERE ct.ID_MEMBER = m.ID_MEMBER
AND m.memberName = '$user'
AND c.ID_COMMUNITY = ct.ID_COMMUNITY
ORDER BY ID_TOPIC DESC
LIMIT $RegistrosAEmpezar, $RegistrosAMostrar");
$rows = mysql_num_rows($request);
if($rows == 0) {
echo '<div class="noesta" style="width:541px;">Este usuario no tiene temas creados.</div>';	
} elseif($rows > 0) {
echo '<div id="ComuTemPerfil"><div class="clearBoth"></div>

<table class="linksList" style="width:541px;"><thead><tr>
					<th>&nbsp;</th>
					<th style="text-align: left;">&Uacute;ltimos temas creados</th>
					<th>Comunidad</th>
                    <th>Calificaci&oacute;n</th>
				</tr></thead><tbody>';
while($row=mysql_fetch_assoc($request)){
echo '<tr>
<td title="Comunidades"><img title="Comunidades" src="' . $settings['images_url'] . '/comunidades/temas.png" alt="" /></td>
<td style="text-align: left;"><a href="/comunidades/' . $row['friendly_url'] . '/' . $row['ID_TOPIC'] . '/' . ssi_amigable($row['subject']) . '.html"  class="titlePost" alt="' . $row['subject'] . '" title="' . $row['subject'] . '">' . reducir30($row['subject']) . '</a></td>

<td><a title="' . $row['title'] . '" alt="' . $row['title'] . '" href="/comunidades/' . $row['friendly_url'] . '">' . reducir22($row['title']) . '</a></td>
';
votos($row['points']);
echo '
</tr>';
}
echo '</tbody></table><div class="clearBoth"></div><div class="windowbgpag" style="width:537px;">';

$NroRegistros=mysql_num_rows(mysql_query("SELECT *
FROM {$db_prefix}community_topic AS ct, {$db_prefix}communities AS c, {$db_prefix}members AS m
WHERE ct.ID_MEMBER = m.ID_MEMBER
AND m.memberName = '$user'
AND c.ID_COMMUNITY = ct.ID_COMMUNITY"));
$PagAnt=$PagAct-1;
$PagSig=$PagAct+1;
$PagUlt=$NroRegistros/$RegistrosAMostrar;
$Res=$NroRegistros%$RegistrosAMostrar;
if($Res>0) $PagUlt=floor($PagUlt)+1;
if($PagAct>1) echo '<a style="cursor: pointer;" onclick="ComuTemPerfil(\''  . $user . '\',\'' . $PagAnt . '\');">&#171; anterior</a>';
if($PagAct<$PagUlt)  echo '<a style="cursor: pointer;" onclick="ComuTemPerfil(\''  . $user . '\',\'' . $PagSig . '\');">siguiente &#187;</a>';
echo '</div><div class="clearBoth"></div></div>';
}