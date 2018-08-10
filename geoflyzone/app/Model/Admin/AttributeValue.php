<?php

namespace App\Model\Admin;

use Illuminate\Database\Eloquent\Model;
use DB;

class AttributeValue extends Model
{

    public function attributes(){
    	return $this->belongsTo('App\Model\Admin\Attribute');
    }

    public function attributeProducts(){
    	return $this->belongsToMany('App\Model\Admin\AttributeProduct', 'attribute_product_attribute_values');
    }
    
    public static function display_attributes_combinations($comma_seperated_combinations){
    	$result = DB::table('attribute_values')
            ->select('value')
            ->whereIn('id', explode(',',
            	$comma_seperated_combinations))
            ->get();
        if(sizeof($result) > 0){
			$return_string = "";
			foreach($result as $val){
				$return_string .= $val->value.", ";	
			}
			if($return_string != ''){
				$return_string = substr($return_string, 0, -2);
			}
			return $return_string;
		}else{
			return "";			
		}
    }
}
