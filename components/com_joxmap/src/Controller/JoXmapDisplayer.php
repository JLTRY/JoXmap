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

// No direct access
namespace JLTRY\Component\JoXmap\Site\Controller;

defined( '_JEXEC' ) or die( 'Restricted access' );

use JLTRY\Component\JoXmap\Site\Helper\XmapHelper;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;
use Joomla\CMS\Date\Date ;
use Joomla\CMS\User\UserHelper;
use Joomla\CMS\Uri\Uri ;
use Joomla\Registry\Registry;

class JoXmapDisplayer extends BaseController {

    /**
     *
     * @var int  Counter for the number of links on the sitemap
     */
    protected $count;
    /**
     *
     * @var JView
     */
    protected $jview;

    public $config;
    public $sitemap;
    /**
     *
     * @var int   Current timestamp
     */
    public $now;
    public $userLevels;
    /**
     *
     * @var string  The current value for the request var "view" (eg. html, xml)
     */
    public $view;

    public $canEdit;

    function __construct($config,$sitemap)
    {
        $user = Factory::getUser();
        $groups = array_keys(UserHelper::getUserGroups($user->get('id')));
        $date = new Date();

        $this->userLevels    = (array)$user->getAuthorisedViewLevels();
        // Deprecated: should use userLevels from now on
        // $this->gid = $user->gid;
        $this->now    = $date->toUnix();
        $this->config    = $config;
        $this->sitemap    = $sitemap;
        $this->isNews   = false;
        $this->isImages    = false;
        $this->count    = 0;
        $this->canEdit  = false;
    }

    public function printNode( &$node ) {
        return false;
    }

    public function printSitemap()
    {
        foreach ($this->jview->items as $menutype => &$items) {

            $node = new \stdclass();

            $node->uid = "menu-".$menutype;
            $node->menutype = $menutype;
            $node->priority = null;
            $node->changefreq = null;
            // $node->priority = $menu->priority;
            // $node->changefreq = $menu->changefreq;
            $node->browserNav = 3;
            $node->type = 'separator';
            /**
             * @todo allow the user to provide the module used to display that menu, or some other
             * workaround
             */
            $node->name = $this->getMenuTitle($menutype,'mod_menu'); // Get the name of this menu

            $this->startMenu($node);
            $this->printMenuTree($node, $items);
            $this->endMenu($node);
        }
    }

    public function setJView($view)
    {
        $this->jview = $view;
    }

    public function getMenuTitle($menutype,$module='mod_menu')
    {
        $app = Factory::getApplication();
        $db = Factory::getDbo();
        $title = $extra = '';

        // Filter by language
        if ($app->getLanguageFilter()) {
            $extra = ' AND language in ('.$db->quote(Factory::getLanguage()->getTag()).','.$db->quote('*').')';
        }

        $db->setQuery(
             "SELECT * FROM #__modules WHERE module='{$module}' AND params "
            ."LIKE '%\"menutype\":\"{$menutype}\"%' AND access IN (".implode(',',$this->userLevels).") "
            ."AND published=1 AND client_id=0 "
            . $extra
            . "LIMIT 1"
        );
        $module = $db->loadObject();
        if ($module) {
            $title = $module->title;
        }
        return $title;
    }

    protected function startMenu(&$node)
    {
        return true;
    }
    protected function endMenu(&$node)
    {
        return true;
    }
    protected function printMenuTree($menu,&$items)
    {
        $this->changeLevel(1);

        //$router = XmapHelper::getRouter("site");//JSite::getRouter();

        foreach ( $items as $i => $item ) {                   // Add each menu entry to the root tree.
            $excludeExternal = false;

            $node = new \stdclass;

            $node->id           = $item->id;
            $node->uid          = $item->uid;
            $node->name         = $item->title;               // displayed name of node
            // $node->parent    = $item->parent;              // id of parent node
            $node->browserNav   = $item->browserNav;          // how to open link
            $node->priority     = $item->priority;
            $node->changefreq   = $item->changefreq;
            $node->type         = $item->type;                // menuentry-type
            $node->menutype     = $menu->menutype;            // menuentry-type
            $node->home         = $item->home;                // If it's a home menu entry
            // $node->link      = isset( $item->link ) ? htmlspecialchars( $item->link ) : '';
            $node->link         = $item->link;
            $node->option       = $item->option;
            $node->modified     = @$item->modified;
            $node->secure       = $item->params->get('secure');

            // New on Xmap 2.0: send the menu params
            $node->params =& $item->params;

            if ($node->home == 1) {
                // Correct the URL for the home page.
                $node->link = Uri::base();
            }
            switch ($item->type)
            {
                case 'separator':
                    $node->browserNav=3;
                    break;
                case 'url':
                    if ((strpos($item->link, 'index.php?') === 0) && (strpos($item->link, 'Itemid=') === false)) {
                        // If this is an internal Joomla link, ensure the Itemid is set.
                        $node->link = $node->link.'&Itemid='.$node->id;
                    } else {
                        $excludeExternal = ($this->view == 'xml');
                    }
                    break;
                case 'alias':
                    // If this is an alias use the item id stored in the parameters to make the link.
                    $node->link = 'index.php?Itemid='.$item->params->get('aliasoptions');
                    break;
                default:
				  if (XmapHelper::isAppSef()) {
					$node->link = 'index.php?Itemid='.$node->id;
                    }
                    elseif (!$node->home) {
                        $node->link .= '&Itemid='.$node->id;
                    }
                    break;
            }

            if ($excludeExternal || $this->printNode($node)) {

                //Restore the original link
                $node->link             = $item->link;
                $this->printMenuTree($node,$item->items);
                $matches=array();
                //if ( preg_match('#^/?index.php.*option=(com_[^&]+)#',$node->link,$matches) ) {
                if ( $node->option ) {
                    if ( !empty($this->jview->extensions[$node->option]) ) {
                         $node->uid = $node->option;
                        $className = 'xmap_'.$node->option;
                        $result = call_user_func_array(array($className, 'getTree'),array(&$this,&$node,&$this->jview->extensions[$node->option]->params));
                    }
                }
                //XmapPlugins::printTree( $this, $node, $this->jview->extensions );    // Determine the menu entry's type and call it's handler
            }
        }
        $this->changeLevel(-1);
    }

    public function changeLevel($step)
    {
        return true;
    }

    public function getCount()
    {
        return $this->count;
    }

    public function &getExcludedItems() {
        static $_excluded_items;
        if (!isset($_excluded_items)) {
            $_excluded_items = array();
            $registry = new Registry('_default');
            $registry->loadString($this->sitemap->excluded_items);
            $_excluded_items = $registry->toArray();
        }
        return $_excluded_items;
    }

    public function isExcluded($itemid,$uid) {
        $excludedItems = $this->getExcludedItems();
        $items = NULL;
        if (!empty($excludedItems[$itemid])) {
            if (is_object($excludedItems[$itemid])) {
                $excludedItems[$itemid] = (array) $excludedItems[$itemid];
            }
            $items =& $excludedItems[$itemid];
        }
        if (!$items) {
            return false;
        }
        return ( in_array($uid, $items));
    }
}
