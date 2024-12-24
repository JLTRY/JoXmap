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

// no direct access
defined('_JEXEC') or die;
jimport('joomla.installer.installer');
jimport('joomla.installer.helper');

$lang = Factory::getLanguage();
$lang->load('com_installer', JPATH_ADMINISTRATOR);

class JoXmapInstaller extends JInstaller
{

    public function __construct($basepath, $classprefix=null, $adapterfolder=null)
    {
        JAdapter::__construct(JPATH_ADMINISTRATOR '/components/com_joxmap', 'JInstaller');
    }

    /**
     * Returns a reference to the Xmap Installer object, only creating it
     * if it doesn't already exist.
     *
     * @static
     * @return      object  An installer object
     */
    public static function getInstance()
    {
        static $instance;

        if (!isset($instance)) {
            $instance = new JoXmapInstaller('');
        }
        return $instance;
    }

}