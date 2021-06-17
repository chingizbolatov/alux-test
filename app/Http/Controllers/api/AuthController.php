<?php

namespace App\Http\Controllers\api;

use App\Console\Job\CurrencyResponseClass;
use App\Http\Controllers\Controller;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'registration']]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return $this->response(422, 'Validation error', $validator->errors()->getMessages());
        }

        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return $this->response(401, 'Invalid login or password');
        }

        return $this->respondWithToken($token);
    }

    public function registration(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users|email',
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return $this->response(422, 'Validation error', $validator->errors()->getMessages());
        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json(['message' => 'Successfully registration!']);
    }

    protected function respondWithToken($token)
    {
        return $this->response(200, 'Success', [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
