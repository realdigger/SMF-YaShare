<?php
/**
 * @package SMF Ya.Share Mod
 * @file Mod-YaShare.php
 * @author digger <digger@mysmf.ru> <http://mysmf.ru>
 * @copyright Copyright (c) 2012-2016, digger
 * @license The MIT License (MIT) https://opensource.org/licenses/MIT
 * @version 1.0
 */

global $sourcedir;
require_once($sourcedir . '/Class-YaShare.php');

/**
 * Load all needed hooks
 */
function loadYaShareHooks()
{
    add_integration_function('integrate_load_theme', 'YaShare::loadAssets', false);
    //add_integration_function('integrate_menu_buttons', 'setYaShareMetaOg', false);
    add_integration_function('integrate_admin_areas', 'addYaShareAdminArea', false);
    add_integration_function('integrate_modify_modifications', 'addYaShareAdminAction', false);
    add_integration_function('integrate_menu_buttons', 'addYaShareCopyright', false);
    add_integration_function('integrate_display_buttons', 'addYaShareTopicBlock', false);

    // Custom hooks
    add_integration_function('integrate_yashare_topic', 'addYaShareTopicBlock', false);
    add_integration_function('integrate_yashare_message', 'addYaShareTopicBlock', false);
}


function addYaShareTopicBlock($position = 'top', $data = array())
{
    global $context, $scripturl, $modSettings;

    if (is_array($position)) {
        $position = 'top';
    }

    if ($position == 'top' && empty($modSettings['yashare_topic_top'])) {
        return false;
    } else {
        if ($position == 'bottom' && empty($modSettings['yashare_topic_bottom'])) {
            return false;
        } else {
            if ($position == 'message' && empty($modSettings['yashare_topic_message'])) {
                return false;
            }
        }
    }
//var_dump($position);
//$data['href']
//$data['body']
//$data['attachment']

    if ($position == 'top' || $position == 'bottom') {
        $yashare = new YaShare($scripturl . '?topic=' . $context['current_topic'] . '.0', $context['subject']);
    } elseif ($position == 'message') {
        $yashare = new YaShare($data['href'], $data['subject'], $data['body']);
    }
    echo $yashare->constructBlock();
    unset ($yashare);
}


/**
 * Add mod admin area
 * @param $admin_areas
 */
function addYaShareAdminArea(&$admin_areas)
{
    global $txt;
    loadLanguage('YaShare/');

    $admin_areas['config']['areas']['modsettings']['subsections']['yashare'] = array($txt['yashare']);
}


/**
 * Add mod admin action
 * @param $subActions
 */
function addYaShareAdminAction(&$subActions)
{
    $subActions['yashare'] = 'addYaShareAdminSettings';
}


/**
 * Add mod settings area
 * @param bool $return_config
 * @return array
 */
function addYaShareAdminSettings($return_config = false)
{
    global $txt, $scripturl, $context;
    loadLanguage('YaShare/');

    $context['page_title'] = $context['settings_title'] = $txt['yashare'];
    $context['settings_message'] = $txt['yashare_settings_message'];
    $context['post_url'] = $scripturl . '?action=admin;area=modsettings;save;sa=yashare';

    $config_vars = array(
        array('check', 'yashare_enabled'),
        array('check', 'yashare_topic_top'),
        array('check', 'yashare_topic_bottom'),
        array('check', 'yashare_topic_message', 'subtext' => $txt['yashare_topic_message_sub']),
        '',
        array('int', 'yashare_icons_count', 'subtext' => $txt['yashare_icons_count_sub']),
        array('check', 'yashare_icons_small'),
        array('check', 'yashare_counter', 'subtext' => $txt['yashare_counter_sub']),
        array(
            'select',
            'yashare_icons_list',
            array(
                'blogger' => $txt['yashare_blogger'],
                'collections' => $txt['yashare_collections'],
                'delicious' => $txt['yashare_delicious'],
                'digg' => $txt['yashare_digg'],
                'evernote' => $txt['yashare_evernote'],
                'facebook' => $txt['yashare_facebook'],
                'gplus' => $txt['yashare_gplus'],
                'linkedin' => $txt['yashare_linkedin'],
                'lj' => $txt['yashare_lj'],
                'moimir' => $txt['yashare_moimir'],
                'odnoklassniki' => $txt['yashare_odnoklassniki'],
                'pinterest' => $txt['yashare_pinterest'],
                'pocket' => $txt['yashare_pocket'],
                'qzone' => $txt['yashare_qzone'],
                'reddit' => $txt['yashare_reddit'],
                'renren' => $txt['yashare_renren'],
                'sinaWeibo' => $txt['yashare_sinaWeibo'],
                'skype' => $txt['yashare_skype'],
                'surfingbird' => $txt['yashare_surfingbird'],
                'telegram' => $txt['yashare_telegram'],
                'tencentWeibo' => $txt['yashare_tencentWeibo'],
                'tumblr' => $txt['yashare_tumblr'],
                'twitter' => $txt['yashare_twitter'],
                'viber' => $txt['yashare_viber'],
                'vkontakte' => $txt['yashare_vkontakte'],
                'whatsapp' => $txt['yashare_whatsapp'],
            ),
            'multiple' => true,
            'subtext' => $txt['yashare_icons_list_sub'],
        ),
        '',
        array('text', 'yashare_image', 'subtext' => $txt['yashare_image_sub']),
        array('check', 'yashare_msg_image', 'subtext' => $txt['yashare_msg_image_sub']),
        array('check', 'yashare_og_enabled', 'subtext' => $txt['yashare_og_enabled_sub']),
    );

    if ($return_config) {
        return $config_vars;
    }

    if (isset($_GET['save'])) {
        checkSession();
        saveDBSettings($config_vars);
        redirectexit('action=admin;area=modsettings;sa=yashare');
    }

    prepareDBSettingContext($config_vars);
}


/**
 * Add mod copyright to the forum credits page
 */
function addYaShareCopyright()
{
    global $context;

    if ($context['current_action'] == 'credits') {
        $context['copyrights']['mods'][] = '<a href="http://mysmf.ru/mods/yashare" title="Ya.Share" target="_blank">Ya.Share</a> &copy; 2012-2016, digger | Powered by <a href="https://tech.yandex.ru/share" title="Powered by Ya.Share API" target="_blank">Ya.Share API</a>';
    }
}


/**
 * Set meta tags for OpenGraph markup
 */
function setYaShareMetaOg()
{
    global $mbname, $context, $attachments, $scripturl, $modSettings, $settings, $txt;

    // Set og:site_name
    $og_site_name = $context['forum_name'];

    // Set og:title
    $og_title = $context['subject'];


    // Set og:type
    if (!empty($context['current_topic'])) {
        $og_type = 'article';
    } else {
        $og_type = 'website';
    }

    // Set og:description
    if (!empty($context['is_poll'])) {
        $og_description = $txt['poll'] . ': ' . $context['poll']['question'];
    } else {
        if (!empty($context['first_message'])) {
            $og_description = YaShare::getMsgDescriptionAndImage($context['first_message']);
        } else {
            $og_description = $og_title;
        }
    }

    // Set og:image
    // TODO: first_message -> topic_first_message ??? First on page or first of topic ? is_image
    if (!empty($modSettings['microdata4smf_logo_attachment']) && !empty($context['first_message']) && !empty($attachments[$context['first_message']][0]['width']) && !empty($attachments[$context['first_message']][0]['approved']) && $attachments[$context['first_message']][0]['width'] >= 200 && $attachments[$context['first_message']][0]['height'] >= 200) {
        $og_image = $scripturl . '?action=dlattach;topic=' . $context['current_topic'] . '.0;attach=' . $attachments[$context['first_message']][0]['id_attach'] . ';image';
    } else {
        if (!empty($modSettings['microdata4smf_logo_img']) && !empty($og_body['image'])) {
            $og_image = $og_body['image'];
        } else {
            if (!empty($modSettings['microdata4smf_logo'])) {
                $og_image = trim($modSettings['microdata4smf_logo']);
            } else {
                if (!empty($context['header_logo_url_html_safe'])) {
                    $og_image = $context['header_logo_url_html_safe'];
                } else {
                    $og_image = $settings['images_url'] . '/smflogo.png"';
                }
            }
        }
    }

    // Set og:url if we have canonical
    if (!empty($context['canonical_url'])) {
        $context['html_headers'] .= '
  <meta property="og:url" content="' . $context['canonical_url'] . '" />';
    }

    $context['html_headers'] .= '
  <meta property="og:site_name" content="' . $og_site_name . '" />
  <meta property="og:title" content="' . $og_title . '" />
  <meta property="og:type" content="' . $og_type . '" />
  <meta property="og:image" content="' . $og_image . '" />
  <meta property="og:description" content="' . $og_description . '" />
  ';
}
