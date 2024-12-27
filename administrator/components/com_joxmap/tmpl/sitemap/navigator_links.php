<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_joxmap
 *
 * @copyright   Copyright (C) 2024 JL Tryoen. All rights reserved.
                Copyright (C) 2007 - 2009 Joomla! Vargas. All rights reserved.
 * @author      JL Tryoen /  Guillermo Vargas (guille@vargas.co.cr)
 * @license     GNU General Public License version 3; see LICENSE
 */

defined('_JEXEC') or die;

header('Content-type: text/xml');

$name = XmapHelper::getCmd('e_name');
?>
<?xml version="1.0" encoding="UTF-8" ?>
<nodes>
<?php foreach ($this->list as $node) {
    $load = 'index.php?option=com_joxmap&amp;task=navigator-links&amp;sitemap='.$this->item->id.'&amp;e_name='.$name.(isset($node->id)?'&amp;Itemid='.$node->id:'').(isset($node->link)?'&amp;link='.urlencode($node->link):'').'&amp;tmpl=component';
?>
    <node text="<?php echo htmlentities($node->name); ?>" <?php echo ($node->expandible?" openicon=\"_open\" icon=\"_closed\" load=\"$load\"":' icon="_doc"'); ?> uid="<?php $node->uid; ?>" link="<?php echo str_replace(array('&amp;','&'),array('&','&amp;'),$node->link); ?>" selectable="<?php echo ($node->selectable?'true':'false'); ?>" />
<?php } ?>
</nodes>
