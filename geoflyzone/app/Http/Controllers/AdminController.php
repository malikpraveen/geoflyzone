<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.index');
    }

    public function login()
    {
        return view('admin.login');
    }

     public function advance()
    {
        return view('admin.advanced');
    }
   
      public function  blank()
    {
        return view('admin.blank');
    }

     public function  button()
    {
        return view('admin.buttons');
    }

     public function  calender()
    {
        return view('admin.calendar');
    }

    public function  chartjs()
    {
        return view('admin.chartjs');
    }

     public function  compose()
    {
        return view('admin.compose');
    }

    public function  data()
    {
        return view('admin.data');
    }

    public function  editor()
    {
        return view('admin.editors');
    }

    public function  flot()
    {
        return view('admin.flot');
    }

     public function  general()
    {
        return view('admin.general');
    }

   public function  generals()
    {
        return view('admin.generals');
    }

     public function  icons()
    {
        return view('admin.icons');
    }

    public function  inline()
    {
        return view('admin.inline');
    }

    public function  invoiceprint()
    {
        return view('admin.invoice-print');
    }

    public function  invoice()
    {
        return view('admin.invoice');
    }

    public function  lockscreen()
    {
        return view('admin.lockscreen');
    }

      public function  mailbox()
    {
        return view('admin.mailbox');
    }

     public function  profile()
    {
        return view('admin.profile');
    }

     public function  readmail()
    {
        return view('admin.read-mail');
    }

     public function  register()
    {
        return view('admin.register');
    }

    public function  simple()
    {
        return view('admin.simple');
    }

     public function  widget()
    {
        return view('admin.widgets');
    }
}
