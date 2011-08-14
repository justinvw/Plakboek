<?php
class PlakboekActivation {
	public function beforeActivation(&$controller) {
		return true;
	}

	public function onActivation(&$controller) {
		// ACL: set ACOs with permissions
		$controller->Croogo->addAco('PlakboekTypes');
		$controller->Croogo->addAco('PlakboekTypes/admin_index');
		$controller->Croogo->addAco('PlakboekTypes/admin_add');
		$controller->Croogo->addAco('PlakboekTypes/admin_edit');
		$controller->Croogo->addAco('PlakboekTypes/admin_delete');
		
		$controller->Croogo->addAco('PlakboekCategories');
		$controller->Croogo->addAco('PlakboekCategories/admin_index');
		$controller->Croogo->addAco('PlakboekCategories/admin_add');
		$controller->Croogo->addAco('PlakboekCategories/admin_edit');
		$controller->Croogo->addAco('PlakboekCategories/admin_delete');
		
	    $controller->Croogo->addAco('PlakboekItems');
	    $controller->Croogo->addAco('PlakboekItems/index', array('registered', 'public'));
	    $controller->Croogo->addAco('PlakboekItems/index_items', array('registered', 'public'));
	    $controller->Croogo->addAco('PlakboekItems/view', array('registered', 'public'));
	    $controller->Croogo->addAco('PlakboekItems/admin_index');
	    $controller->Croogo->addAco('PlakboekItems/admin_add');
	    $controller->Croogo->addAco('PlakboekItems/admin_edit');
	    $controller->Croogo->addAco('PlakboekItems/admin_delete');
	    
	    $controller->Croogo->addAco('PlakboekPictures');
	    $controller->Croogo->addAco('PlakboekPictures/admin_index');
	    $controller->Croogo->addAco('PlakboekPictures/admin_upload');
	    $controller->Croogo->addAco('PlakboekPictures/admin_delete');
	}

	public function beforeDeactivation(&$controller) {
		return true;
	}

	public function onDeactivation(&$controller) {
		// ACL: remove ACOs with permissions
		$controller->Croogo->removeAco('PlakboekTypes');
		$controller->Croogo->removeAco('PlakboekCategories');
		$controller->Croogo->removeAco('PlakboekItems');
		$controller->Croogo->removeAco('PlakboekPictures');
	}
}
?>