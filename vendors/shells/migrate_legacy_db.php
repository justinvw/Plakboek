<?php
class MigrateLegacyDbShell extends Shell {
    var $uses = array(
        'Plakboek.PlakboekItem',
        'Plakboek.PlakboekCategory',
        'Plakboek.PlakboekType',
        'Plakboek.PlakboekPicture'
    );
    
    function main(){
        # Load the Kieken settings
    	if(file_exists(APP.'plugins'.DS.'plakboek'.DS.'config'.DS.'settings.yml')){
    		 $settings = Spyc::YAMLLoad(file_get_contents(APP.'plugins'.DS.'plakboek'.DS.'config'.DS.'settings.yml'));
    	}

    	foreach($settings AS $settingKey => $settingValue){
    		Configure::write($settingKey, $settingValue);
    	}
        
        $db_host = '';
        $db_user = '';
        $db_pass = '';
        $db_name = '';
        $db_connect = mysql_connect($db_host, $db_user, $db_pass);
        mysql_select_db($db_name, $db_connect);
        mysql_query("SET NAMES 'utf8'", $db_connect);
        
        $query = mysql_query("SELECT * FROM categories", $db_connect);
       
       while($row = mysql_fetch_array($query)){
           print_r($row);
           
           $data = array(
             'type' => $row['name'],
             'slug' => strtolower($row['name'])
           );
           
           $this->PlakboekType->create();
           $this->PlakboekType->save($data);
       }
       
       $query = mysql_query("SELECT * FROM groups", $db_connect);
       while($row = mysql_fetch_array($query)){
           print_r($row);
           
           $data = array(
             'title' => $row['name'],
             'slug' => str_replace(' ', '-', strtolower($row['name']))
           );
           
           $this->PlakboekCategory->create();
           $this->PlakboekCategory->save($data);
       }
       
       $used_slugs = array();
       
       $available_categories = array();
       foreach($this->PlakboekCategory->find('list', array('fields' => 'title')) as $category_id => $category){
           $available_categories[$category] = $category_id;
       }
       
       $available_types = array();
       foreach($this->PlakboekType->find('list', array('fields' => 'type')) as $type_id => $type){
           $available_types[$type] = $type_id;
       }
       
       $query = mysql_query("SELECT d.id, d.title, d.description, d.date, g.name FROM datas AS d JOIN groups AS g ON d.group_id = g.id", $db_connect);
       while($row = mysql_fetch_array($query)){
           $slug = str_replace(' ', '-', strtolower($row['title']));         
           if(array_key_exists($slug, $used_slugs)){
               $used_slugs[$slug] += 1;
               $slug = $slug.'-'.$used_slugs[$slug];
           }
           else {
               $used_slugs[$slug] = 1;
           }
           
           $item = array(
               'title' => $row['title'],
               'slug' => $slug,
               'excerpt' => $row['description'],
               'date_published' => $row['date'],
               'category_id' => $available_categories[$row['name']],
               'status' => 1
           );
           
           $this->PlakboekItem->create();
           $this->PlakboekItem->save($item);
           
           $types = array();
           $type_query = mysql_query("SELECT c.name FROM category_data AS cd JOIN categories as c ON cd.category_id = c.id WHERE cd.data_id = ".$row['id'], $db_connect);
           
           while($t = mysql_fetch_array($type_query)){
               $types[] = $available_types[$t['name']];
           }
           
           foreach($types as $type){
               $this->PlakboekItem->PlakboekItemsType->create();
               $this->PlakboekItem->PlakboekItemsType->save(array('type_id' => $type, 'item_id' => $this->PlakboekItem->id));
           }
           
           $datas_query = mysql_query("SELECT filepath FROM files WHERE data_id = ".$row['id'], $db_connect);
           while($d = mysql_fetch_array($datas_query)){
               print_r($d['filepath']);
               
               $source_path = "";
               $source_file = str_replace('', '', $d['filepath']);

               require_once(APP.'plugins'.DS.'plakboek'.DS.'vendors'.DS.'phpthumb'.DS.'ThumbLib.inc.php');

               $uploadInfo = pathinfo($source_file);
               $uploadInfo['uploadFilename'] = String::uuid().'.'.$uploadInfo['extension'];
               $uploadData = fopen($source_path.$source_file, "r");
               $storeUpload = fopen(WWW_ROOT.'plakboek'.DS.$uploadInfo['uploadFilename'], "w");
               while($data = fread($uploadData, 1024)){
       		       fwrite($storeUpload, $data);
       		   }
       		fclose($storeUpload);
       		fclose($uploadData);

       		$this->PlakboekPicture->create();
       		$this->data['PlakboekPicture']['item_id'] = $this->params['url']['item_id'];
       		#$this->data['PlakboekPicture']['user_id'] = $this->Session->read('Auth.User.id');
       		$this->PlakboekPicture->save(array('item_id' => $this->PlakboekItem->id));

       		// If there is no thumbnail picture assigned to the item, use this picture as thumbnail
       		$item = $this->PlakboekItem->find('first', array(
       			'conditions' => array(
       				'PlakboekItem.id' => $this->PlakboekItem->id,
       				'PlakboekItem.thumbnail_picture_id' => 0
       			)
       		));
       		if($item){
       			$this->PlakboekItem->id = $this->PlakboekItem->id;
       			$this->PlakboekItem->saveField('thumbnail_picture_id', $this->PlakboekPicture->id);
       		}

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
           }
       }
       
       print_r($used_slugs);
    }
    
}
?>