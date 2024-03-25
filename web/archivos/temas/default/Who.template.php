<?php

// The only template in the file.

function template_main()
{
	global $context, $settings, $options, $scripturl, $txt;

	$brokendown = array (
		'Miembros' => array(),
		'Visitantes' => array(),
		'Spiders' => array(),
	);
	
	foreach($context['members'] AS $key => $member)
	{
		$spider = getAgent($member['query']['USER_AGENT'], $context['members'][$key]['name'], $agent, $member['id'] == 0);
		$context['members'][$key]['agent'] = $agent;
		$member['query']['USER_AGENT'] = isset($member['query']['USER_AGENT']) ? $member['query']['USER_AGENT'] : '';
		if ( $member['id'] != 0 )
			$brokendown['Miembros'][] = &$context['members'][$key];
		else if ( $spider )
			$brokendown['Spiders'][] = &$context['members'][$key];
		else
			$brokendown['Visitantes'][] = &$context['members'][$key];
	}
//	echo '<pre>'; print_r($brokendown['Spiders']); echo '</pre>'; return;
	foreach($brokendown AS $group => $members)
	{
		echo '<table class="linksList"><thead align="center"><tr><th><a href="' . $scripturl . '?action=who;start=', $context['start'], ';sort=user', $context['sort_direction'] != 'down' && $context['sort_by'] == 'user' ? '' : ';asc', '">';
if($group == 'Miembros') {
	echo 'Miembros';
} elseif($group == 'Visitantes') {
	echo 'Visitantes';
} elseif($group == 'Spiders') {
	echo 'Spiders';
}
echo '</a></th>';
if($context['allow_admin']) {
echo '<th>IP</th>';
}
echo '<th><a href="' . $scripturl . '?action=who;start=', $context['start'], ';sort=time', $context['sort_direction'] == 'down' && $context['sort_by'] == 'time' ? ';asc' : '', '">', $txt['who_time'], ' ', $context['sort_by'] == 'time' ? '' : '', '</a></th><th>', $txt['who_action'], '</th></tr></thead><tbody>';


		// This is used to alternate the color of the background.
		$alternate = true;

		// For every member display their name, time and action (and more for admin).
		foreach ($members as $member)
		{

			// $alternate will either be true or false.  If it's true, use "windowbg2" and otherwise use "windowbg".
			echo '
			<tr class="windowbg', $alternate ? '2' : '', '">
				<td>';


		echo '
				<span', $member['is_hidden'] ? ' style="font-style: italic;"' : '', '>', $member['is_guest'] ? $member['name'] : '<a href="' . $member['href'] . '" title="' . $txt[92] . ' ' . $member['name'] . '"' . (empty($member['color']) ? '' : ' style="color: ' . $member['color'] . '"') . '>' . $member['name'] . '</a>', '</span></td>';

		if (!empty($member['ip']) && $context['allow_admin']){
			echo ' <td><a href="' . $scripturl . '?action=trackip;searchip=' . $member['ip'] . '" target="_blank" title="' . $member['ip'] . '" >' . $member['ip'] . '</a>, (<acronym title="' . $member['query']['USER_AGENT'] . '">' . $member['agent'] . '</acronym>)</span>';	
}
		echo '
			</td>
			<td nowrap="nowrap">', $member['time'], '</td>
			<td>', $member['action'], '</td>

		</tr>

		';

		// Switch alternate to whatever it wasn't this time. (true -> false -> true -> false, etc.)
		$alternate = !$alternate;
			$contar++;

		}
	echo '</tbody></table><br />';

	}


echo '<br /><div class="noesta">Tenemos un total de ' . $contar++ . ' usuarios conectados...</div><br />';
	echo '<tr class="linksList"><td align="center"><b>', $txt[139], ':</b> ', $context['page_index'], '</td></tr>';
		

}

function getAgent( &$user_agent, &$user_name, &$result, $guest )
{
	$known_agents = array (
				//Search Spiders
		array (
			'agent' => 'WISENutbot',
			'spidername' => 'Looksmart spider',
								'spider' => true,
		),
		array (
			'agent' => 'MSNBot',
			'spidername' => 'MSN spider',
			'spider' => true,
		),
		array (
			'agent' => 'W3C_Validator',
			'spidername' => 'W3C Validaator',
				  'spider' => true,
		),
		array (
			'agent' => 'Googlebot-Image',
			'spidername' => 'Google-Image Spider',
			'spider' => true,
		),
		array (
			'agent' => 'Googlebot',
			'spidername' => 'Google spider',
			'spider' => true,
		),

		array (
			'agent' => 'Mediapartners-Google',
			'spidername' => 'Google AdSense spider',
			'spider' => true,
		),

		array (
			'agent' => 'Openbot',
			'spidername' => 'Openfind spider',
			'spider' => true,
		  ),
	
		array (
			'agent' => 'Yahoo! Slurp',
			'spidername' => 'Yahoo spider',
			'spider' => true,
		),

		array (
			'agent' => 'FAST-WebCrawler',
			'spider' => true,

		),
		array (
			'agent' => 'Wget',
			'spider' => true,
		),
		array (
			'agent' => 'Ask Jeeves', 
			'spider' => true,
		),
		array (
			'agent' => 'Speedy Spider',
			'spider' => true,
		),
		array (
			'agent' => 'SurveyBot',
			'spider' => true,
		),
		array (
			'agent' => 'IBM_Planetwide',
			'spider' => true,
		),

		array (
			'agent' => 'GigaBot',
			'spider' => true,
		),
		
		array (
			'agent' => 'ia_archiver',
			'spider' => true,
		),
		
		array (
			'agent' => 'FAST-WebCrawler',
			'spider' => true,
		),

		array (
			'agent' => 'Inktomi Slurp',
			'spider' => true,
		),
					 
		array (
			'agent' => 'appie',
			'spidername' => 'Walhello spider',
			'spider' => true,
		),

				//mobiles
		array (
			'agent' => 'Nokia', 
		 ),
		
		array (
			'agent' => 'Samsung',
		),
		 array (
			'agent' => 'Ericsson',
		),
		array (
			'agent' => 'Siemens',
		),
		array (
			'agent' => 'Motorola',
		),
		  
				//Browsers
		 array (
			'agent' => 'Opera',
		),
		array (
			'agent' => 'Firefox',
		),
		array (
			'agent' => 'Firebird',
		),
		array (
			'agent' => 'Safari', 
		),
		  array (
			'agent' => 'Netscape',
		),
		array (
			'agent' => 'MyIE2', 
		),
		array (
			'agent' => 'Konqueror', 
		),
		array (
			'agent' => 'Galeon', 
		),
		array (
			'agent' => 'KMeleon',
		),
		  array (
			'agent' => 'NG/2.0',
		),
		  array (
			'agent' => 'Gecko',
			'name' => 'Mozilla',
		  ),
		  array (
			'agent' => 'MSIE',

		),
	);

foreach( $known_agents AS $poss )
		if (strpos(strtolower($user_agent), strtolower($poss['agent'])) !== false)
		{
			if ( $guest && isset($poss['spider']) && $poss['spider'] )
				$user_name = isset($poss['spidername']) ? $poss['spidername'] : (isset($poss['name']) ? $poss['name'] : $poss['agent']); 
			$result = isset($poss['name']) ? $poss['name'] : $poss['agent']; 
			return isset($poss['spider']) && $poss['spider'];
		}
	$result = $user_agent;
	return false;}

?>