<?php
defined('MOODLE_INTERNAL') || die();
/**
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_auth_iomademailadmin_upgrade($oldversion) {
    global $CFG, $DB;

    if ($oldversion < 2019070800) {
        upgrade_fix_config_auth_plugin_names('iomademailadmin');
        upgrade_fix_config_auth_plugin_defaults('iomademailadmin');
        upgrade_plugin_savepoint(true, 2019070800, 'auth', 'iomademailadmin');
    }

    return true;
}
