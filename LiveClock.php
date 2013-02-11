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

if (!defined('SMF'))
	die('Hacking attempt...');

loadLanguage('LiveClock');

function LC_addAction(&$actionArray) {
	global $context;

	$actionArray['liveclock'] = array('LiveClock.php', 'LC_mainIndex');
}

function LC_mainIndex() {
	global $context;

	$default_action_func = 'showClock';
	$subActions = array(
		'showclock' => 'LC_showClock',
	);

	if (isset($_REQUEST['sa']) && isset($subActions[$_REQUEST['sa']]) && function_exists($subActions[$_REQUEST['sa']]))
		return $subActions[$key]();
	$default_action_func();
}

function LC_showClock() {
	global $context, $settings, $user_info;

	echo '
		<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/LiveClock.js"></script>
		
		<script type="text/javascript">
			//lets make params
			var params = {
				timezone : "' . $user_info['time_offset'] . '"
			}
			refrClock(params)
		</script>';
}

?>