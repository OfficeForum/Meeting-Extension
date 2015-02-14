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
	'ACP_MEETING'			=> 'Meetings',

	'ACL_A_MEETING_CONFIG'	=> array('lang' => 'Kann die Meetings Einstellungen ändern',	'cat' => 'meeting'),
	'ACL_A_MEETING_ADD'		=> array('lang' => 'Kann neue Meetings erfasssen',				'cat' => 'meeting'),
	'ACL_A_MEETING_MANAGE'	=> array('lang' => 'Kann bestehende Meetings bearbeiten',		'cat' => 'meeting'),

	'ACL_U_MEETING'			=> array('lang' => 'Kann Meetings bearbeiten',					'cat' => 'meeting'),
));
