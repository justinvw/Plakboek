<?php
class PlakboekItemsController extends PlakboekAppController {
    var $name = 'PlakboekItems';
	var $uses = array('Plakboek.PlakboekItem');
    
    function index(){
        $types = $this->PlakboekItem->PlakboekType->find('list', array('fields' => 'type'));
        $item_count = $this->PlakboekItem->itemCountPerYearPerMonth());
        
        $this->compact('types', 'item_count');
    }
    
    function admin_index(){
        $this->set('title_for_layout', sprintf(__('Items', true)));
	    
	    $items = $this->paginate('PlakboekItem');
	    
	    $this->set(compact('items'));
    }
    
    function admin_add(){
        $this->set('title_for_layout', sprintf(__('Add Item', true)));
        
        if(!empty($this->data)){
            $this->data['PlakboekItem']['user_id'] = $this->Session->read('Auth.User.id');
            $this->PlakboekItem->create();
			if($this->PlakboekItem->save($this->data)){
				$this->Session->setFlash(sprintf(__('%s has been saved, now add your pictures!', true), $this->data['PlakboekItem']['title']));
                $this->redirect(array('controller' => 'plakboek_items', 'action' => 'edit', $this->PlakboekItem->id));
			}
			else {
				$this->Session->setFlash(sprintf(__('%s could not be saved. Please, try again.', true), $this->data['PlakboekItem']['title']));
			}
        }
        
        $types = $this->PlakboekItem->PlakboekType->find('list', array('fields' => 'type'));
        $categories = $this->PlakboekItem->PlakboekCategory->find('list', array('fields' => 'title'));
        
        $this->set(compact('types', 'categories'));
    }
    
    function admin_edit($id = null){
        if(!$id){
			$this->Session->setFlash(__('Invalid content', true));
			$this->redirect(array('action' => 'index'));
		}
		
		$item = $this->PlakboekItem->findById($id);
		
		if(!$item) {
			$this->Session->setFlash(__('The item does not exist.', true));
            $this->redirect(array('action' => 'index'));
		}
		
		$this->set('title_for_layout', sprintf(__('Edit Item', true)));
		
		if(!empty($this->data)){
            $this->PlakboekItem->id = $id;
		    
            if($this->PlakboekItem->save($this->data)){
                $this->Session->setFlash(sprintf(__('%s has been saved', true), $this->data['PlakboekItem']['title']));
                $this->redirect(array('controller' => 'plakboek_items', 'action' => 'index'));
            }
            else {
			    $this->Session->setFlash(sprintf(__('%s could not be saved. Please, try again.', true), $this->data['PlakboekItem']['title']));
            }
		}
		
		$this->data = $item;
		
		$types = $this->PlakboekItem->PlakboekType->find('list', array('fields' => 'type'));
		$categories = $this->PlakboekItem->PlakboekCategory->find('list', array('fields' => 'title'));
        
		
		$this->set(compact('types', 'categories'));
    }
    
    function admin_delete($id = null){
        if(!$id){
			$this->Session->setFlash(__('Invalid content', true));
			$this->redirect(array('action' => 'index'));
		}
		
		$item = $this->PlakboekItem->findById($id);

		if(!$item) {
			$this->Session->setFlash(__('The item does not exist.', true));
            $this->redirect(array('action' => 'index'));
		}
        
        $pictures = $this->PlakboekItem->PlakboekPicture->find('all', array(
	        'conditions' => array('item_id' => $id)
	    ));
        
        if($this->PlakboekItem->delete($id, true)){
            foreach($pictures as $picture){
                foreach($picture['PlakboekFile'] as $imageFile){
        	            unlink(WWW_ROOT.Configure::read('Plakboek.uploadDirectory').$imageFile['filename']);
        	    }
            }
            
            $this->Session->setFlash(__('Tour deleted', true), 'default', array('class' => 'success'));
            $this->redirect(array('action' => 'index'));
        }
    }
}
?>