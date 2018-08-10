<?php

namespace App\Model\Admin;

use Illuminate\Database\Eloquent\Model;

class AttributeProduct extends Model
{
	protected $fillable = [
		'product_id',
		'attribute_id'
	];


    public function attributeValues(){
    	return $this->belongsToMany('App\Model\Admin\AttributeValue', 'attribute_product_attribute_values');
    }

    public static function saveAttrProdRelations($arrAttrProdRelations) {
    	//return $arrAttrProdRelations;
    	$arrAttrProdRelations = self::create($arrAttrProdRelations);
    	return $arrAttrProdRelations->id;
    }
}
