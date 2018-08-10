<?php

namespace App\Model\Admin;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    public function products(){
    	return $this->belongsToMany('App\Model\Admin\Product', 'attribute_products');
    }

    public function attributeValues(){
    	return $this->hasMany('App\Model\Admin\AttributeValue');
    }
}
