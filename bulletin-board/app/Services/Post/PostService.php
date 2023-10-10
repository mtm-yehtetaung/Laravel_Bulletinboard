<?php

namespace App\Services\Post;

use App\Contracts\Dao\Post\PostDaoInterface;
use App\Contracts\Services\Post\PostServiceInterface;
use Illuminate\Http\Request;

class PostService implements PostServiceInterface
{
    private $postDao;
    
    public function __construct(PostDaoInterface $postDao)
    {
      $this->postDao = $postDao;
    }

    public function savePost(Request $request)
    {
        return $this->postDao->savePost($request);
    }

    public function getAllPosts()
    {
        return $this->postDao->getAllPosts();
    }

    public function getPostById($id)
    {
        return $this->postDao->getPostById($id);
    }

    public function updatePostById(Request $request, $id)
    {
        return $this->postDao->updatePostById($request, $id);
    }

    public function deletePostById($id)
    {
        return $this->postDao->deletePostById($id);
    }
    
    public function searchPost(Request $request)
    {
        return $this->postDao->searchPost($request);
    }
}

?>