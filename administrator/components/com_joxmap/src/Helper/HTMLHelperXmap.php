<?php
/**
 * @version     $Id$
 * @copyright   Copyright (C) 2007 - 2009 Joomla! Vargas. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Guillermo Vargas (guille@vargas.co.cr)
 */

namespace JLTRY\Component\JoXmap\Administrator\Helper;
// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Table\Table;
use Joomla\CMS\HTML\HTMLHelper;



/**
 * @package       Xmap
 * @subpackage    com_xmap
 */
abstract class HTMLHelperXmap
{

    /**
     * @param    string  $name
     * @param    string  $value
     * @param    int     $j
     */
    public static function priorities($name, $value = '0.5', $j)
    {
        // Array of options
        for ($i=0.1; $i<=1;$i+=0.1) {
            $options[] = HTMLHelper::_('select.option',$i,$i);;
        }
        return HTMLHelper::_('select.genericlist', $options, $name, null, 'value', 'text', $value, $name.$j);
    }

    /**
     * @param    string  $name
     * @param    string  $value
     * @param    int     $j
     */
    public static function changefrequency($name, $value = 'weekly', $j)
    {
        // Array of options
        $options[] = HTMLHelper::_('select.option','hourly','hourly');
        $options[] = HTMLHelper::_('select.option','daily','daily');
        $options[] = HTMLHelper::_('select.option','weekly','weekly');
        $options[] = HTMLHelper::_('select.option','monthly','monthly');
        $options[] = HTMLHelper::_('select.option','yearly','yearly');
        $options[] = HTMLHelper::_('select.option','never','never');
        return HTMLHelper::_('select.genericlist', $options, $name, null, 'value', 'text', $value, $name.$j);
    }

}

