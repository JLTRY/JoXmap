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

namespace JLTRY\Component\JoXmap\Administrator\View\Sitemap;

// no direct access
defined('_JEXEC') or die;

use JLTRY\Component\JoXmap\Administrator\Helper\XmapHelper;
use JLTRY\Component\JoXmap\Administrator\Helper\Field\XmapMenusField;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\Version;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

/**
 * @package    JoXmap
 * @subpackage com_joxmap
 */
class HtmlView extends BaseHtmlView
{

    protected $item;
    protected $list;
    protected $form;
    protected $state;

    /**
     * Display the view
     *
     * @access    public
     */
    function display($tpl = null)
    {
        $app = Factory::getApplication();
        $this->item = $this->get('Item');
        if ($this->item) {
            $this->state = $this->get('State');
        }
        $this->form = $this->get('Form');
        $this->fieldsets   =  $this->form ?  $this->form->getFieldsets() : null;

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode("\n", $errors));
            return false;
        }

        HTMLHelper::stylesheet('media/com_joxmap/css/xmapedit.css');
        // Convert dates from UTC
        $offset = $app->getCfg('offset');
        if (intval($this->item->created)) {
            $this->item->created = HTMLHelper::date($this->item->created, '%Y-%m-%d %H-%M-%S', $offset);
        }

        $this->addToolbar();

        // XmapHelper::setVar('hidemainmenu', true);
        parent::display($tpl);
    }

    /**
     * Display the view
     *
     * @access    public
     */
    function navigator($tpl = null)
    {
        $app = Factory::getApplication();
        $this->item = $this->get('Item');
        if ($this->item) {
            $this->state = $this->get('State');
        }

        $menuItems = XmapHelper::getMenuItems($item->selections);
        $extensions = XmapHelper::getExtensions();
        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode("\n", $errors));
            return false;
        }

        HTMLHelper::script('mootree.js', 'media/system/js/');
        HTMLHelper::stylesheet('mootree.css', 'media/system/css/');

        $this->loadTemplate('class');
        $displayer = new XmapNavigatorDisplayer($state->params, $this->item);

        parent::display($tpl);
    }

    function navigatorLinks($tpl = null)
    {
        $link = urldecode(XmapHelper::getVar('link', ''));
        $name = XmapHelper::getCmd('e_name', '');
        $Itemid = XmapHelper::getInt('Itemid');

        $this->item = $this->get('Item');
        $this->state = $this->get('State');
        $menuItems = XmapHelper::getMenuItems($item->selections);
        $extensions = XmapHelper::getExtensions();

        $this->loadTemplate('class');
        $nav = new XmapNavigatorDisplayer($state->params, $item);
        $nav->setExtensions($extensions);

        $this->list = array();
        // Show the menu list
        if (!$link && !$Itemid) {
            foreach ($menuItems as $menutype => &$menu) {
                $menu = new stdclass();
                #$menu->id = 0;
                #$menu->menutype = $menutype;

                $node = new stdClass;
                $node->uid = "menu-" . $menutype;
                $node->menutype = $menutype;
                $node->ordering = $item->selections->$menutype->ordering;
                $node->priority = $item->selections->$menutype->priority;
                $node->changefreq = $item->selections->$menutype->changefreq;
                $node->browserNav = 3;
                $node->type = 'separator';
                if (!$node->name = $nav->getMenuTitle($menutype, @$menu->module)) {
                    $node->name = $menutype;
                }
                $node->link = '-menu-' . $menutype;
                $node->expandible = true;
                $node->selectable = false;
                //$node->name = $this->getMenuTitle($menutype,@$menu->module);    // get the mod_mainmenu title from modules table

                $this->list[] = $node;
            }
        } else {
            $parent = new stdClass;
            if ($Itemid) {
                // Expand a menu Item
                $items = &JSite::getMenu();
                $node = & $items->getItem($Itemid);
                if (isset($menuItems[$node->menutype])) {
                    $parent->name = $node->title;
                    $parent->id = $node->id;
                    $parent->uid = 'itemid' . $node->id;
                    $parent->link = $link;
                    $parent->type = $node->type;
                    $parent->browserNav = $node->browserNav;
                    $parent->priority = $item->selections->{$node->menutype}->priority;
                    $parent->changefreq = $item->selections->{$node->menutype}->changefreq;
                    $parent->menutype = $node->menutype;
                    $parent->selectable = false;
                    $parent->expandible = true;
                }
            } else {
                $parent->id = 1;
                $parent->link = $link;
            }
            $this->list = $nav->expandLink($parent);
        }

        parent::display('links');
    }

    /**
     * Display the toolbar
     *
     * @access    private
     */
    function addToolbar()
    {
        $user = Factory::getUser();
        $isNew = ($this->item->id == 0);

		$title = Text::_('XMAP_PAGE_' . ($isNew ? 'ADD_SITEMAP' : 'EDIT_SITEMAP'));
		ToolbarHelper::title($title,'article-add.png');
		ToolBarHelper::apply('sitemap.apply', 'JTOOLBAR_APPLY');
		ToolbarHelper::saveGroup(
				[
					['save', 'sitemap.save'],
					['save2new', 'sitemap.save2new']
				]);
        if (!$isNew) {
            ToolbarHelper::saveGroup(
				['save2copy', 'sitemap.save2copy']);
        }
        ToolBarHelper::cancel('sitemap.cancel', 'JTOOLBAR_CLOSE');
    }

}
