<?php
	Croogo::hookRoutes('Plakboek');
	Croogo::hookAdminMenu('Plakboek');
    
	# Load the Kieken settings
	if(file_exists(APP.'plugins'.DS.'plakboek'.DS.'config'.DS.'settings.yml')){
		 $settings = Spyc::YAMLLoad(file_get_contents(APP.'plugins'.DS.'plakboek'.DS.'config'.DS.'settings.yml'));
	}
	
	foreach($settings AS $settingKey => $settingValue){
		Configure::write($settingKey, $settingValue);
	}
?>