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

namespace JLTRY\Component\JoXmap\Site\View\Html;

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

use JLTRY\Component\JoXmap\Site\Controller\JoXmapHtmlDisplayer;
use JLTRY\Component\JoXmap\Site\Helper\XmapHelper;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route as JRoute;

/**
 * XML Sitemap View class for the Xmap component
 *
 * @package      Xmap
 * @subpackage   com_xmap
 * @since        2.0
 */
class HtmlView extends BaseHtmlView
{

    protected $state;
    protected $print;

    function display($tpl = null)
    {
        // Initialise variables.
        $this->app = Factory::getApplication();
        $this->user = Factory::getUser();
        $doc = Factory::getDocument();

        // Get view related request variables.
        $this->print = XmapHelper::getBool('print');

        // Get model data.
        $this->state = $this->get('State');
        $this->item = $this->get('Item');
        $this->items = $this->get('Items');

        $this->canEdit = Factory::getUser()->authorise('core.admin', 'com_xmap');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseWarning(500, implode("\n", $errors));
            return false;
        }

        $this->extensions = $this->get('Extensions');
        // Add router helpers.
        $this->item->slug = $this->item->alias ? ($this->item->id . ':' . $this->item->alias) : $this->item->id;

        $this->item->rlink = JRoute::_('index.php?option=com_xmap&view=html&id=' . $this->item->slug);

        // Create a shortcut to the paramemters.
        $params = &$this->state->params;
        $offset = $this->state->get('page.offset');
        if ($params->get('include_css', 0)){
            $doc->addStyleSheet(Uri::root().'components/com_xmap/assets/css/xmap.css');
        }

        // If a guest user, they may be able to log in to view the full article
        // TODO: Does this satisfy the show not auth setting?
        if (!$this->item->params->get('access-view')) {
            if ($user->get('guest')) {
                // Redirect to login
                $uri = XmapHelper::getURI();
                $app->redirect(
                    'index.php?option=com_users&view=login&return=' . base64_encode($uri),
                    JText::_('Xmap_Error_Login_to_view_sitemap')
                );
                return;
            } else {
                JError::raiseWarning(403, JText::_('Xmap_Error_Not_auth'));
                return;
            }
        }

        // Override the layout.
        if ($layout = $params->get('layout')) {
            $this->setLayout($layout);
        }

        // Load the class used to display the sitemap
        $this->loadTemplate('class');
        $this->displayer = new JoXmapHtmlDisplayer($params, $this->item);

        $this->displayer->setJView($this);
        $this->displayer->canEdit = $this->canEdit;

        $this->_prepareDocument();
        parent::display($tpl);

        $model = $this->getModel();
        $model->hit($this->displayer->getCount());
    }

    /**
     * Prepares the document
     */
    protected function _prepareDocument()
    {
        $app = Factory::getApplication();
        $pathway = $app->getPathway();
        $menus = $app->getMenu();
        $title = null;

        // Because the application sets a default page title, we need to get it from the menu item itself
        if ($menu = $menus->getActive()) {
            if (isset($menu->query['view']) && isset($menu->query['id'])) {
            
                if ($menu->query['view'] == 'html' && $menu->query['id'] == $this->item->id) {
                    $title = $menu->title;
                    if (empty($title)) {
                        $title = $app->getCfg('sitename');
                    } else if ($app->getCfg('sitename_pagetitles', 0) == 1) {
                        $title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
                    } else if ($app->getCfg('sitename_pagetitles', 0) == 2) {
                        $title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
                    }
                    // set meta description and keywords from menu item's params
                    $params = new Registry();
                    $params->loadString($menu->getParams());
                    $this->document->setDescription($params->get('menu-meta_description'));
                    $this->document->setMetadata('keywords', $params->get('menu-meta_keywords'));
                }
            }
        }
        $this->document->setTitle($title);

        if ($app->getCfg('MetaTitle') == '1') {
            $this->document->setMetaData('title', $title);
        }

        if ($this->print) {
            $this->document->setMetaData('robots', 'noindex, nofollow');
        }
    }

}
