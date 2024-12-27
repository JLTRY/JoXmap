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

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
use JLTRY\Component\JoXmap\Site\Helper\XmapHelper;
use Joomla\CMS\Uri\Uri ;

// Create shortcut to parameters.
$params = $this->item->params;

$live_site = substr_replace(Uri::root(), "", -1, 1);

header('Content-type: text/xml; charset=utf-8');

echo '<?xml version="1.0" encoding="UTF-8"?>',"\n";
if (($this->item->params->get('beautify_xml', 1) == 1) && !$this->displayer->isNews) {
    $params  = '&amp;filter_showtitle='.XmapHelper::getBool('filter_showtitle',0);
    $params .= '&amp;filter_showexcluded='.XmapHelper::getBool('filter_showexcluded',0);
    $params .= (XmapHelper::getCmd('lang')?'&amp;lang='.XmapHelper::getCmd('lang'):'');
    echo '<?xml-stylesheet type="text/xsl" href="'. $live_site.'/index.php?option=com_joxmap&amp;view=xml&amp;layout=xsl&amp;tmpl=component&amp;id='.$this->item->id.($this->isImages?'&amp;images=1':'').$params.'"?>'."\n";
}
?>
<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"<?php echo ($this->displayer->isImages? ' xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"':''); ?><?php echo ($this->displayer->isNews? ' xmlns:news="http://www.google.com/schemas/sitemap-news/0.9"':''); ?>>

<?php echo $this->loadTemplate('items'); ?>

</urlset>