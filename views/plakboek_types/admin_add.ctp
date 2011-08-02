<script type="text/javascript" charset="utf-8">
    $(document).ready(function(){
        $("#PlakboekTypeType").slug({
            slug:'slug',
            hide: false
        }); 
    });
</script>
<div id="plakboek_types" class="plakboek form">
    <h2><?php echo $title_for_layout; ?></h2>
    
    <?php echo $form->create('PlakboekType', array('action' => 'add')); ?>
	<fieldset>
	<?php
		echo $form->input('type', array('label' => __('Type name', true)));
		echo $form->input('slug', array('label' => __('Slug', true), 'class' => 'slug'));
		echo $form->input('description', array('label' => __('Description', true)));
	?>
	</fieldset>
	<?php echo $form->end('Submit'); ?>
</div>