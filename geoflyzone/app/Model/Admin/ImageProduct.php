<?php

namespace App\Model\Admin;

use Illuminate\Database\Eloquent\Model;

class ImageProduct extends Model
{
    protected $fillable = [
    	'product_id',
        'attribute_id',
        'attribute_value_id',
        'original_image',
        'product_image',
        'product_image_thumb',
        'product_image_small',
        'mark_as_main'
    ];

    public static function saveImgProdDetails($arrImgProdDetail) {
    	//return $arrImgProdDetail;
    	$arrImgProdDetail = self::create($arrImgProdDetail);
    }
}
