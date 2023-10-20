<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\UploadedFile;
use App\Imports\PostsImport;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PostTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;
    // use DatabaseTransactions;

    public function createUser($isAdmin = false)
    {
        $user = User::factory()->create([
            'name' => 'john',
            'email' => 'john@gmail.com',
            'password' => bcrypt('password'),
            'profile' => '1588646773.png',
            'type' => ($isAdmin) ? '0':'1',
            'created_user_id' => 1,
            'updated_user_id' => 1,
        ]);
        return $user;
    }

    public function createPost($user)
    {
        $post = Post::factory()->create([
            'title'=>'post one',
            'description'=>'post one description',
            "status"=>"0",
            'created_user_id'=> $user->id,
            'updated_user_id'=> $user->id,
        ]);
    }

    public function test_post_list()
    {
        $response = $this->get('/post/list');
        $response->assertStatus(200)
        ->assertViewHas('postList');
    }

    public function test_create_post()
    {
        $this->createUser(true);
        $user = User::where('email', 'john@gmail.com')->first();
        $postData = [
            'title'=>'post one',
            'description'=>'post one description',
            'created_user_id'=> $user->id,
            'updated_user_id'=> $user->id,
        ];
        $response = $this->actingAs($user)->post('/post/create/confirm', $postData);
        $response->assertRedirect('/post/list');
        $this->assertDatabaseHas('posts', [
            'title' => 'post one',
            'description' => 'post one description',
        ]);
    }

    public function test_update_post()
    {
        $this->createUser(true);
        $user = User::where('email', 'john@gmail.com')->first();
        $this->createPost($user);
        $post = Post::where('title', 'post one')->first();
        $updateData = [
            'title' => 'update post one',
            'description'=>'update post one description',
            'updated_user_id'=> $user->id,
        ];
        $response = $this->actingAs($user)->post("/post/edit/{$post->id}/confirm", $updateData);
        $response->assertRedirect('/post/list');
        $updatePost = Post::find($post->id);
        $this->assertEquals('update post one', $updatePost->title);
        $this->assertEquals('update post one description', $updatePost->description);
    }

    public function test_delete_post()
    {
        $this->createUser(true);
        $user = User::where('email', 'john@gmail.com')->first();
        $this->createPost($user);
        $post = Post::where('title', 'post one')->first();
        $deleteData = [
            'deleteId' => $post->id
        ];
        $this->actingAs($user)->delete("/post/delete", $deleteData);
        $response = $this->get('/post/list')
        ->assertDontSeeText('post one');
    }

    public function test_csv_export_post()
    {   
        $this->createUser(true);
        $user = User::where('email', 'john@gmail.com')->first();
        for ($i = 1; $i <= 5; $i++) {
            Post::factory()->create([
                'title'=>"post {$i}",
                'description'=>"post {$i} description",
                "status"=>"1",
                'created_user_id'=> $user->id,
                'updated_user_id'=> $user->id,
            ]);
        }
        $downloadFile = time() .'_posts.csv';
        $response = $this->actingAs($user)->get('post/download/')
        ->assertStatus(200);
    }

    public function test_csv_import_post()
    {
        $user = User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password'),
            'profile' => '1588646773.png',
            'type' => '0',
            'created_user_id' => 1,
            'updated_user_id' => 1,
        ]);
        $user = User::where('email', 'admin@gmail.com')->first();
        $filePath = storage_path('app/uploadtesting.csv');
        $csvFile = new UploadedFile($filePath, 'uploadtesting.csv');
        $uploadFolder = [
    'csv_file' => $csvFile,
    'created_user_id' => $user->id,
    'updated_user_id' => $user->id, 
       ];
         Excel::import(new PostsImport(), $csvFile);
        $response = $this->actingAs($user)->post('/post/upload/', $uploadFolder);
        $this->assertDatabaseHas('posts', [
                'title' => 'post eight',
                'description' => 'post eight description', 
            ]);
    }

    

}
