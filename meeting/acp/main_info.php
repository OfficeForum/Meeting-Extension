<?php

/**
*
* @package phpBB Extension - Meeting
* @copyright (c) 2014 OXPUS - www.oxpus.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace oxpus\meeting\acp;

class main_info
{
	function module()
	{
		global $config;

		return array(
			'filename'	=> '\oxpus\meeting\acp\main_info',
			'title'		=> 'ACP_MEETING',
			'version'	=> $config['meeting_version'],
			'modes'		=> array(
				'config'	=> array('title' => 'MEETING_CONFIG',	'auth' => 'ext_oxpus/meeting && acl_a_meeting_config',	'cat' => array('ACP_MEETING')),
				'add'		=> array('title' => 'MEETING_ADD',		'auth' => 'ext_oxpus/meeting && acl_a_meeting_add',		'cat' => array('ACP_MEETING')),
				'manage'	=> array('title' => 'MEETING_MANAGE',	'auth' => 'ext_oxpus/meeting && acl_a_meeting_manage',	'cat' => array('ACP_MEETING')),
			),
		);
	}
}
