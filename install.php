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

// If SSI.php is in the same place as this file, and SMF isn't defined...
if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
	require_once(dirname(__FILE__) . '/SSI.php');

// Hmm... no SSI.php and no SMF?
elseif (!defined('SMF'))
	die('<b>Error:</b> Cannot uninstall - please verify you put this in the same place as SMF\'s index.php.');

global $smcFunc, $db_prefix, $sourcedir;

if (!array_key_exists('db_add_column', $smcFunc))
db_extend('packages');

$tables = array(
	'live_clock_timezones' => array(
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
    ),
    'live_clock_user_zone' => array(
        'columns' => array(
            array(
                'name' => 'id_member',
                'type' => 'mediumint',
                'size' => 8,
                'unsigned' => true,
                'null' => false,
                'default' => 0,
            ),
            array(
                'name' => 'id_zone',
                'type' => 'smallint',
                'size' => 2,
                'unsigned' => true,
                'null' => false,
                'default' => 0,
            ),
        ),
        'indexes' => array(
			array(
				'type' => 'primary',
				'columns' => array('id_member'),
			),
		),
    )
);

foreach ($tables as $table => $data) {
	$smcFunc['db_create_table']('{db_prefix}' . $table, $data['columns'], $data['indexes']);
}

updateSettings(array('lc_mod_enable' => 1, 'lc_show_timezone_dropdown' => 1, 'lc_24_hr_format' => 0, 'lc_show_date' => 1));

$live_clock_timezones = array(
    'Eniwetok, Kwajalein' => '-12',
    'Midway Island, Samoa' => '-11',
    'Hawaii' => '-10',
    'Alaska' => '-9',
    'Pacific Time (US &amp; Canada)' => '-8',
    'Mountain Time (US &amp; Canada)' => '-7',
    'Central Time (US &amp; Canada), Mexico City' => '-6',
    'Eastern Time (US &amp; Canada), Bogota, Lima' => '-5',
    'Atlantic Time (Canada), Caracas, La Paz' => '-4',
    'Newfoundland' => '-3.5',
    'Brazil, Buenos Aires, Georgetown' => '-3',
    'Mid-Atlantic' => '-2',
    'Azores, Cape Verde Islands' => '-1',
    'Western Europe Time, London, Lisbon, Casablanca' => '0',
    'Brussels, Copenhagen, Madrid, Paris' => '+1',
    'Kaliningrad, South Africa' => '+2',
    'Baghdad, Riyadh, Moscow, St. Petersburg' => '+3',
    'Tehran' => '+3.5',
    'Abu Dhabi, Muscat, Baku, Tbilisi' => '+4',
    'Kabul' => '+4.5',
    'Ekaterinburg, Islamabad, Karachi, Tashkent' => '+5',
    'Bombay, Calcutta, Madras, New Delhi' => '+5.5',
    'Kathmandu' => '+5.75',
    'Almaty, Dhaka, Colombo' => '+6',
    'Bangkok, Hanoi, Jakarta' => '+7',
    'Beijing, Perth, Singapore, Hong Kong' => '+8',
    'Tokyo, Seoul, Osaka, Sapporo, Yakutsk' => '+9',
    'Adelaide, Darwin' => '+9.5',
    'Eastern Australia, Guam, Vladivostok' => '+10',
    'Magadan, Solomon Islands, New Caledonia' => '+11',
    'Auckland, Wellington, Fiji, Kamchatka' => '+12',
);

foreach ($live_clock_timezones as $key => $value) {
    $smcFunc['db_insert']('ignore',
        '{db_prefix}live_clock_timezones', array('zone_name' => 'string', 'zone_diff' => 'string'),
        array($key, $value), ''
    );
}

add_integration_function('integrate_pre_include', '$sourcedir/LiveClockHooks.php');
add_integration_function('integrate_pre_include', '$sourcedir/LiveClock.php');
add_integration_function('integrate_admin_areas', 'LC_addAdminPanel');
add_integration_function('integrate_actions', 'LC_addAction', true);


if (SMF == 'SSI')
	echo 'Database adaptation successful!';

?>