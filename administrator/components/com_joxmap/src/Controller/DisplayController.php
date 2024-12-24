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

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;


/**
 * Default Controller of JoXmap component
 *
 * @package     Joomla.Administrator
 * @subpackage  com_joxmap
 */
class DisplayController extends BaseController {
    
    
    
    /**
     * Constructor.
     *
     * @param    array An optional associative array of configuration settings.
     * @see      BaseController
     */
   /*  public function __construct($config = array())
    {
        //$config["view_path"] = "F:\Sites\site OVH JLT local\joomla_5.0\administrator\components\com_joxmap\src\View";
        parent::__construct($config);
    }
 */
    public function display($cachable = false, $urlparams = array()) {

        $input = $this->app->getInput();
        // Set the default view (if not specified)
        $input->set('view', $input->getCmd('view', 'SiteMaps'));

        // Call parent to display
        parent::display($cachable);
    }
}