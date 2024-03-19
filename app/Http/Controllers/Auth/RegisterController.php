<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed'
        ]);
        if ($validator->fails()) {
            return $this->response(false, 'Please provide valid data!', null, 400, $validator->errors());
        }

        try {
            $data = $request->only(['name', 'email', 'password']);
            $data['email_verified_at'] = now();
            $data['user'] = $this->createOrUpdate($data);

            if (Auth::attempt(["email" => $data['email'], "password" => $data['password']])) {
                $data['token'] = $data['user']->createToken('appToken')->accessToken;
                return $this->response(true, 'User registered successfully.', $data);
            } else {
                return $this->response(false, 'Unauthorized', null, 401);
            }

        } catch (\Exception $e) {
            return $this->response(false, $e->getMessage() ?? 'Something went wrong!', null, 400);
        }
    }

    public function createOrUpdate($data = [], $user = null)
    {
        if (blank($user)) {
            $user = new User();
        }
        $data['password'] = Hash::make($data['password']);
        $user->fill($data);
        $user->save();
        return $user->fresh();
    }
}
