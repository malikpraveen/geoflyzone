<?php

namespace App\Model\Admin;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $fillable = [
        'product_id',
        'attribute_combination_id',
        'attribute_combination_count',
        'product_price',
        'product_quantity',
        'inventory_purchased'
    ];


    public static function saveAttrCombRelations($arrAttrCombRelations) {
        //return $arrAttrCombRelations;
    	$arrAttrCombRelations = self::create($arrAttrCombRelations);
    }


}
