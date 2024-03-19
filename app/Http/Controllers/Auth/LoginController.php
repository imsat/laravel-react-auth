<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{

    /**
     * Login valid user and return token.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return $this->response(false, 'Invalid data!', null, 400, $validator->errors());
        }

        try {
            $credential = $request->only('email', 'password');

            if (Auth::attempt($credential)) {
                // Successfully authentication
                $user = User::find(Auth::user()->id);

                $data['token'] = $user->createToken('appToken')->accessToken;
                $data['user'] = auth()->user()->only(['id', 'name', 'email', 'email_verified_at']);

                return $this->response(true, 'Login successfully.', $data);
            } else {
                return $this->response(false, 'Wrong email or password!!', null, 403);
            }

        } catch (\Exception $e) {
            return $this->response(false, $e->getMessage() ?? 'Something went wrong!', null, 400);
        }

    }

    /**
     * Logout authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            // For revoke token
            $request->user()->token()->revoke();
            // For delete token
            $request->user()->token()->delete();

            return $this->response(true, 'Logout successfully.');
        } catch (\Exception $e) {
            return $this->response(false, $e->getMessage() ?? 'Something went wrong!', null, 400);
        }
    }

}
