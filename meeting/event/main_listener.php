<?php

/**
*
* @package phpBB Extension - Meeting
* @copyright (c) 2014 OXPUS - www.oxpus.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace oxpus\meeting\event;

/**
* @ignore
*/
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class main_listener implements EventSubscriberInterface
{
	static public function getSubscribedEvents()
	{
		return array(
			'core.user_setup'						=> 'load_language_on_setup',
			'core.page_header'						=> 'add_page_header_links',
			'core.viewonline_overwrite_location'	=> 'add_viewonline',
			'core.update_username'					=> 'change_username',
			'core.delete_user_after'				=> 'delete_user',
			'core.permissions'						=> 'add_permission_cat',
		);
	}

	/* @var string phpbb_root_path */
	protected $root_path;

	/* @var string phpEx */
	protected $php_ext;

	/* @var string table_prefix */
	protected $table_prefix;

	/* @var \phpbb\extension\manager */
	protected $phpbb_extension_manager;
	
	/* @var \phpbb\path_helper */
	protected $phpbb_path_helper;

	/* @var Container */
	protected $phpbb_container;

	/* @var \phpbb\db\driver\driver_interface */
	protected $db;

	/* @var \phpbb\config\config */
	protected $config;

	/* @var \phpbb\controller\helper */
	protected $helper;

	/* @var \phpbb\auth\auth */
	protected $auth;

	/* @var \phpbb\template\template */
	protected $template;
	
	/* @var \phpbb\user */
	protected $user;

	/**
	* Constructor
	*
	* @param string									$root_path
	* @param string									$php_ext
	* @param string									$table_prefix
	* @param \phpbb\extension\manager				$phpbb_extension_manager
	* @param \phpbb\path_helper						$phpbb_path_helper
	* @param Container								$phpbb_container
	* @param \phpbb\db\driver\driver_interfacer		$db
	* @param \phpbb\config\config					$config
	* @param \phpbb\controller\helper				$helper
	* @param \phpbb\auth\auth						$auth
	* @param \phpbb\template\template				$template
	* @param \phpbb\user							$user
	*/
	public function __construct($root_path, $php_ext, $table_prefix, \phpbb\extension\manager $phpbb_extension_manager, \phpbb\path_helper $phpbb_path_helper, Container $phpbb_container, \phpbb\db\driver\driver_interface $db, \phpbb\config\config $config, \phpbb\controller\helper $helper, \phpbb\auth\auth $auth, \phpbb\template\template $template, \phpbb\user $user)
	{
		$this->root_path				= $root_path;
		$this->php_ext 					= $php_ext;
		$this->table_prefix 			= $table_prefix;
		$this->phpbb_extension_manager	= $phpbb_extension_manager;
		$this->phpbb_path_helper		= $phpbb_path_helper;
		$this->phpbb_container 			= $phpbb_container;
		$this->db 						= $db;
		$this->config 					= $config;
		$this->helper 					= $helper;
		$this->auth						= $auth;
		$this->template 				= $template;
		$this->user 					= $user;
	}

	public function load_language_on_setup($event)
	{	
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name' => 'oxpus/meeting',
			'lang_set' => 'common',
		);

		if (defined('ADMIN_START'))
		{
			$lang_set_ext[] = array(
				'ext_name' => 'oxpus/meeting',
				'lang_set' => 'permissions_meeting',
			);
		}

		$event['lang_set_ext'] = $lang_set_ext;

	}

	public function add_page_header_links($event)
	{
		$ext_path					= $this->phpbb_extension_manager->get_extension_path('oxpus/meeting', true);
		$this->phpbb_path_helper	= $this->phpbb_container->get('path_helper');
		$ext_path_web				= $this->phpbb_path_helper->update_web_root_path($ext_path);

		$table_prefix = $this->table_prefix;
		include_once($ext_path . 'includes/helpers/constants.' . $this->php_ext);

		$this->template->assign_vars(array(
			'MEETING_EXT_PATH'			=> $ext_path,
			'MEETING_EXT_PATH_WEB'		=> $ext_path_web,
		));

		$ext_main_link = $this->helper->route('meeting_controller');

		if (!defined('IN_MEETING') && $this->auth->acl_get('u_meeting') && !defined('ADMIN_START'))
		{
			$this->db->return_on_error = true;
	
			// Get access status for all meetings
			$sql = "SELECT m.meeting_id, mg.meeting_group FROM " . MEETING_DATA_TABLE . " m, " . MEETING_USERGROUP_TABLE . " mg
				WHERE mg.meeting_id = m.meeting_id";
			$result = $this->db->sql_query($sql);
		
			$meetings_access_ids = array();
		
			while ($row = $this->db->sql_fetchrow($result))
			{
				$meeting_id		= $row['meeting_id'];
				$meeting_group	= $row['meeting_group'];
		
				if ($meeting_group == -1)
				{
					$meetings_access_ids[] = $meeting_id;
				}
				else
				{
					$sql_auth_id = "SELECT g.group_id FROM " . GROUPS_TABLE . " g, " . USER_GROUP_TABLE . " ug
							WHERE g.group_id IN (0, $meeting_group)
							AND g.group_id = ug.group_id
							AND ug.user_pending <> " . TRUE . "
							AND ug.user_id = " . $this->user->data['user_id'];
					$result_auth_id = $this->db->sql_query($sql_auth_id);
					$count_usergroups = $this->db->sql_affectedrows($result_auth_id);
					$this->db->sql_freeresult($result_auth_id);
		
					if ($count_usergroups > 0)
					{
						$meetings_access_ids[] = $meeting_id;
					}
				}
			}
		
			$this->db->sql_freeresult($result);
		
			if (sizeof($meetings_access_ids) > 0)
			{
				$meeting_ids = (sizeof($meetings_access_ids) == 1) ? $meetings_access_ids[0] : implode(',', $meetings_access_ids);
				$sql_meeting_access = ' WHERE meeting_id IN ('.$meeting_ids.') AND meeting_time > '.time();
			}
			else if ($this->auth->acl_get('a_') && $this->user->data['is_registered'])
			{
				$sql_meeting_access = ' WHERE meeting_time > '.time();
			}
			else
			{
				$sql_meeting_access = '';
			}
		
			if ($sql_meeting_access != '')
			{
				$sql = "SELECT COUNT(meeting_id) AS total FROM " . MEETING_DATA_TABLE . "
					$sql_meeting_access";
				$result = $this->db->sql_query($sql);
				$row = $this->db->sql_fetchrow($result);
				$meeting_active_ids = $row['total'];
				$this->db->sql_freeresult($result);
			}
			else
			{
				$meeting_active_ids = 0;
			}
		
			if (!$meeting_active_ids)
			{
				$meeting_active_string = $this->user->lang['NO_ACTIVE_MEETINGS'];
			}
			else if ($meeting_active_ids == 1)
			{
				$meeting_active_string = $this->user->lang['ONE_ACTIVE_MEETING'];
			}
			else
			{
				$meeting_active_string = sprintf($this->user->lang['ACTIVE_MEETINGS'], $meeting_active_ids);
			}
		
			$u_meeting_link = $ext_main_link;
			$l_meeting_link = $meeting_active_string;
		}
		else
		{
			$u_meeting_link = '';
			$l_meeting_link = '';
		}
	
		$this->db->return_on_error = false;

		$this->template->assign_vars(array(
			'L_MEETING_LINK_N'		=> $l_meeting_link,
			'U_MEETING_LINK_N'		=> $u_meeting_link,
		));

		if (defined('IN_MEETING') && ($this->auth->acl_get('u_meeting') || $this->auth->acl_get('a_meeting_add') || $this->auth->acl_get('a_meeting_manage')))
		{
			$this->template->assign_var('S_MEETING_JS', true);
		}
	}

	public function add_viewonline($event)
	{
		if ($event['row']['session_page'] === 'app.php/meeting' || $event['row']['session_page'] === 'app.' . $this->php_ext . '/meeting.php')
		{
			$ext_link = $this->helper->route('meeting_controller');

			$event['location'] = $this->user->lang('MEETING');
			$event['location_url'] = $ext_link;
		}
	}

	public function change_username($event)
	{
		$ext_path = $this->phpbb_extension_manager->get_extension_path('oxpus/meeting', true);
		$table_prefix = $this->table_prefix;
		include_once($ext_path . '/includes/helpers/constants.' . $this->php_ext);

		$update_ary = array(MEETING_COMMENT_TABLE, MEETING_GUESTNAMES_TABLE, MEETING_USER_TABLE);

		foreach ($update_ary as $table)
		{
			$sql = "UPDATE $table
				SET username = '" . $this->db->sql_escape($event['new_name']) . "'
				WHERE username = '" . $this->db->sql_escape($event['old_name']) . "'";
			$this->db->sql_query($sql);
		}	
	}

	public function delete_user($event)
	{
		$ext_path = $this->phpbb_extension_manager->get_extension_path('oxpus/meeting', true);
		$table_prefix = $this->table_prefix;
		include_once($ext_path . '/includes/helpers/constants.' . $this->php_ext);

		$table_ary = array(MEETING_COMMENT_TABLE, MEETING_GUESTNAMES_TABLE, MEETING_USER_TABLE);
	
		// Delete the miscellaneous (non-post) data for the user
		foreach ($table_ary as $table)
		{
			$sql = "DELETE FROM $table
				WHERE " . $this->db->sql_in_set('user_id', $event['user_ids']);
			$this->db->sql_query($sql);
		}
	}

	public function add_permission_cat($event)
	{
		$perm_cat = $event['categories'];
		$perm_cat['meetings'] = 'ACP_MEETING';
		$event['categories'] = $perm_cat;

		$permission = $event['permissions'];
		$permission['a_meeting_config']	= array('lang' => 'ACL_A_MEETING_CONFIG',	'cat' => 'meetings');
		$permission['a_meeting_add']	= array('lang' => 'ACL_A_MEETING_ADD',		'cat' => 'meetings');
		$permission['a_meeting_manage']	= array('lang' => 'ACL_A_MEETING_MANAGE',	'cat' => 'meetings');
		$event['permissions'] = $permission;
	}
}
