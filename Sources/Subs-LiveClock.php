<?php

/**
* @package manifest file for Live clock in header
* @version 1.1.1
* @author Joker (http://www.simplemachines.org/community/index.php?action=profile;u=226111)
* @copyright Copyright (c) 2012, Siddhartha Gupta
* @license http://www.mozilla.org/MPL/MPL-1.1.html
*/

/*
* Version: MPL 1.1
*
* The contents of this file are subject to the Mozilla Public License Version
* 1.1 (the "License"); you may not use this file except in compliance with
* the License. You may obtain a copy of the License at
* http://www.mozilla.org/MPL/
*
* Software distributed under the License is distributed on an "AS IS" basis,
* WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
* for the specific language governing rights and limitations under the
* License.
*
* The Initial Developer of the Original Code is
*  Joker (http://www.simplemachines.org/community/index.php?action=profile;u=226111)
* Portions created by the Initial Developer are Copyright (C) 2012
* the Initial Developer. All Rights Reserved.
*
* Contributor(s):
*
*/

if (!defined('SMF'))
	die('Hacking attempt...');

function LC_getALlTimeZones() {
	global $smcFunc;

	$request = $smcFunc['db_query']('', '
		SELECT id_zone, zone_name, zone_diff
		FROM {db_prefix}live_clock_timezones',
		array()
	);
	if ($smcFunc['db_num_rows']($request) == 0)
		return;

	$timezones = array();
	while ($row = $smcFunc['db_fetch_assoc']($request)) {
		$timezones[$row['id_zone']] = array(
			'id_zone' => $row['id_zone'],
			'zone_name' => $row['zone_name'],
			'zone_diff' => $row['zone_diff'],
		);
	}
	$smcFunc['db_free_result']($request);

	return $timezones;
}

function LC_updateTimeZones($data = array()) {
	global $smcFunc;

	if (!is_array($data))
		return false;

	//Just empty the data and add new data
	LC_clearAllTimezones();

	foreach ($data as $val) {
		$smcFunc['db_insert']('',
			'{db_prefix}live_clock_timezones',
			array(
				'zone_name' => 'string', 'zone_diff' => 'string',
			),
			array(
				$val['zone_name'], $val['zone_diff'],
			),
			array()
		);
	}
}


function LC_clearAllTimezones() {
	global $smcFunc;

	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}live_clock_timezones',
		array()
	);
}

function LC_getUserTimezone()  {
	global $smcFunc, $user_info;

	if ($user_info['is_guest']) {
		return false;
	}

	$request = $smcFunc['db_query']('', '
		SELECT ct.zone_diff
		FROM {db_prefix}live_clock_user_zone as uz 
		INNER JOIN {db_prefix}live_clock_timezones AS ct ON (uz.id_zone = ct.id_zone)
		WHERE uz.id_member = {int:id_member}
		LIMIT 1',
		array(
			'id_member' => $user_info['id'],
		)
	);
	if ($smcFunc['db_num_rows']($request) == 0)
		return;

	list ($zone_diff) = $smcFunc['db_fetch_row']($request);
	$smcFunc['db_free_result']($request);
	return $zone_diff;
}

function LC_updateUserDBZone($timezoneID = '') {
	global $smcFunc, $user_info;

	if (empty($timezoneID)) {
		return false;
	}
	
	if ($user_info['is_guest']) {
		return false;
	}

	$replaceArray[] = array($user_info['id'], $timezoneID);
	$smcFunc['db_insert']('replace',
		'{db_prefix}live_clock_user_zone',
		array('id_member' => 'int', 'id_zone' => 'int'),
		$replaceArray,
		array('id_member')
	);
	return true;
}

function LC_resetDBTimezones($data = array()) {
	global $smcFunc;

	if (!is_array($data))
		return false;

	//Just empty the data and add new data
	LC_clearAllTimezones();

	foreach ($data as $key => $val) {
		$smcFunc['db_insert']('',
			'{db_prefix}live_clock_timezones',
			array(
				'zone_name' => 'string', 'zone_diff' => 'string',
			),
			array(
				$key, $val,
			),
			array()
		);
	}
	return true;
}

?>