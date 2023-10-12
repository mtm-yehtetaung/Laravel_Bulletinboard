<?php
namespace App\Dao\User;
use App\Contracts\Dao\User\UserDaoInterface;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class UserDao implements UserDaoInterface
{
   
    public function saveUser(Request $request)
    {
        $profileName = session('uploadProfile')??'';
        $user = new User();
        $user->name = $request['name'];
        $user->email = $request['email'];
        $user->password = Hash::make($request['password']);
        $user->profile = $profileName;
        $user->type = $request['type']??'1';
        $user->phone = $request['phone']?? null;
        $user->dob = $request['dob']?? null;
        $user->address = $request['address']??null;
        $user->created_user_id = Auth::user()->id ?? 1;
        $user->updated_user_id = Auth::user()->id ?? 1;
        $user->save();
        Session::forget('uploadProfile');
        return $user;
    }

    public function getAllUsers()
    {
        $userList = DB::table('users as user')
        ->join('users as created_user', 'user.created_user_id', '=', 'created_user.id')
        ->join('users as updated_user', 'user.updated_user_id', '=', 'updated_user.id')
        ->select('user.*', 'created_user.name as created_user', 'updated_user.name as updated_user')
        ->whereNull('user.deleted_at')
        ->paginate(5);
        return $userList;
    }

    public function deleteUser(Request $request)
    {
        $user = User::find($request['deleteId']);
        if ($user) {
          $user->deleted_user_id = Auth::user()->id;
          $user->save();
          $user->delete();
        }
    }

    public function getUserById($id)
    {
        $user = User::find($id);
        return $user;
    }

    public function updateUser(Request $request)
    {
        $id = Auth::user()->id;
        $user = User::find($id);
        $user->name = $request['name'];
        $user->email = $request['email'];
        $user->type = $request['type'];
        $user->phone = $request['phone'];
        $user->dob = $request['dob'];
        $user->address = $request['address'];
        $user->profile = session('uploadProfile');
        $user->updated_user_id = Auth::user()->id;
        $user->save();
        Session::forget('uploadProfile');
        return $user;
    }

    public function changePassword(Request $request){
        $user = User::find(Auth::user()->id);
        $user->password = Hash::make($request['new-password']);
        $user->updated_user_id = Auth::user()->id;
        $user->save();
    }
}

?>