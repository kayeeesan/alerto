<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Resources\User as ResourcesUser;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::when($request->search, function ($db, $search) {
            $db->where(function($q) use($search){
                return $q->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('middle_name', 'like', '%' . $search . '%');
            });
        })->when($request->role, function ($db, $role) {
            $db->whereHas('roles', function($q) use($role) {
                return $q->where('name', 'like', '%' . $role . '%');
            });
        });

        return ResourcesUser::collection($users->paginate(10));
    }

    public function store(UserRequest $request)
    {
        try {
            $default_password = "*1234#";

            $user = new User();
            $user->username = $request->username;
            $user->first_name = ucwords($request->first_name);
            $user->middle_name = ucwords($request->middle_name);
            $user->last_name = ucwords($request->last_name);
            $user->password = bcrypt($default_password);
            $user->status = 'pending';
            $user->save();
            
            $this->storeUserRoles($user->id, $request->user_roles);

            return response()->json(['message' => 'User has been successfully saved.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function register(UserRequest $request)
{
    try {
        // Default password or allow user to set their own password
        $default_password = "*1234#";

        // Create a new user instance
        $user = new User();
        $user->username = $request->username;
        $user->first_name = ucwords($request->first_name);
        $user->middle_name = ucwords($request->middle_name);
        $user->last_name = ucwords($request->last_name);
        $user->password = bcrypt($default_password); // Or use bcrypt($request->password) for custom password
        $user->save();

        // If no roles are provided, assign the 'user' role by default
        $roles = $request->user_roles ?: ['user']; // Default to 'user' role if none provided
        $this->storeUserRoles($user->id, $roles);

        return response()->json(['message' => 'User has been successfully registered.'], Response::HTTP_CREATED);
    } catch (\Exception $e) {
        return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}



    public function storeUserRoles($user_id, $user_roles)
    {
        try {
            $user = User::findOrFail($user_id);
            $user->roles()->sync($user_roles);
            $user->update();
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function update($id, UserRequest $request)
    {
        try {
            $user = User::findOrFail($id);
            $user->username = $request->username;
            $user->first_name = ucwords($request->first_name);
            $user->middle_name = ucwords($request->middle_name);
            $user->last_name = ucwords($request->last_name);
            $this->storeUserRoles($user->id, $request->user_roles);

            $user->update();
            return response(['message' => 'User has been successfully updated.']);
        } catch (\Exception $e) {
            return response(['message' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return response(['message' => 'User has been successfully deleted!']);
    }
}
