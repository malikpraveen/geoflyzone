<?php

namespace App\Model\Admin;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
	protected $fillable = [
        'brand_id',
        'sku',
        'name',
        'slug',
        'description',
        'cover',
        'quantity',
        'price',
        'status',
        'featured_product'
    ];

    public function categories(){
    	return $this->belongsToMany('App\Model\Admin\Category', 'category_products');
    }
    
    public function attributes(){
    	return $this->belongsToMany('App\Model\Admin\Attribute', 'attribute_products');
    }

    public static function saveProductDetails($arrProductDetail) {
    	$arrProductDetail = self::create($arrProductDetail);
    	return $arrProductDetail->id;
    }
}
