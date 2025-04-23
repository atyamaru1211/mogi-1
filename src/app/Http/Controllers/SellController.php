<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExhibitionRequest;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;


class SellController extends Controller
{
    public function create()
    {
        $categories = Category::all();
        return view('item.sell', compact('categories'));
    }

    public function store(ExhibitionRequest $request)
    {
        //画像の保存
        $imagePath = null;
        $path = null;

        if ($request->hasFile('image_upload')) {
            $image = $request->file('image_upload');
            $path = $image->store('public/items'); 
            $imagePath = str_replace('public/items/', 'items/', $path);
        }
    
        $item = new Item();
        $item->user_id = auth()->id();
        $item->name = $request->input('name');
        $item->brand = $request->input('brand');
        $item->description = $request->input('description');
        $item->price = $request->input('price');
        $item->condition = $request->input('condition');
        $item->image_path = $imagePath;
        $item->save();
    
        //カテゴリーの関連付け
        if ($request->has('category')) {
            $item->categories()->attach($request->input('category'));
        }
    
        return redirect('/');
    }
}
