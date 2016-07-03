<?php

// PSR-4 Autoload by composer
require_once __DIR__ . '/vendor/autoload.php';

define('JPATH_BASE', __DIR__);

/**
 * Class RoboFile
 *
 * @since  1.0.0
 */
class RoboFile extends \Robo\Tasks
{
	use \Joomla\Jorobo\Tasks\loadTasks;
	use \joomla_projects\robo\loadTasks;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		// Set default timezone (so no warnings are generated if it is not set)
		date_default_timezone_set('UTC');
	}

	/**
	 * Map into Joomla installation.
	 *
	 * @param   String   $target    The target joomla instance
	 * @param   boolean  $override  Override existing mappings?
	 *
	 * @return  void
	 */
	public function map($target, $override = true)
	{
		$this->taskMap($target)->run();
	}

	/**
	 * Build the joomla extension package
	 *
	 * @param   array  $params  Additional params
	 *
	 * @return  void
	 */
	public function build($params = ['dev' => false])
	{
		$this->taskBuild($params)->run();
	}

	/**
	 * Update copyright headers for this project. (Set them up in the jorobo.ini)
	 **
	 * @param   array  $params  Additional params
	 *
	 * @return  void
	 */
	public function headers()
	{
		(new \Joomla\Jorobo\Tasks\CopyrightHeader())->run();
	}
}