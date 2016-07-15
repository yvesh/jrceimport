<?php
/**
 * @package     Joomla.Marketing
 * @subpackage  com_jrceimport
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controllerform');

/**
 * Class JrceimportControllerImport
 *
 * @since  1.0
 */
class JrceControllerImport extends JControllerLegacy
{
	/**
	 * The import lang
	 *
	 * @var    string
	 *
	 * @since  0.9.3
	 */
	protected $lang = '*';

	/**
	 * Import of the csv file
	 *
	 * @todo refactor, add error management and test data before
	 *
	 * @throws  Exception on error
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function import()
	{
		$input = JFactory::getApplication()->input;

		$file = $input->files->get('jform')['csv'];

		if (empty($file))
		{
			throw new Exception('No file uploaded');
		}

		jimport('joomla.filesystem.file');

		if (!strtolower(JFile::getExt($file['name'])) == 'csv')
		{
			throw new Exception('No CSV File');
		}

		$delimiter = $input->get("csv_delimiter", ",", "string");
		$enclosure = $input->get("csv_enclosure", '"', "string");
		$escape = $input->get("csv_escape", '\\', "string");

		$csv_array = $this->convertCsvToArray($file['tmp_name'], $delimiter, $enclosure, $escape);

		JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_content/models');
		JTable::addIncludePath(JPATH_ROOT . '/libraries/legacy/table/content.php');

		/** @var  ContentModelArticle $articleModel */
		$articleModel = JModelLegacy::getInstance('Article', 'ContentModel');

		$jform = $input->get('jform', array(), 'ARRAY');

		$catid = $jform['category'];
		$this->lang  = $jform['language'];

		foreach ($csv_array as $item)
		{
			$table = $articleModel->getTable();

			$article = array();

			if (!isset($item['Headline']) || empty($item['Headline']))
			{
				continue;
			}

			$article['title']      = trim($item['Headline']);
			$article['catid']      = $catid;
			$article['alias']      = JFilterOutput::stringURLSafe(trim($item['Headline']));

			$article['introtext']  = '<p>' . nl2br(trim($item['introtext'])) . '</p>';
			$article['fulltext']   = '<p>' . nl2br(trim($item['fulltext'])) . '</p>';
			$article['state']      = 1;
			$article['created']    = JFactory::getDate()->toSql();
			$article['modified']   = JFactory::getDate()->toSql();
			$article['created_by'] = JFactory::getUser()->id;
			$article['language']   = $this->lang;

			$images = new stdClass;

			$images->image_intro            = $this->getImagePath($item['intro-img-path']);
			$images->image_intro_alt        = $item['intro-img-path-alt'];
			$images->image_intro_caption    = $item['intro-img-title'];
			$images->image_fulltext         = $this->getImagePath($item['full-img-path']);
			$images->image_fulltext_alt     = $item['full-img-alt'];
			$images->image_fulltext_caption = $item['full-img-title'];

			$article['images']  = json_encode($images);

			$urls = new stdClass;

			$urls->urla     = $item['issues-link-A'];
			$urls->urlatext = $item['Link-Title-A'];
			$urls->targeta  = $item['Link-target-A'];
			$urls->urlb     = $item['docs-joomla-org-link-B'];
			$urls->urlbtext = $item['Link-Title-B'];
			$urls->targetb  = $item['Link-target-B'];
			$urls->urlc     = $item['more-link-C'];
			$urls->urlctext = $item['Link-Title-C'];
			$urls->targetc  = $item['Link-target-C'];

			$urls->metakey  = '';
			$urls->metadesc = $item['metadescription'];

			$article['urls'] = json_encode($urls);

			$tags = explode(',', $item['tags']);

			// Remove white spaces
			if (count($tags))
			{
				$tags = array_map('trim', $tags);
			}

			$article['tags']    = $tags;
			$article['newTags'] = $tags;

			// Save the article with the data we created
			// $articleModel->save($article);
			$table->bind($article);

			$table->tags = $tags;
			$table->newTags = $tags;

			// Really, i dream of dead kittens now
			foreach ($tags as $i => $tag)
			{
				$tags[$i] = '#new#' . $tag;
			}

			$table->tagsHelper = new JHelperTags();
			$ids = $table->tagsHelper->createTagsFromField($tags);

			// Update tags language...
			$this->updateTags($ids);

			$table->store();
		}

		$this->setRedirect('index.php?option=com_jrceimport', 'Import to category ' . $catid . ' and language ' . $lang . ' successfully finished!');
	}


	/**
	 * Converts a csv string to an array
	 *
	 * @param   string  $filename   - The filename
	 * @param   string  $delimiter  - The delimiter char
	 *
	 * @return  array|bool
	 */
	protected function convertCsvToArray($filename = '', $delimiter = ',', $enclosure = '"', $escape = "\\")
	{
		if (!file_exists($filename) || !is_readable($filename))
		{
			return false;
		}

		$header = null;
		$data = array();

		if (($handle = fopen($filename, 'r')) !== false)
		{
			while (($row = fgetcsv($handle, 1000, $delimiter, $enclosure, $escape)) !== false)
			{
				if (!$header)
				{
					$header = $row;
				}
				else
				{
					$data[] = array_combine($header, $row);
				}
			}

			fclose($handle);
		}

		return $data;
	}

	/**
	 * Get the path (including images) of the image
	 *
	 * @param   string  $image  The image path
	 *
	 * @return  string
	 */
	protected function getImagePath($image)
	{
		$image = trim($image);

		if (empty($image))
		{
			return '';
		}

		return 'images/' . $image;
	}

	/**
	 * Update tags as createTagsFromField, does not really fit
	 *
	 * @param   array  $ids  The tag ids to update
	 *
	 * @return  bool
	 *
	 * @since   0.9.3
	 */
	protected function updateTags($ids)
	{
		if (!$ids && !count($ids))
		{
			return false;
		}

		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tags/tables');
		$tagTable = JTable::getInstance('Tag', 'TagsTable');

		foreach ($ids as $id)
		{
			$tagTable->load($id);

			$tagTable->language = $this->lang;

			$tagTable->store();
		}

		return true;
	}
}
