<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    public function login(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);
        if ($validate->fails()) {
            return response()->json($validate->errors(), 400);
        }

        $credentials = $request->only('email', 'password');
        if (auth()->once($credentials)) {

            $token = auth()->user()->createToken('token')->accessToken;

            return response()->json([
                'token' => $token
            ], 200);
        }
        return response()->json([
            'message' => 'Credentials does not match'
        ], 200);
    }

    public function postAll()
    {
        $post = User::find(auth('api')->id())->post;

        if (count($post)) {
            return response()->json($post, 200);
        }
        return response()->json([
            'message' => 'No Post Found'
        ], 200);
    }

    public function post($id)
    {
        $post = Post::where('user_id', auth('api')->id())->find($id);

        if ($post) {
            return response()->json($post, 200);
        }
        return response()->json([
            'message' => 'No Post Found'
        ], 200);
    }

    public function logout()
    {
        $token = auth('api')->user()->token()->revoke();

        return response()->json([
            'message' => 'Logout Successful'
        ], 200);
    }
}
