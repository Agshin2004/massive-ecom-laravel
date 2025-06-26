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
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function registerUser(StoreUserRequest $request)
    {
        $password = $request->input('password');
        $passwordConfirm = $request->input('password_confirm');
        if (!$password || !$passwordConfirm || $password !== $passwordConfirm) {
            throw ValidationException::withMessages([
                'invalid_password' => "password and password_confirm do not match or not provided"
            ]);
        }

        $hashedPassword = Hash::make($password);
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
        [$token, $seller] = DB::transaction(function () use ($request, $hashedPassword) {
            // create user
            $user = User::create([
                'username' => $request->input('username'),
                'email' => $request->input('email'),
                'role' => Role::Seller,  // default role to seller
                'phone_number' => $request->input('phone_number'),
                'password' => $hashedPassword,
            ]);

            // create seller
            $seller = Seller::create([
                'user_id' => $user->id,
                'store_name' => $request->input('store_name'),
                'slug' => $request->input('slug'),
            ]);

            // create and return jwt token
            return [JWTAuth::fromUser($user), $seller];
        }, attempts: 3);

        return $this->successResponse(
            [
                'seller' => $seller,
                'token' => $token
            ],
            'Seller has been created',
        );
    }

    public function login(Request $request)
    {
        $creds = $request->validate([
            'email' => ['email'],
            'username' => ['alpha:ascii'],
            'password' => ['required']
        ]);

        if (!isset($creds['email']) && !isset($creds['username'])) {
            throw new \Exception('Username or Email missing.');
        }

        $authData = $request->only(
            array_key_exists('email', $creds) ? 'email' : 'username',
            'password'
        );


        $token = JWTAuth::attempt($authData);
        if (!$token) {
            $errMsg = array_key_exists('username', $authData) ? "invalid username or password" : "invalid email or password";
            throw ValidationException::withMessages([
                'invalid_credentials' => $errMsg,
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
