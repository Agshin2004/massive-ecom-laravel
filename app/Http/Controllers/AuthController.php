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
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function registerUser(StoreUserRequest $request)
    {
        $hashedPassword = Hash::make($request->input('password'));
        $user = User::create([
            'username' => $request->input('username'),
            'email' => $request->input('email'),
            'role' => $request->input('role'),
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
        // create user
        $hashedPassword = Hash::make($request->input('password'));

        // used transaction because seller needs user_id and if creating the user or the seller fails none of them will be created
        $token = DB::transaction(function () use ($request, $hashedPassword) {
            $user = User::create([
                'username' => $request->input('username'),
                'email' => $request->input('email'),
                'role' => $request->input('role'),
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
}
