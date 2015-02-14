<?php

/**
*
* @package phpBB Extension - Meeting
* @copyright (c) 2014 OXPUS - www.oxpus.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace oxpus\meeting\migrations;

class release_3_0_0 extends \phpbb\db\migration\migration
{
	var $ext_version = '3.0.0';

	public function effectively_installed()
	{
		return isset($this->config['meeting_version']) && version_compare($this->config['meeting_version'], $this->ext_version, '>=');
	}

	public function update_data()
	{
		return array(
			// Set the current version
			array('config.add', array('meeting_version', $this->ext_version)),

			// Preset the config data
			array('config.add', array('meeting_notify', '1')),
			array('config.add', array('meeting_user_delete', '0')),
			array('config.add', array('meeting_user_delete_comments', '0')),
			array('config.add', array('meeting_user_edit', '0')),
			array('config.add', array('meeting_user_enter', '0')),
			array('config.add', array('meeting_sign_perm', '3')),
			array('config.add', array('meeting_first_weekday', 'm')),

			array('module.add', array(
 				'acp',
 				'ACP_CAT_DOT_MODS',
 				'ACP_MEETING'
 			)),
			array('module.add', array(
				'acp',
				'ACP_MEETING',
				array(
					'module_basename'	=> '\oxpus\meeting\acp\main_module',
					'modes'				=> array('config', 'add', 'manage'),
				),
			)),

			// The needed permissions
			array('permission.add', array('a_meeting_config')),
			array('permission.add', array('a_meeting_add')),
			array('permission.add', array('a_meeting_manage')),
			array('permission.add', array('u_meeting')),

			// Join permissions to administrators
			array('permission.permission_set', array('ROLE_ADMIN_FULL', 'a_meeting_config')),
			array('permission.permission_set', array('ROLE_ADMIN_FULL', 'a_meeting_add')),
			array('permission.permission_set', array('ROLE_ADMIN_FULL', 'a_meeting_manage')),
			array('permission.permission_set', array('REGISTERED', 'u_meeting', 'group')),
		);
	}
			
	public function update_schema()
	{
		return array(
			'add_tables'	=> array(
				$this->table_prefix . 'meeting_comment' => array(
					'COLUMNS'		=> array(
						'comment_id'		=> array('UINT', NULL, 'auto_increment'),
						'meeting_id'		=> array('UINT', 0),
						'user_id'			=> array('INT:8', 0),
						'meeting_comment'	=> array('MTEXT_UNI', ''),
						'meeting_edit_time'	=> array('INT:11', 0),
						'approve'			=> array('BOOL', 0),
						'uid'				=> array('VCHAR:8', ''),
						'bitfield'			=> array('VCHAR:255', ''),
						'flags'				=> array('UINT:11', 0),
					),
					'PRIMARY_KEY'	=> 'comment_id',
				),

				$this->table_prefix . 'meeting_data' => array(
					'COLUMNS'		=> array(
						'meeting_id'			=> array('UINT', 0),
						'meeting_time'			=> array('INT:11', 0),
						'meeting_end'			=> array('INT:11', 0),
						'meeting_until'			=> array('INT:11', 0),
						'meeting_location'		=> array('VCHAR', ''),
						'meeting_subject'		=> array('STEXT_UNI', ''),
						'meeting_desc'			=> array('MTEXT_UNI', ''),
						'meeting_link'			=> array('VCHAR', ''),
						'meeting_places'		=> array('UINT:8', 0),
						'meeting_by_user'		=> array('UINT:8', 0),
						'meeting_edit_by_user'	=> array('UINT:8', 0),
						'meeting_start_value'	=> array('UINT:8', 0),
						'meeting_recure_value'	=> array('UINT:8', 0),
						'meeting_notify'		=> array('BOOL', 0),
						'meeting_guest_overall'	=> array('INT:8', 0),
						'meeting_guest_single'	=> array('INT:8', 0),
						'meeting_guest_names'	=> array('BOOL', 0),
						'uid'					=> array('CHAR:8', ''),
						'bitfield'				=> array('VCHAR', ''),
						'flags'					=> array('UINT:11', 0),
					),
					'PRIMARY_KEY'	=> 'meeting_id',
				),

				$this->table_prefix . 'meeting_guestnames' => array(
					'COLUMNS'		=> array(
						'meeting_id'	=> array('INT:8', 0),
						'user_id'		=> array('INT:8', 0),
						'guest_prename'	=> array('VCHAR', ''),
						'guest_name'	=> array('VCHAR', ''),
					),
				),


				$this->table_prefix . 'meeting_user' => array(
					'COLUMNS'		=> array(
						'meeting_id'		=> array('UINT', 0),
						'user_id'			=> array('INT:8', 0),
						'meeting_sure'		=> array('TINT:4', 0),
						'meeting_guests'	=> array('INT:8', 0),
					),
				),


				$this->table_prefix . 'meeting_usergroup' => array(
					'COLUMNS'		=> array(
						'meeting_id'	=> array('UINT', 0),
						'meeting_group'	=> array('INT:8', 0),
					),
				),
			),

			'add_columns'	=> array(
				$this->table_prefix . 'groups'		=> array(
					'group_meeting_create'			=> array('BOOL', 0),
					'group_meeting_select'			=> array('BOOL', 0),
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_tables' => array(
				$this->table_prefix . 'meeting_comment',
				$this->table_prefix . 'meeting_data',
				$this->table_prefix . 'meeting_guestnames',
				$this->table_prefix . 'meeting_user',
				$this->table_prefix . 'meeting_usergroup',
			),

			'drop_columns'	=> array(
				$this->table_prefix . 'groups' => array(
					'group_meeting_create',
					'group_meeting_select',
				),
			),
		);
	}
}
