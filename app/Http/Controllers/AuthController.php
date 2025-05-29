<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Http\Requests\StoreSellerRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenBlacklistedException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function registerUser(StoreUserRequest $request)
    {
        $hashedPassword = Hash::make($request->input('password'));
        $user = User::create([
            'username' => $request->input('username'),
            'email' => $request->input('email'),
            'role' => Role::User,  // default user role to user
            'phone_number' => $request->input('phone_number'),
            'password' => $hashedPassword,
        ]);

        $token = JWTAuth::fromUser($user);

        return $this->successResponse([
            'user' => $user,
            'jwt' => $token
        ]);
    }

    public function registerSeller(StoreSellerRequest $request)
    {
        $hashedPassword = Hash::make($request->input('password'));

        // used transaction because seller needs user_id and if creating the user or the seller fails none of them will be created
        $token = DB::transaction(function () use ($request, $hashedPassword) {
            // create user
            $user = User::create([
                'username' => $request->input('username'),
                'email' => $request->input('email'),
                'role' => Role::User,  // default user role to user
                'phone_number' => $request->input('phone_number'),
                'password' => $hashedPassword,
            ]);

            // create seller
            Seller::create([
                'user_id' => $user->id,
                'store_name' => $request->input('store_name'),
                'slug' => $request->input('slug'),
            ]);

            // create and return jwt token
            return JWTAuth::fromUser($user);
        }, attempts: 3);

        return $this->successResponse(
            [
                'token' => $token
            ],
            'Seller and User created successfully!'
        );
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);
        $authData = $request->only('email', 'password');

        $token = JWTAuth::attempt($authData);
        if (!$token) {
            throw ValidationException::withMessages([
                'Invalid credentials' => 'Invalid email or password'
            ]);
        }

        return $this->successResponse([
            'token' => $token
        ]);
    }

    public function logout()
    {
        // get tokenfrom the current request and invalidate it (by blacklisting)
        JWTAuth::invalidate(JWTAuth::getToken());
        return $this->successResponse([
            'message' => 'successfully logged out'
        ]);
    }

    public function refresh()
    {
        // note: when token is refreshed old token gets blacklisted

        JWTAuth::parseToken();  // parse token and check if it is valid
        $newToken = JWTAuth::refresh();
        return $this->successResponse([
            'token' => $newToken
        ]);
    }
}
