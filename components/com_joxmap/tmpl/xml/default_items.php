<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_joxmap
 *
 * @copyright   Copyright (C) 2024 JL Tryoen. All rights reserved.
     (com_xmap) Copyright (C) 2007 - 2009 Joomla! Vargas. All rights reserved.
 * @author      JL Tryoen /  Guillermo Vargas (guille@vargas.co.cr)
 * @license     GNU General Public License version 3; see LICENSE
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Create shortcut to parameters.
$params = $this->state->get('params');

// Use the class defined in default_class.php to print the sitemap
$this->displayer->printSitemap();