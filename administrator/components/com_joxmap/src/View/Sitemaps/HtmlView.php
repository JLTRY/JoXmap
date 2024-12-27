<?php
/**
 * @version     $Id$
 * @copyright   Copyright (C) 2007 - 2009 Joomla! Vargas. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Guillermo Vargas (guille@vargas.co.cr)
 */

namespace JLTRY\Component\JoXmap\Administrator\View\Sitemaps;
// no direct access
defined('_JEXEC') or die;

use JLTRY\Component\JoXmap\Administrator\Helper\XmapHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\HTML\Helpers\Sidebar;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Version;
use Joomla\CMS\Language\Text;

/**
 * @package     Xmap
 * @subpackage  com_xmap
 * @since       2.0
 */
class HtmlView extends BaseHtmlView
{
    protected $state;
    protected $items;
    protected $pagination;

    /**
     * Display the view
     */
    public function display($tpl = null)
    {
        if ($this->getLayout() !== 'modal') {
            XmapHelper::addSubmenu('sitemaps');
        }

        $this->state      = $this->get('State');
        $this->items      = $this->get('Items');
        $this->pagination = $this->get('Pagination');

        $version = new Version;

        $message = $this->get('ExtensionsMessage');
        if ( $message ) {
            Factory::getApplication()->enqueueMessage($message);
        }

        // Check for errors.
        if ($errors && count($errors = $this->get('Errors'))) {
            Factory::getApplication()->enqueueMessage(500, implode("\n", $errors), 'error');
            return false;
        }

        // We don't need toolbar in the modal window.
        if ($this->getLayout() !== 'modal') {
            $this->addToolbar();
        }

        parent::display($tpl);
    }

    /**
     * Display the toolbar
     *
     * @access      private
     */
    protected function addToolbar()
    {
        $state = $this->get('State');
        $doc = Factory::getDocument();
        $version = new Version;

        ToolBarHelper::addNew('sitemap.add');
        ToolBarHelper::custom('sitemap.edit', 'edit.png', 'edit_f2.png', 'JTOOLBAR_EDIT', true);

        $doc->addStyleDeclaration('.icon-48-sitemap {background-image: url(media/com_joxmap/images/sitemap-icon.png);}');
        ToolBarHelper::title(Text::_('XMAP_SITEMAPS_TITLE'), 'sitemap.png');
        ToolBarHelper::custom('sitemaps.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_Publish', true);
        ToolBarHelper::custom('sitemaps.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);

        if (version_compare($version->getShortVersion(), '3.0.0', '>=')) {
            ToolBarHelper::custom('sitemaps.setdefault', 'featured.png', 'featured_f2.png', 'XMAP_TOOLBAR_SET_DEFAULT', true);
        } else {
            ToolBarHelper::custom('sitemaps.setdefault', 'default.png', 'default_f2.png', 'XMAP_TOOLBAR_SET_DEFAULT', true);
        }
        if ($state) {
            if ($state->get('filter.published') == -2) {
                ToolBarHelper::deleteList('', 'sitemaps.delete','JTOOLBAR_DELETE');
            }
            else {
                ToolBarHelper::trash('sitemaps.trash','JTOOLBAR_TRASH');
            }
        }
        ToolBarHelper::divider();


        if (class_exists('JHtmlSidebar') && $this->state){
            Sidebar::addFilter(
                Text::_('JOPTION_SELECT_PUBLISHED'),
                'filter_published',
                HTMLHelper::_('select.options', HTMLHelper::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true)
            );

            SideBar::addFilter(
                Text::_('JOPTION_SELECT_ACCESS'),
                'filter_access',
                HTMLHelper::_('select.options', HTMLHelper::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'))
            );

            $this->sidebar = SideBar::render();
        }
    }
}
