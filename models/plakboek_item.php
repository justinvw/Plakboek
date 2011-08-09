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
	    ),
	    'PlakboekThumbnail' => array(
			'className' => 'Plakboek.PlakboekPicture',
			'foreignKey' => 'thumbnail_picture_id',
			'dependent' => false
		)
	);
	
	function itemCountPerYearPerMonth(){
	    $item_count = $this->query("SELECT YEAR(`date_published`) AS `year`, MONTH(`date_published`) AS `month`, COUNT(*) as `count` FROM `plakboek_items` AS `PlakboekItem` GROUP BY YEAR(`date_published`), MONTH(`date_published`) ORDER BY YEAR(`date_published`), MONTH(`date_published`)");
	    
	    if($item_count[0][0]['year']){
	        $first_item_count = $item_count[0][0];
	    }
	    else{
	        $first_item_count = $item_count[1][0];
	    }
	    	    
	    end($item_count);
	    $last_item_count = $item_count[key($item_count)][0];
	    reset($item_count);
	    
	    $item_count_per_year_month = array();
	    foreach($item_count as $item){
	        if($item[0]['year']){
	            $item_count_per_year_month[$item[0]['year'].'-'.$item[0]['month'].'-01'] = $item[0]['count'];
	        }
	    }
	    
	    $date_start = strtotime('2007-01-01');
	    $date_end = strtotime($last_item_count['year'].'-'.$last_item_count['month'].'-01');
	    
	    while($date_start <= $date_end){
	        $current = date('Y-m-d', $date_start);
	        if(!array_key_exists($current, $item_count_per_year_month)){
	            $item_count_per_year_month[$current] = 0;
	        }
	        
	        $date_start = mktime(0, 0, 0, date('m', $date_start) + 1, date('d', $date_start), date('Y', $date_start));
	    }
	    
	    ksort($item_count_per_year_month);
	    
	    return $item_count_per_year_month;
	}
	
	function itemCountPerYearPerMonthConditional($conditions = array()){
	    if(array_key_exists('types', $conditions)){
	        $types = $this->PlakboekItemsType->find('list', array(
                'fields' => array('item_id'),
                'conditions' => array('PlakboekItemsType.type_id' => $conditions['types'])
            ));
            
            if(!$types){
                return array();
            }
	    }

	    $applicable_conditions = array();
	    if(isset($types)){
	        $applicable_conditions['PlakboekItem.id'] = $types;
	    }
	    
	    if(array_key_exists('start_date', $conditions)){
	        $applicable_conditions['PlakboekItem.date_published <='] = $conditions['start_date'];
	    }
	    
	    $this->unbindModel(array(
				'hasMany' => array('PlakboekPicture'),
				'belongsTo' => array('PlakboekThumbnail', 'PlakboekCategory'),
			), true
		);
        
	    $item_count = $this->find('all', array(
	        'fields' => array('YEAR(PlakboekItem.date_published) AS `year`', 'MONTH(PlakboekItem.date_published) AS `month`', 'COUNT(id) AS `count`'),
	        'conditions' => $applicable_conditions,
	        'group' => 'YEAR(PlakboekItem.date_published), MONTH(PlakboekItem.date_published)',
	        'order' => 'PlakboekItem.date_published'
	    ));
	    
	    if(count($item_count) > 0){
	        if($item_count[0][0]['year']){
    	        $first_item_count = $item_count[0][0];
    	    }
    	    else{
    	        $first_item_count = $item_count[1][0];
    	    }
	    
    	    end($item_count);
    	    $last_item_count = $item_count[key($item_count)][0];
    	    reset($item_count);
	    	
	    	$item_count_per_year_month = array();
    	    foreach($item_count as $item){
    	        if($item[0]['year']){
    	            $item_count_per_year_month[$item[0]['year'].'-'.$item[0]['month'].'-01'] = $item[0]['count'];
    	        }
    	    }
	    		    	
    	    return array(
    	        'first' => $first_item_count,
    	        'last' => $last_item_count,
    	        'overview' => array_reverse($item_count_per_year_month, true)
    	    );
    	}
    	else {
    	    return array();
    	}
	}
}
?>