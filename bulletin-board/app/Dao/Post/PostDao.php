<?php

namespace App\Dao\Post;
use App\Contracts\Dao\Post\PostDaoInterface;
use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class PostDao implements PostDaoInterface
{

    public function savePost(Request $request)
    {
        $post = new Post();
        $post->title = $request['title'];
        $post->description = $request['description'];
        $post->created_user_id = Auth::user()->id ?? 1;
        $post->updated_user_id = Auth::user()->id ?? 1;
        $post->save();
        return $post;
    }

    public function getAllPosts()
    {
        $postList = DB::table('posts as post')
        ->join('users as created_user', 'post.created_user_id', '=', 'created_user.id')
        ->join('users as updated_user', 'post.updated_user_id', '=', 'updated_user.id')
        ->select('post.*', 'created_user.name as created_user', 'updated_user.name as updated_user')
        ->whereNull('post.deleted_at')
        ->paginate(5);
      return $postList;
    }

    public function getPostById($id){
        $post = Post::find($id);
        return $post;
    }

    public function updatePostById(Request $request, $id)
    {
        $post = Post::find($id);
        $post->title = $request['title'];
        $post->description = $request['description'];
        if ($request['status']) {
          $post->status = '1';
        } else {
          $post->status = '0';
        }
        $post->updated_user_id = Auth::user()->id;
        $post->save();
        return $post;
    }

    public function deletePostById($id) {
        $post = Post::find($id);
        if ($post) {
          $post->deleted_user_id = Auth::user()->id;
          $post->save();
          $post->delete();
        }
    }

    public function searchPost(Request $request)
    {
        $keyword = $request['keyword'];
        session(['last_search_keyword' => $keyword]);
        $postList = DB::table('posts as post')
        ->join('users as created_user', 'post.created_user_id', '=', 'created_user.id')
        ->join('users as updated_user', 'post.updated_user_id', '=', 'updated_user.id')
        ->select('post.*', 'created_user.name as created_user', 'updated_user.name as updated_user')
        ->whereNull('post.deleted_at')
        ->where('title', 'like', "%$keyword%")
        ->orWhere('description', 'like', "%$keyword%")
        ->paginate(5);

        return $postList;
    }
    
}

?>