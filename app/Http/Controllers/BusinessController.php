<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;

class BusinessController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:business.create')->only(['create','store']);
        $this->middleware('permission:business.view')->only(['index','show']);
    }

    public function index()
    {
        $businesses = Business::latest()->paginate(12);
        return view('businesses.index', compact('businesses'));
    }

    public function create()
    {
        return view('businesses.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);
        $data['created_by'] = $request->user()->id;
        Business::create($data);
        return redirect()->route('businesses.index')->with('success','Business created');
    }

    public function show(Business $business)
    {
        return view('businesses.show', compact('business'));
    }
}
