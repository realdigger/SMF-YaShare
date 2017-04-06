<?php
/**
 * @package SMF Ya.Share Mod
 * @file add_settings.php
 * @author digger <digger@mysmf.ru> <http://mysmf.ru>
 * @copyright Copyright (c) 2012-2016, digger
 * @license The MIT License (MIT) https://opensource.org/licenses/MIT
 * @version 1.0
 *
 * To run this install manually please make sure you place this
 * in the same place and SSI.php and index.php
 */

global $context, $user_info;

if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF')) {
    require_once(dirname(__FILE__) . '/SSI.php');
} elseif (!defined('SMF')) {
    die('<b>Error:</b> Cannot install - please verify that you put this file in the same place as SMF\'s index.php and SSI.php files.');
}

if ((SMF == 'SSI') && !$user_info['is_admin']) {
    die('Admin privileges required.');
}

// List settings here in the format: setting_key => default_value.  Escape any "s. (" => \")
$mod_settings = array(
    'redirector_enabled' => 0,
    'redirector_guest_only' => 0,
    'redirector_check_referer' => 0,
    'redirector_mode' => 'immediate',
    'redirector_delay' => 5,
    'redirector_whitelist' => 'localhost',
    'redirector_hide_guest_links' => 0,
);

// Update mod settings if applicable
foreach ($mod_settings as $new_setting => $new_value) {
    if (!isset($modSettings[$new_setting])) {
        updateSettings(array($new_setting => $new_value));
    }
}

if (SMF == 'SSI') {
    echo 'Database changes are complete! <a href="/">Return to the main page</a>.';
}