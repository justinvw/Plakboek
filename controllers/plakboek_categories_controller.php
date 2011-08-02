<?php
class PlakboekCategoriesController extends PlakboekAppController {
    var $name = 'PlakboekCategories';
	var $uses = array('Plakboek.PlakboekCategory', 'Plakboek.PlakboekItem');
	
	function admin_index(){
	    $this->set('title_for_layout', sprintf(__('Categories', true)));
	    
	    $categories = $this->paginate('PlakboekCategory');
	    
	    $this->set(compact('categories'));
	}
	
	function admin_add(){
	    $this->set('title_for_layout', sprintf(__('Add a Category', true)));
	    
	    if(!empty($this->data)){
	        $this->data['PlakboekCategory']['user_id'] = $this->Session->read('Auth.User.id');
	        
	        $this->PlakboekCategory->create();
			if($this->PlakboekCategory->save($this->data)){
				$this->Session->setFlash(sprintf(__('%s has been saved', true), $this->data['PlakboekCategory']['title']));
                $this->redirect(array('controller' => 'plakboek_categories', 'action' => 'index'));
			}
			else {
				$this->Session->setFlash(sprintf(__('%s could not be saved. Please, try again.', true), $this->data['PlakboekType']['type']));
			}
	    }
	}
	
	function admin_edit($id = null){
	    if(!$id){
			$this->Session->setFlash(__('Invalid content', true));
			$this->redirect(array('action' => 'index'));
		}
		
		$category = $this->PlakboekCategory->findById($id);
		
		if(!$category) {
			$this->Session->setFlash(__('The category does not exist.', true));
            $this->redirect(array('action' => 'index'));
		}
		
		$this->set('title_for_layout', sprintf(__('Edit Category', true)));
		
		if(!empty($this->data)){
		   $this->PlakboekCategory->id = $id;
		   
		   if($this->PlakboekCategory->save($this->data)){
               $this->Session->setFlash(sprintf(__('%s has been saved', true), $this->data['PlakboekCategory']['title']));
               $this->redirect(array('controller' => 'plakboek_categories', 'action' => 'index'));
           }
           else {
				$this->Session->setFlash(sprintf(__('%s could not be saved. Please, try again.', true), $this->data['PlakboekCategory']['title']));
           }
		}
		
		$this->data = $category;
	}
	
	function admin_delete($id = null){
	    if(!$id){
			$this->Session->setFlash(__('Invalid content', true));
			$this->redirect(array('action' => 'index'));
		}
		
		
		if($this->PlakboekCategory->delete($id)){
            $this->PlakboekItem->updateAll(array('category_id' => null), array('category_id' => $id));
			$this->Session->setFlash(__('Type deleted', true), 'default', array('class' => 'success'));
            $this->redirect(array('action' => 'index'));
		}
		else {
			$this->Session->setFlash(__('Failed to remove the type', true), 'default', array('class' => 'error'));
            $this->redirect(array('action' => 'index'));
		}
	}
}
?>