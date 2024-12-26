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
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

/**
 * Xmap Ajax Controller
 *
 * @package      Xmap
 * @subpackage   com_xmap
 * @since        2.0
 */
class XmapControllerAjax extends JControllerLegacy
{

    public function editElement()
    {
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));

        jimport('joomla.utilities.date');
        jimport('joomla.user.helper');
        $user = Factory::getUser();
        $groups = array_keys(UserHelper::getUserGroups($user->get('id')));
        $result = new Registry('_default');
        $sitemapId = XmapHelper::getInt('id');

        if (!$user->authorise('core.edit', 'com_xmap.sitemap.'.$sitemapId)) {
            $result->setValue('result', 'KO');
            $result->setValue('message', 'You are not authorized to perform this action!');
        } else {
            $model = $this->getModel('sitemap');
            if ($model->getItem()) {
                $action = XmapHelper::getCmd('action', '');
                $uid = XmapHelper::getCmd('uid', '');
                $itemid = XmapHelper::getInt('itemid', '');
                switch ($action) {
                    case 'toggleElement':
                        if ($uid && $itemid) {
                            $state = $model->toggleItem($uid, $itemid);
                        }
                        break;
                    case 'changeProperty':
                        $uid = XmapHelper::getCmd('uid', '');
                        $property = XmapHelper::getCmd('property', '');
                        $value = XmapHelper::getCmd('value', '');
                        if ($uid && $itemid && $uid && $property) {
                            $state = $model->chageItemPropery($uid, $itemid, 'xml', $property, $value);
                        }
                        break;
                }
            }
            $result->set('result', 'OK');
            $result->set('state', $state);
            $result->set('message', '');
        }

        echo $result->toString();
    }
}