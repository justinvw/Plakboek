<script type="text/javascript" charset="utf-8">
    $(document).ready(function(){
        $("#PlakboekCategoryTitle").slug({
            slug:'slug',
            hide: false
        }); 
    });
</script>
<div id="plakboek_categories" class="plakboek form">
    <h2><?php echo $title_for_layout; ?></h2>
    
    <?php echo $form->create('PlakboekCategory', array('action' => 'edit')); ?>
	<fieldset>
	<?php
		echo $form->input('title', array('label' => __('Title', true)));
		echo $form->input('slug', array('label' => __('Slug', true), 'class' => 'slug'));
		echo $form->input('description', array('label' => __('Description', true)));
	?>
	</fieldset>
	<?php echo $form->end('Submit'); ?>
</div>