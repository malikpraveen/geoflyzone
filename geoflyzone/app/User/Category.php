<?php

namespace App\User;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public function products(){
    	return $this->belongsToMany('App\Model\Admin\Product', 'category_products');
    }
}
