<?php
class PlakboekCategory extends PlakboekAppModel {
	var $name = 'PlakboekCategory';
	var $useTable = 'plakboek_categories';
	
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
}
?>