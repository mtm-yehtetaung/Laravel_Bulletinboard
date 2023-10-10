<?php

namespace App\Contracts\Services\Post;

use Illuminate\Http\Request;
interface PostServiceInterface
{
    public function savePost(Request $request);

    public function getAllPosts();

    public function getPostById($id);

    public function updatePostById(Request $request, $id);

    public function deletePostById($id);

    public function searchPost(Request $request);

}

?>
