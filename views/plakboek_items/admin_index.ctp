<div id="plakboek_items" class="plakboek index">
    <h2><?php echo '<span>'.$title_for_layout.'</span>'; ?></h2>
    <div class="actions">
		<ul>
			<li><?php echo $html->link(__('Add Item', true), array('action' => 'add')); ?></li>
		</ul>
	</div>
    <table cellpadding="0" cellspacing="0">
        <?php
			$tableHeaders = $html->tableHeaders(array(
				$paginator->sort('id'),
				$paginator->sort('title'),
				$paginator->sort('date_published'),
				$paginator->sort('updated'),
				$paginator->sort('created'),
				__('Actions', true),
			));
			echo $tableHeaders;
			
			$rows = array();
			foreach($items as $item){
				$actions = $html->link(__('Edit', true), array('action' => 'edit', $item['PlakboekItem']['id']));
				$actions .= ' ' . $html->link(__('Delete', true), array(
					'action' => 'delete',
					$item['PlakboekItem']['id']
				), null, __('Are you sure?', true));
			
				$rows[] = array(
					$item['PlakboekItem']['id'],
					$item['PlakboekItem']['title'],
					$item['PlakboekItem']['date_published'],
					$item['PlakboekItem']['updated'],
					$item['PlakboekItem']['created'],
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