<?php
	echo $html->script('/plakboek/js/fileuploader.js', array('inline' => false));
	echo $html->css('/plakboek/css/fileuploader.css', 'stylesheet', array('inline' => false));
?>
<script type="text/javascript" charset="utf-8">
    $(document).ready(function(){
	    var askuser = false;
		window.onbeforeunload = confirmClose;

		function confirmClose(){
			if(askuser){
				return "You are uploading files, blahblah blah!";
			}
		}
		
		function getUploadedPictures(){
		    picture_container = $('#item-pictures');
		    picture_container.fadeOut();
		    
		    $.get(Croogo.basePath + 'admin/plakboek/plakboek_pictures/index/<?php echo $this->data['PlakboekItem']['id']; ?>', function(data) {
                picture_container.html(data);
                picture_container.fadeIn();
                $('a.delete').click(function(){
                    $.get(this.href);
      			    getUploadedPictures();
                    
      			    return false;
      		    });
            });
		}
	    
	    var uploader = new qq.FileUploader({
		    element: $('#file-uploader')[0],
		    action: Croogo.basePath + 'admin/plakboek/plakboek_pictures/upload',
			allowedExtensions: ['jpg', 'jpeg', 'gif', 'png'],
			onSubmit: function(id, fileName){
				console.log('submitting');
			},
			onProgress: function(id, fileName, loaded, total){
				askuser = true;
			},
			onComplete: function(id, fileName, responseJSON){
				askuser = false;
				getUploadedPictures();
			}
		});
		
		uploader.setParams({ item_id: <?php echo $this->data['PlakboekItem']['id']; ?>});
		
		getUploadedPictures();
		
        $("#PlakboekItemTitle").slug({
            slug:'slug',
            hide: false
        });
	});
</script>

<div id="plakboek_items" class="plakboek form">
    <h2><?php echo $title_for_layout; ?></h2>
    
    <?php echo $form->create('PlakboekItem', array('action' => 'edit')); ?>
	<fieldset>
	    <div class="tabs">
	        <ul>
	            <li><a href="#general"><span><?php __('General'); ?></span></a></li>
	            <li><a href="#pictures"><span><?php __('Pictures'); ?></span></a></li>
	        </ul>
	        
	        <div id="general">
	            <?php
            		echo $form->input('title', array('label' => __('Title', true)));
            		echo $form->input('slug', array('label' => __('Slug', true), 'class' => 'slug'));
            		echo $form->input('PlakboekType', array('label' => __('Types', true), 'type' => 'select', 'multiple' => true, 'options' => $types));
            		echo $form->input('category_id', array('type' => 'select', 'options' => $categories, 'empty' => true)); 
            		echo $form->input('excerpt', array('label' => __('Excerpt', true)));
            		echo $form->input('description', array('label' => __('Description', true)));
            		echo $form->input('date_published');
            		echo $form->input('status', array('label' => __('Status', true)));		
            	?>
	        </div>
	        <div id="pictures">
                <div id="file-uploader">       
            	    <noscript>          
            	        <p>Please enable JavaScript to use file uploader.</p>
            	    </noscript>         
            	</div>
            	
            	<div id="item-pictures">
            	    
            	</div>
	        </div>
	    </div>
	</fieldset>
	<?php echo $form->end('Submit'); ?>
</div>