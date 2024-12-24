<?php

/**
 * @version        $Id$
 * @copyright      Copyright (C) 2005 - 2009 Joomla! Vargas. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 * @author         Guillermo Vargas (guille@vargas.co.cr)
 */

namespace JLTRY\Component\JoXmap\Site\Controller;

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


use JLTRY\Component\JoXmap\Site\Helper\XmapHelper;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;
/**
 * Xmap Component Controller
 *
 * @package        Xmap
 * @subpackage     com_xmap
 * @since          2.0
 */
class DisplayController extends BaseController
{

    /**
     * Method to display a view.
     *
     * @param   boolean         If true, the view output will be cached
     * @param   array           An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     *
     * @return  JController     This object to support chaining.
     * @since   1.5
     */
    public function display($cachable = false, $urlparams = false)
    {
        $cachable = true;

        $id         = XmapHelper::getInt('id');
        $viewName   = XmapHelper::getCmd('view');
        $viewLayout = XmapHelper::getCmd('layout', 'default');

        $user = Factory::getUser();

        if ($user->get('id') || !in_array($viewName, array('html', 'xml')) || $viewLayout == 'xsl') {
            $cachable = false;
        }

        if ($viewName) {
            $document = Factory::getDocument();
            $viewType = $document->getType();
            $view = $this->getView($viewName, $viewType, '', array('base_path' => $this->basePath, 'layout' => $viewLayout));
            $sitemapmodel = $this->getModel('Sitemap');
            $view->setModel($sitemapmodel, true);
        }

        $safeurlparams = array('id' => 'INT', 'itemid' => 'INT', 'uid' => 'CMD', 'action' => 'CMD', 'property' => 'CMD', 'value' => 'CMD');

        parent::display($cachable, $safeurlparams);

        return $this;
    }

}
