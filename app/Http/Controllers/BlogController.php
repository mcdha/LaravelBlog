<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BlogController extends Controller
{
    public function app()
    {
        $blogs = Blog::all();
        return view('blogs.app', compact('blogs'));
    }

    public function list()
    {
        $blogs = Blog::all();
        return view('blogs.list', compact('blogs'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:191',
            'author_name' => 'required|string|max:191',
            'body' => 'required|string',
            'category' => 'required|string|max:191',
            // 'image' => 'required|string|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages()
            ], 422);
        } else {
            $blog = new Blog;
            $blog->title = $request->title;
            $blog->author_name = $request->author_name;
            $blog->body = $request->body;
            $blog->category = $request->category;
            // $blog->image = $request->image;
            $blog->save();

            return response()->json([
                'status' => 200,
                'message' => "Blog Created Successfully"
            ], 200);
        }
    }

    public function edit($id)
    {
        $blog = Blog::findOrFail($id);
        return response()->json($blog);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:191',
            'author_name' => 'required|string|max:191',
            'body' => 'required|string',
            'category' => 'required|string|max:191',
            // 'image' => 'required|string|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages()
            ], 422);
        } else {
            $blog = Blog::findOrFail($id);
            $blog->title = $request->title;
            $blog->author_name = $request->author_name;
            $blog->body = $request->body;
            $blog->category = $request->category;
            // $blog->image = $request->image;
            $blog->save();

            return response()->json([
                'status' => 200,
                'message' => "Blog Updated Successfully"
            ], 200);
        }
    }

    public function destroy($id)
    {
        $blog = Blog::findOrFail($id);
        $blog->delete();

        return response()->json([
            'status' => 200,
            'message' => "Blog Deleted Successfully"
        ], 200);
    }
}


