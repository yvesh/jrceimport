<?php
/**
 * @package     Joomla.Marketing
 * @subpackage  com_jrceimport
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die('Restricted access');

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_jrceimport'))
{
	JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');

	return false;
}

$input = JFactory::getApplication()->input;
JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');

require_once JPATH_COMPONENT . '/controller.php';

JLoader::discover('JrceHelper', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/');

$controller = JControllerLegacy::getInstance('Jrce');
$controller->execute($input->getCmd('task', ''));
$controller->redirect();
