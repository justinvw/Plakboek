<div id="plakboek_categories" class="plakboek index">
    <h2><?php echo '<span>'.$title_for_layout.'</span>'; ?></h2>
    <div class="actions">
		<ul>
			<li><?php echo $html->link(__('Add a Category', true), array('action' => 'add')); ?></li>
		</ul>
	</div>
    <table cellpadding="0" cellspacing="0">
        <?php
			$tableHeaders = $html->tableHeaders(array(
				$paginator->sort('id'),
				$paginator->sort('title'),
				$paginator->sort('updated'),
				$paginator->sort('created'),
				__('Actions', true),
			));
			echo $tableHeaders;
			
			$rows = array();
			foreach($categories as $category){
				$actions = $html->link(__('Edit', true), array('action' => 'edit', $category['PlakboekCategory']['id']));
				$actions .= ' ' . $html->link(__('Delete', true), array(
					'action' => 'delete',
					$category['PlakboekCategory']['id']
				), null, __('Are you sure?', true));
			
				$rows[] = array(
					$category['PlakboekCategory']['id'],
					$category['PlakboekCategory']['title'],
					$category['PlakboekCategory']['updated'],
					$category['PlakboekCategory']['created'],
					$actions
				);
			}
			
			echo $html->tableCells($rows);
			echo $tableHeaders;
		?>
    </table>
</div>
<div class="paging"><?php echo $paginator->numbers(); ?></div>
<div class="counter"><?php echo $paginator->counter(array('format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true))); ?></div>