<?php
class PlakboekItemsController extends PlakboekAppController {
    var $name = 'PlakboekItems';
	var $uses = array('Plakboek.PlakboekItem');
    
    function index(){
        $types = $this->PlakboekItem->PlakboekType->find('list', array('fields' => 'type'));
        $item_count = $this->PlakboekItem->itemCountPerYearPerMonth();
        
        $this->set(compact('types', 'item_count'));
    }
    
    function index_items(){
        $rows_per_page = 5;
                
        if(!array_key_exists('start_date', $this->params['url'])){
            $this->params['url']['start_date'] = date('Y-m-d');
        }
        else {
            $this->params['url']['start_date']= substr($this->params['url']['start_date'], 0, -2).'31';
        }
        $this->set('start_date', $this->params['url']['start_date']);
        
        if(!array_key_exists('position', $this->params['url'])){
            $this->params['url']['position'] = $this->params['url']['start_date'];
        }
        $this->set('position', $this->params['url']['position']);
                
        if(!array_key_exists('types', $this->params['url'])){
            $this->params['url']['types'] = array();
        }
        
        if(!array_key_exists('query', $this->params['url'])){
            $this->params['url']['query'] = '';
        }
        
        if($this->params['url']['types']){
            $available_items = $this->PlakboekItem->itemCountPerYearPerMonthConditional(array(
                'types' => $this->params['url']['types'], 
                'start_date' => $this->params['url']['start_date'],
                'query'=> array('OR' => array(
                    'title LIKE' => '%'.$this->params['url']['query'].'%',
                    'excerpt LIKE' => '%'.$this->params['url']['query'].'%',
                    'description LIKE' => '%'.$this->params['url']['query'].'%',
                ))
            ));
        }
        else{
            $available_items = $this->PlakboekItem->itemCountPerYearPerMonthConditional(array(
                'start_date' => $this->params['url']['start_date'],
                'query'=> array('OR' => array(
                    'title LIKE' => '%'.$this->params['url']['query'].'%',
                    'excerpt LIKE' => '%'.$this->params['url']['query'].'%',
                    'description LIKE' => '%'.$this->params['url']['query'].'%',
                ))
            ));
        }
        
        if($available_items){
            $this->PlakboekItem->recursive = 2;
    		$this->PlakboekItem->unbindModel(array(
    			    'hasMany' => array('PlakboekPicture'),
    			), true
    		);
    		
    		if($this->params['url']['types']){
    		    $filtered_items = $this->PlakboekItem->PlakboekItemsType->find('list', array(
                    'fields' => array('item_id'),
                    'conditions' => array('PlakboekItemsType.type_id' => $this->params['url']['types'])
                ));
		    }
		    		    
    		if(count($available_items['overview']) > $rows_per_page){
    		    $available_dates = array_keys($available_items['overview']);
    		    $items_left = (count($available_dates) - 1) - array_search($this->params['url']['position'], $available_dates);
    		    
    		    if($items_left >= $rows_per_page){
    		        $last = $available_dates[array_search($this->params['url']['position'], $available_dates) + ($rows_per_page-1)];
    		    }
    		    else{
    		        $last = end($available_dates);
    		    }
                
    		    $date_restrictions = array($last, $this->params['url']['position']);
    		}
    		else {
    		    $date_restrictions = array($available_items['first']['year'].'-'.$available_items['first']['month'].'-01', $available_items['last']['year'].'-'.$available_items['last']['month'].'-01');
    		}

    		$date_restrictions[1] = substr($date_restrictions[1], 0, -2).'31';
    		
    		if(isset($filtered_items)){
                $items = $this->PlakboekItem->find('all', array(
                    'conditions' => array(
                        'PlakboekItem.date_published BETWEEN ? AND ?' => $date_restrictions,
                        'PlakboekItem.id' => $filtered_items,
                        'OR' => array(
                            'PlakboekItem.title LIKE' => '%'.$this->params['url']['query'].'%',
                            'PlakboekItem.excerpt LIKE' => '%'.$this->params['url']['query'].'%',
                            'PlakboekItem.description LIKE' => '%'.$this->params['url']['query'].'%',
                        ),
                    ),
                    'order' => array('PlakboekItem.date_published DESC')
                ));
            }
            else {
                $items = $this->PlakboekItem->find('all', array(
                    'conditions' => array(
                        'PlakboekItem.date_published BETWEEN ? AND ?' => $date_restrictions,
                        'OR' => array(
                            'PlakboekItem.title LIKE' => '%'.$this->params['url']['query'].'%',
                            'PlakboekItem.excerpt LIKE' => '%'.$this->params['url']['query'].'%',
                            'PlakboekItem.description LIKE' => '%'.$this->params['url']['query'].'%',
                        ),
                    ),
                    'order' => array('PlakboekItem.date_published DESC')
                ));
            }
            
            $items_by_year_month = array();
            foreach($items as $itemKey => $item) {
    			$items[$itemKey]['PlakboekThumbnail']['PlakboekFile'] = Set::combine($item['PlakboekThumbnail']['PlakboekFile'], '{n}.thumbname', '{n}');
    			$items_by_year_month[date('Y-m', strtotime($item['PlakboekItem']['date_published'])).'-01'][$itemKey] = $items[$itemKey];
    		}
    		
    		$items = $items_by_year_month;
        }
        else {
            $available_items = array();
            $items = array();
        }
        
        $this->set(compact('items', 'available_items', 'rows_per_page'));
    }
    
    function view(){
        $this->PlakboekItem->unbindModel(array('belongsTo' => array('PlakboekThumbnail')));
        $this->PlakboekItem->recursive = 2;
        $item = $this->PlakboekItem->findBySlug($this->params['slug']);
        foreach($item['PlakboekPicture'] as $pictureKey => $picture){
            $item['PlakboekPicture'][$pictureKey]['PlakboekFile'] = Set::combine($picture['PlakboekFile'], '{n}.thumbname', '{n}');
        }
		
        if($this->RequestHandler->isAjax()) {
            $this->set(compact('item'));
            $this->render('a_view');
		}
		else {
		    if(!$item){
		        $this->Session->setFlash(__('Invalid content', true));
			    $this->redirect(array('controller' => 'plakboek_items', 'action' => 'index'));
			}
			
			$this->set(compact('item'));
		}
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