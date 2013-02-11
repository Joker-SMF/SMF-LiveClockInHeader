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

function template_lc_admin_info() {
	global $context, $txt, $scripturl;

	echo '
	<div class="cat_bar">
		<h3 class="catbg">
			<span class="ie6_header floatleft">', $txt['lc_admin_panel'] ,'</span>
		</h3>
	</div>
	<p class="windowbg description">', isset($context['live_clock']['tab_desc']) ? $context['live_clock']['tab_desc'] : $txt['lc_general_desc'] ,'</p>';
	
	// The admin tabs.
		echo '
	<div id="adm_submenus">
		<ul class="dropmenu">';
	
		// Print out all the items in this tab.
		$menu_buttons = $context[$context['admin_menu_name']]['tab_data'];
		foreach ($menu_buttons['tabs'] as $sa => $tab)
		{
			echo '
			<li>
				<a class="', ($menu_buttons['active_button'] == $tab['url']) ? 'active ' : '', 'firstlevel" href="', $scripturl, '?action=admin;area=liveclock;sa=', $tab['url'],'"><span class="firstlevel">', $tab['label'], '</span></a>
			</li>';
		}
	
		// the end of tabs
		echo '
		</ul>
	</div><br class="clear" />';

	echo '
	<div class="cat_bar">
		<h3 class="catbg">
			', $context['live_clock']['tab_name'] ,'
		</h3>
	</div>';
}

function template_lc_admin_basic_setting_panel()
{
	global $context, $txt, $scripturl;

	template_lc_admin_info();

	echo '
	<div id="admincenter">
		<form action="'. $scripturl .'?action=admin;area=liveclock;sa=savebasicsettings" method="post" accept-charset="UTF-8">
			<div class="windowbg2">
				<span class="topslice"><span></span></span>
					<div class="content">';
	
					foreach ($context['config_vars'] as $config_var) {
						echo '
						<dl class="settings">
							<dt>
								<span>'. $txt[$config_var['name']] .'</span>';
								if (isset($config_var['subtext']) && !empty($config_var['subtext'])) {
									echo '
									<br /><span class="smalltext">', $config_var['subtext'] ,'</span>';
								}
							echo '
							</dt>
							<dd>
								<input type="checkbox" name="', $config_var['name'], '" id="', $config_var['name'], '"', ($config_var['value'] ? ' checked="checked"' : ''), ' value="1" class="input_check" />
							</dd>
						</dl>';
					}
	
					echo '
					<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
					<input type="submit" name="submit" value="', $txt['lc_submit'], '" tabindex="', $context['tabindex']++, '" class="button_submit" />';
		
					echo '
					</div>
				<span class="botslice"><span></span></span>
			</div>
	
		</form>
	</div>
	<br class="clear">';
}

?>