<?php

/**
*
* @package phpBB Extension - Meeting
* @copyright (c) 2014 OXPUS - www.oxpus.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

/*
* connect to phpBB
*/
if ( !defined('IN_PHPBB') )
{
	exit;
}

define('MEETING_COMMENT_TABLE',		$table_prefix.'meeting_comment');
define('MEETING_DATA_TABLE',		$table_prefix.'meeting_data');
define('MEETING_GUESTNAMES_TABLE',	$table_prefix.'meeting_guestnames');
define('MEETING_USER_TABLE',		$table_prefix.'meeting_user');
define('MEETING_USERGROUP_TABLE',	$table_prefix.'meeting_usergroup');
