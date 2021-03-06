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
 * Message class for auth-iomademailadmin plugin.
 *
 * @package    moodle multiauth
 * @copyright  2017 Felipe Carasso
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace auth\iomademailadmin;
defined('MOODLE_INTERNAL') || die();

//require('../../config.php');
require_once($CFG->libdir.'/authlib.php');

class message {
    public static function get_user_language($user) {
        // (FECA) Crazy hack to reuse existing language logic (who knows, it could have been customized or something).
        global $USER, $COURSE, $SESSION;
        $lang_hack = new \stdClass();
        $lang_hack->forcelang = $user->lang;
        $lang_hack->lang = $user->lang;
        $hack_backup = ['USER' => false, 'COURSE' => false, 'SESSION' => false];
        foreach ($hack_backup as $hack_backup_key => $hack_backup_value) {
            $hack_backup[$hack_backup_key] = $GLOBALS[$hack_backup_key];
            $GLOBALS[$hack_backup_key] = $lang_hack;
        }
        $use_lang = current_language();
        foreach ($hack_backup as $hack_backup_key => $hack_backup_value) {
            $GLOBALS[$hack_backup_key] = $hack_backup_value;
        }
        /* (FECA) End of crazy hack. Could just have repeated some ifs or just uses $CFG as suggested by ewallah, but no...
         * I had to do something crazy, hadn't I?
         */

        return $use_lang;
    }

    public static function send_confirmation_email_user($user) {
        global $CFG;
    
        $site = get_site();
        $supportuser = \core_user::get_support_user();
    
        $data = new \stdClass();
        $data->firstname = fullname($user);
        $data->sitename  = format_string($site->fullname);
        $data->admin     = generate_email_signoff();

        include_once($CFG->dirroot.'/local/iomad/lib/company.php');
        if (class_exists('company')) {
            $companyid = \company::get_company_byuserid($user->id);
            $company = new \company($companyid);
        }
        if (!empty($company)) {
            $data->company = $company->get_name();
        } else {
            $data->company = get_string('myorganization', 'auth_iomademailadmin');
        }
    
        $use_lang = message::get_user_language($user);
        $subject = get_string_manager()->get_string('auth_iomademailadminconfirmationsubject', 'auth_iomademailadmin', format_string($site->fullname), $use_lang);
    
        $username = urlencode($user->username);
        $username = str_replace('.', '%2E', $username); // Prevent problems with trailing dots.
        $data->link  = $CFG->wwwroot;
        $data->username = $username;
        $message     = get_string_manager()->get_string('auth_iomademailadminuserconfirmation', 'auth_iomademailadmin', $data, $use_lang);
        $messagehtml = text_to_html(get_string('auth_iomademailadminuserconfirmation', 'auth_iomademailadmin', $data), false, false, true);
    
        $user->mailformat = 1;  // Always send HTML version as well.
    
        // Directly email rather than using the messaging system to ensure its not routed to a popup or jabber.
    
        return email_to_user($user, $supportuser, $subject, $message, $messagehtml);
    }
}
