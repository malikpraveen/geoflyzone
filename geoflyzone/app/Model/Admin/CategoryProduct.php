<?php

namespace App\Model\Admin;

use Illuminate\Database\Eloquent\Model;

class CategoryProduct extends Model
{
	protected $fillable = [
		'product_id',
		'category_id'
	];


    public static function saveCatProdRelations($arrCatProdRelations) {
    	$arrCatProdRelations = self::create($arrCatProdRelations);
    }
}
