<?php

namespace App\Http\Controllers\User;

use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AboutController extends Controller
{
    public function index()
{
    $ratings = Rating::with(['user', 'product'])
                     ->latest()
                     ->take(3)
                     ->get();

    return view('user.about', compact('ratings'));
}

}
