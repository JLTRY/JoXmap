<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_joxmap
 *
 * @copyright   Copyright (C) 2024 JL Tryoen. All rights reserved.
     (com_xmap) Copyright (C) 2007 - 2009 Joomla! Vargas. All rights reserved.
 * @author      JL Tryoen /  Guillermo Vargas (guille@vargas.co.cr)
 * @license     GNU General Public License version 3; see LICENSE
 */

namespace JLTRY\Component\JoXmap\Administrator\Helper;
// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Version;
use Joomla\CMS\HTML\Helpers\Sidebar;
use Joomla\CMS\Language\Text;

/**
 * Xmap component helper.
 *
 * @package     JoXmap
 * @subpackage  com_joxmap
 * @since       2.0
 */
class Xmap
{
    /**
     * Configure the Linkbar.
     *
     * @param    string  The name of the active view.
     */
    public static function addSubmenu($vName)
    {
        $version = new Version;

        if (Version_compare($version->getShortVersion(), '3.0.0', '<')) {
            JSubMenuHelper::addEntry(
                Text::_('Xmap_Submenu_Sitemaps'),
                'index.php?option=com_joxmap',
                $vName == 'sitemaps'
            );
            JSubMenuHelper::addEntry(
                Text::_('Xmap_Submenu_Extensions'),
                'index.php?option=com_plugins&view=plugins&filter_folder=joxmap',
                $vName == 'extensions');
        } else {
            SideBar::addEntry(
                Text::_('Xmap_Submenu_Sitemaps'),
                'index.php?option=com_joxmap',
                $vName == 'sitemaps'
            );
            SideBar::addEntry(
                Text::_('Xmap_Submenu_Extensions'),
                'index.php?option=com_plugins&view=plugins&filter_folder=joxmap',
                $vName == 'extensions');
        }
    }
	
	public static function getpost() {
		if (Version_compare(Version::MAJOR_VERSION, '4.0', 'ge')){
			return Factory::getApplication()->input->getArray(array());
		}
		else {
			return call_user_func_array('XmapHelper::get', ['post']);
		}
	}
	
	public static function get(...$params) {
		if (Version_compare(Version::MAJOR_VERSION, '4.0', 'ge')){
			if ($params[0] == 'post '){
				return Factory::getApplication()->input->getInputForRequestMethod('POST');
			} else {
				return call_user_func_array(array(Factory::getApplication()->input, 'get'), $params);
			}
		}
		else {
			return call_user_func_array('XmapHelper::get', $params);
		}
	}
	
	public static function getVar(...$params) {
		if (Version_compare(Version::MAJOR_VERSION, '4.0', 'ge')){
			return call_user_func_array(array(Factory::getApplication()->input, 'getVar'), $params);
		}
		else {
			return call_user_func_array('XmapHelper::getVar', $params);
		}
	}
	

	public static function setVar(...$params) {
		if (Version_compare(Version::MAJOR_VERSION, '4.0', 'ge')){
			call_user_func_array(array(Factory::getApplication()->input, 'setVar'), $params);
		}
		else {
			call_user_func_array('XmapHelper::setVar', $params);
		}
	}

	public static function getCmd(...$params) {
		if (Version_compare(Version::MAJOR_VERSION, '4.0', 'ge')){
			return call_user_func_array(array(Factory::getApplication()->input, 'getCmd'), $params);
		}
		else {
			return call_user_func_array('XmapHelper::getCmd', $params);
		}
	}

	public static function getInt(...$params) {
		if (Version_compare(Version::MAJOR_VERSION, '4.0', 'ge')){
			$recordId = call_user_func_array(array(Factory::getApplication()->input, 'getInt'), $params);
		}
		else {
			$recordId	= (int)call_user_func_array('XmapHelper::getInt', $params);
		}
	}
	
	
	public static function getBool(...$params) {
		if (Version_compare(Version::MAJOR_VERSION, '4.0', 'ge')){
			return call_user_func_array(array(Factory::getApplication()->input, 'getBool'), $params);
		}
		else {
			return (int)call_user_func_array('XmapHelper::getBool', $params);
		}
	}
	public static function getWord(...$params) {
		if (Version_compare(Version::MAJOR_VERSION, '4.0', 'ge')){
			return call_user_func_array(array(Factory::getApplication()->input, 'getWord'), $params);
		}
		else {
			return (int)call_user_func_array('XmapHelper::getWord', $params);
		}
	}
	
	public static function getURI() {
		if (Version_compare(Version::MAJOR_VERSION, '4.0', 'ge')){
			return JUri::getInstance();
		}
		else {
			return Factory::getURI();
		}
	}
	
	public static function getShortVersion() {
		return implode(".", array_slice(explode(".", Version), 0,3));
	}
}
