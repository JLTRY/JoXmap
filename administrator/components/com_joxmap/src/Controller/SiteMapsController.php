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


use JLTRY\Component\JoXmap\Administrator\Helper\JoXmapHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Language\Text;


/**
 * @package     JoXmap
 * @subpackage  com_xmap
 * @since       2.0
 */
class SitemapsController extends AdminController
{

    protected $text_prefix = 'COM_JOXMAP_SITEMAPS';

    /**
     * Constructor
     */
    public function __construct($config = array())
    {
        parent::__construct($config);

        $this->registerTask('unpublish',    'publish');
        $this->registerTask('trash',        'publish');
        $this->registerTask('unfeatured',   'featured');
    }


    /**
     * Method to toggle the default sitemap.
     *
     * @return      void
     * @since       2.0
     */
    function setDefault()
    {
        // Check for request forgeries
        if (version_compare(Version, '4.0', '<')){
            Factory::getApplication()->input->checkToken() or die('Invalid Token');
        }

        // Get items to publish from the request.
        $cid = JoXmapHelper::getVar('cid', 0, '', 'array');
        $id  = @$cid[0];

        if (!$id) {
            Factory::getApplication()->enqueueMessage(500, Text::_('Select an item to set as default'), 'warning');
        }
        else
        {
            // Get the model.
            $model = $this->getModel();

            // Publish the items.
            if (!$model->setDefault($id)) {
                Factory::getApplication()->enqueueMessage(500, $model->getError(), 'warning');
            }
        }

        $this->setRedirect('index.php?option=com_joxmap&view=sitemaps');
    }

    /**
     * Proxy for getModel.
     *
     * @param    string    $name    The name of the model.
     * @param    string    $prefix    The prefix for the PHP class name.
     *
     * @return    JModel
     * @since    2.0
     */
    public function getModel($name = 'Sitemap', $prefix = 'JoXmapModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);

        return $model;
    }
}