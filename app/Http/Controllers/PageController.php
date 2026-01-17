<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class PageController extends Controller
{

    public function guideline()
    {
        return view('pages.guideline');
    }

    public function faq()
    {
        return view('pages.faq');
    }

    public function rewardPoint()
    {
        return view('pages.reward-point');
    }

    public function privacy()
    {
        return view('pages.privacy');
    }

    public function shipping()
    {
        return view('pages.shipping');
    }

    public function returns()
    {
        return view('pages.returns');
    }
    
    public function terms()
    {
        return view('pages.terms');
    }
}
