<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\TryCatch;
use App\Helpers\ResponseFormatter;
use Laravel\Fortify\Rules\Password;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request){

        try {

            //Validate Request
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'unique:users'],
                'password' => ['required', 'string', new Password],
            ]);

            //Create User
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            //Generate Token
            $tokenResult = $user->createToken('authToken')->plainTextToken;

            //Return Response
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'Register Success');

        } catch (Exception $error) {
            return ResponseFormatter::error($error->getMessage());
        }
    }

    public function login(Request $request){

        try {
            //Validate Request
            $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);

            //Find user By Email
            $credentials = request(['email', 'password']);
            if(!Auth::attempt($credentials)){
                return ResponseFormatter::error('Unauthorized', 401);
            }

            $user = User::where('email', $request->email)->first();

            if(!Hash::check($request->password, $user->password)){
                throw new Exception('Invalid Password');
            }

            //Generate TOken
            $tokenResult = $user->createToken('authToken')->plainTextToken;
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'Login Success');
        } catch (\Throwable $th) {
            return ResponseFormatter::error('Authentication Failed');
        }
    }

    public function logout(Request $request){

        $token = $request->user()->currentAccessToken()->delete();

        return ResponseFormatter::success($token, 'Logout Success');
    }

    public function fetch(Request $request){

        $user = $request->user();

        return ResponseFormatter::success($user, 'Fetch Success');
    }
}
