[url=http://custom.simplemachines.org/mods/index.php?mod=3641]Link to Mod[/url]

[b][i]Live clock in header[/i][/b]

This modification helps to show a dynamic clock in the header of your forum, instead of static clock which comes with SMF.

All mod settings are available under
Admin center -> Configuration -> Live Clock Admin

Mod comes with an admin panel which provides following features:
?action=admin;area=liveclock;

- General Features
1. Enable/Disable the mod
2. Choose whether you want to show the timezone drop down or not
3. Use 24/12 hr format clock
4. Show date with time

- Timezone features
1. You can edit the timezone configurations from the following link
action=admin;area=liveclock;sa=displaytimezones

Note - If any of the 2 coloumns is left blank, the mod neglects the value of other column, i.e if you have entered value for 'Timezone Name' only but 'Timezone Difference' hasn't been filled. In that case mod neglects both values.


If you made any error while editing default timezone you can reset all the timezones to default values(which comes with the mod), by clicking 'Reset all timezones' from
Admin center -> Configuration -> Live Clock Admin


[b]Change Log[/b]

[i]Version Next[/i]
- Loading scripts in specific order with specific jquery version
- overhauling of JS with revealing modular pattern
- XHTML fixes
- checks refined with usage of typeof, generic function to check null or undefined
- var scoping in JS
- params init only once
- code optimisation
- Obsolete comment removed
- Using anonymous function concept to invoke the clock class


[i]Version 1.2[/i]
- Mod not parsed correctly. Fixed.
- Duplicate entries while populating DB on mod installation. Fixed.


[i]Version 1.1.1[/i]
- Mod was unable to carry "Western Europe Time, London, Lisbon, Casablanca" as timezone. Fixed.
- Mod timezone bar unable to load due to jQuery conflict. Fixed.
- Github repo moved back to https://github.com/Joker-SMF/SMF-LiveClockInHeader


[i]Version 1.1[/i]
- Option added to show timezone dropdown
- Show date with time
- Bug fixes, JS optimisations and improvements
- Github Repo moved to https://github.com/Joker-SMF/SMF-Mods


[url=https://github.com/Joker-SMF/SMF-LiveClockInHeader]GitHub Link[/url]

License
 * This SMF Modification is subject to the Mozilla Public License Version
 * 1.1 (the "License"); you may not use this SMF modification except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/