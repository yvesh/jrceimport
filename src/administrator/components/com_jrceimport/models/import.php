<?php
/**
 * @package     Joomla.Marketing
 * @subpackage  com_jrceimport
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die();
jimport('joomla.application.component.modeladmin');

/**
 * Class JrceModelImport
 *
 * @since  0.9.0
 */
class JrceModelImport extends JModelAdmin
{
	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      An optional array of data for the form to interogate.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  JForm    A JForm object on success, false on failure
	 *
	 * @since   0.9.0
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_jrceimport.import', 'import', array('control' => 'jform'));

		return $form;
	}
}
