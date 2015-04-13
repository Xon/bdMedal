<?php

class bdMedal_Listener
{
    public static function load_class($class, array &$extend)
    {
        static $classes = array(
            'XenForo_ControllerAdmin_User',

            'XenForo_ControllerPublic_Account',
            'XenForo_ControllerPublic_Member',
            'XenForo_ControllerPublic_Help',
            'XenForo_ControllerPublic_Thread',

            'XenForo_Model_Import',
        );

        if (in_array($class, $classes)) {
            $extend[] = str_replace('XenForo_', 'bdMedal_Extend_', $class);
        }
    }

    public static function load_class_importer($class, array &$extend)
    {
        if (strpos(strtolower($class), 'vbulletin') != false AND !defined('bdMedal_Extend_Importer_vBulletin_LOADED')) {
            $extend[] = 'bdMedal_Extend_Importer_vBulletin';
        }
    }

    public static function init_dependencies(XenForo_Dependencies_Abstract $dependencies, array $data)
    {
        XenForo_Template_Helper_Core::$helperCallbacks[strtolower('bdMedal_image')] = array(
            'bdMedal_Model_Medal',
            'helperMedalImage'
        );
        XenForo_Template_Helper_Core::$helperCallbacks[strtolower('bdMedal_imageSize')] = array(
            'bdMedal_Model_Medal',
            'helperMedalImageSize'
        );
        XenForo_Template_Helper_Core::$helperCallbacks[strtolower('bdMedal_getOption')] = array(
            'bdMedal_Option',
            'get'
        );

        // sondh@2012-10-18
        // these two helper is kept for legacy reason only
        XenForo_Template_Helper_Core::$helperCallbacks[strtolower('medalImage')] = XenForo_Template_Helper_Core::$helperCallbacks['bdmedal_image'];
        XenForo_Template_Helper_Core::$helperCallbacks[strtolower('medalImageSize')] = XenForo_Template_Helper_Core::$helperCallbacks['bdmedal_imagesize'];

        // sondh@2012-11-04
        // add rebuilder
        if ($dependencies instanceof XenForo_Dependencies_Admin) {
            XenForo_CacheRebuilder_Abstract::$builders['bdMedal_User'] = 'bdMedal_CacheRebuilder_User';
        }
    }

    public static function navigation_tabs(array &$extraTabs, $selectedTabId)
    {
        $listPage = bdMedal_Option::get('listPage');

        if ($listPage == 'help') {
            // no need to add navtab
        } else {
            $position = false;
            $tabId = bdMedal_Option::get('navtabId');

            switch ($listPage) {
                case 'navtab_home':
                    $position = 'home';
                    break;
                case 'navtab_middle':
                    $position = 'middle';
                    break;
                case 'navtab_end':
                    $position = 'end';
                    break;
            }

            if ($position !== false) {
                $extraTabs[$tabId] = array(
                    'title' => new XenForo_Phrase('bdmedal_medals'),
                    'href' => XenForo_Link::buildPublicLink('help/medals'),
                    'position' => $position,
                    'selected' => ($selectedTabId == $tabId),
                );
            }
        }
    }

    public static function file_health_check(XenForo_ControllerAdmin_Abstract $controller, array &$hashes)
    {
        $hashes += bdMedal_FileSums::getHashes();
    }

}
