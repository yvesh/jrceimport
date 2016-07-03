<?php
/**
 * @package     Joomla.Marketing
 * @subpackage  com_jrceimport
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die('Restricted access');
?>

<div class="container-fluid">
	<form action="<?php echo JRoute::_('index.php?option=com_jrceimport&task=import.import'); ?>" method="post" enctype="multipart/form-data">

		<?php  ?>

			<?php foreach ($this->form->getFieldset('basic') as $field) : ?>
				<?php if (strtolower($field->type) != 'spacer') : ?>
					<div class="form-group">
						<?php echo $field->label; ?>
						<div class="col-sm-10">
							<?php echo $field->input; ?>
						</div>
					</div>
				<?php else : ?>
					<hr/>
				<?php endif; ?>
			<?php endforeach; ?>

		<hr />
		<div>
			<button class="btn btn-primary">Import</button>
		</div>
	</form>
</div>
