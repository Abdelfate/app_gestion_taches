<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class homecontroller extends Controller
{
    public function index(Request $request){
        $nom ="fethou";
        return view('hello',compact('nom'));
    }
}
