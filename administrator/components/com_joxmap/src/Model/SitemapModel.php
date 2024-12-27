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

namespace JLTRY\Component\JoXmap\Administrator\Model;
// No direct access
defined('_JEXEC') or die;

use JLTRY\Component\JoXmap\Administrator\Helper\XmapHelper;
use Joomla\CMS\Factory; 
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Object\CMSObject;
use Joomla\Registry\Registry as JRegistry;
use Joomla\CMS\Version;
 
/**
 * Sitemap model.
 *
 * @package       Xmap
 * @subpackage    com_xmap
 */
class SiteMapModel extends AdminModel
{
    protected $_context = 'com_joxmap';

    /**
     * Constructor.
     *
     * @param    array An optional associative array of configuration settings.
     * @see      JController
     */
    public function __construct($config = array())
    {
        parent::__construct($config);

        $this->_item = 'sitemap';
        $this->_option = 'com_joxmap';
    }

    /**
     * Method to auto-populate the model state.
     */
    protected function _populateState()
    {
        $app = Factory::getApplication('administrator');

        // Load the User state.
        if (!($pk = (int) $app->getUserState('com_joxmap.edit.sitemap.id'))) {
            $pk = Factory::getApplication()->input->getInt('id');
        }
        $this->setState('sitemap.id', $pk);

        // Load the parameters.
        $params    = ComponentHelper::getParams('com_joxmap');
        $this->setState('params', $params);
    }

/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param		type	The table type to instantiate
	 * @param		string	A prefix for the table class name. Optional.
	 * @param		array	Configuration array for model. Optional.
	 * @return		Table	A database object
	 * @since		1.6
	 */
	public function getTable($type = 'Joxmap', $prefix = 'Administrator', $config = array())
	{
		/** @var \Joomla\CMS\MVC\Factory\MVCFactory $mvc */
		$mvc = Factory::getApplication()
				->bootComponent("com_joxmap")
				->getMVCFactory();
		return $mvc->createTable($type, $prefix, $config);
	}


    /**
     * Method to get a single record.
     *
     * @param    integer    The id of the primary key.
     *
     * @return   mixed      Object on success, false on failure.
     */
    public function getItem($pk = null)
    {
        return parent::getItem($pk);
    }

    /**
     * Method to get the record form.
     *
     * @param    array      $data        Data for the form.
     * @param    boolean    $loadData    True if the form is to load its own data (default case), false if not.
     * @return   mixed                   A JForm object on success, false on failure
     * @since    2.0
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm('com_joxmap.sitemap', 'sitemap', array('control' => 'jform', 'load_data' => $loadData));

        if (empty($form)) {
            return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return    mixed    The data for the form.
     * @since    1.6
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $data = Factory::getApplication()->getUserState('com_joxmap.edit.sitemap.data', array());

        if (empty($data)) {
            $data = $this->getItem();
        }

        return $data;
    }


    /**
     * Method to save the form data.
     *
     * @param    array    The form data.
     * @return    boolean    True on success.
     * @since    1.6
     */
    public function save($data)
    {
        // Initialise variables;
        $table      = $this->getTable();
        $pk         = (!empty($data['id'])) ? $data['id'] : (int)$this->getState('sitemap.id');
        $isNew      = true;

        // Load the row if saving an existing record.
        if ($pk > 0) {
            $table->load($pk);
            $isNew = false;
        }

        // Bind the data.
        if (!$table->bind($data)) {
            $this->setError(Text::sprintf('JERROR_TABLE_BIND_FAILED', $table->getError()));
            return false;
        }

        // Prepare the row for saving
        $this->_prepareTable($table);

        // Check the data.
        if (!$table->check()) {
            $this->setError($table->getError());
            return false;
        }

        if (!$table->is_default) {
            // Check if there is no default sitemap. Then, set it as default if not
            $result = $this->getDefaultSitemapId();
            if (!$result) {
                $table->is_default=1;
            }
        }

        // Store the data.
        if (!$table->store()) {
            $this->setError($table->getError());
            return false;
        }

        if ($table->is_default) {
            $query =  $this->_db->getQuery(true)
                           ->update($this->_db->quoteName('#__joxmap_sitemap'))
                           ->set($this->_db->quoteName('is_default').' = 0')
                           ->where($this->_db->quoteName('id').' <> '.$table->id);

            $this->_db->setQuery($query);
            if (!$this->_db->execute()) {
                  $this->setError($table->_db->getErrorMsg());
            }
        }

        // Clean the cache.
        $cache = Factory::getCache('com_joxmap');
        $cache->clean();

        $this->setState('sitemap.id', $table->id);

        return true;
    }

    /**
     * Prepare and sanitise the table prior to saving.
     */
    protected function _prepareTable(&$table)
    {
        // TODO.
    }

    function _orderConditions($table = null)
    {
        $condition = array();
        return $condition;
    }

    function setDefault($id)
    {
        $table = $this->getTable();
        if ($table->load($id)) {
            $db = Factory::getDbo();
            $query = $db->getQuery(true)
                        ->update($db->quoteName('#__joxmap_sitemap'))
                        ->set($db->quoteName('is_default').' = 0')
                        ->where($db->quoteName('id').' <> '.$table->id);
            $this->_db->setQuery($query);
            if (version_compare(Version, '4.0', 'ge')) {
              if (!$this->_db->execute()) {
                  $this->setError($table->_db->getErrorMsg());
              }
            } else {
                if (!$this->_db->query()) {
                    $this->setError($table->_db->getErrorMsg());
                    return false;
                }
           }
            $table->is_default = 1;
            $table->store();

            // Clean the cache.
            $cache = Factory::getCache('com_joxmap');
            $cache->clean();
            return true;
        }
    }

    /**
     * Override to avoid warnings
     *
     */
    public function checkout($pk = null)
    {
        return true;
    }

    private function getDefaultSitemapId()
    {
        $db = Factory::getDBO();
        $query  = $db->getQuery(true);
        $query->select('id');
        $query->from($db->quoteName('#__joxmap_sitemap'));
        $query->where('is_default=1');
        $db->setQuery($query);
        return $db->loadResult();
    }
}