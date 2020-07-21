<?php
/**
 * @package SMF Ya.Share Mod
 * @file add_settings.php
 * @author digger <digger@mysmf.net> <https://mysmf.net>
 * @copyright Copyright (c) 2012-2020, digger
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
$mod_settings = [
    'yashare_enabled'       => 0,
    'yashare_topic_top'     => 0,
    'yashare_topic_bottom'  => 0,
    'yashare_topic_message' => 0,
    'yashare_icons_count'   => 0,
    'yashare_icons_small'   => 0,
    'yashare_counter'       => 0,
    'yashare_msg_image'     => 0,
    'yashare_og_enabled'    => 0,
    'yashare_icons_list'    => '',
    'yashare_image'         => '',
];

// Update mod settings if applicable
foreach ($mod_settings as $new_setting => $new_value) {
    if (!isset($modSettings[$new_setting])) {
        updateSettings([$new_setting => $new_value]);
    }
}

if (SMF == 'SSI') {
    echo 'Database changes are complete! <a href="/">Return to the main page</a>.';
}