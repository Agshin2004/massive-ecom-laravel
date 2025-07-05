<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Models\User;
use App\Enums\SellerStatus;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    public function updateSellerStatus(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            abort(400, 'Unauthorized');
        }

        $userId = $request->input('userId') ?? abort(400, 'userId must be provided');

        // * NOTE: could use tryFrom but then we would need to wrap it inside try / catch
        // * so decided to write static fromValue method on SellerStatus that throws exception if not found
        $status = SellerStatus::fromValue($request->input('status'))->value;

        // $user = User::find($userId) ?: abort(404, 'user not found');
        $user = User::findOrFail($userId);

        if ($user->role !== Role::Seller->value) {
            abort(400, 'user is not seller');
        }

        $user->seller->status = $status;
        $user->seller->save(); // saving seller NOT USER

        return $this->successResponse(message: "seller \"{$user->username}\" was {$status}");
    }
}
