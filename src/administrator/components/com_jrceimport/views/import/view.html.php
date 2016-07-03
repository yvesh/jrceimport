<?php
/**
 * @package     Joomla.Marketing
 * @subpackage  com_jrceimport
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die('Restricted access');

/**
 * Class JrceimportViewImport
 *
 * @since  1.0
 */
class JrceViewImport extends JViewLegacy
{
	/**
	 * Displays the view
	 *
	 * @param   string  $tpl  - custom template
	 *
	 * @return mixed|void
	 */
	public function display($tpl = null)
	{
		$this->addToolbar();

		$this->form = $this->get('Form');

		parent::display($tpl);
	}

	/**
	 * Ads the toolbar buttons
	 *
	 * @return void
	 */
	public function addToolbar()
	{
		JToolbarHelper::title('Joomla Release Content entry Importer');
	}
}
