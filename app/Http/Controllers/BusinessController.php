<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\View\View;

class BusinessController extends Controller
{

    public function index(): View
    {

        $businesses = Business::all();


        return view('businesses.index', compact('businesses'));
    }
    public function show($slug): View
    {
        return view('businesses.show', compact('slug'));
    }
}
