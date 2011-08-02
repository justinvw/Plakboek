<a href="#"><?php __('Plakboek'); ?></a>
<ul>
	<li><?php echo $html->link(__('Types', true), array('plugin' => 'plakboek', 'controller' => 'plakboek_types', 'action' => 'index')); ?></li>
	<li><?php echo $html->link(__('Items', true), array('plugin' => 'plakboek', 'controller' => 'plakboek_items', 'action' => 'index')); ?></li>
	<li><?php echo $html->link(__('Categories', true), array('plugin' => 'plakboek', 'controller' => 'plakboek_categories', 'action' => 'index')); ?></li>
</ul>