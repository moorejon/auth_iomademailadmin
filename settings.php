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
 * Admin settings and defaults. Heavily based on auth/email/settings.php.
 *
 * @package    auth_iomademailadmin
 * @copyright  2019 Felipe Carasso
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    // Introductory explanation.
    $settings->add(new admin_setting_heading('auth_iomademailadmin/pluginname', '',
        new lang_string('auth_iomademailadmindescription', 'auth_iomademailadmin')));

    $options = array(
        new lang_string('no'),
        new lang_string('yes'),
    );

    $settings->add(new admin_setting_configselect('auth_iomademailadmin/recaptcha',
        new lang_string('auth_iomademailadminrecaptcha_key', 'auth_iomademailadmin'),
        new lang_string('auth_iomademailadminrecaptcha', 'auth_iomademailadmin'), 0, $options));
    $options = array('-1' => get_string("auth_iomademailadminnotif_strategy_first", "auth_iomademailadmin"),
        '-2' => get_string("auth_iomademailadminnotif_strategy_all", "auth_iomademailadmin"),
        '-3' => get_string("auth_iomademailadminnotif_strategy_allupdate", "auth_iomademailadmin"),
        '-4' => get_string("auth_iomademailadminnotif_strategy_company", "auth_iomademailadmin")
        );
    $admins = array_merge(get_admins(), get_users_by_capability(context_system::instance(), 'moodle/user:update'));
    foreach ($admins as $admin) {
        $options[$admin->id] = $admin->username;
    }

    $settings->add(new admin_setting_configselect('auth_iomademailadmin/notif_strategy',
        new lang_string('auth_iomademailadminnotif_strategy_key', 'auth_iomademailadmin'),
        new lang_string('auth_iomademailadminnotif_strategy', 'auth_iomademailadmin'), -1, $options));

    // Display locking / mapping of profile fields.
    $authplugin = get_auth_plugin('iomademailadmin');
    display_auth_lock_options($settings, $authplugin->authtype, $authplugin->userfields,
            get_string('auth_fieldlocks_help', 'auth'), false, false);
}
