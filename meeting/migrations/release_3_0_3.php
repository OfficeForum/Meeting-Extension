<?php

/**
*
* @package phpBB Extension - Meeting
* @copyright (c) 2014 OXPUS - www.oxpus.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace oxpus\meeting\migrations;

class release_3_0_3 extends \phpbb\db\migration\migration
{
	var $ext_version = '3.0.3';

	public function effectively_installed()
	{
		return isset($this->config['meeting_version']) && version_compare($this->config['meeting_version'], $this->ext_version, '>=');
	}

	static public function depends_on()
	{
		return array('\oxpus\meeting\migrations\release_3_0_2');
	}

	public function update_data()
	{
		return array(
			// Set the current version
			array('config.update', array('meeting_version', $this->ext_version)),
		);
	}
}
