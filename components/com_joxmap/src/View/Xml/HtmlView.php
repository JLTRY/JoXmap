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
 
namespace JLTRY\Component\JoXmap\Site\View\Xml;

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

use JLTRY\Component\JoXmap\Site\Controller\JoXmapXmlDisplayer;
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

    protected $_obLevel;

    function display($tpl = null)
    {
        // Initialise variables.
        $app = Factory::getApplication();
        $this->user = Factory::getUser();
        $isNewsSitemap = XmapHelper::getInt('news',0);
        $this->isImages = XmapHelper::getInt('images',0);

        $model = $this->getModel('Sitemap');
        $this->setModel($model);

        // force to not display errors on XML sitemap
        @ini_set('display_errors', 0);
        # Increase memory and max execution time for XML sitemaps to make it work
        # with very large sites
        @ini_set('memory_limit','512M');
        @ini_set('max_execution_time',300);

        $layout = $this->getLayout();

        $this->item = $this->get('Item');
        $this->state = $this->get('State');
        $this->canEdit = Factory::getUser()->authorise('core.admin', 'com_xmap');

        // For now, news sitemaps are not editable
        $this->canEdit = $this->canEdit && !$isNewsSitemap;

        if ($layout == 'xsl') {
            return $this->displayXSL($layout);
        }

        // Get model data.
        $this->items = $this->get('Items');
        $this->sitemapItems = $this->get('SitemapItems');
        $this->extensions = $this->get('Extensions');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
        if (version_compare(JVERSION, '4.0', 'ge')){
           Factory::getApplication()->enqueueMessage(implode("\n", $errors), 'warning');
        } else {
            JError::raiseWarning(500, implode("\n", $errors));
        }
            return false;
        }

        // Add router helpers.
        $this->item->slug = $this->item->alias ? ($this->item->id . ':' . $this->item->alias) : $this->item->id;

        $this->item->rlink = JRoute::_('index.php?option=com_xmap&view=xml&id=' . $this->item->slug);

        // Create a shortcut to the paramemters.
        $params = &$this->state->params;
        $offset = $this->state->get('page.offset');

        if (!$this->item->params->get('access-view')) {
            if ($this->user->get('guest')) {
                // Redirect to login
                $uri = Factory::getURI();
                $app->redirect(
                    'index.php?option=com_users&view=login&return=' . base64_encode($uri),
                    JText::_('Xmap_Error_Login_to_view_sitemap')
                );
                return;
            } else {
                JError::raiseWarning(403, Text::_('Xmap_Error_Not_auth'));
                return;
            }
        }

        // Override the layout.
        if ($layout = $params->get('layout')) {
            $this->setLayout($layout);
        }

        // Load the class used to display the sitemap
        $this->loadTemplate('class');
        $this->displayer = new JoXmapXmlDisplayer($params, $this->item);

        $this->displayer->setJView($this);

        $this->displayer->isNews = $isNewsSitemap;
        $this->displayer->isImages = $this->isImages;
        $this->displayer->canEdit = $this->canEdit;

        $doCompression = ($this->item->params->get('compress_xml') && !ini_get('zlib.output_compression') && ini_get('output_handler') != 'ob_gzhandler');
        $this->endAllBuffering();
        if ($doCompression) {
            ob_start();
        }

        parent::display($tpl);

        $model = $this->getModel();
        $model->hit($this->displayer->getCount());

        if ($doCompression) {
            $data = ob_get_contents();
            if (version_compare(JVERSION, '4.0', 'ge')){
                $app->setBody($data);
             } else {
                JResponse::setBody($data);
            }
            @ob_end_clean();
            if (version_compare(JVERSION, '4.0', 'ge')){
                echo $app->toString(true);
            } else {
                echo JResponse::toString(true);
            }
        }
        $this->recreateBuffering();
        exit;
    }

    function displayXSL()
    {
        $this->setLayout('default');

        $this->endAllBuffering();
        parent::display('xsl');
        $this->recreateBuffering();
        exit;
    }

    private function endAllBuffering()
    {
        $this->_obLevel = ob_get_level();
        $level = FALSE;
        while (ob_get_level() > 0 && $level !== ob_get_level()) {
            @ob_end_clean();
            $level = ob_get_level();
        }
    }
    private function recreateBuffering()
    {
        while($this->_obLevel--) {
            ob_start();
        }
    }

}
