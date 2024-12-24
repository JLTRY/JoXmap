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

namespace JLTRY\Component\JoXmap\Administrator\Controller;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;
use JLTRY\Component\JoXmap\Administrator\Helper\Xmap;


/**
 * Component Controller
 *
 * @package     JoXmap
 * @subpackage  com_joxmap
 */
class JoXmapController extends BaseController
{

    function __construct()
    {
        parent::__construct();

        $this->registerTask('navigator-links', 'navigatorLinks');
    }


    function navigator()
    {
        $db = Factory::getDBO();
        $document = Factory::getDocument();
        $app = Factory::getApplication('administrator');

        $id = Xmap::getInt('sitemap', 0);
        $link = urldecode(Xmap::getVar('link', ''));
        $name = Xmap::getCmd('e_name', '');
        if (!$id) {
            $id = $this->getDefaultSitemapId();
        }

        if (!$id) {
			Factory::getApplication()->enqueueMessage(500, Text::_('Xmap_Not_Sitemap_Selected'), 'warning');
			return false;
        }

        $app->setUserState('com_joxmap.edit.sitemap.id', $id);

        $view = $this->getView('sitemap', $document->getType());
        $model = $this->getModel('Sitemap');
        $view->setLayout('navigator');
        $view->setModel($model, true);

        // Push document object into the view.
        $view->assignRef('document', $document);

        $view->navigator();
    }

    function navigatorLinks()
    {

        $db = Factory::getDBO();
        $document = Factory::getDocument();
        $app = Factory::getApplication('administrator');

        $id = Xmap::getInt('sitemap', 0);
        $link = urldecode(Xmap::getVar('link', ''));
        $name = Xmap::getCmd('e_name', '');
        if (!$id) {
            $id = $this->getDefaultSitemapId();
        }

        if (!$id) {
            Factory::getApplication()->enqueueMessage(500, Text::_('Xmap_Not_Sitemap_Selected'), 'warning');
            return false;
        }

        $app->setUserState('com_joxmap.edit.sitemap.id', $id);

        $view = $this->getView('sitemap', $document->getType());
        $model = $this->getModel('Sitemap');
        $view->setLayout('navigator');
        $view->setModel($model, true);

        // Push document object into the view.
        $view->assignRef('document', $document);

        $view->navigatorLinks();
    }

    private function getDefaultSitemapId()
    {
        $db = Factory::getDBO();
        $query  = $db->getQuery(true);
        $query->select('id');
        $query->from($db->quoteName('#__joxmap_sitemap'));
        $query->where('is_default=1');
        $db->setQuery($query);
        return $db->loadResult();
    }

}