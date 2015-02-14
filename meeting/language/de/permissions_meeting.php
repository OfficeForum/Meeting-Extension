<?php

/**
*
* @package phpBB Extension - Meeting
* @copyright (c) 2014 OXPUS - www.oxpus.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

/**
* Language pack for Extension permissions [German]
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// Permissions
$lang = array_merge($lang, array(
	'ACP_MEETING'			=> 'Treffen',

	'ACL_A_MEETING_CONFIG'	=> 'Kann die Einstellungen für Treffen ändern',
	'ACL_A_MEETING_ADD'		=> 'Kann neue Treffen erfassen',
	'ACL_A_MEETING_MANAGE'	=> 'Kann bestehende Treffen bearbeiten',

	'ACL_U_MEETING'			=> 'Kann Treffen bearbeiten',
));
