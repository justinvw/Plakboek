<?php
class PlakboekTypesController extends PlakboekAppController {
	var $name = 'PlakboekTypes';
	var $uses = array('Plakboek.PlakboekType', 'Plakboek.PlakboekItem');
	
	function admin_index(){
	    $this->set('title_for_layout', sprintf(__('Types', true)));
	    
	    $types = $this->paginate('PlakboekType');
	    
	    $this->set(compact('types'));
	}
		
	function admin_add(){
	    $this->set('title_for_layout', sprintf(__('Add Type', true)));
	    
	    if(!empty($this->data)){
	        $this->data['PlakboekType']['user_id'] = $this->Session->read('Auth.User.id');
	        
	        $this->PlakboekType->create();
			if($this->PlakboekType->save($this->data)){
				$this->Session->setFlash(sprintf(__('%s has been saved', true), $this->data['PlakboekType']['type']));
                $this->redirect(array('controller' => 'plakboek_types', 'action' => 'index'));
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
		
		$type = $this->PlakboekType->findById($id);
		
		if(!$type) {
			$this->Session->setFlash(__('The type does not exist.', true));
            $this->redirect(array('action' => 'index'));
		}
		
		$this->set('title_for_layout', sprintf(__('Edit Type', true)));
		
		if(!empty($this->data)){
		   $this->PlakboekType->id = $id;
		   
		   if($this->PlakboekType->save($this->data)){
               $this->Session->setFlash(sprintf(__('%s has been saved', true), $this->data['PlakboekType']['type']));
               $this->redirect(array('controller' => 'plakboek_types', 'action' => 'index'));
           }
           else {
				$this->Session->setFlash(sprintf(__('%s could not be saved. Please, try again.', true), $this->data['PlakboekType']['type']));
           }
		}
		
		$this->data = $type;
	}
	
	function admin_delete($id = null){
	    if(!$id){
			$this->Session->setFlash(__('Invalid content', true));
			$this->redirect(array('action' => 'index'));
		}
		
		$type = $this->PlakboekType->findById($id);
		
		if($this->PlakboekType->delete($id)){
		    $this->PlakboekItem->PlakboekItemsType->deleteAll(array('PlakboekItemsType.type_id' => $id), false);
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