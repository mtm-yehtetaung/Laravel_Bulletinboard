<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\PostCreateRequest;
use App\Http\Requests\PostEditRequest;
use App\Contracts\Services\Post\PostServiceInterface;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PostsExport;
use App\Http\Requests\PostCsvRequest;
use App\Imports\PostsImport;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Validator;
class PostController extends Controller
{


    private $postInterface;
    public $filteredData;
    public function __construct(PostServiceInterface $postServiceInterface)
    {
      $this->postInterface = $postServiceInterface;
    }
    //show all posts
    public function index()
    {
        session()->forget('filteredPostList');
        $postList = $this->postInterface->getAllPosts();
        return view('post.list',compact('postList'));
    }

    //show post create form
    public function showPostCreate()
    {
        return view('post.create');
    }

    //submit post create result
    public function submitPostCreate(PostCreateRequest $request)
    {
        $result = $request->validated();
        return redirect()
        ->route('postconfirm')
        ->withInput();
    }

    //show post confirm form
    public function showPostConfirm()
    {
        if(old()){
            return view('post.post-confirm');
        }
        return redirect()->route('postlist');
        
    }

    //submit post confirm form
    public function submitPostConfirm(Request $request)
    {
        $this->postInterface->savePost($request);
        return redirect()->route('postlist');
    }

    //show post edit form
    public function showPostEdit($id)
    {
        $post = $this->postInterface->getPostById($id);
        return view('post.edit',compact('post')); 
    }
    
    //submit post edit result
    public function submitPostEdit(PostEditRequest $request, $id) 
    {
        $result = $request->validated();
        return redirect()
        ->route('posteditconfirm',[$id])
        ->withInput();
    }

    //show post edit confirm form
    public function showPostEditConfirm($id)
    {
        if(old()){
            return view('post.post-edit-confirm');
        }
        return redirect()->route('postlist',[$id]);        
    }
    
    //submit post edit confirm form
    public function submitPostEditConfirm(Request $request, $id) 
    {
        $post = $this->postInterface->updatePostById($request, $id);
        return redirect()->route('postlist');
    }

    // public function deletePost($id) {
    //     $post = $this->postInterface->deletePostById($id);
    //     return redirect()->route('postlist'); 
    // }
    
    //delete post
    public function deletePost(Request $request) {
        $post = Post::find($request['deleteId']);
        if ($post) {
          $post->deleted_user_id = Auth::user()->id;
          $post->save();
          $post->delete();
        }
        return redirect()->route('postlist'); 
    }
    
    //search post
    public function searchPost(Request $request) {
        $postList = $this->postInterface->searchPost($request);
        session(['filteredPostList' => $postList]);
        return view('post.list',compact('postList'));
    }

    public function downloadPostCSV()
    {
    
    if(session('filteredPostList')) {
        $postList =  session('filteredPostList');
    } else {
        $postList = Post::all();
    }
    return Excel::download(new PostsExport($postList), 'posts.csv');
    }

    public function showPostUpload()
    {
        return view('post.upload');
    }

    public function submitPostUpload(PostCsvRequest $request)
    {
    
        $validator = Validator::make($request->all(),$request->rules());
        if ($validator->fails()) {
            return redirect()->route('postlist')
                ->withErrors($validator)
                ->withInput();
        }
                // Read the uploaded CSV file
                $csv = array_map('str_getcsv', file($request->file('csv_file')->getRealPath()));
        
                // Calculate the column count (assuming the first row contains headers)
                $columnCount = count($csv[0]);
                if($columnCount < 10 || $columnCount > 10){
                    Toastr::error('Column count of CSV must be 10');
                    return view('post.upload');
                }
        try {
            $file = $request['csv_file'];
            Excel::import(new PostsImport,$file);
            Toastr::error("Uploading successfully");
            return redirect()->route('postlist');
        } 
        catch (\Exception $e) {
            Toastr::error('Uploading failed due to duplicate data');
            return view('post.upload');
        }

    }

}
