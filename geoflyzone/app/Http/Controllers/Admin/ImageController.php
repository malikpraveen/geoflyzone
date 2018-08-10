<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Admin\Product;
use App\Model\Admin\AttributeProduct;
use App\Model\Admin\ImageProduct;
use Image;
use DB;


//Illuminate\Http\Request, instance of Illuminate\Http\UploadedFile

class ImageController extends Controller
{
    protected $atrProAtrValtable = 'attribute_product_attribute_values';
    protected $atrValtable = 'attribute_values';
    protected $atrtable = 'attributes';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //return view('admin.images.list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //return view('admin.images.create');
    }

    public function createImage($id)
    {   
        $product_id = $id;
        $attributes = Product::find($id)->attributes;
        foreach($attributes as $attribute){
            $details[] = DB::table('attribute_product_attribute_values')
            ->join('attribute_values', 'attribute_product_attribute_values.attribute_value_id', '=', 'attribute_values.id')
            ->join('attributes', 'attribute_values.attribute_id', '=', 'attributes.id')
            ->select('attributes.id as attribute_id', 'attributes.name', 'attribute_values.id as attribute_value_id', 'attribute_values.value')
            ->where('attribute_product_attribute_values.attribute_product_id', $attribute->id)
            ->get();
        }
        //return $details;
        //$attributeValues = AttributeProduct::find($id)->attributes;
        return view('admin.images.create', compact('details', 'product_id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function postResizeImage($imagesname, $max, $min)
    {
        $photo = $imagesname;
        $photo2 = $imagesname;
        $photo3 = $imagesname;
        $photo4 = $imagesname;
        
        $imagename = time().rand(1000,9999).'.'.$photo->getClientOriginalExtension(); 
       
       $imagename2 = time().rand(1000,9999).'50'.'.'.$photo2->getClientOriginalExtension();
       $imagename3 = time().rand(1000,9999).'200'.'.'.$photo3->getClientOriginalExtension();
       $imagename4 = time().rand(1000,9999).'300'.'.'.$photo4->getClientOriginalExtension(); 
       
        
        $destinationPath = public_path('/thumbnail_images');
        
        $thumb_img2 = Image::make($photo2->getRealPath())->resize(50, 50);
        $thumb_img3 = Image::make($photo3->getRealPath())->resize(150, 150);
        $thumb_img4 = Image::make($photo4->getRealPath())->resize(300, 300);


        
        $thumb_img2->save($destinationPath.'/'.$imagename2,80);
        $thumb_img3->save($destinationPath.'/'.$imagename3,80);
        $thumb_img4->save($destinationPath.'/'.$imagename4,80);
                    
        $destinationPath = public_path('/normal_images');

        $photo->move($destinationPath, $imagename);
        return array(
            'original_image'        =>$imagename, 
            'product_image'         =>$imagename4, 
            'product_image_thumb'   =>$imagename3, 
            'product_image_small'   =>$imagename2
        );
    }


    public function store(Request $request)
    {
        $arrValue = $request->all();

        /*$rules = array(
            'markmain_0' => 'required_without_all:markmain_1,markmain_2,markmain_3',
            'markmain_1' => 'required_without_all:markmain_0,markmain_2,markmain_3',
            'markmain_2' => 'required_without_all:markmain_0,markmain_1,markmain_3',
            'markmain_3' => 'required_without_all:markmain_0,markmain_1,markmain_2',
        );*/

        $this->validate($request, [
            'attr'=>'required',
            'attrval'=>'required',
            'firstProImg'=>'required|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            //$rules,
        ]);

        if($request->hasFile('firstProImg')){
            $fourthProImgs[] = $this->postResizeImage($request->firstProImg, '50', '50');            
        }

        if($request->hasFile('secondProImg')){
            $fourthProImgs[] = $this->postResizeImage($request->secondProImg, '50', '50');
        }

        if($request->hasFile('thirdProImg')){
            $fourthProImgs[] =  $this->postResizeImage($request->thirdProImg, '50', '50');
        }

        if($request->hasFile('fourthProImg')){
            $fourthProImgs[] = $this->postResizeImage($request->fourthProImg, '50', '50');
        }
        foreach ($fourthProImgs as $key=>$prodImg) {
            //echo $key;
            

            if(!isset($arrValue['markmain_'.$key]))
                $arrValue['markmain_'.$key] = 0;

          $proImgDetails = array(
            'product_id' => $arrValue['product_id'],
            'attribute_id' => $arrValue['attr'],
            'attribute_value_id' => $arrValue['attrval'],
            'original_image' => $prodImg['original_image'],
            'product_image' => $prodImg['product_image'],
            'product_image_thumb' => $prodImg['product_image_thumb'],
            'product_image_small' => $prodImg['product_image_small'],
            'mark_as_main'  => $arrValue['markmain_'.$key],
          );
          $imageDetails = ImageProduct::saveImgProdDetails($proImgDetails);
        }
        //return $proImgDetails;
        return redirect(route('image.show', $arrValue['product_id']));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product_id = $id;
        $imgDetails = DB::table('image_products')
            ->join('attributes', 'image_products.attribute_id', '=', 'attributes.id')
            ->join('attribute_values', 'image_products.attribute_value_id', '=', 'attribute_values.id')
            ->select('image_products.product_image_small', 'attributes.name', 'attribute_values.value')
            ->where('product_id', $product_id)
            ->where('mark_as_main', 1)
            ->get();
        return view('admin.images.list', compact('imgDetails', 'product_id'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
