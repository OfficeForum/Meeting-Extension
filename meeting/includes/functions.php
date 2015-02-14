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

function meeting_date($gmepoch, $user)
{
	static $midnight;
	static $date_cache;

	$format = $user->data['user_dateformat'];
	$now = time();
	$delta = $now - $gmepoch;

	$date_cache[$format] = array(
		'is_short'		=> strpos($format, '|'),
		'format_short'	=> substr($format, 0, strpos($format, '|')) . '||' . substr(strrchr($format, '|'), 1),
		'format_long'	=> str_replace('|', '', $format),
		'lang'			=> $user->lang['datetime'],
	);

	// Short representation of month in format? Some languages use different terms for the long and short format of May
	if ((strpos($format, '\M') === false && strpos($format, 'M') !== false) || (strpos($format, '\r') === false && strpos($format, 'r') !== false))
	{
		$date_cache[$format]['lang']['May'] = $user->lang['datetime']['May_short'];
	}

	// Show date <= 1 hour ago as 'xx min ago'
	// A small tolerence is given for times in the future and times in the future but in the same minute are displayed as '< than a minute ago'
	if ($delta <= 3600 && ($delta >= -5 || (($now / 60) % 60) == (($gmepoch / 60) % 60)) && $date_cache[$format]['is_short'] !== false && isset($user->lang['datetime']['AGO']))
	{
		return $user->lang(array('datetime', 'AGO'), max(0, (int) floor($delta / 60)));
	}

	if (!$midnight)
	{
		list($d, $m, $y) = explode(' ', date('j n Y', time()));
		$midnight = mktime(0, 0, 0, $m, $d, $y);
	}

	if ($date_cache[$format]['is_short'] !== false)
	{
		$day = false;

		if ($gmepoch > $midnight + 2 * 86400)
		{
			$day = false;
		}
		else if($gmepoch > $midnight + 86400)
		{
			$day = 'TOMORROW';
		}
		else if ($gmepoch > $midnight)
		{
			$day = 'TODAY';
		}
		else if ($gmepoch > $midnight - 86400)
		{
			$day = 'YESTERDAY';
		}

		if ($day !== false)
		{
			return str_replace('||', $user->lang['datetime'][$day], @strtr(@date($date_cache[$format]['format_short'], $gmepoch), $date_cache[$format]['lang']));
		}
	}

	return @strtr(@date($date_cache[$format]['format_long'], $gmepoch), $date_cache[$format]['lang']);
}

function meeting_date_save($date_string, $time_string)
{
	if (!$date_string)
	{
		return time();
	}

	$year	= substr($date_string, 0, 4);
	$month	= substr($date_string, 5, 2);
	$day	= substr($date_string, 8, 2);
	$hour	= substr($time_string, 0, 2);
	$minute	= substr($time_string, 3, 2);

	$date_check = @mktime($hour, $minute, 0, $month, $day, $year);

	if (!$date_check || $date_check === false)
	{
		return 0;
	}			

	return $date_check;
}

function meeting_date_edit($module, $date, $user)
{
	if (!$date)
	{
		$date = time();
	}

	$day	= date('d', $date);
	$month	= date('m', $date);
	$year	= date('Y', $date);
	$hour	= date('H', $date);
	$minute	= date('i', $date);

	$date = sprintf("%04s-%02s-%02s", $year, $month, $day);
	$time = sprintf("%02s:%02s", $hour, $minute);

	return '<input type="text" name="' . $module . '" id="' . $module . '" class="tcal" value="' . $date . '" readonly="readonly" size="11" maxlength="10" /><a href="#" onclick="DropDate(\'' . $module . '\');" style="text-decoration: none; border: 1px #A9B8C2 solid; padding: 1px 2px 1px 2px; background-color: #ECECEC;" /><strong>X</strong></a>&nbsp&nbsp;<input type="text" name="' . $module . '_time" size="5" maxlength="5" value="' . $time . '" />';
}
