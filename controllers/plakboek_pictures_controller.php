<?php
class PlakboekPicturesController extends PlakboekAppController {
    var $name = 'PlakboekPictures';
	var $uses = array('Plakboek.PlakboekPicture');
	
	function admin_index($item_id = null){
	    $pictures = $this->PlakboekPicture->find('all', array(
	        'conditions' => array('item_id' => $item_id)
	    ));
	    
	    if($pictures){
			foreach($pictures as $pictureKey => $picture) {
				$pictures[$pictureKey]['PlakboekFile'] = Set::combine($picture['PlakboekFile'], '{n}.thumbname', '{n}');
			}
		}
	    
	    $this->set(compact('pictures'));
	}
	
	function admin_upload(){
	    if(!$this->RequestHandler->isAjax()) {
			$this->redirect(array('controller' => 'plakboek_items', 'action' => 'index'));
		}
	    
	    require_once(APP.'plugins'.DS.'plakboek'.DS.'vendors'.DS.'phpthumb'.DS.'ThumbLib.inc.php');
	    
	    Configure::write('debug', 0);
	    $this->autoRender = false;
	    	    
	    if(!is_writable(WWW_ROOT.Configure::read('Plakboek.uploadDirectory'))) {
			echo json_encode(array('success' => false, 'error' => 'The upload directory is not writeable'));
			exit();
		}
		
		$uploadInfo = pathinfo($this->params['url']['qqfile']);
		$uploadInfo['uploadFilename'] = String::uuid().'.'.$uploadInfo['extension'];
		$uploadData = fopen("php://input", "r");
		
		$storeUpload = fopen(WWW_ROOT.Configure::read('Plakboek.uploadDirectory').$uploadInfo['uploadFilename'], "w");
		while($data = fread($uploadData, 1024)){
			fwrite($storeUpload, $data);
		}
		fclose($storeUpload);
		fclose($uploadData);
		
		$this->PlakboekPicture->create();
		$this->data['PlakboekPicture']['item_id'] = $this->params['url']['item_id'];
		$this->data['PlakboekPicture']['user_id'] = $this->Session->read('Auth.User.id');
		$this->PlakboekPicture->save($this->data);
		
		foreach(Configure::read('Plakboek.thumbnails') as $thumbnail){
		    $thumbnailFilename = String::uuid().'.'.$uploadInfo['extension'];
		    $manipulateImage = PhpThumbFactory::create(WWW_ROOT.Configure::read('Plakboek.uploadDirectory').$uploadInfo['uploadFilename']);
		    
		    if($thumbnail['resizeMethod'] == 'normal') {
				$manipulateImage->resize($thumbnail['width'], $thumbnail['height']);
			}
			elseif($thumbnail['resizeMethod'] == 'adaptive') {
				$manipulateImage->adaptiveResize($thumbnail['width'], $thumbnail['height']);
			}
			$newDimensions = $manipulateImage->getNewDimensions();
			$manipulateImage->save(WWW_ROOT.Configure::read('Plakboek.uploadDirectory').$thumbnailFilename);
			unset($this->data);
			
			$this->PlakboekPicture->PlakboekFile->create();
			$this->data['PlakboekFile']['picture_id'] = $this->PlakboekPicture->id;
			$this->data['PlakboekFile']['filename'] = $thumbnailFilename;
			$this->data['PlakboekFile']['width'] = $newDimensions['newWidth'];
			$this->data['PlakboekFile']['height'] = $newDimensions['newHeight'];
			$this->data['PlakboekFile']['thumbname'] = $thumbnail['thumbName'];
			$this->PlakboekPicture->PlakboekFile->save($this->data);
		}
		
		// If the original file must be kept, create a DB entry for it, else delete the file
		if(Configure::read('Plakboek.keepOriginal') == 1) {
			$originalImage = PhpThumbFactory::create(WWW_ROOT.Configure::read('Plakboek.uploadDirectory').$uploadInfo['uploadFilename']);
			$dimensions = $originalImage->getCurrentDimensions();

			unset($this->data);
			$this->PlakboekPicture->PlakboekFile->create();
			$this->data['PlakboekFile']['picture_id'] = $this->PlakboekPicture->id;
			$this->data['PlakboekFile']['filename'] = $uploadInfo['uploadFilename'];
			$this->data['PlakboekFile']['width'] = $dimensions['width'];
			$this->data['PlakboekFile']['height'] = $dimensions['height'];
			$this->data['PlakboekFile']['thumbname'] = 'original';
			$this->PlakboekPicture->PlakboekFile->save($this->data);
		}
		else {
			unlink(WWW_ROOT.Configure::read('Plakboek.uploadDirectory').$uploadInfo['uploadFilename']);
		}
		
		echo json_encode(array('success' => true));
	}
	
	function admin_delete($id){
	    Configure::write('debug', 0);
		$this->autoRender = false;
	    
	    $picture = $this->PlakboekPicture->findById($id);
	    
	    if($this->PlakboekPicture->delete($id, true)){
	        foreach($picture['PlakboekFile'] as $imageFile){
	            unlink(WWW_ROOT.Configure::read('Plakboek.uploadDirectory').$imageFile['filename']);
	        }
	    }
	}
}
?>