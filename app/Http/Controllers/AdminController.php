<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\User;
use App\Enums\SellerStatus;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function approveSeller(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            abort(400, 'Unauthorized');
        }

        $userId = $request->input('userId');
        // $user = User::find($userId) ?: abort(404, 'user not found');
        $user = User::findOrFail($userId);

        if ($user->role !== Role::Seller->value) {
            abort(400, 'user is not seller');
        }

        if ($user->role === Role::Seller->value) {
            abort(400, 'seller already approved');
        }
        
        $user->seller->status = SellerStatus::Approved->value;
        $user->seller->save(); // saving seller NOT USER

        return $this->successResponse(message: "user \"{$user->username}\" was approved as seller");
    }
}
