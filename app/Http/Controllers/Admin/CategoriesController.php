<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\models\Category;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::orderBy('id', 'desc')->get();
        return view('Admin.categories.index',compact('categories'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('Admin.categories.create');
    }


    public function store(Request $request)
    {

         $rules = [
             'name_ar' => ['required','max:255','not_regex:/([%\$#\*<>]+)/'],
             'name_en' =>['required','max:2','not_regex:/([%\$#\*<>]+)/'],
             'type' => ['required','string'],
             'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,JPG|max:2048',
         ];

//        $messages = [
//            'name_ar.required' => trans('error.name_ar_required'),
//            'name_ar.regex' => trans('error.name_ar_required'),
//            'name_ar.min' => trans('error.name_ar_min'),
//            'name_en.required' => trans('error.name_en_required'),
//            'name_en.regex' => trans('error.name_en_regex'),
//            'name_en.min' => trans('error.name_en_min'),
//            'type.required' => trans('error.type_required'),
//            'type.string' => trans('error.type_string'),
//            'image.mimes' => trans('error.image_mimes'),
//            'image.max' => trans('error.image_max'),
//        ];

         $this->validate($request,$rules);

         $image = $request->file('image');

        $filename = $image->getClientOriginalName();
        $fileextension = $image->getClientOriginalExtension();
        $file_to_store = time() . '_' . explode('.', $filename)[0] . '_.' . $fileextension;

        $image->move('category_images', $file_to_store);

         $category = Category::create([
             'name_ar' => $request->name_ar,
             'name_en' => $request->name_en,
             'type' => $request->type,
             'image'=> $file_to_store
         ]);

         if ($category) {
             return redirect()->route('categories.index')->withStatus(trans('admin.created_successfully'));
         } else {
             return redirect()->route('categories.index')->withStatus(trans('admin.something_wrong'));
         }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        $category = Category::find($id);

        if($category)
        {
            return view('Admin.categories.create',compact('category'));
        }
        else
        {
            return redirect('admin/categories')->withStatus(trans('admin.not_id'));
        }
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

        $category = Category::find($id);

        $rules = [
            'name_ar' => ['required','max:255','not_regex:/([%\$#\*<>]+)/'],
            'name_en' =>['required','max:255','not_regex:/([%\$#\*<>]+)/'],
            'type' => ['required','string'],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,JPG|max:2048',
        ];


//        $messages = [
//            'name_ar.required' => trans('error.name_ar_required'),
//            'name_ar.regex' => trans('error.name_ar_required'),
//            'name_ar.min' => trans('error.name_ar_min'),
//            'name_en.required' => trans('error.name_en_required'),
//            'name_en.regex' => trans('error.name_en_regex'),
//            'name_en.min' => trans('error.name_en_min'),
//            'type.required' => trans('error.type_required'),
//            'type.string' => trans('error.type_string'),
//            'image.mimes' => trans('error.image_mimes'),
//            'image.max' => trans('error.image_max'),
//        ];


        $this->validate($request,$rules);

        if ($category) {

            $file_to_store = null;

            if($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = $image->getClientOriginalName();
                $fileextension = $image->getClientOriginalExtension();
                $file_to_store = time() . '_' . explode('.', $filename)[0] . '_.' . $fileextension;
                $image->move('category_images', $file_to_store);
            }

            $file_to_store = $file_to_store != null ? $file_to_store : $category->image;

            $category->update([
                'name_ar' => $request->name_ar,
                'name_en' => $request->name_en,
                'type' => $request->type,
                'image'=> $file_to_store
            ]);
            return redirect()->route('categories.index')->withStatus(trans('admin.updated_successfully'));
        } else {
            return redirect()->route('categories.index')->withStatus(trans('messages.something_wrong'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */



    public function destroy($id)
    {

        $category = Category::find($id);

        if($category)
        {
            if($category->image != null) {
                unlink('category_images/' . $category->image);
            }

            $category->delete();

            return redirect()->route('categories.index')->withStatus(trans('messages.deleted_successfully'));
        } else {
            return redirect()->route('categories.index')->withStatus(trans('messages.something_wrong'));
        }
    }
}
