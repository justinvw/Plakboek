<?php if(count($pictures) == 0): ?>
	<div id="no-pictures">
		<p><?php __('This item does not contain any pictures.'); ?></p>
	</div>
<?php else: ?>
<div>
	<table>
		<tbody>
			<tr>
			<?php
				$count = 0;
				foreach($pictures as $picture):
				$count++;
			?>
					<td id="<?php echo $picture['PlakboekPicture']['id']; ?>">
						<?php echo $html->image(DS.Configure::read('Plakboek.uploadDirectory').$picture['PlakboekFile']['small']['filename'], array('class' => 'thumbnail','width' => '200px')); ?>
						<ul class="actions">
							<li><?php echo $html->link(__('Delete', true), array(
										'controller' => 'plakboek_pictures', 
										'action' => 'delete', 
										$picture['PlakboekPicture']['id']), 
									array('class' => 'delete')); ?></li>
						</ul>
					</td>
			<?
					if($count % 3 == 0){
						echo '</tr><tr>';
					}
				endforeach;
			?>
		</tbody>
	</table>
</div>
<?php endif;?>