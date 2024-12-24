<?php
/**
 * @version     $Id$
 * @copyright   Copyright (C) 2005 - 2009 Joomla! Vargas. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Guillermo Vargas (guille@vargas.co.cr)
 */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Execute the requested task
$mvc = Factory::getApplication()
    ->bootComponent("com_joxmap")
    ->getMVCFactory();

$controller = $mvc->createController('SiteMap');
$controller->execute(Factory::getApplication()->getInput()->get('task'));
$controller->redirect();
