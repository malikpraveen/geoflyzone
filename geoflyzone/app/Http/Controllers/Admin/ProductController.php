<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\MOdel\Admin\Product;
use App\MOdel\Admin\Attribute;
use App\MOdel\Admin\Category;
use App\MOdel\Admin\CategoryProduct;
use App\MOdel\Admin\Brand;
use App\MOdel\Admin\AttributeValue;
use App\Model\Admin\AttributeProduct;
use App\Model\Admin\AttributeProductAttributeValue;
use App\Model\Admin\Inventory;
use DB;
class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::all();
        //print_r($products);
        return view('admin.products.list', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $attributes = Attribute::with('attributeValues')->get();
        $brands = Brand::all();
        $categories = Category::where('parent_id', 1)->get();
        $subCategories = Category::where('parent_id', '!=',1)
        ->where('parent_id', '!=',0)
        ->get();
        return view('admin.products.create', compact('brands', 'categories', 'attributes', 'subCategories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */


    /**
     * Very Important function
     *
     * This Function will find the all possible
     * combination of array values.
     */
    public function combinations($arrays, $i = 0) 
    {
        if (!isset($arrays[$i])) 
        {
            return array();
        }
        if ($i == count($arrays) - 1) 
        {
            return $arrays[$i];
        }
        // get combinations from subsequent arrays
        $tmp = $this->combinations($arrays, $i + 1);
        $result = array();
        // concat each array from tmp with each element from $arrays[$i]
        foreach ($arrays[$i] as $v) 
        {
            foreach ($tmp as $t) 
            {
                $result[] = is_array($t) ? 
                array_merge(array($v), $t) :
                array($v, $t);
            }
        }
        return $result;
    }


    public function store(Request $request)
    {
        $arrValue = $request->all();

        $this->validate($request, [
            'brand'             => 'required',
            'sku'               => 'required',
            'name'              => 'required',
            'description'       => 'required',
            'cover'             => 'required',
            'quantity'          => 'required',
            'price'             => 'required',
            'featured_product'  => 'required',
            'categories'        => 'required',
            'attr'              => 'required',
            //'attrval'           => 'required',
        ]);


        if($request->hasFile('cover')){
            $imageName = $request->cover->store('public/product/cover');
        }
        // echo $imageName;
        // die();
        $productDetails = array(
            'brand_id'              => $arrValue['brand'],
            'sku'                   => $arrValue['sku'],
            'name'                  => $arrValue['name'],
            'slug'                  => str_slug($request->input('name')),
            'description'           => $arrValue['description'],
            'cover'                 => $imageName,
            'quantity'              => $arrValue['quantity'],
            'price'                 => $arrValue['price'],
            'featured_product'      => $arrValue['featured_product'],
            'status'                => $arrValue['status'],
        );
        $lastInsertedProductId = Product::saveProductDetails($productDetails);


        /**
         * Creating Attribute value combination function.
         *
         * Taking last inserted product id.
         * Inserting combinations into 'inventories' table.
         */
        $param = [];
        if($arrValue['attr']){
            $attribute_ids = $arrValue['attr'];
            if(sizeof($attribute_ids)>0){
                $param['attribute_ids'] = implode(",",$attribute_ids);
            }
            $i=0;
            $attribute_value_id_array = array();
            foreach($attribute_ids as $attribute_id){
                if($arrValue['attribute_'.$attribute_id.'_value_ids']){
                    $attribute_values_array[$i] = $arrValue['attribute_'.$attribute_id.'_value_ids'];
                }
                if(sizeof($attribute_values_array[$i]) > 0){
                    foreach($attribute_values_array[$i] as $attribute_value_id){
                        array_push($attribute_value_id_array,$attribute_value_id);  
                    }
                }
                $i++;
            }
            if(sizeof($attribute_value_id_array) > 0){
                $param['attribute_value_ids'] = implode(",",$attribute_value_id_array); 
            }
        }

        $attribute_values_combinations = array();
        if(sizeof($attribute_values_array) > 0){
            $attribute_values_combinations = $this->combinations($attribute_values_array);
        }

        if(sizeof($attribute_values_combinations) > 0){
            foreach($attribute_values_combinations as $this_combination){
                if(is_array($this_combination) && sizeof($this_combination)>0){
                    $attribute_combination_id = implode(",", $this_combination);
                    if($attribute_combination_id != ""){
                        $attrCombDetails = array(
                            'product_id' => $lastInsertedProductId,
                            'attribute_combination_id' => "$attribute_combination_id",
                            'attribute_combination_count' => sizeof($this_combination),
                            
                        );
                        $proComb = Inventory::saveAttrCombRelations($attrCombDetails);             
                    }               
                }else if($this_combination != ""){
                    $attrCombDetails = array(
                        'product_id' => $lastInsertedProductId,
                        'attribute_combination_id' => $this_combination,
                        'attribute_combination_count' => 1,
                        
                    );
                    $proComb = Inventory::saveAttrCombRelations($attrCombDetails);                               
                }
            }
        }
        /**
         *Inserting combinations into 'inventories' table
         *end here.
         */


        $category_ids = $arrValue['categories'];
        $countCat = count($category_ids);
        if($countCat > 0){
          foreach ($category_ids as $category_id) {
            $catProdRelations = array(
                'product_id'    => $lastInsertedProductId,
                'category_id'   => $category_id,
            );
            $this->catProduct = CategoryProduct::saveCatProdRelations($catProdRelations);
          }
        }

        $attribute_ids = $arrValue['attr'];
        $countAttr = count($attribute_ids);
        if($countAttr > 0){
          foreach ($attribute_ids as $attribute_id) {
            $attrProdRelations = array(
                'product_id' => $lastInsertedProductId,
                'attribute_id' => $attribute_id,
            );
            $getAtrProIds[] = AttributeProduct::saveAttrProdRelations($attrProdRelations);
          }
        }

        $atr_ids = $arrValue['attr'];
        $attrval_ids=array();
        foreach($atr_ids as $atr_id){
            $attrval_ids = array_merge($attrval_ids,$arrValue['attribute_'.$atr_id.'_value_ids']);
        }
        if(count($getAtrProIds) > 0){
            $result[] = AttributeProductAttributeValue::saveAttrProdAttrValRelations($getAtrProIds, $attrval_ids);
        }
        return redirect(route('product.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::where('id', $id)->first();
        $attributes = Attribute::with('attributeValues')->get();
        $brands = Brand::all();
        $categories = Category::where('parent_id', 1)->get();
        $subCategories = Category::where('parent_id', '!=',1)
        ->where('parent_id', '!=',0)
        ->get();
        return view('admin.products.edit', compact('product','brands', 'categories', 'attributes', 'subCategories'));
    }



    public function editInventory(Request $request, $product_id)
    {
        $attribute_combinations = DB::table('inventories')
            ->where('product_id', $product_id)
            ->orderBy('id', 'DESC')
            ->get();
        $request_action = '';
        if($request->request_action){
            $request_action = $request->request_action; 
        }
        
        if($request_action == 'edit_product_inventory'){
            if(isset($attribute_combinations) && sizeof($attribute_combinations) > 0){
                foreach($attribute_combinations as $attr_comb_val){
                    $inventory_id = $attr_comb_val->id;

                    $addinventory = 'addinventory_'.$inventory_id;
                    $inventory_available = $request->$addinventory;
                    if(empty($inventory_available)){
                        $inventory_available = 0;
                    }

                    $subinventory = 'subinventory_'.$inventory_id;
                    $inventory_available_subtract = $request->$subinventory;
                    if(empty($inventory_available_subtract)){
                        $inventory_available_subtract = 0;
                    }

                    if(!empty($inventory_available)){
                        $combUpdQry = DB::table('inventories')
                            ->where('id', $inventory_id)
                            ->increment('product_quantity', $inventory_available);
                    }

                    if(!empty($inventory_available_subtract)){
                        $combUpdQry = DB::table('inventories')
                            ->where('id', $inventory_id)
                            ->decrement('product_quantity', $inventory_available_subtract);
                    }
                }
            }
        }
        /*$attribute_combinations = $this->manageInventory($product_id);*/
        $attr_comb_list = array();
        $attribute_combinations = DB::table('inventories')
            ->where('product_id', $product_id)
            ->orderBy('id', 'DESC')
            ->get();
        if(sizeof($attribute_combinations) > 0) {
            $i=0;
            foreach($attribute_combinations as $attribute_val){
                $attribute_combination_id = $attribute_val->attribute_combination_id;

                $attribute_combination_name = AttributeValue::display_attributes_combinations($attribute_combination_id);

                $attr_comb_list[$i]['attribute_combination_name'] = $attribute_combination_name;

                $attr_comb_list[$i]['id'] = $attribute_val->id;

                $attr_comb_list[$i]['product_id'] = $attribute_val->product_id;

                $attr_comb_list[$i]['attribute_combination_id'] = $attribute_val->attribute_combination_id;

                $attr_comb_list[$i]['attribute_combination_count'] = $attribute_val->attribute_combination_count;

                $attr_comb_list[$i]['product_price'] = $attribute_val->product_price;

                $attr_comb_list[$i]['product_quantity'] = $attribute_val->product_quantity;

                $attr_comb_list[$i]['inventory_purchased'] = $attribute_val->inventory_purchased;

                $i++;
            }
        }

        $product_data = Product::where('id', $product_id)->value('name');
        return view('admin.products.manage-inventory', compact('attr_comb_list','product_data', 'product_id'));
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }


    public function manageInventory($product_id)
    {
        $attribute_combinations = DB::table('inventories')
            ->where('product_id', $product_id)
            ->get();
        if(sizeof($attribute_combinations) > 0) {
            $i=0;
            foreach($attribute_combinations as $attribute_val){
                $attribute_combination_id = $attribute_val->attribute_combination_id;

                $attribute_combination_name = AttributeValue::display_attributes_combinations($attribute_combination_id);

                $attr_comb_list[$i]['attribute_combination_name'] = $attribute_combination_name;

                $attr_comb_list[$i]['id'] = $attribute_val->id;

                $attr_comb_list[$i]['product_id'] = $attribute_val->product_id;

                $attr_comb_list[$i]['attribute_combination_id'] = $attribute_val->attribute_combination_id;

                $attr_comb_list[$i]['attribute_combination_count'] = $attribute_val->attribute_combination_count;

                $attr_comb_list[$i]['product_price'] = $attribute_val->product_price;

                $attr_comb_list[$i]['product_quantity'] = $attribute_val->product_quantity;

                $attr_comb_list[$i]['inventory_purchased'] = $attribute_val->inventory_purchased;

                $i++;
            }
        }

        $product_data = Product::where('id', $product_id)->value('name');
        return view('admin.products.manage-inventory', compact('attr_comb_list','product_data', 'product_id'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
