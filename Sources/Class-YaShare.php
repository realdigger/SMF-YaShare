<?php

/**
 * @package SMF Ya.Share Mod
 * @file Class-YaShare.php
 * @author digger <digger@mysmf.ru> <http://mysmf.ru>
 * @copyright Copyright (c) 2012-2016, digger
 * @license The MIT License (MIT) https://opensource.org/licenses/MIT
 * @version 1.0
 */
class YaShare
{
    public $msgID;
    public $url, $title, $description, $image, $position;

    /**
     * YaShare constructor.
     * @param $url
     * @param $title
     * @param $description
     */
    public function __construct($url = '', $title = '', $description = '')
    {
        global $context;

        if ($url) {
            $this->url = $url;
        } else {
            $this->url = $context['canonical_url'];
        }

        if ($title) {
            $this->title = $title;
        } else {
            $this->title = $context['page_title'];
        }

        if ($description) {
            $this->description = $description;
        } else {
            $this->description = $this->title;
        }
    }


    /**
     * Get message description and image
     * @param $id_msg
     * @return array description and image
     */
    function getMsgDescriptionAndImage($id_msg = 0)
    {
        global $smcFunc, $modSettings;

        $request = $smcFunc['db_query']('', '
			SELECT ' . (!empty($modSettings['yashare_msg_image']) ? 'body ' : 'SUBSTRING(body, 1, 250) ') . '
			FROM {db_prefix}messages
			WHERE id_msg = {int:id_msg}
			LIMIT 1',
            array(
                'id_msg' => (int)$id_msg
            )
        );

        list ($description) = $smcFunc['db_fetch_row']($request);
        $smcFunc['db_free_result']($request);

        preg_match('/\[img.*](.+)\[\/img]/i', $description, $image);
        if (!empty($image[1])) {
            $image = trim($image[1]);
        }

        $description = strip_tags(str_replace(array('<br>', '<br/>', '<br />', '<hr>', '<hr/>', '<hr />'), '. ',
            parse_bbc($description, false)));

        if ($smcFunc['strlen']($description) > 200) {
            $description = $smcFunc['substr']($description, 0, 197);
            $position = $smcFunc['strpos']($description, ' ', $smcFunc['strlen']($description) - 15);
            $description = $smcFunc['substr']($description, 0, $position);
        }

        return array(
            'description' => trim($description) . '...',
            'image' => $image,
        );


    }

    /**
     * Load assets
     * @return bool
     */
    public static function loadAssets()
    {
        global $modSettings, $context;
        // TODO: check guest permission

        if (!empty($context['current_topic']) && !empty($modSettings['yashare_enabled']) && !WIRELESS) {
            $context['insert_after_template'] .= '
                <script type="text/javascript" src="https://yastatic.net/es5-shims/0.0.2/es5-shims.min.js"></script>
                <script type="text/javascript" src="https://yastatic.net/share2/share.js" async="async"></script>
                ';
        } else {
            return false;
        }
        return true;
    }

    /**
     * Constrict div with share buttons
     * @return string|void
     */
    function constructBlock()
    {
        global $modSettings, $txt, $context;

        // data-url
        $params[] = 'data-url="' . $this->url . '"';

        // data-services
        if (!empty($modSettings['yashare_icons_list'])) {
            $params[] = 'data-services="' . implode(',', unserialize($modSettings['yashare_icons_list'])) . '"';
        } else {
            $params[] = 'data-services="vkontakte,odnoklassniki,gplus,facebook,twitter"';
        }

        // data-title
        $params[] = 'data-title="' . $this->title . '"';
        $params[] = 'data-title:twitter="' . $this->title . '"'; //cut 140


        // data-description
        $params[] = 'data-description="' . $this->description . '"';


        // data-image
        if (!empty($modSettings['yashare_image'])) {
            // TODO: check it's really url
            $params[] = 'data-image="' . $modSettings['yashare_image'] . '"';
        }

        // data-counter
        if (!empty($modSettings['yashare_counter'])) {
            $params[] = 'data-counter=""';
        }

        // data-size
        if (!empty($modSettings['yashare_icons_small'])) {
            $params[] = 'data-size="s"';
        }

        // data-limit
        if (!empty($modSettings['yashare_icons_count'])) {
            $params[] = 'data-limit="' . $modSettings['yashare_icons_count'] . '"';
        }

        // data-lang
        if (!empty($txt['lang_dictionary'])) {
            $params[] = 'data-lang="' . $txt['lang_dictionary'] . '"';
        } else {
            $params[] = 'data-lang="en"';
        }


        return '
    <div class="ya-share2 nextlinks"' . implode(' ', $params) . '></div>
    ';
    }

}
