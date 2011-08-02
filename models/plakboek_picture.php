<?php
class PlakboekPicture extends PlakboekAppModel {
    var $name = 'PlakboekPicture';
	var $useTable = 'plakboek_pictures';
	
	var $hasMany = array(
		'PlakboekFile' => array(
			'className' => 'Plakboek.PlakboekFile',
			'foreignKey' => 'picture_id',
            'dependent' => true
		)
	);
}
?>