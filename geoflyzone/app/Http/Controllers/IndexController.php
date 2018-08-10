<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\User\Category;
use DB;
use session;
class IndexController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
        $categories = DB::table('categories')
            ->select('name','id as catid' )
            ->where('parent_id', 1)
            ->where('id', '!=', 1)
            ->get();
            //print_r($categories); die();
        $subCategories = DB::table('categories')
            ->select('id', 'name','parent_id')
            ->where('parent_id', '!=',1)
            ->where('parent_id', '!=',0)
            ->get();

        $brands = DB::table('brands')
            ->select('id', 'name')
            ->orderBy('id', 'DESC')
            ->get();

     $featured_pro = DB::table('products')
            ->select('cover','price')
            ->where('featured_product',1)
            ->get();
            //print_r($featured_pro); die();

           //  $size = DB::table('attribute_values')
           //  ->select('id as attribute_value_id', 'value')
           //  ->where('attribute_id', 2)
           // ->get();

           //  $color = DB::table('attribute_values')
           //  ->select('id as attribute_value_id', 'value')
           //  ->where('attribute_id', 1)
           // ->get();
             //print_r($color); die();
        // $productImg = DB::table('categories')
        //     ->select('id', 'name')
        //     ->where('id', $cat_id)
        //     -get();

        // print_r($productImg);
        // die();


            //print_r($images); die();
        /*foreach ($categories as $category) {
            $cate[] = $category->name;
        }*/
        //  return session_unset(SESSION([$categories]));
        //   return session_unset(SESSION([$subCategories]));
       
         // session()->put('color',$color);
         //  session()->put('size',$size);
        session()->put('featured_pro',$featured_pro);
        session()->put('categories',$categories);
        session()->put('subCategories',$subCategories);
        session()->put('brands',$brands);
       ;
    }
    
    public function  product($cate_id)
    {
        //return $cate_id;
        $productImg = DB::table('categories')
            ->join('category_products','category_products.category_id', '=','categories.id' )
            ->join('products', 'products.id', '=', 'category_products.product_id')


            ->select('categories.id as category_id', 'categories.name', 'category_products.product_id as product_id', 'products.name', 'products.price', 'products.cover')
            ->where('categories.id', $cate_id)
            ->get();
            


       
        return view('users.produt', compact('productImg'));
    }

      public function  productdetails($product_id)

    { 
      
          $productdata = DB::table('image_products')
          ->select('product_image_thumb','original_image','product_image','product_image_small','attribute_value_id','product_id')
          ->where( 'image_products.product_id',$product_id)
          //->where( 'mark_as_main',1)
          ->get();
              //print_r($productdata); die();

          

       $brand = DB::table('brands')
        ->join('products', 'products.brand_id', '=', 'brands.id')
       ->select('brands.name as br_name','products.sku','products.price','products.name','products.description')
        ->where( 'products.id',$product_id)
       ->get();
       //print_r($brand); die();
         

         
         
         $size = DB::table('attribute_values')
        
        
         ->join('attribute_product_attribute_values','attribute_product_attribute_values.attribute_value_id','=','attribute_values.id')

          ->join('attribute_products','attribute_products.id','=','attribute_product_attribute_values.attribute_product_id')
         
         ->select('value','attribute_values.attribute_id')
        
         ->where('attribute_products.product_id','=',$product_id)
        ->get();

       // print_r($size); die();

   
        return view('users.product-details', compact('productdata','brand','size'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$categories = Category::where('parent_id', 1)->get();
        
        //session()->put('user',$categories1);
        return view('users.index');
    }

  

    public function myAccount()
    {
        return view('users.my-account');
    }

     public function changepassword()
    {
        return view('users.change-password');
    }
   
      public function  checkoutconfirm()
    {
        return view('users.checkout-confirm');
    }

     public function  checkoutpayment()
    {
        return view('users.checkout-payment');
    }

     public function  checkoutshipping()
    {
        return view('users.checkout-shipping');
    }

    public function  checkoutthankyou()
    {
        return view('users.checkout-thank-you');
    }

     public function  helpsupport()
    {
        return view('users.help-support');
    }

    public function  myaccountsetting()
    {
        return view('users.my-account-setting');
    }

    public function  mycart()
    {
        return view('users.my-cart');
    }

    public function  myorders()
    {
        return view('users.my-orders');
    }

     public function  myprofile()
    {
        return view('users.my-profile');
    }

    public function  mywishlist()
    {
        return view('users.my-wishlist');
    }

     public function  orderstatus()
    {
        return view('users.order-status');
    }

    public function  ordersummary()
    {
        return view('users.order-summary');
    }





    public function aboutus(){
        return view('users.aboutus');
    }

    public function contactus(){
        return view('users.contactus');
    }

     public function careeratgeoflyzone(){
        return view('users.career-at-geoflyzone');
    }

     public function inthemedia(){
        return view('users.in-the-media');
    }

     public function blog(){
        return view('users.blog');
    }

     public function privacypolicy(){
        return view('users.privacy-policy');
    }

    public function termsconditions(){
        return view('users.terms-conditions');
    }

     public function returnrefundpolicy(){
        return view('users.return-refund-policy');
    }

     public function disclaimer(){
        return view('users.disclaimer');
    }

      public function faqs(){
        return view('users.faqs');
    }

     public function howitwork(){
        return view('users.how-it-work');
    }

    public function discountcouponspoints(){
        return view('users.discount-coupons-points');
    }

     public function howdoipay(){
        return view('users.how-do-i-pay');
    }

    public function listareacovered(){
        return view('users.list-area-covered');
    }

    public function orderscancellation(){
        return view('users.orders-cancellation');
    }

      public function manageaddress(){
        return view('users.manage-address');
    }

     public function paymentbillingsetting(){
        return view('users.payment-billing-setting');
    }

     public function customerservice(){
        return view('users.customer-service');
    }

    public function shippingdelivery(){
        return view('users.shipping-delivery');
    }
}
