<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
/**
 * Plugin administration pages are defined here.
 *
 * @package     local_virus_scanner
 * @category    admin
 * @copyright   2022 Santosh N. <santosh.nag2217@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {

    $ADMIN->add('localplugins', new admin_category('local_virus_scanner_settings',
        new lang_string('pluginname', 'local_virus_scanner')));
    $settingspage = new admin_settingpage('managelocalvirusscanner', new lang_string('manage', 'local_virus_scanner'));

    if ($ADMIN->fulltree) {

        // Path to clamav.
        $settingspage->add(new admin_setting_configexecutable('local_virus_scanner/clamav',
                get_string('clamav', 'local_virus_scanner'),
                get_string('configclamav', 'local_virus_scanner'),
                '', PARAM_TEXT));

        // Directory to save scanner report file.
        $settingspage->add(new admin_setting_configtext('local_virus_scanner/directory',
            get_string('directory', 'local_virus_scanner'),
            get_string('configdirectory', 'local_virus_scanner'),
            '', PARAM_TEXT));

        // Enable/Disable Mail Sending.
        $settingspage->add(new admin_setting_configcheckbox('local_virus_scanner/sendmail',
            get_string('sendmail', 'local_virus_scanner'),
            get_string('configsendmail', 'local_virus_scanner'), 1));

        // Send mail to this mail id.
        $settingspage->add(new admin_setting_configtext('local_virus_scanner/mailid',
            get_string('mailid', 'local_virus_scanner'),
            get_string('configmailid', 'local_virus_scanner'), '', PARAM_EMAIL));
    }

    $ADMIN->add('localplugins', $settingspage);
}
