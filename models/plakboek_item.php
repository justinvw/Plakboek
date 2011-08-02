<?php
class PlakboekItem extends PlakboekAppModel {
    var $name = 'PlakboekItem';
	var $useTable = 'plakboek_items';
	
	var $validate = array(
		'title' => array(
            'rule' => 'notEmpty',
            'message' => 'This field cannot be left blank.',
        ),
		'slug' => array(
            'isUnique' => array(
                'rule' => 'isUnique',
                'message' => 'This slug has already been taken.',
            ),
            'minLength' => array(
                'rule' => array('minLength', 1),
                'message' => 'Slug cannot be empty.',
            ),
        )
	);
	
	var $hasAndBelongsToMany = array(
		'PlakboekType' => array(
			'className' => 'Plakboek.PlakboekType',
			'joinTable' => 'plakboek_items_types',
			'foreignKey' => 'item_id',
			'associationForeignKey' => 'type_id',
			'dependent' => true
		)
	);
	
	var $hasMany = array(
		'PlakboekPicture' => array(
			'className' => 'Plakboek.PlakboekPicture',
			'foreignKey' => 'item_id',
            'dependent' => true
		)
	);
	
	var $belongsTo = array(
	    'PlakboekCategory' => array(
	        'className' => 'Plakboek.PlakboekCategory',
	        'foreignKey' => 'category_id'
	    )
	);
}
?>