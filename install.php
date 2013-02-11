<?php

/**
* @package manifest file for Live clock in header
* @version 1.0 Alpha
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

global $smcFunc, $db_prefix;

if (!array_key_exists('db_add_column', $smcFunc))
db_extend('packages');

$table = array(
	'table_name' => 'live_clock_timezones',
	'columns' => array(
		array(
			'name' => 'id_zone',
			'type' => 'smallint',
			'unsigned' => true,
			'size' => 5,
            'auto' => true,
		),
		array(
			'name' => 'zone_name',
			'type' => 'varchar',
			'size' => 255,
			'default' => '',
		),
		array(
			'name' => 'zone_diff',
			'type' => 'varchar',
			'size' => 255,
			'default' => '0',
		),
	),
	'indexes' => array(
        array(
            'type' => 'primary',
            'columns' => array('id_zone'),
        ),
    ),
);
$smcFunc['db_create_table']('{db_prefix}' . $table['table_name'], $table['columns'], $table['indexes']);

$general_settings = array(
	'lc_mod_enable' => 0,
    'lc_forum_timezone_offset' => 0,
    'lc_24_hr_format' => 0,
);

foreach ($general_settings as $key => $value) {
    $smcFunc['db_insert']('ignore',
        '{db_prefix}settings', array('variable' => 'string', 'value' => 'string'),
        array($key, $value), ''
    );
}

add_integration_function('integrate_pre_include', '$sourcedir/LiveClockHooks.php');
add_integration_function('integrate_admin_areas', 'LC_addAdminPanel');
add_integration_function('integrate_actions', 'LC_addAction', true);


if (SMF == 'SSI')
	echo 'Database adaptation successful!';

?>