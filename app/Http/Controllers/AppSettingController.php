<?php

namespace App\Http\Controllers;

use App\Models\SoftImage;
use App\Models\SoftNews;
use Illuminate\Http\Request;

class AppSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $galleries = SoftImage::orderByDesc("id")->paginate(20);
        return view("appSettings.index", compact('galleries'));
    }

    public function indexNews()
    {
        //
        $news = SoftNews::orderByDesc("id")->paginate(20);
        return view("appSettings.index-news", compact('news'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view("appSettings.create");
    }

    public function createNew()
    {
        //
        return view("appSettings.create-news");
    }

    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        // Validate file
        $request->validate([
            'name' => 'required',
            'image_file' => 'required|mimes:jpg,jpeg,png,gif|max:2048',
        ], [
            'image_file.required' => 'Please upload an image.',
            'image_file.mimes'    => 'Only JPG, JPEG, PNG, and GIF formats are allowed.',
            'image_file.max'      => 'Image size must not exceed 2MB.',
        ]);

        // Store the image in "storage/app/public/images"
        $path = $request->file('image_file')->store('images', 'public');

        SoftImage::create([
            'name' => $request->name,
            'image_path' => $path,
        ]);
        return to_route("appSetting.index")->with('success', 'Image uploaded successfully!')->with('path', $path);
    }

    public function storeNews(Request $request)
    {
        // Validate file
        $request->validate([
            'name' => 'required',
            'notes' => 'required',
        ]);

        // Store the image in "storage/app/public/images"

        SoftNews::create([
            'name_title' => $request->name,
            'new_content' => $request->notes,
        ]);
        return to_route("appSetting.indexNews")->with('success', 'News Recorded successfully!');
    }
    

    /**
     * Display the specified resource.
     */

    public function fetchApi()
    {

        return SoftImage::orderByDesc("id")->limit(8)->get();
    }

    public function fetchNewApi()
    {
        
        return SoftNews::orderByDesc("id")->limit(8)->get();
    }
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function newDelete(SoftNews $new)
    {
        //
        $new->delete();
        return redirect()->back()->with('success', 'News deleted successfully!');
    }
    public function galleryDelete(SoftImage $gallery)
    {
        //
        $gallery->delete();
        return redirect()->back()->with('success', 'Gallery deleted successfully!');
    }
}
