<?php
class PlakboekType extends PlakboekAppModel {
	var $name = 'PlakboekType';
	var $useTable = 'plakboek_types';
	
	var $validate = array(
		'type' => array(
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