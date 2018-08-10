<?php

namespace App\Model\Admin;

use Illuminate\Database\Eloquent\Model;
use DB;

class AttributeProductAttributeValue extends Model
{
    protected $atrProdtable = 'attribute_products';
    protected $atrValtable = 'attribute_values';


    protected $fillable = [
        'attribute_product_id',
        'attribute_value_id',
        'attribute_id',
    ];

    public static function saveAttrProdAttrValRelations($getAtrProIds, $attrval_ids) {
        //return $attrval_ids;
        $details = DB::table('attribute_products')
            ->join('attribute_values', 'attribute_products.attribute_id', '=', 'attribute_values.attribute_id')
            ->select('attribute_values.attribute_id', 'attribute_values.id as attribute_value_id', 'attribute_products.id as attribute_product_id')
            ->whereIn('attribute_products.id', $getAtrProIds)
            ->whereIn('attribute_values.id', $attrval_ids)
            ->get();

        $prodRelWithAttrVal = json_decode(json_encode($details),true);

        $saveProdRelWithAttrVal = self::insert($prodRelWithAttrVal);
    }
}
