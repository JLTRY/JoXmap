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
 
defined('_JEXEC') or die;

use Joomla\CMS\Form\Field\FormField;
use Joomla\CMS\HTML\HTMLHelper;
/**
 * Supports a modal sitemap picker.
 *
 * @package             Xmap
 * @subpackage          com_xmap
 * @since               2.0
 */
class JFormFieldModal_Sitemaps extends FormField
{

    /**
     * The field type.
     *
     * @var    string
     */
    protected $type = 'Modal_Sitemaps';

    /**
     * Method to get a list of options for a sitemaps list input.
     *
     * @return    array        An array of JHtml options.
     */
    protected function getInput()
    {
        // Initialise variables.
        $db  = Factory::getDBO();
        $doc = Factory::getDocument();


        // Load the modal behavior.
        if (version_compare(Version, '4.0', '>'))
        {
            HTMLHelper::_('bootstrap.renderModal', 'moderateModal');
        } else
        {
            JHTMLHelper::_('behavior.modal', 'a.modal');
        }

        // Get the title of the linked chart
        if ($this->value) {
            $db->setQuery(
                    'SELECT title' .
                    ' FROM #__joxmap_sitemap' .
                    ' WHERE id = ' . (int) $this->value
            );
            $title = $db->loadResult();
            if (version_compare(Version, '4.0', '<')){
                if ($error = $db->getErrorMsg()) {
                    Factory::getApplication()->enqueueMessage(500, $error, 'warning');
                }
            }
        } else {
            $title = '';
        }

        if (empty($title)) {
            $title = Text::_('COM_JOXMAP_SELECT_AN_SITEMAP');
        }

        $doc->addScriptDeclaration(
                  "function jSelectSitemap_" . $this->id . "(id, title, object) {
                       $('" . $this->id . "_id').value = id;
                       $('" . $this->id . "_name').value = title;
                       SqueezeBox.close();
                  }"
        );

        $link = 'index.php?option=com_joxmap&amp;view=sitemaps&amp;layout=modal&amp;tmpl=component&amp;function=jSelectSitemap_' . $this->id;
        if (version_compare(Version, '4.0', '<')){
            JHTMLHelper::_('behavior.modal', 'a.modal');
        }
        $html = '<span class="input-append">';
        $html .= "\n" . '<input class="input-medium" type="text" id="' . $this->id . '_name" value="' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '" disabled="disabled" />';
        if(version_compare(Version,'3.0.0','ge'))
            $html .= '<a class="modal btn" title="' . Text::_('COM_JOXMAP_CHANGE_SITEMAP') . '"  href="' . $link . '" rel="{handler: \'iframe\', size: {x: 800, y: 450}}"><i class="icon-file"></i> ' . Text::_('COM_JOXMAP_CHANGE_SITEMAP_BUTTON') . '</a>' . "\n";
        else
            $html .= '<div class="button2-left"><div class="blank"><a class="modal btn" title="' . Text::_('COM_JOXMAP_CHANGE_SITEMAP') . '"  href="' . $link . '" rel="{handler: \'iframe\', size: {x: 800, y: 450}}"><i class="icon-file"></i> ' . Text::_('COM_JOXMAP_CHANGE_SITEMAP_BUTTON') . '</a></div></div>' . "\n";
        $html .= '</span>';
        $html .= "\n" . '<input type="hidden" id="' . $this->id . '_id" name="' . $this->name . '" value="' . (int) $this->value . '" />';
        return $html;
    }

}