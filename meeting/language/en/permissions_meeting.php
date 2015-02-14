<?php

/**
*
* @package phpBB Extension - Meeting
* @copyright (c) 2014 OXPUS - www.oxpus.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

/**
* Language pack for Extension permissions [English]
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

	'ACL_A_MEETING_CONFIG'	=> array('lang' => 'Can edit the meeting configuration',	'cat' => 'meeting'),
	'ACL_A_MEETING_ADD'		=> array('lang' => 'Can add new meetings',					'cat' => 'meeting'),
	'ACL_A_MEETING_MANAGE'	=> array('lang' => 'Can manage existing meetings',			'cat' => 'meeting'),

	'ACL_U_MEETING'			=> array('lang' => 'Can edit meetings',						'cat' => 'meeting'),
));
