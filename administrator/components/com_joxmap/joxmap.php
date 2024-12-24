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

use Joomla\CMS\Factory;

defined('_JEXEC') or die('Restricted access');

// Execute the requested task
$mvc = Factory::getApplication()
    ->bootComponent("com_joxmap")
    ->getMVCFactory();

$controller = $mvc->createController('SiteMaps');
$controller->execute(Factory::getApplication()->getInput()->get('task'));
$controller->redirect();