<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;
class UserTest extends TestCase
{
    
    /**
     * A basic feature test example.
     *
     * @return void
     */
 
    
    use RefreshDatabase;

    public function test_login_view()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }
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


    public function test_cannot_access_login_view()
    {
     $this->createUser();
     $user = User::where('email', 'john@gmail.com')->first();
      $response = $this->actingAs($user)->get('/login');
      $response->assertRedirect('/home');
    }


    public function test_user_login()
    {
       $user = $this->createUser();

        $response = $this->post('/login', [
            'email' => 'john@gmail.com',
            'password' => 'password',
        ]);
        // $response->assertRedirect('/'); 
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_login_with_incorrect_password()
    {
        $user = $this->createUser();
        $response = $this->post('/login', [
            'email' => 'john@gmail.com',
            'password' => 'incorrectpassword',
        ]);
        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_user_logout()
    {
         $this->createUser();
          $user = User::where('email', 'john@gmail.com')->first();
          $this->actingAs($user);
          $response = $this->post('/logout');
          $response = $this->get('/user/profile');
          $response->assertStatus(302);
    }
    public function test_get_user_list_admin()
    {
        $this->createUser(true);
        $user = User::where('email', 'john@gmail.com')->first();
        $this->actingAs($user);
        $response = $this->get('/user/list');
        $response->assertStatus(200)
        ->assertViewHas('userList');
    }

    public function test_get_user_list_user()
    {
        $this->createUser();
        $user = User::where('email', 'john@gmail.com')->first();
        $this->actingAs($user);
        $response = $this->get('/user/list');
        $response->assertStatus(302);
    }

    public function test_get_user_list_guest()
    {
        $response = $this->get('/user/list');
        $response->assertStatus(302);
    }

   public function test_create_user()
    { 
        $this->createUser(true);
        $user = User::where('email', 'john@gmail.com')->first();

        $newUser = [
            'name' => 'newuser',
            'email' => 'newuser@gmail.com',
            'password' => bcrypt('password'),
            'profile' => '1588646773.png',
            'type' => '1',
            'created_user_id' => 1,
            'updated_user_id' => 1,
        ];

        $response = $this->actingAs($user)->post('/user/register/confirm', $newUser);
        $response->assertRedirect('/user/list');
        $this->assertDatabaseHas('users', [
            'name' => 'newuser',
            'email' => 'newuser@gmail.com',
        ]);
    } 

    public function test_delete_user()
    {
        $this->createUser(true);
        $user = User::where('email', 'john@gmail.com')->first();
        $newUserData = [
            'name' => 'newuser',
            'email' => 'newuser@gmail.com',
            'password' =>bcrypt('password'),
            'profile' => '1588646773.png',
            'type' => '1',
            'created_user_id' => 1,
            'updated_user_id' => 1,
        ];
        $this->actingAs($user)->post('/user/register/confirm', $newUserData);
        $newUser = User::where('email', 'newuser@gmail.com')->first();
        $deleteData = [
            'deleteId' => $newUser->id
        ];
        $response = $this->actingAs($user)->delete('/user/delete',$deleteData);
        $response = $this->get('/user/list')
        ->assertDontSeeText('newuser@gmail.com');
    }

    public function test_update_user()
    {
        $this->createUser(true);
        $user = User::where('email', 'john@gmail.com')->first();
        $UserData = [
            'name' => 'testuser',
            'email' => 'testuser@gmail.com',
            'password' =>bcrypt('password'),
            'profile' => '1588646773.png',
            'type' => '0',
            'created_user_id' => 1,
            'updated_user_id' => 1,
        ];
        $this->actingAs($user)->post('/user/register/confirm', $UserData);
        $newUser = User::where('email', 'testuser@gmail.com')->first();
        $updateData = [
            'name' => 'newUpdateUser',
            'email' => 'newUpdateuser@gmail.com',
            'type' => '1',
            'updated_user_id' => $newUser->id,
        ];
        session(['uploadProfile' => 'default.png']);
        $response = $this->actingAs($newUser)->post('/user/profile/edit/confirm',$updateData);
        $updateUser = User::find($newUser->id);
        $this->assertEquals('newUpdateUser', $updateUser->name);
        $this->assertEquals('newUpdateuser@gmail.com', $updateUser->email);
    }

    public function test_change_password()
    {
        $this->createUser(true);
        $user = User::where('email', 'john@gmail.com')->first(); 
        $requestData = [
            'old-password' => 'password',
           'new-password' => 'newpassword',
            'password-confirm'=> 'newpassword'
        ];
        $response = $this->actingAs($user)->post('user/change-password',$requestData);
        $updateUser = User::find($user->id);
        $this->assertTrue(Hash::check('newpassword', $updateUser->password));
    }

   
    public function test_send_reset_password_email()
    {
        Config::set('mail.default', 'smtp');
        $user = User::factory()->create([
            'name' => 'yehtet',
            'email' => 'scm.yehtetaung@gmail.com',
            'password' => bcrypt('password'),
            'profile' => '1588646773.png',
            'type' => '0',
            'created_user_id' => 1,
            'updated_user_id' => 1,
        ]);
        $user = User::where('email', 'scm.yehtetaung@gmail.com')->first();
        $response = $this->actingAs($user)->post('/password/email', ['email' => 'scm.yehtetaung@gmail.com.com']);
        $response->assertStatus(302);
    }


}
