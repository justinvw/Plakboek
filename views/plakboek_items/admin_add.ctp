<script type="text/javascript" charset="utf-8">
    $(document).ready(function(){
        $("#PlakboekItemTitle").slug({
            slug:'slug',
            hide: false
        });
    });
</script>
<div id="plakboek_items" class="plakboek form">
    <h2><?php echo $title_for_layout; ?></h2>
    
    <?php echo $form->create('PlakboekItem', array('action' => 'add')); ?>
	<fieldset>
	<?php
		echo $form->input('title', array('label' => __('Title', true)));
		echo $form->input('slug', array('label' => __('Slug', true), 'class' => 'slug'));
		echo $form->input('PlakboekType', array('label' => __('Types', true), 'type' => 'select', 'multiple' => true, 'options' => $types));
		echo $form->input('category_id', array('type' => 'select', 'options' => $categories, 'empty' => true)); 
		echo $form->input('excerpt', array('label' => __('Excerpt', true)));
		echo $form->input('description', array('label' => __('Description', true)));
		echo $form->input('date_published');
		echo $form->input('status', array(
			'label' => __('Published', true),
			'checked' => 'checked',
		));
	?>
	</fieldset>
	<?php echo $form->end('Submit'); ?>
</div>