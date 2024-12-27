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

namespace JLTRY\Component\JoXmap\Administrator\Controller;
// No direct access
defined('_JEXEC') or die;


use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Factory;

/**
 * @package     JoXmap
 * @subpackage  com_joxmap
 * @since       2.0
 */
class SitemapController extends FormController
{
    /**
     * Method override to check if the user can edit an existing record.
     *
     * @param    array    An array of input data.
     * @param    string   The name of the key for the primary key.
     *
     * @return   boolean
     */
    protected function _allowEdit($data = array(), $key = 'id')
    {
        // Initialise variables.
        $recordId = (int) isset($data[$key]) ? $data[$key] : 0;

        // Assets are being tracked, so no need to look into the category.
        return Factory::getUser()->authorise('core.edit', 'com_joxmap.sitemap.'.$recordId);
    }
}